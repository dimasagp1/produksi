@extends('layouts.app')

@section('title', 'Log Aktivitas')

@section('content')
    <div class="flex flex-col gap-8 fade-in-up">

        {{-- 1. HEADER SECTION & SEARCH --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2.5 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl text-indigo-600 dark:text-indigo-400">
                        <i class="fa-solid fa-clock-rotate-left text-xl"></i>
                    </div>
                    <h1 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight">Log Aktivitas</h1>
                </div>
                <p class="text-slate-500 dark:text-slate-400 font-medium">
                    Pantau semua perubahan data dan riwayat akses pengguna.
                </p>
            </div>

            {{-- Search Bar --}}
            <div class="flex items-center gap-3 w-full md:w-auto">
                <form action="{{ route('activity-logs.index') }}" method="GET" class="relative group w-full md:w-80">
                    {{-- Hidden input untuk menjaga filter event saat searching --}}
                    @if (request('event'))
                        <input type="hidden" name="event" value="{{ request('event') }}">
                    @endif

                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i
                            class="fa-solid fa-magnifying-glass text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari aktivitas, user, atau ID..."
                        class="block w-full pl-11 pr-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm font-semibold text-slate-700 dark:text-slate-200 placeholder:text-slate-400 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-sm">
                </form>
                <a href="{{ route('activity-logs.index') }}"
                    class="p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-slate-500 hover:text-red-600 hover:border-red-200 dark:hover:border-red-500/50 hover:shadow-lg transition-all active:scale-95 tooltip"
                    title="Reset Filter">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </div>

        {{-- 2. SUMMARY CARDS (CLICKABLE & FILTERABLE) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            @php
                $currentEvent = request('event');

                // Helper function untuk class active
                $getCardClass = function ($targetEvent) use ($currentEvent) {
                    $base =
                        'bg-white dark:bg-slate-800 p-5 rounded-3xl border shadow-sm relative overflow-hidden group transition-all duration-300 hover:-translate-y-1 hover:shadow-md cursor-pointer block';

                    if (($targetEvent === null && !$currentEvent) || $currentEvent === $targetEvent) {
                        // Style jika aktif
                        return "$base ring-2 ring-offset-2 ring-offset-slate-50 dark:ring-offset-slate-950 border-transparent " .
                            match ($targetEvent) {
                                'created' => 'ring-emerald-500',
                                'updated' => 'ring-amber-500',
                                'deleted' => 'ring-rose-500',
                                'users' => 'ring-blue-500',
                                default => 'ring-indigo-500', // All Logs
                            };
                    }
                    // Style jika tidak aktif
                    return "$base border-slate-200 dark:border-slate-700 hover:border-indigo-300 dark:hover:border-indigo-700";
                };
            @endphp

            {{-- 1. All Logs --}}
            <a href="{{ route('activity-logs.index') }}" class="{{ $getCardClass(null) }}">
                <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fa-solid fa-layer-group text-5xl text-indigo-600"></i>
                </div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Aktivitas</p>
                <h3 class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ number_format($totalLogs) }}</h3>
                <div class="mt-2 text-[10px] font-medium text-indigo-600 flex items-center gap-1">
                    <i class="fa-solid fa-list"></i> <span>Tampilkan Semua</span>
                </div>
            </a>

            {{-- 2. Created (Penambahan) --}}
            <a href="{{ route('activity-logs.index', ['event' => 'created']) }}" class="{{ $getCardClass('created') }}">
                <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fa-solid fa-folder-plus text-5xl text-emerald-600"></i>
                </div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Penambahan</p>
                <h3 class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ number_format($totalCreated) }}</h3>
                <div class="mt-2 text-[10px] font-medium text-emerald-600 flex items-center gap-1">
                    <i class="fa-solid fa-plus-circle"></i> <span>Event 'Created'</span>
                </div>
            </a>

            {{-- 3. Updated (Perubahan) --}}
            <a href="{{ route('activity-logs.index', ['event' => 'updated']) }}" class="{{ $getCardClass('updated') }}">
                <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fa-solid fa-pen-to-square text-5xl text-amber-500"></i>
                </div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Perubahan</p>
                <h3 class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ number_format($totalUpdated) }}</h3>
                <div class="mt-2 text-[10px] font-medium text-amber-500 flex items-center gap-1">
                    <i class="fa-solid fa-edit"></i> <span>Event 'Updated'</span>
                </div>
            </a>

            {{-- 4. Deleted (Penghapusan) --}}
            <a href="{{ route('activity-logs.index', ['event' => 'deleted']) }}" class="{{ $getCardClass('deleted') }}">
                <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fa-solid fa-trash-can text-5xl text-rose-600"></i>
                </div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Penghapusan</p>
                <h3 class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ number_format($totalDeleted) }}</h3>
                <div class="mt-2 text-[10px] font-medium text-rose-500 flex items-center gap-1">
                    <i class="fa-solid fa-triangle-exclamation"></i> <span>Event 'Deleted'</span>
                </div>
            </a>

            {{-- 5. User Activity (Kontributor) --}}
            <a href="{{ route('activity-logs.index', ['event' => 'users']) }}" class="{{ $getCardClass('users') }}">
                <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fa-solid fa-users text-5xl text-blue-600"></i>
                </div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kontributor</p>
                <h3 class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ number_format($totalUsers) }}</h3>
                <div class="mt-2 text-[10px] font-medium text-blue-600 flex items-center gap-1">
                    <i class="fa-solid fa-user-check"></i> <span>Aktivitas SDM</span>
                </div>
            </a>

        </div>

        {{-- 3. MAIN CONTENT (TABLE) --}}
        <div
            class="bg-white dark:bg-slate-800 rounded-[2rem] border border-slate-200 dark:border-slate-700 shadow-xl shadow-slate-200/40 dark:shadow-slate-900/40 overflow-hidden">
            {{-- Indikator Filter Aktif (Opsional, untuk kejelasan) --}}
            @if (request('event'))
                <div
                    class="px-6 py-3 bg-indigo-50 dark:bg-indigo-900/20 border-b border-indigo-100 dark:border-indigo-800/30 flex justify-between items-center">
                    <span class="text-xs font-bold text-indigo-700 dark:text-indigo-300">
                        <i class="fa-solid fa-filter mr-1"></i> Memfilter berdasarkan: <span
                            class="uppercase">{{ request('event') }}</span>
                    </span>
                    <a href="{{ route('activity-logs.index') }}"
                        class="text-[10px] font-bold text-slate-400 hover:text-red-500 transition-colors">
                        HAPUS FILTER <i class="fa-solid fa-xmark ml-1"></i>
                    </a>
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead
                        class="bg-slate-50/80 dark:bg-slate-900/50 backdrop-blur-sm border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th
                                class="px-6 py-5 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest w-16 text-center">
                                No</th>
                            <th class="px-6 py-5 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest w-48">
                                Waktu & Tanggal</th>
                            <th class="px-6 py-5 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest w-64">
                                Pengguna</th>
                            <th class="px-6 py-5 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest w-48">
                                Entitas / Modul</th>
                            <th class="px-6 py-5 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Detail
                                Perubahan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                        @forelse ($logs as $log)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/20 transition-colors group">

                                {{-- NO --}}
                                <td class="px-6 py-6 text-center">
                                    <span class="font-bold text-slate-400 text-xs">
                                        {{ ($logs->currentPage() - 1) * $logs->perPage() + $loop->iteration }}
                                    </span>
                                </td>

                                {{-- WAKTU --}}
                                <td class="px-6 py-6 align-top">
                                    <div class="flex flex-col">
                                        <span class="font-black text-slate-700 dark:text-white text-base font-mono">
                                            {{ $log->created_at->setTimezone('Asia/Jakarta')->format('H:i') }}
                                        </span>
                                        <div class="flex items-center gap-1.5 mt-1">
                                            <i class="fa-regular fa-calendar text-[10px] text-slate-400"></i>
                                            <span class="text-xs font-semibold text-slate-500">
                                                {{ $log->created_at->setTimezone('Asia/Jakarta')->translatedFormat('d M Y') }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                {{-- PENGGUNA --}}
                                <td class="px-6 py-6 align-top">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="h-10 w-10 rounded-2xl bg-indigo-50 dark:bg-slate-700 flex items-center justify-center text-xs font-black text-indigo-600 dark:text-indigo-400 ring-4 ring-white dark:ring-slate-800 shadow-sm shrink-0">
                                            {{ substr($log->causer->name ?? 'SY', 0, 2) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800 dark:text-white text-sm line-clamp-1">
                                                {{ $log->causer->name ?? 'System Bot' }}
                                            </div>
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 dark:bg-slate-900 text-slate-500 mt-1 border border-slate-200 dark:border-slate-700">
                                                {{ $log->causer->role ?? 'AUTOMATED' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                {{-- MODUL & EVENT --}}
                                <td class="px-6 py-6 align-top">
                                    @php
                                        // 1. Tentukan Style Badge
                                        $badges = [
                                            'created' => [
                                                'bg' => 'bg-emerald-50 dark:bg-emerald-900/20',
                                                'text' => 'text-emerald-600 dark:text-emerald-400',
                                                'border' => 'border-emerald-200 dark:border-emerald-800',
                                                'icon' => 'fa-plus',
                                            ],
                                            'updated' => [
                                                'bg' => 'bg-amber-50 dark:bg-amber-900/20',
                                                'text' => 'text-amber-600 dark:text-amber-400',
                                                'border' => 'border-amber-200 dark:border-amber-800',
                                                'icon' => 'fa-pen-to-square',
                                            ],
                                            'deleted' => [
                                                'bg' => 'bg-rose-50 dark:bg-rose-900/20',
                                                'text' => 'text-rose-600 dark:text-rose-400',
                                                'border' => 'border-rose-200 dark:border-rose-800',
                                                'icon' => 'fa-trash',
                                            ],
                                        ];
                                        $style = $badges[$log->event] ?? [
                                            'bg' => 'bg-slate-50',
                                            'text' => 'text-slate-600',
                                            'border' => 'border-slate-200',
                                            'icon' => 'fa-info-circle',
                                        ];

                                        // 2. Logika Penamaan Subjek (Agar bukan ID)
                                        $subjectType = class_basename($log->subject_type ?? 'System');
                                        $displayLabel = '#' . $log->subject_id; // Default fallback ke ID

                                        if ($log->subject) {
                                            // Jika datanya masih ada di database
                                            switch ($subjectType) {
                                                case 'Batch':
                                                    $displayLabel = $log->subject->batch_code;
                                                    break;
                                                case 'Product':
                                                    $displayLabel = $log->subject->name;
                                                    break;
                                                case 'Machine':
                                                    $displayLabel = $log->subject->name;
                                                    break;
                                                case 'User':
                                                    $displayLabel = $log->subject->name;
                                                    break;
                                                // Tambahkan model lain sesuai kebutuhan
                                                case 'DailyReport':
                                                    // Contoh format tanggal laporan
                                                    $displayLabel = \Carbon\Carbon::parse(
                                                        $log->subject->production_date,
                                                    )->format('d M Y');
                                                    break;
                                            }
                                        } else {
                                            // Jika data sudah dihapus (Hard Delete), coba ambil dari log properties 'old' atau 'attributes'
                                            if (isset($log->properties['attributes']['batch_code'])) {
                                                $displayLabel = $log->properties['attributes']['batch_code'];
                                            } elseif (isset($log->properties['old']['batch_code'])) {
                                                $displayLabel = $log->properties['old']['batch_code'];
                                            } elseif (isset($log->properties['attributes']['name'])) {
                                                $displayLabel = $log->properties['attributes']['name'];
                                            } elseif (isset($log->properties['old']['name'])) {
                                                $displayLabel = $log->properties['old']['name'];
                                            } else {
                                                $displayLabel = '#' . $log->subject_id . ' (Terhapus)';
                                            }
                                        }
                                    @endphp

                                    <div class="flex flex-col items-start gap-2">
                                        {{-- Badge Event --}}
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg border {{ $style['bg'] }} {{ $style['border'] }} {{ $style['text'] }}">
                                            <i class="fa-solid {{ $style['icon'] }} text-[10px]"></i>
                                            <span
                                                class="text-[10px] font-bold uppercase tracking-wide">{{ $log->event }}</span>
                                        </span>

                                        {{-- Info Subjek yang Lebih Jelas --}}
                                        <div class="ml-1">
                                            <div class="text-xs font-bold text-slate-700 dark:text-white break-words max-w-[180px]"
                                                title="{{ $displayLabel }}">
                                                {{ $displayLabel }}
                                            </div>
                                            <div
                                                class="text-[10px] text-slate-400 font-medium uppercase tracking-wide mt-0.5">
                                                {{ $subjectType }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- DETAIL PERUBAHAN (FULL WRAP) --}}
                                <td class="px-6 py-6 align-top">
                                    @if ($log->event === 'updated' && isset($log->properties['old']) && isset($log->properties['attributes']))
                                        <div
                                            class="bg-slate-50 dark:bg-slate-900/60 rounded-xl p-4 border border-slate-200 dark:border-slate-700/60 text-sm w-full">
                                            <div class="flex flex-col gap-3">
                                                @foreach ($log->properties['attributes'] as $key => $newValue)
                                                    @if (isset($log->properties['old'][$key]) && $log->properties['old'][$key] != $newValue)
                                                        {{-- Container Item --}}
                                                        <div
                                                            class="flex flex-col sm:flex-row sm:items-start gap-2 sm:gap-4 p-2 rounded-lg hover:bg-white dark:hover:bg-slate-800 transition-colors border border-transparent hover:border-slate-100 dark:hover:border-slate-700">

                                                            {{-- 1. NAMA FIELD (KEY) --}}
                                                            <div class="sm:w-32 shrink-0">
                                                                <span
                                                                    class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block break-words leading-snug mt-1">
                                                                    {{ str_replace('_', ' ', $key) }}
                                                                </span>
                                                            </div>

                                                            {{-- 2. NILAI DATA (OLD vs NEW) --}}
                                                            <div
                                                                class="flex flex-col sm:flex-row sm:items-start gap-3 flex-1 min-w-0">
                                                                {{-- Old Value --}}
                                                                <div
                                                                    class="bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 px-2 py-1.5 rounded text-xs line-through decoration-rose-300 font-medium break-words w-full sm:w-auto">
                                                                    {{ $log->properties['old'][$key] ?? 'Null' }}
                                                                </div>

                                                                {{-- Icon Arrow --}}
                                                                <i
                                                                    class="fa-solid fa-arrow-right-long text-slate-300 text-xs mt-1.5 hidden sm:block"></i>
                                                                <i
                                                                    class="fa-solid fa-arrow-down-long text-slate-300 text-xs sm:hidden"></i>

                                                                {{-- New Value --}}
                                                                <div
                                                                    class="bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 px-2 py-1.5 rounded text-xs font-bold break-words w-full sm:w-auto shadow-sm border border-emerald-100 dark:border-emerald-900/30">
                                                                    {{ $newValue ?? 'Null' }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @elseif($log->event === 'created')
                                        <div
                                            class="flex items-start gap-3 p-3 rounded-xl bg-emerald-50/50 dark:bg-emerald-900/10 border border-emerald-100 dark:border-emerald-800/30">
                                            <div
                                                class="h-8 w-8 rounded-full bg-emerald-100 dark:bg-emerald-800 flex items-center justify-center text-emerald-600 dark:text-emerald-300 shrink-0">
                                                <i class="fa-solid fa-check"></i>
                                            </div>
                                            <span class="text-sm font-medium text-emerald-800 dark:text-emerald-200 mt-1">
                                                Data baru berhasil dibuat.
                                            </span>
                                        </div>
                                    @elseif($log->event === 'deleted')
                                        <div
                                            class="flex items-start gap-3 p-3 rounded-xl bg-rose-50/50 dark:bg-rose-900/10 border border-rose-100 dark:border-rose-800/30">
                                            <div
                                                class="h-8 w-8 rounded-full bg-rose-100 dark:bg-rose-800 flex items-center justify-center text-rose-600 dark:text-rose-300 shrink-0">
                                                <i class="fa-solid fa-trash"></i>
                                            </div>
                                            <span class="text-sm font-medium text-rose-800 dark:text-rose-200 mt-1">
                                                Data telah dihapus permanen.
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-slate-500 italic block mt-2">{{ $log->description }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-20 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div
                                            class="h-24 w-24 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4">
                                            <i class="fa-solid fa-clipboard-check text-4xl text-slate-300"></i>
                                        </div>
                                        <h3 class="text-lg font-bold text-slate-700 dark:text-slate-200">Tidak ada
                                            aktivitas</h3>
                                        <p class="text-slate-400 text-sm mt-1">Tidak ada data log untuk filter yang
                                            dipilih.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- 3. PAGINATION --}}
            @if ($logs->hasPages())
                <div class="px-6 py-6 border-t border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
