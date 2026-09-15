@extends('layouts.app')

@section('title', 'Detail Riwayat Mesin')

@section('content')
    {{-- HEADER --}}
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('machine-history.index') }}"
            class="h-10 w-10 flex items-center justify-center bg-white dark:bg-slate-800 rounded-xl shadow text-slate-500 hover:text-brand-600">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">{{ $machine->name }}</h1>
            <p class="text-slate-500 text-sm">Kode: {{ $machine->code }} | Lokasi: {{ $machine->location ?? '-' }}</p>
        </div>
    </div>

    {{-- STATS CARD --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border-l-4 border-rose-500">
            <p class="text-xs text-slate-500 uppercase font-bold">Total Breakdown</p>
            <h3 class="text-2xl font-extrabold text-slate-800 dark:text-white">{{ $totalBreakdown }} <span
                    class="text-sm font-normal text-slate-400">Kali</span></h3>
        </div>
        {{-- Bisa ditambah statistik lain seperti Total Biaya, Umur Mesin, dll --}}
    </div>

    {{-- TIMELINE --}}
    <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700">
        <h3 class="font-bold text-lg mb-6 border-b pb-2 border-slate-100 dark:border-slate-700">Lini Masa Aktivitas</h3>

        <div class="relative border-l-2 border-slate-200 dark:border-slate-700 ml-4 space-y-8">
            @forelse ($timeline as $item)
                @php
                    // Mapping Warna Tailwind
                    $colors = [
                        'rose' => 'bg-rose-100 text-rose-600 border-rose-200',
                        'amber' => 'bg-amber-100 text-amber-600 border-amber-200',
                        'blue' => 'bg-blue-100 text-blue-600 border-blue-200',
                        'slate' => 'bg-slate-100 text-slate-600 border-slate-200',
                    ];
                    $theme = $colors[$item['color']] ?? $colors['slate'];
                @endphp

                <div class="relative pl-8">
                    {{-- Dot Icon --}}
                    <div
                        class="absolute -left-[17px] top-0 h-9 w-9 rounded-full border-4 border-white dark:border-slate-800 flex items-center justify-center {{ $theme }}">
                        <i class="fa-solid {{ $item['icon'] }} text-xs"></i>
                    </div>

                    {{-- Content --}}
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2">
                        <div>
                            <span class="text-xs font-mono text-slate-400 mb-1 block">
                                {{ \Carbon\Carbon::parse($item['date'])->format('d M Y, H:i') }}
                            </span>
                            <h4 class="font-bold text-slate-800 dark:text-white text-base">
                                {{ $item['title'] }}
                            </h4>
                            <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                                {{ $item['desc'] }}
                            </p>
                        </div>

                        <span
                            class="inline-flex px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider {{ $theme }}">
                            {{ $item['status'] }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="pl-8 text-slate-400 italic">Belum ada riwayat aktivitas untuk mesin ini.</div>
            @endforelse
        </div>
    </div>
@endsection
