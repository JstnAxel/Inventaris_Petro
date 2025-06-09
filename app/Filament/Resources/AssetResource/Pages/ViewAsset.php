<?php

namespace App\Filament\Resources\AssetResource\Pages;

use App\Filament\Resources\AssetResource;
use Filament\Resources\Pages\ViewRecord;

class ViewAsset extends ViewRecord
{
    protected static string $resource = AssetResource::class;

    public function getView(): string
    {
        return 'filament.resources.asset-resource.pages.view-asset';
    }

    public function getViewData(): array
    {
        return [
            'record' => $this->record,
        ];
    }
}
