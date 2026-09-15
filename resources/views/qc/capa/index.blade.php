@extends('layouts.app')

@section('title', 'CAPA - Corrective & Preventive Action')

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
                <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Status Open</p>
                <h3 class="text-3xl font-extrabold text-blue-600 mt-1">{{ $totalOpen }}</h3>
                <p class="text-[10px] text-slate-400 mt-1">Belum Selesai</p>
            </div>
            <div class="h-12 w-12 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600">
                <i class="fa-solid fa-folder-open text-xl"></i>
            </div>
        </div>
        <div
            class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Overdue</p>
                <h3 class="text-3xl font-extrabold text-rose-600 mt-1">{{ $totalOverdue }}</h3>
                <p class="text-[10px] text-slate-400 mt-1">Melewati Batas Waktu</p>
            </div>
            <div class="h-12 w-12 rounded-xl bg-rose-50 dark:bg-rose-900/20 flex items-center justify-center text-rose-600">
                <i class="fa-solid fa-clock text-xl"></i>
            </div>
        </div>
        <div
            class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Closed</p>
                <h3 class="text-3xl font-extrabold text-emerald-600 mt-1">{{ $totalClosed }}</h3>
                <p class="text-[10px] text-slate-400 mt-1">Masalah Tuntas</p>
            </div>
            <div
                class="h-12 w-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center text-emerald-600">
                <i class="fa-solid fa-check text-xl"></i>
            </div>
        </div>
    </div>

    {{-- TOOLBAR --}}
    <div
        class="bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <button onclick="document.getElementById('modalNewCapa').classList.remove('hidden')"
                class="h-10 px-4 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow-md transition-all flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Buat CAPA
            </button>

            <form action="{{ route('capa.index') }}" method="GET" class="flex gap-2">
                <select name="status" class="saas-input h-10 w-32 cursor-pointer" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" class="saas-input h-10 !pl-9"
                        placeholder="Cari Masalah...">
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
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Kode & Tgl</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Sumber & Masalah</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">PIC & Due Date</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse ($actions as $item)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-brand-600 font-mono">{{ $item->code }}</div>
                                <div class="text-xs text-slate-500">{{ $item->issue_date->format('d M Y') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="text-[10px] font-bold uppercase text-slate-400 border border-slate-200 px-1.5 rounded">{{ $item->source }}</span>
                                <div class="text-sm text-slate-700 dark:text-slate-300 mt-1 line-clamp-2">
                                    {{ $item->problem_description }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-slate-800 dark:text-white">{{ $item->pic }}</div>
                                <div
                                    class="text-xs {{ $item->status == 'open' && $item->due_date < now() ? 'text-rose-600 font-bold' : 'text-slate-500' }}">
                                    Due: {{ $item->due_date ? $item->due_date->format('d M') : '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($item->status == 'open')
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">OPEN</span>
                                @else
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">CLOSED</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button onclick="editCapa({{ json_encode($item) }})"
                                    class="text-slate-400 hover:text-brand-600 transition-colors">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">Tidak ada data CAPA.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700">{{ $actions->links() }}</div>
    </div>

    {{-- MODAL FORM (Create & Edit) --}}
    <div id="modalNewCapa" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closeModal()"></div>
            <div
                class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
                <form id="capaForm" action="{{ route('capa.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">

                    <div class="bg-white dark:bg-slate-800 px-6 py-5">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4" id="modalTitle">Form CAPA Baru
                        </h3>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tanggal
                                    Isu</label>
                                <input type="text" name="issue_date" id="issue_date" class="saas-input date-picker"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Sumber
                                    Masalah</label>
                                <select name="source" id="source" class="saas-input">
                                    <option value="Internal Audit">Internal Audit</option>
                                    <option value="Customer Complaint">Customer Complaint</option>
                                    <option value="Patrol IPQC">Patrol IPQC</option>
                                    <option value="OQC Reject">OQC Reject</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Deskripsi
                                Masalah</label>
                            <textarea name="problem_description" id="problem_description" class="saas-input h-20 pt-2" required></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Analisa Akar
                                Masalah (Root Cause)</label>
                            <textarea name="root_cause" id="root_cause" class="saas-input h-20 pt-2" placeholder="Mengapa bisa terjadi?"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tindakan
                                    Perbaikan</label>
                                <textarea name="corrective_action" id="corrective_action" class="saas-input h-20 pt-2"
                                    placeholder="Fix jangka pendek"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tindakan
                                    Pencegahan</label>
                                <textarea name="preventive_action" id="preventive_action" class="saas-input h-20 pt-2"
                                    placeholder="Agar tidak terulang"></textarea>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">PIC</label>
                                <input type="text" name="pic" id="pic" class="saas-input" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Due
                                    Date</label>
                                <input type="text" name="due_date" id="due_date" class="saas-input date-picker"
                                    required>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Status</label>
                                <select name="status" id="status" class="saas-input font-bold">
                                    <option value="open">OPEN</option>
                                    <option value="closed">CLOSED</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-700/50 px-6 py-4 flex justify-end gap-3">
                        <button type="button" onclick="closeModal()"
                            class="px-4 py-2 border border-slate-300 rounded-xl text-slate-700 hover:bg-white transition">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-brand-600 text-white rounded-xl hover:bg-brand-700 transition">Simpan
                            CAPA</button>
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

        function editCapa(data) {
            document.getElementById('modalNewCapa').classList.remove('hidden');
            document.getElementById('modalTitle').innerText = 'Edit CAPA: ' + data.code;

            // Set Form Action URL
            let url = "{{ route('capa.update', ':id') }}";
            url = url.replace(':id', data.id);
            document.getElementById('capaForm').action = url;
            document.getElementById('formMethod').value = 'PUT';

            // Fill Data
            document.getElementById('issue_date').value = data.issue_date.split('T')[0];
            document.getElementById('source').value = data.source;
            document.getElementById('problem_description').value = data.problem_description;
            document.getElementById('root_cause').value = data.root_cause;
            document.getElementById('corrective_action').value = data.corrective_action;
            document.getElementById('preventive_action').value = data.preventive_action;
            document.getElementById('pic').value = data.pic;
            document.getElementById('due_date').value = data.due_date ? data.due_date.split('T')[0] : '';
            document.getElementById('status').value = data.status;
        }

        function closeModal() {
            document.getElementById('modalNewCapa').classList.add('hidden');
            // Reset form manual jika perlu
            document.getElementById('capaForm').reset();
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('capaForm').action = "{{ route('capa.store') }}";
            document.getElementById('modalTitle').innerText = 'Form CAPA Baru';
        }
    </script>
@endsection
