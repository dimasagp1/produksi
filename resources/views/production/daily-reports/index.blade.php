@extends('layouts.app')

@section('title', 'Laporan Harian')

@section('content')
    {{-- 1. LIBRARY PENDUKUNG --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    {{-- Flatpickr --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://npmcdn.com/flatpickr/dist/themes/dark.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    {{-- Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    {{-- 2. CUSTOM CSS --}}
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

        /* Padding khusus untuk icon */
        .saas-input.\!pl-10 {
            padding-left: 2.5rem !important;
        }

        /* Select2 Customization */
        .select2-container .select2-selection--single {
            height: 44px !important;
            border-radius: 0.75rem !important;
            display: flex !important;
            align-items: center !important;
            border: 1px solid #e2e8f0 !important;
            background-color: #f8fafc !important;
        }

        .dark .select2-container .select2-selection--single {
            background-color: #1e293b !important;
            border-color: #334155 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #1e293b !important;
            font-size: 0.875rem;
            padding-left: 16px !important;
        }

        .dark .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #f1f5f9 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 42px !important;
            right: 10px !important;
        }

        /* Table Styles */
        .table-row-hover:hover td {
            background-color: #f8fafc;
        }

        .dark .table-row-hover:hover td {
            background-color: rgba(30, 41, 59, 0.5);
        }

        /* Summary Card Hover */
        .summary-card-link {
            transition: all 0.3s ease;
        }

        .summary-card-link:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
    </style>

    <div class="w-full font-sans text-slate-600 dark:text-slate-300">
        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white tracking-tight">Laporan Harian</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Monitoring kinerja produksi harian.</p>
            </div>
            @if (in_array(auth()->user()->role, ['admin', 'super_admin', 'spv', 'leader', 'operator']))
                <div>
                    {{-- UBAH KE LINK (route daily-reports.create) --}}
                    <a href="{{ route('daily-reports.create') }}"
                        class="inline-flex items-center px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-brand-500/30 transition-all transform hover:-translate-y-0.5 active:scale-95">
                        <i class="fas fa-plus mr-2"></i> Tambah Laporan
                    </a>
                </div>
            @endif
        </div>

        {{-- SUMMARY CARDS (6 GRID) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            {{-- 1. TOTAL LAPORAN (Keseluruhan) --}}
            <a href="{{ route('daily-reports.index') }}"
                class="summary-card-link p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 flex items-center justify-between group hover:border-brand-300 cursor-pointer {{ !request('filter_status') && !request('start_date') ? 'ring-2 ring-slate-400 bg-slate-50 dark:bg-slate-700' : 'bg-white dark:bg-slate-800' }}">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Total Laporan (Semua)</p>
                    <h3 class="text-3xl font-extrabold text-slate-800 dark:text-white mt-1">{{ $statTotalReports ?? 0 }}
                    </h3>
                </div>
                <div
                    class="h-12 w-12 rounded-xl bg-slate-100 dark:bg-slate-700/50 flex items-center justify-center text-slate-600 dark:text-slate-400 group-hover:scale-110 transition-transform">
                    <i class="fas fa-folder-open text-xl"></i>
                </div>
            </a>

            {{-- 2. LAPORAN HARI INI --}}
            <a href="{{ route('daily-reports.index', ['start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d')]) }}"
                class="summary-card-link p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 flex items-center justify-between group hover:border-brand-300 cursor-pointer {{ request('start_date') == date('Y-m-d') ? 'ring-2 ring-brand-500 bg-brand-50 dark:bg-brand-900/20' : 'bg-white dark:bg-slate-800' }}">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Laporan Hari Ini</p>
                    <h3 class="text-3xl font-extrabold text-brand-600 dark:text-brand-400 mt-1">{{ $statTodayReports ?? 0 }}
                    </h3>
                </div>
                <div
                    class="h-12 w-12 rounded-xl bg-brand-50 dark:bg-brand-900/20 flex items-center justify-center text-brand-600 dark:text-brand-400 group-hover:scale-110 transition-transform">
                    <i class="fas fa-calendar-day text-xl"></i>
                </div>
            </a>

            {{-- 3. TOTAL DOWNTIME --}}
            <a href="{{ route('daily-reports.index') }}"
                class="summary-card-link bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 flex items-center justify-between group hover:border-amber-300 cursor-pointer">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Total Downtime (Menit)</p>
                    <h3 class="text-3xl font-extrabold text-amber-600 dark:text-amber-400 mt-1">
                        {{ number_format($statTotalDowntime ?? 0, 0, ',', '.') }}</h3>
                </div>
                <div
                    class="h-12 w-12 rounded-xl bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center text-amber-600 dark:text-amber-400 group-hover:scale-110 transition-transform">
                    <i class="fas fa-stopwatch text-xl"></i>
                </div>
            </a>

            {{-- 4. TOTAL REJECT --}}
            <a href="{{ route('daily-reports.index') }}"
                class="summary-card-link bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 flex items-center justify-between group hover:border-rose-300 cursor-pointer">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Total Reject (Pcs)</p>
                    <h3 class="text-3xl font-extrabold text-rose-600 dark:text-rose-400 mt-1">
                        {{ number_format($statTotalReject ?? 0, 0, ',', '.') }}</h3>
                </div>
                <div
                    class="h-12 w-12 rounded-xl bg-rose-50 dark:bg-rose-900/20 flex items-center justify-center text-rose-600 dark:text-rose-400 group-hover:scale-110 transition-transform">
                    <i class="fas fa-ban text-xl"></i>
                </div>
            </a>

            {{-- 5. TOTAL LAPORAN SELESAI --}}
            <a href="{{ route('daily-reports.index', ['filter_status' => 'verified']) }}"
                class="summary-card-link p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 flex items-center justify-between group hover:border-emerald-300 cursor-pointer {{ request('filter_status') == 'verified' ? 'ring-2 ring-emerald-500 bg-emerald-50 dark:bg-emerald-900/20' : 'bg-white dark:bg-slate-800' }}">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Laporan Selesai</p>
                    <h3 class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">
                        {{ number_format($statTotalVerified ?? 0, 0, ',', '.') }}</h3>
                </div>
                <div
                    class="h-12 w-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
            </a>

            {{-- 6. LAPORAN AKTIF --}}
            <a href="{{ route('daily-reports.index', ['filter_status' => 'active']) }}"
                class="summary-card-link p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 flex items-center justify-between group hover:border-indigo-300 cursor-pointer {{ request('filter_status') == 'active' ? 'ring-2 ring-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'bg-white dark:bg-slate-800' }}">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Laporan Aktif</p>
                    <h3 class="text-3xl font-extrabold text-indigo-600 dark:text-indigo-400 mt-1">
                        {{ number_format($statTotalActive ?? 0, 0, ',', '.') }}</h3>
                </div>
                <div
                    class="h-12 w-12 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400 group-hover:scale-110 transition-transform">
                    <i class="fas fa-pen-to-square text-xl"></i>
                </div>
            </a>
        </div>

        {{-- TOOLBAR (FILTER) --}}
        <div
            class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 mb-6">
            <form action="{{ route('daily-reports.index') }}" method="GET" class="contents w-full" id="filterForm">
                @if (request('filter_status'))
                    <input type="hidden" name="filter_status" value="{{ request('filter_status') }}">
                @endif

                <div class="flex flex-col xl:flex-row justify-between gap-5 items-end">

                    {{-- GRID INPUT FILTER --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4 w-full xl:w-auto flex-grow items-end">

                        {{-- 1. Pencarian --}}
                        <div class="relative sm:col-span-2 lg:col-span-4 group">
                            <label
                                class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wide">Pencarian</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-slate-400"></i>
                                </div>
                                <input type="text" name="search" value="{{ request('search') }}"
                                    class="saas-input !pl-10" placeholder="Batch, Produk, atau Mesin...">
                            </div>
                        </div>

                        {{-- 2. Tanggal Mulai --}}
                        <div class="relative lg:col-span-2 group">
                            <label
                                class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wide">Tanggal
                                Awal</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                    <i class="fas fa-calendar text-slate-400"></i>
                                </div>
                                <input type="text" name="start_date" id="startDate" class="saas-input !pl-10 date-filter"
                                    value="{{ request('start_date') }}" placeholder="Pilih Tanggal...">
                            </div>
                        </div>

                        {{-- 3. Tanggal Selesai --}}
                        <div class="relative lg:col-span-2 group">
                            <label
                                class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wide">Tanggal
                                Akhir</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                    <i class="fas fa-calendar-check text-slate-400"></i>
                                </div>
                                <input type="text" name="end_date" id="endDate" class="saas-input !pl-10 date-filter"
                                    value="{{ request('end_date') }}" placeholder="Pilih Tanggal...">
                            </div>
                        </div>

                        {{-- 4. Shift --}}
                        <div class="relative lg:col-span-2">
                            <label
                                class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wide">Shift</label>
                            <select name="filter_shift" id="filter_shift" class="saas-input cursor-pointer"
                                data-placeholder="Semua Shift">
                                <option value=""></option>
                                @foreach ($shifts as $s)
                                    <option value="{{ $s->id }}"
                                        {{ request('filter_shift') == $s->id ? 'selected' : '' }}>{{ $s->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 5. Tombol Filter & Reset --}}
                        <div class="flex items-center gap-2 lg:col-span-2">
                            <button type="submit"
                                class="w-full h-[44px] bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow-md transition-all active:scale-95 flex items-center justify-center">
                                <i class="fas fa-filter mr-2"></i> Filter
                            </button>

                            @if (request()->anyFilled(['search', 'start_date', 'end_date', 'filter_shift', 'filter_status']))
                                <a href="{{ route('daily-reports.index') }}"
                                    class="h-[44px] px-3 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 font-bold rounded-xl shadow-sm transition-all active:scale-95 flex items-center justify-center"
                                    title="Reset Filter">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- TOMBOL IMPORT/EXPORT --}}
                    <div class="flex items-center gap-3 w-full xl:w-auto justify-end mt-4 xl:mt-0">
                        @if (in_array(auth()->user()->role, ['admin', 'super_admin']))
                            <button type="button" onclick="window.openModal('importModal')"
                                class="inline-flex items-center justify-center h-[44px] px-4 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 rounded-xl text-sm font-medium hover:bg-slate-50 transition-all">
                                <i class="fas fa-file-import mr-2 text-slate-400"></i> Import
                            </button>
                        @endif
                        <div class="relative">
                            <button type="button" onclick="window.toggleExportMenu(event)"
                                class="inline-flex items-center justify-center h-[44px] px-4 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 rounded-xl text-sm font-medium hover:bg-slate-50 transition-all shadow-sm">
                                <i class="fas fa-download mr-2 text-slate-400"></i> Export <i
                                    class="fas fa-chevron-down ml-2 text-xs text-slate-400"></i>
                            </button>
                            <div id="exportMenu"
                                class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-slate-800 rounded-xl shadow-xl ring-1 ring-black ring-opacity-5 border border-slate-100 dark:border-slate-700 z-50">
                                <div class="py-1">
                                    <a href="{{ route('daily-reports.export-excel', request()->all()) }}" target="_blank"
                                        class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-emerald-600"><i
                                            class="fas fa-file-excel mr-2 text-emerald-500"></i>Excel</a>
                                    <a href="{{ route('daily-reports.export-pdf', request()->all()) }}" target="_blank"
                                        class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-rose-600"><i
                                            class="fas fa-file-pdf mr-2 text-rose-500"></i>PDF</a>
                                    @if (in_array(auth()->user()->role, ['admin', 'super_admin']))
                                        <a href="{{ route('daily-reports.template') }}" target="_blank"
                                            class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-blue-600 border-t border-slate-100"><i
                                                class="fas fa-file-csv mr-2 text-blue-500"></i>Template Import</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- TABLE DATA --}}
        <div
            class="bg-white dark:bg-slate-800 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                    <thead class="bg-slate-50/80 dark:bg-slate-700/50">
                        <tr>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-12">
                                No</th>
                            <th
                                class="px-6 py-4 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Batch Info</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Waktu & Ops</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Produk & Mesin</th>

                            {{-- Group Cycle Time --}}
                            <th
                                class="px-6 py-4 text-center bg-amber-50/50 dark:bg-amber-900/10 border-l border-r border-slate-200 dark:border-slate-700">
                                <div class="text-xs font-bold text-amber-700 dark:text-amber-400 uppercase">Cycle Time
                                    (Detik)
                                </div>
                                <div class="flex justify-center gap-8 mt-1 text-[10px] text-slate-400">
                                    <span>Standar (Master)</span>
                                    <span>Aktual</span>
                                </div>
                            </th>

                            {{-- Group Cavity --}}
                            <th
                                class="px-6 py-4 text-center bg-blue-50/50 dark:bg-blue-900/10 border-r border-slate-200 dark:border-slate-700">
                                <div class="text-xs font-bold text-blue-700 dark:text-blue-400 uppercase">Cavity (Pcs)
                                </div>
                                <div class="flex justify-center gap-8 mt-1 text-[10px] text-slate-400">
                                    <span>Standar (Master)</span>
                                    <span>Aktual</span>
                                </div>
                            </th>

                            <th
                                class="px-6 py-4 text-right text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Output & WIP</th>
                            <th
                                class="px-6 py-4 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                OEE & Metrik</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Catatan</th>
                            <th
                                class="px-6 py-4 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-32">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse ($reports as $index => $report)
                            @php
                                $isLocked = $report->status === 'verified';
                                $prio = strtolower($report->batch->priority ?? 'medium');
                                $color = match ($prio) {
                                    'high' => 'border-rose-100 text-rose-600 bg-rose-50',
                                    'medium' => 'border-amber-100 text-amber-600 bg-amber-50',
                                    'low' => 'border-emerald-100 text-emerald-600 bg-emerald-50',
                                    'default' => 'border-slate-100 text-slate-600 bg-slate-50',
                                };
                                $showEdit = !$isLocked;
                                $userRole = auth()->user()->role;

                                // --- LOGIKA BARU UNTUK CYCLE TIME & CAVITY ---
                                // Mengambil data master (Standar) dari 'master_cycle_time' yang di-join di Controller
                                // Jika tidak ada, gunakan data cycle_time dari laporan sebagai fallback
                                $ctStd = $report->master_cycle_time ?? $report->cycle_time;
                                $cavStd = $report->master_cavity ?? $report->cavity;

                                $ctAct = $report->actual_cycle_time;
                                $cavAct = $report->actual_cavity;

                                // Logika Warna CT
                                $ctClass = '';
                                $ctIcon = '';
                                if ($ctStd > 0 && $ctAct > $ctStd) {
                                    $ctClass = 'text-rose-600 font-bold';
                                    $ctIcon =
                                        '<i class="fa-solid fa-arrow-trend-up text-rose-500 text-[10px] ml-1"></i>';
                                } elseif ($ctStd > 0 && $ctAct > 0 && $ctAct < $ctStd) {
                                    $ctClass = 'text-emerald-600 font-bold';
                                    $ctIcon =
                                        '<i class="fa-solid fa-arrow-trend-down text-emerald-500 text-[10px] ml-1"></i>';
                                } elseif ($ctStd == 0) {
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
                            <tr
                                class="table-row-hover transition-colors {{ $isLocked ? 'bg-slate-50/50 dark:bg-slate-800/50' : '' }}">
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400 font-medium">
                                    {{ ($reports->currentPage() - 1) * $reports->perPage() + $loop->iteration }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($isLocked)
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700"><i
                                                class="fas fa-lock mr-1"></i> Final</span>
                                    @else
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600"><i
                                                class="fas fa-pen-to-square mr-1"></i> Draft</span>
                                    @endif
                                    <div class="relative group/qr inline-block cursor-pointer mt-2"
                                        onclick="window.showQr('{{ $report->batch->batch_code }}')">
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ $report->batch->batch_code }}"
                                            class="h-8 w-8 mx-auto border border-slate-200 p-0.5 rounded bg-white">
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-brand-600">{{ $report->batch->batch_code }}</div>
                                    <div
                                        class="text-xs text-slate-400 mt-0.5 font-mono bg-slate-100 px-1.5 py-0.5 rounded w-fit">
                                        {{ $report->report_code }}</div>
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold border uppercase mt-1 {{ $color }}">{{ $prio }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-slate-700">
                                        {{ \Carbon\Carbon::parse($report->production_date)->format('d M Y') }}</div>
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-500 mt-0.5 uppercase">{{ $report->shift->name }}</span>
                                    <div class="text-[10px] text-slate-400 mt-1"><i
                                            class="fas fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($report->start_time)->format('H:i') }}
                                        - {{ \Carbon\Carbon::parse($report->end_time)->format('H:i') }}</div>
                                    @if ($report->downtime_total > 0)
                                        <div class="text-[10px] mt-0.5 text-rose-500 font-bold" title="Downtime"><i
                                                class="fas fa-pause-circle mr-1"></i>{{ number_format($report->downtime_total, 0) }}
                                            min</div>
                                    @endif
                                    <div class="text-xs text-slate-600 mt-1 font-medium"><i
                                            class="fas fa-user mr-1"></i>{{ $report->operator->name ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-slate-800">{{ $report->machine->name }}</div>
                                    <div class="text-xs text-slate-500 truncate max-w-[150px] font-medium">
                                        {{ $report->product->name }}</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">{{ $report->color->name ?? '-' }}</div>
                                    <div class="mt-2 text-[10px] text-slate-500 border-t border-slate-100 pt-1">
                                        <div><span class="font-bold">Runner:</span>
                                            {{ number_format($report->total_runner, 2, ',', '.') }} Kg</div>
                                        <div><span class="font-bold">Purging:</span>
                                            {{ number_format($report->purging_kg, 2, ',', '.') }} Kg</div>
                                    </div>
                                </td>

                                {{-- KOLOM CYCLE TIME (DATA MASTER VS AKTUAL) --}}
                                <td class="px-6 py-4 text-center border-l border-r border-slate-100 dark:border-slate-700">
                                    <div class="flex justify-center items-center gap-6">
                                        {{-- Standard (Master) --}}
                                        <span
                                            class="text-sm {{ $ctStd == 0 ? 'text-rose-400 italic' : 'text-slate-500' }}">
                                            {{ $ctStd > 0 ? number_format($ctStd, 1) : '0' }}
                                        </span>
                                        {{-- Aktual --}}
                                        <span class="text-sm {{ $ctClass }}">
                                            {{ number_format($ctAct, 1) }} {!! $ctIcon !!}
                                        </span>
                                    </div>
                                </td>

                                {{-- KOLOM CAVITY (DATA MASTER VS AKTUAL) --}}
                                <td class="px-6 py-4 text-center border-r border-slate-100 dark:border-slate-700">
                                    <div class="flex justify-center items-center gap-6">
                                        {{-- Standard (Master) --}}
                                        <span
                                            class="text-sm {{ $cavStd == 0 ? 'text-rose-400 italic' : 'text-slate-500' }}">
                                            {{ $cavStd > 0 ? $cavStd : '0' }}
                                        </span>
                                        {{-- Aktual --}}
                                        <span class="text-sm {{ $cavClass }}">
                                            {{ $cavAct }} {!! $cavIcon !!}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="text-sm font-bold text-slate-800">
                                        {{ number_format($report->qty_good, 0, ',', '.') }} <span
                                            class="text-xs font-normal text-slate-400">FG</span></div>
                                    <div class="text-xs font-semibold text-rose-500 mt-0.5">
                                        {{ number_format($report->qty_reject_total, 0, ',', '.') }} <span
                                            class="font-normal text-rose-300">NG</span></div>
                                    <div class="text-xs font-semibold text-blue-500 mt-0.5">
                                        {{ number_format($report->qty_sample, 0, ',', '.') }} <span
                                            class="font-normal text-blue-300">SAMP</span></div>
                                    <div class="mt-2 pt-1 border-t border-slate-100 text-[10px]">
                                        <div class="flex justify-between gap-2 text-slate-500"><span>WIP
                                                Awal:</span><span>{{ number_format($report->wip_previous, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between gap-2 text-slate-700 font-bold"><span>WIP
                                                Akhir:</span><span>{{ number_format($report->wip, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex flex-col space-y-1">
                                        <div class="flex justify-between text-xs w-24 mx-auto"><span
                                                class="text-slate-400">Eff:</span><span
                                                class="font-bold {{ $report->efficiency >= 90 ? 'text-emerald-600' : 'text-amber-600' }}">{{ number_format($report->efficiency, 1) }}%</span>
                                        </div>
                                        <div class="flex justify-between text-xs w-24 mx-auto"><span
                                                class="text-slate-400">Yld:</span><span
                                                class="font-bold text-emerald-600">{{ number_format($report->yield, 1) }}%</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-[10px] text-amber-600 font-bold mb-1"><i
                                            class="fas fa-box mr-1"></i>{{ number_format($report->packaging_qty, 0, ',', '.') }}
                                    </div>
                                    <div class="text-xs text-slate-500 max-w-[150px] truncate"
                                        title="{{ $report->notes }}">{{ $report->notes ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center items-center gap-1">
                                        {{-- VIEW (Link) --}}
                                        <a href="{{ route('daily-reports.show', $report->id) }}"
                                            class="p-2 text-slate-400 hover:text-brand-600 rounded-lg"><i
                                                class="fas fa-eye"></i></a>

                                        {{-- EDIT (Link) --}}
                                        @if ($showEdit)
                                            <a href="{{ route('daily-reports.edit', $report->id) }}"
                                                class="p-2 text-slate-400 hover:text-amber-600 rounded-lg"><i
                                                    class="fas fa-pen"></i></a>
                                        @endif

                                        {{-- DELETE --}}
                                        @if (in_array($userRole, ['admin', 'super_admin']) && !$isLocked)
                                            <button type="button"
                                                onclick="window.confirmDelete('{{ route('daily-reports.destroy', $report->id) }}')"
                                                class="p-2 text-slate-400 hover:text-rose-600 rounded-lg"><i
                                                    class="fas fa-trash-alt"></i></button>
                                        @endif

                                        {{-- LOCK --}}
                                        @if (in_array($userRole, ['admin', 'super_admin']))
                                            <form action="{{ route('daily-reports.toggle-lock', $report->id) }}"
                                                method="POST" class="inline-block">@csrf
                                                <button type="submit"
                                                    class="p-2 rounded-lg transition-colors {{ $isLocked ? 'text-emerald-600 hover:bg-emerald-50' : 'text-slate-300 hover:text-slate-500' }}">
                                                    <i class="fas {{ $isLocked ? 'fa-lock' : 'fa-lock-open' }}"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-20 text-center text-slate-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="bg-slate-50 dark:bg-slate-700/50 rounded-full p-4 mb-3"><i
                                                class="fas fa-file-circle-xmark text-3xl text-slate-300 dark:text-slate-500"></i>
                                        </div>
                                        <span class="text-sm font-medium">Belum ada data laporan.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div
                class="px-6 py-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/30 rounded-b-2xl">
                {{ $reports->links() }}
            </div>
        </div>
    </div>

    {{-- HIDDEN FORM FOR DELETE --}}
    <form id="deleteForm" method="POST" class="hidden">@csrf @method('DELETE')</form>

    {{-- MODAL IMPORT (Tetap Pop-up karena simpel) --}}
    @if (in_array(auth()->user()->role, ['admin', 'super_admin']))
        <div id="importModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20">
                <div class="fixed inset-0 bg-slate-900/60 dark:bg-slate-900/80 backdrop-blur-sm"
                    onclick="window.closeModal('importModal')"></div>
                <div
                    class="relative inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-100 dark:border-slate-700">
                    <div class="bg-white dark:bg-slate-800 px-8 pt-8 pb-6">
                        <div class="text-center">
                            <div
                                class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-emerald-50 dark:bg-emerald-900/20 mb-4">
                                <i class="fas fa-file-excel text-emerald-500 text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Import Excel</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Upload file .xlsx</p>
                        </div>
                        <div class="mt-6">
                            <form action="{{ route('daily-reports.import') }}" method="POST"
                                enctype="multipart/form-data" id="importForm">@csrf
                                <div
                                    class="flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 dark:border-slate-600 border-dashed rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 cursor-pointer relative group">
                                    <input type="file" name="file" required
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                    <div class="space-y-1 text-center"><i
                                            class="fas fa-cloud-arrow-up text-slate-400 text-3xl group-hover:text-brand-500 transition-colors"></i>
                                        <div class="text-sm font-medium text-slate-600 dark:text-slate-300">Klik upload
                                        </div>
                                        <p class="text-xs text-slate-400">XLSX up to 5MB</p>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="bg-slate-5 dark:bg-slate-700/50 px-6 py-4 flex justify-end gap-3">
                        <button type="button" onclick="window.closeModal('importModal')"
                            class="px-4 py-2 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-200 font-bold hover:bg-slate-50 dark:hover:bg-slate-600 text-sm">Batal</button>
                        <button type="submit" form="importForm"
                            class="px-4 py-2 bg-emerald-600 text-white rounded-lg font-bold hover:bg-emerald-700 text-sm shadow-md">Upload</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- SCRIPTS --}}
    <script>
        window.closeModal = function(id = 'formModal') {
            document.getElementById(id).classList.add('hidden');
        }
        window.openModal = function(id) {
            document.getElementById(id).classList.remove('hidden');
        }

        window.toggleExportMenu = function(e) {
            e.stopPropagation();
            const menu = document.getElementById('exportMenu');
            menu.classList.toggle('hidden');
        }
        document.addEventListener('click', function(e) {
            const menu = document.getElementById('exportMenu');
            if (menu && !menu.contains(e.target)) menu.classList.add('hidden');
        });

        // Initialize Filter Components
        flatpickr(".date-filter", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d/m/Y",
            allowInput: true
        });
        $('#filter_shift').select2({
            placeholder: "Semua Shift",
            allowClear: true,
            width: '100%'
        });

        window.confirmDelete = function(url) {
            Swal.fire({
                title: 'Hapus Laporan?',
                text: "Data tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#e2e8f0' : '#334155',
                customClass: {
                    popup: 'rounded-2xl'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#deleteForm').attr('action', url);
                    $('#deleteForm').submit();
                }
            });
        }

        window.showQr = function(code) {
            Swal.fire({
                title: 'QR Code Batch',
                text: code,
                imageUrl: `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${code}`,
                imageWidth: 300,
                imageHeight: 300,
                imageAlt: 'QR Code',
                showCloseButton: true,
                showConfirmButton: false,
                background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#e2e8f0' : '#334155',
                customClass: {
                    popup: 'rounded-2xl'
                }
            });
        }

        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: "{{ session('success') }}",
                timer: 2000,
                showConfirmButton: false,
                background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#e2e8f0' : '#334155',
                customClass: {
                    popup: 'rounded-2xl'
                }
            });
        @endif
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: "{{ session('error') }}",
                background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#e2e8f0' : '#334155'
            });
        @endif
    </script>
@endsection
