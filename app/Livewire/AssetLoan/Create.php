<?php

namespace App\Livewire\AssetLoan;

use Livewire\Component;
use App\Models\Asset;
use App\Models\AssetLoan;
use Illuminate\Support\Facades\Auth;

class Create extends Component
{
    public $rows = [
        ['asset_id' => ''],
    ];

    public $assets;

    public function mount()
    {
        if (! auth()->user()->hasAnyPermission(['view asset', 'view both'])) {
            abort(403);
        }

        $this->assets = Asset::where('status', 'available')
            ->select('name')
            ->selectRaw('MIN(id) as id') // Ambil satu ID untuk tiap nama
            ->groupBy('name')
            ->with('category') // jika butuh relasi lain
            ->get();
    }

    public function addRow()
    {
        $this->rows[] = ['asset_id' => ''];
    }

    public function removeRow($index)
    {
        unset($this->rows[$index]);
        $this->rows = array_values($this->rows);
    }


    public function submit()
    {
        $rules = [];
        foreach ($this->rows as $index => $row) {
            $rules["rows.$index.asset_id"] = [
                'required',
                function ($attr, $value, $fail) {
                    $asset = Asset::find($value);
                    if (!$asset || $asset->status !== 'available') {
                        $fail("Asset tidak tersedia atau sedang digunakan.");
                    }
                }
            ];
        }

        $this->validate($rules);

        $user = Auth::user();
        $departmentName = $user->department ?? 'UMUM';

        // Hilangkan huruf vokal
        $departmentCode = preg_replace('/[aeiouAEIOU]/', '', $departmentName);

        // Ambil nomor urut terakhir
        $lastLoan = AssetLoan::whereNotNull('code')
            ->orderBy('created_at', 'desc')
            ->first();

        $lastNumber = 0;
        if ($lastLoan) {
            $parts = explode('/', $lastLoan->code);
            if (count($parts) > 0) {
                $lastNumber = (int) ltrim($parts[0], '0');
            }
        }

        $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);

        // Gabungkan kode barang semua asset yang dipinjam sekaligus (misal pakai dash)
        foreach ($this->rows as $row) {
            $lastLoan = AssetLoan::whereNotNull('code')
                ->orderBy('created_at', 'desc')
                ->first();

            $lastNumber = 0;
            if ($lastLoan) {
                $parts = explode('/', $lastLoan->code);
                if (count($parts) > 0) {
                    $lastNumber = (int) ltrim($parts[0], '0');
                }
            }

            $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);

            $asset = Asset::find($row['asset_id']);
            $assetCode = $asset->code ?? 'UNKNOWN';

            $kodePeminjaman = "{$newNumber}/{$departmentCode}/{$assetCode}";

            AssetLoan::create([
                'user_id' => $user->id,
                'asset_id' => $row['asset_id'],
                'status' => 'pending',
            ]);
        }

        session()->flash('success', 'Peminjaman asset berhasil diajukan.');
        $this->reset('rows');
        $this->rows = [['asset_id' => '']];
    }

    public function render()
    {
        $history = AssetLoan::where('user_id', Auth::id())
            ->with('asset')
            ->latest()
            ->get();

        return view('livewire.asset-loan.create', [
            'history' => $history,
        ]);
    }
}
