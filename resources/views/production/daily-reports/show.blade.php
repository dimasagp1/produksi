@extends('layouts.app')

@section('title', 'Detail Laporan ' . $report->report_code)

@section('content')
    {{-- CUSTOM STYLES --}}
    <style>
        /* Modern Input Style */
        .saas-input {
            height: 48px;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 0 16px;
            width: 100%;
            background-color: #f8fafc;
            color: #1e293b;
            font-size: 0.875rem;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* FIX CSS TEXTAREA: Agar Tinggi Sama dengan Form Edit/Create */
        textarea.saas-input {
            height: auto !important;
            min-height: 150px;
            line-height: 1.5;
            padding-top: 12px;
            padding-bottom: 12px;
        }

        /* Readonly specific styling */
        .saas-input[readonly] {
            background-color: #f1f5f9;
            color: #475569;
            cursor: default;
            border-color: #e2e8f0;
        }

        .dark .saas-input {
            background-color: #1e293b;
            border-color: #334155;
            color: #f1f5f9;
        }

        .dark .saas-input[readonly] {
            background-color: #0f172a;
            color: #94a3b8;
            border-color: #334155;
        }

        /* Typography */
        .form-section-title {
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
            margin-bottom: 1.25rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #f1f5f9;
            margin-top: 2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .dark .form-section-title {
            border-color: #334155;
            color: #64748b;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.875rem;
            color: #334155;
            margin-bottom: 0.5rem;
            display: block;
        }

        .dark .form-label {
            color: #e2e8f0;
        }

        /* Stats Card Professional */
        .stat-card-pro {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            padding: 1rem;
            text-align: center;
        }

        .dark .stat-card-pro {
            background: #1e293b;
            border-color: #334155;
        }

        .stat-card-pro .label {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            margin-bottom: 0.25rem;
            display: block;
        }

        .stat-card-pro .value {
            font-family: 'Inter', sans-serif;
            font-weight: 900;
            font-size: 1.5rem;
        }
    </style>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">Detail Laporan</h1>
                    @if ($report->status === 'verified')
                        <span
                            class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                            <i class="fas fa-lock mr-1"></i> Final
                        </span>
                    @else
                        <span
                            class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-600">
                            <i class="fas fa-pen mr-1"></i> Draft
                        </span>
                    @endif
                </div>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                    Kode Laporan: <span
                        class="font-mono font-bold text-slate-700 dark:text-slate-300">{{ $report->report_code }}</span>
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('daily-reports.index') }}"
                    class="inline-flex items-center px-5 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-sm">
                    <i class="fas fa-arrow-left mr-2 text-slate-400"></i> Kembali
                </a>
                @if ($report->status !== 'verified')
                    <a href="{{ route('daily-reports.edit', $report->id) }}"
                        class="inline-flex items-center px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white border border-amber-600 rounded-xl text-sm font-bold transition-all shadow-lg shadow-amber-500/30">
                        <i class="fas fa-edit mr-2"></i> Edit Laporan
                    </a>
                @endif
            </div>
        </div>

        {{-- CONTENT CARD --}}
        <div
            class="bg-white dark:bg-slate-800 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-700 p-6 md:p-10">

            {{-- SECTION 1: INFORMASI DASAR --}}
            <div class="form-section-title mt-0">
                <i class="fas fa-info-circle text-indigo-500"></i> 1. Informasi Dasar
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <label class="form-label">Tanggal Produksi</label>
                    <input type="text" class="saas-input" readonly
                        value="{{ \Carbon\Carbon::parse($report->production_date)->format('d F Y') }}">
                </div>
                <div>
                    <label class="form-label">Shift Kerja</label>
                    <input type="text" class="saas-input" readonly value="{{ $report->shift->name ?? '-' }}">
                </div>
                <div>
                    <label class="form-label">Koordinator</label>
                    <input type="text" class="saas-input" readonly value="{{ $report->coordinator->name ?? '-' }}">
                </div>
                <div>
                    <label class="form-label">Operator</label>
                    <input type="text" class="saas-input" readonly value="{{ $report->operator->name ?? '-' }}">
                </div>
            </div>

            {{-- SECTION 2: BATCH & MESIN --}}
            <div class="form-section-title">
                <i class="fas fa-layer-group text-blue-500"></i> 2. Batch & Mesin
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                {{-- Batch Info --}}
                <div class="md:col-span-1">
                    <label class="form-label">Nomor Batch (SPK)</label>
                    <input type="text" class="saas-input font-bold text-slate-800 dark:text-white" readonly
                        value="{{ $report->batch->batch_code ?? '-' }}">
                </div>

                {{-- Info Card --}}
                <div
                    class="md:col-span-2 bg-slate-50 dark:bg-slate-700/30 p-6 rounded-2xl border border-slate-200 dark:border-slate-600">
                    <div class="mb-5 border-b border-slate-200 dark:border-slate-600 pb-3">
                        <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest block mb-1">Nama
                            Produk</span>
                        <div class="text-lg font-black text-slate-800 dark:text-white leading-tight">
                            {{ $report->product->name ?? '-' }}
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-6 mb-5">
                        <div>
                            <span
                                class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Warna</span>
                            <div class="text-sm font-bold text-slate-700 dark:text-slate-200">
                                {{ $report->color->name ?? '-' }}
                            </div>
                        </div>
                        <div>
                            <span
                                class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Prioritas</span>
                            <div class="text-sm font-bold text-indigo-600 dark:text-indigo-400 uppercase">
                                {{ $report->batch->priority ?? '-' }}
                            </div>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Target
                                Batch</span>
                            <div class="text-sm font-black text-slate-800 dark:text-white">
                                {{ isset($report->batch) ? number_format($report->batch->current_quantity ?? 0, 0, ',', '.') . ' / ' . number_format($report->batch->target_quantity ?? 0, 0, ',', '.') : '-' }}
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Mesin
                            Produksi</label>
                        <input type="text" class="saas-input h-9 text-xs font-bold text-slate-700 dark:text-slate-300"
                            readonly value="{{ $report->machine->name ?? '-' }}">
                    </div>
                </div>
            </div>

            {{-- SECTION 3: PARAMETER WAKTU --}}
            <div class="form-section-title">
                <i class="fas fa-clock text-amber-500"></i> 3. Parameter Waktu
            </div>

            <div class="bg-slate-50 dark:bg-slate-700/30 p-5 rounded-2xl border border-slate-200 dark:border-slate-700">
                <div class="grid grid-cols-2 md:grid-cols-6 gap-5 mb-4">
                    <div>
                        <label class="form-label text-xs">Mulai</label>
                        <input type="text" class="saas-input text-center font-bold bg-white dark:bg-slate-800" readonly
                            value="{{ \Carbon\Carbon::parse($report->start_time)->format('H:i') }}">
                    </div>
                    <div>
                        <label class="form-label text-xs">Selesai</label>
                        <input type="text" class="saas-input text-center font-bold bg-white dark:bg-slate-800" readonly
                            value="{{ \Carbon\Carbon::parse($report->end_time)->format('H:i') }}">
                    </div>

                    @php
                        // ==========================================
                        // PENYELARASAN RUMUS DENGAN FORM.BLADE.PHP
                        // ==========================================

                        // 1. Ambil nilai mentah dari pivot untuk menjamin kesamaan dengan form.blade.php
                        $pivot = \Illuminate\Support\Facades\DB::table('machine_product')
                            ->where('product_id', $report->product_id)
                            ->where('machine_id', $report->machine_id)
                            ->first();

                        $ctStd = $pivot ? $pivot->cycle_time : $report->cycle_time ?? 0;
                        $cavStd = $pivot ? $pivot->cavity : $report->cavity ?? 1;
                        $ctAct = $report->actual_cycle_time ?? 0;
                        $cavAct = $report->actual_cavity ?? 0;

                        // 2. Tentukan nilai mana yang dipakai untuk kalkulasi (Prioritaskan Standar)
                        $ctCalc = $ctStd > 0 ? $ctStd : ($ctAct > 0 ? $ctAct : 0);
                        $cavCalc = $cavStd > 0 ? $cavStd : ($cavAct > 0 ? $cavAct : 0);

                        // 3. Ambil parameter waktu dan reject
                        $tm = $report->total_minutes ?? 0;
                        $td = $report->downtime_total ?? 0;
                        $netTime = $tm - $td; // Waktu operasional bersih (Shift - Downtime)

                        // 4. Hitung Target Shift (Qty Theory)
                        $liveQtyTheory = 0;
                        if ($ctCalc > 0 && $netTime > 0) {
                            $totalSeconds = $netTime * 60;
                            $liveQtyTheory = floor(($totalSeconds / $ctCalc) * $cavCalc);
                        }

                        // 5. Hitung Efisiensi
                        $qtyGood = $report->qty_good ?? 0;
                        $totalReject = $report->qty_reject_total ?? 0;
                        $totalOutput = $qtyGood + $totalReject; // Qty Good + Reject

                        $liveEfficiency = 0;
                        if ($liveQtyTheory > 0) {
                            $liveEfficiency = ($qtyGood / $liveQtyTheory) * 100;
                        }

                        // Styling Logic untuk Cycle & Cavity (Hanya untuk UI)
                        $ctActStyle = 'text-amber-600 dark:text-amber-400';
                        if ($ctStd > 0) {
                            if ($ctAct > $ctStd) {
                                $ctActStyle =
                                    'text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-900/10 border-rose-200';
                            } elseif ($ctAct > 0 && $ctAct < $ctStd) {
                                $ctActStyle =
                                    'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/10 border-emerald-200';
                            }
                        }

                        $cavActStyle = 'text-amber-600 dark:text-amber-400';
                        if ($cavStd > 0) {
                            if ($cavAct < $cavStd) {
                                $cavActStyle =
                                    'text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-900/10 border-rose-200';
                            } elseif ($cavAct == $cavStd) {
                                $cavActStyle =
                                    'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/10 border-emerald-200';
                            }
                        }
                    @endphp

                    <div>
                        <label class="form-label text-xs">Cycle Std</label>
                        <input type="text" class="saas-input text-center bg-slate-200 dark:bg-slate-600" readonly
                            value="{{ number_format($ctStd, 2, ',', '.') }}">
                    </div>
                    <div>
                        <label class="form-label text-xs text-amber-600 dark:text-amber-400">Cycle Act</label>
                        <input type="text" class="saas-input text-center font-bold {{ $ctActStyle }}" readonly
                            value="{{ number_format($ctAct, 2, ',', '.') }}">
                    </div>
                    <div>
                        <label class="form-label text-xs">Cav Std</label>
                        <input type="text" class="saas-input text-center bg-slate-200 dark:bg-slate-600" readonly
                            value="{{ $cavStd }}">
                    </div>
                    <div>
                        <label class="form-label text-xs text-amber-600 dark:text-amber-400">Cav Act</label>
                        <input type="text" class="saas-input text-center font-bold {{ $cavActStyle }}" readonly
                            value="{{ $cavAct }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t border-slate-200 dark:border-slate-600 pt-4">
                    <div
                        class="flex items-center justify-between bg-blue-50 dark:bg-blue-900/20 px-4 py-2 rounded-xl border border-blue-100 dark:border-blue-800">
                        <span class="text-xs font-bold text-blue-700 dark:text-blue-300 uppercase tracking-wide">Total
                            Durasi</span>
                        <div class="flex items-center gap-1">
                            <span class="text-lg font-black text-blue-700 dark:text-blue-300">{{ $tm }}</span>
                            <span class="text-[10px] font-bold text-blue-500 uppercase">Min</span>
                        </div>
                    </div>
                    <div
                        class="flex items-center justify-between bg-indigo-50 dark:bg-indigo-900/20 px-4 py-2 rounded-xl border border-indigo-100 dark:border-indigo-800">
                        <span class="text-xs font-bold text-indigo-700 dark:text-indigo-300 uppercase tracking-wide">Target
                            Shift (Theory)</span>
                        <div class="flex items-center gap-1">
                            {{-- MENAMPILKAN NILAI YANG DIHITUNG ULANG --}}
                            <span
                                class="text-lg font-black text-indigo-700 dark:text-indigo-300">{{ number_format($liveQtyTheory, 0, ',', '.') }}</span>
                            <span class="text-[10px] font-bold text-indigo-500 uppercase">Pcs</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION 4: OUTPUT PRODUKSI --}}
            <div class="form-section-title">
                <i class="fas fa-industry text-emerald-500"></i> 4. Output Produksi
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
                <div class="col-span-2 md:col-span-1">
                    <label class="form-label text-emerald-600 dark:text-emerald-400">Good / FG (Pcs)</label>
                    <input type="text"
                        class="saas-input h-14 text-center text-2xl font-black text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/10 border-emerald-200"
                        readonly value="{{ number_format($qtyGood, 0, ',', '.') }}">
                </div>
                <div>
                    <label class="form-label text-rose-600 dark:text-rose-400">Total Reject</label>
                    <input type="text"
                        class="saas-input h-14 text-center text-xl font-bold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-900/10 border-rose-200"
                        readonly value="{{ number_format($totalReject, 0, ',', '.') }}">
                </div>
                <div>
                    <label class="form-label text-blue-600 dark:text-blue-400">Sample QC</label>
                    <input type="text"
                        class="saas-input h-14 text-center text-xl font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/10 border-blue-200"
                        readonly value="{{ number_format($report->qty_sample, 0, ',', '.') }}">
                </div>

                {{-- STATISTIK REALTIME --}}
                <div class="col-span-2 md:col-span-4 grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                    <div class="stat-card-pro border-l-4 border-l-slate-800 dark:border-l-slate-500">
                        <span class="label">Total Output</span>
                        <div class="value text-slate-800 dark:text-white">
                            {{ number_format($totalOutput, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="stat-card-pro border-l-4 border-l-emerald-500">
                        <span class="label text-emerald-600">Yield</span>
                        <div class="value text-emerald-600">
                            {{ number_format($totalOutput > 0 ? ($qtyGood / $totalOutput) * 100 : 0, 2, ',', '.') }}%
                        </div>
                    </div>
                    <div class="stat-card-pro border-l-4 border-l-blue-500">
                        <span class="label text-blue-600">Efisiensi</span>
                        <div class="value text-blue-600">
                            {{ number_format($liveEfficiency, 2, ',', '.') }}%
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION 5: MATERIAL & WASTE --}}
            <div class="form-section-title">
                <i class="fas fa-recycle text-orange-500"></i> 5. Material & Waste (Purging)
            </div>

            <div
                class="p-5 bg-orange-50 dark:bg-orange-900/10 rounded-2xl border border-orange-200 dark:border-orange-800 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <label class="text-xs font-bold text-slate-600 dark:text-slate-400 block mb-1">Runner (Kg)</label>
                        <input type="text"
                            class="saas-input h-10 text-center font-bold text-sm bg-white dark:bg-slate-800" readonly
                            value="{{ number_format($report->total_runner, 2, ',', '.') }}">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-orange-800 dark:text-orange-400 block mb-1">Berat Purging
                            (Gram)</label>
                        <input type="text" class="saas-input h-10 text-center text-sm bg-white dark:bg-slate-800"
                            readonly value="{{ number_format($report->purging_kg, 2, ',', '.') }}">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-orange-800 dark:text-orange-400 block mb-1">Berat
                            Std/Pcs</label>
                        <input type="text"
                            class="saas-input h-10 text-center bg-orange-100 dark:bg-orange-900/30 text-sm" readonly
                            value="{{ number_format($report->weight_per_pcs, 2, ',', '.') }}">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-orange-800 dark:text-orange-400 block mb-1">Konversi
                            Pcs</label>
                        <input type="text"
                            class="saas-input h-10 text-center bg-orange-100 dark:bg-orange-900/30 font-bold text-sm"
                            readonly value="{{ number_format($report->qty_purging, 0, ',', '.') }}">
                    </div>
                </div>
            </div>

            {{-- SECTION 6: DETAIL LOSSES --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                {{-- Reject List --}}
                <div
                    class="border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden flex flex-col h-96 bg-white dark:bg-slate-800">
                    <div
                        class="bg-rose-50 dark:bg-rose-900/20 px-6 py-3 border-b border-rose-100 dark:border-rose-800 font-bold text-rose-700 dark:text-rose-400 text-sm flex items-center gap-2">
                        <i class="fas fa-bug"></i> Detail Reject
                    </div>
                    <div class="p-5 overflow-y-auto flex-1 custom-scroll">
                        @forelse ($report->rejects as $reject)
                            <div
                                class="flex justify-between items-center py-3 border-b border-slate-50 dark:border-slate-700 last:border-0">
                                <label class="text-xs font-bold text-slate-700 dark:text-slate-300 w-2/3 truncate">
                                    {{ $reject->rejectItem->name }}
                                    <span
                                        class="text-[10px] text-slate-400 block font-normal">{{ $reject->rejectItem->category->name }}</span>
                                </label>
                                <span
                                    class="text-sm font-bold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-900/20 px-3 py-1 rounded-lg">
                                    {{ number_format($reject->qty, 0, ',', '.') }} Pcs
                                </span>
                            </div>
                        @empty
                            <div class="flex flex-col items-center justify-center h-full text-slate-400 text-xs italic">
                                <i class="fas fa-check-circle text-2xl mb-2 text-slate-300"></i>
                                Tidak ada reject yang tercatat.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Downtime List --}}
                <div
                    class="border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden flex flex-col h-96 bg-white dark:bg-slate-800">
                    <div
                        class="bg-amber-50 dark:bg-amber-900/20 px-6 py-3 border-b border-amber-100 dark:border-amber-800 font-bold text-amber-700 dark:text-amber-400 text-sm flex justify-between items-center">
                        <span class="flex items-center gap-2"><i class="fas fa-stopwatch"></i> Detail Downtime</span>
                        <span
                            class="bg-white dark:bg-slate-900 px-3 py-1 rounded-full text-xs border border-amber-200 dark:border-amber-800 shadow-sm">
                            {{ number_format($td, 0, ',', '.') }} Min
                        </span>
                    </div>
                    <div class="p-5 overflow-y-auto flex-1 space-y-4 custom-scroll">
                        @forelse ($report->downtimes as $dt)
                            <div
                                class="bg-slate-5 dark:bg-slate-700/30 p-3 rounded-xl border border-slate-100 dark:border-slate-700">
                                <div class="flex justify-between items-center mb-2">
                                    <span
                                        class="text-xs font-bold text-slate-700 dark:text-slate-300 w-2/3 truncate">{{ $dt->downtime->name }}</span>
                                    <span
                                        class="text-xs font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 px-2 py-1 rounded">
                                        {{ number_format($dt->duration, 0, ',', '.') }} Min
                                    </span>
                                </div>
                                @if ($dt->remarks)
                                    <div
                                        class="text-[11px] text-slate-500 dark:text-slate-400 italic bg-white dark:bg-slate-800 p-2 rounded border border-dashed border-slate-200 dark:border-slate-600">
                                        "{{ $dt->remarks }}"
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="flex flex-col items-center justify-center h-full text-slate-400 text-xs italic">
                                <i class="fas fa-clock text-2xl mb-2 text-slate-300"></i>
                                Tidak ada downtime yang tercatat.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- SECTION 7: WIP & KEMASAN --}}
            <div class="form-section-title">
                <i class="fas fa-boxes-stacked text-purple-500"></i> 7. Counter, WIP & Kemasan
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                {{-- WIP --}}
                <div
                    class="p-5 bg-slate-50 dark:bg-slate-700/30 rounded-2xl border border-slate-200 dark:border-slate-700">
                    <h5 class="text-xs font-bold text-slate-500 uppercase mb-4 tracking-wider">Counter & WIP</h5>
                    <div class="space-y-4">
                        <div class="grid grid-cols-3 gap-4 items-center">
                            <label class="text-xs text-slate-600 dark:text-slate-400 font-semibold col-span-1">WIP
                                Awal</label>
                            <div class="col-span-2">
                                <input type="text"
                                    class="saas-input h-9 text-center bg-slate-200 dark:bg-slate-600 font-mono text-sm"
                                    readonly value="{{ number_format($report->wip_previous ?? 0, 0, ',', '.') }}">
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-4 items-center">
                            <label class="text-xs text-slate-600 dark:text-slate-400 font-semibold col-span-1">WIP
                                Akhir</label>
                            <div class="col-span-2">
                                <input type="text"
                                    class="saas-input h-9 text-center font-bold bg-white dark:bg-slate-800 text-sm"
                                    readonly value="{{ number_format($report->wip ?? 0, 0, ',', '.') }}">
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-4 items-center">
                            <label class="text-xs text-slate-600 dark:text-slate-400 font-semibold col-span-1">Counter
                                Mesin</label>
                            <div class="col-span-2">
                                <input type="text"
                                    class="saas-input h-9 text-center font-bold bg-white dark:bg-slate-800 text-sm"
                                    readonly value="{{ number_format($report->total_counter ?? 0, 0, ',', '.') }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KEMASAN --}}
                <div
                    class="p-5 bg-slate-50 dark:bg-slate-700/30 rounded-2xl border border-slate-200 dark:border-slate-700">
                    <h5 class="text-xs font-bold text-slate-500 uppercase mb-4 tracking-wider">Informasi Kemasan</h5>
                    <div class="space-y-4">
                        <div>
                            <label class="text-xs block text-slate-500 mb-1 font-semibold">Jenis Kemasan</label>
                            <input type="text"
                                class="saas-input h-9 bg-slate-200 dark:bg-slate-600 text-xs font-bold text-slate-700 dark:text-slate-300"
                                readonly value="{{ $report->packagingType->name ?? '-' }}">
                        </div>
                        <div>
                            <label class="text-xs block text-slate-500 mb-1 font-semibold">Hasil Packing</label>
                            <input type="text"
                                class="saas-input h-9 bg-white dark:bg-slate-800 text-xs font-bold text-left pl-3 text-indigo-600 dark:text-indigo-400 border-indigo-200"
                                readonly
                                value="{{ $report->packaging_qty > 0 ? number_format($report->packaging_qty, 0, ',', '.') . ' Box' : '0' }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- NOTES --}}
            <div class="mb-8">
                <label class="form-label">Catatan Tambahan</label>
                <textarea rows="8" class="saas-input h-auto py-3 resize-none bg-slate-50 dark:bg-slate-700/30" readonly>{{ $report->notes ?? '-' }}</textarea>
            </div>

        </div>
    </div>
@endsection
