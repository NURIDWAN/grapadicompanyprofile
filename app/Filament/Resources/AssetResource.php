<?php

namespace App\Filament\Resources;

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
            Forms\Components\Section::make('Pemilik dan Aset')->schema([
                Forms\Components\TextInput::make('owner.name')->label('Pemilik')->disabled(),
                Forms\Components\TextInput::make('owner.whatsapp')->label('WhatsApp')->disabled(),
                Forms\Components\TextInput::make('name')->label('Nama Aset')->disabled(),
                Forms\Components\TextInput::make('category.name')->label('Kategori')->disabled(),
                Forms\Components\TextInput::make('province')->disabled(), Forms\Components\TextInput::make('city')->disabled(),
                Forms\Components\Textarea::make('full_address')->label('Alamat Lengkap')->disabled()->columnSpanFull(),
                Forms\Components\TextInput::make('area_sqm')->label('Luas (m²)')->disabled(),
                Forms\Components\TextInput::make('certificate_type')->label('Jenis Sertifikat')->disabled(),
                Forms\Components\TextInput::make('certificate_number')->label('Nomor Sertifikat')->disabled(),
                Forms\Components\Placeholder::make('certificate_download')->label('Dokumen Sertifikat')->content(fn (Asset $record) => new HtmlString('<a class="text-primary-600 underline" href="'.route('matching.assets.certificate', $record).'">Unduh dokumen privat</a>')),
            ])->columns(2),
            Forms\Components\Section::make('Status Aset')->schema([
                Forms\Components\TextInput::make('condition')->formatStateUsing(fn ($state) => $state instanceof \App\Enums\AssetCondition ? $state->label() : (\App\Enums\AssetCondition::tryFrom((string) $state)?->label() ?? $state))->disabled(),
                Forms\Components\Textarea::make('condition_notes')->disabled(),
                Forms\Components\TextInput::make('ownership_status')->formatStateUsing(fn ($state) => $state instanceof \App\Enums\AssetOwnershipStatus ? $state->label() : (\App\Enums\AssetOwnershipStatus::tryFrom((string) $state)?->label() ?? $state))->disabled(),
                Forms\Components\Textarea::make('ownership_notes')->disabled(),
                Forms\Components\TextInput::make('utilization_status')->formatStateUsing(fn ($state) => $state instanceof \App\Enums\AssetUtilizationStatus ? $state->label() : (\App\Enums\AssetUtilizationStatus::tryFrom((string) $state)?->label() ?? $state))->disabled(),
                Forms\Components\Textarea::make('utilization_notes')->disabled(),
                Forms\Components\TextInput::make('objective')->formatStateUsing(fn ($state) => $state instanceof \App\Enums\AssetObjective ? $state->label() : (\App\Enums\AssetObjective::tryFrom((string) $state)?->label() ?? $state))->disabled(),
                Forms\Components\TextInput::make('status')->formatStateUsing(fn ($state) => $state instanceof AssetStatus ? $state->label() : (AssetStatus::tryFrom((string) $state)?->label() ?? $state))->disabled(),
            ])->columns(2),
            Forms\Components\Section::make('Foto Aset')->schema([
                Forms\Components\Placeholder::make('photo_gallery')->hiddenLabel()->content(function (Asset $record) {
                    $images = $record->photos->map(fn ($photo) => '<img src="'.e(asset('storage/'.$photo->path)).'" alt="Foto aset" style="width:180px;height:120px;object-fit:cover;border-radius:8px">')->implode('');

                    return new HtmlString('<div style="display:flex;flex-wrap:wrap;gap:12px">'.$images.'</div>');
                }),
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
