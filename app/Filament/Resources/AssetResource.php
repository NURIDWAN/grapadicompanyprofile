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
                Forms\Components\Select::make('owner_id')
                    ->label('Pemilik')
                    ->relationship('owner', 'name')
                    ->searchable()
                    ->preload()
                    ->default(fn () => auth()->id())
                    ->required(),
            ])->columns(1),
            Forms\Components\Section::make('Informasi Aset')->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Aset')
                    ->required()
                    ->maxLength(150)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set('slug', \Illuminate\Support\Str::slug($state))),
                Forms\Components\TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(180)
                    ->unique('assets', 'slug', ignoreRecord: true),
                Forms\Components\Select::make('asset_category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->required(),
                Forms\Components\Select::make('listing_status')
                    ->label('Status Listing')
                    ->options(collect(AssetListingStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()]))
                    ->required()
                    ->default(AssetListingStatus::Available->value),
                Forms\Components\TextInput::make('area_sqm')
                    ->label('Luas (m²)')
                    ->numeric()
                    ->required()
                    ->minValue(0.01),
                Forms\Components\TextInput::make('price')
                    ->label('Harga')
                    ->numeric()
                    ->minValue(0)
                    ->prefix('Rp'),
                Forms\Components\TextInput::make('price_per_sqm')
                    ->label('Harga/m²')
                    ->numeric()
                    ->minValue(0)
                    ->prefix('Rp'),
            ])->columns(2),
            Forms\Components\Section::make('Lokasi')->schema([
                Forms\Components\TextInput::make('province')->label('Provinsi')->required()->maxLength(100),
                Forms\Components\TextInput::make('city')->label('Kabupaten/Kota')->required()->maxLength(100),
                Forms\Components\TextInput::make('district')->label('Kecamatan')->required()->maxLength(100),
                Forms\Components\TextInput::make('village')->label('Kelurahan/Desa')->required()->maxLength(100),
                Forms\Components\Textarea::make('full_address')->label('Alamat Lengkap')->required()->maxLength(1000)->columnSpanFull(),
                Forms\Components\TextInput::make('google_maps_url')->label('Google Maps')->url()->maxLength(2000)->columnSpanFull(),
            ])->columns(2),
            Forms\Components\Section::make('Legalitas')->schema([
                Forms\Components\TextInput::make('certificate_type')->label('Sertifikat')->required()->maxLength(50),
            ]),
            Forms\Components\Section::make('Detail Aset')->schema([
                Forms\Components\Textarea::make('description')->label('Deskripsi')->required()->maxLength(10000)->columnSpanFull(),
                Forms\Components\Select::make('condition')
                    ->label('Kondisi')
                    ->options(collect(\App\Enums\AssetCondition::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()]))
                    ->required(),
                Forms\Components\Select::make('utilization_status')
                    ->label('Pemanfaatan')
                    ->options(collect(\App\Enums\AssetUtilizationStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()]))
                    ->required(),
                Forms\Components\Select::make('objective')
                    ->label('Tujuan')
                    ->options(collect(\App\Enums\AssetObjective::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()]))
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Status Review')
                    ->options(collect(AssetStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()]))
                    ->required()
                    ->default(AssetStatus::Draft->value),
            ])->columns(2),
            Forms\Components\Section::make('Fasilitas')->schema([
                Forms\Components\CheckboxList::make('facilities')
                    ->label('Fasilitas')
                    ->relationship('facilities', 'name')
                    ->columns(3),
            ]),
            Forms\Components\Section::make('Foto Aset')->schema([
                Forms\Components\Repeater::make('photos')
                    ->label('Foto Aset')
                    ->relationship('photos')
                    ->schema([
                        Forms\Components\FileUpload::make('path')
                            ->label('Foto')
                            ->disk('public')
                            ->directory('assets')
                            ->image()
                            ->required(),
                        Forms\Components\TextInput::make('alt_text')
                            ->label('Alt Text')
                            ->maxLength(180),
                    ])
                    ->columns(2)
                    ->defaultItems(1)
                    ->reorderableWithDragAndDrop('sort_order'),
            ]),
            Forms\Components\Section::make('SEO Otomatis')->schema([
                Forms\Components\TextInput::make('seo_title')->label('SEO Title')->disabled()->columnSpanFull(),
                Forms\Components\Textarea::make('meta_description')->label('Meta Description')->disabled()->columnSpanFull(),
            ])->collapsed(),
            Forms\Components\Section::make('Riwayat Review')->schema([
                Forms\Components\Placeholder::make('review_history')->hiddenLabel()->content(function (?Asset $record) {
                    if (! $record || $record->reviews->isEmpty()) {
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
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
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
        return [
            'index' => Pages\ListAssets::route('/'),
            'create' => Pages\CreateAsset::route('/create'),
            'view' => Pages\ViewAsset::route('/{record}'),
            'edit' => Pages\EditAsset::route('/{record}/edit'),
        ];
    }
}
