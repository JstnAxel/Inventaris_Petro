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
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;




class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Asset';
    protected static ?string $recordTitleAttribute = 'slug';
    protected static ?string $slug = 'inventory/assets';
    protected static ?string $navigationGroup = 'Inventory';
    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return route('filament.admin.resources.inventory.assets.view', ['record' => $record->slug]);
    }
    public static function getGlobalSearchEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $ids = parent::getEloquentQuery()
            ->withTrashed() // take trashed too
            ->selectRaw('MIN(id) as id')
            ->groupBy('name')
            ->pluck('id');

        return parent::getEloquentQuery()->withTrashed()->whereIn('id', $ids);
    }


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
                ->live(debounce: 2000)
                ->datalist(
                    Asset::query()->select('name')->distinct()->pluck('name')->toArray()
                )
                ->afterStateUpdated(
                    fn($state, callable $set) =>
                    $set('slug', Str::slug($state))
                )
                ->afterStateHydrated(function ($state, callable $set) {
                    if (blank($state)) return;
                    $set('slug', Str::slug($state));
                }),


            TextInput::make('slug')
                ->label('slug')
                ->dehydrated(fn() => true)
                ->required()
                ->readOnly(), // bisa tampil tapi tidak bisa diketik manual

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
        ->headerActions([
    Action::make('exportExcel')
        ->label('Export ke Excel')
        ->icon('heroicon-o-document')
        ->url(route('export.assets'))
        ->openUrlInNewTab(),
])

            ->query(function () {
                $ids = Asset::query()
                    ->withTrashed() // take trashed too
                    ->selectRaw('MIN(id) as id')
                    ->groupBy('name')
                    ->pluck('id');
                return Asset::query()->withTrashed()->whereIn('id', $ids);
            })
            ->columns([
                TextColumn::make('name')->label('Asset Name')->searchable(),

                TextColumn::make('total')
                    ->label('Total Items')
                    ->getStateUsing(
                        fn($record) =>
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
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->getStateUsing(
                        fn($record) =>
                        optional(
                            Asset::where('name', $record->name)->orderBy('created_at')->first()
                        )?->created_at?->timezone('Asia/Jakarta')->format('d M Y H:i')
                    ),

                TextColumn::make('updated_at')
                    ->label('Terakhir Diupdate')
                    ->getStateUsing(
                        fn($record) =>
                        optional(
                            Asset::where('name', $record->name)->orderByDesc('updated_at')->first()
                        )?->updated_at?->timezone('Asia/Jakarta')->format('d M Y H:i')
                    ),

            ])
            ->actions([
                Action::make('view')
                    ->label('View Items')
                    ->button()
                    ->color('primary')
                    ->url(fn($record) => route('filament.admin.resources.inventory.assets.view', $record)),
                                ForceDeleteAction::make()
                ->label('Delete Permanent')
                ->color('danger')
                ->requiresConfirmation(),

            RestoreAction::make()
                ->label('Restore')
                ->color('success')
                ->requiresConfirmation(),

            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->bulkActions([
            BulkActionGroup::make([
                DeleteBulkAction::make(),
                ForceDeleteBulkAction::make()
                    ->label('Delete Permanent')
                    ->color('danger')
                    ->requiresConfirmation(),
                RestoreBulkAction::make()
                    ->label('Restore')
                    ->color('success')
                    ->requiresConfirmation(),
            ]),
        ]);

    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssets::route('/'),
            'create' => Pages\CreateAsset::route('/create'),
            'edit' => Pages\EditAsset::route('/{record}/edit'),
            'view' => Pages\ViewAssetGroup::route('/group/{record:slug}'),
        ];
    }
}
