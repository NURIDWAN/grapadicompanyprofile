<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadResource\Pages;
use App\Filament\Resources\LeadResource\RelationManagers;
use App\Models\Lead;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    protected static ?string $navigationGroup = 'CRM';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Leads';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Lead Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('whatsapp')
                            ->label('No. WhatsApp')
                            ->tel()
                            ->placeholder('08xxxxxxxxxx')
                            ->maxLength(20),
                        Forms\Components\TextInput::make('company')
                            ->label('Perusahaan')
                            ->maxLength(100),
                        Forms\Components\Select::make('status')
                            ->options(Lead::STATUS_OPTIONS)
                            ->default('new')
                            ->required(),
                        Forms\Components\Select::make('source')
                            ->label('Sumber')
                            ->options(Lead::SOURCE_OPTIONS)
                            ->default('manual')
                            ->required(),
                        Forms\Components\TextInput::make('value')
                            ->label('Value (Rp)')
                            ->numeric()
                            ->prefix('Rp'),
                    ])->columns(2),

                Forms\Components\Section::make('Catatan')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('whatsapp')
                    ->label('WhatsApp')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-phone'),
                Tables\Columns\TextColumn::make('company')
                    ->label('Perusahaan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('asset.name')
                    ->label('Aset Matching')
                    ->searchable()
                    ->url(fn (Lead $record): ?string => $record->asset ? \App\Filament\Resources\AssetResource::getUrl('view', ['record' => $record->asset]) : null)
                    ->color('primary')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'info',
                        'contacted' => 'warning',
                        'meeting' => 'primary',
                        'proposal' => 'purple',
                        'negotiation' => 'pink',
                        'deal' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => Lead::STATUS_OPTIONS[$state] ?? $state),
                Tables\Columns\TextColumn::make('source')
                    ->label('Sumber')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'web_wa' => 'success',
                        'manual' => 'gray',
                        'referral' => 'info',
                        'social' => 'purple',
                        'event' => 'warning',
                        'asset_matching' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => Lead::SOURCE_OPTIONS[$state] ?? $state),
                Tables\Columns\TextColumn::make('value')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_converted')
                    ->label('Converted')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(Lead::STATUS_OPTIONS),
                Tables\Filters\SelectFilter::make('source')
                    ->label('Sumber')
                    ->options(Lead::SOURCE_OPTIONS),
                Tables\Filters\TernaryFilter::make('is_converted')
                    ->label('Converted')
                    ->placeholder('All')
                    ->trueLabel('Converted')
                    ->falseLabel('Not Converted')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('converted_to_client_id'),
                        false: fn ($query) => $query->whereNull('converted_to_client_id'),
                    ),
            ])
            ->actions([
                Tables\Actions\Action::make('whatsapp')
                    ->label('Chat WA')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color('success')
                    ->url(fn (Lead $record): ?string => $record->whatsapp_url)
                    ->openUrlInNewTab()
                    ->visible(fn (Lead $record): bool => ! empty($record->whatsapp)),
                Tables\Actions\Action::make('convert')
                    ->label('Convert to Client')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Convert Lead to Client')
                    ->modalDescription('Lead ini akan dikonversi menjadi Client. Status lead akan berubah menjadi "Deal Won".')
                    ->action(function (Lead $record) {
                        $client = $record->convertToClient();
                        Notification::make()
                            ->success()
                            ->title('Lead converted!')
                            ->body("Lead berhasil dikonversi menjadi Client: {$client->company_name}")
                            ->send();
                    })
                    ->visible(fn (Lead $record): bool => ! $record->is_converted),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\InteractionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeads::route('/'),
            'create' => Pages\CreateLead::route('/create'),
            'view' => Pages\ViewLead::route('/{record}'),
            'edit' => Pages\EditLead::route('/{record}/edit'),
        ];
    }
}
