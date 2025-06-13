<?php

namespace App\Filament\Resources\AssetResource\Widgets;

use App\Models\Asset;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class LatestAssets extends BaseWidget
{
    protected function getTableQuery(): Builder
    {
        return Asset::query()
            ->latest()    // order by created_at descending
            ->take(5);    // ambil 5 data teratas
    }

    protected function getTableColumns(): array
    {
        return [

            TextColumn::make('name')
                ->label('Nama'),

            TextColumn::make('category.name')
                ->label('Kategori'),

            TextColumn::make('status')
                ->badge()
                ->color(fn(string $state): string => match ($state) {
                    'available' => 'success',
                    'loaned' => 'warning',
                    'maintenance' => 'danger',
                    default => 'gray',
                }),
        ];
    }
}
