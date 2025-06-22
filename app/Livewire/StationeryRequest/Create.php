<?php

namespace App\Livewire\StationeryRequest;

use Livewire\Component;
use App\Models\Stationary;
use App\Models\StationeryRequest;
use Illuminate\Support\Facades\Auth;

class Create extends Component
{
    public $requests = [
        ['stationary_id' => '', 'quantity' => 1]
    ];

    public function mount()
    {
        if (! auth()->user()->hasAnyPermission(['view stationary', 'view both'])) {
            abort(403);
        }
    }


    public function addRow()
    {
        $this->requests[] = ['stationary_id' => '', 'quantity' => 1];
    }

    public function removeRow($index)
    {
        unset($this->requests[$index]);
        $this->requests = array_values($this->requests); // Reindex
    }

    public function submit()
    {
        $stationaryMap = Stationary::whereIn('id', collect($this->requests)->pluck('stationary_id'))->get()->keyBy('id');

        $this->validate([
            'requests.*.stationary_id' => [
                'required',
                function ($attribute, $value, $fail) use ($stationaryMap) {
                    $item = $stationaryMap[$value] ?? null;
                    if (!$item || $item->stock <= 0) {
                        $fail("Stok tidak tersedia.");
                    }
                }
            ],
            'requests.*.quantity' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) use ($stationaryMap) {
                    $index = explode('.', $attribute)[1];
                    $stationary_id = $this->requests[$index]['stationary_id'] ?? null;
                    $item = $stationaryMap[$stationary_id] ?? null;
                    if ($item && $value > $item->stock) {
                        $fail("Jumlah melebihi stok untuk {$item->name}.");
                    }
                }
            ]
        ]);

        foreach ($this->requests as $req) {
            StationeryRequest::create([
                'user_id' => Auth::id(),
                'stationary_id' => $req['stationary_id'],
                'quantity' => $req['quantity'],
                'status' => 'pending',
            ]);
        }

        $this->dispatch('swal', [
            'title' => 'Berhasil!',
            'text' => 'Peminjaman asset berhasil diajukan.',
            'icon' => 'success',
        ]);
        $this->reset('requests');
        $this->requests = [['stationary_id' => '', 'quantity' => 1]];
    }

    public function render()
    {
        $items = Stationary::where('stock', '>', 0)->get();
        $history = StationeryRequest::with('stationary')
            ->where('user_id', Auth::id())
            ->latest()
            ->take(10)
            ->get();

        return view('livewire.stationery-request.create', compact('items', 'history'));
    }
}
