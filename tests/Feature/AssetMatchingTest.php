<?php

namespace Tests\Feature;

use App\Enums\AssetCondition;
use App\Enums\AssetObjective;
use App\Enums\AssetOwnershipStatus;
use App\Enums\AssetStatus;
use App\Enums\AssetUtilizationStatus;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Lead;
use App\Models\User;
use App\Notifications\AssetReviewResultNotification;
use App\Services\AssetReviewService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
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

    public function test_owner_can_submit_draft_and_other_user_cannot_access_private_certificate(): void
    {
        Storage::fake('local');
        $owner = User::factory()->create(['whatsapp' => '081234567890']);
        $asset = $this->makeAsset($owner);
        Storage::disk('local')->put($asset->certificate_file, 'private document');

        $this->actingAs($owner)->post(route('matching.assets.submit', $asset))->assertRedirect(route('matching.dashboard'));
        $this->assertSame(AssetStatus::PendingReview, $asset->fresh()->status);

        $other = User::factory()->create(['whatsapp' => '081111111111']);
        $this->actingAs($other)->get(route('matching.assets.certificate', $asset))->assertForbidden();
        $this->actingAs($owner)->get(route('matching.assets.certificate', $asset))->assertOk();
    }

    public function test_owner_can_create_draft_with_private_certificate_and_public_photos(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        $owner = User::factory()->create(['whatsapp' => '081234567890']);
        $category = AssetCategory::create(['name' => 'Gudang', 'slug' => 'gudang', 'is_active' => true]);

        $response = $this->actingAs($owner)->post(route('matching.assets.store'), [
            'asset_category_id' => $category->id, 'name' => 'Gudang Baru', 'province' => 'Jawa Barat',
            'city' => 'Bekasi', 'full_address' => 'Alamat privat', 'area_sqm' => 500,
            'certificate_type' => 'HGB', 'certificate_number' => 'HGB-001',
            'certificate_file' => UploadedFile::fake()->create('sertifikat.pdf', 100, 'application/pdf'),
            'condition' => AssetCondition::Good->value, 'ownership_status' => AssetOwnershipStatus::Company->value,
            'utilization_status' => AssetUtilizationStatus::Vacant->value, 'objective' => AssetObjective::FindInvestor->value,
            'photos' => [UploadedFile::fake()->image('depan.jpg')],
        ]);

        $asset = Asset::where('name', 'Gudang Baru')->firstOrFail();
        $response->assertRedirect(route('matching.assets.edit', $asset));
        $this->assertSame(AssetStatus::Draft, $asset->status);
        Storage::disk('local')->assertExists($asset->certificate_file);
        Storage::disk('public')->assertExists($asset->photos()->first()->path);
    }

    public function test_catalog_only_shows_published_assets_without_private_data(): void
    {
        $owner = User::factory()->create(['whatsapp' => '081234567890']);
        $published = $this->makeAsset($owner, AssetStatus::Published, ['name' => 'Gudang Terbit']);
        $pending = $this->makeAsset($owner, AssetStatus::PendingReview, ['name' => 'Gudang Rahasia']);

        $this->get(route('matching.index'))->assertOk()->assertSee('Gudang Terbit')->assertDontSee('Gudang Rahasia');
        $this->get(route('matching.show', $published))->assertOk()
            ->assertSee('Gudang Terbit')->assertSee($published->certificate_type)
            ->assertDontSee($published->certificate_number)->assertDontSee($published->full_address);
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
            'province' => $asset->province, 'city' => $asset->city, 'full_address' => $asset->full_address,
            'area_sqm' => 1200, 'certificate_type' => $asset->certificate_type,
            'certificate_number' => $asset->certificate_number, 'condition' => AssetCondition::Good->value,
            'ownership_status' => AssetOwnershipStatus::Personal->value,
            'utilization_status' => AssetUtilizationStatus::Vacant->value, 'objective' => AssetObjective::Sell->value,
        ])->assertSessionHasNoErrors();

        $asset->refresh();
        $this->assertSame(AssetStatus::PendingReview, $asset->status);
        $this->assertNull($asset->published_at);
        $this->get(route('matching.show', $asset))->assertNotFound();
    }

    public function test_admin_can_open_asset_matching_resources_and_private_document(): void
    {
        Storage::fake('local');
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole($adminRole);
        $owner = User::factory()->create(['whatsapp' => '081234567890']);
        $asset = $this->makeAsset($owner, AssetStatus::PendingReview);
        Storage::disk('local')->put($asset->certificate_file, 'private document');

        $this->actingAs($admin)->get(route('filament.admin.resources.assets.index'))->assertOk();
        $this->actingAs($admin)->get(route('filament.admin.resources.assets.view', $asset))->assertOk();
        $this->actingAs($admin)->get(route('filament.admin.resources.asset-categories.index'))->assertOk();
        $this->actingAs($admin)->get(route('matching.assets.certificate', $asset))->assertOk();
    }

    private function makeAsset(User $owner, AssetStatus $status = AssetStatus::Draft, array $overrides = []): Asset
    {
        $category = AssetCategory::firstOrCreate(['slug' => 'tanah'], ['name' => 'Tanah', 'is_active' => true]);

        return Asset::create([...[
            'owner_id' => $owner->id, 'asset_category_id' => $category->id, 'name' => 'Aset Uji',
            'province' => 'DKI Jakarta', 'city' => 'Jakarta Selatan', 'full_address' => 'Jl. Privat No. 1',
            'area_sqm' => 1000, 'certificate_type' => 'SHM', 'certificate_number' => 'SHM-SECRET-123',
            'certificate_file' => 'asset-certificates/test.pdf', 'condition' => AssetCondition::Good,
            'ownership_status' => AssetOwnershipStatus::Personal, 'utilization_status' => AssetUtilizationStatus::Vacant,
            'objective' => AssetObjective::Sell, 'status' => $status, 'published_at' => $status === AssetStatus::Published ? now() : null,
        ], ...$overrides]);
    }
}
