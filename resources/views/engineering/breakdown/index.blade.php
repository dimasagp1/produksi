@extends('layouts.app')

@section('title', 'Machine Breakdown Report')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <style>
        .saas-input {
            height: 44px;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 0 16px;
            width: 100%;
            background-color: #f8fafc;
        }

        .dark .saas-input {
            background-color: #1e293b;
            border-color: #334155;
            color: #f1f5f9;
        }

        .saas-input:focus {
            outline: none;
            border-color: #3b82f6;
        }
    </style>

    {{-- STATISTIK --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div
            class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Total Kejadian</p>
                <h3 class="text-3xl font-extrabold text-slate-800 dark:text-white mt-1">{{ $totalEvents }} <span
                        class="text-sm font-normal text-slate-400">Kasus</span></h3>
            </div>
            <div
                class="h-12 w-12 rounded-xl bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center text-orange-600">
                <i class="fa-solid fa-triangle-exclamation text-xl"></i>
            </div>
        </div>
        <div
            class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Total Downtime</p>
                <h3 class="text-3xl font-extrabold text-rose-600 mt-1">{{ number_format($totalDowntime) }} <span
                        class="text-sm font-normal text-slate-400">Menit</span></h3>
            </div>
            <div class="h-12 w-12 rounded-xl bg-rose-50 dark:bg-rose-900/20 flex items-center justify-center text-rose-600">
                <i class="fa-solid fa-stopwatch text-xl"></i>
            </div>
        </div>
        <div
            class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Paling Sering Rusak</p>
                <h3 class="text-xl font-extrabold text-slate-800 dark:text-white mt-1 truncate w-40"
                    title="{{ $topMachine->machine->name ?? '-' }}">
                    {{ $topMachine->machine->name ?? '-' }}
                </h3>
                <p class="text-[10px] text-slate-400 mt-1">{{ $topMachine->total ?? 0 }} Kali Breakdown</p>
            </div>
            <div
                class="h-12 w-12 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center text-indigo-600">
                <i class="fa-solid fa-chart-simple text-xl"></i>
            </div>
        </div>
    </div>

    {{-- TOOLBAR --}}
    <div
        class="bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <button onclick="document.getElementById('modalCreate').classList.remove('hidden')"
                class="h-10 px-4 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl shadow-md transition-all flex items-center gap-2 animate-pulse">
                <i class="fa-solid fa-burst"></i> Lapor Breakdown
            </button>

            <form action="{{ route('breakdown.index') }}" method="GET"
                class="flex flex-col sm:flex-row gap-2 w-full md:w-auto items-end sm:items-center">
                <div class="flex gap-2 w-full sm:w-auto">
                    <input type="text" name="start_date" class="saas-input h-10 date-picker cursor-pointer w-32"
                        value="{{ $startDate }}" placeholder="Start">
                    <span class="text-slate-400">-</span>
                    <input type="text" name="end_date" class="saas-input h-10 date-picker cursor-pointer w-32"
                        value="{{ $endDate }}" placeholder="End">
                </div>
                <div class="relative w-full sm:max-w-xs">
                    <input type="text" name="search" value="{{ request('search') }}" class="saas-input h-10 !pl-9"
                        placeholder="Cari Kode/Mesin...">
                    <i class="fa-solid fa-search absolute left-3 top-3 text-slate-400"></i>
                </div>
                <button type="submit"
                    class="h-10 px-4 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition-all">
                    <i class="fa-solid fa-filter"></i>
                </button>
            </form>
        </div>
    </div>

    {{-- TABEL --}}
    <div
        class="bg-white dark:bg-slate-800 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                <thead class="bg-slate-50/80 dark:bg-slate-700/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Waktu Breakdown</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Mesin</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Masalah</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">Kategori</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">Downtime</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse ($reports as $item)
                        <tr
                            class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors {{ $item->status == 'open' ? 'bg-rose-50/50 dark:bg-rose-900/10' : '' }}">
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-slate-800 dark:text-white">
                                    {{ $item->breakdown_time->format('d M H:i') }}</div>
                                <div class="text-xs text-brand-600 font-mono">{{ $item->report_code }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-slate-700 dark:text-slate-300">
                                    {{ $item->machine->name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-slate-600 dark:text-slate-400 line-clamp-2"
                                    title="{{ $item->problem_description }}">
                                    {{ $item->problem_description }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="px-2 py-1 rounded text-[10px] uppercase font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                    {{ $item->category }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($item->status == 'resolved')
                                    <span
                                        class="text-sm font-mono font-bold text-rose-600">{{ $item->downtime_minutes }}m</span>
                                @else
                                    <span class="text-xs italic text-slate-400">Berjalan...</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($item->status == 'open')
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-bold bg-rose-600 text-white animate-pulse">OPEN</span>
                                @else
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">RESOLVED</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($item->status == 'open')
                                    <button onclick="resolveBreakdown({{ json_encode($item) }})"
                                        class="inline-flex items-center gap-1 px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold shadow">
                                        <i class="fa-solid fa-wrench"></i> Fix
                                    </button>
                                @else
                                    <button class="text-slate-400 cursor-not-allowed" disabled><i
                                            class="fa-solid fa-check"></i></button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">Tidak ada laporan breakdown.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700">{{ $reports->links() }}</div>
    </div>

    {{-- MODAL CREATE (START BREAKDOWN) --}}
    <div id="modalCreate" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75"
                onclick="document.getElementById('modalCreate').classList.add('hidden')"></div>
            <div
                class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <form action="{{ route('breakdown.store') }}" method="POST">
                    @csrf
                    <div class="bg-rose-600 px-6 py-4">
                        <h3 class="text-lg font-bold text-white"><i class="fa-solid fa-triangle-exclamation mr-2"></i>Lapor
                            Kerusakan Mesin</h3>
                    </div>
                    <div class="bg-white dark:bg-slate-800 px-6 py-5 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Waktu
                                    Kejadian</label>
                                <input type="text" name="breakdown_time" class="saas-input date-time-picker" required>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Kategori</label>
                                <select name="category" class="saas-input" required>
                                    <option value="mechanical">Mechanical</option>
                                    <option value="electrical">Electrical</option>
                                    <option value="software">Software / Program</option>
                                    <option value="utility">Utility (Air/Angin)</option>
                                    <option value="other">Lainnya</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Mesin</label>
                            <select name="machine_id" class="saas-input" required>
                                @foreach ($machines as $m)
                                    <option value="{{ $m->id }}">{{ $m->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Deskripsi
                                Masalah</label>
                            <textarea name="problem_description" class="saas-input h-24 pt-2"
                                placeholder="Apa yang terjadi? Suara kasar? Mati total?" required></textarea>
                        </div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-700/50 px-6 py-4 flex justify-end gap-3">
                        <button type="button" onclick="document.getElementById('modalCreate').classList.add('hidden')"
                            class="px-4 py-2 border rounded-xl">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-rose-600 text-white rounded-xl hover:bg-rose-700">Simpan Laporan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL RESOLVE (FINISH BREAKDOWN) --}}
    <div id="modalResolve" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75"
                onclick="document.getElementById('modalResolve').classList.add('hidden')"></div>
            <div
                class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <form id="formResolve" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="bg-emerald-600 px-6 py-4">
                        <h3 class="text-lg font-bold text-white"><i class="fa-solid fa-wrench mr-2"></i>Selesaikan
                            Perbaikan</h3>
                    </div>
                    <div class="bg-white dark:bg-slate-800 px-6 py-5 space-y-4">
                        <p class="text-sm text-slate-500 mb-2">Kode Laporan: <span id="res_code"
                                class="font-bold text-slate-800"></span></p>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Waktu Selesai
                                (Resolution Time)</label>
                            <input type="text" name="resolution_time" class="saas-input date-time-picker" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tindakan
                                Perbaikan</label>
                            <textarea name="action_taken" class="saas-input h-24 pt-2" placeholder="Apa yang diganti/diperbaiki?" required></textarea>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Teknisi</label>
                            <input type="text" name="technician" class="saas-input" placeholder="Nama Teknisi"
                                required>
                        </div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-700/50 px-6 py-4 flex justify-end gap-3">
                        <button type="button" onclick="document.getElementById('modalResolve').classList.add('hidden')"
                            class="px-4 py-2 border rounded-xl">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700">Selesai & Hitung
                            Downtime</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('load', function() {
            if (typeof flatpickr !== 'undefined') {
                flatpickr(".date-picker", {
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "d M Y"
                });
                flatpickr(".date-time-picker", {
                    enableTime: true,
                    dateFormat: "Y-m-d H:i",
                    altInput: true,
                    altFormat: "d M Y H:i",
                    time_24hr: true,
                    defaultDate: new Date()
                });
            }
        });

        function resolveBreakdown(item) {
            document.getElementById('modalResolve').classList.remove('hidden');
            document.getElementById('res_code').innerText = item.report_code;

            let url = "{{ route('breakdown.update', ':id') }}";
            url = url.replace(':id', item.id);
            document.getElementById('formResolve').action = url;
        }
    </script>
@endsection
