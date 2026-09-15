@extends('layouts.app')

@section('title', 'Downtime Tracking')

@section('content')
    {{-- LOAD LIBRARY CSS & JS FLATPICKR KHUSUS HALAMAN INI --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    {{-- 1. HEADER STATISTIK --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        {{-- Total Durasi --}}
        <div
            class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Total Durasi</p>
                <div class="flex items-baseline gap-1 mt-1">
                    <h3 class="text-3xl font-extrabold text-rose-600">{{ number_format($stats->total_duration ?? 0) }}</h3>
                    <span class="text-sm text-slate-400 font-medium">Menit</span>
                </div>
                <p class="text-[10px] text-slate-400 mt-1">
                    @if ($filterDate)
                        Tanggal: {{ \Carbon\Carbon::parse($filterDate)->format('d M Y') }}
                    @else
                        Semua Waktu (Keseluruhan)
                    @endif
                </p>
            </div>
            <div class="h-12 w-12 rounded-xl bg-rose-50 dark:bg-rose-900/20 flex items-center justify-center text-rose-600">
                <i class="fa-solid fa-clock-rotate-left text-xl"></i>
            </div>
        </div>

        {{-- Frekuensi --}}
        <div
            class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Frekuensi Kejadian</p>
                <div class="flex items-baseline gap-1 mt-1">
                    <h3 class="text-3xl font-extrabold text-amber-600">{{ number_format($stats->total_events ?? 0) }}</h3>
                    <span class="text-sm text-slate-400 font-medium">Kali</span>
                </div>
            </div>
            <div
                class="h-12 w-12 rounded-xl bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center text-amber-600">
                <i class="fa-solid fa-triangle-exclamation text-xl"></i>
            </div>
        </div>

        {{-- Top Machine --}}
        <div
            class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Mesin Sering Down</p>
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mt-1 truncate max-w-[150px]">
                    {{ $topMachine->name ?? '-' }}
                </h3>
                <p class="text-xs text-rose-500 font-medium mt-0.5">
                    Total: {{ number_format($topMachine->duration ?? 0) }} Menit
                </p>
            </div>
            <div
                class="h-12 w-12 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-500">
                <i class="fa-solid fa-industry text-xl"></i>
            </div>
        </div>
    </div>

    {{-- 2. TOOLBAR FILTER --}}
    <div
        class="bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 mb-6">
        <form action="{{ route('downtime-tracking.index') }}" method="GET"
            class="flex flex-col md:flex-row justify-between items-center gap-4">

            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto flex-1">
                {{-- Filter Tanggal (Opsional) --}}
                <div class="relative w-full sm:w-48 group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                        <i class="fas fa-calendar text-slate-400"></i>
                    </div>
                    {{-- Input Flatpickr --}}
                    <input type="text" name="filter_date" id="filter_date"
                        class="saas-input !pl-10 h-11 date-picker cursor-pointer bg-white" value="{{ $filterDate }}"
                        placeholder="Semua Tanggal">

                    {{-- Tombol Clear X jika ada tanggal --}}
                    @if ($filterDate)
                        <button type="button" onclick="window.location.href='{{ route('downtime-tracking.index') }}'"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-rose-500 z-20">
                            <i class="fas fa-times-circle"></i>
                        </button>
                    @endif
                </div>

                {{-- Search --}}
                <div class="relative w-full sm:max-w-xs">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-slate-400"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" class="saas-input !pl-10 h-11"
                        placeholder="Cari Mesin, Batch atau Alasan...">
                </div>

                <button type="submit"
                    class="h-11 px-5 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow-md transition-all active:scale-95">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
            </div>
        </form>
    </div>

    {{-- 3. TABEL LOG --}}
    <div
        class="bg-white dark:bg-slate-800 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                <thead class="bg-slate-50/80 dark:bg-slate-700/50">
                    <tr>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-10">
                            No</th>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Tanggal & Jam</th>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Mesin / Produk</th>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Penyebab</th>
                        <th
                            class="px-6 py-4 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Durasi</th>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse ($logs as $index => $log)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-6 py-4 text-xs text-slate-500">
                                {{ $logs->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-slate-700 dark:text-slate-300">
                                    {{ \Carbon\Carbon::parse($log->production_date)->format('d M Y') }}
                                </div>
                                <div class="text-xs text-slate-500 font-mono">
                                    Input: {{ \Carbon\Carbon::parse($log->created_at)->format('H:i') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-slate-800 dark:text-white">{{ $log->machine_name }}
                                </div>
                                <div class="text-xs text-slate-500">{{ $log->product_name }}</div>
                                <div class="flex gap-1 mt-1">
                                    <span
                                        class="text-[10px] text-slate-500 bg-slate-100 px-1.5 rounded border border-slate-200">{{ $log->shift_name ?? '-' }}</span>
                                    {{-- PERBAIKAN 2: Menampilkan Nomor Batch, bukan Report Code --}}
                                    <span
                                        class="text-[10px] text-brand-600 bg-brand-50 px-1.5 rounded border border-brand-100 font-bold">
                                        {{ $log->batch_code ?? '-' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                    {{ $log->downtime_reason }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-bold bg-rose-50 text-rose-600 border border-rose-100">
                                    {{ $log->duration }} m
                                </span>
                            </td>
                            {{-- PERBAIKAN 1: Teks Keterangan tidak terpotong (Wrap) --}}
                            <td class="px-6 py-4 text-sm text-slate-500 italic min-w-[250px]">
                                <div class="whitespace-normal break-words leading-relaxed">
                                    {{ $log->remarks ?? '-' }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center">
                                    <div class="h-12 w-12 bg-slate-50 rounded-full flex items-center justify-center mb-3">
                                        <i class="fas fa-list text-slate-300 text-xl"></i>
                                    </div>
                                    <p class="mb-1">Belum ada data downtime.</p>
                                    @if ($filterDate)
                                        <p class="text-xs text-slate-400">Tidak ada data pada tanggal
                                            <b>{{ \Carbon\Carbon::parse($filterDate)->format('d M Y') }}</b>.</p>
                                    @else
                                        <p class="text-xs text-slate-400">Belum ada data produksi yang tercatat memiliki
                                            downtime.</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/30">
            {{ $logs->links() }}
        </div>
    </div>

    <script>
        // Gunakan window load untuk memastikan library sudah siap
        window.addEventListener('load', function() {
            if (typeof flatpickr !== 'undefined') {
                flatpickr(".date-picker", {
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "d M Y",
                    allowInput: true,
                    // Tambahkan baris ini untuk membatasi maksimal tanggal hari ini
                    maxDate: "today",
                    // Jika filterDate ada, set tanggalnya. Jika null, biarkan kosong.
                    defaultDate: "{{ $filterDate ?? '' }}"
                });
            } else {
                console.error("Flatpickr library not loaded.");
            }
        });
    </script>
@endsection
