@extends('layouts.app')

@section('title', 'OEE Dashboard')

@section('content')
    {{-- LOAD LIBRARY FLATPICKR --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    {{-- TAMBAHAN CSS AGAR INPUT TERLIHAT RAPI --}}
    <style>
        /* Base Input Styles */
        .saas-input {
            height: 44px;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 0 16px;
            font-size: 0.875rem;
            width: 100%;
            background-color: #f8fafc;
            color: #1e293b;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .dark .saas-input {
            background-color: #1e293b;
            border-color: #334155;
            color: #f1f5f9;
        }

        .saas-input:focus {
            border-color: #3b82f6;
            outline: none;
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .dark .saas-input:focus {
            background-color: #0f172a;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
        }

        .saas-input.\!pl-10 {
            padding-left: 2.5rem !important;
        }
    </style>

    {{-- TOOLBAR FILTER --}}
    <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 mb-8">
        <form action="{{ route('oee-dashboard.index') }}" method="GET" id="filterForm"
            class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-2">
                <div
                    class="h-10 w-10 rounded-full bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center text-indigo-600">
                    <i class="fa-solid fa-chart-pie text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 dark:text-white">Overall Equipment Effectiveness</h3>
                    <p class="text-xs text-slate-500">
                        @if ($startDate && $endDate)
                            Periode: <span
                                class="font-bold text-indigo-500">{{ \Carbon\Carbon::parse($startDate)->format('d M') }} -
                                {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</span>
                        @else
                            Periode: <span class="font-bold text-emerald-500">Semua Waktu (All Time)</span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto items-end sm:items-center">
                {{-- Input Rentang Tanggal --}}
                <div class="flex gap-2 w-full sm:w-auto">
                    <div class="relative flex-1 sm:w-40 group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                            <i class="fa-solid fa-calendar text-slate-400"></i>
                        </div>
                        <input type="text" name="start_date" id="start_date"
                            class="saas-input h-10 !pl-10 date-picker cursor-pointer bg-white" value="{{ $startDate }}"
                            placeholder="Tgl Mulai">
                    </div>
                    <span class="text-slate-400 self-center">-</span>
                    <div class="relative flex-1 sm:w-40 group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                            <i class="fa-solid fa-calendar text-slate-400"></i>
                        </div>
                        <input type="text" name="end_date" id="end_date"
                            class="saas-input h-10 !pl-10 date-picker cursor-pointer bg-white" value="{{ $endDate }}"
                            placeholder="Tgl Selesai">
                    </div>
                </div>

                {{-- Tombol Filter --}}
                <button type="submit"
                    class="h-10 px-4 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow-md transition-all flex items-center gap-2">
                    <i class="fa-solid fa-filter"></i> <span>Filter</span>
                </button>

                {{-- Tombol Reset (Muncul jika ada filter) --}}
                @if ($startDate || $endDate)
                    <a href="{{ route('oee-dashboard.index') }}"
                        class="h-10 px-3 flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-500 rounded-xl transition-colors"
                        title="Reset Filter">
                        <i class="fa-solid fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- BIG OEE STATS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- OEE Score --}}
        <div
            class="bg-gradient-to-br from-indigo-600 to-blue-700 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden group">
            <div
                class="absolute right-0 top-0 opacity-10 transform translate-x-2 -translate-y-2 transition-transform group-hover:scale-110">
                <i class="fa-solid fa-trophy text-8xl"></i>
            </div>
            <p class="text-indigo-100 text-xs font-bold uppercase tracking-wider mb-1">OEE Score</p>
            <div class="flex items-end gap-2">
                <h2 class="text-4xl font-extrabold">{{ number_format($oeeScore, 1) }}%</h2>
            </div>
            <div class="mt-4 h-2 w-full bg-black/20 rounded-full overflow-hidden">
                <div class="h-full bg-white/90 rounded-full transition-all duration-1000"
                    style="width: {{ $oeeScore }}%"></div>
            </div>
            <p class="text-[10px] mt-2 text-indigo-100 opacity-80">World Class Target: 85%</p>
        </div>

        {{-- Availability --}}
        <div
            class="bg-white dark:bg-slate-800 p-5 rounded-2xl border-l-4 border-emerald-500 shadow-subtle hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-xs text-slate-400 uppercase font-bold">Availability</p>
                    <h3 class="text-2xl font-bold text-slate-800 dark:text-white mt-1">
                        {{ number_format($availability, 1) }}%</h3>
                </div>
                <div class="p-2 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 rounded-lg">
                    <i class="fa-solid fa-clock"></i>
                </div>
            </div>
            <p class="text-xs text-slate-500">Rasio Waktu Operasi vs Rencana</p>
        </div>

        {{-- Performance --}}
        <div
            class="bg-white dark:bg-slate-800 p-5 rounded-2xl border-l-4 border-amber-500 shadow-subtle hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-xs text-slate-400 uppercase font-bold">Performance</p>
                    <h3 class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ number_format($performance, 1) }}%
                    </h3>
                </div>
                <div class="p-2 bg-amber-50 dark:bg-amber-900/20 text-amber-600 rounded-lg">
                    <i class="fa-solid fa-gauge-high"></i>
                </div>
            </div>
            <p class="text-xs text-slate-500">Rasio Kecepatan vs Cycle Time Std</p>
        </div>

        {{-- Quality --}}
        <div
            class="bg-white dark:bg-slate-800 p-5 rounded-2xl border-l-4 border-rose-500 shadow-subtle hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-xs text-slate-400 uppercase font-bold">Quality</p>
                    <h3 class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ number_format($quality, 1) }}%
                    </h3>
                </div>
                <div class="p-2 bg-rose-50 dark:bg-rose-900/20 text-rose-600 rounded-lg">
                    <i class="fa-solid fa-check-double"></i>
                </div>
            </div>
            <p class="text-xs text-slate-500">Rasio Produk OK vs Total Output</p>
        </div>
    </div>

    {{-- MACHINE BREAKDOWN --}}
    <div
        class="bg-white dark:bg-slate-800 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
            <h4 class="font-bold text-slate-800 dark:text-white">Detail Per Mesin</h4>
            <span class="text-xs text-slate-500">Diurutkan berdasarkan OEE tertinggi</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                <thead class="bg-slate-50/80 dark:bg-slate-700/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Mesin</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-emerald-600 uppercase">Availability</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-amber-600 uppercase">Performance</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-rose-600 uppercase">Quality</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-indigo-600 uppercase">OEE</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase">Total Output</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse ($machineStats as $m)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800 dark:text-white">{{ $m->name }}</div>
                                <div class="text-xs text-slate-400">Downtime: {{ number_format($m->downtime) }} min</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col items-center">
                                    <span
                                        class="text-sm font-bold text-emerald-600">{{ number_format($m->availability, 1) }}%</span>
                                    <div class="w-16 h-1 bg-emerald-100 rounded-full mt-1 overflow-hidden">
                                        <div class="h-full bg-emerald-500" style="width: {{ $m->availability }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col items-center">
                                    <span
                                        class="text-sm font-bold text-amber-600">{{ number_format($m->performance, 1) }}%</span>
                                    <div class="w-16 h-1 bg-amber-100 rounded-full mt-1 overflow-hidden">
                                        <div class="h-full bg-amber-500" style="width: {{ $m->performance }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col items-center">
                                    <span
                                        class="text-sm font-bold text-rose-600">{{ number_format($m->quality, 1) }}%</span>
                                    <div class="w-16 h-1 bg-rose-100 rounded-full mt-1 overflow-hidden">
                                        <div class="h-full bg-rose-500" style="width: {{ $m->quality }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="inline-flex px-3 py-1 rounded-lg text-sm font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    {{ number_format($m->oee, 1) }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-mono text-sm text-slate-600 dark:text-slate-300">
                                {{ number_format($m->total_output) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                Tidak ada data produksi pada rentang ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- SCRIPT: FLATPICKR OTOMATIS --}}
    <script>
        window.addEventListener('load', function() {
            if (typeof flatpickr !== 'undefined') {
                // Konfigurasi Tanggal Selesai (End Date)
                const endPicker = flatpickr("#end_date", {
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "d M Y",
                    allowInput: true,
                    maxDate: "today",
                    defaultDate: "{{ $endDate ?? '' }}"
                });

                // Konfigurasi Tanggal Mulai (Start Date)
                const startPicker = flatpickr("#start_date", {
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "d M Y",
                    allowInput: true,
                    maxDate: "today",
                    defaultDate: "{{ $startDate ?? '' }}",
                    // Saat tanggal mulai dipilih/berubah
                    onChange: function(selectedDates, dateStr, instance) {
                        // Set batas minimum tanggal selesai agar tidak bisa < tanggal mulai
                        endPicker.set('minDate', dateStr);

                        // Opsional: Jika tanggal selesai sebelumnya lebih kecil, kosongkan
                        const currentEndDate = endPicker.selectedDates[0];
                        if (currentEndDate && selectedDates[0] && currentEndDate < selectedDates[0]) {
                            endPicker.clear();
                        }
                    }
                });

                // Inisialisasi awal: jika sudah ada tanggal mulai, update minDate endPicker
                if (document.getElementById('start_date').value) {
                    endPicker.set('minDate', document.getElementById('start_date').value);
                }

            } else {
                console.error("Flatpickr library not loaded.");
            }
        });
    </script>
@endsection
