@extends('layouts.app')

@section('title', 'IQC - Incoming Quality Control')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    {{-- CSS Input --}}
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
        {{-- Total --}}
        <div
            class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Total Inspeksi</p>
                <h3 class="text-3xl font-extrabold text-slate-800 dark:text-white mt-1">
                    {{ number_format($totalInspections) }}</h3>
                <p class="text-[10px] text-slate-400 mt-1">Periode ini</p>
            </div>
            <div class="h-12 w-12 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600">
                <i class="fa-solid fa-clipboard-check text-xl"></i>
            </div>
        </div>
        {{-- Rejected --}}
        <div
            class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Material Reject</p>
                <h3 class="text-3xl font-extrabold text-rose-600 mt-1">{{ number_format($totalRejected) }}</h3>
                <p class="text-[10px] text-slate-400 mt-1">Tidak lolos QC</p>
            </div>
            <div class="h-12 w-12 rounded-xl bg-rose-50 dark:bg-rose-900/20 flex items-center justify-center text-rose-600">
                <i class="fa-solid fa-ban text-xl"></i>
            </div>
        </div>
        {{-- Pass Rate --}}
        <div
            class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Pass Rate</p>
                <h3 class="text-3xl font-extrabold text-emerald-600 mt-1">{{ number_format($passRate, 1) }}%</h3>
                <p class="text-[10px] text-slate-400 mt-1">Tingkat Kelolosan</p>
            </div>
            <div
                class="h-12 w-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center text-emerald-600">
                <i class="fa-solid fa-percent text-xl"></i>
            </div>
        </div>
    </div>

    {{-- TOOLBAR --}}
    <div
        class="bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            {{-- Tombol Tambah --}}
            <button onclick="document.getElementById('modalNewIQC').classList.remove('hidden')"
                class="h-10 px-4 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow-md transition-all flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Input IQC
            </button>

            {{-- Filter Form --}}
            <form action="{{ route('iqc.index') }}" method="GET"
                class="flex flex-col sm:flex-row gap-2 w-full md:w-auto items-end sm:items-center">
                <div class="flex gap-2 w-full sm:w-auto">
                    <input type="text" name="start_date" class="saas-input h-10 date-picker cursor-pointer"
                        value="{{ $startDate }}" placeholder="Start">
                    <input type="text" name="end_date" class="saas-input h-10 date-picker cursor-pointer"
                        value="{{ $endDate }}" placeholder="End">
                </div>
                <div class="relative w-full sm:max-w-xs">
                    <input type="text" name="search" value="{{ request('search') }}" class="saas-input h-10 !pl-9"
                        placeholder="Cari Material/Supplier...">
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
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase w-10">No</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Tgl & Batch</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Material / Supplier</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">Qty Datang</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse ($inspections as $index => $item)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-6 py-4 text-xs text-slate-500">{{ $inspections->firstItem() + $index }}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-slate-700 dark:text-slate-300">
                                    {{ $item->inspection_date->format('d M Y') }}
                                </div>
                                <div class="text-xs text-slate-500 font-mono">{{ $item->batch_no ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-slate-800 dark:text-white">{{ $item->material_name }}
                                </div>
                                <div class="text-xs text-slate-500">{{ $item->supplier_name ?? 'No Supplier' }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="font-bold text-slate-700 dark:text-slate-300">{{ number_format($item->qty_received) }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($item->status == 'approved')
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                        APPROVED
                                    </span>
                                @elseif($item->status == 'rejected')
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-700 border border-rose-200">
                                        REJECTED
                                    </span>
                                @else
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700 border border-amber-200">
                                        ON HOLD
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500 italic truncate max-w-xs">
                                {{ $item->remarks ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">Belum ada data IQC.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700">
            {{ $inspections->links() }}
        </div>
    </div>

    {{-- MODAL INPUT NEW IQC --}}
    <div id="modalNewIQC" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                onclick="document.getElementById('modalNewIQC').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <form action="{{ route('iqc.store') }}" method="POST">
                    @csrf
                    <div class="bg-white dark:bg-slate-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-slate-900 dark:text-white mb-4" id="modal-title">Input
                            IQC Baru</h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tanggal
                                    Inspeksi</label>
                                <input type="text" name="inspection_date" class="saas-input date-picker"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama
                                    Material</label>
                                <input type="text" name="material_name" class="saas-input"
                                    placeholder="Contoh: Resin PP, Pewarna Merah" required>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Supplier</label>
                                    <input type="text" name="supplier_name" class="saas-input"
                                        placeholder="Nama Vendor">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">No.
                                        Batch</label>
                                    <input type="text" name="batch_no" class="saas-input" placeholder="Lot No.">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Qty
                                        Datang</label>
                                    <input type="number" step="0.01" name="qty_received" class="saas-input"
                                        required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Qty
                                        Reject</label>
                                    <input type="number" step="0.01" name="qty_rejected" class="saas-input"
                                        value="0">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Status
                                    Akhir</label>
                                <select name="status" class="saas-input">
                                    <option value="approved">APPROVED (Lolos)</option>
                                    <option value="rejected">REJECTED (Tolak)</option>
                                    <option value="hold">HOLD (Tahan)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Catatan /
                                    Remarks</label>
                                <textarea name="remarks" class="saas-input h-20 pt-2" placeholder="Keterangan tambahan..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-brand-600 text-base font-medium text-white hover:bg-brand-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Simpan Data
                        </button>
                        <button type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-xl border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            onclick="document.getElementById('modalNewIQC').classList.add('hidden')">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('load', function() {
            if (typeof flatpickr !== 'undefined') {
                flatpickr(".date-picker", {
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "d M Y",
                    allowInput: true
                });
            }
        });
    </script>
@endsection
