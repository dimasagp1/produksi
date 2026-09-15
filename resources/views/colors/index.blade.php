@extends('layouts.app')

@section('title', 'Master Data Warna')

@section('content')
    {{-- LIBRARY PENDUKUNG --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    {{-- CUSTOM CSS --}}
    <style>
        /* Base Input Styles - SaaS Look */
        .saas-input {
            height: 44px;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 0 16px;
            font-size: 0.875rem;
            width: 100%;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
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

        /* Readonly State for View Modal */
        .saas-input[readonly],
        .saas-input:disabled {
            background-color: #f1f5f9;
            border-color: #cbd5e1;
            color: #64748b;
            cursor: default;
        }

        .dark .saas-input[readonly],
        .dark .saas-input:disabled {
            background-color: #334155;
            border-color: #475569;
            color: #94a3b8;
        }

        .saas-input.with-icon {
            padding-left: 2.75rem !important;
        }

        /* Force padding left for search icon */
        .saas-input.\!pl-12 {
            padding-left: 3rem !important;
        }

        /* Select2 Customization */
        .select2-container {
            width: 100% !important;
        }

        .select2-container .select2-selection--single {
            height: 44px !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 0.75rem !important;
            display: flex !important;
            align-items: center !important;
            background-color: #f8fafc !important;
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
            font-size: 0.875rem;
            padding-left: 16px !important;
        }

        .select2-dropdown {
            border: 1px solid #e2e8f0 !important;
            border-radius: 0.75rem !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
            overflow: hidden;
            z-index: 9999;
        }

        .dark .select2-dropdown {
            background-color: #1e293b !important;
            border-color: #334155 !important;
        }

        .dark .select2-search__field {
            background-color: #0f172a !important;
            color: #fff !important;
            border-color: #334155 !important;
        }

        .dark .select2-results__option {
            color: #f1f5f9 !important;
        }

        .dark .select2-results__option--highlighted[aria-selected] {
            background-color: #3b82f6 !important;
            color: #fff !important;
        }

        /* Table Styles */
        .table-row-hover:hover td {
            background-color: #f8fafc;
        }

        .dark .table-row-hover:hover td {
            background-color: rgba(30, 41, 59, 0.5);
        }

        .badge-status {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 2px 8px;
            border-radius: 999px;
        }
    </style>

    @php
        $statTotal = $totalColors ?? \App\Models\Color::count();
        $statActive = $activeColors ?? \App\Models\Color::where('status', 'active')->count();
        $statInactive = $inactiveColors ?? \App\Models\Color::where('status', 'inactive')->count();
    @endphp

    <div class="w-full font-sans text-slate-600 dark:text-slate-300">

        {{-- HEADER SECTION --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white tracking-tight">Data Warna</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Master data warna dan finishing produk.</p>
            </div>
            <div>
                <button onclick="openModal('createModal')"
                    class="inline-flex items-center px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-brand-500/30 transition-all transform hover:-translate-y-0.5 active:scale-95">
                    <i class="fas fa-plus mr-2"></i> Tambah Warna
                </button>
            </div>
        </div>

        {{-- DASHBOARD CARDS --}}
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3 mb-8">
            <div
                class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 flex items-center justify-between group hover:border-brand-200 transition-colors">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Warna</p>
                    <h3 class="text-2xl font-extrabold text-slate-800 dark:text-white">{{ $statTotal }}</h3>
                </div>
                <div
                    class="h-12 w-12 rounded-xl bg-brand-50 dark:bg-brand-900/20 flex items-center justify-center text-brand-600 dark:text-brand-400 group-hover:scale-110 transition-transform">
                    <i class="fas fa-palette text-xl"></i>
                </div>
            </div>
            <div
                class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 flex items-center justify-between group hover:border-emerald-200 transition-colors">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Warna Aktif</p>
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
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-slate-400 group-focus-within:text-brand-500 transition-colors"></i>
                        </div>
                        {{-- PERBAIKAN: Menggunakan !pl-12 untuk memaksa padding kiri lebih besar --}}
                        <input type="text" id="searchInput" value="{{ request('search') }}" class="saas-input !pl-12"
                            placeholder="Cari nama warna...">
                    </div>
                    <div class="relative w-full sm:w-48">
                        <select id="filterStatus" class="saas-input select2-filter">
                            <option value=""></option>
                            <option value="active" {{ request('filter_status') == 'active' ? 'selected' : '' }}>Aktif
                            </option>
                            <option value="inactive" {{ request('filter_status') == 'inactive' ? 'selected' : '' }}>
                                Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-3 w-full xl:w-auto justify-end">
                    @if (in_array(auth()->user()->role, ['admin', 'super_admin']))
                        <button onclick="openModal('importModal')"
                            class="inline-flex items-center px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 rounded-xl text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-600 transition-all">
                            <i class="fas fa-file-import mr-2 text-slate-400"></i> Import
                        </button>
                    @endif
                    <div class="relative">
                        <button onclick="toggleExportMenu(event)"
                            class="inline-flex items-center px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 rounded-xl text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-600 transition-all active:scale-95 shadow-sm">
                            <i class="fas fa-download mr-2 text-slate-400"></i> Export
                            <i id="exportArrow"
                                class="fas fa-chevron-down ml-2 text-xs text-slate-400 transition-transform duration-200"></i>
                        </button>
                        <div id="exportMenu"
                            class="hidden absolute right-0 mt-2 w-40 bg-white dark:bg-slate-800 rounded-xl shadow-xl ring-1 ring-black ring-opacity-5 border border-slate-100 dark:border-slate-700 z-50 transform origin-top-right transition-all duration-200">
                            <div class="py-1">
                                <a href="{{ route('colors.export', 'excel') }}" target="_blank"
                                    class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 hover:text-emerald-600"><i
                                        class="fas fa-file-excel mr-2 text-emerald-500"></i>Excel</a>
                                <a href="{{ route('colors.export', 'pdf') }}" target="_blank"
                                    class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 hover:text-rose-600"><i
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
                                class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider w-10">
                                #</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Nama
                                Warna</th>
                            <th
                                class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider w-32">
                                Status</th>
                            <th
                                class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider w-32">
                                Opsi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse ($colors as $color)
                            <tr class="table-row-hover transition-colors duration-150">
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-center text-slate-500 dark:text-slate-400 font-medium">
                                    {{ ($colors->currentPage() - 1) * $colors->perPage() + $loop->iteration }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div
                                                class="h-10 w-10 rounded-full bg-gradient-to-tr from-brand-100 to-brand-200 flex items-center justify-center text-brand-700 font-bold text-sm border border-brand-200">
                                                {{ substr($color->name, 0, 1) }}
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-slate-800 dark:text-white">
                                                {{ $color->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex flex-col items-center justify-center gap-1.5">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="sr-only peer status-toggle"
                                                data-id="{{ $color->id }}"
                                                {{ $color->status == 'active' ? 'checked' : '' }}>
                                            <div
                                                class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-brand-600">
                                            </div>
                                        </label>
                                        <span
                                            class="badge-status {{ $color->status == 'active' ? 'text-emerald-600 bg-emerald-50' : 'text-slate-500 bg-slate-100' }}">
                                            {{ $color->status == 'active' ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex justify-center items-center gap-1">
                                        <button onclick="openViewModal({{ $color }})"
                                            class="p-2 text-slate-400 hover:text-brand-600 hover:bg-brand-50 rounded-lg"
                                            title="Lihat Detail"><i class="fas fa-eye"></i></button>
                                        <button onclick="openEditModal({{ $color }})"
                                            class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg"
                                            title="Edit Data"><i class="fas fa-pen"></i></button>
                                        <form action="{{ route('colors.destroy', $color->id) }}" method="POST"
                                            class="inline-block">
                                            @csrf @method('DELETE')
                                            <button type="button" onclick="confirmDelete(this)"
                                                class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg"
                                                title="Hapus Data"><i class="fas fa-trash-alt"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-20 text-center text-slate-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="bg-slate-50 rounded-full p-4 mb-3"><i
                                                class="fas fa-inbox text-3xl text-slate-300"></i></div>
                                        <span class="text-sm font-medium">Data warna tidak ditemukan.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 rounded-b-2xl">{{ $colors->links() }}</div>
        </div>
    </div>

    {{-- MODAL CREATE --}}
    <div id="createModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModal('createModal')"></div>
            <div
                class="relative inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100 dark:border-slate-700">
                <div
                    class="bg-white dark:bg-slate-800 px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white">Tambah Warna Baru</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Isi informasi warna di bawah ini.</p>
                    </div>
                    <button onclick="closeModal('createModal')" class="text-slate-400 hover:text-slate-600"><i
                            class="fas fa-times text-lg"></i></button>
                </div>
                <div class="px-6 py-6 custom-scroll">
                    <form action="{{ route('colors.store') }}" method="POST" id="createForm">
                        @csrf
                        <div class="space-y-5">
                            <div class="relative">
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Nama
                                    Warna <span class="text-red-500">*</span></label>
                                <input type="text" name="name" required class="saas-input"
                                    placeholder="Masukkan nama warna">
                            </div>
                            <div class="relative">
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Status
                                    Awal <span class="text-red-500">*</span></label>
                                <select name="status" class="saas-input select2-modal" required
                                    data-placeholder="Pilih Status...">
                                    <option value=""></option>
                                    <option value="active">Aktif</option>
                                    <option value="inactive">Nonaktif</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="bg-slate-50 dark:bg-slate-700/50 px-6 py-4 border-t border-slate-100 flex justify-end gap-3">
                    <button onclick="closeModal('createModal')"
                        class="px-5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm font-bold text-slate-700 hover:bg-slate-50">Batal</button>
                    <button type="submit" form="createForm"
                        class="px-5 py-2.5 bg-brand-600 text-white rounded-xl text-sm font-bold hover:bg-brand-700 shadow-lg">Simpan
                        Data</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT --}}
    <div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModal('editModal')"></div>
            <div
                class="relative inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100 dark:border-slate-700">
                <div
                    class="bg-white dark:bg-slate-800 px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white">Edit Data Warna</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Perbarui informasi warna.</p>
                    </div>
                    <button onclick="closeModal('editModal')" class="text-slate-400 hover:text-slate-600"><i
                            class="fas fa-times text-lg"></i></button>
                </div>
                <div class="px-6 py-6 custom-scroll">
                    <form id="editForm" method="POST">
                        @csrf @method('PUT')
                        <div class="space-y-5">
                            <div class="relative">
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Nama
                                    Warna <span class="text-red-500">*</span></label>
                                <input type="text" id="edit_name" name="name" required class="saas-input">
                            </div>
                            <div class="relative">
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Status
                                    <span class="text-red-500">*</span></label>
                                <select id="edit_status" name="status" class="saas-input select2-modal" required
                                    data-placeholder="Pilih Status...">
                                    <option value=""></option>
                                    <option value="active">Aktif</option>
                                    <option value="inactive">Nonaktif</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="bg-slate-50 dark:bg-slate-700/50 px-6 py-4 border-t border-slate-100 flex justify-end gap-3">
                    <button onclick="closeModal('editModal')"
                        class="px-5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm font-bold text-slate-700 hover:bg-slate-50">Batal</button>
                    <button type="submit" form="editForm"
                        class="px-5 py-2.5 bg-brand-600 text-white rounded-xl text-sm font-bold hover:bg-brand-700 shadow-lg">Update
                        Data</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL VIEW (SAMA SEPERTI FORM TAPI READONLY) --}}
    <div id="viewModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModal('viewModal')"></div>
            <div
                class="relative inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100 dark:border-slate-700">
                <div
                    class="bg-white dark:bg-slate-800 px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white">Detail Warna</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Informasi lengkap warna (Read-only).</p>
                    </div>
                    <button onclick="closeModal('viewModal')" class="text-slate-400 hover:text-slate-600"><i
                            class="fas fa-times text-lg"></i></button>
                </div>
                <div class="px-6 py-6 custom-scroll">
                    <div class="space-y-5">
                        <div class="relative">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Nama
                                Warna</label>
                            <input type="text" id="view_name" class="saas-input" readonly>
                        </div>
                        <div class="relative">
                            <label
                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Status</label>
                            <input type="text" id="view_status_text" class="saas-input" readonly>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 dark:bg-slate-700/50 px-6 py-4 border-t border-slate-100 flex justify-end gap-3">
                    <button onclick="closeModal('viewModal')"
                        class="px-5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm font-bold text-slate-700 hover:bg-slate-50">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL IMPORT --}}
    <div id="importModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModal('importModal')"></div>
            <div
                class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl max-w-md w-full p-8 border border-slate-100 dark:border-slate-700 transform transition-all scale-100">
                <div class="text-center">
                    <div
                        class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-emerald-50 dark:bg-emerald-900/20 mb-5">
                        <i class="fas fa-file-excel text-emerald-500 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 dark:text-white">Import Data Warna</h3>
                    <p class="text-sm text-slate-500 mt-2">Upload file .xlsx sesuai template.</p>
                </div>
                <form action="{{ route('colors.import') }}" method="POST" enctype="multipart/form-data" class="mt-8"
                    id="importForm">
                    @csrf
                    <div
                        class="flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-xl hover:bg-slate-50 group cursor-pointer relative">
                        <input id="file-upload" name="file" type="file"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" required accept=".xlsx,.xls">
                        <div class="space-y-1 text-center">
                            <i
                                class="fas fa-cloud-arrow-up text-slate-400 text-3xl group-hover:text-brand-500 transition-colors"></i>
                            <div class="text-sm text-slate-600 font-medium"><span
                                    class="text-brand-600 group-hover:underline">Klik untuk upload</span> atau drag file
                            </div>
                            <p class="text-xs text-slate-400">XLSX up to 5MB</p>
                        </div>
                    </div>
                    <div class="mt-4 text-right">
                        <a href="{{ route('colors.template') }}" target="_blank"
                            class="text-xs text-brand-600 font-bold flex items-center justify-end gap-1"><i
                                class="fas fa-download"></i> Download Template</a>
                    </div>
                    <div class="mt-8 flex gap-3">
                        <button type="button" onclick="closeModal('importModal')"
                            class="w-full py-2.5 bg-white border border-slate-300 rounded-xl text-slate-700 font-bold hover:bg-slate-50">Batal</button>
                        <button type="submit"
                            class="w-full py-2.5 bg-emerald-600 text-white rounded-xl font-bold hover:bg-emerald-700 shadow-lg">Import
                            Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script>
        window.closeModal = function(id) {
            document.getElementById(id).classList.add('hidden');
        }
        window.openModal = function(id) {
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

        // EDIT MODAL FILL
        function openEditModal(color) {
            document.getElementById('edit_name').value = color.name;
            $('#edit_status').val(color.status).trigger('change');
            document.getElementById('editForm').action = '/colors/' + color.id;
            openModal('editModal');
        }

        // VIEW MODAL FILL (Form Readonly)
        function openViewModal(color) {
            document.getElementById('view_name').value = color.name;
            let statusText = color.status === 'active' ? 'Aktif' : 'Nonaktif';
            document.getElementById('view_status_text').value = statusText;
            openModal('viewModal');
        }

        function confirmDelete(btn) {
            Swal.fire({
                title: 'Hapus Warna?',
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
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl',
                    cancelButton: 'rounded-xl'
                }
            }).then((res) => {
                if (res.isConfirmed) btn.closest('form').submit();
            });
        }

        $(document).ready(function() {
            // Init Select2 Single
            $('.select2-modal').select2({
                placeholder: 'Pilih...',
                width: '100%',
                dropdownParent: $('body'),
                minimumResultsForSearch: -1
            });
            $('.select2-filter').select2({
                placeholder: 'Semua Status',
                width: '100%',
                minimumResultsForSearch: -1,
                allowClear: true
            }).on('change', function() {
                fetchColors();
            });

            // Re-bind Select2 parent when modals open to fix z-index issues
            $('#createModal .select2-modal').select2({
                dropdownParent: $('#createModal'),
                width: '100%',
                placeholder: 'Pilih Status...'
            });
            $('#editModal .select2-modal').select2({
                dropdownParent: $('#editModal'),
                width: '100%',
                placeholder: 'Pilih Status...'
            });

            // AJAX Search
            let timeout = null;
            let xhr = null;
            window.fetchColors = function(url = "{{ route('colors.index') }}") {
                let search = $('#searchInput').val();
                let filterStatus = $('#filterStatus').val();
                let separator = url.includes('?') ? '&' : '?';
                let fullUrl = `${url}${separator}search=${search}&filter_status=${filterStatus}`;
                if (xhr) xhr.abort();
                $('#table-data').addClass('opacity-50 pointer-events-none');
                xhr = $.ajax({
                    url: fullUrl,
                    success: function(response) {
                        $('#table-data').html($(response).find('#table-data').html());
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
                    fetchColors();
                }, 300);
            });
            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                let url = $(this).attr('href');
                fetchColors(url);
            });

            // Toggle Status
            $(document).on('change', '.status-toggle', function() {
                let $checkbox = $(this);
                let id = $checkbox.data('id');
                let status = $checkbox.is(':checked') ? 'active' : 'inactive';
                $.ajax({
                    url: "/colors/" + id + "/toggle-status",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: status
                    },
                    success: function(response) {
                        Swal.fire({
                            position: 'center',
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Status diperbarui.',
                            showConfirmButton: false,
                            timer: 1500,
                            customClass: {
                                popup: 'rounded-2xl'
                            }
                        });
                    },
                    error: function() {
                        $checkbox.prop('checked', !status === 'active');
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Gagal update status'
                        });
                    }
                });
            });
        });

        @if (session('success'))
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Berhasil',
                text: "{{ session('success') }}",
                timer: 2000,
                showConfirmButton: false,
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
                customClass: {
                    popup: 'rounded-2xl'
                }
            });
        @endif
    </script>
@endsection
