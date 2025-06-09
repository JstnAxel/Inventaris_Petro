<?php

namespace App\Filament\Resources\AssetLoanResource\Pages;

use App\Filament\Resources\AssetLoanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAssetLoans extends ListRecords
{
    protected static string $resource = AssetLoanResource::class;

    protected function canCreate(): bool
    {
        return false;
    }
}
