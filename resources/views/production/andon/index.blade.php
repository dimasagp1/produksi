@extends('layouts.app')

@section('title', 'Andon Digital')

@section('content')
    {{-- TOMBOL FULLSCREEN & JAM --}}
    <div
        class="flex justify-between items-center mb-6 bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 bg-brand-600 rounded-lg flex items-center justify-center text-white">
                <i class="fa-solid fa-tv text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-800 dark:text-white uppercase tracking-wider">Production Andon</h2>
                <p class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($today)->format('l, d F Y') }}</p>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <div class="text-right hidden md:block">
                <div class="text-2xl font-mono font-bold text-slate-800 dark:text-white" id="digital-clock">00:00:00</div>
                <div class="text-[10px] text-slate-400 uppercase font-bold tracking-widest">Waktu Sekarang</div>
            </div>
            <button onclick="toggleFullScreen()"
                class="p-3 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-xl text-slate-600 dark:text-slate-300 transition-colors"
                title="Layar Penuh">
                <i class="fa-solid fa-expand text-lg"></i>
            </button>
        </div>
    </div>

    {{-- GRID MESIN --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6">
        @foreach ($andonData as $data)
            <div
                class="relative rounded-2xl border-4 shadow-lg overflow-hidden flex flex-col min-h-[220px] transition-all {{ $data['color_class'] }} {{ isset($data['pulse']) && $data['pulse'] ? 'animate-pulse' : '' }}">

                {{-- Header Kartu --}}
                <div class="px-5 py-3 flex justify-between items-center border-b border-white/20 bg-black/10">
                    <h3 class="text-xl font-black truncate w-2/3" title="{{ $data['name'] }}">{{ $data['name'] }}</h3>
                    <span
                        class="text-xs font-bold px-2 py-1 rounded bg-black/20 backdrop-blur-sm">{{ $data['message'] }}</span>
                </div>

                {{-- Body Kartu --}}
                <div class="p-5 flex-1 flex flex-col justify-between">
                    {{-- Info Produk --}}
                    <div class="mb-4">
                        <p class="text-xs opacity-80 uppercase font-bold tracking-wider mb-1">Produk Aktif</p>
                        <p class="text-lg font-bold leading-tight line-clamp-2" title="{{ $data['product'] }}">
                            {{ $data['product'] }}</p>
                        @if ($data['status'] !== 'idle')
                            <p class="text-xs font-mono mt-1 opacity-75">{{ $data['batch'] ?? '-' }}</p>
                        @endif
                    </div>

                    {{-- Target vs Actual (Grid) --}}
                    @if ($data['status'] !== 'idle')
                        <div class="grid grid-cols-3 gap-2 text-center mb-3">
                            <div class="bg-black/20 rounded-lg p-2">
                                <span class="block text-[10px] uppercase opacity-70">Target</span>
                                <span class="block text-lg font-bold">{{ number_format($data['target']) }}</span>
                            </div>
                            <div class="bg-black/20 rounded-lg p-2">
                                <span class="block text-[10px] uppercase opacity-70">Actual</span>
                                <span class="block text-lg font-bold">{{ number_format($data['actual']) }}</span>
                            </div>
                            <div class="bg-black/20 rounded-lg p-2">
                                <span class="block text-[10px] uppercase opacity-70">Eff %</span>
                                <span class="block text-lg font-bold">{{ number_format($data['efficiency'], 1) }}%</span>
                            </div>
                        </div>
                    @else
                        <div class="flex-1 flex items-center justify-center opacity-50">
                            <i class="fa-solid fa-power-off text-6xl"></i>
                        </div>
                    @endif
                </div>

                {{-- Footer (Downtime Alert) --}}
                @if ($data['downtime'] > 0)
                    <div class="px-5 py-2 bg-black/30 flex justify-between items-center text-xs font-bold">
                        <span><i class="fa-solid fa-triangle-exclamation mr-1"></i> Total Downtime</span>
                        <span class="text-base">{{ $data['downtime'] }} Min</span>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- LEGEND (KETERANGAN WARNA) --}}
    <div class="mt-8 flex flex-wrap justify-center gap-4 md:gap-8">
        <div class="flex items-center gap-2">
            <span class="w-4 h-4 bg-emerald-600 rounded"></span>
            <span class="text-xs font-bold text-slate-600 dark:text-slate-400">Normal (>90%)</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-4 h-4 bg-amber-600 rounded"></span>
            <span class="text-xs font-bold text-slate-600 dark:text-slate-400">Warning (70-90%)</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-4 h-4 bg-rose-600 rounded"></span>
            <span class="text-xs font-bold text-slate-600 dark:text-slate-400">Critical (<70%)</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-4 h-4 bg-rose-700 rounded animate-pulse"></span>
            <span class="text-xs font-bold text-slate-600 dark:text-slate-400">High Downtime</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-4 h-4 bg-slate-800 rounded"></span>
            <span class="text-xs font-bold text-slate-600 dark:text-slate-400">Idle / Off</span>
        </div>
    </div>

    <script>
        // 1. Digital Clock
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', {
                hour12: false
            });
            document.getElementById('digital-clock').innerText = timeString;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // 2. Auto Refresh (60 Detik)
        setTimeout(function() {
            window.location.reload();
        }, 60000);

        // 3. Fullscreen Toggle
        function toggleFullScreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        }
    </script>
@endsection
