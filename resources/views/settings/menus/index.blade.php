@extends('layouts.app')

@section('title', 'Akses Menu')

@section('content')
    <style>
        /* Custom checkbox style */
        .custom-checkbox {
            width: 1.25rem;
            height: 1.25rem;
            border-radius: 0.25rem;
            border: 2px solid #cbd5e1;
            cursor: pointer;
            transition: all 0.2s;
        }

        .custom-checkbox:checked {
            background-color: #3b82f6;
            border-color: #3b82f6;
        }

        /* Row Highlight */
        tr.active-row {
            background-color: #eff6ff !important;
            /* blue-50 */
        }

        .dark tr.active-row {
            background-color: rgba(59, 130, 246, 0.1) !important;
        }
    </style>

    <div class="flex flex-col gap-6">

        {{-- PAGE HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white tracking-tight">Konfigurasi Akses Menu</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Atur hak akses menu sidebar untuk setiap role
                    pengguna.</p>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center gap-3">
                <div
                    class="hidden sm:flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-semibold">
                    <i class="fas fa-info-circle"></i>
                    <span>Perubahan langsung aktif setelah disimpan</span>
                </div>
            </div>
        </div>

        {{-- MAIN CARD --}}
        <div
            class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <form action="{{ route('settings.menus.update') }}" method="POST" id="accessForm">
                @csrf

                {{-- TOOLBAR --}}
                <div
                    class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 flex justify-between items-center">
                    <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Matriks Hak Akses</span>
                    <button type="submit"
                        class="inline-flex items-center px-5 py-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-brand-500/30 transition-all transform hover:-translate-y-0.5">
                        <i class="fas fa-save mr-2"></i> Simpan Perubahan
                    </button>
                </div>

                {{-- TABLE WRAPPER --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead
                            class="text-xs text-slate-500 dark:text-slate-400 uppercase bg-slate-50 dark:bg-slate-700/50 border-b border-slate-200 dark:border-slate-700">
                            <tr>
                                <th scope="col" class="px-6 py-4 font-bold w-1/3">
                                    Struktur Menu Aplikasi
                                </th>
                                @foreach ($roles as $role)
                                    <th scope="col" class="px-4 py-4 text-center min-w-[100px]">
                                        <div class="flex flex-col items-center gap-2">
                                            <span
                                                class="font-bold text-slate-700 dark:text-slate-200">{{ ucwords(str_replace('_', ' ', $role)) }}</span>
                                            {{-- SELECT ALL PER ROLE --}}
                                            <label class="flex items-center gap-1.5 cursor-pointer group">
                                                <input type="checkbox"
                                                    class="role-select-all w-3 h-3 rounded border-slate-300 text-brand-600 focus:ring-brand-500"
                                                    data-role="{{ $role }}">
                                                <span
                                                    class="text-[10px] text-slate-400 group-hover:text-brand-500 transition-colors">Pilih
                                                    Semua</span>
                                            </label>
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @foreach ($menus as $parent)
                                {{-- 1. PARENT ROW (GROUP TITLE) --}}
                                <tr
                                    class="bg-slate-50/80 dark:bg-slate-800/80 font-bold group hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                                    <td class="px-6 py-3 text-slate-800 dark:text-white flex items-center gap-2">
                                        <i class="fas fa-folder text-brand-500/50"></i>
                                        {{ $parent->name }}
                                    </td>
                                    @foreach ($roles as $role)
                                        <td class="px-4 py-3 text-center">
                                            <div class="flex justify-center">
                                                <input type="checkbox"
                                                    name="access[{{ $parent->id }}][{{ $role }}]" value="1"
                                                    {{ $parent->hasAccess($role) ? 'checked' : '' }}
                                                    class="custom-checkbox role-{{ $role }} checkbox-item"
                                                    onchange="toggleHighlight(this)">
                                            </div>
                                        </td>
                                    @endforeach
                                </tr>

                                {{-- 2. CHILD ROWS (SUB MENUS) --}}
                                @foreach ($parent->children as $child)
                                    <tr
                                        class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors border-b border-slate-50 dark:border-slate-800">
                                        <td
                                            class="px-6 py-3 pl-10 text-slate-600 dark:text-slate-300 font-medium flex items-center gap-3">
                                            <div class="w-1.5 h-1.5 rounded-full bg-slate-300 dark:bg-slate-600"></div>
                                            <i class="{{ $child->icon }} w-5 text-center text-slate-400"></i>
                                            {{ $child->name }}
                                        </td>
                                        @foreach ($roles as $role)
                                            <td class="px-4 py-3 text-center">
                                                <div class="flex justify-center">
                                                    <input type="checkbox"
                                                        name="access[{{ $child->id }}][{{ $role }}]"
                                                        value="1" {{ $child->hasAccess($role) ? 'checked' : '' }}
                                                        class="custom-checkbox role-{{ $role }} checkbox-item"
                                                        onchange="toggleHighlight(this)">
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- FOOTER ACTION --}}
                <div
                    class="px-6 py-4 bg-slate-50 dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow-lg shadow-brand-500/30 transition-all transform hover:-translate-y-0.5 active:scale-95">
                        <i class="fas fa-save mr-2"></i> Simpan Konfigurasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- JAVASCRIPT LOGIC --}}
    <script>
        // 1. Logic Select All per Role (Kolom)
        document.querySelectorAll('.role-select-all').forEach(selectAllBox => {
            selectAllBox.addEventListener('change', function() {
                const role = this.dataset.role;
                const isChecked = this.checked;

                // Cari semua checkbox yang punya class 'role-{nama_role}'
                document.querySelectorAll(`.role-${role}`).forEach(checkbox => {
                    checkbox.checked = isChecked;
                    // Trigger event change manual agar highlight jalan
                    toggleHighlight(checkbox);
                });
            });
        });

        // 2. Logic Highlighter (Baris & Cell aktif saat dicentang)
        function toggleHighlight(checkbox) {
            const cell = checkbox.closest('td');
            const row = checkbox.closest('tr');

            // Highlight Cell (Opsional, agar fokus ke kotak yang dicentang)
            if (checkbox.checked) {
                // cell.classList.add('bg-blue-50', 'dark:bg-blue-900/20'); // Uncomment jika ingin highlight cell
            } else {
                // cell.classList.remove('bg-blue-50', 'dark:bg-blue-900/20');
            }

            // Cek apakah ada setidaknya satu checkbox aktif di baris ini
            const anyChecked = row.querySelectorAll('input[type="checkbox"]:checked').length > 0;

            if (anyChecked) {
                row.classList.add('active-row');
            } else {
                // Jangan hapus class active-row jika ini adalah parent row (karena style defaultnya beda)
                // Tapi logika di CSS 'tr.active-row' sudah handle override
                row.classList.remove('active-row');
            }
        }

        // 3. Init Highlight saat halaman dimuat (untuk checkbox yang sudah checked dari DB)
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('.checkbox-item').forEach(checkbox => {
                if (checkbox.checked) {
                    toggleHighlight(checkbox);
                }
            });
        });
    </script>
@endsection
