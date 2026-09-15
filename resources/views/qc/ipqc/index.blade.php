@extends('layouts.app')

@section('title', 'IPQC - In-Process Control')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <style>
        .saas-input {
            height: 44px;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 0 16px;
            width: 100%;
            background-color: #f8fafc;
        }

        .dark .saas-input {
            background-color: #1e293b;
            border-color: #334155;
            color: #f1f5f9;
        }

        .saas-input:focus {
            outline: none;
            border-color: #3b82f6;
        }
    </style>

    {{-- HEADER STATS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        {{-- Total Cek --}}
        <div
            class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Total Patrol</p>
                <h3 class="text-3xl font-extrabold text-slate-800 dark:text-white mt-1">{{ number_format($totalCheck) }}</h3>
                <p class="text-[10px] text-slate-400 mt-1">Hari Ini</p>
            </div>
            <div
                class="h-12 w-12 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center text-indigo-600">
                <i class="fa-solid fa-person-walking-arrow-right text-xl"></i>
            </div>
        </div>
        {{-- OK --}}
        <div
            class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Status OK</p>
                <h3 class="text-3xl font-extrabold text-emerald-600 mt-1">{{ number_format($totalOK) }}</h3>
                <p class="text-[10px] text-slate-400 mt-1">Sesuai Standar</p>
            </div>
            <div
                class="h-12 w-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center text-emerald-600">
                <i class="fa-solid fa-check-circle text-xl"></i>
            </div>
        </div>
        {{-- NG --}}
        <div
            class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Temuan NG</p>
                <h3 class="text-3xl font-extrabold text-rose-600 mt-1">{{ number_format($totalNG) }}</h3>
                <p class="text-[10px] text-slate-400 mt-1">Not Good / Masalah</p>
            </div>
            <div class="h-12 w-12 rounded-xl bg-rose-50 dark:bg-rose-900/20 flex items-center justify-center text-rose-600">
                <i class="fa-solid fa-triangle-exclamation text-xl"></i>
            </div>
        </div>
    </div>

    {{-- TOOLBAR --}}
    <div
        class="bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <button onclick="document.getElementById('modalNewIPQC').classList.remove('hidden')"
                class="h-10 px-4 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow-md transition-all flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Patrol Baru
            </button>

            <form action="{{ route('ipqc.index') }}" method="GET"
                class="flex flex-col sm:flex-row gap-2 w-full md:w-auto items-end sm:items-center">
                <div class="relative w-full sm:w-40">
                    <input type="text" name="date" class="saas-input h-10 date-picker cursor-pointer"
                        value="{{ $date }}" placeholder="Tanggal">
                </div>
                <div class="relative w-full sm:max-w-xs">
                    <input type="text" name="search" value="{{ request('search') }}" class="saas-input h-10 !pl-9"
                        placeholder="Cari Mesin/Produk...">
                    <i class="fa-solid fa-search absolute left-3 top-3 text-slate-400"></i>
                </div>
                <button type="submit"
                    class="h-10 px-4 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition-all">
                    <i class="fa-solid fa-filter"></i>
                </button>
            </form>
        </div>
    </div>

    {{-- TABEL --}}
    <div
        class="bg-white dark:bg-slate-800 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                <thead class="bg-slate-50/80 dark:bg-slate-700/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase w-10">Jam</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Mesin & Shift</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Produk</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Temuan / Catatan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Inspector</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse ($inspections as $item)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-6 py-4 text-sm font-mono text-slate-600 dark:text-slate-300">
                                {{ \Carbon\Carbon::parse($item->inspection_time)->format('H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-slate-800 dark:text-white">{{ $item->machine->name }}
                                </div>
                                <div class="text-xs text-slate-500">{{ $item->shift->name ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700 dark:text-slate-300">
                                {{ $item->product->name }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($item->status == 'ok')
                                    <span
                                        class="px-3 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                        OK
                                    </span>
                                @else
                                    <span
                                        class="px-3 py-1 rounded-lg text-xs font-bold bg-rose-100 text-rose-700 border border-rose-200 animate-pulse">
                                        NG (Not Good)
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500 italic max-w-xs break-words">
                                {{ $item->remarks ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                {{ $item->inspector ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">Belum ada data patrol hari ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700">{{ $inspections->links() }}</div>
    </div>

    {{-- MODAL INPUT --}}
    <div id="modalNewIPQC" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75"
                onclick="document.getElementById('modalNewIPQC').classList.add('hidden')"></div>
            <div
                class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <form action="{{ route('ipqc.store') }}" method="POST">
                    @csrf
                    <div class="bg-white dark:bg-slate-800 px-4 pt-5 pb-4 sm:p-6">
                        <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-4">Input Patrol IPQC</h3>
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tanggal</label>
                                    <input type="text" name="inspection_date" class="saas-input date-picker"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Jam</label>
                                    <input type="time" name="inspection_time" class="saas-input"
                                        value="{{ date('H:i') }}" required>
                                </div>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Mesin</label>
                                <select name="machine_id" class="saas-input" required>
                                    @foreach ($machines as $m)
                                        <option value="{{ $m->id }}">{{ $m->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Produk</label>
                                    <select name="product_id" class="saas-input" required>
                                        @foreach ($products as $p)
                                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Shift</label>
                                    <select name="shift_id" class="saas-input">
                                        @foreach ($shifts as $s)
                                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Hasil
                                    Cek</label>
                                <select name="status" class="saas-input font-bold">
                                    <option value="ok" class="text-emerald-600">✅ OK - Sesuai Standar</option>
                                    <option value="ng" class="text-rose-600">❌ NG - Ada Masalah</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Keterangan
                                    / Temuan</label>
                                <textarea name="remarks" class="saas-input h-20 pt-2" placeholder="Contoh: Dimensi kurang, warna belang..."></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama
                                    Inspector</label>
                                <input type="text" name="inspector" class="saas-input" placeholder="Nama QC">
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-700/50 px-4 py-3 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-brand-600 text-white font-medium hover:bg-brand-700 sm:ml-3 sm:w-auto">Simpan</button>
                        <button type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-xl border border-slate-300 shadow-sm px-4 py-2 bg-white text-slate-700 font-medium hover:bg-slate-50 sm:mt-0 sm:ml-3 sm:w-auto"
                            onclick="document.getElementById('modalNewIPQC').classList.add('hidden')">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('load', function() {
            if (typeof flatpickr !== 'undefined') flatpickr(".date-picker", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d M Y"
            });
        });
    </script>
@endsection
