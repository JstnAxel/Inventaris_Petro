<?php

namespace App\Filament\Widgets;

use App\Models\Stationary;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class LatestStationary extends BaseWidget
{
    protected function getTableQuery(): Builder
    {
        return Stationary::query()
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

            TextColumn::make('stock')
                ->label('Stok'),

        ];
    }
}
