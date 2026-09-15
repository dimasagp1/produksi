{{-- HEADER SECTION --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white tracking-tight">Daftar Batch Produksi</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Pengelolaan Surat Perintah Kerja (SPK) dan jadwal
            produksi.</p>
    </div>

    {{-- PERBAIKAN DI SINI: Memisahkan 'super_admin' dan 'spv' dengan tanda kutip dan koma --}}
    @if (in_array(auth()->user()->role, ['admin', 'super_admin', 'spv', 'manager']))
        <div>
            <button type="button" onclick="window.openModal('createModal')"
                class="group px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl shadow-lg shadow-indigo-500/30 transition-all active:scale-95 flex items-center gap-2">
                <i class="fa-solid fa-plus transition-transform group-hover:rotate-90"></i>
                <span>Tambah Batch</span>
            </button>
        </div>
    @endif
</div>

{{-- SUMMARY CARDS --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <a href="{{ route('batches.index') }}"
        class="summary-card bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border {{ !$currentStatus && !$currentGroup ? 'border-brand-500 ring-1 ring-brand-500' : 'border-slate-200 dark:border-slate-700' }} flex items-center justify-between group hover:border-brand-200 dark:hover:border-brand-500/50">
        <div>
            <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Total Batch
            </p>
            <h3 class="text-2xl font-extrabold text-slate-800 dark:text-white">{{ number_format($stats['total']) }}</h3>
        </div>
        <div
            class="icon-box h-12 w-12 rounded-xl {{ !$currentStatus && !$currentGroup ? 'bg-brand-600 text-white' : 'bg-brand-50 dark:bg-brand-900/20 text-brand-600 dark:text-brand-400' }} flex items-center justify-center group-hover:scale-110 transition-transform">
            <i class="fas fa-layer-group text-xl"></i>
        </div>
    </a>
    <a href="{{ route('batches.index', ['status_group' => 'active']) }}"
        class="summary-card bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border {{ $currentGroup == 'active' ? 'border-teal-500 ring-1 ring-teal-500' : 'border-slate-200 dark:border-slate-700' }} flex items-center justify-between group hover:border-teal-200 dark:hover:border-teal-500/50">
        <div>
            <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Batch Aktif
            </p>
            <h3 class="text-2xl font-extrabold text-teal-600 dark:text-teal-400">{{ number_format($stats['active']) }}
            </h3>
        </div>
        <div
            class="icon-box h-12 w-12 rounded-xl {{ $currentGroup == 'active' ? 'bg-teal-600 text-white' : 'bg-teal-50 dark:bg-teal-900/20 text-teal-600 dark:text-teal-400' }} flex items-center justify-center group-hover:scale-110 transition-transform">
            <i class="fas fa-toggle-on text-xl"></i>
        </div>
    </a>
    <a href="{{ route('batches.index', ['status_group' => 'nonactive']) }}"
        class="summary-card bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border {{ $currentGroup == 'nonactive' ? 'border-slate-500 ring-1 ring-slate-500' : 'border-slate-200 dark:border-slate-700' }} flex items-center justify-between group hover:border-slate-300 dark:hover:border-slate-500/50">
        <div>
            <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Batch
                Non-Aktif</p>
            <h3 class="text-2xl font-extrabold text-slate-600 dark:text-slate-300">
                {{ number_format($stats['non_active']) }}</h3>
        </div>
        <div
            class="icon-box h-12 w-12 rounded-xl {{ $currentGroup == 'nonactive' ? 'bg-slate-500 text-white' : 'bg-slate-100 dark:bg-slate-700/50 text-slate-500 dark:text-slate-400' }} flex items-center justify-center group-hover:scale-110 transition-transform">
            <i class="fas fa-toggle-off text-xl"></i>
        </div>
    </a>
    <a href="{{ route('batches.index', ['status' => 'running']) }}"
        class="summary-card bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border {{ $currentStatus == 'running' ? 'border-emerald-500 ring-1 ring-emerald-500' : 'border-slate-200 dark:border-slate-700' }} flex items-center justify-between group hover:border-emerald-200 dark:hover:border-emerald-500/50">
        <div>
            <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Sedang Jalan
            </p>
            <h3 class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">
                {{ number_format($stats['running']) }}</h3>
        </div>
        <div
            class="icon-box h-12 w-12 rounded-xl {{ $currentStatus == 'running' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400' }} flex items-center justify-center group-hover:scale-110 transition-transform">
            <i class="fas fa-gears text-xl"></i>
        </div>
    </a>
    <a href="{{ route('batches.index', ['status' => 'planning']) }}"
        class="summary-card bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border {{ $currentStatus == 'planning' ? 'border-blue-500 ring-1 ring-blue-500' : 'border-slate-200 dark:border-slate-700' }} flex items-center justify-between group hover:border-blue-200 dark:hover:border-blue-500/50">
        <div>
            <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Perencanaan
            </p>
            <h3 class="text-2xl font-extrabold text-blue-600 dark:text-blue-400">
                {{ number_format($stats['planning']) }}</h3>
        </div>
        <div
            class="icon-box h-12 w-12 rounded-xl {{ $currentStatus == 'planning' ? 'bg-blue-600 text-white' : 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' }} flex items-center justify-center group-hover:scale-110 transition-transform">
            <i class="fas fa-calendar-check text-xl"></i>
        </div>
    </a>
    <a href="{{ route('batches.index', ['status' => 'completed']) }}"
        class="summary-card bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border {{ $currentStatus == 'completed' ? 'border-indigo-500 ring-1 ring-indigo-500' : 'border-slate-200 dark:border-slate-700' }} flex items-center justify-between group hover:border-indigo-200 dark:hover:border-indigo-500/50">
        <div>
            <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Selesai</p>
            <h3 class="text-2xl font-extrabold text-indigo-600 dark:text-indigo-400">
                {{ number_format($stats['completed']) }}</h3>
        </div>
        <div
            class="icon-box h-12 w-12 rounded-xl {{ $currentStatus == 'completed' ? 'bg-indigo-600 text-white' : 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400' }} flex items-center justify-center group-hover:scale-110 transition-transform">
            <i class="fas fa-box-archive text-xl"></i>
        </div>
    </a>
</div>
