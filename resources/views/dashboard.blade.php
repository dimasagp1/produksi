@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @php
        $chartCategories = $topMachineProducts
            ->map(function ($item) {
                $lines = [$item->machine_name];
                $productLines = explode("\n", wordwrap($item->product_name, 25, "\n"));
                return array_merge($lines, $productLines);
            })
            ->values();

        $userRole = auth()->user()->role;
        $isFullAccess = in_array($userRole, ['admin', 'manager', 'super_admin']);
    @endphp

    {{-- LIBRARIES --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://npmcdn.com/flatpickr/dist/themes/dark.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- FONT INTER --}}
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">

    {{-- SELECT2 & JQUERY --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    {{-- CUSTOM STYLE --}}
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        /* Animations */
        .animate-entry {
            opacity: 0;
            animation-fill-mode: forwards;
            animation-timing-function: cubic-bezier(0.34, 1.56, 0.64, 1);
            animation-duration: 0.6s;
        }

        .slide-down {
            animation-name: slideDownAnim;
        }

        @keyframes slideDownAnim {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .pop-in {
            animation-name: popInAnim;
        }

        @keyframes popInAnim {
            from {
                opacity: 0;
                transform: scale(0.8);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .slide-up {
            animation-name: slideUpAnim;
        }

        @keyframes slideUpAnim {
            from {
                opacity: 0;
                transform: translateY(40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .delay-0 {
            animation-delay: 0ms;
        }

        .delay-100 {
            animation-delay: 100ms;
        }

        .delay-200 {
            animation-delay: 200ms;
        }

        .delay-300 {
            animation-delay: 300ms;
        }

        .delay-400 {
            animation-delay: 400ms;
        }

        .delay-500 {
            animation-delay: 500ms;
        }

        /* Select2 Styles */
        .select2-container .select2-selection--single {
            height: 50px !important;
            border-radius: 1rem !important;
            display: flex;
            align-items: center;
            border: 1px solid #e2e8f0 !important;
            background-color: #f8fafc !important;
        }

        .dark .select2-container .select2-selection--single {
            background-color: #1e293b !important;
            border: 1px solid #334155 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #475569 !important;
            padding-left: 20px;
            font-weight: 600;
        }

        .dark .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #e2e8f0 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 48px !important;
            right: 15px !important;
        }

        .select2-dropdown {
            border-radius: 1rem !important;
            padding: 5px;
        }

        .dark .select2-dropdown {
            background-color: #1e293b !important;
            border: 1px solid #334155 !important;
        }

        .dark .select2-search__field {
            background-color: #0f172a !important;
            color: #f1f5f9 !important;
            border-color: #334155 !important;
        }

        .dark .select2-results__option {
            color: #f1f5f9 !important;
        }

        .dark .select2-results__option--highlighted[aria-selected] {
            background-color: #3b82f6 !important;
            color: #fff !important;
        }

        /* KPI Card */
        .kpi-card {
            background-color: white;
            border: 1px solid #e2e8f0;
            border-radius: 1.5rem;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }

        .dark .kpi-card {
            background-color: #1e293b;
            border-color: #334155;
        }

        .kpi-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border-color: #6366f1;
        }

        /* === APEXCHARTS DARK MODE FIX === */
        /* Memaksa Tooltip menjadi gelap saat Dark Mode aktif */
        .dark .apexcharts-tooltip {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            color: #f8fafc !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5) !important;
        }

        .dark .apexcharts-tooltip-title {
            background-color: #0f172a !important;
            border-bottom: 1px solid #334155 !important;
            font-family: 'Inter', sans-serif !important;
            color: #fff !important;
        }

        .dark .apexcharts-tooltip-text {
            color: #cbd5e1 !important;
        }

        .dark .apexcharts-xaxistooltip {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            color: #f8fafc !important;
        }

        /* Memastikan teks legend terlihat */
        .dark .apexcharts-legend-text {
            color: #cbd5e1 !important;
        }
    </style>

    {{-- FILTER SECTION --}}
    <div class="mb-8 animate-entry slide-down delay-0">
        <div
            class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl p-6 rounded-3xl shadow-lg border border-white/50 dark:border-slate-700">
            <form action="{{ route('dashboard') }}" method="GET"
                class="flex flex-col 2xl:flex-row items-stretch 2xl:items-end gap-4 justify-between">
                <div class="flex flex-wrap items-end gap-3.5 w-full 2xl:w-auto flex-1">
                    {{-- Start Date --}}
                    <div class="w-full sm:w-40 group">
                        <label
                            class="block text-[10px] uppercase tracking-wider font-bold text-slate-400 mb-1.5 ml-1">Periode
                            Awal</label>
                        <div class="relative transition-all duration-300 group-hover:-translate-y-0.5">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none"><i
                                    class="fa-regular fa-calendar-days text-brand-500 z-10"></i></div>
                            <input type="text" name="start_date" id="start_date"
                                class="block w-full h-[50px] pl-11 pr-4 bg-slate-50 dark:bg-slate-900 border-0 ring-1 ring-slate-200 dark:ring-slate-700 rounded-2xl text-sm font-semibold text-slate-700 dark:text-slate-200 placeholder:text-slate-400 focus:ring-2 focus:ring-brand-500 transition-all shadow-sm"
                                placeholder="Pilih Tanggal" value="{{ $startDate->format('Y-m-d') }}">
                        </div>
                    </div>
                    {{-- End Date --}}
                    <div class="w-full sm:w-40 group">
                        <label
                            class="block text-[10px] uppercase tracking-wider font-bold text-slate-400 mb-1.5 ml-1">Periode
                            Akhir</label>
                        <div class="relative transition-all duration-300 group-hover:-translate-y-0.5">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none"><i
                                    class="fa-regular fa-calendar-check text-brand-500 z-10"></i></div>
                            <input type="text" name="end_date" id="end_date"
                                class="block w-full h-[50px] pl-11 pr-4 bg-slate-50 dark:bg-slate-900 border-0 ring-1 ring-slate-200 dark:ring-slate-700 rounded-2xl text-sm font-semibold text-slate-700 dark:text-slate-200 placeholder:text-slate-400 focus:ring-2 focus:ring-brand-500 transition-all shadow-sm"
                                placeholder="Pilih Tanggal" value="{{ $endDate->format('Y-m-d') }}">
                        </div>
                    </div>
                    {{-- Product Select --}}
                    <div id="product-select-wrapper" class="w-full sm:w-60 md:w-72 flex-1 min-w-[200px] max-w-sm group">
                        <label
                            class="block text-[10px] uppercase tracking-wider font-bold text-slate-400 mb-1.5 ml-1">Filter
                            Produk</label>
                        <div class="transition-all duration-300 group-hover:-translate-y-0.5">
                            <select name="product_id" class="select2-product w-full">
                                <option value="">-- Semua Produk --</option>
                                @foreach ($productsList as $prod)
                                    <option value="{{ $prod->id }}" {{ $productId == $prod->id ? 'selected' : '' }}>
                                        {{ $prod->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- Submit Button --}}
                    <div class="w-full sm:w-auto">
                        <label
                            class="hidden sm:block text-[10px] uppercase tracking-wider font-bold text-transparent mb-1.5">Action</label>
                        <button type="submit"
                            class="w-full sm:w-auto h-[50px] px-6 bg-slate-900 hover:bg-slate-800 dark:bg-indigo-600 dark:hover:bg-indigo-500 text-white rounded-2xl text-sm font-bold shadow-lg transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-2"><i
                                class="fa-solid fa-filter"></i> <span>Terapkan</span></button>
                    </div>
                </div>
                {{-- Export Buttons --}}
                <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end shrink-0 mt-3 2xl:mt-0 pt-3 2xl:pt-0 border-t 2xl:border-t-0 border-slate-200/60 dark:border-slate-700/60">
                    <button type="button" onclick="downloadExport('excel')"
                        class="flex-1 sm:flex-none h-[50px] px-5 bg-emerald-50 text-emerald-600 ring-1 ring-emerald-200 hover:bg-emerald-500 hover:text-white rounded-2xl text-sm font-bold transition-all duration-300 flex items-center justify-center gap-2 group"><i
                            class="fa-solid fa-file-excel text-lg group-hover:scale-110 transition-transform"></i><span>Excel</span></button>
                    <button type="button" onclick="downloadExport('pdf')"
                        class="flex-1 sm:flex-none h-[50px] px-5 bg-rose-50 text-rose-600 ring-1 ring-rose-200 hover:bg-rose-500 hover:text-white rounded-2xl text-sm font-bold transition-all duration-300 flex items-center justify-center gap-2 group"><i
                            class="fa-solid fa-file-pdf text-lg group-hover:scale-110 transition-transform"></i><span>PDF</span></button>
                </div>
            </form>
        </div>
    </div>

    {{-- KPI CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6 mb-10 animate-entry pop-in delay-100">
        {{-- Target --}}
        <div class="kpi-card group">
            <div
                class="absolute top-4 right-4 p-3 rounded-2xl bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 transition-transform group-hover:scale-110 duration-300">
                <i class="fa-solid fa-bullseye text-xl"></i></div>
            <div class="relative z-10 mt-2">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">Target
                    Produksi</p>
                <h3 id="kpi-target" class="text-3xl font-black text-slate-800 dark:text-white tracking-tight mb-2">
                    {{ number_format($totalTarget, 0, ',', '.') }}</h3>
                <div
                    class="text-[10px] font-bold text-blue-600 bg-blue-50 dark:bg-blue-900/30 inline-block px-2 py-1 rounded-lg">
                    Total Plan</div>
            </div>
        </div>
        {{-- Output --}}
        <div class="kpi-card group">
            <div
                class="absolute top-4 right-4 p-3 rounded-2xl bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 transition-transform group-hover:scale-110 duration-300">
                <i class="fa-solid fa-boxes-stacked text-xl"></i></div>
            <div class="relative z-10 mt-2">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">Total Output
                </p>
                <h3 id="kpi-output" class="text-3xl font-black text-slate-800 dark:text-white tracking-tight mb-2">
                    {{ number_format($totalOutput, 0, ',', '.') }}</h3>
                <div
                    class="text-[10px] font-bold text-indigo-600 bg-indigo-50 dark:bg-indigo-900/30 inline-block px-2 py-1 rounded-lg">
                    Actual Prod</div>
            </div>
        </div>
        {{-- Achievement --}}
        <div class="kpi-card group">
            <div
                class="absolute top-4 right-4 p-3 rounded-2xl bg-amber-50 dark:bg-amber-900/20 text-amber-500 dark:text-amber-400 transition-transform group-hover:scale-110 duration-300">
                <i class="fa-solid fa-trophy text-xl"></i></div>
            <div class="relative z-10 mt-2">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">Achievement
                </p>
                <h3 id="kpi-achievement" class="text-3xl font-black text-slate-800 dark:text-white tracking-tight mb-2">
                    {{ number_format($achievement, 2) }}%</h3>
                <span id="kpi-achievement-badge"
                    class="text-[10px] font-bold px-2 py-1 rounded-lg {{ $achievement >= 100 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' }}">{{ $achievement >= 100 ? 'Achieved' : 'Not Achieved' }}</span>
            </div>
        </div>
        {{-- Reject --}}
        <div class="kpi-card group">
            <div
                class="absolute top-4 right-4 p-3 rounded-2xl bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 transition-transform group-hover:scale-110 duration-300">
                <i class="fa-solid fa-triangle-exclamation text-xl"></i></div>
            <div class="relative z-10 mt-2">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">Total Reject
                </p>
                <h3 id="kpi-reject" class="text-3xl font-black text-slate-800 dark:text-white tracking-tight mb-2">
                    {{ number_format($totalReject, 0, ',', '.') }}</h3>
                <div
                    class="text-[10px] font-bold text-red-600 bg-red-50 dark:bg-red-900/30 inline-block px-2 py-1 rounded-lg">
                    Unit Gagal</div>
            </div>
        </div>
        {{-- Yield --}}
        <div class="kpi-card group">
            <div
                class="absolute top-4 right-4 p-3 rounded-2xl bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 transition-transform group-hover:scale-110 duration-300">
                <i class="fa-solid fa-percent text-xl"></i></div>
            <div class="relative z-10 mt-2">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">Avg Yield</p>
                <h3 id="kpi-yield" class="text-3xl font-black text-slate-800 dark:text-white tracking-tight mb-2">
                    {{ number_format($avgYield, 2) }}%</h3>
                <div
                    class="text-[10px] font-bold text-purple-600 bg-purple-50 dark:bg-purple-900/30 inline-block px-2 py-1 rounded-lg">
                    Quality</div>
            </div>
        </div>
        {{-- Efficiency --}}
        <div class="kpi-card group">
            <div
                class="absolute top-4 right-4 p-3 rounded-2xl bg-teal-50 dark:bg-teal-900/20 text-teal-600 dark:text-teal-400 transition-transform group-hover:scale-110 duration-300">
                <i class="fa-solid fa-gauge-high text-xl"></i></div>
            <div class="relative z-10 mt-2">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">Efisiensi</p>
                <h3 id="kpi-efficiency" class="text-3xl font-black text-slate-800 dark:text-white tracking-tight mb-2">
                    {{ number_format($avgEfficiency, 2) }}%</h3>
                <div id="kpi-eff-badge"
                    class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg text-[10px] font-bold {{ $avgEfficiency >= 90 ? 'bg-teal-100 text-teal-700 dark:bg-teal-900/30 dark:text-teal-400' : 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400' }}">
                    {{ $avgEfficiency >= 90 ? 'Optimal' : 'Check' }}</div>
            </div>
        </div>
    </div>

    {{-- CHART 1: TREND (COMBO CHART) --}}
    <div class="space-y-8 mb-10 animate-entry slide-up delay-200">
        <div
            class="bg-white dark:bg-slate-800 p-8 rounded-[2rem] shadow-lg border border-slate-100 dark:border-slate-700 w-full relative overflow-hidden">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <div class="bg-indigo-100 dark:bg-slate-700 p-3 rounded-2xl text-indigo-600 dark:text-indigo-400"><i
                            class="fa-solid fa-chart-area text-xl"></i></div>
                    <div>
                        <h4 class="font-bold text-slate-800 dark:text-white text-xl">Tren Produksi Botol</h4>
                        <p class="text-sm font-medium text-slate-400 dark:text-slate-500">Analisis Output Harian vs Target
                        </p>
                    </div>
                </div>
            </div>
            <div id="chartTrendOutput" class="w-full h-[450px]"></div>
        </div>

        {{-- TABLE DETAIL --}}
        <div
            class="bg-white dark:bg-slate-800 rounded-[2rem] shadow-lg border border-slate-100 dark:border-slate-700 overflow-hidden">
            <div
                class="px-8 py-6 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/30 dark:bg-slate-800/30">
                <div class="flex items-center gap-4">
                    <div class="bg-slate-100 dark:bg-slate-700 p-2.5 rounded-xl text-slate-500 dark:text-slate-400"><i
                            class="fa-solid fa-table text-lg"></i></div>
                    <div>
                        <h4 class="font-bold text-slate-800 dark:text-white text-lg">Detail Data Produksi</h4>
                        <p class="text-xs font-medium text-slate-400">Klik baris produk untuk melihat detail harian</p>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left">
                    <thead
                        class="bg-slate-50 dark:bg-slate-900/50 text-[11px] font-extrabold text-slate-400 uppercase tracking-widest">
                        <tr>
                            <th class="px-4 py-4 text-center w-12"></th>
                            <th class="px-4 py-4 text-center w-12">No</th>
                            <th class="px-6 py-4">Nama Produk / Tanggal</th>
                            <th class="px-6 py-4 text-right">Qty Theory</th>
                            <th class="px-6 py-4 text-right">Total Output</th>
                            <th class="px-6 py-4 text-right">Qty Good</th>
                            <th class="px-6 py-4 text-right">Qty Reject</th>
                            <th class="px-6 py-4 text-center">Theo Yield</th>
                            <th class="px-6 py-4 text-center">Act Yield</th>
                            <th class="px-6 py-4 text-center">Reject %</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50 text-slate-700 dark:text-slate-300">
                        @forelse($trendTableData as $productName => $rows)
                            @php
                                $totalTheory = $rows->sum('sum_theory');
                                $totalOutput = $rows->sum('sum_output');
                                $totalGood = $rows->sum('sum_good');
                                $totalReject = $rows->sum('sum_reject');
                                $avgTheoYield = $totalTheory > 0 ? ($totalOutput / $totalTheory) * 100 : 0;
                                $avgActYield = $totalOutput > 0 ? ($totalGood / $totalOutput) * 100 : 0;
                                $avgRejectPct = $totalOutput > 0 ? ($totalReject / $totalOutput) * 100 : 0;
                                $rowId = 'prod-' . Str::slug($productName) . '-' . $loop->index;
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors cursor-pointer group"
                                onclick="toggleRow('{{ $rowId }}')">
                                <td
                                    class="px-4 py-4 text-center text-slate-300 group-hover:text-brand-500 transition-colors">
                                    <i id="icon-{{ $rowId }}"
                                        class="fa-solid fa-chevron-right text-xs transition-transform duration-300"></i>
                                </td>
                                <td class="px-4 py-4 text-center font-bold text-slate-400">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-8 w-8 rounded-lg bg-indigo-50 dark:bg-slate-700 flex items-center justify-center text-indigo-600 font-bold text-xs">
                                            {{ substr($productName, 0, 2) }}</div>
                                        <div>
                                            <div class="font-bold text-slate-800 dark:text-white">{{ $productName }}
                                            </div>
                                            <div class="text-[10px] text-slate-400">{{ $rows->count() }} Hari Produksi
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right font-medium text-slate-500">
                                    {{ number_format($totalTheory, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-right font-bold text-indigo-600 dark:text-indigo-400">
                                    {{ number_format($totalOutput, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-right font-semibold text-emerald-600 dark:text-emerald-400">
                                    {{ number_format($totalGood, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-right font-semibold text-rose-600 dark:text-rose-400">
                                    {{ number_format($totalReject, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-center"><span
                                        class="px-2.5 py-1 rounded-md text-xs font-bold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">{{ number_format($avgTheoYield, 2) }}%</span>
                                </td>
                                <td class="px-6 py-4 text-center"><span
                                        class="px-2.5 py-1 rounded-md text-xs font-bold {{ $avgActYield >= 98 ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">{{ number_format($avgActYield, 2) }}%</span>
                                </td>
                                <td class="px-6 py-4 text-center"><span
                                        class="px-2.5 py-1 rounded-md text-xs font-bold {{ $avgRejectPct <= 2 ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">{{ number_format($avgRejectPct, 2) }}%</span>
                                </td>
                            </tr>
                            @foreach ($rows as $row)
                                @php
                                    $theoYield = $row->sum_theory > 0 ? ($row->sum_output / $row->sum_theory) * 100 : 0;
                                    $actYield = $row->sum_output > 0 ? ($row->sum_good / $row->sum_output) * 100 : 0;
                                    $rejectPct = $row->sum_output > 0 ? ($row->sum_reject / $row->sum_output) * 100 : 0;
                                @endphp
                                <tr
                                    class="hidden child-{{ $rowId }} bg-slate-50/50 dark:bg-slate-900/30 border-b border-slate-100 dark:border-slate-800">
                                    <td colspan="2"></td>
                                    <td class="px-6 py-3 pl-16">
                                        <div class="flex items-center gap-2 text-xs font-medium text-slate-500"><i
                                                class="fa-regular fa-calendar text-slate-400"></i>{{ \Carbon\Carbon::parse($row->date)->format('d M Y') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-3 text-right text-xs text-slate-500">
                                        {{ number_format($row->sum_theory, 0, ',', '.') }}</td>
                                    <td class="px-6 py-3 text-right text-xs font-bold text-slate-600 dark:text-slate-400">
                                        {{ number_format($row->sum_output, 0, ',', '.') }}</td>
                                    <td class="px-6 py-3 text-right text-xs text-emerald-600">
                                        {{ number_format($row->sum_good, 0, ',', '.') }}</td>
                                    <td class="px-6 py-3 text-right text-xs text-rose-600">
                                        {{ number_format($row->sum_reject, 0, ',', '.') }}</td>
                                    <td class="px-6 py-3 text-center text-xs text-slate-400">
                                        {{ number_format($theoYield, 2) }}%</td>
                                    <td class="px-6 py-3 text-center text-xs text-slate-400">
                                        {{ number_format($actYield, 2) }}%</td>
                                    <td class="px-6 py-3 text-center text-xs text-slate-400">
                                        {{ number_format($rejectPct, 2) }}%</td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="10" class="px-6 py-12 text-center text-slate-400 italic">Tidak ada data.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($isFullAccess)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 animate-entry slide-up delay-300">
                <div
                    class="bg-white dark:bg-slate-800 p-8 rounded-[2rem] shadow-lg border border-slate-100 dark:border-slate-700">
                    <div class="flex items-center gap-4 mb-8">
                        <div
                            class="bg-indigo-100 dark:bg-indigo-900/30 p-3 rounded-2xl text-indigo-600 dark:text-indigo-400">
                            <i class="fa-solid fa-industry text-xl"></i></div>
                        <div>
                            <h4 class="font-bold text-slate-800 dark:text-white text-lg">10 Mesin & Produk Teratas</h4>
                            <p class="text-sm font-medium text-slate-400">Output Tertinggi Periode Ini</p>
                        </div>
                    </div>
                    <div id="chartTopMachineProducts" class="w-full h-[450px]"></div>
                </div>
                <div
                    class="bg-white dark:bg-slate-800 p-8 rounded-[2rem] shadow-lg border border-slate-100 dark:border-slate-700">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="bg-rose-100 dark:bg-rose-900/30 p-3 rounded-2xl text-rose-600 dark:text-rose-400"><i
                                class="fa-solid fa-chart-bar text-xl"></i></div>
                        <div>
                            <h4 class="font-bold text-slate-800 dark:text-white text-lg">Total Reject per Mesin</h4>
                            <p class="text-sm font-medium text-slate-400">Analisis Reject Terbanyak</p>
                        </div>
                    </div>
                    <div id="chartMultiAxis" class="w-full h-[450px]"></div>
                </div>
            </div>
        @endif
    </div>

    @if ($isFullAccess)
        <div class="space-y-8 mb-10 animate-entry slide-up delay-400">
            {{-- Operator Performance --}}
            <div
                class="bg-white dark:bg-slate-800 p-8 rounded-[2rem] shadow-lg border border-slate-100 dark:border-slate-700">
                <div class="flex items-center gap-4 mb-8">
                    <div class="bg-blue-100 dark:bg-blue-900/30 p-3 rounded-2xl text-blue-600 dark:text-blue-400"><i
                            class="fa-solid fa-users-gear text-xl"></i></div>
                    <div>
                        <h4 class="font-bold text-slate-800 dark:text-white text-lg">Top 10 Performance Operator</h4>
                        <p class="text-sm font-medium text-slate-400">Peringkat Berdasarkan Output & Kualitas</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-5">
                    @forelse($operatorPerformance as $op)
                        <div
                            class="bg-slate-50 dark:bg-slate-900/50 p-5 rounded-2xl border border-slate-200 dark:border-slate-700/60 relative overflow-hidden group hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                            <div class="flex justify-between items-start mb-3"><span
                                    class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2 py-1 rounded-lg">#{{ $loop->iteration }}</span>
                                <div
                                    class="h-8 w-8 rounded-full bg-white dark:bg-slate-800 flex items-center justify-center text-slate-400 shadow-sm">
                                    <i class="fa-solid fa-user"></i></div>
                            </div>
                            <h5 class="font-bold text-slate-800 dark:text-white mb-4 truncate text-sm"
                                title="{{ $op->operator_name }}">{{ $op->operator_name }}</h5>
                            <div class="space-y-3">
                                <div>
                                    <div class="flex justify-between text-[10px] mb-1 font-semibold text-slate-500">
                                        <span>Output Rate</span><span
                                            class="{{ $op->output_rate >= 100 ? 'text-emerald-500' : 'text-blue-500' }}">{{ number_format($op->output_rate, 1) }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-blue-500 h-1.5 rounded-full transition-all duration-500 group-hover:bg-blue-400"
                                            style="width: {{ min($op->output_rate, 100) }}%"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between text-[10px] mb-1 font-semibold text-slate-500">
                                        <span>FG Rate</span><span
                                            class="{{ $op->fg_rate >= 100 ? 'text-emerald-500' : 'text-amber-500' }}">{{ number_format($op->fg_rate, 1) }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-emerald-500 h-1.5 rounded-full transition-all duration-500 group-hover:bg-emerald-400"
                                            style="width: {{ min($op->fg_rate, 100) }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div
                            class="col-span-full py-10 text-center text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-300">
                            Belum ada data kinerja operator.</div>
                    @endforelse
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 animate-entry slide-up delay-500">
                <div
                    class="bg-white dark:bg-slate-800 p-8 rounded-[2rem] shadow-lg border border-slate-100 dark:border-slate-700 flex flex-col h-full">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="bg-red-100 dark:bg-red-900/30 p-3 rounded-2xl text-red-600 dark:text-red-400"><i
                                class="fa-solid fa-bug text-xl"></i></div>
                        <div>
                            <h4 class="font-bold text-slate-800 dark:text-white text-lg">10 Isu Kualitas Teratas</h4>
                        </div>
                    </div>
                    @php $maxQty = $topRejects->max('total_qty') ?? 1; @endphp
                    <div class="space-y-5 flex-1">
                        @forelse($topRejects as $reject)
                            @php
                                $widthPercent = ($reject->total_qty / $maxQty) * 100;
                                $colors = ['bg-red-500', 'bg-orange-500', 'bg-amber-500', 'bg-lime-500', 'bg-sky-500'];
                                $colorClass = $colors[$loop->index % count($colors)];
                            @endphp
                            <div class="group">
                                <div class="flex justify-between items-end mb-2">
                                    <div class="flex items-start gap-3">
                                        <span class="text-xs font-bold text-slate-300 w-5">#{{ $loop->iteration }}</span>
                                        <div>
                                            <h5
                                                class="text-sm font-bold text-slate-700 dark:text-slate-200 group-hover:text-brand-600 transition-colors">
                                                {{ $reject->product_name }}</h5>
                                            <div class="text-[10px] text-slate-500 font-medium mt-0.5"><span
                                                    class="text-red-500">{{ $reject->reject_name }}</span> &bull;
                                                {{ $reject->machine_name }}</div>
                                        </div>
                                    </div>
                                    <div class="text-right"><span
                                            class="block text-sm font-black text-slate-800 dark:text-white">{{ number_format($reject->total_qty, 0, ',', '.') }}</span><span
                                            class="text-[10px] font-bold text-slate-400">{{ $totalReject > 0 ? number_format(($reject->total_qty / $totalReject) * 100, 1) : 0 }}%</span>
                                    </div>
                                </div>
                                <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2 overflow-hidden">
                                    <div class="{{ $colorClass }} h-2 rounded-full relative overflow-hidden group-hover:opacity-80 transition-all duration-500"
                                        style="width: {{ $widthPercent }}%"></div>
                                </div>
                            </div>
                        @empty
                            <div class="py-12 text-center text-slate-400 italic">Belum ada data reject.</div>
                        @endforelse
                    </div>
                    <div
                        class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/50 -mx-8 -mb-8 px-8 py-4">
                        <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total
                            Reject (Top 10)</span>
                        <div class="flex items-baseline gap-1"><span
                                class="text-lg font-black text-slate-800 dark:text-white">{{ number_format($totalTopRejectsQty, 0, ',', '.') }}</span><span
                                class="text-xs font-bold text-slate-400">Pcs</span></div>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-slate-800 p-8 rounded-[2rem] shadow-lg border border-slate-100 dark:border-slate-700 flex flex-col h-full">
                    <div class="flex items-center gap-4 mb-6">
                        <div
                            class="bg-orange-100 dark:bg-orange-900/30 p-3 rounded-2xl text-orange-600 dark:text-orange-400">
                            <i class="fa-solid fa-stopwatch text-xl"></i></div>
                        <div>
                            <h4 class="font-bold text-slate-800 dark:text-white text-lg">10 Downtime Teratas</h4>
                        </div>
                    </div>
                    <div class="overflow-hidden rounded-2xl border border-slate-100 dark:border-slate-700 flex-1">
                        <table class="w-full text-sm text-left">
                            <thead
                                class="bg-slate-5 dark:bg-slate-700/50 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                <tr>
                                    <th class="px-5 py-3">Mesin</th>
                                    <th class="px-5 py-3">Masalah</th>
                                    <th class="px-5 py-3 text-right">Durasi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @forelse($topDowntimes as $dt)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                        <td class="px-5 py-3 font-semibold text-slate-700 dark:text-slate-300"><span
                                                class="text-xs text-slate-400 mr-2">#{{ $loop->iteration }}</span>{{ $dt->machine_name }}
                                        </td>
                                        <td class="px-5 py-3"><span
                                                class="inline-block bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 px-2.5 py-1 rounded-lg text-[10px] font-bold border border-orange-100 dark:border-orange-900/30">{{ Str::limit($dt->downtime_reason, 50) }}</span>
                                        </td>
                                        <td class="px-5 py-3 text-right font-bold text-slate-800 dark:text-white">
                                            {{ number_format($dt->total_minutes, 0, ',', '.') }}m</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-5 py-8 text-center text-slate-400 italic">Tidak ada
                                            data downtime.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div
                        class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/50 -mx-8 -mb-8 px-8 py-4">
                        <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total
                            Durasi (Top 10)</span>
                        <div class="flex items-baseline gap-1"><span
                                class="text-lg font-black text-slate-800 dark:text-white">{{ number_format($totalTopDowntimeMinutes, 0, ',', '.') }}</span><span
                                class="text-xs font-bold text-slate-400">Menit</span></div>
                    </div>
                </div>
            </div>

            <div class="mt-8 animate-entry slide-up delay-500">
                <div class="flex items-center justify-between mb-6 px-2">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-1 bg-brand-500 rounded-full"></div>
                        <h4 class="font-bold text-slate-800 dark:text-white text-lg">Analisis Distribusi</h4>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                    @php $charts = [['id' => 'chartPieRejectItem', 'title' => 'Jenis Reject'], ['id' => 'chartPieMachine', 'title' => 'Mesin'], ['id' => 'chartPieOperator', 'title' => 'Operator'], ['id' => 'chartPieCoordinator', 'title' => 'Koordinator'], ['id' => 'chartPieShift', 'title' => 'Shift']]; @endphp
                    @foreach ($charts as $chart)
                        <div
                            class="bg-white dark:bg-slate-800 p-6 rounded-[2rem] shadow-lg border border-slate-100 dark:border-slate-700 flex flex-col items-center hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                            <h5
                                class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-4 text-center border-b border-slate-100 dark:border-slate-700 pb-2 w-full">
                                {{ $chart['title'] }}</h5>
                            <div id="{{ $chart['id'] }}" class="w-full flex justify-center"></div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div
                class="mt-10 bg-white dark:bg-slate-800 rounded-[2rem] shadow-lg border border-slate-100 dark:border-slate-700 overflow-hidden animate-entry slide-up delay-500">
                <div
                    class="px-8 py-6 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/50">
                    <h4 class="font-bold text-slate-800 dark:text-white text-lg flex items-center gap-3"><span
                            class="flex items-center justify-center w-8 h-8 rounded-xl bg-red-100 text-red-600"><i
                                class="fa-solid fa-list-ol text-sm"></i></span> 10 Detail Reject Teratas</h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                        <thead
                            class="bg-slate-50 dark:bg-slate-900/50 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                            <tr>
                                <th class="px-6 py-4 text-center w-12">No</th>
                                <th class="px-6 py-4 text-left">Produk</th>
                                <th class="px-6 py-4 text-left">Detail</th>
                                <th class="px-6 py-4 text-left">Mesin & Shift</th>
                                <th class="px-6 py-4 text-left">SDM</th>
                                <th class="px-6 py-4 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody
                            class="divide-y divide-slate-100 dark:divide-slate-700 text-sm text-slate-700 dark:text-slate-300">
                            @forelse($detailRejects as $d)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                    <td class="px-6 py-4 text-center text-slate-400 font-semibold">{{ $loop->iteration }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-800 dark:text-white">{{ $d->product_name }}</div>
                                        <div class="text-xs text-slate-400 mt-1">
                                            {{ \Carbon\Carbon::parse($d->production_date)->format('d M Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4"><span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-red-50 text-red-600 border border-red-100 text-xs font-bold">{{ $d->reject_name }}</span>
                                        <div class="text-xs text-slate-400 mt-1">Batch: {{ $d->batch_name }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium">{{ $d->machine_name }}</div><span
                                            class="text-[10px] bg-slate-100 dark:bg-slate-700 px-2 py-0.5 rounded text-slate-500 font-bold mt-1 inline-block">{{ $d->shift_name }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-xs">{{ $d->operator_name }}</div>
                                        <div class="text-[10px] text-slate-400 mt-0.5">{{ $d->coordinator_name }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-right"><span
                                            class="block font-black text-slate-800 dark:text-white text-base">{{ number_format($d->qty, 0, ',', '.') }}</span><span
                                            class="text-[10px] font-bold text-slate-400">({{ $totalReject > 0 ? number_format(($d->qty / $totalReject) * 100, 1) : 0 }}%)</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">Tidak ada data
                                        detail.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- JAVASCRIPT --}}
    <script>
        $(document).ready(function() {
            $('.select2-product').select2({
                placeholder: "-- Pilih Produk --",
                allowClear: true,
                width: '100%',
                dropdownCssClass: 'modern-dropdown'
            });
            setInterval(fetchRealtimeData, 5000);
        });

        var chartTrendOutput, chartTopMachineProducts, chartMultiAxis;

        const startDateInput = flatpickr("#start_date", {
            dateFormat: "Y-m-d",
            allowInput: true,
            maxDate: "today",
            locale: {
                firstDayOfWeek: 1
            },
            onChange: function(selectedDates, dateStr) {
                endDateInput.set('minDate', dateStr);
            }
        });
        const endDateInput = flatpickr("#end_date", {
            dateFormat: "Y-m-d",
            allowInput: true,
            maxDate: "today",
            locale: {
                firstDayOfWeek: 1
            }
        });

        @if (session('warning'))
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: "{{ session('warning') }}",
                confirmButtonColor: '#3b82f6',
                customClass: {
                    popup: 'rounded-3xl',
                    confirmButton: 'rounded-xl'
                }
            });
        @endif
        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Validasi Error',
                html: `<ul style="text-align: left;">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>`,
                confirmButtonColor: '#3b82f6',
                customClass: {
                    popup: 'rounded-3xl',
                    confirmButton: 'rounded-xl'
                }
            });
        @endif

        const commonChartOptions = {
            fontFamily: 'Inter, sans-serif',
            toolbar: {
                show: false
            },
            background: 'transparent',
            theme: {
                mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
            }
        };

        // --- 1. RENDER CHART TREND (COMBO CHART: BAR & LINE) ---
        var optionsTrend = {
            ...commonChartOptions,
            series: [{
                    name: 'Output',
                    type: 'column',
                    data: @json($trendOutput->pluck('total'))
                },
                {
                    name: 'Target',
                    type: 'line',
                    data: @json($trendOutput->pluck('total_target'))
                }
            ],
            chart: {
                id: 'trendChart',
                type: 'line',
                height: 450,
                ...commonChartOptions,
                dropShadow: {
                    enabled: true,
                    top: 10,
                    left: 0,
                    blur: 3,
                    color: '#000',
                    opacity: 0.1
                }
            },
            plotOptions: {
                bar: {
                    columnWidth: '50%',
                    borderRadius: 4
                }
            },
            stroke: {
                width: [0, 4],
                curve: 'straight'
            },
            dataLabels: {
                enabled: false
            },
            legend: {
                show: true,
                position: 'top',
                horizontalAlign: 'center'
            },
            markers: {
                size: [0, 5],
                colors: ["#fff"],
                strokeColors: "#10b981",
                strokeWidth: 2
            },

            // === PERBAIKAN TANGGAL HILANG (CATEGORY + TICKAMOUNT) ===
            xaxis: {
                categories: @json($trendOutput->pluck('label')),
                type: 'category',
                tickAmount: {{ count($trendOutput) }}, // Paksa tampil semua label
                labels: {
                    rotate: -45,
                    rotateAlways: true,
                    hideOverlappingLabels: false, // JANGAN sembunyikan label
                    style: {
                        colors: '#94a3b8',
                        fontSize: '10px',
                        fontFamily: 'Inter, sans-serif'
                    }
                },
                tooltip: {
                    enabled: false
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },

            yaxis: {
                labels: {
                    style: {
                        colors: '#94a3b8',
                        fontFamily: 'Inter, sans-serif'
                    },
                    formatter: function(val) {
                        return val.toLocaleString('id-ID');
                    }
                },
                title: {
                    text: 'Pcs',
                    style: {
                        color: '#94a3b8',
                        fontSize: '10px'
                    }
                }
            },
            colors: ['#3b82f6', '#10b981'],
            grid: {
                show: true,
                borderColor: document.documentElement.classList.contains('dark') ? '#334155' : '#e2e8f0',
                strokeDashArray: 0,
                padding: {
                    top: 0,
                    right: 20,
                    bottom: 0,
                    left: 10
                }
            },
            tooltip: {
                theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                x: {
                    format: 'dd MMMM yyyy'
                },
                y: {
                    formatter: function(val) {
                        return val.toLocaleString('id-ID') + " Pcs";
                    }
                }
            }
        };

        chartTrendOutput = new ApexCharts(document.querySelector("#chartTrendOutput"), optionsTrend);
        chartTrendOutput.render();

        function downloadExport(type) {
            let params = new URLSearchParams({
                export: type,
                start_date: document.querySelector('input[name="start_date"]').value,
                end_date: document.querySelector('input[name="end_date"]').value,
                product_id: document.querySelector('select[name="product_id"]').value
            });
            window.open("{{ route('dashboard') }}?" + params.toString(), '_blank');
        }

        function fetchRealtimeData() {
            let startDate = $('#start_date').val();
            let endDate = $('#end_date').val();
            let productId = $('.select2-product').val();
            $.ajax({
                url: "{{ route('dashboard.json') }}",
                type: "GET",
                data: {
                    start_date: startDate,
                    end_date: endDate,
                    product_id: productId
                },
                success: function(response) {
                    $('#kpi-target').text(response.kpi.total_target);
                    $('#kpi-achievement').text(response.kpi.achievement);
                    $('#kpi-output').text(response.kpi.total_output);
                    $('#kpi-reject').text(response.kpi.total_reject);
                    $('#kpi-yield').text(response.kpi.avg_yield);
                    $('#kpi-efficiency').text(response.kpi.avg_efficiency);
                    let achVal = response.kpi.achievement_val;
                    let achBadge = $('#kpi-achievement-badge');
                    achBadge.text(achVal >= 100 ? 'Achieved' : 'Not Achieved');
                    achBadge.attr('class',
                        `text-[10px] font-bold px-2 py-0.5 rounded-md ${achVal >= 100 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400'}`
                        );
                    let effVal = response.kpi.eff_val;
                    let effBadge = $('#kpi-eff-badge');
                    effBadge.text(effVal >= 90 ? 'Optimal' : 'Check');
                    effBadge.attr('class',
                        `inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[10px] font-bold ${effVal >= 90 ? 'bg-teal-100 text-teal-700 dark:bg-teal-900/30 dark:text-teal-400' : 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400'}`
                        );

                    if (chartTrendOutput) {
                        chartTrendOutput.updateOptions({
                            xaxis: {
                                categories: response.charts.trend.labels,
                                tickAmount: response.charts.trend.labels.length
                            }
                        });
                        chartTrendOutput.updateSeries([{
                                name: 'Total Output',
                                type: 'column',
                                data: response.charts.trend.output
                            },
                            {
                                name: 'Total Target',
                                type: 'line',
                                data: response.charts.trend.target
                            }
                        ]);
                    }

                    @if ($isFullAccess)
                        if (chartTopMachineProducts && response.charts.machine) {
                            chartTopMachineProducts.updateOptions({
                                xaxis: {
                                    categories: response.charts.machine.categories
                                }
                            });
                            chartTopMachineProducts.updateSeries([{
                                name: 'Total Output',
                                data: response.charts.machine.series
                            }]);
                        }
                        if (chartMultiAxis && response.charts.reject_machine) {
                            chartMultiAxis.updateOptions({
                                xaxis: {
                                    categories: response.charts.reject_machine.labels
                                }
                            });
                            chartMultiAxis.updateSeries([{
                                name: 'Total Reject',
                                data: response.charts.reject_machine.series
                            }]);
                        }
                    @endif
                },
                error: function(xhr) {
                    console.log("Realtime fetch failed: " + xhr.statusText);
                }
            });
        }

        @if ($isFullAccess)
            var optionsMachineProd = {
                ...commonChartOptions,
                series: [{
                    name: 'Total Output',
                    data: @json($trendOutput->pluck('total')),
                    dataLabels: {
                        offsetY: 10
                    }
                }, {
                    name: 'Target (Theory)',
                    data: @json($trendOutput->pluck('total_target')),
                    dataLabels: {
                        offsetY: -10
                    }
                }],
                chart: {
                    id: 'machineChart',
                    type: 'bar',
                    height: 450,
                    ...commonChartOptions
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        borderRadius: 6,
                        columnWidth: '50%',
                        distributed: true,
                        dataLabels: {
                            position: 'top'
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                    style: {
                        fontSize: '10px',
                        colors: document.documentElement.classList.contains('dark') ? ['#cbd5e1', '#34d399'] : [
                            '#475569', '#10b981'
                        ]
                    },
                    background: {
                        enabled: true,
                        foreColor: document.documentElement.classList.contains('dark') ? '#0f172a' : '#fff',
                        padding: 4,
                        borderRadius: 6,
                        borderWidth: 1,
                        borderColor: document.documentElement.classList.contains('dark') ? '#334155' : '#e2e8f0',
                        opacity: 0.9,
                        dropShadow: {
                            enabled: false
                        }
                    },
                    formatter: function(val) {
                        return val.toLocaleString('id-ID');
                    }
                },
                xaxis: {
                    categories: @json($chartCategories),
                    labels: {
                        show: true,
                        rotate: -45,
                        rotateAlways: true,
                        trim: false,
                        minHeight: 100,
                        maxHeight: 180,
                        style: {
                            fontSize: '10px',
                            fontFamily: 'Inter, sans-serif',
                            cssClass: 'apexcharts-xaxis-label'
                        }
                    },
                    title: {
                        text: 'Mesin & Produk',
                        style: {
                            color: document.documentElement.classList.contains('dark') ? '#94a3b8' : '#64748b',
                            fontSize: '11px',
                            fontWeight: 600,
                            fontFamily: 'Inter, sans-serif'
                        },
                        offsetY: 80
                    },
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    }
                },
                yaxis: {
                    labels: {
                        show: true,
                        align: 'left',
                        style: {
                            colors: document.documentElement.classList.contains('dark') ? '#cbd5e1' : '#475569',
                            fontSize: '11px',
                            fontWeight: 600,
                            fontFamily: 'Inter, sans-serif'
                        },
                        formatter: function(val) {
                            return val.toLocaleString('id-ID');
                        }
                    },
                    title: {
                        text: 'Jumlah Output (Pcs)',
                        style: {
                            color: document.documentElement.classList.contains('dark') ? '#94a3b8' : '#64748b',
                            fontSize: '11px',
                            fontWeight: 600,
                            fontFamily: 'Inter, sans-serif'
                        }
                    }
                },
                grid: {
                    show: true,
                    borderColor: document.documentElement.classList.contains('dark') ? '#334155' : '#f1f5f9',
                    strokeDashArray: 4,
                    padding: {
                        bottom: 20
                    }
                },
                colors: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899'],
                legend: {
                    show: false
                },
                tooltip: {
                    theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                    y: {
                        formatter: function(val) {
                            return val.toLocaleString('id-ID') + " Pcs"
                        }
                    }
                }
            };
            chartTopMachineProducts = new ApexCharts(document.querySelector("#chartTopMachineProducts"),
            optionsMachineProd);
            chartTopMachineProducts.render();

            var optionsMultiAxis = {
                ...commonChartOptions,
                series: [{
                    name: 'Total Reject',
                    data: @json($rejectByMachine->pluck('total'))
                }],
                chart: {
                    id: 'rejectChart',
                    type: 'bar',
                    height: 450,
                    ...commonChartOptions,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '45%',
                        borderRadius: 6,
                        distributed: true
                    }
                },
                dataLabels: {
                    enabled: true,
                    offsetY: -25,
                    style: {
                        fontSize: '10px',
                        fontWeight: 700,
                        colors: document.documentElement.classList.contains('dark') ? ['#cbd5e1'] : ['#475569']
                    },
                    formatter: function(val) {
                        return new Intl.NumberFormat('id-ID', {
                            notation: "compact",
                            compactDisplay: "short"
                        }).format(val);
                    }
                },
                colors: ['#ef4444', '#f97316', '#f59e0b', '#84cc16', '#10b981', '#06b6d4', '#6366f1'],
                xaxis: {
                    categories: @json($rejectByMachine->pluck('label')),
                    labels: {
                        style: {
                            colors: '#94a3b8',
                            fontSize: '11px',
                            fontFamily: 'Inter, sans-serif'
                        }
                    },
                    title: {
                        text: 'Mesin',
                        style: {
                            color: document.documentElement.classList.contains('dark') ? '#94a3b8' : '#64748b',
                            fontSize: '11px',
                            fontWeight: 600,
                            fontFamily: 'Inter, sans-serif'
                        },
                        offsetY: -5
                    },
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#94a3b8'
                        },
                        formatter: function(val) {
                            return val.toLocaleString('id-ID');
                        }
                    },
                    title: {
                        text: 'Jumlah Reject (Pcs)',
                        style: {
                            color: document.documentElement.classList.contains('dark') ? '#94a3b8' : '#64748b',
                            fontSize: '11px',
                            fontWeight: 600,
                            fontFamily: 'Inter, sans-serif'
                        }
                    }
                },
                grid: {
                    borderColor: document.documentElement.classList.contains('dark') ? '#334155' : '#f1f5f9',
                    strokeDashArray: 4
                },
                legend: {
                    show: false
                },
                tooltip: {
                    theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                    y: {
                        formatter: function(val) {
                            return val.toLocaleString('id-ID') + " Pcs";
                        }
                    }
                }
            };
            chartMultiAxis = new ApexCharts(document.querySelector("#chartMultiAxis"), optionsMultiAxis);
            chartMultiAxis.render();

            function renderPie(selector, data, labels, colors) {
                const container = document.querySelector(selector);
                if (!data || data.length === 0) {
                    container.innerHTML = '<div class="text-center text-xs text-slate-400 py-12 italic">Data Kosong</div>';
                    return;
                }
                container.classList.remove('flex-row');
                container.classList.add('flex', 'flex-col', 'items-center', 'h-full');
                var options = {
                    ...commonChartOptions,
                    series: data.map(Number),
                    chart: {
                        type: 'donut',
                        height: 220,
                        ...commonChartOptions
                    },
                    labels: labels,
                    colors: colors,
                    dataLabels: {
                        enabled: false
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '70%',
                                labels: {
                                    show: true,
                                    total: {
                                        show: true,
                                        showAlways: true,
                                        label: 'Total',
                                        color: '#94a3b8',
                                        fontSize: '11px',
                                        fontFamily: 'Plus Jakarta Sans, sans-serif',
                                        formatter: function(w) {
                                            return w.globals.seriesTotals.reduce((a, b) => a + b, 0).toLocaleString(
                                                'id-ID')
                                        }
                                    },
                                    value: {
                                        show: true,
                                        fontSize: '16px',
                                        fontWeight: '800',
                                        color: document.documentElement.classList.contains('dark') ? '#fff' : '#1e293b',
                                        offsetY: 2,
                                        fontFamily: 'Plus Jakarta Sans, sans-serif'
                                    }
                                }
                            }
                        }
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: document.documentElement.classList.contains('dark') ? ['#1e293b'] : ['#fff']
                    },
                    legend: {
                        show: false
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return val.toLocaleString('id-ID') + " Pcs"
                            }
                        },
                        theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                        style: {
                            fontFamily: 'Plus Jakarta Sans, sans-serif'
                        }
                    }
                };
                container.innerHTML = '';
                const chartDiv = document.createElement('div');
                chartDiv.className = "w-full flex justify-center shrink-0 relative z-10";
                container.appendChild(chartDiv);
                const chart = new ApexCharts(chartDiv, options);
                chart.render();
                let legendHtml =
                    `<div class="w-full mt-4 flex-1 min-h-0 overflow-y-auto custom-scroll pr-2 relative z-0 max-h-48 border-t border-dashed border-slate-200 dark:border-slate-700 pt-3"><div class="grid grid-cols-1 gap-y-2">`;
                labels.forEach((label, index) => {
                    let color = colors[index];
                    let value = new Intl.NumberFormat('id-ID').format(data[index]);
                    legendHtml +=
                        `<div class="flex items-center justify-between text-[10px] p-1.5 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group cursor-default"><div class="flex items-center gap-2.5 overflow-hidden mr-2"><span class="w-2.5 h-2.5 rounded-full shrink-0 ring-2 ring-white dark:ring-slate-800 shadow-sm" style="background-color: ${color}"></span><span class="truncate font-semibold text-slate-600 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-white transition-colors" title="${label}">${label}</span></div><span class="font-bold text-slate-700 dark:text-slate-200 shrink-0 bg-slate-100 dark:bg-slate-700 px-2 py-0.5 rounded text-[10px] group-hover:bg-white dark:group-hover:bg-slate-600 shadow-sm transition-all">${value}</span></div>`;
                });
                legendHtml += '</div></div>';
                container.insertAdjacentHTML('beforeend', legendHtml);
            }

            renderPie("#chartPieRejectItem", @json($rejectByRejectItem->pluck('total')), @json($rejectByRejectItem->pluck('label')), ['#ef4444',
                '#f97316', '#eab308', '#84cc16', '#06b6d4'
            ]);
            renderPie("#chartPieMachine", @json($rejectByMachine->pluck('total')), @json($rejectByMachine->pluck('label')), ['#3b82f6',
                '#10b981', '#f43f5e', '#8b5cf6', '#f59e0b'
            ]);
            renderPie("#chartPieOperator", @json($rejectByOperator->pluck('total')), @json($rejectByOperator->pluck('label')), ['#6366f1',
                '#ec4899', '#14b8a6', '#fcd34d', '#93c5fd'
            ]);
            renderPie("#chartPieCoordinator", @json($rejectByCoordinator->pluck('total')), @json($rejectByCoordinator->pluck('label')), ['#a855f7',
                '#ec4899', '#fb7185', '#22d3ee', '#4ade80'
            ]);
            renderPie("#chartPieShift", @json($rejectByShift->pluck('total')), @json($rejectByShift->pluck('label')), ['#2dd4bf', '#0ea5e9',
                '#38bdf8', '#60a5fa', '#a78bfa'
            ]);
        @endif

        function toggleRow(rowId) {
            const children = document.querySelectorAll(`.child-${rowId}`);
            children.forEach(child => {
                child.classList.toggle('hidden');
            });
            const icon = document.getElementById(`icon-${rowId}`);
            if (icon) {
                if (icon.classList.contains('rotate-90')) {
                    icon.classList.remove('rotate-90');
                } else {
                    icon.classList.add('rotate-90');
                }
            }
        }
    </script>
@endsection
