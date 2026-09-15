@extends('layouts.app')

@section('title', 'Edit Routing: ' . $product->name)

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
        <a href="{{ route('routing.index') }}"
            class="h-10 w-10 flex items-center justify-center bg-white dark:bg-slate-800 rounded-xl shadow text-slate-500 hover:text-brand-600">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">{{ $product->name }}</h1>
            <p class="text-slate-500 text-sm">Pengaturan Urutan Proses Produksi</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- LIST STEPS --}}
        <div class="lg:col-span-2">
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 overflow-hidden">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                    <thead class="bg-slate-50/80 dark:bg-slate-700/50">
                        <tr>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">Urutan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Operasi</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Work Center</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">Std. Time</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($product->routings as $route)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30">
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-blue-100 text-blue-700 font-bold text-sm">
                                        {{ $route->step_number }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800 dark:text-white">{{ $route->operation_name }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ $route->work_center->name }}
                                </td>
                                <td class="px-6 py-4 text-center font-mono text-sm">
                                    {{ $route->standard_time }} {{ $route->time_unit }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <form action="{{ route('routing.step.destroy', $route->id) }}" method="POST"
                                        onsubmit="return confirm('Hapus langkah ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-slate-400 hover:text-rose-600">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                    Belum ada alur proses yang didefinisikan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- VISUAL FLOW SIMPLE --}}
            @if ($product->routings->count() > 0)
                <div
                    class="mt-6 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-dashed border-slate-300 flex flex-wrap gap-4 items-center justify-center">
                    @foreach ($product->routings as $index => $route)
                        <div
                            class="bg-white dark:bg-slate-700 px-4 py-2 rounded-lg shadow-sm text-sm font-bold text-slate-700 dark:text-slate-200">
                            {{ $route->step_number }}. {{ $route->operation_name }}
                        </div>
                        @if (!$loop->last)
                            <i class="fa-solid fa-arrow-right text-slate-300"></i>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

        {{-- FORM ADD STEP --}}
        <div
            class="bg-white dark:bg-slate-800 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 h-fit p-6">
            <h3 class="font-bold text-lg text-slate-800 dark:text-white mb-4">Tambah Proses</h3>
            <form action="{{ route('routing.step.store', $product->id) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-slate-500 mb-1">Urutan (Step)</label>
                            <input type="number" name="step_number" class="saas-input"
                                value="{{ ($product->routings->max('step_number') ?? 0) + 10 }}" required>
                            <p class="text-[10px] text-slate-400 mt-1">Gunakan kelipatan 10</p>
                        </div>
                        <div>
                            <label class="block text-sm text-slate-500 mb-1">Nama Operasi</label>
                            <input type="text" name="operation_name" class="saas-input" placeholder="Misal: Mixing"
                                required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm text-slate-500 mb-1">Work Center</label>
                        <select name="work_center_id" class="saas-input" required>
                            @foreach ($workCenters as $wc)
                                <option value="{{ $wc->id }}">{{ $wc->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-slate-500 mb-1">Waktu Standar</label>
                            <input type="number" step="0.1" name="standard_time" class="saas-input" placeholder="0"
                                required>
                        </div>
                        <div>
                            <label class="block text-sm text-slate-500 mb-1">Satuan</label>
                            <select name="time_unit" class="saas-input">
                                <option value="minutes">Menit</option>
                                <option value="seconds">Detik</option>
                                <option value="hours">Jam</option>
                            </select>
                        </div>
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
