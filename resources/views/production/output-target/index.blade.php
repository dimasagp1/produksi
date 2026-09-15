@extends('layouts.app')

@section('title', 'Output vs Target Produksi')

@section('content')
    {{-- 1. HEADER STATISTIK (GLOBAL ACHIEVEMENT) --}}
    <div
        class="bg-gradient-to-r from-slate-800 to-slate-700 rounded-2xl p-6 shadow-lg mb-8 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 h-32 w-32 rounded-full bg-white/5 blur-2xl"></div>
        <div class="relative z-10 grid grid-cols-1 md:grid-cols-3 gap-8 items-center">
            <div>
                <h3 class="text-sm font-medium text-slate-300 uppercase tracking-wider mb-1">Pencapaian Global (Running)</h3>
                <div class="flex items-end gap-2">
                    <span class="text-4xl font-bold">{{ number_format($globalPercentage, 1) }}%</span>
                    <span class="text-sm text-slate-300 mb-1.5">dari target total</span>
                </div>
            </div>

            {{-- Progress Bar Besar --}}
            <div class="md:col-span-2 space-y-2">
                <div class="flex justify-between text-xs font-semibold tracking-wide">
                    <span>Aktual: {{ number_format($totalCurrent) }} Pcs</span>
                    <span>Target: {{ number_format($totalTarget) }} Pcs</span>
                </div>
                <div class="h-4 w-full bg-slate-600/50 rounded-full overflow-hidden backdrop-blur-sm border border-white/10">
                    <div class="h-full bg-gradient-to-r from-emerald-400 to-teal-400 rounded-full transition-all duration-1000 ease-out shadow-[0_0_10px_rgba(52,211,153,0.5)]"
                        style="width: {{ $globalProgressWidth }}%"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. TOOLBAR FILTER --}}
    <div
        class="bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 mb-6">
        <form action="{{ route('output-target.index') }}" method="GET" class="contents w-full">
            <div class="flex flex-col sm:flex-row justify-between gap-3 items-center">
                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto flex-grow">
                    {{-- Pencarian --}}
                    <div class="relative w-full sm:max-w-xs group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-slate-400"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" class="saas-input !pl-10 h-10"
                            placeholder="Cari Batch, Produk...">
                    </div>

                    {{-- Filter Status --}}
                    <div class="relative w-full sm:w-40">
                        <select name="filter_status" class="saas-input h-10 cursor-pointer" onchange="this.form.submit()">
                            <option value="">Status: Running & Selesai</option>
                            <option value="running" {{ request('filter_status') == 'running' ? 'selected' : '' }}>Running
                            </option>
                            <option value="completed" {{ request('filter_status') == 'completed' ? 'selected' : '' }}>
                                Selesai</option>
                            <option value="planning" {{ request('filter_status') == 'planning' ? 'selected' : '' }}>Planning
                            </option>
                        </select>
                    </div>

                    <button type="submit"
                        class="h-10 px-4 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow-md transition-all active:scale-95">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- 3. GRID BATCH CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse ($batches as $batch)
            @php
                $percentage =
                    $batch->target_quantity > 0 ? ($batch->current_quantity / $batch->target_quantity) * 100 : 0;
                $width = $percentage > 100 ? 100 : $percentage;

                // Warna Progress Bar berdasarkan status & persentase
                if ($batch->status == 'completed') {
                    $barColor = 'from-blue-500 to-indigo-500';
                    $textColor = 'text-blue-600 dark:text-blue-400';
                    $bgColor = 'bg-blue-50 dark:bg-blue-900/20';
                } elseif ($percentage >= 100) {
                    $barColor = 'from-emerald-500 to-green-500'; // Overachieved
                    $textColor = 'text-emerald-600 dark:text-emerald-400';
                    $bgColor = 'bg-emerald-50 dark:bg-emerald-900/20';
                } elseif ($percentage < 50) {
                    $barColor = 'from-rose-500 to-orange-500'; // Behind
                    $textColor = 'text-slate-600 dark:text-slate-400';
                    $bgColor = 'bg-white dark:bg-slate-800';
                } else {
                    $barColor = 'from-amber-400 to-orange-400'; // On Progress
                    $textColor = 'text-slate-600 dark:text-slate-400';
                    $bgColor = 'bg-white dark:bg-slate-800';
                }

                $remaining = max(0, $batch->target_quantity - $batch->current_quantity);
            @endphp

            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 p-5 flex flex-col h-full relative overflow-hidden group hover:border-brand-300 transition-all">

                {{-- Status Badge --}}
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide
                            {{ $batch->status == 'running'
                                ? 'bg-emerald-100 text-emerald-700'
                                : ($batch->status == 'completed'
                                    ? 'bg-blue-100 text-blue-700'
                                    : 'bg-slate-100 text-slate-600') }}">
                            {{ $batch->status }}
                        </span>
                        <h4 class="font-bold text-lg text-slate-800 dark:text-white mt-2 leading-tight">
                            {{ $batch->batch_code }}
                        </h4>
                        <p class="text-xs text-slate-500 truncate mt-0.5 max-w-[250px]"
                            title="{{ $batch->product->name }}">
                            {{ $batch->product->name }}
                        </p>
                    </div>
                    <div class="text-right">
                        <div
                            class="h-10 w-10 rounded-lg {{ $bgColor }} flex items-center justify-center {{ $textColor }}">
                            <i class="fa-solid fa-chart-pie text-lg"></i>
                        </div>
                    </div>
                </div>

                {{-- Progress Info --}}
                <div class="flex-1">
                    <div class="flex justify-between items-end mb-2">
                        <div>
                            <span
                                class="text-3xl font-extrabold text-slate-800 dark:text-white">{{ number_format($percentage, 0) }}%</span>
                        </div>
                        <div class="text-right text-xs text-slate-500">
                            <span class="block">Target: <b>{{ number_format($batch->target_quantity) }}</b></span>
                        </div>
                    </div>

                    {{-- Bar --}}
                    <div class="h-3 w-full bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden mb-4">
                        <div class="h-full bg-gradient-to-r {{ $barColor }} rounded-full"
                            style="width: {{ $width }}%"></div>
                    </div>

                    {{-- Detail Stats --}}
                    <div class="grid grid-cols-2 gap-3 text-center">
                        <div
                            class="bg-slate-50 dark:bg-slate-700/50 rounded-lg p-2 border border-slate-100 dark:border-slate-700">
                            <p class="text-[10px] text-slate-400 uppercase font-bold">Output (OK)</p>
                            <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400">
                                {{ number_format($batch->current_quantity) }}</p>
                        </div>
                        <div
                            class="bg-slate-50 dark:bg-slate-700/50 rounded-lg p-2 border border-slate-100 dark:border-slate-700">
                            <p class="text-[10px] text-slate-400 uppercase font-bold">Sisa Target</p>
                            <p class="text-sm font-bold text-slate-600 dark:text-slate-300">{{ number_format($remaining) }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Footer Info --}}
                <div
                    class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700 flex justify-between items-center text-[10px] text-slate-400">
                    <div class="flex items-center gap-1">
                        <i class="fa-solid fa-gear"></i> {{ $batch->machine->name ?? '-' }}
                    </div>
                    <div class="flex items-center gap-1 text-rose-500" title="Total Reject">
                        <i class="fa-solid fa-ban"></i> {{ number_format($batch->reject_quantity) }} NG
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center">
                <div
                    class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800 mb-4">
                    <i class="fa-solid fa-chart-simple text-3xl text-slate-400"></i>
                </div>
                <h3 class="text-lg font-medium text-slate-900 dark:text-white">Tidak ada data batch</h3>
                <p class="mt-1 text-slate-500">Belum ada batch produksi yang sesuai dengan filter.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $batches->links() }}
    </div>
@endsection
