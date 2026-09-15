@extends('layouts.app')

@section('title', 'Certificate of Analysis (COA)')

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

    {{-- TOOLBAR --}}
    <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <button onclick="openModal()"
                class="h-10 px-4 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow-md transition-all flex items-center gap-2">
                <i class="fa-solid fa-file-signature"></i> Buat Sertifikat Baru
            </button>

            <form action="{{ route('coa.index') }}" method="GET" class="flex gap-2">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" class="saas-input h-10 !pl-9"
                        placeholder="Cari No COA / Batch...">
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
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">No. COA</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Produk & Batch</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Customer</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Tgl Terbit</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse ($coas as $coa)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-brand-600 font-mono">{{ $coa->coa_code }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-slate-800 dark:text-white">
                                    {{ $coa->batch->product->name ?? '-' }}</div>
                                <div class="text-xs text-slate-500 font-mono">Batch: {{ $coa->batch->batch_code }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                {{ $coa->customer_name ?? 'Umum' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                {{ \Carbon\Carbon::parse($coa->report_date)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('coa.print', $coa->id) }}" target="_blank"
                                    class="inline-flex items-center gap-1 px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition-colors">
                                    <i class="fa-solid fa-print"></i> Cetak
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">Belum ada COA diterbitkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700">{{ $coas->links() }}</div>
    </div>

    {{-- MODAL INPUT --}}
    <div id="modalCoa" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75"
                onclick="document.getElementById('modalCoa').classList.add('hidden')"></div>
            <div
                class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl w-full">
                <form action="{{ route('coa.store') }}" method="POST">
                    @csrf
                    <div class="bg-white dark:bg-slate-800 px-6 py-5">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Terbitkan COA Baru</h3>

                        {{-- Header Info --}}
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Pilih
                                    Batch</label>
                                <select name="batch_id" class="saas-input" required>
                                    @foreach ($batches as $batch)
                                        <option value="{{ $batch->id }}">{{ $batch->batch_code }} -
                                            {{ $batch->product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Customer
                                    (Opsional)</label>
                                <input type="text" name="customer_name" class="saas-input" placeholder="Nama Pelanggan">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tgl
                                    Produksi</label>
                                <input type="text" name="manufacture_date" class="saas-input date-picker" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tgl
                                    Kadaluarsa</label>
                                <input type="text" name="expiry_date" class="saas-input date-picker" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tgl
                                    Laporan</label>
                                <input type="text" name="report_date" class="saas-input date-picker"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>

                        {{-- Dynamic Items --}}
                        <div class="mb-4">
                            <div class="flex justify-between items-center mb-2">
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">Hasil
                                    Analisa</label>
                                <button type="button" onclick="addItem()"
                                    class="text-xs bg-blue-50 text-blue-600 px-2 py-1 rounded hover:bg-blue-100 font-bold">+
                                    Tambah Parameter</button>
                            </div>

                            <div class="bg-slate-50 dark:bg-slate-900/50 p-3 rounded-xl">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="text-left text-slate-500">
                                            <th class="pb-2 w-1/4">Parameter</th>
                                            <th class="pb-2 w-1/5">Metode</th>
                                            <th class="pb-2 w-1/4">Spesifikasi</th>
                                            <th class="pb-2 w-1/4">Hasil</th>
                                            <th class="pb-2"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsContainer">
                                        {{-- Row Default --}}
                                        <tr>
                                            <td class="p-1"><input type="text" name="items[0][parameter]"
                                                    class="saas-input h-8 text-xs" placeholder="Misal: pH" required></td>
                                            <td class="p-1"><input type="text" name="items[0][method]"
                                                    class="saas-input h-8 text-xs" placeholder="ASTM..."></td>
                                            <td class="p-1"><input type="text" name="items[0][specification]"
                                                    class="saas-input h-8 text-xs" placeholder="6.0 - 8.0"></td>
                                            <td class="p-1"><input type="text" name="items[0][result]"
                                                    class="saas-input h-8 text-xs" placeholder="7.2"></td>
                                            <td class="p-1 text-center"><button type="button"
                                                    onclick="this.closest('tr').remove()" class="text-rose-500"><i
                                                        class="fa-solid fa-trash"></i></button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Disetujui
                                    Oleh (QA Mgr)</label>
                                <input type="text" name="approver_name" class="saas-input" required>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Catatan</label>
                                <input type="text" name="remarks" class="saas-input">
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-700/50 px-6 py-4 flex justify-end gap-3">
                        <button type="button" onclick="document.getElementById('modalCoa').classList.add('hidden')"
                            class="px-4 py-2 border border-slate-300 rounded-xl text-slate-700 hover:bg-white transition">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-brand-600 text-white rounded-xl hover:bg-brand-700 transition">Terbitkan</button>
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

        function openModal() {
            document.getElementById('modalCoa').classList.remove('hidden');
        }

        let itemIndex = 1;

        function addItem() {
            const row = `
                <tr>
                    <td class="p-1"><input type="text" name="items[${itemIndex}][parameter]" class="saas-input h-8 text-xs" required></td>
                    <td class="p-1"><input type="text" name="items[${itemIndex}][method]" class="saas-input h-8 text-xs"></td>
                    <td class="p-1"><input type="text" name="items[${itemIndex}][specification]" class="saas-input h-8 text-xs"></td>
                    <td class="p-1"><input type="text" name="items[${itemIndex}][result]" class="saas-input h-8 text-xs"></td>
                    <td class="p-1 text-center"><button type="button" onclick="this.closest('tr').remove()" class="text-rose-500"><i class="fa-solid fa-trash"></i></button></td>
                </tr>
            `;
            document.getElementById('itemsContainer').insertAdjacentHTML('beforeend', row);
            itemIndex++;
        }
    </script>
@endsection
