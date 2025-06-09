<?php

namespace App\Filament\Resources\StationeryRequestResource\Pages;

use App\Filament\Resources\StationeryRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStationeryRequest extends EditRecord
{
    protected static string $resource = StationeryRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
