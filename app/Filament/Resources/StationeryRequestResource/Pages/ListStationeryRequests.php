<?php

namespace App\Filament\Resources\StationeryRequestResource\Pages;

use App\Filament\Resources\StationeryRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStationeryRequests extends ListRecords
{
    protected static string $resource = StationeryRequestResource::class;

    protected function canCreate(): bool
    {
        return false;
    }
}
