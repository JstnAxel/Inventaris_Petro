<?php

namespace App\Filament\Resources\StationaryResource\Pages;

use App\Filament\Resources\StationaryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStationary extends EditRecord
{
    protected static string $resource = StationaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');  // redirect ke list page
    }

}
