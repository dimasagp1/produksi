@extends('layouts.app')

@section('title', 'Bill of Materials (BOM)')

@section('content')
    <style>
        .custom-scroll::-webkit-scrollbar {
            height: 6px;
            width: 6px;
        }

        .custom-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 20px;
        }

        .dark .custom-scroll::-webkit-scrollbar-thumb {
            background-color: #475569;
        }

        .saas-input {
            height: 46px;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 0 16px;
            width: 100%;
            background-color: #fff;
            font-size: 0.9rem;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .dark .saas-input {
            background-color: #0f172a;
            border-color: #334155;
            color: #f1f5f9;
        }

        .saas-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }
    </style>

    {{-- PHP Logic untuk Stats Sederhana --}}
    @php
        $totalProducts =
            $products instanceof \Illuminate\Pagination\LengthAwarePaginator ? $products->total() : $products->count();
        $totalMaterials =
            $materials instanceof \Illuminate\Pagination\LengthAwarePaginator
                ? $materials->total()
                : $materials->count();

        $lowStockMaterials = 0;
        foreach ($materials as $m) {
            if ($m->stock < 10) {
                $lowStockMaterials++;
            }
        }
    @endphp

    <div x-data="{ tab: '{{ request('materials_page') || request('low_stock') ? 'materials' : 'products' }}' }" class="flex flex-col gap-8 fade-in-up">

        {{-- 1. HEADER SECTION --}}
        <div>
            <h1 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight">Bill of Materials</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola struktur produk, formula, dan master bahan
                baku.</p>
        </div>

        {{-- 2. SUMMARY CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Card 1: Total Produk --}}
            <div @click="tab = 'products'"
                class="p-6 rounded-[1.5rem] border shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer group relative overflow-hidden"
                :class="tab === 'products'
                    ?
                    'bg-brand-50 dark:bg-brand-900/10 border-brand-200 dark:border-brand-800 ring-1 ring-brand-500' :
                    'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700'">
                <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fa-solid fa-box-open text-6xl text-brand-600"></i>
                </div>
                <div class="relative z-10">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Total Produk</p>
                    <h3 class="text-3xl font-black text-slate-800 dark:text-white">{{ number_format($totalProductsCount) }}
                    </h3>
                    <div class="mt-4 flex items-center text-xs font-medium"
                        :class="tab === 'products' ? 'text-brand-700 dark:text-brand-400' : 'text-slate-500'">
                        <span>Lihat detail produk</span>
                        <i class="fa-solid fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </div>
            </div>

            {{-- Card 2: Total Material --}}
            {{-- PERBAIKAN: Klik card ini akan RESET filter low_stock --}}
            <div @click="window.location.href='{{ route('bom.index', ['materials_page' => 1]) }}'"
                class="p-6 rounded-[1.5rem] border shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer group relative overflow-hidden"
                :class="tab === 'materials' && !{{ request('low_stock') ? 'true' : 'false' }} ?
                    'bg-emerald-50 dark:bg-emerald-900/10 border-emerald-200 dark:border-emerald-800 ring-1 ring-emerald-500' :
                    'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700'">
                <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fa-solid fa-flask text-6xl text-emerald-600"></i>
                </div>
                <div class="relative z-10">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Total Bahan Baku</p>
                    <h3 class="text-3xl font-black text-slate-800 dark:text-white">{{ number_format($totalMaterialsCount) }}
                    </h3>
                    <div class="mt-4 flex items-center text-xs font-medium"
                        :class="tab === 'materials' && !{{ request('low_stock') ? 'true' : 'false' }} ?
                            'text-emerald-700 dark:text-emerald-400' : 'text-slate-500'">
                        <span>Lihat master material</span>
                        <i class="fa-solid fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </div>
            </div>

            {{-- Card 3: Alert Stok --}}
            {{-- PERBAIKAN: Menambahkan logic Active State (Warna Amber) --}}
            <div @click="window.location.href='{{ route('bom.index', ['low_stock' => 1]) }}'"
                class="p-6 rounded-[1.5rem] border shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer group relative overflow-hidden"
                :class="{{ request('low_stock') ? 'true' : 'false' }}
                    ?
                    'bg-amber-50 dark:bg-amber-900/10 border-amber-200 dark:border-amber-800 ring-1 ring-amber-500' :
                    'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700'">
                <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fa-solid fa-triangle-exclamation text-6xl text-amber-500"></i>
                </div>
                <div class="relative z-10">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Stok Menipis</p>
                    <h3 class="text-3xl font-black text-slate-800 dark:text-white">{{ $lowStockCount }}</h3>
                    <div class="mt-4 flex items-center text-xs font-medium"
                        :class="{{ request('low_stock') ? 'true' : 'false' }} ? 'text-amber-700 dark:text-amber-400' :
                            'text-amber-600 dark:text-amber-400'">
                        <span>Item perlu restock</span>
                        <i class="fa-solid fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. LIVE SEARCH (DIPINDAHKAN KE SINI) --}}
        <div class="flex justify-start">
            <form action="{{ route('bom.index') }}" method="GET" class="relative w-full md:w-96 group">

                {{-- PERBAIKAN: Gunakan !pl-12 (Tanda seru penting agar tidak tertimpa style lain) --}}
                <input type="text" name="search" value="{{ $search ?? '' }}"
                    class="saas-input !pl-12 rounded-2xl shadow-sm border-slate-200 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all"
                    placeholder="Cari produk, kode, atau material..." onchange="this.form.submit()">

                {{-- Ikon Search --}}
                <div
                    class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-brand-500 transition-colors">
                    <i class="fa-solid fa-search text-lg"></i>
                </div>
            </form>
        </div>
        {{-- 4. TABS NAVIGATION --}}
        <div class="flex items-center justify-between">
            <div class="flex p-1.5 bg-slate-100 dark:bg-slate-800/50 rounded-xl w-fit">
                <button @click="tab = 'products'"
                    class="px-6 py-2.5 rounded-lg text-sm font-bold transition-all duration-300 flex items-center gap-2"
                    :class="tab === 'products'
                        ?
                        'bg-white dark:bg-slate-700 text-brand-600 dark:text-brand-400 shadow-sm ring-1 ring-black/5 dark:ring-white/10' :
                        'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'">
                    <i class="fa-solid fa-layer-group"></i>
                    <span>Produk & BOM</span>
                </button>
                <button @click="tab = 'materials'"
                    class="px-6 py-2.5 rounded-lg text-sm font-bold transition-all duration-300 flex items-center gap-2"
                    :class="tab === 'materials'
                        ?
                        'bg-white dark:bg-slate-700 text-brand-600 dark:text-brand-400 shadow-sm ring-1 ring-black/5 dark:ring-white/10' :
                        'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'">
                    <i class="fa-solid fa-flask"></i>
                    <span>Bahan Baku</span>
                </button>
            </div>
        </div>

        {{-- TAB CONTENT 1: PRODUCT LIST --}}
        <div x-show="tab === 'products'" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">

            @if ($products->isEmpty())
                <div class="flex flex-col items-center justify-center py-20 text-center">
                    <div class="bg-slate-50 dark:bg-slate-800 rounded-full p-6 mb-4">
                        <i class="fa-solid fa-box-open text-4xl text-slate-300 dark:text-slate-600"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-700 dark:text-slate-300">Data Produk Tidak Ditemukan</h3>
                    <p class="text-slate-500 text-sm">Coba kata kunci lain atau tambahkan produk baru.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6 mb-6">
                    @foreach ($products as $product)
                        <div
                            class="group bg-white dark:bg-slate-800 rounded-[1.5rem] border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between h-full overflow-hidden">
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-3">
                                    <div
                                        class="h-10 w-10 rounded-xl bg-brand-50 dark:bg-brand-900/20 flex items-center justify-center text-brand-600 dark:text-brand-400 mb-2">
                                        <i class="fa-solid fa-box"></i>
                                    </div>
                                    @if ($product->bom_items_count > 0)
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide bg-emerald-50 text-emerald-600 border border-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            Ready
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide bg-slate-100 text-slate-500 border border-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700">
                                            Empty
                                        </span>
                                    @endif
                                </div>
                                <h3 class="font-bold text-lg text-slate-800 dark:text-white line-clamp-1"
                                    title="{{ $product->name }}">
                                    {{ $product->name }}
                                </h3>
                                <p class="text-xs font-mono text-slate-400 mt-1 mb-4">{{ $product->code ?? 'NO-CODE' }}</p>
                                <div
                                    class="flex items-center gap-3 text-xs font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-900/50 p-3 rounded-xl border border-slate-100 dark:border-slate-700/50">
                                    <div class="flex items-center gap-1.5">
                                        <i class="fa-solid fa-cubes text-slate-400"></i>
                                        <span>{{ $product->bom_items_count }} Komponen</span>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="p-4 bg-slate-50/50 dark:bg-slate-900/30 border-t border-slate-100 dark:border-slate-700">
                                <a href="{{ route('bom.show', $product->id) }}"
                                    class="w-full inline-flex justify-center items-center gap-2 py-2.5 rounded-xl bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 font-bold text-sm shadow-sm hover:bg-brand-50 hover:text-brand-600 hover:border-brand-200 dark:hover:bg-brand-900/20 dark:hover:text-brand-400 dark:hover:border-brand-800 transition-all active:scale-95">
                                    <i class="fa-solid fa-pen-to-square"></i> Kelola Struktur
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- PAGINATION PRODUCTS --}}
                <div class="mt-6">
                    {{ $products->appends(['materials_page' => request('materials_page'), 'search' => request('search')])->links() }}
                </div>
            @endif
        </div>

        {{-- TAB CONTENT 2: MATERIALS LIST --}}
        <div x-show="tab === 'materials'" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            class="bg-white dark:bg-slate-800 rounded-[1.5rem] shadow-xl shadow-slate-200/40 dark:shadow-slate-900/40 border border-slate-200 dark:border-slate-700 overflow-hidden">

            <div
                class="p-5 border-b border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h3 class="font-bold text-lg text-slate-800 dark:text-white">Master Bahan Baku</h3>
                    <p class="text-xs text-slate-500 mt-1">Daftar raw material yang tersedia untuk produksi.</p>
                </div>
                <button onclick="document.getElementById('modalMaterial').classList.remove('hidden')"
                    class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-brand-500/20 transition-all active:scale-95 flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> Material Baru
                </button>
            </div>

            <div class="overflow-x-auto custom-scroll">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                    <thead class="bg-slate-50 dark:bg-slate-900/50">
                        <tr>
                            {{-- KOLOM NO BARU --}}
                            <th
                                class="px-6 py-4 text-center text-[11px] font-extrabold text-slate-500 uppercase tracking-wider w-16">
                                No</th>

                            <th
                                class="px-6 py-4 text-left text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">
                                Kode</th>
                            <th
                                class="px-6 py-4 text-left text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">
                                Nama Material</th>
                            <th
                                class="px-6 py-4 text-left text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">
                                Satuan</th>
                            <th
                                class="px-6 py-4 text-right text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">
                                Std Cost</th>
                            <th
                                class="px-6 py-4 text-center text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">
                                Stok</th>
                            <th
                                class="px-6 py-4 text-center text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($materials as $index => $mat)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-colors">
                                {{-- LOGIKA NOMOR URUT PAGINATION --}}
                                <td class="px-6 py-4 text-center text-xs text-slate-500 font-bold">
                                    {{ ($materials->currentPage() - 1) * $materials->perPage() + $loop->iteration }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="font-mono text-xs font-bold text-brand-600 dark:text-brand-400 bg-brand-50 dark:bg-brand-900/20 px-2 py-1 rounded-lg border border-brand-100 dark:border-brand-800">
                                        {{ $mat->material_code }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-bold text-slate-700 dark:text-slate-200">
                                    {{ $mat->name }}
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="text-xs font-bold text-slate-500 bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded">{{ $mat->unit }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-right font-mono text-slate-600 dark:text-slate-400">
                                    Rp {{ number_format($mat->std_cost, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="text-sm font-bold {{ $mat->stock > 0 ? 'text-emerald-600' : 'text-rose-500' }}">
                                        {{ number_format($mat->stock) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button class="text-slate-400 hover:text-brand-600 transition-colors p-2">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center">
                                        <i class="fa-regular fa-folder-open text-2xl mb-2 opacity-50"></i>
                                        <span>Data material tidak ditemukan.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION MATERIALS --}}
            <div class="p-4 border-t border-slate-100 dark:border-slate-700">
                {{ $materials->appends(['products_page' => request('products_page'), 'search' => request('search')])->links() }}
            </div>
        </div>
    </div>

    {{-- MODAL ADD MATERIAL --}}
    <div id="modalMaterial" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        {{-- Backdrop with Blur --}}
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
            onclick="document.getElementById('modalMaterial').classList.add('hidden')"></div>

        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-100 dark:border-slate-700">

                    {{-- Modal Header --}}
                    <div
                        class="bg-white dark:bg-slate-800 px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Material Baru</h3>
                        <button type="button" onclick="document.getElementById('modalMaterial').classList.add('hidden')"
                            class="text-slate-400 hover:text-slate-500 dark:hover:text-slate-300 transition-colors">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>

                    <form action="{{ route('bom.material.store') }}" method="POST">
                        @csrf
                        <div class="px-6 py-6 space-y-5">
                            {{-- Kode Material (Wajib) --}}
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                    Kode Material <span class="text-rose-500" title="Wajib diisi">*</span>
                                </label>
                                <input type="text" name="material_code"
                                    class="saas-input font-mono placeholder:font-sans uppercase"
                                    placeholder="Contoh: RM-PVC-001" required>
                            </div>

                            {{-- Nama Material (Wajib) --}}
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                    Nama Material <span class="text-rose-500" title="Wajib diisi">*</span>
                                </label>
                                <input type="text" name="name" class="saas-input" placeholder="Nama bahan baku..."
                                    required>
                            </div>

                            <div class="grid grid-cols-2 gap-5">
                                {{-- Satuan (Wajib) --}}
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                        Satuan <span class="text-rose-500" title="Wajib diisi">*</span>
                                    </label>
                                    <input type="text" name="unit" class="saas-input" placeholder="Kg / L / Pcs"
                                        required>
                                </div>

                                {{-- Std Cost (Opsional & Format Rupiah) --}}
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                        Std Cost <span class="text-slate-400 font-normal text-xs ml-1">(Opsional)</span>
                                    </label>
                                    <div class="relative">
                                        {{-- Label Rp (Posisi Absolute) --}}
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-slate-400 text-sm font-bold">Rp</span>
                                        </div>
                                        <input type="text" name="std_cost" class="saas-input !pl-10 rupiah-input"
                                            placeholder="0">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Modal Footer --}}
                        <div
                            class="bg-slate-50 dark:bg-slate-900/50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-slate-100 dark:border-slate-700">
                            <button type="submit"
                                class="inline-flex w-full justify-center rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-brand-500/30 hover:bg-brand-700 sm:w-auto transition-all active:scale-95">
                                Simpan
                            </button>
                            <button type="button"
                                onclick="document.getElementById('modalMaterial').classList.add('hidden')"
                                class="inline-flex w-full justify-center rounded-xl bg-white dark:bg-slate-800 px-5 py-2.5 text-sm font-bold text-slate-700 dark:text-slate-300 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700 sm:w-auto transition-all">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT FORMAT ANGKA INDONESIA --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rupiahInputs = document.querySelectorAll('.rupiah-input');

            rupiahInputs.forEach(function(input) {
                input.addEventListener('keyup', function(e) {
                    // Hapus karakter selain angka
                    let value = this.value.replace(/[^0-9]/g, '');

                    // Format ke ribuan (contoh: 1.000.000)
                    if (value) {
                        value = parseInt(value, 10).toLocaleString('id-ID');
                    }

                    this.value = value;
                });
            });
        });
    </script>
@endsection
