<?php

namespace Tests\Feature;

use App\Enums\AssetCondition;
use App\Enums\AssetListingStatus;
use App\Enums\AssetObjective;
use App\Enums\AssetOwnershipStatus;
use App\Enums\AssetStatus;
use App\Enums\AssetUtilizationStatus;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Facility;
use App\Models\Lead;
use App\Models\User;
use App\Notifications\AssetReviewResultNotification;
use App\Services\AssetReviewService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AssetMatchingTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_is_logged_in_immediately(): void
    {
        $response = $this->post(route('matching.register.store'), [
            'name' => 'Pemilik Aset', 'email' => 'owner@example.com', 'whatsapp' => '081234567890',
            'company' => 'PT Contoh', 'password' => 'password123', 'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('matching.dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'owner@example.com', 'whatsapp' => '081234567890']);
    }

    public function test_owner_can_submit_draft(): void
    {
        $owner = User::factory()->create(['whatsapp' => '081234567890']);
        $asset = $this->makeAsset($owner);

        $this->actingAs($owner)->post(route('matching.assets.submit', $asset))->assertRedirect(route('matching.dashboard'));
        $this->assertSame(AssetStatus::PendingReview, $asset->fresh()->status);
    }

    public function test_owner_can_create_draft_without_certificate_document_and_with_public_photos(): void
    {
        Storage::fake('public');
        $owner = User::factory()->create(['whatsapp' => '081234567890']);
        $category = AssetCategory::create(['name' => 'Gudang', 'slug' => 'gudang', 'is_active' => true]);

        $this->actingAs($owner)->get(route('matching.assets.create'))->assertOk()
            ->assertSee('Informasi Aset')->assertSee('SEO Otomatis')
            ->assertDontSee('name="certificate_number"', false)
            ->assertDontSee('name="certificate_file"', false);

        $response = $this->actingAs($owner)->post(route('matching.assets.store'), [
            'asset_category_id' => $category->id, 'name' => 'Gudang Baru', 'province' => 'Jawa Barat',
            'slug' => 'gudang-baru', 'listing_status' => AssetListingStatus::Available->value,
            'city' => 'Bekasi', 'district' => 'Cikarang Barat', 'village' => 'Telaga Asih',
            'full_address' => 'Alamat publik', 'google_maps_url' => 'https://maps.google.com/?q=-6.2,107.1',
            'description' => 'Gudang siap digunakan.', 'area_sqm' => 500, 'price' => 2500000000,
            'price_per_sqm' => 4750000,
            'certificate_type' => 'HGB',
            'condition' => AssetCondition::Good->value, 'ownership_status' => AssetOwnershipStatus::Company->value,
            'utilization_status' => AssetUtilizationStatus::Vacant->value, 'objective' => AssetObjective::FindInvestor->value,
            'photos' => [UploadedFile::fake()->image('depan.jpg')],
            'photo_alt_texts' => ['Tampak depan Gudang Baru'],
        ]);

        $asset = Asset::where('name', 'Gudang Baru')->firstOrFail();
        $response->assertRedirect(route('matching.assets.edit', $asset));
        $this->assertSame(AssetStatus::Draft, $asset->status);
        $this->assertFalse(Schema::hasColumn('assets', 'certificate_number'));
        $this->assertFalse(Schema::hasColumn('assets', 'certificate_file'));
        $this->assertArrayNotHasKey('certificate_number', $asset->getAttributes());
        $this->assertArrayNotHasKey('certificate_file', $asset->getAttributes());
        Storage::disk('public')->assertExists($asset->photos()->first()->path);
        $this->assertSame('Tampak depan Gudang Baru', $asset->photos()->first()->alt_text);
        $this->assertSame('4750000.00', $asset->price_per_sqm);
    }

    public function test_catalog_only_shows_publicly_listed_assets(): void
    {
        $owner = User::factory()->create(['whatsapp' => '081234567890']);
        $published = $this->makeAsset($owner, AssetStatus::Published, ['name' => 'Gudang Terbit']);
        $pending = $this->makeAsset($owner, AssetStatus::PendingReview, ['name' => 'Gudang Rahasia']);

        $this->get(route('matching.index'))->assertOk()->assertSee('Gudang Terbit')->assertDontSee('Gudang Rahasia');
        $this->get(route('matching.show', $published))->assertOk()
            ->assertSee('Gudang Terbit')->assertSee($published->certificate_type)
            ->assertSee('Kondisi Aset')->assertSee('Deskripsi')->assertSee('Pemanfaatan')
            ->assertDontSee('Nomor Sertifikat')->assertDontSee('Dokumen Sertifikat')
            ->assertSee($published->full_address);
        $this->get(route('matching.show', $pending))->assertNotFound();
    }

    public function test_interest_creates_exactly_one_related_crm_lead(): void
    {
        $owner = User::factory()->create(['whatsapp' => '081234567890']);
        $interested = User::factory()->create(['whatsapp' => '089999999999', 'company' => 'Investor Co']);
        $asset = $this->makeAsset($owner, AssetStatus::Published);

        $this->actingAs($interested)->post(route('matching.interests.store', $asset))->assertRedirect(route('matching.dashboard'));
        $this->assertDatabaseHas('asset_interests', ['asset_id' => $asset->id, 'user_id' => $interested->id]);
        $this->assertDatabaseHas('leads', ['asset_id' => $asset->id, 'user_id' => $interested->id, 'source' => 'asset_matching']);

        $this->actingAs($interested)->post(route('matching.interests.store', $asset))->assertRedirect();
        $this->assertSame(1, Lead::whereBelongsTo($interested)->whereBelongsTo($asset)->count());
        $this->actingAs($owner)->post(route('matching.interests.store', $asset))->assertStatus(422);
    }

    public function test_review_requires_all_checks_to_publish_and_notifies_owner(): void
    {
        Notification::fake();
        $owner = User::factory()->create(['whatsapp' => '081234567890']);
        $reviewer = User::factory()->create();
        $asset = $this->makeAsset($owner, AssetStatus::PendingReview);
        $checks = ['data_complete' => true, 'basic_legality' => true, 'photos_adequate' => true, 'publishable' => true];

        $reviewed = app(AssetReviewService::class)->review($asset, $reviewer, $checks);

        $this->assertSame(AssetStatus::Published, $reviewed->status);
        $this->assertNotNull($reviewed->published_at);
        $this->assertDatabaseHas('asset_reviews', ['asset_id' => $asset->id, 'decision' => 'published']);
        Notification::assertSentTo($owner, AssetReviewResultNotification::class);
    }

    public function test_failed_review_requires_notes(): void
    {
        Notification::fake();
        Storage::fake('public');
        $owner = User::factory()->create(['whatsapp' => '081234567890']);
        $reviewer = User::factory()->create();
        $asset = $this->makeAsset($owner, AssetStatus::PendingReview);

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        app(AssetReviewService::class)->review($asset, $reviewer, ['data_complete' => false], null);
    }

    public function test_editing_published_asset_hides_it_and_returns_it_to_review(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        $owner = User::factory()->create(['whatsapp' => '081234567890']);
        $asset = $this->makeAsset($owner, AssetStatus::Published);
        $asset->photos()->create(['path' => 'assets/existing.jpg', 'sort_order' => 0]);

        $this->actingAs($owner)->put(route('matching.assets.update', $asset), [
            'asset_category_id' => $asset->asset_category_id, 'name' => 'Aset Diperbarui',
            'slug' => 'slug-yang-tidak-boleh-dipakai', 'listing_status' => AssetListingStatus::Available->value,
            'province' => $asset->province, 'city' => $asset->city, 'district' => $asset->district,
            'village' => $asset->village, 'full_address' => $asset->full_address, 'description' => $asset->description,
            'area_sqm' => 1200, 'certificate_type' => $asset->certificate_type,
            'condition' => AssetCondition::Good->value,
            'ownership_status' => AssetOwnershipStatus::Personal->value,
            'utilization_status' => AssetUtilizationStatus::Vacant->value, 'objective' => AssetObjective::Sell->value,
        ])->assertSessionHasNoErrors();

        $asset->refresh();
        $this->assertSame(AssetStatus::PendingReview, $asset->status);
        $this->assertNull($asset->published_at);
        $this->assertSame('aset-uji', $asset->slug);
        $this->get(route('matching.show', $asset))->assertNotFound();
    }

    public function test_admin_can_open_asset_matching_resources_without_certificate_document(): void
    {
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole($adminRole);
        $owner = User::factory()->create(['whatsapp' => '081234567890']);
        $asset = $this->makeAsset($owner, AssetStatus::PendingReview);

        $this->actingAs($admin)->get(route('filament.admin.resources.assets.index'))->assertOk();
        $this->actingAs($admin)->get(route('filament.admin.resources.assets.view', $asset))
            ->assertOk()->assertSee('Informasi Aset')->assertSee('Lokasi')->assertSee('Legalitas')
            ->assertSee('Detail Aset')->assertSee('Fasilitas')->assertSee('SEO Otomatis')
            ->assertDontSee('Nomor Sertifikat')->assertDontSee('Dokumen Sertifikat');
        $this->actingAs($admin)->get(route('filament.admin.resources.asset-categories.index'))->assertOk();
        $this->actingAs($admin)->get(route('filament.admin.resources.facilities.index'))->assertOk();
        $this->assertFalse(app('router')->has('matching.assets.certificate'));
        $this->assertFalse(app('router')->has('matching.assets.certificate.preview'));
    }

    public function test_public_asset_uses_slug_and_legacy_uuid_redirects_permanently(): void
    {
        $owner = User::factory()->create(['whatsapp' => '081234567890']);
        $asset = $this->makeAsset($owner, AssetStatus::Published, ['name' => 'Tanah Industri Cikarang']);

        $this->get('/asset-matching/aset/'.$asset->slug)->assertOk();
        $this->get('/asset-matching/aset/'.$asset->public_id)
            ->assertRedirect(route('matching.show', $asset))->assertStatus(301);
    }

    public function test_price_per_sqm_is_automatic_but_can_be_overridden(): void
    {
        $owner = User::factory()->create(['whatsapp' => '081234567890']);
        $automatic = $this->makeAsset($owner, overrides: ['area_sqm' => 1000, 'price' => 5000000000]);
        $overridden = $this->makeAsset($owner, overrides: ['name' => 'Aset Harga Khusus', 'area_sqm' => 1000, 'price' => 5000000000, 'price_per_sqm' => 4500000]);

        $this->assertSame('5000000.00', $automatic->price_per_sqm);
        $this->assertSame('4500000.00', $overridden->price_per_sqm);
    }

    public function test_old_slug_redirects_and_inactive_asset_is_hidden(): void
    {
        $owner = User::factory()->create(['whatsapp' => '081234567890']);
        $asset = $this->makeAsset($owner, AssetStatus::Published);
        $oldSlug = $asset->slug;
        $asset->update(['slug' => 'aset-uji-baru']);

        $this->get('/asset-matching/aset/'.$oldSlug)->assertRedirect(route('matching.show', $asset))->assertStatus(301);
        $asset->update(['listing_status' => AssetListingStatus::Inactive]);
        $this->get(route('matching.show', $asset))->assertNotFound();
        $this->get(route('matching.index'))->assertDontSee($asset->name);
    }

    public function test_facilities_alt_text_and_assets_sitemap_are_public(): void
    {
        $owner = User::factory()->create(['whatsapp' => '081234567890']);
        $asset = $this->makeAsset($owner, AssetStatus::Published);
        $facility = Facility::firstOrCreate(['slug' => 'akses-tol'], ['name' => 'Akses Tol', 'is_active' => true]);
        $asset->facilities()->attach($facility);
        $asset->photos()->create(['path' => 'assets/test.jpg', 'alt_text' => 'Tampak depan aset industri', 'sort_order' => 0]);

        $this->get(route('matching.show', $asset))->assertOk()->assertSee('Akses Tol')->assertSee('Tampak depan aset industri');
        $this->get(route('sitemap.assets'))->assertOk()->assertSee(route('matching.show', $asset), false);
    }

    private function makeAsset(User $owner, AssetStatus $status = AssetStatus::Draft, array $overrides = []): Asset
    {
        $category = AssetCategory::firstOrCreate(['slug' => 'tanah'], ['name' => 'Tanah', 'is_active' => true]);

        return Asset::create([...[
            'owner_id' => $owner->id, 'asset_category_id' => $category->id, 'name' => 'Aset Uji',
            'listing_status' => AssetListingStatus::Available, 'province' => 'DKI Jakarta', 'city' => 'Jakarta Selatan',
            'district' => 'Kebayoran Baru', 'village' => 'Senayan', 'full_address' => 'Jl. Publik No. 1',
            'google_maps_url' => 'https://maps.google.com/?q=-6.2,106.8', 'description' => 'Deskripsi lengkap aset uji.',
            'area_sqm' => 1000, 'price' => 5000000000, 'certificate_type' => 'SHM',
            'condition' => AssetCondition::Good,
            'ownership_status' => AssetOwnershipStatus::Personal, 'utilization_status' => AssetUtilizationStatus::Vacant,
            'objective' => AssetObjective::Sell, 'status' => $status, 'published_at' => $status === AssetStatus::Published ? now() : null,
            'slug_locked_at' => $status === AssetStatus::Published ? now() : null,
        ], ...$overrides]);
    }
}
