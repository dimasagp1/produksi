@extends('layouts.app')

@section('title', 'Sparepart Management')

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

    {{-- STATS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div
            class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Total Item</p>
                <h3 class="text-3xl font-extrabold text-slate-800 dark:text-white mt-1">{{ $totalItems }} <span
                        class="text-sm font-normal text-slate-400">Jenis</span></h3>
            </div>
            <div
                class="h-12 w-12 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center text-indigo-600">
                <i class="fa-solid fa-boxes-stacked text-xl"></i>
            </div>
        </div>
        <div
            class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Stok Kritis</p>
                <h3 class="text-3xl font-extrabold text-rose-600 mt-1">{{ $lowStock }} <span
                        class="text-sm font-normal text-slate-400">Item</span></h3>
                <p class="text-[10px] text-slate-400 mt-1">Perlu Restock</p>
            </div>
            <div class="h-12 w-12 rounded-xl bg-rose-50 dark:bg-rose-900/20 flex items-center justify-center text-rose-600">
                <i class="fa-solid fa-triangle-exclamation text-xl"></i>
            </div>
        </div>
        <div
            class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Pemakaian Bulan Ini</p>
                <h3 class="text-3xl font-extrabold text-blue-600 mt-1">{{ number_format($totalOutThisMonth) }} <span
                        class="text-sm font-normal text-slate-400">Pcs</span></h3>
            </div>
            <div class="h-12 w-12 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600">
                <i class="fa-solid fa-dolly text-xl"></i>
            </div>
        </div>
    </div>

    {{-- TABS & TOOLBAR --}}
    <div x-data="{ activeTab: 'stock' }">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
            <div class="flex gap-2 bg-slate-100 dark:bg-slate-700 p-1 rounded-xl">
                <button @click="activeTab = 'stock'"
                    :class="activeTab === 'stock' ? 'bg-white dark:bg-slate-600 shadow text-brand-600 dark:text-white' :
                        'text-slate-500 hover:text-slate-700'"
                    class="px-4 py-2 rounded-lg text-sm font-bold transition-all">
                    Stok Gudang
                </button>
                <button @click="activeTab = 'history'"
                    :class="activeTab === 'history' ? 'bg-white dark:bg-slate-600 shadow text-brand-600 dark:text-white' :
                        'text-slate-500 hover:text-slate-700'"
                    class="px-4 py-2 rounded-lg text-sm font-bold transition-all">
                    Riwayat Transaksi
                </button>
            </div>

            <div class="flex gap-2">
                <button onclick="document.getElementById('modalTransaction').classList.remove('hidden')"
                    class="h-10 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-md transition-all flex items-center gap-2">
                    <i class="fa-solid fa-right-left"></i> Catat Transaksi
                </button>
                <button onclick="document.getElementById('modalNewPart').classList.remove('hidden')"
                    class="h-10 px-4 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow-md transition-all flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> Item Baru
                </button>
            </div>
        </div>

        {{-- TAB 1: STOK GUDANG --}}
        <div x-show="activeTab === 'stock'"
            class="bg-white dark:bg-slate-800 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="p-4 border-b border-slate-100 dark:border-slate-700 flex justify-end">
                <form action="{{ route('sparepart.index') }}" method="GET">
                    <div class="relative w-64">
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="saas-input h-9 !pl-9 text-xs" placeholder="Cari Part Number / Nama...">
                        <i class="fa-solid fa-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                    </div>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                    <thead class="bg-slate-50/80 dark:bg-slate-700/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Part Number</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Nama Sparepart</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Lokasi</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">Stok</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse ($spareparts as $item)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                <td class="px-6 py-4 text-sm font-mono font-bold text-slate-600 dark:text-slate-300">
                                    {{ $item->part_number }}</td>
                                <td class="px-6 py-4 text-sm font-bold text-slate-800 dark:text-white">{{ $item->name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $item->location ?? '-' }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="text-sm font-bold {{ $item->current_stock <= $item->min_stock ? 'text-rose-600' : 'text-slate-700 dark:text-slate-300' }}">
                                        {{ number_format($item->current_stock) }} <span
                                            class="text-xs font-normal text-slate-400">{{ $item->unit }}</span>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($item->current_stock <= $item->min_stock)
                                        <span
                                            class="px-2 py-1 rounded text-[10px] font-bold uppercase bg-rose-100 text-rose-600 animate-pulse">Low
                                            Stock</span>
                                    @else
                                        <span
                                            class="px-2 py-1 rounded text-[10px] font-bold uppercase bg-emerald-100 text-emerald-600">Aman</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400">Data sparepart kosong.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700">{{ $spareparts->links() }}</div>
        </div>

        {{-- TAB 2: RIWAYAT --}}
        <div x-show="activeTab === 'history'"
            class="bg-white dark:bg-slate-800 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 overflow-hidden"
            style="display: none;">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                    <thead class="bg-slate-50/80 dark:bg-slate-700/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Tanggal</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Sparepart</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">Tipe</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">Qty</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Penggunaan / Ref</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">PIC</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse ($history as $trx)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $trx->date->format('d M Y') }}</td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-slate-800 dark:text-white">
                                        {{ $trx->sparepart->name }}</div>
                                    <div class="text-xs text-slate-400">{{ $trx->sparepart->part_number }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($trx->type == 'in')
                                        <span
                                            class="px-2 py-1 rounded text-[10px] font-bold uppercase bg-emerald-100 text-emerald-600">Masuk</span>
                                    @else
                                        <span
                                            class="px-2 py-1 rounded text-[10px] font-bold uppercase bg-amber-100 text-amber-600">Keluar</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-slate-700">
                                    {{ $trx->quantity }}
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    @if ($trx->machine)
                                        <span class="block text-xs font-bold text-brand-600">Mesin:
                                            {{ $trx->machine->name }}</span>
                                    @endif
                                    {{ $trx->description ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $trx->pic }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400">Belum ada transaksi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700">{{ $history->links() }}</div>
        </div>
    </div>

    {{-- MODAL TAMBAH SPAREPART --}}
    <div id="modalNewPart" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75"
                onclick="document.getElementById('modalNewPart').classList.add('hidden')"></div>
            <div
                class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <form action="{{ route('sparepart.store') }}" method="POST">
                    @csrf
                    <div class="bg-white dark:bg-slate-800 px-6 py-5">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Tambah Master Sparepart</h3>
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Part
                                        Number</label>
                                    <input type="text" name="part_number" class="saas-input" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama
                                        Part</label>
                                    <input type="text" name="name" class="saas-input" required>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Stok
                                        Awal</label>
                                    <input type="number" name="current_stock" class="saas-input" value="0">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Min.
                                        Stok (Alert)</label>
                                    <input type="number" name="min_stock" class="saas-input" value="5">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Satuan</label>
                                    <input type="text" name="unit" class="saas-input"
                                        placeholder="Pcs, Set, Liter">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Lokasi
                                        Rak</label>
                                    <input type="text" name="location" class="saas-input" placeholder="Contoh: A-01">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-700/50 px-6 py-4 flex justify-end gap-3">
                        <button type="button" onclick="document.getElementById('modalNewPart').classList.add('hidden')"
                            class="px-4 py-2 border rounded-xl">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-brand-600 text-white rounded-xl">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL TRANSAKSI (PEMAKAIAN / RESTOCK) --}}
    <div id="modalTransaction" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75"
                onclick="document.getElementById('modalTransaction').classList.add('hidden')"></div>
            <div
                class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <form action="{{ route('sparepart.transaction') }}" method="POST">
                    @csrf
                    <div class="bg-white dark:bg-slate-800 px-6 py-5">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Catat Transaksi Sparepart</h3>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Jenis
                                Transaksi</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="type" value="out" checked
                                        class="text-brand-600 focus:ring-brand-500" onclick="toggleMachine(true)">
                                    <span class="font-bold text-rose-600">Barang Keluar (Pemakaian)</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="type" value="in"
                                        class="text-brand-600 focus:ring-brand-500" onclick="toggleMachine(false)">
                                    <span class="font-bold text-emerald-600">Barang Masuk (Restock)</span>
                                </label>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Pilih
                                    Sparepart</label>
                                <select name="sparepart_id" class="saas-input" required>
                                    <option value="">-- Pilih --</option>
                                    @foreach ($spareparts as $item)
                                        <option value="{{ $item->id }}">{{ $item->part_number }} -
                                            {{ $item->name }} (Stok: {{ $item->current_stock }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Jumlah</label>
                                    <input type="number" name="quantity" class="saas-input" required min="1">
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tanggal</label>
                                    <input type="text" name="date" class="saas-input date-picker"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>

                            <div id="machineField">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Untuk
                                    Mesin</label>
                                <select name="machine_id" class="saas-input">
                                    <option value="">-- Pilih Mesin (Opsional) --</option>
                                    @foreach ($machines as $m)
                                        <option value="{{ $m->id }}">{{ $m->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Keterangan
                                    / No WO</label>
                                <input type="text" name="description" class="saas-input"
                                    placeholder="Contoh: WO-2601-001">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">PIC (Siapa
                                    yang ambil)</label>
                                <input type="text" name="pic" class="saas-input" required>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-700/50 px-6 py-4 flex justify-end gap-3">
                        <button type="button"
                            onclick="document.getElementById('modalTransaction').classList.add('hidden')"
                            class="px-4 py-2 border rounded-xl">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-brand-600 text-white rounded-xl">Simpan
                            Transaksi</button>
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

        function toggleMachine(show) {
            const el = document.getElementById('machineField');
            if (show) {
                el.classList.remove('hidden');
            } else {
                el.classList.add('hidden');
                el.querySelector('select').value = "";
            }
        }
    </script>
@endsection
