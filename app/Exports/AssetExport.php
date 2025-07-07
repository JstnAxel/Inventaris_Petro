<?php

namespace App\Exports;

use App\Models\Asset;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AssetExport implements FromView
{
    public function view(): View
    {
        $ids = Asset::query()
            ->withTrashed()
            ->selectRaw('MIN(id) as id')
            ->groupBy('name')
            ->pluck('id');

        $assets = Asset::withTrashed()->whereIn('id', $ids)->get();

        return view('exports.assets', [
            'assets' => $assets,
        ]);
    }
}
