<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssetResource\Pages;
use App\Models\Asset;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Asset';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $slug = 'inventory/assets';
    protected static ?string $navigationGroup = 'Inventory';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('code')
                ->label('Code')
                ->disabled()
                ->dehydrated(false)
                ->visibleOn('edit'),

            TextInput::make('name')
                ->required()
                ->datalist(
                    Asset::query()->select('name')->distinct()->pluck('name')->toArray()
                ),

            Select::make('category_id')
                ->label('Category')
                ->relationship('category', 'name', fn($query) => $query->where('type', 'asset'))
                ->required(),

            Select::make('status')
                ->options([
                    'available' => 'Available',
                    'loaned' => 'Loaned',
                    'maintenance' => 'Maintenance',
                ])->required(),

            Textarea::make('note'),

            FileUpload::make('image')->image()->disk('public')->directory('assets'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $ids = Asset::query()
                    ->selectRaw('MIN(id) as id')
                    ->groupBy('name')
                    ->pluck('id');
                return Asset::query()->whereIn('id', $ids);
            })
            ->columns([
                TextColumn::make('name')->label('Asset Name')->searchable(),

                TextColumn::make('total')
                    ->label('Total Items')
                    ->getStateUsing(fn($record) =>
                        Asset::where('name', $record->name)->count()
                    ),

                TextColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(fn($record) => match (true) {
                        Asset::where('name', $record->name)->where('status', 'available')->exists() => 'available',
                        Asset::where('name', $record->name)->where('status', 'maintenance')->exists() => 'maintenance',
                        Asset::where('name', $record->name)->where('status', 'loaned')->exists() => 'loaned',
                        default => 'unknown',
                    })
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'loaned' => 'warning',
                        'available' => 'success',
                        'maintenance' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->actions([
                Action::make('view')
                    ->label('View Items')
                    ->url(fn($record) => route('filament.admin.resources.inventory.assets.view', ['name' => $record->name])),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssets::route('/'),
            'create' => Pages\CreateAsset::route('/create'),
            'edit' => Pages\EditAsset::route('/{record}/edit'),
            'view' => Pages\ViewAssetGroup::route('/group/{name}'),
        ];
    }
}
