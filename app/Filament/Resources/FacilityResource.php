<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FacilityResource\Pages;
use App\Models\Facility;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class FacilityResource extends Resource
{
    protected static ?string $model = Facility::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-circle';

    protected static ?string $navigationGroup = 'Asset Matching';

    protected static ?string $navigationLabel = 'Fasilitas Aset';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label('Nama Fasilitas')->required()->maxLength(100)->live(onBlur: true)
                ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set('slug', Str::slug($state))),
            Forms\Components\TextInput::make('slug')->required()->maxLength(120)->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('icon')->label('Material Icon')->helperText('Contoh: wifi, bolt, local_parking')->maxLength(60),
            Forms\Components\TextInput::make('sort_order')->label('Urutan')->numeric()->default(0)->required(),
            Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->label('Fasilitas')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('icon')->label('Icon')->badge(),
            Tables\Columns\TextColumn::make('assets_count')->counts('assets')->label('Dipakai')->badge(),
            Tables\Columns\TextColumn::make('sort_order')->label('Urutan')->sortable(),
            Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
        ])->defaultSort('sort_order')->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListFacilities::route('/'), 'create' => Pages\CreateFacility::route('/create'), 'edit' => Pages\EditFacility::route('/{record}/edit')];
    }
}
