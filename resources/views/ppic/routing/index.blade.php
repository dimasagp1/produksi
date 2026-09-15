@extends('layouts.app')

@section('title', 'Production Routing')

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

    <div x-data="{ tab: 'products' }">
        {{-- TABS --}}
        <div class="flex gap-2 mb-6 bg-white dark:bg-slate-800 p-2 rounded-2xl w-fit shadow-subtle">
            <button @click="tab = 'products'"
                :class="tab === 'products' ? 'bg-brand-600 text-white shadow' : 'text-slate-500 hover:bg-slate-50'"
                class="px-5 py-2 rounded-xl font-bold transition-all text-sm">
                <i class="fa-solid fa-list-ol mr-2"></i> Produk & Alur
            </button>
            <button @click="tab = 'wc'"
                :class="tab === 'wc' ? 'bg-brand-600 text-white shadow' : 'text-slate-500 hover:bg-slate-50'"
                class="px-5 py-2 rounded-xl font-bold transition-all text-sm">
                <i class="fa-solid fa-industry mr-2"></i> Work Centers
            </button>
        </div>

        {{-- TAB 1: PRODUCTS --}}
        <div x-show="tab === 'products'"
            class="bg-white dark:bg-slate-800 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 overflow-hidden">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                <thead class="bg-slate-50/80 dark:bg-slate-700/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Produk</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">Jumlah Proses</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @foreach ($products as $product)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800 dark:text-white">{{ $product->name }}</div>
                                <div class="text-xs text-slate-400">{{ $product->code }}</div>
                            </td>
                            <td class="px-6 py-4 text-center text-sm font-bold text-slate-600">
                                {{ $product->routings_count }} Langkah
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($product->routings_count > 0)
                                    <span
                                        class="bg-emerald-100 text-emerald-700 text-[10px] px-2 py-1 rounded-full font-bold">MAPPED</span>
                                @else
                                    <span
                                        class="bg-slate-100 text-slate-500 text-[10px] px-2 py-1 rounded-full font-bold">NO
                                        ROUTE</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('routing.show', $product->id) }}"
                                    class="text-brand-600 hover:text-brand-700 font-bold text-sm">
                                    <i class="fa-solid fa-pen-to-square"></i> Atur Alur
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- TAB 2: WORK CENTERS --}}
        <div x-show="tab === 'wc'" style="display: none;">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Form Add --}}
                <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-subtle h-fit">
                    <h3 class="font-bold text-lg mb-4 text-slate-800 dark:text-white">Tambah Work Center</h3>
                    <form action="{{ route('routing.wc.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm text-slate-500 mb-1">Kode WC</label>
                                <input type="text" name="wc_code" class="saas-input" placeholder="WC-01" required>
                            </div>
                            <div>
                                <label class="block text-sm text-slate-500 mb-1">Nama Work Center</label>
                                <input type="text" name="name" class="saas-input" placeholder="Misal: Assembly Line 1"
                                    required>
                            </div>
                            <div>
                                <label class="block text-sm text-slate-500 mb-1">Cost / Jam (Rp)</label>
                                <input type="number" name="cost_per_hour" class="saas-input" value="0">
                            </div>
                            <button
                                class="w-full py-3 bg-brand-600 text-white font-bold rounded-xl hover:bg-brand-700">Simpan</button>
                        </div>
                    </form>
                </div>

                {{-- List WC --}}
                <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl shadow-subtle overflow-hidden">
                    <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                        <thead class="bg-slate-50/80 dark:bg-slate-700/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Kode</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Nama</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase">Cost/Jam</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($workCenters as $wc)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30">
                                    <td class="px-6 py-4 font-mono font-bold text-brand-600">{{ $wc->wc_code }}</td>
                                    <td class="px-6 py-4 font-bold text-slate-800 dark:text-white">{{ $wc->name }}</td>
                                    <td class="px-6 py-4 text-right">Rp {{ number_format($wc->cost_per_hour) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
