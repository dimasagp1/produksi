@extends('layouts.app')

@section('title', 'Edit BOM: ' . $product->name)

@section('content')
    <style>
        .saas-input {
            height: 44px;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 0 16px;
            width: 100%;
            background-color: #f8fafc;
        }

        .saas-input:focus {
            outline: none;
            border-color: #3b82f6;
        }
    </style>

    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('bom.index') }}"
            class="h-10 w-10 flex items-center justify-center bg-white dark:bg-slate-800 rounded-xl shadow text-slate-500 hover:text-brand-600">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">{{ $product->name }}</h1>
            <p class="text-slate-500 text-sm">Kode Produk: {{ $product->code }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT: BOM LIST --}}
        <div
            class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="p-5 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                <h3 class="font-bold text-slate-800 dark:text-white">Komposisi Material</h3>
                <div class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-lg">
                    Estimasi Biaya: Rp {{ number_format($totalCost, 2) }} / Unit
                </div>
            </div>

            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                <thead class="bg-slate-50/80 dark:bg-slate-700/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Material</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">Jumlah (Qty)</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Satuan</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase">Subtotal (Est)</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($product->bom_items as $item)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800 dark:text-white">{{ $item->raw_material->name }}</div>
                                <div class="text-xs text-slate-400 font-mono">{{ $item->raw_material->material_code }}</div>
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-blue-600">
                                {{ number_format($item->quantity, 4) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $item->unit }}</td>
                            <td class="px-6 py-4 text-right text-sm text-slate-500">
                                Rp {{ number_format($item->quantity * $item->raw_material->std_cost, 0) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <form action="{{ route('bom.item.destroy', $item->id) }}" method="POST"
                                    onsubmit="return confirm('Hapus material ini dari resep?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-rose-600 transition-colors">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center">
                                    <i class="fa-solid fa-flask text-4xl mb-2 opacity-20"></i>
                                    <p>Belum ada material yang ditambahkan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- RIGHT: ADD FORM --}}
        <div
            class="bg-white dark:bg-slate-800 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 h-fit p-6">
            <h3 class="font-bold text-lg text-slate-800 dark:text-white mb-4">Tambah Material</h3>
            <form action="{{ route('bom.item.store', $product->id) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Pilih
                            Material</label>
                        <select name="raw_material_id" class="saas-input" required>
                            <option value="">-- Cari Material --</option>
                            @foreach ($materials as $mat)
                                <option value="{{ $mat->id }}">{{ $mat->material_code }} - {{ $mat->name }}
                                    ({{ $mat->unit }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Jumlah Pemakaian
                            (Per 1 Unit Produk)</label>
                        <input type="number" step="0.0001" name="quantity" class="saas-input" placeholder="0.00" required>
                        <p class="text-[10px] text-slate-400 mt-1">*Masukkan angka desimal untuk takaran kecil (misal: 0.005
                            Kg)</p>
                    </div>

                    <button type="submit"
                        class="w-full py-3 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow-lg transition-transform active:scale-95">
                        <i class="fa-solid fa-plus mr-1"></i> Tambahkan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
