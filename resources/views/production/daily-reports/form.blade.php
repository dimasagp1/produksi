@extends('layouts.app')

@section('title', isset($report) ? 'Edit Laporan Produksi' : 'Input Laporan Baru')

@section('content')
    {{-- LIBRARIES --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    {{-- Flatpickr --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://npmcdn.com/flatpickr/dist/themes/dark.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    {{-- Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    {{-- DATA MAPPING --}}
    <script>
        const shiftTimes = {
            1: {
                start: '07:00',
                end: '15:00'
            },
            2: {
                start: '15:00',
                end: '23:00'
            },
            3: {
                start: '23:00',
                end: '07:00'
            }
        };
        const machineRejectsMap = @json($machineRejects ?? []);
    </script>

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

        /* FIX CSS TEXTAREA */
        textarea.saas-input {
            height: auto !important;
            min-height: 150px;
            line-height: 1.5;
            padding-top: 12px;
            padding-bottom: 12px;
        }

        .dark .saas-input {
            background-color: #1e293b;
            border-color: #334155;
            color: #f1f5f9;
        }

        .saas-input:focus {
            border-color: #6366f1;
            outline: none;
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .dark .saas-input:focus {
            background-color: #0f172a;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2);
        }

        .saas-input[readonly],
        .saas-input:disabled {
            background-color: #f1f5f9;
            cursor: not-allowed;
            color: #64748b;
        }

        .dark .saas-input[readonly],
        .dark .saas-input:disabled {
            background-color: #334155;
            color: #94a3b8;
        }

        /* Input with Icon Fix */
        .input-icon-wrapper {
            position: relative;
            width: 100%;
        }

        .input-icon-wrapper i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            pointer-events: none;
            z-index: 10;
        }

        .input-icon-wrapper .saas-input {
            padding-left: 44px !important;
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

        /* Select2 Override */
        .select2-container .select2-selection--single {
            height: 48px !important;
            border-radius: 0.75rem !important;
            border: 1px solid #e2e8f0 !important;
            background-color: #f8fafc !important;
            display: flex !important;
            align-items: center !important;
        }

        .dark .select2-container .select2-selection--single {
            background-color: #1e293b !important;
            border-color: #334155 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding-left: 16px !important;
            color: #1e293b !important;
            font-size: 0.875rem;
        }

        .dark .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #f1f5f9 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 46px !important;
            right: 12px !important;
        }

        .select2-dropdown {
            background-color: #fff !important;
            border-color: #e2e8f0 !important;
        }

        .dark .select2-dropdown {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            color: #f1f5f9 !important;
        }

        .dark .select2-results__option--highlighted[aria-selected] {
            background-color: #6366f1 !important;
        }

        .dark .select2-search__field {
            background-color: #0f172a !important;
            color: #fff !important;
        }

        /* Stats Card Professional */
        .stat-card-pro {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            padding: 1rem;
            text-align: center;
            transition: all 0.3s ease;
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

        .stat-card-pro input {
            font-family: 'Inter', sans-serif;
            border: none;
            background: transparent;
            text-align: center;
            width: 100%;
            font-weight: 900;
            font-size: 1.5rem;
            padding: 0;
            margin: 0;
        }

        .stat-card-pro input:focus {
            outline: none;
        }
    </style>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">
                    {{ isset($report) ? 'Edit Laporan' : 'Input Laporan Baru' }}
                </h1>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                    {{ isset($report) ? 'Kode Laporan: ' . $report->report_code : 'Silakan lengkapi data produksi harian di bawah ini.' }}
                </p>
            </div>
            <a href="{{ route('daily-reports.index') }}"
                class="inline-flex items-center px-5 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-sm">
                <i class="fas fa-arrow-left mr-2 text-slate-400"></i> Kembali
            </a>
        </div>

        {{-- FORM CARD --}}
        <div
            class="bg-white dark:bg-slate-800 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-700 p-6 md:p-10">

            <form action="{{ isset($report) ? route('daily-reports.update', $report->id) : route('daily-reports.store') }}"
                method="POST" id="reportForm">
                @csrf
                @if (isset($report))
                    @method('PUT')
                @endif

                {{-- SECTION 1: INFORMASI DASAR --}}
                <div class="form-section-title mt-0">
                    <i class="fas fa-info-circle text-indigo-500"></i> 1. Informasi Dasar
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <label class="form-label">Tanggal Produksi <span class="text-rose-500">*</span></label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-calendar-day"></i>
                            <input type="text" name="production_date" id="production_date" class="saas-input" required
                                value="{{ old('production_date', $report->production_date ?? date('Y-m-d')) }}">
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Shift Kerja <span class="text-rose-500">*</span></label>
                        <select name="shift_id" id="shift_id" class="saas-input select2-init" required
                            data-placeholder="Pilih Shift Kerja" onchange="window.setShiftTime(this.value)">
                            <option value=""></option>
                            @foreach ($shifts as $s)
                                <option value="{{ $s->id }}"
                                    {{ old('shift_id', $report->shift_id ?? '') == $s->id ? 'selected' : '' }}>
                                    {{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Koordinator <span class="text-rose-500">*</span></label>
                        <select name="coordinator_id" id="coordinator_id" class="saas-input select2-init" required
                            data-placeholder="Pilih Nama Koordinator">
                            <option value=""></option>
                            @foreach ($coordinators as $c)
                                <option value="{{ $c->id }}"
                                    {{ old('coordinator_id', $report->coordinator_id ?? '') == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Operator <span class="text-rose-500">*</span></label>
                        <select name="operator_id" id="operator_id" class="saas-input select2-init" required
                            data-placeholder="Pilih Nama Operator">
                            <option value=""></option>
                            @foreach ($operators as $o)
                                <option value="{{ $o->id }}"
                                    {{ old('operator_id', $report->operator_id ?? '') == $o->id ? 'selected' : '' }}>
                                    {{ $o->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- SECTION 2: BATCH & MESIN --}}
                <div class="form-section-title">
                    <i class="fas fa-layer-group text-blue-500"></i> 2. Batch & Mesin
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    {{-- Input Batch --}}
                    <div class="md:col-span-1">
                        <label class="form-label">Nomor Batch (SPK) <span class="text-rose-500">*</span></label>
                        <select name="batch_id" id="batch_id" class="saas-input select2-init" required
                            data-placeholder="Cari Nomor Batch / SPK...">
                            <option value=""></option>

                            {{-- PERBAIKAN: Selalu tampilkan Batch saat ini jika sedang Edit --}}
                            @if (isset($report) && $report->batch)
                                <option value="{{ $report->batch_id }}" selected>
                                    {{ $report->batch->batch_code }} - {{ $report->product->name ?? 'Produk Dihapus' }}
                                </option>
                            @endif

                            @if (isset($batches))
                                @foreach ($batches as $b)
                                    {{-- Cegah Duplikasi jika Batch sudah di-inject di atas --}}
                                    @if (!isset($report) || $report->batch_id != $b->id)
                                        <option value="{{ $b->id }}"
                                            {{ old('batch_id') == $b->id ? 'selected' : '' }}>
                                            {{ $b->batch_code }} - {{ $b->product->name }}
                                        </option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                        <input type="hidden" name="product_id" id="product_id"
                            value="{{ old('product_id', $report->product_id ?? '') }}">
                        <input type="hidden" name="color_id" id="color_id"
                            value="{{ old('color_id', $report->color_id ?? '') }}">
                    </div>

                    {{-- Info Card --}}
                    <div
                        class="md:col-span-2 bg-slate-50 dark:bg-slate-700/30 p-6 rounded-2xl border border-slate-200 dark:border-slate-600">
                        <div class="mb-5 border-b border-slate-200 dark:border-slate-600 pb-3">
                            <span
                                class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest block mb-1">Nama
                                Produk</span>
                            <div class="text-lg font-black text-slate-800 dark:text-white leading-tight"
                                id="display_product">
                                {{ $report->product->name ?? '-' }}
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-6 mb-5">
                            <div>
                                <span
                                    class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Warna</span>
                                <div class="text-sm font-bold text-slate-700 dark:text-slate-200" id="display_color">
                                    {{ $report->color->name ?? '-' }}
                                </div>
                            </div>
                            <div>
                                <span
                                    class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Prioritas</span>
                                <div class="text-sm font-bold text-indigo-600 dark:text-indigo-400 uppercase"
                                    id="display_priority">
                                    {{ $report->batch->priority ?? '-' }}
                                </div>
                            </div>
                            <div>
                                <span
                                    class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Target
                                    Batch</span>
                                <div class="text-sm font-black text-slate-800 dark:text-white" id="display_target">
                                    {{ isset($report) ? number_format($report->batch->current_quantity ?? 0, 0, ',', '.') . ' / ' . number_format($report->batch->target_quantity ?? 0, 0, ',', '.') : '-' }}
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Mesin
                                Produksi <span class="text-rose-500">*</span></label>
                            <select name="machine_id" id="machine_id" class="saas-input h-9 text-xs select2-init" required
                                data-placeholder="Pilih Mesin..." onchange="window.filterRejects(this.value)">
                                <option value=""></option>
                                @foreach ($machines as $m)
                                    <option value="{{ $m->id }}"
                                        {{ old('machine_id', $report->machine_id ?? '') == $m->id ? 'selected' : '' }}>
                                        {{ $m->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- SECTION 3: PARAMETER WAKTU --}}
                <div class="form-section-title">
                    <i class="fas fa-clock text-amber-500"></i> 3. Parameter Waktu
                </div>

                <div
                    class="bg-slate-50 dark:bg-slate-700/30 p-5 rounded-2xl border border-slate-200 dark:border-slate-700">
                    <div class="grid grid-cols-2 md:grid-cols-6 gap-5 mb-4">
                        <div>
                            <label class="form-label text-xs">Mulai <span class="text-rose-500">*</span></label>
                            <input type="text" name="start_time" id="start_time"
                                class="saas-input text-center time-picker bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-600 font-bold"
                                readonly required placeholder="--:--"
                                value="{{ old('start_time', isset($report) ? \Carbon\Carbon::parse($report->start_time)->format('H:i') : '') }}">
                        </div>
                        <div>
                            <label class="form-label text-xs">Selesai <span class="text-rose-500">*</span></label>
                            <input type="text" name="end_time" id="end_time"
                                class="saas-input text-center time-picker bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-600 font-bold"
                                readonly required placeholder="--:--"
                                value="{{ old('end_time', isset($report) ? \Carbon\Carbon::parse($report->end_time)->format('H:i') : '') }}">
                        </div>

                        @php
                            $ctStdDisplay = 0;
                            $cavStdDisplay = 1;

                            if (isset($report)) {
                                $pivot = \Illuminate\Support\Facades\DB::table('machine_product')
                                    ->where('product_id', $report->product_id)
                                    ->where('machine_id', $report->machine_id)
                                    ->first();
                                $ctStdDisplay = $pivot ? $pivot->cycle_time : $report->cycle_time;
                                $cavStdDisplay = $pivot ? $pivot->cavity : $report->cavity;
                            }
                        @endphp

                        <div>
                            <label class="form-label text-xs">Cycle Std</label>
                            <input type="text" name="cycle_time" id="cycle_time"
                                class="saas-input text-center bg-slate-200 dark:bg-slate-600 text-slate-500" readonly
                                value="{{ number_format($ctStdDisplay, 2, ',', '.') }}">
                        </div>
                        <div>
                            <label class="form-label text-xs text-amber-600 dark:text-amber-400">Cycle Act <span
                                    class="text-rose-500">*</span></label>
                            <input type="text" name="actual_cycle_time" id="actual_cycle_time"
                                class="saas-input text-center font-bold text-amber-600 dark:text-amber-400 border-amber-200 calc-trigger format-decimal"
                                required placeholder="0"
                                value="{{ old('actual_cycle_time', isset($report) && $report->actual_cycle_time > 0 ? str_replace('.', ',', (float) $report->actual_cycle_time) : '') }}">
                        </div>
                        <div>
                            <label class="form-label text-xs">Cav Std</label>
                            <input type="text" name="cavity" id="cavity"
                                class="saas-input text-center bg-slate-200 dark:bg-slate-600 text-slate-500" readonly
                                value="{{ $cavStdDisplay }}">
                        </div>
                        <div>
                            <label class="form-label text-xs text-amber-600 dark:text-amber-400">Cav Act <span
                                    class="text-rose-500">*</span></label>
                            <input type="text" name="actual_cavity" id="actual_cavity"
                                class="saas-input text-center font-bold text-amber-600 dark:text-amber-400 border-amber-200 calc-trigger format-number"
                                required placeholder="0"
                                value="{{ old('actual_cavity', isset($report) && $report->actual_cavity > 0 ? number_format($report->actual_cavity, 0, ',', '.') : '') }}">
                        </div>
                    </div>

                    {{-- Info Bar: Durasi & Qty Theory --}}
                    <div
                        class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t border-slate-200 dark:border-slate-600 pt-4">
                        <div
                            class="flex items-center justify-between bg-blue-50 dark:bg-blue-900/20 px-4 py-2 rounded-xl border border-blue-100 dark:border-blue-800">
                            <span class="text-xs font-bold text-blue-700 dark:text-blue-300 uppercase tracking-wide">Total
                                Durasi</span>
                            <div class="flex items-center gap-1">
                                <input type="text" id="display_total_minutes"
                                    class="bg-transparent text-right font-black text-blue-700 dark:text-blue-300 w-16 focus:outline-none cursor-default"
                                    readonly value="0">
                                <span class="text-[10px] font-bold text-blue-500 uppercase">Min</span>
                                <input type="hidden" name="total_minutes" id="total_minutes"
                                    value="{{ old('total_minutes', $report->total_minutes ?? 0) }}">
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between bg-indigo-50 dark:bg-indigo-900/20 px-4 py-2 rounded-xl border border-indigo-100 dark:border-indigo-800">
                            <span
                                class="text-xs font-bold text-indigo-700 dark:text-indigo-300 uppercase tracking-wide">Target
                                Shift (Theory)</span>
                            <div class="flex items-center gap-1">
                                <input type="text" name="qty_theory" id="qty_theory"
                                    class="bg-transparent text-right font-black text-indigo-700 dark:text-indigo-300 w-24 focus:outline-none cursor-default text-lg"
                                    readonly value="{{ old('qty_theory', $report->qty_theory ?? 0) }}">
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
                        <label class="form-label text-emerald-600 dark:text-emerald-400">Good / FG (Pcs) <span
                                class="text-rose-500">*</span></label>
                        <input type="text" name="qty_good" id="qty_good"
                            class="saas-input h-14 text-center text-2xl font-black text-emerald-600 dark:text-emerald-400 border-emerald-200 bg-emerald-50 dark:bg-emerald-900/10 focus:border-emerald-500 focus:ring-emerald-200 calc-trigger format-number"
                            required placeholder="0"
                            value="{{ old('qty_good', isset($report) && $report->qty_good > 0 ? number_format($report->qty_good, 0, ',', '.') : '') }}">
                    </div>
                    <div>
                        <label class="form-label text-rose-600 dark:text-rose-400">Total Reject</label>
                        <input type="text" name="qty_reject_total" id="qty_reject_total"
                            class="saas-input h-14 text-center text-xl font-bold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-900/10 border-rose-200 cursor-default"
                            readonly value="{{ old('qty_reject_total', $report->qty_reject_total ?? 0) }}">
                    </div>
                    <div>
                        <label class="form-label text-blue-600 dark:text-blue-400">Sample QC <span
                                class="text-slate-400 font-normal text-xs">(Opsional)</span></label>
                        <input type="text" name="qty_sample" id="qty_sample"
                            class="saas-input h-14 text-center text-xl font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/10 border-blue-200 calc-trigger format-number"
                            placeholder="0"
                            value="{{ old('qty_sample', isset($report) && $report->qty_sample > 0 ? number_format($report->qty_sample, 0, ',', '.') : '') }}">
                    </div>

                    {{-- STATISTIK REALTIME PROFESIONAL --}}
                    <div class="col-span-2 md:col-span-4 grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                        <div class="stat-card-pro border-l-4 border-l-slate-800 dark:border-l-slate-500">
                            <span class="label">Total Output</span>
                            <input type="text" name="total_output" id="total_output"
                                class="text-slate-800 dark:text-white" readonly
                                value="{{ old('total_output', $report->total_output ?? 0) }}">
                        </div>
                        <div class="stat-card-pro border-l-4 border-l-emerald-500">
                            <span class="label text-emerald-600">Yield</span>
                            <input type="text" name="yield" id="yield" class="text-emerald-600" readonly
                                value="{{ old('yield', $report->yield ?? 0) }}%">
                        </div>
                        <div class="stat-card-pro border-l-4 border-l-blue-500">
                            <span class="label text-blue-600">Efisiensi</span>
                            <input type="text" name="efficiency" id="efficiency" class="text-blue-600" readonly
                                value="{{ old('efficiency', $report->efficiency ?? 0) }}%">
                        </div>
                    </div>
                </div>

                {{-- SECTION 5: MATERIAL & WASTE --}}
                <div class="form-section-title">
                    <i class="fas fa-recycle text-orange-500"></i> 5. Material & Waste (Purging)
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div>
                        <label class="form-label text-slate-600">Runner (Kg) <span
                                class="text-slate-400 font-normal text-xs">(Opsional)</span></label>
                        <input type="text" name="total_runner" id="total_runner"
                            class="saas-input h-10 text-center text-sm font-bold format-decimal bg-white dark:bg-slate-800"
                            placeholder="0"
                            value="{{ old('total_runner', isset($report) && $report->total_runner > 0 ? str_replace('.', ',', (float) $report->total_runner) : '') }}">
                    </div>
                    <div
                        class="md:col-span-3 p-4 bg-orange-50 dark:bg-orange-900/10 rounded-xl border border-orange-200 dark:border-orange-800">
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="text-xs font-bold text-orange-800 dark:text-orange-400 block mb-1">Berat
                                    Purging (Gram) <span
                                        class="text-slate-400 font-normal opacity-70">(Opsional)</span></label>
                                <input type="text" name="purging_kg" id="purging_kg"
                                    class="saas-input h-10 text-center format-decimal text-sm bg-white dark:bg-slate-800"
                                    placeholder="0"
                                    value="{{ old('purging_kg', isset($report) && $report->purging_kg > 0 ? str_replace('.', ',', (float) $report->purging_kg) : '') }}">
                            </div>
                            <div>
                                <label class="text-xs font-bold text-orange-800 dark:text-orange-400 block mb-1">Berat
                                    Std/Pcs</label>
                                <input type="text" name="weight_per_pcs" id="weight_per_pcs"
                                    class="saas-input h-10 text-center bg-orange-100 dark:bg-orange-900/30 text-sm"
                                    readonly value="{{ old('weight_per_pcs', $report->weight_per_pcs ?? 0) }}">
                            </div>
                            <div>
                                <label class="text-xs font-bold text-orange-800 dark:text-orange-400 block mb-1">Konversi
                                    Pcs</label>
                                <input type="text" name="qty_purging" id="qty_purging"
                                    class="saas-input h-10 text-center bg-orange-100 dark:bg-orange-900/30 font-bold text-sm"
                                    readonly value="{{ old('qty_purging', $report->qty_purging ?? 0) }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- DETAIL LOSSES (REJECT & DOWNTIME) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    {{-- Reject List --}}
                    <div
                        class="border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden flex flex-col h-96 bg-white dark:bg-slate-800">
                        <div
                            class="bg-rose-50 dark:bg-rose-900/20 px-6 py-3 border-b border-rose-100 dark:border-rose-800 font-bold text-rose-700 dark:text-rose-400 text-sm flex items-center gap-2">
                            <i class="fas fa-bug"></i> Detail Reject <span
                                class="text-xs font-normal text-rose-500 ml-auto">(Opsional)</span>
                        </div>
                        <div class="p-5 overflow-y-auto flex-1 custom-scroll" id="reject_list_container">
                            @foreach ($rejectItems as $idx => $reject)
                                @php
                                    $val = 0;
                                    if (isset($report) && $report->rejects) {
                                        $found = $report->rejects->firstWhere('reject_item_id', $reject->id);
                                        if ($found) {
                                            $val = $found->qty;
                                        }
                                    }
                                @endphp
                                <div class="flex justify-between items-center py-2 border-b border-slate-50 dark:border-slate-700 reject-item-row last:border-0"
                                    data-id="{{ $reject->id }}" data-index="{{ $idx }}">
                                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300 w-2/3 truncate"
                                        title="{{ $reject->name }}">
                                        {{ $reject->name }}
                                        <span
                                            class="text-[10px] text-slate-400 block font-normal">{{ $reject->category->name }}</span>
                                    </label>
                                    <div class="w-1/3 relative">
                                        <input type="text" name="rejects[{{ $idx }}][qty]"
                                            class="saas-input h-9 text-xs text-center reject-input format-number pr-8"
                                            placeholder="0"
                                            value="{{ $val > 0 ? number_format($val, 0, ',', '.') : '' }}">
                                        <span
                                            class="absolute right-3 top-2.5 text-[10px] text-slate-400 pointer-events-none">Pcs</span>
                                        <input type="hidden" name="rejects[{{ $idx }}][id]"
                                            value="{{ $reject->id }}">
                                    </div>
                                </div>
                            @endforeach
                            <div id="no_reject_msg"
                                class="hidden flex flex-col items-center justify-center h-full text-slate-400 text-xs italic">
                                <i class="fas fa-check-circle text-2xl mb-2 text-slate-300"></i>
                                Item reject tidak tersedia untuk mesin ini.
                            </div>
                        </div>
                    </div>

                    {{-- Downtime List --}}
                    <div
                        class="border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden flex flex-col h-96 bg-white dark:bg-slate-800">
                        <div
                            class="bg-amber-50 dark:bg-amber-900/20 px-6 py-3 border-b border-amber-100 dark:border-amber-800 font-bold text-amber-700 dark:text-amber-400 text-sm flex justify-between items-center">
                            <span class="flex items-center gap-2"><i class="fas fa-stopwatch"></i> Detail Downtime <span
                                    class="text-xs font-normal text-amber-600 ml-1">(Opsional)</span></span>
                            <span
                                class="bg-white dark:bg-slate-900 px-3 py-1 rounded-full text-xs border border-amber-200 dark:border-amber-800 shadow-sm"
                                id="display_downtime_total">0 Min</span>
                            <input type="hidden" name="downtime_total" id="downtime_total"
                                value="{{ old('downtime_total', $report->downtime_total ?? 0) }}">
                        </div>
                        <div class="p-5 overflow-y-auto flex-1 space-y-4 custom-scroll">
                            @foreach ($downtimes as $idx => $dt)
                                @php
                                    $dDur = '';
                                    $dRem = '';
                                    if (isset($report) && $report->downtimes) {
                                        $found = $report->downtimes->firstWhere('downtime_id', $dt->id);
                                        if ($found) {
                                            $dDur = $found->duration;
                                            $dRem = $found->remarks;
                                        }
                                    }
                                @endphp
                                <div
                                    class="bg-slate-5 dark:bg-slate-700/30 p-3 rounded-xl border border-slate-100 dark:border-slate-700">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300 w-2/3 truncate"
                                            title="{{ $dt->name }}">{{ $dt->name }}</span>
                                        <div class="w-1/3 relative">
                                            <input type="text" name="downtimes[{{ $idx }}][duration]"
                                                class="saas-input h-8 text-xs text-center downtime-input format-number pr-8 bg-white dark:bg-slate-800"
                                                placeholder="0"
                                                value="{{ $dDur > 0 ? number_format($dDur, 0, ',', '.') : '' }}">
                                            <span class="absolute right-2 top-2 text-[9px] text-slate-400">Min</span>
                                        </div>
                                    </div>
                                    <input type="text" name="downtimes[{{ $idx }}][remarks]"
                                        class="w-full text-[11px] bg-transparent border-b border-dashed border-slate-300 dark:border-slate-600 focus:border-indigo-500 outline-none px-1 py-1"
                                        placeholder="Tulis keterangan kendala disini..." value="{{ $dRem }}">
                                    <input type="hidden" name="downtimes[{{ $idx }}][id]"
                                        value="{{ $dt->id }}">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- SECTION 6: WIP & KEMASAN --}}
                <div class="form-section-title">
                    <i class="fas fa-boxes-stacked text-purple-500"></i> 6. Counter, WIP & Kemasan
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    {{-- WIP --}}
                    <div
                        class="p-5 bg-slate-50 dark:bg-slate-700/30 rounded-2xl border border-slate-200 dark:border-slate-700">
                        <h5 class="text-xs font-bold text-slate-500 uppercase mb-4 tracking-wider">Counter & WIP <span
                                class="text-slate-400 font-normal lowercase">(opsional)</span></h5>
                        <div class="space-y-4">
                            <div class="grid grid-cols-3 gap-4 items-center">
                                <label class="text-xs text-slate-600 dark:text-slate-400 font-semibold col-span-1">WIP
                                    Awal</label>
                                <div class="col-span-2">
                                    <input type="text" name="wip_previous" id="wip_previous"
                                        class="saas-input h-9 text-center bg-slate-200 dark:bg-slate-600 font-mono text-sm"
                                        readonly value="{{ number_format($report->wip_previous ?? 0, 0, ',', '.') }}">
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-4 items-center">
                                <label class="text-xs text-slate-600 dark:text-slate-400 font-semibold col-span-1">WIP
                                    Akhir</label>
                                <div class="col-span-2">
                                    <input type="text" name="wip" id="wip"
                                        class="saas-input h-9 text-center calc-trigger format-number text-sm font-bold"
                                        placeholder="0"
                                        value="{{ old('wip', isset($report) && $report->wip > 0 ? number_format($report->wip, 0, ',', '.') : '') }}">
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-4 items-center">
                                <label class="text-xs text-slate-600 dark:text-slate-400 font-semibold col-span-1">Counter
                                    Mesin</label>
                                <div class="col-span-2">
                                    <input type="text" name="total_counter" id="total_counter"
                                        class="saas-input h-9 text-center calc-trigger format-number text-sm"
                                        placeholder="0"
                                        value="{{ old('total_counter', isset($report) && $report->total_counter > 0 ? number_format($report->total_counter, 0, ',', '.') : '') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- KEMASAN --}}
                    <div
                        class="p-5 bg-slate-50 dark:bg-slate-700/30 rounded-2xl border border-slate-200 dark:border-slate-700">
                        <h5 class="text-xs font-bold text-slate-500 uppercase mb-4 tracking-wider">Informasi Kemasan <span
                                class="text-slate-400 font-normal lowercase">(auto)</span></h5>
                        <div class="space-y-4">
                            <div>
                                <label class="text-xs block text-slate-500 mb-1 font-semibold">Jenis Kemasan</label>
                                <input type="text" id="display_packaging_type"
                                    class="saas-input h-9 bg-slate-200 dark:bg-slate-600 text-xs font-bold text-slate-700 dark:text-slate-300"
                                    readonly
                                    value="{{ isset($report) && $report->packagingType ? $report->packagingType->name : '-' }}">
                                <input type="hidden" name="packaging_type_id" id="packaging_type_id"
                                    value="{{ old('packaging_type_id', $report->packaging_type_id ?? '') }}">
                                <input type="hidden" id="current_product_packaging_qty" value="0">
                            </div>
                            <div>
                                <label class="text-xs block text-slate-500 mb-1 font-semibold">Hasil Packing</label>
                                <input type="text" id="packaging_qty_display"
                                    class="saas-input h-9 bg-white dark:bg-slate-800 text-xs font-bold text-left pl-3 text-indigo-600 dark:text-indigo-400 border-indigo-200"
                                    readonly value="0">
                                <input type="hidden" name="packaging_qty" id="packaging_qty"
                                    value="{{ old('packaging_qty', $report->packaging_qty ?? 0) }}">
                                <div id="packaging_detail_info" class="text-[10px] text-right text-slate-400 mt-1 italic">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- NOTES --}}
                <div class="mb-8">
                    <label class="form-label">Catatan Tambahan <span
                            class="text-slate-400 font-normal text-xs">(Opsional)</span></label>
                    <textarea name="notes" id="notes" rows="8" class="saas-input h-auto py-3 resize-y"
                        placeholder="Tulis catatan penting terkait produksi shift ini...">{{ old('notes', $report->notes ?? '') }}</textarea>
                </div>

                {{-- FOOTER ACTIONS --}}
                <div class="flex items-center justify-end gap-4 border-t border-slate-100 dark:border-slate-700 pt-6">
                    <a href="{{ route('daily-reports.index') }}"
                        class="px-6 py-3 rounded-xl font-bold text-slate-600 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-700 transition-colors">Batal</a>
                    <button type="submit"
                        class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-lg shadow-indigo-500/30 transition-all transform hover:-translate-y-0.5 active:scale-95 flex items-center">
                        <i class="fas fa-save mr-2"></i> Simpan Laporan
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- SCRIPT JAVASCRIPT --}}
    <script>
        // Utility Formatter yang diperbaiki untuk sinkronisasi desimal blade
        function formatIndo(n, decimals = 0) {
            if (n === undefined || n === null || n === '') return '0';
            let num = typeof n === 'string' ? parseFloat(n.toString().replace(/\./g, '').replace(',', '.')) : Number(n);
            if (isNaN(num)) num = 0;
            return new Intl.NumberFormat('id-ID', {
                maximumFractionDigits: decimals
            }).format(num);
        }

        function parseIndo(s) {
            if (!s) return 0;
            if (typeof s === 'number') return s;
            let clean = s.toString().replace(/\./g, '').replace(',', '.');
            return parseFloat(clean) || 0;
        }

        function formatDecimalIndo(val) {
            if (!val) return '0';
            return val.toString().replace(/[^0-9,]/g, '').replace(/(,.*?),/g, '$1');
        }

        $(document).ready(function() {
            // Init Select2 with modern styling
            $('.select2-init').select2({
                width: '100%'
            });

            // Init Flatpickr
            flatpickr("#production_date", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d/m/Y",
                allowInput: true,
                maxDate: "today"
            });

            const timePickerConf = {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                allowInput: true,
                onClose: function() {
                    calculateAll();
                }
            };
            flatpickr(".time-picker", timePickerConf);

            // Load Batch Data if Edit Mode
            let initialBatchId = "{{ old('batch_id', $report->batch_id ?? '') }}";
            if (initialBatchId) {
                loadBatchData(initialBatchId);
            }

            // Event Listeners
            $('#batch_id').on('select2:select', function() {
                loadBatchData($(this).val());
            });

            const inputs =
                '.calc-trigger, .reject-input, .downtime-input, #qty_good, #purging_kg, #qty_sample, #total_runner, #wip, #total_counter';
            $(document).on('input change', inputs, function() {
                calculateAll();
            });
            $(document).on('focus', inputs, function() {
                if ($(this).val() === '0' || $(this).val() === '0,00') {
                    $(this).val('');
                }
            });

            $('#purging_kg, #weight_per_pcs').on('input', function() {
                let m = parseIndo($('#purging_kg').val()),
                    w = parseIndo($('#weight_per_pcs').val());
                $('#qty_purging').val(formatIndo((m > 0 && w > 0) ? Math.floor(m / w) : 0));
                calculateAll();
            });

            $(document).on('input', '.format-number', function() {
                let raw = $(this).val().replace(/\D/g, '');
                if (raw === '') {
                    $(this).val('');
                    return;
                }
                let v = parseInt(raw, 10);
                $(this).val(formatIndo(v));
                calculateAll();
            });

            $(document).on('input', '.format-decimal', function() {
                $(this).val(formatDecimalIndo($(this).val()));
                calculateAll();
            });
        });

        function loadBatchData(id) {
            if (!id) return;
            $.get('/daily-reports/get-batch/' + id, function(data) {
                $('#product_id').val(data.product_id);
                $('#color_id').val(data.color_id);
                $('#display_product').text(data.product_name);
                $('#display_color').text(data.color_name);
                $('#display_priority').text(data.priority || '-');
                $('#display_target').text(formatIndo(data.current_qty) + ' / ' + formatIndo(data.target_qty));

                let isEditMode = "{{ isset($report) ? 'true' : 'false' }}";
                let currentBatchId = "{{ $report->batch_id ?? '' }}";

                // Jika input baru ATAU user mengganti batch saat edit
                if (isEditMode === 'false' || (isEditMode === 'true' && id != currentBatchId)) {
                    $('#cycle_time').val(formatIndo(data.cycle_time, 2));
                    $('#cavity').val(formatIndo(data.cavity));
                    $('#wip_previous').val(formatIndo(data.previous_wip));
                    $('#weight_per_pcs').val(formatDecimalIndo(String(data.product_weight).replace('.', ',')));
                    $('#current_product_packaging_qty').val(data.product_packaging_qty);

                    if (data.packaging_type_id) {
                        $('#packaging_type_id').val(data.packaging_type_id);
                        $('#display_packaging_type').val(data.packaging_type_name);
                    }
                    if (data.machine_id) $('#machine_id').val(data.machine_id).trigger('change');
                } else {
                    $('#current_product_packaging_qty').val(data.product_packaging_qty);
                    if (data.packaging_type_id) {
                        $('#packaging_type_id').val(data.packaging_type_id);
                        $('#display_packaging_type').val(data.packaging_type_name);
                    }
                }

                calculateAll();
            });
        }

        window.setShiftTime = function(shiftId) {
            if (!shiftId) return;
            const times = shiftTimes[shiftId];
            if (times) {
                document.querySelector('#start_time')._flatpickr.setDate(times.start);
                document.querySelector('#end_time')._flatpickr.setDate(times.end);
                calculateAll();
            }
        }

        window.filterRejects = function(machineId) {
            const container = $('#reject_list_container');
            const items = container.find('.reject-item-row');
            if (!machineId) {
                items.show();
                $('#no_reject_msg').hide();
                return;
            }
            const validIds = machineRejectsMap[machineId] || [];
            if (validIds.length > 0) {
                let has = false;
                items.each(function() {
                    let id = parseInt($(this).data('id'));
                    if (validIds.includes(id)) {
                        $(this).show();
                        has = true;
                    } else {
                        $(this).hide();
                        $(this).find('input').val('');
                    }
                });
                $('#no_reject_msg').toggle(!has).toggleClass('flex', !has);
            } else {
                items.show();
                $('#no_reject_msg').hide();
            }
            calculateAll();
        }

        window.calculateAll = function() {
            let s = $('#start_time').val(),
                e = $('#end_time').val(),
                tm = 0;
            if (s && e) {
                let d1 = new Date("1970-01-01 " + s),
                    d2 = new Date("1970-01-01 " + e);
                if (d2 < d1) d2.setDate(d2.getDate() + 1);
                tm = Math.floor((d2 - d1) / 60000);
            }
            $('#total_minutes').val(tm);
            $('#display_total_minutes').val(tm);

            let tr = 0;
            $('.reject-input').each(function() {
                tr += parseIndo($(this).val());
            });
            $('#qty_reject_total').val(formatIndo(tr));

            let td = 0;
            $('.downtime-input').each(function() {
                td += parseIndo($(this).val());
            });
            $('#downtime_total').val(td);
            $('#display_downtime_total').text(td + " Min");

            let ctStd = parseIndo($('#cycle_time').val());
            let cavStd = parseIndo($('#cavity').val());
            let ctAct = parseIndo($('#actual_cycle_time').val());
            let cavAct = parseIndo($('#actual_cavity').val());

            let ctCalc = ctStd > 0 ? ctStd : (ctAct > 0 ? ctAct : 0);
            let cavCalc = cavStd > 0 ? cavStd : (cavAct > 0 ? cavAct : 0);

            // ==========================================
            // PERBAIKAN: Kurangi Downtime dari Total Waktu
            // ==========================================
            let netOperatingTime = tm - td; // Waktu Shift dikurangi Total Downtime

            let qt = 0;
            if (ctCalc > 0 && netOperatingTime > 0) {
                let totalSeconds = netOperatingTime * 60;
                qt = Math.floor((totalSeconds / ctCalc) * cavCalc);
            }
            $('#qty_theory').val(formatIndo(qt));

            let good = parseIndo($('#qty_good').val()),
                sample = parseIndo($('#qty_sample').val());
            let to = good + tr;
            $('#total_output').val(formatIndo(to));

            // Format desimal (2 angka di belakang koma) untuk yield & efficiency
            let yld = (to > 0) ? (good / to) * 100 : 0;
            $('#yield').val(formatIndo(yld, 2) + '%');

            let eff = (qt > 0) ? (good / qt) * 100 : 0;
            $('#efficiency').val(formatIndo(eff, 2) + '%');

            let net = Math.max(0, good - sample);
            let pi = parseFloat($('#current_product_packaging_qty').val()) || 0;
            if (pi > 0 && net > 0) {
                let box = Math.floor(net / pi),
                    rem = net % pi;
                $('#packaging_qty').val(box);
                $('#packaging_qty_display').val(rem > 0 ? `${formatIndo(box)} Box + ${rem} Pcs` :
                    `${formatIndo(box)} Box`);
                $('#packaging_detail_info').text(`Isi: ${pi} Pcs / Box`);
            } else {
                $('#packaging_qty').val(0);
                $('#packaging_qty_display').val('0');
                $('#packaging_detail_info').text('');
            }
        }
    </script>
@endsection
