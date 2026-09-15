@extends('layouts.app')

@section('title', 'Master Data Produk')

@section('content')
    {{-- 1. LIBRARY PENDUKUNG --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    {{-- 2. CUSTOM CSS --}}
    <style>
        .saas-input {
            height: 44px;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 0 16px;
            font-size: 0.875rem;
            width: 100%;
            transition: all 0.2s;
            background-color: #f8fafc;
            color: #1e293b;
        }

        .dark .saas-input {
            background-color: #1e293b;
            border-color: #334155;
            color: #f1f5f9;
        }

        .saas-input:focus {
            border-color: #3b82f6;
            outline: none;
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .dark .saas-input:focus {
            background-color: #0f172a;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
        }

        .saas-input[readonly],
        .saas-input:disabled {
            background-color: #f1f5f9;
            border-color: #cbd5e1;
            color: #64748b;
            cursor: not-allowed;
        }

        .dark .saas-input[readonly],
        .dark .saas-input:disabled {
            background-color: #334155;
            border-color: #475569;
            color: #94a3b8;
        }

        .saas-input.pr-12 {
            padding-right: 3rem !important;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-container .select2-selection--single {
            height: 44px !important;
            border-radius: 0.75rem !important;
            display: flex !important;
            align-items: center !important;
            background-color: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
        }

        .dark .select2-container .select2-selection--single {
            background-color: #1e293b !important;
            border-color: #334155 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 42px !important;
            right: 10px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #475569 !important;
            padding-left: 16px !important;
            font-size: 0.875rem;
        }

        .badge-status {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            padding: 2px 8px;
            border-radius: 999px;
            letter-spacing: 0.05em;
        }

        .table-row-hover:hover td {
            background-color: #f8fafc;
        }

        .dark .table-row-hover:hover td {
            background-color: rgba(30, 41, 59, 0.5);
        }

        .cell-item {
            min-height: 28px;
            display: flex;
            align-items: center;
        }
    </style>

    @php
        $statTotal = $totalProducts ?? \App\Models\Product::count();
        $statActive = $activeProducts ?? \App\Models\Product::where('status', 'aktif')->count();
        $statInactive = $inactiveProducts ?? \App\Models\Product::where('status', 'nonaktif')->count();
    @endphp

    <div class="w-full font-sans text-slate-600 dark:text-slate-300">
        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white tracking-tight">Data Produk</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Manajemen data produk, spesifikasi mesin, dan
                    kemasan.</p>
            </div>
            <div>
                <button onclick="openModal('createModal')"
                    class="inline-flex items-center px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl shadow-lg transition-all active:scale-95 transform hover:-translate-y-0.5">
                    <i class="fas fa-plus mr-2"></i> Tambah Produk
                </button>
            </div>
        </div>

        {{-- DASHBOARD CARDS --}}
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3 mb-8">
            <div
                class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 flex items-center justify-between group hover:border-brand-200 transition-colors">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Produk</p>
                    <h3 class="text-2xl font-extrabold text-slate-800 dark:text-white">{{ $statTotal }}</h3>
                </div>
                <div
                    class="h-12 w-12 rounded-xl bg-brand-50 dark:bg-brand-900/20 flex items-center justify-center text-brand-600 dark:text-brand-400 group-hover:scale-110 transition-transform">
                    <i class="fas fa-box text-xl"></i>
                </div>
            </div>
            <div
                class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 flex items-center justify-between group hover:border-emerald-200 transition-colors">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Produk Aktif</p>
                    <h3 class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ $statActive }}</h3>
                </div>
                <div
                    class="h-12 w-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
            </div>
            <div
                class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 flex items-center justify-between group hover:border-slate-300 transition-colors">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Nonaktif</p>
                    <h3 class="text-2xl font-extrabold text-slate-600 dark:text-slate-400">{{ $statInactive }}</h3>
                </div>
                <div
                    class="h-12 w-12 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-500 dark:text-slate-400 group-hover:scale-110 transition-transform">
                    <i class="fas fa-ban text-xl"></i>
                </div>
            </div>
        </div>

        {{-- TOOLBAR --}}
        <div
            class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 mb-6">
            <div class="flex flex-col xl:flex-row justify-between gap-4 items-center">
                <div class="flex flex-col sm:flex-row gap-3 w-full xl:w-auto flex-grow items-center">
                    <div class="relative w-full sm:max-w-xs group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i
                                class="fas fa-search text-slate-400 group-focus-within:text-brand-500 transition-colors"></i>
                        </div>
                        <input type="text" id="searchInput" value="{{ request('search') }}" class="saas-input !pl-12"
                            placeholder="Cari nama produk...">
                    </div>
                    <div class="relative w-full sm:w-48">
                        <select id="filterStatus" class="saas-input cursor-pointer">
                            <option value="">Semua Status</option>
                            <option value="aktif" {{ request('filter_status') == 'aktif' ? 'selected' : '' }}>Aktif
                            </option>
                            <option value="nonaktif" {{ request('filter_status') == 'nonaktif' ? 'selected' : '' }}>
                                Nonaktif
                            </option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-3 w-full xl:w-auto justify-end">
                    @if (in_array(auth()->user()->role, ['admin', 'super_admin']))
                        <button onclick="openModal('importModal')"
                            class="inline-flex items-center px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 rounded-xl text-sm font-medium hover:bg-slate-50 transition-all">
                            <i class="fas fa-file-import mr-2 text-slate-400"></i> Import
                        </button>
                    @endif
                    <div class="relative">
                        <button onclick="toggleExportMenu(event)"
                            class="inline-flex items-center px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 rounded-xl text-sm font-medium hover:bg-slate-50 transition-all active:scale-95 shadow-sm">
                            <i class="fas fa-download mr-2 text-slate-400"></i> Export <i id="exportArrow"
                                class="fas fa-chevron-down ml-2 text-xs text-slate-400 transition-transform duration-200"></i>
                        </button>
                        <div id="exportMenu"
                            class="hidden absolute right-0 mt-2 w-40 bg-white dark:bg-slate-800 rounded-xl shadow-xl ring-1 ring-black ring-opacity-5 border border-slate-100 dark:border-slate-700 z-50 transform origin-top-right transition-all duration-200">
                            <div class="py-1">
                                <a href="{{ route('products.export', 'excel') }}" target="_blank"
                                    class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-emerald-600 transition-colors"><i
                                        class="fas fa-file-excel mr-2 text-emerald-500"></i>Excel</a>
                                <a href="{{ route('products.export', 'pdf') }}" target="_blank"
                                    class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-rose-600 transition-colors"><i
                                        class="fas fa-file-pdf mr-2 text-rose-500"></i>PDF</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABLE DATA --}}
        <div id="table-data"
            class="bg-white dark:bg-slate-800 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                    <thead class="bg-slate-50/80 dark:bg-slate-700/50">
                        <tr>
                            <th
                                class="px-4 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase w-10">
                                No</th>
                            <th
                                class="px-4 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase min-w-[200px]">
                                Nama Produk</th>
                            <th
                                class="px-4 py-4 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">
                                Routing (PPIC)</th>
                            <th
                                class="px-4 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase min-w-[100px]">
                                Berat (Gr)</th>
                            <th
                                class="px-4 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase min-w-[150px]">
                                Jenis Kemasan</th>
                            <th
                                class="px-4 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase min-w-[130px]">
                                Mesin</th>
                            <th
                                class="px-4 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase min-w-[140px]">
                                Cycle Time (s)</th>
                            <th
                                class="px-4 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase min-w-[100px]">
                                Cavity</th>
                            <th
                                class="px-4 py-4 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase w-24">
                                Status</th>
                            <th
                                class="px-4 py-4 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase w-24">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse ($products as $product)
                            <tr class="table-row-hover transition-colors duration-150 align-top">
                                <td
                                    class="px-4 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400 font-medium">
                                    {{ ($products->currentPage() - 1) * $products->perPage() + $loop->iteration }}</td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-9 w-9">
                                            <div
                                                class="h-9 w-9 rounded-full bg-gradient-to-tr from-brand-100 to-brand-200 dark:from-brand-900/30 dark:to-brand-800/30 flex items-center justify-center text-brand-700 dark:text-brand-400 font-bold text-xs border border-brand-200 dark:border-brand-700">
                                                {{ substr($product->name, 0, 1) }}</div>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-bold text-slate-800 dark:text-white">
                                                {{ $product->name }}</div>
                                            <div class="text-xs text-slate-400 font-mono">
                                                {{ $product->code ?? 'NO-SKU' }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-4 align-middle text-center">
                                    @if ($product->routings_count > 0)
                                        <a href="{{ route('routing.show', $product->id) }}"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100 hover:bg-emerald-100 transition-colors text-xs font-bold">
                                            <i class="fas fa-check-circle"></i> {{ $product->routings_count }} Tahap
                                        </a>
                                    @else
                                        <a href="{{ route('routing.show', $product->id) }}"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-rose-50 text-rose-600 border border-rose-100 hover:bg-rose-100 transition-colors text-xs font-bold animate-pulse"
                                            title="Produk ini belum bisa dihitung kapasitasnya">
                                            <i class="fas fa-triangle-exclamation"></i> Set Routing
                                        </a>
                                    @endif
                                </td>

                                <td class="px-4 py-4">
                                    <div class="text-sm text-slate-600 dark:text-slate-300 font-medium">
                                        {{ $product->weight > 0 ? number_format($product->weight, 2, ',', '.') : '-' }}
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-sm text-slate-600 dark:text-slate-300">
                                        @if ($product->packagingType)
                                            {{ $product->packagingType->name }}
                                        @else
                                            <span class="text-slate-400 italic">Belum set</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    @if ($product->machines->isEmpty())
                                        <span class="text-slate-400 text-xs italic">- Belum set -</span>
                                    @else
                                        <div class="flex flex-col gap-2">
                                            @foreach ($product->machines as $machine)
                                                <div
                                                    class="cell-item text-xs font-semibold text-slate-700 dark:text-slate-200 border-b border-slate-100 dark:border-slate-700 last:border-0 pb-1 last:pb-0">
                                                    {{ $machine->name }}</div>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    @if ($product->machines->isEmpty())
                                        <span class="text-slate-400 text-xs">-</span>
                                    @else
                                        <div class="flex flex-col gap-2">
                                            @foreach ($product->machines as $machine)
                                                <div
                                                    class="cell-item text-xs border-b border-slate-100 dark:border-slate-700 last:border-0 pb-1 last:pb-0">
                                                    <span class="text-slate-400 mr-1">Std:</span>
                                                    <span
                                                        class="text-blue-600 dark:text-blue-400 font-semibold w-10">{{ $machine->pivot->cycle_time }}</span>
                                                    <span class="text-slate-300 mx-1">|</span>
                                                    <span class="text-slate-400 mr-1">Act:</span>
                                                    <span class="text-emerald-600 dark:text-emerald-400 font-bold">
                                                        {{ $machine->pivot->actual_cycle_time > 0 ? number_format($machine->pivot->actual_cycle_time, 2, ',', '.') : '-' }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    @if ($product->machines->isEmpty())
                                        <span class="text-slate-400 text-xs">-</span>
                                    @else
                                        <div class="flex flex-col gap-2">
                                            @foreach ($product->machines as $machine)
                                                <div
                                                    class="cell-item text-xs border-b border-slate-100 dark:border-slate-700 last:border-0 pb-1 last:pb-0">
                                                    <span class="text-slate-400 mr-1">Std:</span>
                                                    <span
                                                        class="text-slate-700 dark:text-slate-200 font-bold w-4">{{ $machine->pivot->cavity }}</span>
                                                    <span class="text-slate-300 mx-1">|</span>
                                                    <span class="text-slate-400 mr-1">Act:</span>
                                                    <span class="text-rose-600 dark:text-rose-400 font-bold">
                                                        {{ $machine->pivot->actual_cavity > 0 ? $machine->pivot->actual_cavity : '-' }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-center align-middle">
                                    <div class="flex flex-col items-center justify-center gap-1.5 h-full">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="sr-only peer status-toggle"
                                                data-id="{{ $product->id }}"
                                                {{ $product->status == 'aktif' ? 'checked' : '' }}>
                                            <div
                                                class="w-9 h-5 bg-slate-200 dark:bg-slate-700 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-brand-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-brand-600">
                                            </div>
                                        </label>
                                        <span
                                            class="badge-status {{ $product->status == 'aktif' ? 'text-emerald-600 bg-emerald-50 dark:bg-emerald-900/20 dark:text-emerald-400' : 'text-slate-500 bg-slate-100 dark:bg-slate-700 dark:text-slate-400' }}"
                                            id="status-label-{{ $product->id }}">{{ $product->status == 'aktif' ? 'Aktif' : 'Nonaktif' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-center align-middle">
                                    <div class="flex justify-center items-center gap-1 h-full">
                                        <button onclick="openViewModal({{ $product->id }})"
                                            class="p-2 text-slate-400 hover:text-brand-600 hover:bg-brand-50 dark:hover:bg-brand-900/20 dark:hover:text-brand-400 rounded-lg transition-colors"
                                            title="Lihat Detail"><i class="fas fa-eye"></i></button>
                                        <button onclick="openEditModal({{ $product->id }})"
                                            class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 dark:hover:text-amber-400 rounded-lg transition-colors"
                                            title="Edit Data"><i class="fas fa-pen"></i></button>
                                        <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                            class="inline-block">
                                            @csrf @method('DELETE')
                                            <button type="button" onclick="confirmDelete(this)"
                                                class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 dark:hover:text-rose-400 rounded-lg transition-colors"
                                                title="Hapus Data"><i class="fas fa-trash-alt"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-6 py-20 text-center text-slate-500 dark:text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="bg-slate-50 dark:bg-slate-700/50 rounded-full p-4 mb-3"><i
                                                class="fas fa-inbox text-3xl text-slate-300 dark:text-slate-500"></i></div>
                                        <span class="text-sm font-medium">Data produk tidak ditemukan.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div
                class="px-6 py-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/30 rounded-b-2xl">
                {{ $products->links() }}
            </div>
        </div>
    </div>

    {{-- MODAL CREATE --}}
    <div id="createModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-slate-900/60 dark:bg-slate-900/80 transition-opacity backdrop-blur-sm"
                onclick="closeModal('createModal')"></div>
            <div
                class="relative inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100 dark:border-slate-700">
                <div
                    class="bg-white dark:bg-slate-800 px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white">Tambah Produk Baru</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Isi informasi produk di bawah ini.</p>
                    </div>
                    <button onclick="closeModal('createModal')"
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"><i
                            class="fas fa-times text-lg"></i></button>
                </div>
                <div class="px-6 py-6 custom-scroll">
                    <form action="{{ route('products.store') }}" method="POST" id="createForm">
                        @csrf
                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Nama
                                Produk <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" required class="saas-input"
                                placeholder="Masukkan nama produk">
                        </div>
                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Berat
                                Standar (Gram) <span class="text-slate-400 text-xs font-normal">(Opsional)</span></label>
                            <div class="relative">
                                <input type="text" name="weight" class="saas-input decimal-input pr-12"
                                    placeholder="0">
                                <span
                                    class="absolute right-3 top-2.5 text-xs text-slate-400 font-bold bg-slate-50 dark:bg-slate-700 px-2 py-0.5 rounded">Gram</span>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1">Digunakan untuk kalkulasi otomatis purging.</p>
                        </div>
                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Jenis
                                Kemasan <span class="text-rose-500">*</span></label>
                            <select name="packaging_type_id" class="saas-input select2-modal" required
                                data-placeholder="Cari & Pilih Jenis Kemasan...">
                                <option value=""></option>
                                @foreach ($packagingTypes as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}
                                        ({{ $p->conversion_quantity }} {{ $p->content_unit }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- PERUBAHAN: Container Dinamis Mesin di CREATE --}}
                        <div class="mb-5">
                            <div class="flex justify-between items-center mb-1.5">
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Data Mesin &
                                    Standar Produksi <span class="text-rose-500">*</span></label>
                                <button type="button" onclick="addMachineRow('create')"
                                    class="text-xs px-2 py-1 bg-brand-100 text-brand-600 rounded font-bold hover:bg-brand-200 transition-colors">+
                                    Tambah Mesin</button>
                            </div>
                            <div id="createMachineContainer"></div>
                        </div>

                        <div class="mb-2">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Status
                                Awal <span class="text-rose-500">*</span></label>
                            <select name="status" class="saas-input cursor-pointer">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div
                    class="bg-slate-50 dark:bg-slate-700/50 px-6 py-4 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                    <button type="button" onclick="closeModal('createModal')"
                        class="px-5 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-xl text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-600 transition-all">Batal</button>
                    <button type="submit" form="createForm"
                        class="px-5 py-2.5 bg-brand-600 text-white rounded-xl text-sm font-bold hover:bg-brand-700 shadow-lg transition-all">Simpan
                        Data</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT --}}
    <div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-slate-900/60 dark:bg-slate-900/80 transition-opacity backdrop-blur-sm"
                onclick="closeModal('editModal')"></div>
            <div
                class="relative inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100 dark:border-slate-700">
                <div
                    class="bg-white dark:bg-slate-800 px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white">Edit Data Produk</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Perbarui informasi produk.</p>
                    </div>
                    <button onclick="closeModal('editModal')"
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"><i
                            class="fas fa-times text-lg"></i></button>
                </div>
                <div class="px-6 py-6 custom-scroll">
                    <form id="editForm" method="POST">
                        @csrf @method('PUT')
                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Nama
                                Produk <span class="text-rose-500">*</span></label>
                            <input type="text" id="edit_name" name="name" required class="saas-input">
                        </div>
                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Berat
                                Standar (Gram) <span class="text-slate-400 text-xs font-normal">(Opsional)</span></label>
                            <div class="relative">
                                <input type="text" id="edit_weight" name="weight"
                                    class="saas-input decimal-input pr-12" placeholder="0">
                                <span
                                    class="absolute right-3 top-2.5 text-xs text-slate-400 font-bold bg-slate-50 dark:bg-slate-700 px-2 py-0.5 rounded">Gram</span>
                            </div>
                        </div>
                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Jenis
                                Kemasan <span class="text-rose-500">*</span></label>
                            <select name="packaging_type_id" id="edit_packaging_type_id" class="saas-input select2-modal"
                                required data-placeholder="Cari & Pilih Jenis Kemasan...">
                                <option value=""></option>
                                @foreach ($packagingTypes as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}
                                        ({{ $p->conversion_quantity }} {{ $p->content_unit }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- PERUBAHAN: Container Dinamis Mesin di EDIT --}}
                        <div class="mb-5">
                            <div class="flex justify-between items-center mb-1.5">
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Data Mesin &
                                    Standar Produksi <span class="text-rose-500">*</span></label>
                                <button type="button" onclick="addMachineRow('edit')"
                                    class="text-xs px-2 py-1 bg-brand-100 text-brand-600 rounded font-bold hover:bg-brand-200 transition-colors">+
                                    Tambah Mesin</button>
                            </div>
                            <div id="editMachineContainer"></div>
                        </div>

                        <div class="mb-2">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Status
                                <span class="text-rose-500">*</span></label>
                            <select id="edit_status" name="status" class="saas-input cursor-pointer">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div
                    class="bg-slate-50 dark:bg-slate-700/50 px-6 py-4 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                    <button type="button" onclick="closeModal('editModal')"
                        class="px-5 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-xl text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-600 transition-all">Batal</button>
                    <button type="submit" form="editForm"
                        class="px-5 py-2.5 bg-brand-600 text-white rounded-xl text-sm font-bold hover:bg-brand-700 shadow-lg transition-all">Update
                        Data</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL VIEW (DETAIL) --}}
    <div id="viewModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-slate-900/60 dark:bg-slate-900/80 transition-opacity backdrop-blur-sm"
                onclick="closeModal('viewModal')"></div>
            <div
                class="relative inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100 dark:border-slate-700">
                <div
                    class="bg-white dark:bg-slate-800 px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white">Detail Data Produk</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Informasi lengkap produk.</p>
                    </div>
                    <button onclick="closeModal('viewModal')"
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"><i
                            class="fas fa-times text-lg"></i></button>
                </div>
                <div class="px-6 py-6 custom-scroll">
                    <div class="mb-5">
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Nama
                            Produk</label>
                        <input type="text" id="view_name" class="saas-input" readonly>
                    </div>
                    <div class="mb-5">
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Berat
                            Standar
                            (Gram)</label>
                        <div class="relative">
                            <input type="text" id="view_weight" class="saas-input pr-12" readonly>
                            <span
                                class="absolute right-3 top-2.5 text-xs text-slate-400 font-bold bg-slate-50 dark:bg-slate-700 px-2 py-0.5 rounded">Gram</span>
                        </div>
                    </div>
                    <div class="mb-5">
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Jenis
                            Kemasan</label>
                        <input type="text" id="view_packaging" class="saas-input" readonly>
                    </div>

                    {{-- PERUBAHAN: Container Dinamis Mesin di VIEW --}}
                    <div class="mb-5">
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Data Mesin &
                            Standar Produksi</label>
                        <div id="viewMachineContainer"></div>
                    </div>

                    <div class="mb-2">
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Status</label>
                        <input type="text" id="view_status_input" class="saas-input" readonly>
                    </div>
                </div>
                <div
                    class="bg-slate-50 dark:bg-slate-700/50 px-6 py-4 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                    <button type="button" onclick="closeModal('viewModal')"
                        class="px-5 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-xl text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-600 transition-all">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL IMPORT --}}
    @if (in_array(auth()->user()->role, ['admin', 'super_admin']))
        <div id="importModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div class="fixed inset-0 bg-slate-900/60 dark:bg-slate-900/80 backdrop-blur-sm transition-opacity"
                    onclick="closeModal('importModal')"></div>
                <div
                    class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl max-w-md w-full p-8 border border-slate-100 dark:border-slate-700 transform transition-all scale-100">
                    <div class="text-center">
                        <div
                            class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-emerald-50 dark:bg-emerald-900/20 mb-5">
                            <i class="fas fa-file-excel text-emerald-500 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 dark:text-white">Import Batch Excel</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Upload file .xlsx sesuai template untuk
                            import data massal.</p>
                    </div>
                    <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data"
                        class="mt-8" id="importForm">
                        @csrf
                        <div
                            class="flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 dark:border-slate-600 border-dashed rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 hover:border-brand-400 dark:hover:border-brand-500 transition-all group cursor-pointer relative">
                            <input id="file-upload" name="file" type="file"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" required
                                accept=".xlsx,.xls">
                            <div class="space-y-1 text-center"><i
                                    class="fas fa-cloud-arrow-up text-slate-400 text-3xl group-hover:text-brand-500 transition-colors"></i>
                                <div class="text-sm text-slate-600 dark:text-slate-300 font-medium"><span
                                        class="text-brand-600 dark:text-brand-400 group-hover:underline">Klik untuk
                                        upload</span> atau drag file</div>
                                <p class="text-xs text-slate-400">XLSX up to 5MB</p>
                            </div>
                        </div>
                        <div class="mt-4 text-right">
                            <a href="{{ route('products.template') }}" target="_blank"
                                class="text-xs text-brand-600 dark:text-brand-400 hover:text-brand-700 font-bold flex items-center justify-end gap-1"><i
                                    class="fas fa-download"></i> Download Template</a>
                        </div>
                        <div class="mt-8 flex gap-3">
                            <button type="button" onclick="closeModal('importModal')"
                                class="w-full py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-700 dark:text-slate-200 font-bold hover:bg-slate-50 dark:hover:bg-slate-600 transition-all">Batal</button>
                            <button type="submit"
                                class="w-full py-2.5 bg-emerald-600 text-white rounded-xl font-bold hover:bg-emerald-700 shadow-lg shadow-emerald-500/30 transition-all">Import
                                Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- TEMPLATE HIDDEN UNTUK BARIS MESIN DINAMIS --}}
    <template id="machineRowTemplate">
        <div
            class="machine-row relative border border-slate-200 dark:border-slate-700 rounded-xl p-4 bg-slate-50 dark:bg-slate-800/50 mb-3">
            <button type="button" onclick="removeMachineRow(this)"
                class="btn-remove-machine absolute top-2 right-2 text-rose-500 hover:text-rose-700 p-1 hidden"><i
                    class="fas fa-times"></i></button>
            <div class="mb-3 pr-6">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Pilih Mesin <span
                        class="text-rose-500">*</span></label>
                <select name="machine_id[]" class="saas-input select2-dynamic" required>
                    <option value="">Pilih Mesin...</option>
                    @foreach ($machines as $m)
                        <option value="{{ $m->id }}">{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Cycle Time
                        Standar</label>
                    <input type="text" name="cycle_time[]" class="saas-input decimal-input h-10 cycle-time-input"
                        placeholder="0">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Cavity
                        Standar</label>
                    <input type="number" name="cavity[]" class="saas-input h-10 cavity-input" placeholder="0">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 mt-3 actual-data-container hidden">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Cycle Time
                        Aktual</label>
                    <input type="text" class="saas-input h-10 actual-cycle-input" readonly
                        style="background-color: #f1f5f9; color: #64748b;">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Cavity
                        Aktual</label>
                    <input type="text" class="saas-input h-10 actual-cavity-input" readonly
                        style="background-color: #f1f5f9; color: #64748b;">
                </div>
            </div>
        </div>
    </template>

    {{-- SCRIPTS --}}
    <script>
        function formatIndo(n) {
            if (n === '' || n === null || n === undefined) return '';
            return n.toString().replace('.', ',');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }

        window.toggleExportMenu = function(e) {
            e.stopPropagation();
            const menu = document.getElementById('exportMenu');
            const arrow = document.getElementById('exportArrow');
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                menu.classList.add('opacity-100', 'translate-y-0');
                menu.classList.remove('opacity-0', '-translate-y-2');
                arrow.classList.add('rotate-180');
            } else {
                closeExportMenu();
            }
        }

        function closeExportMenu() {
            const menu = document.getElementById('exportMenu');
            const arrow = document.getElementById('exportArrow');
            if (!menu.classList.contains('hidden')) {
                menu.classList.add('hidden');
                arrow.classList.remove('rotate-180');
            }
        }
        document.addEventListener('click', function(e) {
            const menu = document.getElementById('exportMenu');
            if (!menu.contains(e.target)) {
                closeExportMenu();
            }
        });

        // --- FUNGSI DINAMIS UNTUK ROW MESIN ---
        window.addMachineRow = function(modalType, machineData = null) {
            let container = document.getElementById(modalType + 'MachineContainer');
            let template = document.getElementById('machineRowTemplate').content.cloneNode(true);
            let row = template.querySelector('.machine-row');
            let select = row.querySelector('select');

            if (machineData) {
                select.value = machineData.id;
                row.querySelector('.cycle-time-input').value = formatIndo(machineData.pivot.cycle_time);
                row.querySelector('.cavity-input').value = machineData.pivot.cavity;

                let actContainer = row.querySelector('.actual-data-container');
                actContainer.classList.remove('hidden');
                row.querySelector('.actual-cycle-input').value = (machineData.pivot.actual_cycle_time > 0) ? formatIndo(
                    machineData.pivot.actual_cycle_time) : '-';
                row.querySelector('.actual-cavity-input').value = (machineData.pivot.actual_cavity > 0) ? machineData
                    .pivot.actual_cavity : '-';

                if (modalType === 'view') {
                    select.disabled = true;
                    row.querySelector('.cycle-time-input').readOnly = true;
                    row.querySelector('.cavity-input').readOnly = true;
                    row.querySelector('.btn-remove-machine').remove();
                }
            }

            container.appendChild(row);
            updateRemoveButtons(container);

            if (modalType !== 'view') {
                $(select).select2({
                    placeholder: "Pilih Mesin...",
                    width: '100%',
                    dropdownParent: $('#' + modalType + 'Modal')
                });
            }
        }

        window.removeMachineRow = function(btn) {
            let container = btn.closest('div[id$="MachineContainer"]');
            btn.closest('.machine-row').remove();
            updateRemoveButtons(container);
        }

        window.updateRemoveButtons = function(container) {
            let rows = container.querySelectorAll('.machine-row');
            rows.forEach(row => {
                let btn = row.querySelector('.btn-remove-machine');
                if (btn) btn.classList.toggle('hidden', rows.length <= 1);
            });
        }

        $(document).ready(function() {
            // Init Select2 di modal dasar
            $('.select2-modal').select2({
                placeholder: "Pilih...",
                width: '100%',
                dropdownParent: $('#createModal')
            });

            // Init Baris Mesin Pertama di Modal Create
            addMachineRow('create');

            $(document).on('input', '.decimal-input', function() {
                let val = $(this).val().replace(/[^0-9,.]/g, '');
                $(this).val(val);
            });

            // Filter logic
            $('#filterStatus').select2({
                placeholder: "Semua Status",
                allowClear: true,
                minimumResultsForSearch: -1
            }).on('change', function() {
                fetchProducts();
            });
            let timeout = null;
            let xhr = null;
            window.fetchProducts = function(url = "{{ route('products.index') }}") {
                let search = $('#searchInput').val();
                let filterStatus = $('#filterStatus').val();
                let separator = url.includes('?') ? '&' : '?';
                let fullUrl = `${url}${separator}search=${search}&filter_status=${filterStatus}`;
                if (xhr) {
                    xhr.abort();
                }
                $('#table-data').addClass('opacity-50 pointer-events-none');
                xhr = $.ajax({
                    url: fullUrl,
                    success: function(response) {
                        let newTable = $(response).find('#table-data').html();
                        $('#table-data').html(newTable);
                        window.history.pushState({
                            path: fullUrl
                        }, '', fullUrl);
                    },
                    complete: function() {
                        $('#table-data').removeClass('opacity-50 pointer-events-none');
                        xhr = null;
                    }
                });
            }
            $('#searchInput').on('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(function() {
                    fetchProducts();
                }, 300);
            });
            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                let url = $(this).attr('href');
                fetchProducts(url);
            });

            $(document).on('change', '.status-toggle', function() {
                let $checkbox = $(this);
                let id = $checkbox.data('id');
                let originalState = !$checkbox.is(':checked');
                $.ajax({
                    url: "/products/" + id + "/toggle-status",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                position: 'center',
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500,
                                background: document.documentElement.classList.contains(
                                    'dark') ? '#1e293b' : '#fff',
                                color: document.documentElement.classList.contains(
                                    'dark') ? '#e2e8f0' : '#334155',
                                customClass: {
                                    popup: 'rounded-2xl'
                                }
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            $checkbox.prop('checked', originalState);
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Gagal mengubah status'
                            });
                        }
                    },
                    error: function(xhr) {
                        $checkbox.prop('checked', originalState);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON ? xhr.responseJSON.message :
                                'Terjadi kesalahan sistem'
                        });
                    }
                });
            });
        });

        // EDIT MODAL
        window.openEditModal = function(id) {
            $.get('/products/' + id, function(data) {
                $('#edit_name').val(data.name);
                $('#edit_status').val(data.status);
                $('#edit_weight').val(formatIndo(data.weight));

                $('#edit_packaging_type_id').select2({
                    dropdownParent: $('#editModal')
                });
                $('#edit_packaging_type_id').val(data.packaging_type_id).trigger('change');

                $('#editMachineContainer').empty();
                if (data.machines && data.machines.length > 0) {
                    data.machines.forEach(m => addMachineRow('edit', m));
                } else {
                    addMachineRow('edit');
                }

                $('#editForm').attr('action', '/products/' + id);
                openModal('editModal');
            });
        }

        // VIEW MODAL
        window.openViewModal = function(id) {
            $.get('/products/' + id, function(data) {
                $('#view_name').val(data.name);
                $('#view_weight').val(formatIndo(data.weight));
                $('#view_packaging').val(data.packaging_type ? data.packaging_type.name : '-');
                $('#view_status_input').val(data.status === 'aktif' ? 'Aktif' : 'Nonaktif');

                $('#viewMachineContainer').empty();
                if (data.machines && data.machines.length > 0) {
                    data.machines.forEach(m => addMachineRow('view', m));
                } else {
                    $('#viewMachineContainer').html(
                        '<span class="text-sm text-slate-400">Tidak ada data mesin.</span>');
                }

                openModal('viewModal');
            });
        }

        window.confirmDelete = function(btn) {
            Swal.fire({
                title: 'Hapus Produk?',
                text: "Data tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#e2e8f0' : '#334155',
                customClass: {
                    popup: 'rounded-2xl'
                }
            }).then((res) => {
                if (res.isConfirmed) btn.closest('form').submit();
            });
        }

        @if (session('success'))
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Berhasil',
                text: "{{ session('success') }}",
                timer: 2000,
                showConfirmButton: false,
                background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#e2e8f0' : '#334155',
                customClass: {
                    popup: 'rounded-2xl'
                }
            });
        @endif
        @if (session('error'))
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: 'Gagal',
                text: "{{ session('error') }}",
                background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#e2e8f0' : '#334155',
                customClass: {
                    popup: 'rounded-2xl'
                }
            });
        @endif
    </script>
@endsection
