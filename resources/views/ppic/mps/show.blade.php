@extends('layouts.app')

@section('title', 'Detail MPS: ' . $plan->period->format('F Y'))

@section('content')
    {{-- Custom CSS untuk Input Table --}}
    <style>
        .input-cell {
            background: transparent;
            width: 100%;
            text-align: center;
            font-weight: 600;
            outline: none;
            border-radius: 0.5rem;
            padding: 0.5rem;
            transition: all 0.2s;
        }

        /* Focus state yang modern */
        .input-cell:focus {
            background-color: white;
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.5);
            /* Indigo-500 ring */
        }

        .dark .input-cell:focus {
            background-color: #1e293b;
            /* Slate-800 */
            box-shadow: 0 0 0 2px rgba(129, 140, 248, 0.5);
            /* Indigo-400 ring */
        }

        /* Menghilangkan panah pada input number */
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>

    {{-- 1. HEADER SECTION --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8 fade-in-up">

        {{-- Kiri: Judul & Info --}}
        <div class="flex items-center gap-4">
            <a href="{{ route('mps.index') }}"
                class="h-12 w-12 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-sm text-slate-500 hover:text-indigo-600 hover:border-indigo-200 transition-all active:scale-95">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight">
                        {{ $plan->period->translatedFormat('F Y') }}
                    </h1>
                    {{-- Status Badge --}}
                    <span
                        class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border
                        {{ $plan->status == 'draft' ? 'bg-slate-100 text-slate-600 border-slate-200' : '' }}
                        {{ $plan->status == 'confirmed' ? 'bg-blue-50 text-blue-600 border-blue-100' : '' }}
                        {{ $plan->status == 'closed' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : '' }}">
                        {{ $plan->status }}
                    </span>
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">
                    Kode Rencana: <span class="font-mono text-indigo-500 font-bold">{{ $plan->plan_code }}</span>
                </p>
            </div>
        </div>

        {{-- Kanan: Tombol Aksi --}}
        @if ($plan->status != 'closed')
            <div class="flex items-center gap-3">
                <button type="button" onclick="confirmAction('draft')"
                    class="px-5 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 font-bold rounded-xl shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-all active:scale-95 flex items-center gap-2">
                    <i class="fa-regular fa-floppy-disk"></i>
                    <span>Simpan Perubahan</span>
                </button>

                <button type="button" onclick="confirmAction('confirm')"
                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-500/30 transition-all active:scale-95 flex items-center gap-2">
                    <i class="fa-solid fa-check-circle"></i>
                    <span>Konfirmasi MPS</span>
                </button>
            </div>
        @endif
    </div>

    {{-- 2. MAIN CONTENT CARD --}}
    <div
        class="bg-white dark:bg-slate-800 rounded-[2rem] border border-slate-200 dark:border-slate-700 shadow-xl shadow-slate-200/40 dark:shadow-slate-900/40 overflow-hidden fade-in-up delay-100 flex flex-col h-full">

        {{-- Toolbar: Search Bar --}}
        <div
            class="p-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50 flex justify-between items-center sticky top-0 z-30">
            <div class="relative w-full max-w-md group">
                <i
                    class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                <input type="text" id="searchInput" onkeyup="filterTable()"
                    placeholder="Cari nama produk atau kode SKU..."
                    class="w-full pl-10 pr-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all shadow-sm">
            </div>
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider hidden sm:block">
                Total Item: <span class="text-indigo-500 text-sm">{{ $plan->items->count() }}</span>
            </div>
        </div>

        {{-- Form Wrapper --}}
        <form id="mpsForm" action="{{ route('mps.update-items', $plan->id) }}" method="POST"
            class="flex-1 overflow-hidden flex flex-col">
            @csrf

            {{-- Scrollable Table Container --}}
            <div class="overflow-x-auto custom-scroll flex-1" style="max-height: 65vh;">
                <table id="mpsTable" class="min-w-full divide-y divide-slate-200 dark:divide-slate-700 border-collapse">

                    {{-- Table Header (Sticky) --}}
                    <thead class="bg-slate-50 dark:bg-slate-900/90 backdrop-blur-sm sticky top-0 z-20 shadow-sm">
                        <tr>
                            {{-- KOLOM NO (Sticky Layer 1) --}}
                            <th
                                class="px-4 py-5 text-center text-[11px] font-extrabold text-slate-500 uppercase tracking-widest sticky left-0 bg-slate-50 dark:bg-slate-900 z-30 w-16 border-r border-slate-200 dark:border-slate-700 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">
                                No
                            </th>

                            {{-- KOLOM PRODUK (Sticky Layer 2) --}}
                            <th
                                class="px-6 py-5 text-left text-[11px] font-extrabold text-slate-500 uppercase tracking-widest sticky left-16 bg-slate-50 dark:bg-slate-900 z-30 shadow-[4px_0_12px_-4px_rgba(0,0,0,0.1)]">
                                Produk & SKU
                            </th>

                            <th
                                class="px-4 py-5 text-center text-[11px] font-extrabold text-slate-500 uppercase tracking-widest bg-slate-100/50 dark:bg-slate-800/50 w-36">
                                Stok Awal
                            </th>
                            <th
                                class="px-4 py-5 text-center text-[11px] font-extrabold text-amber-600 dark:text-amber-500 uppercase tracking-widest bg-amber-50/50 dark:bg-amber-900/10 w-36 border-l border-r border-amber-100 dark:border-amber-900/20">
                                Forecast (Jual)
                            </th>
                            <th
                                class="px-4 py-5 text-center text-[11px] font-extrabold text-indigo-600 dark:text-indigo-400 uppercase tracking-widest bg-indigo-50/50 dark:bg-indigo-900/10 w-36 border-r border-indigo-100 dark:border-indigo-900/20">
                                Rencana Prod
                            </th>
                            <th
                                class="px-4 py-5 text-center text-[11px] font-extrabold text-slate-500 uppercase tracking-widest bg-slate-100/50 dark:bg-slate-800/50 w-36">
                                Stok Akhir
                            </th>
                        </tr>
                    </thead>

                    {{-- Table Body --}}
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                        @foreach ($plan->items as $item)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/20 transition-colors group item-row">

                                {{-- KOLOM 1: NO (Sticky Layer 1) --}}
                                <td
                                    class="px-4 py-4 text-center font-bold text-slate-400 text-xs sticky left-0 bg-white dark:bg-slate-800 z-10 border-r border-slate-100 dark:border-slate-700 group-hover:bg-slate-50 dark:group-hover:bg-slate-800 transition-colors">
                                    {{ $loop->iteration }}
                                </td>

                                {{-- KOLOM 2: PRODUK (Sticky Layer 2) --}}
                                <td
                                    class="px-6 py-4 sticky left-16 bg-white dark:bg-slate-800 z-10 group-hover:bg-slate-50 dark:group-hover:bg-slate-800 transition-colors shadow-[4px_0_12px_-4px_rgba(0,0,0,0.05)] border-r border-slate-100 dark:border-slate-700">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-10 w-10 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-xs font-bold text-slate-500 dark:text-slate-400 shrink-0">
                                            {{ substr($item->product->name, 0, 2) }}
                                        </div>
                                        <div>
                                            {{-- Class searchable-name & code utk JS --}}
                                            <div class="font-bold text-slate-800 dark:text-white text-sm searchable-name line-clamp-1"
                                                title="{{ $item->product->name }}">
                                                {{ $item->product->name }}
                                            </div>
                                            <div
                                                class="text-[10px] font-mono text-slate-400 mt-0.5 bg-slate-100 dark:bg-slate-700 inline-block px-1.5 rounded searchable-code">
                                                {{ $item->product->code }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- KOLOM 3: STOK AWAL --}}
                                <td class="px-2 py-4 text-center bg-slate-50/30 dark:bg-slate-900/20">
                                    <div
                                        class="py-2 px-3 rounded-lg bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 font-mono font-bold text-sm">
                                        {{ number_format($item->beginning_stock) }}
                                    </div>
                                    <input type="hidden" id="beg_{{ $item->id }}"
                                        value="{{ $item->beginning_stock }}">
                                </td>

                                {{-- KOLOM 4: FORECAST --}}
                                <td
                                    class="px-2 py-4 text-center bg-amber-50/10 dark:bg-amber-900/5 border-l border-r border-slate-100 dark:border-slate-700/50">
                                    <input type="number" name="items[{{ $item->id }}][sales_forecast]"
                                        id="fc_{{ $item->id }}" value="{{ $item->sales_forecast }}"
                                        class="input-cell text-amber-600 font-bold bg-amber-50/50 dark:bg-amber-900/20 focus:bg-white dark:focus:bg-slate-800 border border-transparent focus:border-amber-400"
                                        placeholder="0" oninput="calc({{ $item->id }})"
                                        {{ $plan->status == 'closed' ? 'readonly' : '' }}>
                                </td>

                                {{-- KOLOM 5: PRODUKSI --}}
                                <td
                                    class="px-2 py-4 text-center bg-indigo-50/10 dark:bg-indigo-900/5 border-r border-slate-100 dark:border-slate-700/50">
                                    <input type="number" name="items[{{ $item->id }}][production_qty]"
                                        id="prod_{{ $item->id }}" value="{{ $item->production_qty }}"
                                        class="input-cell text-indigo-600 font-bold bg-indigo-50/50 dark:bg-indigo-900/20 focus:bg-white dark:focus:bg-slate-800 border border-transparent focus:border-indigo-400"
                                        placeholder="0" oninput="calc({{ $item->id }})"
                                        {{ $plan->status == 'closed' ? 'readonly' : '' }}>
                                </td>

                                {{-- KOLOM 6: STOK AKHIR --}}
                                <td class="px-4 py-4 text-center bg-slate-50/30 dark:bg-slate-900/20">
                                    <div
                                        class="py-2 px-3 rounded-lg bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                                        <span id="end_txt_{{ $item->id }}"
                                            class="font-mono font-bold text-sm {{ $item->ending_stock < 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                            {{ number_format($item->ending_stock) }}
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- State Empty Search --}}
                <div id="noResults" class="hidden py-12 text-center bg-white dark:bg-slate-800">
                    <div
                        class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 mb-3">
                        <i class="fa-solid fa-magnifying-glass text-2xl"></i>
                    </div>
                    <p class="text-slate-500 font-medium">Produk tidak ditemukan.</p>
                </div>
            </div>
        </form>
    </div>

    {{-- 3. JAVASCRIPT LOGIC --}}
    <script>
        // --- A. Notifikasi Sukses / Gagal (Tanpa Tombol OK) ---
        document.addEventListener("DOMContentLoaded", function() {
            @if (session('success'))
                Swal.fire({
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    icon: 'success',
                    showConfirmButton: false, // Hilangkan tombol OK
                    timer: 2000, // Auto close 2 detik
                    timerProgressBar: true,
                    customClass: {
                        popup: 'rounded-3xl'
                    }
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    title: 'Gagal!',
                    text: "{{ session('error') }}",
                    icon: 'error',
                    showConfirmButton: false, // Hilangkan tombol OK
                    timer: 3000, // Auto close 3 detik (lebih lama utk dibaca)
                    timerProgressBar: true,
                    customClass: {
                        popup: 'rounded-3xl'
                    }
                });
            @endif
        });

        // --- B. Kalkulasi Otomatis ---
        function calc(id) {
            let beg = parseInt(document.getElementById('beg_' + id).value) || 0;
            let fc = parseInt(document.getElementById('fc_' + id).value) || 0;
            let prod = parseInt(document.getElementById('prod_' + id).value) || 0;

            let end = beg + prod - fc;

            let el = document.getElementById('end_txt_' + id);
            el.innerText = end.toLocaleString('id-ID');

            if (end < 0) {
                el.classList.remove('text-emerald-600');
                el.classList.add('text-rose-600');
            } else {
                el.classList.remove('text-rose-600');
                el.classList.add('text-emerald-600');
            }
        }

        // --- C. Fitur Live Search Table ---
        function filterTable() {
            let input = document.getElementById("searchInput");
            let filter = input.value.toUpperCase();
            let table = document.getElementById("mpsTable");
            let tr = table.getElementsByTagName("tr");
            let hasResult = false;

            for (let i = 1; i < tr.length; i++) {
                let nameEl = tr[i].querySelector(".searchable-name");
                let codeEl = tr[i].querySelector(".searchable-code");

                if (nameEl || codeEl) {
                    let nameValue = nameEl.textContent || nameEl.innerText;
                    let codeValue = codeEl.textContent || codeEl.innerText;

                    if (nameValue.toUpperCase().indexOf(filter) > -1 || codeValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                        hasResult = true;
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }

            let noResDiv = document.getElementById("noResults");
            if (!hasResult) {
                noResDiv.classList.remove("hidden");
                table.classList.add("hidden");
            } else {
                noResDiv.classList.add("hidden");
                table.classList.remove("hidden");
            }
        }

        // --- D. SweetAlert2 Confirmation Logic ---
        function confirmAction(type) {
            let title, text, icon, confirmBtnText, confirmBtnColor, inputName;

            if (type === 'confirm') {
                title = 'Kunci Rencana Produksi?';
                text = "Status akan berubah menjadi CONFIRMED. Data tidak dapat diedit sementara.";
                icon = 'warning';
                confirmBtnText = 'Ya, Konfirmasi!';
                confirmBtnColor = '#4f46e5';
                inputName = 'confirm_plan';
            } else {
                title = 'Simpan Perubahan?';
                text = "Data inputan akan disimpan sebagai DRAFT.";
                icon = 'question';
                confirmBtnText = 'Ya, Simpan';
                confirmBtnColor = '#64748b';
                inputName = 'save_draft';
            }

            Swal.fire({
                title: title,
                text: text,
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: confirmBtnColor,
                cancelButtonColor: '#94a3b8',
                confirmButtonText: confirmBtnText,
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-3xl',
                    confirmButton: 'rounded-xl font-bold px-6 py-2.5',
                    cancelButton: 'rounded-xl font-bold px-6 py-2.5'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.getElementById('mpsForm');

                    let input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = inputName;
                    input.value = '1';
                    form.appendChild(input);

                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Mohon tunggu sebentar.',
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
