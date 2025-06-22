<?php

namespace App\Livewire\AssetLoan;

use Livewire\Component;
use App\Models\Asset;
use App\Models\AssetLoan;
use Illuminate\Support\Facades\Auth;

class Create extends Component
{
    public $rows = [
        ['asset_id' => '', 'quantity' => 1],
    ];

    public $assets;

    public function mount()
    {
        if (!auth()->user()->hasAnyPermission(['view asset', 'view both'])) {
            abort(403);
        }

        $this->assets = Asset::where('status', 'available')
            ->select('name')
            ->selectRaw('MIN(id) as id')
            ->groupBy('name')
            ->get();
    }

    public function addRow()
    {
        $this->rows[] = ['asset_id' => '', 'quantity' => 1];
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
            $rules["rows.$index.asset_id"] = ['required', 'exists:assets,id'];
            $rules["rows.$index.quantity"] = ['required', 'integer', 'min:1'];
        }

        $this->validate($rules);

        $user = Auth::user();

        foreach ($this->rows as $row) {
            for ($i = 0; $i < $row['quantity']; $i++) {
                AssetLoan::create([
                    'user_id' => $user->id,
                    'asset_id' => $row['asset_id'],
                    'status' => 'pending',
                ]);
            }
        }

        session()->flash('success', 'Peminjaman asset berhasil diajukan.');
        $this->reset('rows');
        $this->rows = [['asset_id' => '', 'quantity' => 1]];
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
