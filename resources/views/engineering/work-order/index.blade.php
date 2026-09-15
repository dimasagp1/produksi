@extends('layouts.app')

@section('title', 'Work Order Maintenance')

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
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div
            class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border-l-4 border-rose-500 flex justify-between items-center">
            <div>
                <p class="text-xs text-slate-500 uppercase font-bold">Pending Request</p>
                <h3 class="text-3xl font-extrabold text-slate-800 dark:text-white">{{ $totalPending }}</h3>
            </div>
            <i class="fa-solid fa-bell text-3xl text-rose-200"></i>
        </div>
        <div
            class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border-l-4 border-amber-500 flex justify-between items-center">
            <div>
                <p class="text-xs text-slate-500 uppercase font-bold">Sedang Dikerjakan</p>
                <h3 class="text-3xl font-extrabold text-slate-800 dark:text-white">{{ $totalProgress }}</h3>
            </div>
            <i class="fa-solid fa-screwdriver-wrench text-3xl text-amber-200"></i>
        </div>
        <div
            class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border-l-4 border-emerald-500 flex justify-between items-center">
            <div>
                <p class="text-xs text-slate-500 uppercase font-bold">Selesai (Bulan Ini)</p>
                <h3 class="text-3xl font-extrabold text-slate-800 dark:text-white">{{ $totalCompleted }}</h3>
            </div>
            <i class="fa-solid fa-clipboard-check text-3xl text-emerald-200"></i>
        </div>
    </div>

    {{-- TOOLBAR --}}
    <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-subtle mb-6">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <button onclick="document.getElementById('modalCreate').classList.remove('hidden')"
                class="h-10 px-4 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow-md transition-all flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Buat WO Baru
            </button>

            <form action="{{ route('work-order.index') }}" method="GET" class="flex gap-2">
                <select name="status" class="saas-input h-10 w-32 cursor-pointer" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress
                    </option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" class="saas-input h-10 !pl-9"
                        placeholder="Cari WO/Mesin...">
                    <i class="fa-solid fa-search absolute left-3 top-3 text-slate-400"></i>
                </div>
            </form>
        </div>
    </div>

    {{-- TABEL --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-subtle overflow-hidden">
        <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
            <thead class="bg-slate-50/80 dark:bg-slate-700/50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">WO No & Tgl</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Mesin & Isu</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">Prioritas</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Teknisi</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">Status</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse ($workOrders as $wo)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-brand-600 font-mono">{{ $wo->wo_number }}</div>
                            <div class="text-xs text-slate-500">{{ $wo->created_at->format('d M H:i') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-slate-800 dark:text-white">{{ $wo->machine->name }}</div>
                            <div class="text-xs text-slate-600 italic line-clamp-1" title="{{ $wo->issue_description }}">
                                {{ $wo->issue_description }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $colors = [
                                    'low' => 'bg-slate-100 text-slate-600',
                                    'medium' => 'bg-blue-100 text-blue-600',
                                    'high' => 'bg-orange-100 text-orange-600',
                                    'critical' => 'bg-rose-100 text-rose-600 animate-pulse',
                                ];
                            @endphp
                            <span class="px-2 py-1 rounded text-[10px] font-bold uppercase {{ $colors[$wo->priority] }}">
                                {{ $wo->priority }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $wo->assigned_to ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if ($wo->status == 'pending')
                                <span
                                    class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-200 text-slate-700">PENDING</span>
                            @elseif($wo->status == 'in_progress')
                                <span
                                    class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">KERJA</span>
                            @elseif($wo->status == 'completed')
                                <span
                                    class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">SELESAI</span>
                            @else
                                <span
                                    class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-700">BATAL</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button onclick="editWo({{ json_encode($wo) }})" class="text-slate-400 hover:text-brand-600">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">Tidak ada Work Order.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700">{{ $workOrders->links() }}</div>
    </div>

    {{-- MODAL CREATE --}}
    <div id="modalCreate" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75"
                onclick="document.getElementById('modalCreate').classList.add('hidden')"></div>
            <div
                class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <form action="{{ route('work-order.store') }}" method="POST">
                    @csrf
                    <div class="bg-white dark:bg-slate-800 px-6 py-5">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Buat Permintaan Perbaikan</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Mesin
                                    Bermasalah</label>
                                <select name="machine_id" class="saas-input" required>
                                    @foreach ($machines as $m)
                                        <option value="{{ $m->id }}">{{ $m->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Prioritas</label>
                                <select name="priority" class="saas-input">
                                    <option value="low">Low - Tidak Mendesak</option>
                                    <option value="medium" selected>Medium - Normal</option>
                                    <option value="high">High - Mendesak</option>
                                    <option value="critical">Critical - Mesin Stop</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Deskripsi
                                    Masalah</label>
                                <textarea name="issue_description" class="saas-input h-24 pt-2" placeholder="Jelaskan kerusakan..." required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-700/50 px-6 py-4 flex justify-end gap-3">
                        <button type="button" onclick="document.getElementById('modalCreate').classList.add('hidden')"
                            class="px-4 py-2 border rounded-xl">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-brand-600 text-white rounded-xl">Kirim Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT --}}
    <div id="modalEdit" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75"
                onclick="document.getElementById('modalEdit').classList.add('hidden')"></div>
            <div
                class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <form id="formEdit" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="bg-white dark:bg-slate-800 px-6 py-5">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Update Work Order</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Status
                                    Pengerjaan</label>
                                <select name="status" id="edit_status" class="saas-input font-bold">
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">Sedang Dikerjakan</option>
                                    <option value="completed">Selesai</option>
                                    <option value="cancelled">Dibatalkan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Teknisi
                                    (Assigned To)</label>
                                <input type="text" name="assigned_to" id="edit_assigned_to" class="saas-input"
                                    placeholder="Nama Teknisi">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tindakan
                                    Perbaikan</label>
                                <textarea name="action_taken" id="edit_action_taken" class="saas-input h-24 pt-2"
                                    placeholder="Apa yang diperbaiki?"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-700/50 px-6 py-4 flex justify-end gap-3">
                        <button type="button" onclick="document.getElementById('modalEdit').classList.add('hidden')"
                            class="px-4 py-2 border rounded-xl">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-brand-600 text-white rounded-xl">Simpan
                            Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function editWo(wo) {
            document.getElementById('modalEdit').classList.remove('hidden');
            let url = "{{ route('work-order.update', ':id') }}";
            url = url.replace(':id', wo.id);
            document.getElementById('formEdit').action = url;

            document.getElementById('edit_status').value = wo.status;
            document.getElementById('edit_assigned_to').value = wo.assigned_to;
            document.getElementById('edit_action_taken').value = wo.action_taken;
        }
    </script>
@endsection
