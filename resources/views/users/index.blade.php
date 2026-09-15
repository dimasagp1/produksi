@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
    {{-- LIBRARY PENDUKUNG --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    {{-- LOGIC PENGHITUNG JUMLAH --}}
    @php
        $totalUsers = \App\Models\User::count();
        $activeUsers = \App\Models\User::where('status', 'active')->count();
        $inactiveUsers = \App\Models\User::where('status', 'inactive')->count();
    @endphp

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

        /* Readonly State */
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

    <div class="w-full font-sans text-slate-600 dark:text-slate-300">

        {{-- HEADER SECTION --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white tracking-tight">Manajemen Pengguna</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Kelola akses login, role, dan status pengguna
                    sistem.</p>
            </div>
            <div>
                <button onclick="openModal('createModal')"
                    class="inline-flex items-center px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-brand-500/30 transition-all transform hover:-translate-y-0.5 active:scale-95">
                    <i class="fas fa-user-plus mr-2"></i> Tambah Pengguna
                </button>
            </div>
        </div>

        {{-- DASHBOARD CARDS --}}
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3 mb-8">
            <div
                class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 flex items-center justify-between group hover:border-brand-200 transition-colors">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Pengguna</p>
                    <h3 class="text-2xl font-extrabold text-slate-800 dark:text-white">{{ $totalUsers }}</h3>
                </div>
                <div
                    class="h-12 w-12 rounded-xl bg-brand-50 dark:bg-brand-900/20 flex items-center justify-center text-brand-600 dark:text-brand-400 group-hover:scale-110 transition-transform">
                    <i class="fas fa-users text-xl"></i>
                </div>
            </div>
            <div
                class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 flex items-center justify-between group hover:border-emerald-200 transition-colors">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Akun Aktif</p>
                    <h3 class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ $activeUsers }}</h3>
                </div>
                <div
                    class="h-12 w-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform">
                    <i class="fas fa-user-check text-xl"></i>
                </div>
            </div>
            <div
                class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 flex items-center justify-between group hover:border-slate-300 transition-colors">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Nonaktif</p>
                    <h3 class="text-2xl font-extrabold text-slate-600 dark:text-slate-400">{{ $inactiveUsers }}</h3>
                </div>
                <div
                    class="h-12 w-12 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-500 dark:text-slate-400 group-hover:scale-110 transition-transform">
                    <i class="fas fa-user-slash text-xl"></i>
                </div>
            </div>
        </div>

        {{-- TOOLBAR --}}
        <div
            class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 mb-6">
            <div class="flex flex-col xl:flex-row justify-between gap-4 items-center">
                <div class="flex flex-col sm:flex-row gap-3 w-full flex-grow items-center">
                    <form action="{{ route('users.index') }}" method="GET" class="contents w-full" id="filterForm">
                        <div class="relative w-full sm:max-w-xs group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i
                                    class="fas fa-search text-slate-400 group-focus-within:text-brand-500 transition-colors"></i>
                            </div>
                            <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                                class="saas-input !pl-12" placeholder="Cari nama atau email...">
                        </div>
                        <div class="relative w-full sm:w-48">
                            {{-- FILTER ROLE LENGKAP --}}
                            <select name="role" id="filterRole" class="saas-input select2-filter">
                                <option value=""></option>
                                <option value="super_admin" {{ request('role') == 'super_admin' ? 'selected' : '' }}>Super
                                    Admin</option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="gm" {{ request('role') == 'gm' ? 'selected' : '' }}>GM</option>
                                <option value="manager" {{ request('role') == 'manager' ? 'selected' : '' }}>Manager
                                </option>
                                <option value="spv" {{ request('role') == 'spv' ? 'selected' : '' }}>SPV</option>
                                <option value="leader" {{ request('role') == 'leader' ? 'selected' : '' }}>Leader</option>
                                <option value="operator" {{ request('role') == 'operator' ? 'selected' : '' }}>Operator
                                </option>
                            </select>
                        </div>
                        <div class="relative w-full sm:w-48">
                            <select name="status" id="filterStatus" class="saas-input select2-filter">
                                <option value=""></option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif
                                </option>
                            </select>
                        </div>
                    </form>
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
                                class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-10">
                                No</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Nama & Email</th>
                            <th
                                class="px-6 py-4 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Role</th>
                            <th
                                class="px-6 py-4 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Terdaftar</th>
                            <th
                                class="px-6 py-4 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-40">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse ($users as $user)
                            <tr class="table-row-hover transition-colors duration-150 align-top">
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400 font-medium">
                                    {{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div
                                                class="h-10 w-10 rounded-full bg-gradient-to-tr from-brand-100 to-brand-200 dark:from-brand-900/30 dark:to-brand-800/30 flex items-center justify-center text-brand-700 dark:text-brand-400 font-bold text-sm border border-brand-200 dark:border-brand-700 shadow-sm uppercase">
                                                {{ substr($user->name, 0, 2) }}
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-slate-800 dark:text-white">
                                                {{ $user->name }}</div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ $user->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @php
                                        // DEFINISI WARNA BADGE PER ROLE
                                        $badges = [
                                            'super_admin' =>
                                                'bg-rose-50 text-rose-700 border-rose-100 dark:bg-rose-900/20 dark:text-rose-400 dark:border-rose-900/30',
                                            'admin' =>
                                                'bg-purple-50 text-purple-700 border-purple-100 dark:bg-purple-900/20 dark:text-purple-400 dark:border-purple-900/30',
                                            'gm' =>
                                                'bg-indigo-50 text-indigo-700 border-indigo-100 dark:bg-indigo-900/20 dark:text-indigo-400 dark:border-indigo-900/30',
                                            'manager' =>
                                                'bg-amber-50 text-amber-700 border-amber-100 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-900/30',
                                            'spv' =>
                                                'bg-teal-50 text-teal-700 border-teal-100 dark:bg-teal-900/20 dark:text-teal-400 dark:border-teal-900/30',
                                            'leader' =>
                                                'bg-blue-50 text-blue-700 border-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-900/30',
                                            'operator' =>
                                                'bg-slate-100 text-slate-600 border-slate-200 dark:bg-slate-700/50 dark:text-slate-300 dark:border-slate-600',
                                        ];
                                        $label = ucwords(str_replace('_', ' ', $user->role));
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $badges[$user->role] ?? 'bg-slate-50 text-slate-700' }} uppercase tracking-wide">
                                        {{ $label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if (auth()->id() !== $user->id)
                                        <div class="flex flex-col items-center justify-center gap-1.5">
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" class="sr-only peer status-toggle"
                                                    data-id="{{ $user->id }}"
                                                    {{ $user->status == 'active' ? 'checked' : '' }}>
                                                <div
                                                    class="w-9 h-5 bg-slate-200 dark:bg-slate-700 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-brand-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-brand-600">
                                                </div>
                                            </label>
                                            <span
                                                class="badge-status {{ $user->status == 'active' ? 'text-emerald-600 bg-emerald-50 dark:bg-emerald-900/20 dark:text-emerald-400' : 'text-slate-500 bg-slate-100 dark:bg-slate-700 dark:text-slate-400' }}"
                                                id="status-label-{{ $user->id }}">
                                                {{ $user->status == 'active' ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </div>
                                    @else
                                        <span
                                            class="text-xs text-slate-400 italic font-medium bg-slate-50 dark:bg-slate-700 px-2 py-1 rounded">Akun
                                            Anda</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                                    {{ $user->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex justify-center items-center gap-1">
                                        <button onclick='openViewModal({{ $user->id }})'
                                            class="p-2 text-slate-400 hover:text-brand-600 hover:bg-brand-50 dark:hover:bg-brand-900/20 dark:hover:text-brand-400 rounded-lg transition-colors"
                                            title="Lihat Detail"><i class="fas fa-eye"></i></button>
                                        <button onclick='openEditModal({{ $user->id }})'
                                            class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 dark:hover:text-amber-400 rounded-lg transition-colors"
                                            title="Edit"><i class="fas fa-pen"></i></button>
                                        @if (auth()->id() !== $user->id)
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                                class="inline-block">
                                                @csrf @method('DELETE')
                                                <button type="button" onclick="confirmDelete(this)"
                                                    class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 dark:hover:text-rose-400 rounded-lg transition-colors"
                                                    title="Hapus"><i class="fas fa-trash-alt"></i></button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-20 text-center text-slate-500 dark:text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="bg-slate-50 dark:bg-slate-700/50 rounded-full p-4 mb-3"><i
                                                class="fas fa-user-slash text-3xl text-slate-300 dark:text-slate-500"></i>
                                        </div>
                                        <span class="text-sm font-medium">Data pengguna tidak ditemukan.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div
                class="px-6 py-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/30 rounded-b-2xl">
                {{ $users->links() }}
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
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white">Tambah Pengguna</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Buat akun baru untuk akses sistem.</p>
                    </div>
                    <button onclick="closeModal('createModal')"
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"><i
                            class="fas fa-times text-lg"></i></button>
                </div>
                <div class="px-6 py-6 custom-scroll">
                    <form action="{{ route('users.store') }}" method="POST" id="createForm">
                        @csrf
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Nama
                                    Lengkap <span class="text-rose-500">*</span></label>
                                <input type="text" name="name" required class="saas-input"
                                    placeholder="Masukkan nama lengkap">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Email
                                    <span class="text-rose-500">*</span></label>
                                <input type="email" name="email" required class="saas-input"
                                    placeholder="email@example.com">
                            </div>
                            <div class="grid grid-cols-2 gap-5">
                                <div>
                                    <label
                                        class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Role
                                        <span class="text-rose-500">*</span></label>
                                    <select name="role" class="saas-input select2-modal" required
                                        data-placeholder="Pilih Role">
                                        <option value=""></option>
                                        <option value="super_admin">Super Admin</option>
                                        <option value="admin">Admin</option>
                                        <option value="gm">GM</option>
                                        <option value="manager">Manager</option>
                                        <option value="spv">SPV</option>
                                        <option value="leader">Leader</option>
                                        <option value="operator">Operator</option>
                                    </select>
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Status
                                        <span class="text-rose-500">*</span></label>
                                    <select name="status" class="saas-input select2-modal" required
                                        data-placeholder="Pilih Status">
                                        <option value=""></option>
                                        <option value="active">Aktif</option>
                                        <option value="inactive">Nonaktif</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Password
                                    <span class="text-rose-500">*</span></label>
                                <input type="password" name="password" required minlength="6" class="saas-input"
                                    placeholder="Minimal 6 karakter">
                            </div>
                        </div>
                    </form>
                </div>
                <div
                    class="bg-slate-50 dark:bg-slate-700/50 px-6 py-4 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                    <button type="button" onclick="closeModal('createModal')"
                        class="px-5 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-xl text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-600 transition-all">Batal</button>
                    <button type="submit" form="createForm"
                        class="px-5 py-2.5 bg-brand-600 text-white rounded-xl text-sm font-bold hover:bg-brand-700 shadow-lg shadow-brand-500/30 transition-all">Simpan
                        Akun</button>
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
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white">Edit Pengguna</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Perbarui informasi akun pengguna.</p>
                    </div>
                    <button onclick="closeModal('editModal')"
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"><i
                            class="fas fa-times text-lg"></i></button>
                </div>
                <div class="px-6 py-6 custom-scroll">
                    <form id="editForm" method="POST">
                        @csrf @method('PUT')
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Nama
                                    Lengkap <span class="text-rose-500">*</span></label>
                                <input type="text" id="edit_name" name="name" required class="saas-input">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Email
                                    <span class="text-rose-500">*</span></label>
                                <input type="email" id="edit_email" name="email" required class="saas-input">
                            </div>
                            <div class="grid grid-cols-2 gap-5">
                                <div>
                                    <label
                                        class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Role
                                        <span class="text-rose-500">*</span></label>
                                    <select name="role" id="edit_role" class="saas-input select2-modal" required
                                        data-placeholder="Pilih Role">
                                        <option value=""></option>
                                        <option value="super_admin">Super Admin</option>
                                        <option value="admin">Admin</option>
                                        <option value="gm">GM</option>
                                        <option value="manager">Manager</option>
                                        <option value="spv">SPV</option>
                                        <option value="leader">Leader</option>
                                        <option value="operator">Operator</option>
                                    </select>
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Status
                                        <span class="text-rose-500">*</span></label>
                                    <select name="status" id="edit_status" class="saas-input select2-modal" required
                                        data-placeholder="Pilih Status">
                                        <option value=""></option>
                                        <option value="active">Aktif</option>
                                        <option value="inactive">Nonaktif</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Password
                                    Baru <span class="text-xs text-slate-400 font-normal ml-1">(Opsional)</span></label>
                                <input type="password" name="password" minlength="6" class="saas-input"
                                    placeholder="Kosongkan jika tidak diubah">
                            </div>
                        </div>
                    </form>
                </div>
                <div
                    class="bg-slate-50 dark:bg-slate-700/50 px-6 py-4 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                    <button type="button" onclick="closeModal('editModal')"
                        class="px-5 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-xl text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-600 transition-all">Batal</button>
                    <button type="submit" form="editForm"
                        class="px-5 py-2.5 bg-brand-600 text-white rounded-xl text-sm font-bold hover:bg-brand-700 shadow-lg shadow-brand-500/30 transition-all">Update
                        Akun</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL VIEW (Readonly) --}}
    <div id="viewModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-slate-900/60 dark:bg-slate-900/80 transition-opacity backdrop-blur-sm"
                onclick="closeModal('viewModal')"></div>
            <div
                class="relative inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-100 dark:border-slate-700">
                <div
                    class="bg-white dark:bg-slate-800 px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white">Detail Pengguna</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Informasi lengkap akun.</p>
                    </div>
                    <button onclick="closeModal('viewModal')"
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300"><i
                            class="fas fa-times text-lg"></i></button>
                </div>
                <div class="px-6 py-6 custom-scroll">
                    <div class="space-y-5">
                        <div class="flex flex-col items-center mb-4">
                            <div
                                class="h-20 w-20 bg-brand-50 dark:bg-brand-900/20 rounded-full flex items-center justify-center text-brand-600 dark:text-brand-400 font-bold text-2xl uppercase border border-brand-200 dark:border-brand-700 shadow-sm mb-3">
                                <span id="view_initials"></span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Nama
                                Lengkap</label>
                            <input type="text" id="view_name" class="saas-input" readonly>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Email</label>
                            <input type="text" id="view_email" class="saas-input" readonly>
                        </div>
                        <div class="grid grid-cols-2 gap-5">
                            <div>
                                <label
                                    class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Role</label>
                                <input type="text" id="view_role_input" class="saas-input capitalize" readonly>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Status</label>
                                <input type="text" id="view_status_input" class="saas-input" readonly>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Terdaftar
                                Pada</label>
                            <input type="text" id="view_created_at" class="saas-input" readonly>
                        </div>
                    </div>
                </div>
                <div
                    class="bg-slate-50 dark:bg-slate-700/50 px-6 py-4 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                    <button onclick="closeModal('viewModal')"
                        class="px-5 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-xl text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-600 transition-all">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script>
        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }

        function openEditModal(id) {
            $.get('/users/' + id, function(user) {
                document.getElementById('edit_name').value = user.name;
                document.getElementById('edit_email').value = user.email;
                $('#edit_role').val(user.role).trigger('change');
                $('#edit_status').val(user.status).trigger('change');
                document.getElementById('editForm').action = '/users/' + user.id;
                openModal('editModal');
            });
        }

        function openViewModal(id) {
            $.get('/users/' + id, function(user) {
                document.getElementById('view_initials').innerText = user.name.substring(0, 2);
                document.getElementById('view_name').value = user.name;
                document.getElementById('view_email').value = user.email;
                document.getElementById('view_role_input').value = user.role.replace('_', ' ');
                document.getElementById('view_status_input').value = user.status === 'active' ? 'Aktif' :
                'Nonaktif';
                let date = new Date(user.created_at);
                document.getElementById('view_created_at').value = date.toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });
                openModal('viewModal');
            });
        }

        function confirmDelete(btn) {
            Swal.fire({
                title: 'Hapus Pengguna?',
                text: "Data tidak bisa dikembalikan!",
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
            $('.select2-modal').select2({
                placeholder: function() {
                    return $(this).data('placeholder');
                },
                width: '100%',
                dropdownParent: $('body'),
                minimumResultsForSearch: -1
            });
            $('.select2-filter').select2({
                placeholder: 'Semua Status',
                width: '100%',
                minimumResultsForSearch: -1,
                allowClear: true
            });
            $('#createModal .select2-modal').select2({
                dropdownParent: $('#createModal'),
                width: '100%'
            });
            $('#editModal .select2-modal').select2({
                dropdownParent: $('#editModal'),
                width: '100%'
            });
            $('#filterRole, #filterStatus').on('change', function() {
                $(this).closest('form').submit();
            });
            let timeout = null;
            $('input[name="search"]').on('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    $(this).closest('form').submit();
                }, 500);
            });
            $(document).on('change', '.status-toggle', function() {
                let $checkbox = $(this);
                let id = $checkbox.data('id');
                let originalState = !$checkbox.is(':checked');
                $.ajax({
                    url: "/users/" + id + "/toggle-status",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            let label = response.new_status === 'active' ? 'Aktif' : 'Nonaktif';
                            let color = response.new_status === 'active' ?
                                'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20' :
                                'text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-700';
                            $('#status-label-' + id).text(label).attr('class', 'badge-status ' +
                                color);
                            Swal.fire({
                                position: 'center',
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 800,
                                background: document.documentElement.classList.contains(
                                    'dark') ? '#1e293b' : '#fff',
                                color: document.documentElement.classList.contains(
                                    'dark') ? '#e2e8f0' : '#334155',
                                customClass: {
                                    popup: 'rounded-2xl'
                                }
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
                },
                background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#e2e8f0' : '#334155'
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
                },
                background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#e2e8f0' : '#334155'
            });
        @endif
    </script>
@endsection
