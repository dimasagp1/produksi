@extends('layouts.app')

@section('title', 'Master Production Schedule (MPS)')

@section('content')
    <div class="flex flex-col gap-8 fade-in-up">

        {{-- 1. HEADER & ACTION --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2.5 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl text-indigo-600 dark:text-indigo-400">
                        <i class="fa-solid fa-calendar-days text-xl"></i>
                    </div>
                    <h1 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight">Production Schedule</h1>
                </div>
                <p class="text-slate-500 dark:text-slate-400 font-medium">
                    Kelola rencana produksi bulanan, forecast, dan target output.
                </p>
            </div>

            <button onclick="openModal('modalCreate')"
                class="group px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl shadow-lg shadow-indigo-500/30 transition-all active:scale-95 flex items-center gap-2">
                <i class="fa-solid fa-plus transition-transform group-hover:rotate-90"></i>
                <span>Tambah Periode</span>
            </button>
        </div>

        {{-- 2. SUMMARY CARDS (CLICKABLE FILTER) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                $status = request('status');
                $cardBase =
                    'bg-white dark:bg-slate-800 p-5 rounded-3xl border shadow-sm relative overflow-hidden group transition-all duration-300 hover:-translate-y-1 hover:shadow-md cursor-pointer block';

                // Helper Class Active
                $getRing = fn($s, $color) => $status === $s
                    ? "ring-2 ring-offset-2 ring-offset-slate-50 dark:ring-offset-slate-950 border-transparent ring-$color-500"
                    : 'border-slate-200 dark:border-slate-700 hover:border-indigo-300 dark:hover:border-indigo-700';
            @endphp

            {{-- All Plans --}}
            <a href="{{ route('mps.index') }}"
                class="{{ $cardBase }} {{ $status ? 'border-slate-200 dark:border-slate-700' : 'ring-2 ring-offset-2 ring-offset-slate-50 dark:ring-offset-slate-950 border-transparent ring-indigo-500' }}">
                <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fa-solid fa-layer-group text-5xl text-indigo-600"></i>
                </div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Rencana</p>
                <h3 class="text-3xl font-black text-slate-800 dark:text-white mt-1">{{ $totalPlans }}</h3>
                <div class="mt-2 text-[10px] font-medium text-indigo-600 flex items-center gap-1">
                    <i class="fa-solid fa-list"></i> <span>Semua Periode</span>
                </div>
            </a>

            {{-- Draft --}}
            <a href="{{ route('mps.index', ['status' => 'draft']) }}"
                class="{{ $cardBase }} {{ $getRing('draft', 'slate') }}">
                <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fa-solid fa-file-pen text-5xl text-slate-600"></i>
                </div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Draft</p>
                <h3 class="text-3xl font-black text-slate-800 dark:text-white mt-1">{{ $draftPlans }}</h3>
                <div class="mt-2 text-[10px] font-medium text-slate-500 flex items-center gap-1">
                    <i class="fa-solid fa-clock"></i> <span>Menunggu Konfirmasi</span>
                </div>
            </a>

            {{-- Confirmed --}}
            <a href="{{ route('mps.index', ['status' => 'confirmed']) }}"
                class="{{ $cardBase }} {{ $getRing('confirmed', 'blue') }}">
                <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fa-solid fa-check-circle text-5xl text-blue-600"></i>
                </div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Confirmed</p>
                <h3 class="text-3xl font-black text-slate-800 dark:text-white mt-1">{{ $confirmedPlans }}</h3>
                <div class="mt-2 text-[10px] font-medium text-blue-600 flex items-center gap-1">
                    <i class="fa-solid fa-play"></i> <span>Sedang Berjalan</span>
                </div>
            </a>

            {{-- Closed --}}
            <a href="{{ route('mps.index', ['status' => 'closed']) }}"
                class="{{ $cardBase }} {{ $getRing('closed', 'emerald') }}">
                <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fa-solid fa-flag-checkered text-5xl text-emerald-600"></i>
                </div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Closed</p>
                <h3 class="text-3xl font-black text-slate-800 dark:text-white mt-1">{{ $closedPlans }}</h3>
                <div class="mt-2 text-[10px] font-medium text-emerald-600 flex items-center gap-1">
                    <i class="fa-solid fa-lock"></i> <span>Selesai / Arsip</span>
                </div>
            </a>
        </div>

        {{-- 3. PLAN GRID --}}
        @if ($plans->isEmpty())
            <div
                class="text-center py-20 bg-slate-50 dark:bg-slate-800/50 rounded-3xl border border-dashed border-slate-300 dark:border-slate-700">
                <div
                    class="inline-flex h-20 w-20 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 mb-4">
                    <i class="fa-solid fa-calendar-xmark text-3xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-700 dark:text-white">Tidak ada Data MPS</h3>
                <p class="text-sm text-slate-500">Belum ada rencana produksi untuk filter ini.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($plans as $plan)
                    <div
                        class="bg-white dark:bg-slate-800 rounded-[2rem] border border-slate-200 dark:border-slate-700 shadow-xl shadow-slate-200/40 dark:shadow-slate-900/40 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col">

                        {{-- Card Header --}}
                        <div class="p-6 border-b border-slate-100 dark:border-slate-700/50">
                            <div class="flex justify-between items-start mb-2">
                                <span
                                    class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border
                                    {{ $plan->status == 'draft' ? 'bg-slate-100 text-slate-600 border-slate-200' : '' }}
                                    {{ $plan->status == 'confirmed' ? 'bg-blue-50 text-blue-600 border-blue-100' : '' }}
                                    {{ $plan->status == 'closed' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : '' }}">
                                    {{ $plan->status }}
                                </span>

                                {{-- Tombol Hapus (Draft & Confirmed Boleh, Closed Tidak Boleh) --}}
                                @if ($plan->status !== 'closed')
                                    <form action="{{ route('mps.destroy', $plan->id) }}" method="POST"
                                        onsubmit="return confirmDelete(event)">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-slate-300 hover:text-red-500 transition-colors"
                                            title="Hapus Periode">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <h3 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight">
                                {{ $plan->period->translatedFormat('F Y') }}
                            </h3>
                            <p class="text-xs font-mono text-slate-400 mt-1">{{ $plan->plan_code }}</p>
                        </div>

                        {{-- Card Body --}}
                        <div class="p-6 flex-1">
                            <div class="grid grid-cols-2 gap-4">
                                <div
                                    class="p-3 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-100 dark:border-slate-700">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase">Total SKU</p>
                                    <p class="text-lg font-bold text-slate-700 dark:text-slate-200">
                                        {{ $plan->items()->count() }} Item</p>
                                </div>
                                <div
                                    class="p-3 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-100 dark:border-slate-700">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase">Total Target</p>
                                    <p class="text-lg font-bold text-indigo-600 dark:text-indigo-400">
                                        {{ number_format($plan->items()->sum('production_qty')) }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Card Footer --}}
                        <div
                            class="p-4 bg-slate-50/50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-700 flex flex-col gap-2">
                            <a href="{{ route('mps.show', $plan->id) }}"
                                class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-center shadow-lg shadow-indigo-500/20 transition-all active:scale-95 flex items-center justify-center gap-2">
                                <span>Buka Detail</span> <i class="fa-solid fa-arrow-right"></i>
                            </a>

                            <div class="grid grid-cols-2 gap-2 mt-1">
                                <a href="{{ route('mrp.show', $plan->id) }}"
                                    class="py-2.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 font-bold rounded-xl text-xs text-center hover:bg-slate-50 dark:hover:bg-slate-600 transition-colors">
                                    <i class="fa-solid fa-calculator mr-1"></i> MRP
                                </a>
                                <a href="{{ route('crp.show', $plan->id) }}"
                                    class="py-2.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 font-bold rounded-xl text-xs text-center hover:bg-slate-50 dark:hover:bg-slate-600 transition-colors">
                                    <i class="fa-solid fa-chart-simple mr-1"></i> CRP
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $plans->links() }}
            </div>
        @endif
    </div>

    {{-- MODAL CREATE (MODERN) --}}
    <div id="modalCreate" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
            onclick="document.getElementById('modalCreate').classList.add('hidden')"></div>

        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-3xl bg-white dark:bg-slate-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-100 dark:border-slate-700">
                    <form action="{{ route('mps.store') }}" method="POST">
                        @csrf
                        <div class="bg-white dark:bg-slate-800 px-8 py-8">
                            <div class="flex items-center gap-4 mb-6">
                                <div
                                    class="h-12 w-12 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600">
                                    <i class="fa-solid fa-calendar-plus text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-black text-slate-800 dark:text-white" id="modal-title">Buat
                                        Periode Baru</h3>
                                    <p class="text-sm text-slate-500">Tentukan bulan untuk rencana produksi.</p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pilih
                                    Bulan & Tahun</label>
                                <input type="month" name="period"
                                    class="block w-full rounded-2xl border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-3 px-4 font-semibold text-slate-700 dark:text-white"
                                    required>
                            </div>
                        </div>
                        <div
                            class="bg-slate-50 dark:bg-slate-900/50 px-8 py-5 flex flex-row-reverse gap-3 border-t border-slate-100 dark:border-slate-700">
                            <button type="submit"
                                class="inline-flex w-full justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-500/30 hover:bg-indigo-500 sm:w-auto transition-all active:scale-95">Generate
                                MPS</button>
                            <button type="button"
                                class="inline-flex w-full justify-center rounded-xl bg-white dark:bg-slate-800 px-5 py-3 text-sm font-bold text-slate-700 dark:text-slate-300 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700 sm:w-auto transition-all"
                                onclick="document.getElementById('modalCreate').classList.add('hidden')">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPTS (Konfirmasi Hapus & Flash Message) --}}
    <script>
        // --- 1. Notifikasi Sukses/Gagal Otomatis ---
        document.addEventListener("DOMContentLoaded", function() {
            @if (session('success'))
                Swal.fire({
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    icon: 'success',
                    showConfirmButton: false, // Tanpa Tombol OK
                    timer: 2000, // Hilang dalam 2 detik
                    timerProgressBar: true,
                    customClass: {
                        popup: 'rounded-3xl'
                    }
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    title: 'Gagal!',
                    text: "{{ session('error') }}",
                    icon: 'error',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'rounded-3xl'
                    }
                });
            @endif
        });

        // --- 2. Konfirmasi Hapus ---
        function confirmDelete(event) {
            event.preventDefault();
            const form = event.target;
            Swal.fire({
                title: 'Hapus Periode Ini?',
                text: "Data detail SKU dan Forecast di dalamnya akan ikut terhapus permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-3xl',
                    confirmButton: 'rounded-xl font-bold px-6 py-2.5',
                    cancelButton: 'rounded-xl font-bold px-6 py-2.5'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>
@endsection
