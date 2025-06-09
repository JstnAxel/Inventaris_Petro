<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StationaryResource\Pages;
use App\Filament\Resources\StationaryResource\RelationManagers;
use App\Models\Stationary;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StationaryResource extends Resource
{
    protected static ?string $model = Stationary::class;

    protected static ?string $slug = 'inventory/stationery';

    public static function getModelLabel(): string
    {
        return 'Stationery';
    }
    public static function getPluralModelLabel(): string
    {
        return 'Stationery';
    }

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationIcon = 'heroicon-o-pencil';
    protected static ?string $navigationGroup = 'Inventory';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('code')
                    ->label('Code')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('name')->required(),
                Select::make('category_id')
                    ->relationship('category', 'name', fn($query) => $query->where('type', 'stationery'))
                    ->label('Category')
                    ->required(),
                TextInput::make('stock')->numeric()->required(),
                TextInput::make('unit')->required(),
                Textarea::make('note'),
                TextInput::make('price')->numeric()->prefix('Rp')->suffix('.00'),
                FileUpload::make('image')->image()->disk('public')->directory('stationary'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('image')
                    ->square()
                    ->label('Foto'),
                TextColumn::make('code'),
                TextColumn::make('name'),
                TextColumn::make('category.name'),
                TextColumn::make('stock')
                    ->label('Stok')
                    ->formatStateUsing(fn ($record) => "{$record->stock} {$record->unit}"),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => !$record->trashed()),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn($record) => !$record->trashed()),

                Tables\Actions\RestoreAction::make()
                    ->visible(fn($record) => $record->trashed()),

                Tables\Actions\ForceDeleteAction::make()
                    ->label('Delete Permanent')
                    ->visible(fn($record) => $record->trashed())
                    ->requiresConfirmation()
                    ->color('danger'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStationaries::route('/'),
            'create' => Pages\CreateStationary::route('/create'),
            'edit' => Pages\EditStationary::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
