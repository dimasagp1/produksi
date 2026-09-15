@extends('layouts.app')

@section('title', 'Preventive Maintenance (PM)')

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

    {{-- STATS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div
            class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border-l-4 border-rose-500 flex justify-between items-center">
            <div>
                <p class="text-xs text-slate-500 uppercase font-bold">Terlambat (Overdue)</p>
                <h3 class="text-3xl font-extrabold text-rose-600">{{ $totalOverdue }}</h3>
                <p class="text-[10px] text-slate-400">Jadwal Lewat Jatuh Tempo</p>
            </div>
            <i class="fa-solid fa-triangle-exclamation text-3xl text-rose-200"></i>
        </div>
        <div
            class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border-l-4 border-blue-500 flex justify-between items-center">
            <div>
                <p class="text-xs text-slate-500 uppercase font-bold">Jadwal Minggu Ini</p>
                <h3 class="text-3xl font-extrabold text-blue-600">{{ $dueThisWeek }}</h3>
                <p class="text-[10px] text-slate-400">Harus dikerjakan segera</p>
            </div>
            <i class="fa-solid fa-calendar-week text-3xl text-blue-200"></i>
        </div>
    </div>

    {{-- TOOLBAR --}}
    <div
        class="bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <button onclick="document.getElementById('modalCreate').classList.remove('hidden')"
                class="h-10 px-4 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow-md transition-all flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Tambah Jadwal PM
            </button>

            <form action="{{ route('preventive.index') }}" method="GET" class="flex gap-2">
                <select name="frequency" class="saas-input h-10 w-40 cursor-pointer" onchange="this.form.submit()">
                    <option value="">Semua Frekuensi</option>
                    <option value="weekly" {{ request('frequency') == 'weekly' ? 'selected' : '' }}>Mingguan</option>
                    <option value="monthly" {{ request('frequency') == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                    <option value="yearly" {{ request('frequency') == 'yearly' ? 'selected' : '' }}>Tahunan</option>
                </select>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" class="saas-input h-10 !pl-9"
                        placeholder="Cari Mesin/Tugas...">
                    <i class="fa-solid fa-search absolute left-3 top-3 text-slate-400"></i>
                </div>
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
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Jatuh Tempo</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Mesin & Tugas</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Frekuensi</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Terakhir Dikerjakan</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse ($schedules as $item)
                        @php
                            $isOverdue = \Carbon\Carbon::today()->gt($item->next_due_date);
                        @endphp
                        <tr
                            class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors {{ $isOverdue ? 'bg-rose-50/50 dark:bg-rose-900/10' : '' }}">
                            <td class="px-6 py-4">
                                <div
                                    class="text-sm font-bold {{ $isOverdue ? 'text-rose-600 animate-pulse' : 'text-slate-700 dark:text-slate-300' }}">
                                    {{ $item->next_due_date->format('d M Y') }}
                                </div>
                                @if ($isOverdue)
                                    <span class="text-[10px] font-bold text-rose-600 uppercase">Terlambat</span>
                                @else
                                    <span
                                        class="text-[10px] text-slate-400">{{ $item->next_due_date->diffForHumans() }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-slate-800 dark:text-white">{{ $item->machine->name }}
                                </div>
                                <div class="text-xs text-slate-600 italic">{{ $item->task_name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2 py-1 rounded text-[10px] font-bold uppercase bg-blue-100 text-blue-700 border border-blue-200">
                                    {{ $item->frequency }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                {{ $item->last_maintenance_date ? $item->last_maintenance_date->format('d M Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <form action="{{ route('preventive.complete', $item->id) }}" method="POST"
                                    onsubmit="return confirm('Tandai perawatan ini sudah selesai dilakukan hari ini?');">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold shadow transition-transform active:scale-95">
                                        <i class="fa-solid fa-check"></i> Selesai
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">Belum ada jadwal maintenance.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700">{{ $schedules->links() }}</div>
    </div>

    {{-- MODAL CREATE --}}
    <div id="modalCreate" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75"
                onclick="document.getElementById('modalCreate').classList.add('hidden')"></div>
            <div
                class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <form action="{{ route('preventive.store') }}" method="POST">
                    @csrf
                    <div class="bg-white dark:bg-slate-800 px-6 py-5">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Buat Jadwal Rutin</h3>
                        <div class="space-y-4">
                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Mesin</label>
                                <select name="machine_id" class="saas-input" required>
                                    @foreach ($machines as $m)
                                        <option value="{{ $m->id }}">{{ $m->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama
                                    Kegiatan (Task)</label>
                                <input type="text" name="task_name" class="saas-input"
                                    placeholder="Contoh: Ganti Filter Oli" required>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Frekuensi</label>
                                    <select name="frequency" class="saas-input" required>
                                        <option value="daily">Harian</option>
                                        <option value="weekly">Mingguan</option>
                                        <option value="monthly" selected>Bulanan</option>
                                        <option value="quarterly">3 Bulanan</option>
                                        <option value="yearly">Tahunan</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Jadwal
                                        Pertama</label>
                                    <input type="text" name="next_due_date" class="saas-input date-picker"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">PIC
                                    (Teknisi)</label>
                                <input type="text" name="assigned_to" class="saas-input"
                                    placeholder="Nama Penanggung Jawab">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Instruksi
                                    Kerja</label>
                                <textarea name="description" class="saas-input h-20 pt-2" placeholder="Detail langkah-langkah..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-700/50 px-6 py-4 flex justify-end gap-3">
                        <button type="button" onclick="document.getElementById('modalCreate').classList.add('hidden')"
                            class="px-4 py-2 border rounded-xl">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-brand-600 text-white rounded-xl">Simpan Jadwal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('load', function() {
            if (typeof flatpickr !== 'undefined') flatpickr(".date-picker", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d M Y"
            });
        });
    </script>
@endsection
