<?php

namespace App\Filament\Resources;

use App\Enums\AssetListingStatus;
use App\Enums\AssetStatus;
use App\Filament\Resources\AssetResource\Pages;
use App\Models\Asset;
use App\Services\AssetReviewService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Asset Matching';

    protected static ?string $navigationLabel = 'Review Aset';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', AssetStatus::PendingReview->value)->count();

        return $count ? (string) $count : null;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Pemilik')->schema([
                Forms\Components\TextInput::make('owner.name')->label('Pemilik')->disabled(),
                Forms\Components\TextInput::make('owner.whatsapp')->label('WhatsApp')->disabled(),
            ])->columns(2),
            Forms\Components\Section::make('Informasi Aset')->schema([
                Forms\Components\TextInput::make('name')->label('Nama Aset')->disabled(),
                Forms\Components\TextInput::make('slug')->label('Slug')->disabled(),
                Forms\Components\TextInput::make('category.name')->label('Kategori')->disabled(),
                Forms\Components\TextInput::make('listing_status')->label('Status Listing')->formatStateUsing(fn ($state) => $state instanceof AssetListingStatus ? $state->label() : (AssetListingStatus::tryFrom((string) $state)?->label() ?? $state))->disabled(),
                Forms\Components\TextInput::make('area_sqm')->label('Luas (m²)')->disabled(),
                Forms\Components\TextInput::make('price')->label('Harga')->formatStateUsing(fn ($state) => $state ? 'Rp '.number_format((float) $state, 0, ',', '.') : 'Hubungi Grapadi')->disabled(),
                Forms\Components\TextInput::make('price_per_sqm')->label('Harga/m²')->formatStateUsing(fn ($state) => $state ? 'Rp '.number_format((float) $state, 0, ',', '.') : 'Hubungi Grapadi')->disabled(),
            ])->columns(2),
            Forms\Components\Section::make('Lokasi')->schema([
                Forms\Components\TextInput::make('province')->label('Provinsi')->disabled(),
                Forms\Components\TextInput::make('city')->label('Kabupaten/Kota')->disabled(),
                Forms\Components\TextInput::make('district')->label('Kecamatan')->disabled(),
                Forms\Components\TextInput::make('village')->label('Kelurahan/Desa')->disabled(),
                Forms\Components\Textarea::make('full_address')->label('Alamat Lengkap')->disabled()->columnSpanFull(),
                Forms\Components\TextInput::make('google_maps_url')->label('Google Maps')->disabled()->columnSpanFull(),
            ])->columns(2),
            Forms\Components\Section::make('Legalitas')->schema([
                Forms\Components\TextInput::make('certificate_type')->label('Sertifikat')->disabled(),
            ]),
            Forms\Components\Section::make('Detail Aset')->schema([
                Forms\Components\Textarea::make('description')->label('Deskripsi')->disabled()->columnSpanFull(),
                Forms\Components\TextInput::make('condition')->label('Kondisi')->formatStateUsing(fn ($state) => $state instanceof \App\Enums\AssetCondition ? $state->label() : (\App\Enums\AssetCondition::tryFrom((string) $state)?->label() ?? $state))->disabled(),
                Forms\Components\TextInput::make('utilization_status')->label('Pemanfaatan')->formatStateUsing(fn ($state) => $state instanceof \App\Enums\AssetUtilizationStatus ? $state->label() : (\App\Enums\AssetUtilizationStatus::tryFrom((string) $state)?->label() ?? $state))->disabled(),
                Forms\Components\TextInput::make('objective')->label('Tujuan')->formatStateUsing(fn ($state) => $state instanceof \App\Enums\AssetObjective ? $state->label() : (\App\Enums\AssetObjective::tryFrom((string) $state)?->label() ?? $state))->disabled(),
                Forms\Components\TextInput::make('status')->label('Status Review')->formatStateUsing(fn ($state) => $state instanceof AssetStatus ? $state->label() : (AssetStatus::tryFrom((string) $state)?->label() ?? $state))->disabled(),
            ])->columns(2),
            Forms\Components\Section::make('Fasilitas')->schema([
                Forms\Components\Placeholder::make('facilities_list')->hiddenLabel()->content(function (Asset $record) {
                    if ($record->facilities->isEmpty()) {
                        return 'Tidak ada fasilitas yang dipilih.';
                    }

                    return new HtmlString($record->facilities->map(fn ($facility) => '<span style="display:inline-flex;align-items:center;margin:0 8px 8px 0;padding:6px 10px;border:1px solid #d5b24b;border-radius:8px">'.e($facility->name).'</span>')->implode(''));
                }),
            ]),
            Forms\Components\Section::make('Foto Aset')->schema([
                Forms\Components\Placeholder::make('photo_gallery')->hiddenLabel()->content(function (Asset $record) {
                    $images = $record->photos->map(fn ($photo) => '<figure><img src="'.e(asset('storage/'.$photo->path)).'" alt="'.e($photo->alt_text ?: $record->name).'" style="width:180px;height:120px;object-fit:cover;border-radius:8px"><figcaption style="max-width:180px;font-size:11px">'.e($photo->alt_text ?: $record->name).'</figcaption></figure>')->implode('');

                    return new HtmlString('<div style="display:flex;flex-wrap:wrap;gap:12px">'.$images.'</div>');
                }),
            ]),
            Forms\Components\Section::make('SEO Otomatis')->schema([
                Forms\Components\TextInput::make('seo_title')->label('SEO Title')->disabled()->columnSpanFull(),
                Forms\Components\Textarea::make('meta_description')->label('Meta Description')->disabled()->columnSpanFull(),
            ]),
            Forms\Components\Section::make('Riwayat Review')->schema([
                Forms\Components\Placeholder::make('review_history')->hiddenLabel()->content(function (Asset $record) {
                    if ($record->reviews->isEmpty()) {
                        return 'Belum ada review.';
                    }
                    $items = $record->reviews->map(fn ($review) => '<div style="margin-bottom:12px"><strong>'.e($review->decision).'</strong> · '.e($review->created_at->format('d M Y H:i')).'<br>'.e($review->notes ?: 'Tanpa catatan').'</div>')->implode('');

                    return new HtmlString($items);
                }),
            ])->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->label('Aset')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('owner.name')->label('Pemilik')->searchable(),
            Tables\Columns\TextColumn::make('category.name')->label('Kategori'),
            Tables\Columns\TextColumn::make('listing_status')->label('Listing')->badge()->formatStateUsing(fn ($state) => $state->label()),
            Tables\Columns\TextColumn::make('city')->label('Lokasi')->formatStateUsing(fn ($state, Asset $record) => $state.', '.$record->province),
            Tables\Columns\TextColumn::make('status')->badge()->formatStateUsing(fn ($state) => $state->label())->color(fn ($state) => match ($state) {
                AssetStatus::Published => 'success', AssetStatus::RevisionRequired => 'danger', AssetStatus::PendingReview => 'warning', default => 'gray'
            }),
            Tables\Columns\TextColumn::make('submitted_at')->label('Diajukan')->dateTime('d M Y H:i')->sortable(),
        ])->defaultSort('submitted_at', 'desc')->filters([
            Tables\Filters\SelectFilter::make('status')->options(collect(AssetStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()])->all()),
            Tables\Filters\SelectFilter::make('asset_category_id')->label('Kategori')->relationship('category', 'name'),
        ])->actions([
            Tables\Actions\ViewAction::make(),
            Tables\Actions\Action::make('editSlug')->label('Ubah Slug')->icon('heroicon-o-link')->form([
                Forms\Components\TextInput::make('slug')->required()->maxLength(180)->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')->unique('assets', 'slug', ignoreRecord: true),
            ])->fillForm(fn (Asset $record) => ['slug' => $record->slug])->action(function (Asset $record, array $data) {
                $record->update(['slug' => $data['slug']]);
                Notification::make()->success()->title('Slug diperbarui; URL lama tetap diarahkan.')->send();
            }),
            Tables\Actions\Action::make('review')->label('Review')->icon('heroicon-o-clipboard-document-check')->color('warning')
                ->visible(fn (Asset $record) => $record->status === AssetStatus::PendingReview)
                ->form([
                    Forms\Components\Toggle::make('data_complete')->label('Data lengkap')->required(),
                    Forms\Components\Toggle::make('basic_legality')->label('Legalitas dasar')->required(),
                    Forms\Components\Toggle::make('photos_adequate')->label('Foto memadai')->required(),
                    Forms\Components\Toggle::make('publishable')->label('Layak dipublikasikan')->required(),
                    Forms\Components\Textarea::make('notes')->label('Catatan review')->maxLength(2000)
                        ->required(fn (Forms\Get $get) => ! ($get('data_complete') && $get('basic_legality') && $get('photos_adequate') && $get('publishable'))),
                ])->action(function (Asset $record, array $data) {
                    $passed = $data['data_complete'] && $data['basic_legality'] && $data['photos_adequate'] && $data['publishable'];
                    app(AssetReviewService::class)->review($record, auth()->user(), $data, $data['notes'] ?? null);
                    Notification::make()->success()->title($passed ? 'Aset dipublikasikan' : 'Revisi diminta')->send();
                }),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListAssets::route('/'), 'view' => Pages\ViewAsset::route('/{record}')];
    }
}
