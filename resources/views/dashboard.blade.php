<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
        <!-- Welcome Message -->
        <div class="text-xl font-semibold text-zinc-800 dark:text-zinc-200">
            Selamat datang, {{ $user->name }}!
        </div>

        <!-- Info Boxes -->
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="relative aspect-video flex flex-col items-center justify-center rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                <div class="text-4xl font-bold text-blue-600">{{ $assets }}</div>
                <div class="text-sm text-zinc-600 dark:text-zinc-400 mt-2">Asset Tersedia</div>
            </div>
            <div class="relative aspect-video flex flex-col items-center justify-center rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                <div class="text-4xl font-bold text-green-600">{{ $stationeries }}</div>
                <div class="text-sm text-zinc-600 dark:text-zinc-400 mt-2">Stationery Tersedia</div>
            </div>
            <div class="relative aspect-video flex flex-col items-center justify-center rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                <div class="text-4xl font-bold text-purple-600">{{ now()->format('d M Y') }}</div>
                <div class="text-sm text-zinc-600 dark:text-zinc-400 mt-2">Tanggal Hari Ini</div>
            </div>
        </div>

        <!-- Recent Activity Section -->
        <div class="relative flex-1 overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
            <h2 class="text-lg font-semibold text-zinc-800 dark:text-zinc-200 mb-4">Riwayat Aktivitas Terakhir</h2>
            
            @php
                $loanHistory = \App\Models\AssetLoan::with('asset')
                    ->where('user_id', $user->id)
                    ->latest()
                    ->take(5)
                    ->get();

                $stationeryRequests = \App\Models\StationeryRequest::with('stationary')
                    ->where('user_id', $user->id)
                    ->latest()
                    ->take(5)
                    ->get();
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Asset Loan History -->
                <div>
                    <h3 class="font-medium text-sm text-zinc-600 dark:text-zinc-400 mb-2">Peminjaman Asset</h3>
                    <ul class="space-y-2 text-sm">
                        @forelse ($loanHistory as $loan)
                            <li class="p-3 rounded-lg bg-zinc-100 dark:bg-neutral-800">
                                <span class="font-medium">{{ $loan->asset->name ?? 'Asset tidak ditemukan' }}</span><br>
                                    <div class="flex flex-col">
                                        <span class="text-xs text-zinc-500">Status: {{ ucfirst($loan->status) }} </span>
                                        <span class="text-xs text-zinc-500">{{ $loan->created_at->diffForHumans() }}</span>
                                    </div>
                            </li>
                        @empty
                            <li class="text-zinc-500 dark:text-zinc-400">Belum ada riwayat peminjaman.</li>
                        @endforelse
                    </ul>
                </div>

                <!-- Stationery Request History -->
                <div>
                    <h3 class="font-medium text-sm text-zinc-600 dark:text-zinc-400 mb-2">Permintaan Stationery</h3>
                    <ul class="space-y-2 text-sm">
                        @forelse ($stationeryRequests as $request)
                            <li class="p-3 rounded-lg bg-zinc-100 dark:bg-neutral-800">
                                <span class="font-medium">{{ $request->stationary->name ?? 'Item tidak ditemukan' }}</span><br>
                                <div class="flex flex-col">
                                    <span class="text-xs text-zinc-500">Status: {{ ucfirst($request->status) }}</span>
                                    <span class="text-xs text-zinc-500">Jumlah: {{ $request->quantity }} - {{ $request->created_at->diffForHumans() }}</span>
                                    <span class="text-xs text-zinc-500">{{ $request->created_at->diffForHumans() }}</span>
                                </div>
                            </li>
                        @empty
                            <li class="text-zinc-500 dark:text-zinc-400">Belum ada permintaan stationery.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
