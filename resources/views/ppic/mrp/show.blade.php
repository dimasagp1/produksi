@extends('layouts.app')

@section('title', 'Material Requirements Planning (MRP)')

@section('content')
    <div class="flex flex-col gap-8 fade-in-up">

        {{-- 1. HEADER SECTION --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('mps.index') }}"
                    class="h-12 w-12 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-sm text-slate-500 hover:text-indigo-600 hover:border-indigo-200 transition-all active:scale-95">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight">
                            MRP: {{ $plan->period->translatedFormat('F Y') }}
                        </h1>
                        <span
                            class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-indigo-50 text-indigo-600 border border-indigo-100">
                            Planning
                        </span>
                    </div>
                    <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">
                        Kode Plan: <span class="font-mono text-indigo-500 font-bold">{{ $plan->plan_code }}</span>
                    </p>
                </div>
            </div>

            {{-- FORM CALCULATE --}}
            <form id="mrpForm" action="{{ route('mrp.generate', $plan->id) }}" method="POST">
                @csrf
                <button type="button" onclick="confirmCalculation()"
                    class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl shadow-lg shadow-indigo-500/30 transition-all active:scale-95 flex items-center gap-2">
                    <i class="fa-solid fa-calculator"></i>
                    <span>Hitung Kebutuhan</span>
                </button>
            </form>
        </div>

        {{-- 2. CONTENT SECTION --}}
        @if ($requirements->isEmpty())
            {{-- EMPTY STATE --}}
            <div
                class="flex flex-col items-center justify-center py-20 bg-white dark:bg-slate-800 rounded-[2.5rem] border border-dashed border-slate-300 dark:border-slate-700">
                <div class="h-24 w-24 bg-indigo-50 dark:bg-slate-700/50 rounded-full flex items-center justify-center mb-6">
                    <i class="fa-solid fa-calculator text-4xl text-indigo-400"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2">Data Belum Dihitung</h3>
                <p class="text-slate-500 dark:text-slate-400 text-center max-w-md mb-8">
                    Sistem perlu melakukan kalkulasi kebutuhan material berdasarkan Master Production Schedule (MPS) yang
                    telah dibuat.
                </p>
                <button type="button" onclick="confirmCalculation()"
                    class="px-8 py-3 bg-white border border-slate-200 text-slate-700 font-bold rounded-xl hover:bg-slate-50 hover:text-indigo-600 transition-all shadow-sm">
                    Lakukan Perhitungan Sekarang
                </button>
            </div>
        @else
            {{-- SUMMARY CARDS (CLICKABLE FILTER) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Card 1: Total Material (Filter All) --}}
                <div onclick="filterRows('all')" id="card-all"
                    class="bg-white dark:bg-slate-800 p-6 rounded-3xl border shadow-sm relative overflow-hidden group cursor-pointer transition-all duration-300 hover:-translate-y-1 hover:shadow-md ring-2 ring-indigo-500 ring-offset-2 ring-offset-slate-50 dark:ring-offset-slate-950 border-transparent">
                    <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <i class="fa-solid fa-boxes-stacked text-6xl text-slate-600"></i>
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Material</p>
                    <h3 class="text-3xl font-black text-slate-800 dark:text-white mt-1">
                        {{ $requirements->count() }} <span class="text-lg font-bold text-slate-400">Item</span>
                    </h3>
                    <div class="mt-2 text-[10px] font-bold text-indigo-500 uppercase tracking-wide flex items-center gap-1">
                        <i class="fa-solid fa-filter"></i> Menampilkan Semua
                    </div>
                </div>

                {{-- Card 2: Shortage (Filter Shortage) --}}
                <div onclick="filterRows('shortage')" id="card-shortage"
                    class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm relative overflow-hidden group cursor-pointer transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-rose-300">
                    <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <i class="fa-solid fa-cart-shopping text-6xl text-rose-600"></i>
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Perlu Dibeli (Shortage)</p>
                    <h3 class="text-3xl font-black text-slate-800 dark:text-white mt-1">
                        {{ $requirements->where('net_requirement', '>', 0)->count() }} <span
                            class="text-lg font-bold text-slate-400">Item</span>
                    </h3>
                    <div
                        class="mt-2 inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-rose-50 text-rose-600 text-[10px] font-bold uppercase">
                        <i class="fa-solid fa-circle-exclamation"></i> Action Required
                    </div>
                </div>

                {{-- Card 3: Safe Stock (Filter Safe) --}}
                <div onclick="filterRows('safe')" id="card-safe"
                    class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm relative overflow-hidden group cursor-pointer transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-emerald-300">
                    <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <i class="fa-solid fa-check-circle text-6xl text-emerald-600"></i>
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Stok Aman</p>
                    <h3 class="text-3xl font-black text-slate-800 dark:text-white mt-1">
                        {{ $requirements->where('net_requirement', '<=', 0)->count() }} <span
                            class="text-lg font-bold text-slate-400">Item</span>
                    </h3>
                    <div class="mt-2 text-[10px] font-medium text-slate-400">Stok Mencukupi</div>
                </div>
            </div>

            {{-- TABLE CARD --}}
            <div
                class="bg-white dark:bg-slate-800 rounded-[2rem] border border-slate-200 dark:border-slate-700 shadow-xl shadow-slate-200/40 dark:shadow-slate-900/40 overflow-hidden">
                <div class="p-6 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
                    <h3 class="font-bold text-lg text-slate-800 dark:text-white">Rincian Kebutuhan Material</h3>

                    {{-- Indikator Filter Aktif --}}
                    <div id="filter-indicator" class="hidden animate-pulse">
                        <span
                            class="px-3 py-1 bg-slate-100 dark:bg-slate-700 rounded-full text-xs font-bold text-slate-600 dark:text-slate-300">
                            Memfilter: <span id="filter-name">Semua Data</span>
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead class="bg-slate-50 dark:bg-slate-900/50">
                            <tr>
                                <th
                                    class="px-6 py-5 text-center text-[11px] font-extrabold text-slate-500 uppercase tracking-widest w-16">
                                    No</th>
                                <th
                                    class="px-6 py-5 text-left text-[11px] font-extrabold text-slate-500 uppercase tracking-widest">
                                    Material & Kode</th>
                                <th
                                    class="px-6 py-5 text-right text-[11px] font-extrabold text-slate-500 uppercase tracking-widest">
                                    Kebutuhan Kotor (Gross)</th>
                                <th
                                    class="px-6 py-5 text-right text-[11px] font-extrabold text-slate-500 uppercase tracking-widest">
                                    Stok Gudang</th>
                                <th
                                    class="px-6 py-5 text-right text-[11px] font-extrabold text-rose-600 dark:text-rose-400 uppercase tracking-widest">
                                    Kekurangan (Net)</th>
                                <th
                                    class="px-6 py-5 text-center text-[11px] font-extrabold text-slate-500 uppercase tracking-widest">
                                    Status</th>
                            </tr>
                        </thead>
                        <tbody id="mrpTableBody" class="divide-y divide-slate-100 dark:divide-slate-700/50">
                            @foreach ($requirements as $item)
                                {{-- Tambahkan class type-shortage atau type-safe untuk filtering --}}
                                <tr
                                    class="mrp-row {{ $item->net_requirement > 0 ? 'type-shortage' : 'type-safe' }} hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors group">

                                    {{-- No --}}
                                    <td class="px-6 py-4 text-center font-bold text-slate-400 text-xs">
                                        {{ $loop->iteration }}
                                    </td>

                                    {{-- Material --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="h-10 w-10 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-xs font-bold text-slate-500 dark:text-slate-400 shrink-0">
                                                {{ substr($item->raw_material->name, 0, 2) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-800 dark:text-white text-sm">
                                                    {{ $item->raw_material->name }}</div>
                                                <div
                                                    class="text-[10px] font-mono text-slate-400 mt-0.5 bg-slate-100 dark:bg-slate-700 inline-block px-1.5 rounded">
                                                    {{ $item->raw_material->material_code }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Gross --}}
                                    <td class="px-6 py-4 text-right">
                                        <span class="font-mono font-bold text-slate-700 dark:text-slate-300 text-sm">
                                            {{ number_format($item->gross_requirement, 2) }}
                                        </span>
                                        <span class="text-xs text-slate-400 ml-1">{{ $item->unit }}</span>
                                    </td>

                                    {{-- Stock --}}
                                    <td class="px-6 py-4 text-right">
                                        <span class="font-mono font-bold text-slate-600 dark:text-slate-400 text-sm">
                                            {{ number_format($item->current_stock, 2) }}
                                        </span>
                                        <span class="text-xs text-slate-400 ml-1">{{ $item->unit }}</span>
                                    </td>

                                    {{-- Net --}}
                                    <td class="px-6 py-4 text-right">
                                        @if ($item->net_requirement > 0)
                                            <div
                                                class="inline-block py-1 px-2 rounded bg-rose-50 dark:bg-rose-900/20 border border-rose-100 dark:border-rose-800">
                                                <span class="font-mono font-bold text-rose-600 dark:text-rose-400 text-sm">
                                                    {{ number_format($item->net_requirement, 2) }}
                                                </span>
                                                <span class="text-[10px] text-rose-400 ml-0.5">{{ $item->unit }}</span>
                                            </div>
                                        @else
                                            <span
                                                class="text-emerald-500 font-bold text-xs flex items-center justify-end gap-1">
                                                <i class="fa-solid fa-check"></i> Terpenuhi
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-6 py-4 text-center">
                                        @if ($item->net_requirement > 0)
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400 text-[10px] font-bold uppercase tracking-wide border border-rose-200 dark:border-rose-800">
                                                <i class="fa-solid fa-cart-plus"></i> Order
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-[10px] font-bold uppercase tracking-wide border border-slate-200 dark:border-slate-700">
                                                <i class="fa-solid fa-thumbs-up"></i> Aman
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Empty State saat filter tidak menemukan hasil --}}
                    <div id="no-filter-results" class="hidden py-12 text-center">
                        <p class="text-slate-400 italic">Tidak ada data untuk kategori ini.</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- 3. JAVASCRIPT LOGIC --}}
    <script>
        // --- A. Filter Table Logic (Client Side) ---
        function filterRows(type) {
            const rows = document.querySelectorAll('.mrp-row');
            const noRes = document.getElementById('no-filter-results');
            const indicator = document.getElementById('filter-indicator');
            const filterName = document.getElementById('filter-name');
            let hasVisible = false;

            // 1. Reset Style Cards
            resetCardStyles();

            // 2. Set Active Card Style
            const activeCard = document.getElementById('card-' + type);
            if (activeCard) {
                // Hapus border default
                activeCard.classList.remove('border-slate-200', 'dark:border-slate-700');
                // Tambahkan ring focus
                activeCard.classList.add('ring-2', 'ring-offset-2', 'ring-offset-slate-50', 'dark:ring-offset-slate-950',
                    'border-transparent');

                // Set warna ring sesuai tipe
                if (type === 'all') activeCard.classList.add('ring-indigo-500');
                if (type === 'shortage') activeCard.classList.add('ring-rose-500');
                if (type === 'safe') activeCard.classList.add('ring-emerald-500');
            }

            // 3. Update Indicator Text
            if (indicator) {
                indicator.classList.remove('hidden');
                if (type === 'all') filterName.innerText = "Semua Material";
                if (type === 'shortage') filterName.innerText = "Kekurangan (Order)";
                if (type === 'safe') filterName.innerText = "Stok Aman";
            }

            // 4. Show/Hide Rows
            rows.forEach(row => {
                if (type === 'all') {
                    row.style.display = '';
                    hasVisible = true;
                } else if (type === 'shortage' && row.classList.contains('type-shortage')) {
                    row.style.display = '';
                    hasVisible = true;
                } else if (type === 'safe' && row.classList.contains('type-safe')) {
                    row.style.display = '';
                    hasVisible = true;
                } else {
                    row.style.display = 'none';
                }
            });

            // 5. Show Empty State if Needed
            if (noRes) {
                noRes.classList.toggle('hidden', hasVisible);
            }
        }

        function resetCardStyles() {
            const cards = ['card-all', 'card-shortage', 'card-safe'];
            cards.forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    // Kembalikan ke style default (tidak aktif)
                    el.classList.remove('ring-2', 'ring-offset-2', 'ring-offset-slate-50',
                        'dark:ring-offset-slate-950', 'border-transparent', 'ring-indigo-500', 'ring-rose-500',
                        'ring-emerald-500');
                    el.classList.add('border-slate-200', 'dark:border-slate-700');
                }
            });
        }

        // --- B. Notifikasi Sukses/Gagal Otomatis ---
        document.addEventListener("DOMContentLoaded", function() {
            @if (session('success'))
                Swal.fire({
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'rounded-3xl'
                    }
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    title: 'Perhatian!',
                    text: "{{ session('error') }}",
                    icon: 'warning',
                    showConfirmButton: true,
                    confirmButtonText: 'Cek MPS',
                    confirmButtonColor: '#4f46e5',
                    customClass: {
                        popup: 'rounded-3xl',
                        confirmButton: 'rounded-xl font-bold px-6 py-2.5'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('mps.show', $plan->id) }}";
                    }
                });
            @endif
        });

        // --- C. Konfirmasi Sebelum Kalkulasi ---
        function confirmCalculation() {
            Swal.fire({
                title: 'Hitung Kebutuhan Material?',
                text: "Sistem akan menghitung kebutuhan bahan baku berdasarkan data Forecast dan Rencana Produksi pada MPS saat ini.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hitung!',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-3xl',
                    confirmButton: 'rounded-xl font-bold px-6 py-2.5',
                    cancelButton: 'rounded-xl font-bold px-6 py-2.5'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.getElementById('mrpForm');

                    Swal.fire({
                        title: 'Sedang Menghitung...',
                        text: 'Mohon tunggu sebentar, sistem sedang memproses BOM dan Stok.',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    form.submit();
                }
            });
        }
    </script>
@endsection
