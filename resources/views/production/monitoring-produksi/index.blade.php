@extends('layouts.app')

@section('title', 'Monitoring Produksi Real-time')

@section('content')
    {{-- Header Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div
            class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500 uppercase font-bold">Total Mesin</p>
                <h3 class="text-3xl font-extrabold text-slate-800 dark:text-white">{{ $summary['total_mesin'] }}</h3>
            </div>
            <div
                class="h-10 w-10 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-500">
                <i class="fa-solid fa-gears"></i>
            </div>
        </div>
        <div
            class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs text-emerald-600 uppercase font-bold">Sedang Berjalan (Running)</p>
                <h3 class="text-3xl font-extrabold text-emerald-600">{{ $summary['running'] }}</h3>
            </div>
            <div
                class="h-10 w-10 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600">
                <i class="fa-solid fa-bolt"></i>
            </div>
        </div>
        <div
            class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-400 uppercase font-bold">Idle / Berhenti</p>
                <h3 class="text-3xl font-extrabold text-slate-500">{{ $summary['idle'] }}</h3>
            </div>
            <div
                class="h-10 w-10 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-400">
                <i class="fa-solid fa-power-off"></i>
            </div>
        </div>
    </div>

    {{-- Machine Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach ($monitoringData as $m)
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl border {{ $m['status'] === 'running' ? 'border-emerald-500 ring-1 ring-emerald-500' : 'border-slate-200 dark:border-slate-700' }} shadow-sm overflow-hidden hover:shadow-md transition-shadow relative">

                {{-- Status Indicator --}}
                <div class="absolute top-4 right-4">
                    @if ($m['status'] === 'running')
                        <span class="flex h-3 w-3">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                        </span>
                    @else
                        <span class="inline-flex rounded-full h-3 w-3 bg-slate-300"></span>
                    @endif
                </div>

                <div class="p-5">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="h-10 w-10 rounded-lg {{ $m['status'] === 'running' ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center">
                            <i class="fa-solid fa-industry text-lg"></i>
                        </div>
                        <h4 class="font-bold text-lg text-slate-800 dark:text-white">{{ $m['machine_name'] }}</h4>
                    </div>

                    @if ($m['status'] === 'running' && $m['data'])
                        <div class="space-y-3">
                            <div class="bg-slate-50 dark:bg-slate-700/50 p-3 rounded-lg">
                                <p class="text-[10px] text-slate-400 uppercase font-bold">Produk</p>
                                <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 truncate">
                                    {{ $m['data']['product'] }}</p>
                                <p class="text-xs text-brand-600">{{ $m['data']['batch'] }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-2 text-center">
                                <div class="p-2 border border-slate-100 dark:border-slate-700 rounded-lg">
                                    <p class="text-[10px] text-slate-400 uppercase">Output</p>
                                    <p class="font-bold text-emerald-600">{{ number_format($m['data']['output']) }}</p>
                                </div>
                                <div class="p-2 border border-slate-100 dark:border-slate-700 rounded-lg">
                                    <p class="text-[10px] text-slate-400 uppercase">Reject</p>
                                    <p class="font-bold text-rose-600">{{ number_format($m['data']['reject']) }}</p>
                                </div>
                            </div>

                            <div class="flex justify-between items-center text-xs text-slate-400 mt-2">
                                <span>Eff: <b
                                        class="text-slate-600 dark:text-slate-300">{{ number_format($m['data']['efficiency'], 1) }}%</b></span>
                                <span><i class="fa-regular fa-clock mr-1"></i> {{ $m['data']['last_update'] }}</span>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700">
                            <span
                                class="inline-block w-full text-center text-xs font-bold text-emerald-600 bg-emerald-50 py-1 rounded">PRODUKSI
                                BERJALAN</span>
                        </div>
                    @else
                        <div class="h-32 flex flex-col items-center justify-center text-slate-400">
                            <i class="fa-solid fa-ban text-3xl mb-2 opacity-50"></i>
                            <p class="text-sm">Mesin Idle / Berhenti</p>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6 text-center">
        <p class="text-xs text-slate-400">Halaman ini diperbarui secara otomatis setiap 60 detik.</p>
    </div>

    <script>
        // Auto refresh setiap 60 detik
        setTimeout(function() {
            window.location.reload(1);
        }, 60000);
    </script>
@endsection
