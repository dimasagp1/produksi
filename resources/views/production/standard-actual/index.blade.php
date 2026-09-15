@extends('layouts.app')

@section('title', 'Analisis Standar vs Aktual')

@section('content')
    {{-- LOAD LIBRARY FLATPICKR --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    {{-- CSS CUSTOM --}}
    <style>
        .saas-input {
            height: 44px;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 0 16px;
            font-size: 0.875rem;
            width: 100%;
            background-color: #f8fafc;
            color: #1e293b;
            transition: all 0.2s;
        }

        .dark .saas-input {
            background-color: #1e293b;
            border-color: #334155;
            color: #f1f5f9;
        }

        .saas-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .saas-input.\!pl-10 {
            padding-left: 2.5rem !important;
        }
    </style>

    {{-- HEADER STATS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        {{-- Total Data --}}
        <div
            class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Total Laporan</p>
                <h3 class="text-3xl font-extrabold text-slate-800 dark:text-white mt-1">{{ number_format($totalRecords) }}
                </h3>
                <p class="text-[10px] text-slate-400 mt-1">Sesuai filter tanggal</p>
            </div>
            <div class="h-12 w-12 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-500">
                <i class="fa-solid fa-file-invoice text-xl"></i>
            </div>
        </div>

        {{-- Masalah Cycle Time --}}
        <div
            class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Cycle Time Melambat</p>
                <h3 class="text-3xl font-extrabold text-amber-600 mt-1">{{ number_format($ctProblemCount) }}</h3>
                <p class="text-[10px] text-slate-400 mt-1">Aktual > Standar</p>
            </div>
            <div
                class="h-12 w-12 rounded-xl bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center text-amber-600">
                <i class="fa-solid fa-stopwatch text-xl"></i>
            </div>
        </div>

        {{-- Masalah Cavity --}}
        <div
            class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Cavity Drop</p>
                <h3 class="text-3xl font-extrabold text-rose-600 mt-1">{{ number_format($cavityProblemCount) }}</h3>
                <p class="text-[10px] text-slate-400 mt-1">Aktual < Standar</p>
            </div>
            <div class="h-12 w-12 rounded-xl bg-rose-50 dark:bg-rose-900/20 flex items-center justify-center text-rose-600">
                <i class="fa-solid fa-shapes text-xl"></i>
            </div>
        </div>
    </div>

    {{-- TOOLBAR FILTER --}}
    <div
        class="bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 mb-6">
        <form action="{{ route('standard-actual.index') }}" method="GET" id="filterForm"
            class="flex flex-col md:flex-row justify-between items-center gap-4">

            <div class="flex items-center gap-2 md:w-1/3">
                <div
                    class="h-10 w-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600">
                    <i class="fa-solid fa-scale-balanced text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 dark:text-white">Analisis Varian</h3>
                    <p class="text-xs text-slate-500">Bandingkan performa mesin.</p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto items-end sm:items-center flex-1 justify-end">
                {{-- Range Tanggal --}}
                <div class="flex gap-2 w-full sm:w-auto">
                    <div class="relative flex-1 sm:w-36 group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                            <i class="fa-solid fa-calendar text-slate-400"></i>
                        </div>
                        <input type="text" name="start_date" id="start_date"
                            class="saas-input h-10 !pl-10 date-picker cursor-pointer bg-white" value="{{ $startDate }}"
                            placeholder="Tgl Mulai">
                    </div>
                    <span class="text-slate-400 self-center">-</span>
                    <div class="relative flex-1 sm:w-36 group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                            <i class="fa-solid fa-calendar text-slate-400"></i>
                        </div>
                        <input type="text" name="end_date" id="end_date"
                            class="saas-input h-10 !pl-10 date-picker cursor-pointer bg-white" value="{{ $endDate }}"
                            placeholder="Tgl Selesai">
                    </div>
                </div>

                {{-- Search --}}
                <div class="relative w-full sm:max-w-xs">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-slate-400"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" class="saas-input !pl-10 h-10"
                        placeholder="Cari Batch / Produk...">
                </div>

                <button type="submit"
                    class="h-10 px-4 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow-md transition-all">
                    <i class="fa-solid fa-filter"></i>
                </button>

                @if ($startDate || $endDate || request('search'))
                    <a href="{{ route('standard-actual.index') }}"
                        class="h-10 px-3 flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-500 rounded-xl transition-colors"
                        title="Reset">
                        <i class="fa-solid fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- TABEL DATA --}}
    <div
        class="bg-white dark:bg-slate-800 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                <thead class="bg-slate-50/80 dark:bg-slate-700/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider w-10">No
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal &
                            Mesin</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Batch /
                            Produk</th>

                        {{-- Group Cycle Time --}}
                        <th
                            class="px-6 py-4 text-center bg-amber-50/50 dark:bg-amber-900/10 border-l border-r border-slate-200 dark:border-slate-700">
                            <div class="text-xs font-bold text-amber-700 dark:text-amber-400 uppercase">Cycle Time (Detik)
                            </div>
                            <div class="flex justify-center gap-8 mt-1 text-[10px] text-slate-400">
                                <span>Standar (Master)</span>
                                <span>Aktual</span>
                            </div>
                        </th>

                        {{-- Group Cavity --}}
                        <th
                            class="px-6 py-4 text-center bg-blue-50/50 dark:bg-blue-900/10 border-r border-slate-200 dark:border-slate-700">
                            <div class="text-xs font-bold text-blue-700 dark:text-blue-400 uppercase">Cavity (Pcs)</div>
                            <div class="flex justify-center gap-8 mt-1 text-[10px] text-slate-400">
                                <span>Standar (Master)</span>
                                <span>Aktual</span>
                            </div>
                        </th>

                        <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Status
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse ($reports as $index => $report)
                        @php
                            // PERBAIKAN: STRICT MENGGUNAKAN DATA MASTER (tanpa fallback ke $report->cycle_time)
                            $ctStd = $report->master_cycle_time;
                            $cavStd = $report->master_cavity;

                            $ctAct = $report->actual_cycle_time;
                            $cavAct = $report->actual_cavity;

                            // Logika Warna CT (Merah jika Aktual > Standar & Standar > 0)
                            $ctClass = '';
                            $ctIcon = '';

                            if ($ctStd > 0 && $ctAct > $ctStd) {
                                $ctClass = 'text-rose-600 font-bold';
                                $ctIcon = '<i class="fa-solid fa-arrow-trend-up text-rose-500 text-[10px] ml-1"></i>';
                            } elseif ($ctStd > 0 && $ctAct > 0 && $ctAct < $ctStd) {
                                $ctClass = 'text-emerald-600 font-bold';
                                $ctIcon =
                                    '<i class="fa-solid fa-arrow-trend-down text-emerald-500 text-[10px] ml-1"></i>';
                            } elseif ($ctStd == 0) {
                                // Jika master 0, beri indikasi
                                $ctClass = 'text-slate-400 italic';
                            }

                            // Logika Warna Cavity
                            $cavClass = '';
                            $cavIcon = '';

                            if ($cavStd > 0 && $cavAct < $cavStd) {
                                $cavClass = 'text-rose-600 font-bold';
                                $cavIcon =
                                    '<i class="fa-solid fa-circle-exclamation text-rose-500 text-[10px] ml-1"></i>';
                            } elseif ($cavStd > 0 && $cavAct == $cavStd) {
                                $cavClass = 'text-emerald-600 font-bold';
                                $cavIcon = '<i class="fa-solid fa-check text-emerald-500 text-[10px] ml-1"></i>';
                            } elseif ($cavStd == 0) {
                                $cavClass = 'text-slate-400 italic';
                            }
                        @endphp

                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-6 py-4 text-xs text-slate-500">
                                {{ $reports->firstItem() + $index }}
                            </td>

                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-slate-700 dark:text-slate-300">
                                    {{ \Carbon\Carbon::parse($report->production_date)->format('d M Y') }}
                                </div>
                                <div class="text-xs text-slate-500">
                                    <i class="fa-solid fa-gears mr-1"></i> {{ $report->machine->name ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-slate-800 dark:text-white">
                                    {{ $report->product->name ?? '-' }}</div>
                                <div class="text-xs text-brand-600 font-mono">{{ $report->batch->batch_code ?? '-' }}</div>
                            </td>

                            {{-- Kolom CT --}}
                            <td class="px-6 py-4 text-center border-l border-r border-slate-100 dark:border-slate-700">
                                <div class="flex justify-center items-center gap-6">
                                    <span class="text-sm {{ $ctStd == 0 ? 'text-rose-400 italic' : 'text-slate-500' }}">
                                        {{ $ctStd > 0 ? number_format($ctStd, 1) : '0' }}
                                    </span>
                                    <span class="text-sm {{ $ctClass }}">
                                        {{ number_format($ctAct, 1) }} {!! $ctIcon !!}
                                    </span>
                                </div>
                            </td>

                            {{-- Kolom Cavity --}}
                            <td class="px-6 py-4 text-center border-r border-slate-100 dark:border-slate-700">
                                <div class="flex justify-center items-center gap-6">
                                    <span class="text-sm {{ $cavStd == 0 ? 'text-rose-400 italic' : 'text-slate-500' }}">
                                        {{ $cavStd > 0 ? $cavStd : '0' }}
                                    </span>
                                    <span class="text-sm {{ $cavClass }}">
                                        {{ $cavAct }} {!! $cavIcon !!}
                                    </span>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-center">
                                @if (($ctStd > 0 && $ctAct > $ctStd) || ($cavStd > 0 && $cavAct < $cavStd))
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800">
                                        Inefisien
                                    </span>
                                @elseif($ctStd == 0 || $cavStd == 0)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-500"
                                        title="Master Data Kosong">
                                        No Std
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                        Optimal
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                Tidak ada data laporan pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700">
            {{ $reports->links() }}
        </div>
    </div>

    <script>
        window.addEventListener('load', function() {
            if (typeof flatpickr !== 'undefined') {
                const startPicker = flatpickr("#start_date", {
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "d M Y",
                    allowInput: true,
                    defaultDate: "{{ $startDate ?? '' }}",
                    onChange: function(selectedDates, dateStr, instance) {
                        endPicker.set('minDate', dateStr);
                    }
                });

                const endPicker = flatpickr("#end_date", {
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "d M Y",
                    allowInput: true,
                    defaultDate: "{{ $endDate ?? '' }}"
                });

                if (document.getElementById('start_date').value) {
                    endPicker.set('minDate', document.getElementById('start_date').value);
                }
            }
        });
    </script>
@endsection
