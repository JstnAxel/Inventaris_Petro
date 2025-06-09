<?php

namespace App\Filament\Resources\AssetLoanResource\Pages;

use App\Filament\Resources\AssetLoanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssetLoan extends EditRecord
{
    protected static string $resource = AssetLoanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
