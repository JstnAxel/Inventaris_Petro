<?php

namespace App\Filament\Resources\StationaryResource\Pages;

use App\Filament\Resources\StationaryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStationaries extends ListRecords
{
    protected static string $resource = StationaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
