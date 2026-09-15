<div class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 mb-6">
    <form action="{{ route('batches.index') }}" method="GET" class="contents" id="filterForm">
        {{-- Hidden Inputs (Status) --}}
        @if (request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
        @if (request('status_group'))
            <input type="hidden" name="status_group" value="{{ request('status_group') }}">
        @endif

        <div class="flex flex-col xl:flex-row justify-between gap-4 items-end">
            <div class="flex flex-col sm:flex-row gap-3 w-full items-start sm:items-end flex-wrap">

                {{-- SEARCH INPUT --}}
                <div class="relative w-full sm:max-w-xs group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-slate-400 group-focus-within:text-brand-500 transition-colors"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" class="saas-input with-icon"
                        placeholder="Cari Kode Batch / Produk...">
                </div>

                {{-- DATE FILTERS --}}
                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto items-end">
                    <div class="w-full sm:w-36">
                        <label
                            class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 mb-1 uppercase tracking-wide">
                            Tanggal Awal
                        </label>
                        {{-- Hapus value="" jika tidak ada request, biarkan kosong --}}
                        <input type="text" name="filter_start_date" id="filter_start_date"
                            class="saas-input datepicker text-xs" value="{{ request('filter_start_date') }}"
                            placeholder="Semua Tanggal" autocomplete="off"> {{-- Tambahkan autocomplete off --}}
                    </div>
                    <div class="hidden sm:block text-slate-400 font-bold pb-3">-</div>
                    <div class="w-full sm:w-36">
                        <label
                            class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 mb-1 uppercase tracking-wide">
                            Tanggal Akhir
                        </label>
                        <input type="text" name="filter_end_date" id="filter_end_date"
                            class="saas-input datepicker text-xs" value="{{ request('filter_end_date') }}"
                            placeholder="Semua Tanggal" autocomplete="off">
                    </div>
                </div>

                {{-- BUTTONS --}}
                <button type="submit"
                    class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow-md transition-all active:scale-95 flex items-center">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>

                {{-- RESET BUTTON Logic --}}
                @if (request()->anyFilled(['status', 'status_group', 'search', 'filter_start_date', 'filter_end_date']))
                    <a href="{{ route('batches.index') }}"
                        class="px-5 py-2.5 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 font-bold rounded-xl shadow-sm transition-all active:scale-95 flex items-center">
                        <i class="fas fa-times mr-2"></i> Reset
                    </a>
                @endif
            </div>

            {{-- ... Bagian Import/Export biarkan tetap ... --}}
            <div class="flex items-center gap-3 w-full xl:w-auto justify-end sm:mt-0 mt-2 pb-0.5">
                @if (in_array(auth()->user()->role, ['admin', 'super_admin']))
                    <button onclick="window.openModal('importModal')" type="button"
                        class="inline-flex items-center px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 rounded-xl text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-600 transition-all"><i
                            class="fas fa-file-import mr-2 text-slate-400"></i> Import</button>
                @endif
                <div class="relative">
                    <button type="button" onclick="toggleExportMenu(event)"
                        class="inline-flex items-center px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 rounded-xl text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-600 transition-all active:scale-95 shadow-sm"><i
                            class="fas fa-download mr-2 text-slate-400"></i> Export <i id="exportArrow"
                            class="fas fa-chevron-down ml-2 text-xs text-slate-400 transition-transform duration-200"></i></button>
                    <div id="exportMenu"
                        class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-slate-800 rounded-xl shadow-xl ring-1 ring-black ring-opacity-5 border border-slate-100 dark:border-slate-700 z-50 transform origin-top-right transition-all duration-200">
                        <div class="py-1.5">
                            <a href="{{ route('batches.export-excel', request()->query()) }}" target="_blank"
                                class="group flex items-center px-4 py-2.5 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                <div class="w-8 flex justify-center"><i
                                        class="fas fa-file-excel text-emerald-500 text-lg group-hover:scale-110 transition-transform"></i>
                                </div><span class="font-medium">Download Excel</span>
                            </a>
                            <a href="{{ route('batches.export-pdf', request()->query()) }}" target="_blank"
                                class="group flex items-center px-4 py-2.5 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                <div class="w-8 flex justify-center"><i
                                        class="fas fa-file-pdf text-rose-500 text-lg group-hover:scale-110 transition-transform"></i>
                                </div><span class="font-medium">Download PDF</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
