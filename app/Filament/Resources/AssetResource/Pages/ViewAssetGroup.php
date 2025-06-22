<?php

namespace App\Filament\Resources\AssetResource\Pages;

use App\Filament\Resources\AssetResource;
use App\Models\Asset;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class ViewAssetGroup extends Page implements HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string $resource = AssetResource::class;
    protected static string $view = 'filament.resources.asset-resource.pages.view-asset-group';
    protected static ?string $title = 'Asset Items';

    public Asset $record;

    public function mount(Asset $record): void
    {
        $this->record = $record;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Asset::query()->where('name', $this->record->name)
            )
            ->columns([
                Tables\Columns\ImageColumn::make('image')->square()->label('Foto'),
                Tables\Columns\TextColumn::make('code'),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('category.name')->label('Category'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'loaned' => 'warning',
                        'available' => 'success',
                        'maintenance' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('user.name')->label('Input By')->sortable(),
            ])
            ->filters([
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->url(fn($record) => route('filament.admin.resources.inventory.assets.edit', ['record' => $record->getKey()])),
                Tables\Actions\DeleteAction::make()
                    ->label('Delete')
                    ->requiresConfirmation()
                    ->color('danger')
                    ->action(fn(Asset $record) => $record->delete())
    ->after(function () {
        // pakai Filament notification kalau mau
        return redirect(AssetResource::getUrl('index'));
    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('delete')
                        ->label('Delete Permanent')
                        ->requiresConfirmation()
                        ->color('danger')
                        ->action(fn(\Illuminate\Support\Collection $records) => $records->each->delete())
    ->after(function () {
        // pakai Filament notification kalau mau
        return redirect(AssetResource::getUrl('index'));
    }),
                ]),
            ]);
    }
}
