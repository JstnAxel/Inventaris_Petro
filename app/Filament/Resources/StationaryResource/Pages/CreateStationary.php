<?php

namespace App\Filament\Resources\StationaryResource\Pages;

use App\Filament\Resources\StationaryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateStationary extends CreateRecord
{
    protected static string $resource = StationaryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');  // redirect ke list page
    }

}
