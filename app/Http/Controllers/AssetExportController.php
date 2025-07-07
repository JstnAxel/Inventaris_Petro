<?php

namespace App\Http\Controllers;

use App\Exports\AssetExport;
use Maatwebsite\Excel\Facades\Excel;

class AssetExportController extends Controller
{
    public function __invoke()
    {
        return Excel::download(new AssetExport, 'Assets_' . now()->format('Ymd_His') . '.xlsx');
    }
}
