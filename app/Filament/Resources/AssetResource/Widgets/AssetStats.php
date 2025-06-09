<?php

namespace App\Filament\Resources\AssetResource\Widgets;

use Filament\Widgets\Widget;
use App\Models\Asset;


class AssetStats extends Widget
{
    protected static string $view = 'filament.resources.asset-resource.widgets.asset-stats';

        public function getStats(): array
    {
        return [
            'total' => Asset::count(),
            'available' => Asset::where('status', 'available')->count(),
            'loaned' => Asset::where('status', 'loaned')->count(),
            'maintenance' => Asset::where('status', 'maintenance')->count(),
        ];
    }

}
