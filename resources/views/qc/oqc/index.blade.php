@extends('layouts.app')

@section('title', 'OQC - Outgoing Quality Control')

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
        <div
            class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Total Inspeksi</p>
                <h3 class="text-3xl font-extrabold text-slate-800 dark:text-white mt-1">{{ number_format($totalCheck) }}</h3>
                <p class="text-[10px] text-slate-400 mt-1">Final Check</p>
            </div>
            <div class="h-12 w-12 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600">
                <i class="fa-solid fa-box-open text-xl"></i>
            </div>
        </div>
        <div
            class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Lolos QC (Pass)</p>
                <h3 class="text-3xl font-extrabold text-emerald-600 mt-1">{{ number_format($totalPass) }}</h3>
                <p class="text-[10px] text-slate-400 mt-1">Siap Kirim</p>
            </div>
            <div
                class="h-12 w-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center text-emerald-600">
                <i class="fa-solid fa-check-double text-xl"></i>
            </div>
        </div>
        <div
            class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Reject Akhir</p>
                <h3 class="text-3xl font-extrabold text-rose-600 mt-1">{{ number_format($totalReject) }}</h3>
                <p class="text-[10px] text-slate-400 mt-1">Tahan di Gudang</p>
            </div>
            <div class="h-12 w-12 rounded-xl bg-rose-50 dark:bg-rose-900/20 flex items-center justify-center text-rose-600">
                <i class="fa-solid fa-hand text-xl"></i>
            </div>
        </div>
    </div>

    {{-- TOOLBAR --}}
    <div
        class="bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <button onclick="document.getElementById('modalNewOQC').classList.remove('hidden')"
                class="h-10 px-4 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow-md transition-all flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Input OQC
            </button>

            <form action="{{ route('oqc.index') }}" method="GET"
                class="flex flex-col sm:flex-row gap-2 w-full md:w-auto items-end sm:items-center">
                <div class="relative w-full sm:w-40">
                    <input type="text" name="date" class="saas-input h-10 date-picker cursor-pointer"
                        value="{{ $date }}" placeholder="Tanggal">
                </div>
                <div class="relative w-full sm:max-w-xs">
                    <input type="text" name="search" value="{{ request('search') }}" class="saas-input h-10 !pl-9"
                        placeholder="Cari Batch/Produk...">
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
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Batch & Produk</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">Packaging</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">Labeling</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">Status Akhir</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse ($inspections as $item)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-6 py-4 text-sm font-mono text-slate-600 dark:text-slate-300">
                                {{ \Carbon\Carbon::parse($item->inspection_time)->format('H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-brand-600 font-mono">
                                    {{ $item->batch->batch_code ?? '-' }}</div>
                                <div class="text-xs text-slate-500">{{ $item->product->name }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($item->packaging_status == 'ok')
                                    <i class="fa-solid fa-check text-emerald-500"></i>
                                @else
                                    <i class="fa-solid fa-xmark text-rose-500"></i>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($item->labeling_status == 'ok')
                                    <i class="fa-solid fa-check text-emerald-500"></i>
                                @else
                                    <i class="fa-solid fa-xmark text-rose-500"></i>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($item->status == 'pass')
                                    <span
                                        class="px-3 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">PASS</span>
                                @elseif($item->status == 'reject')
                                    <span
                                        class="px-3 py-1 rounded-lg text-xs font-bold bg-rose-100 text-rose-700 border border-rose-200">REJECT</span>
                                @else
                                    <span
                                        class="px-3 py-1 rounded-lg text-xs font-bold bg-amber-100 text-amber-700 border border-amber-200">HOLD</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500 italic max-w-xs truncate">
                                {{ $item->remarks ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">Belum ada data OQC.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700">{{ $inspections->links() }}</div>
    </div>

    {{-- MODAL INPUT --}}
    <div id="modalNewOQC" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75"
                onclick="document.getElementById('modalNewOQC').classList.add('hidden')"></div>
            <div
                class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <form action="{{ route('oqc.store') }}" method="POST">
                    @csrf
                    <div class="bg-white dark:bg-slate-800 px-4 pt-5 pb-4 sm:p-6">
                        <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-4">Input Final Check (OQC)</h3>
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
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Pilih Batch
                                    Produksi</label>
                                <select name="batch_id" class="saas-input" required>
                                    @foreach ($batches as $batch)
                                        <option value="{{ $batch->id }}">
                                            {{ $batch->batch_code }} - {{ $batch->product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Cek
                                        Kemasan</label>
                                    <select name="packaging_status" class="saas-input">
                                        <option value="ok">OK - Baik</option>
                                        <option value="ng">NG - Rusak/Kotor</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Cek
                                        Label</label>
                                    <select name="labeling_status" class="saas-input">
                                        <option value="ok">OK - Sesuai</option>
                                        <option value="ng">NG - Salah/Miring</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Jml
                                        Sample</label>
                                    <input type="number" name="sample_size" class="saas-input" value="10">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Jml
                                        Cacat</label>
                                    <input type="number" name="defect_qty" class="saas-input" value="0">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Keputusan
                                    Akhir</label>
                                <select name="status" class="saas-input font-bold">
                                    <option value="pass" class="text-emerald-600">✅ PASS - Lolos</option>
                                    <option value="reject" class="text-rose-600">❌ REJECT - Tolak</option>
                                    <option value="hold" class="text-amber-600">⚠️ HOLD - Tahan</option>
                                </select>
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
                            onclick="document.getElementById('modalNewOQC').classList.add('hidden')">Batal</button>
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
