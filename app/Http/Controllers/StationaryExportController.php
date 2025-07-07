<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\StationaryExport;
use Maatwebsite\Excel\Facades\Excel;


class StationaryExportController extends Controller
{
        public function __invoke()
    {
        return Excel::download(new StationaryExport, 'Stationery_' . now()->format('Ymd_His') . '.xlsx');
    }

}
