<?php

namespace App\Exports;

use App\Models\Stationary;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class StationaryExport implements FromView
{
    public function view(): View
    {
        $bulan = session('filter_bulan', now()->format('m'));
        $tahun = session('filter_tahun', now()->format('Y'));

        $stationaries = Stationary::with('category')
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->get();

        return view('exports.stationaries', [
            'stationaries' => $stationaries,
            'bulan' => $bulan,
            'tahun' => $tahun,
        ]);
    }
}
