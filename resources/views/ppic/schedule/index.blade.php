@extends('layouts.app')

@section('title', 'Schedule Board (SPK)')

@section('content')
    {{-- 1. Library Dependencies --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="https://unpkg.com/vis-timeline@latest/standalone/umd/vis-timeline-graph2d.min.js">
    </script>
    <link href="https://unpkg.com/vis-timeline@latest/styles/vis-timeline-graph2d.min.css" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- 2. CSS Overrides --}}
    <style>
        .vis-timeline {
            border: none;
            font-family: 'Inter', sans-serif;
            background-color: transparent;
        }

        .vis-time-axis .vis-text {
            color: #64748b;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .vis-time-axis .vis-grid.vis-vertical {
            border-left: 1px dashed #e2e8f0;
        }

        .vis-item {
            border-color: transparent;
            background-color: #fff;
            border-radius: 0.5rem;
            box-shadow: 0 2px 5px -1px rgba(0, 0, 0, 0.1);
            font-size: 0.8rem;
            font-weight: 500;
            cursor: pointer;
        }

        .vis-item.vis-range {
            border-left-width: 4px;
            border-left-style: solid;
        }

        .vis-item .vis-item-content {
            padding: 6px 10px;
        }

        .vis-label {
            color: #1e293b;
            font-weight: 600;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #f1f5f9;
        }

        .vis-panel.vis-left {
            background-color: #f8fafc;
            border-right: 1px solid #e2e8f0;
        }

        .dark .vis-label {
            color: #f1f5f9;
            border-bottom-color: #334155;
        }

        .dark .vis-panel.vis-left {
            background-color: #1e293b;
            border-right-color: #334155;
        }

        .dark .vis-time-axis .vis-text {
            color: #94a3b8;
        }

        .dark .vis-time-axis .vis-grid.vis-vertical {
            border-left-color: #334155;
        }

        .dark .vis-item {
            background-color: #334155;
            color: #fff;
        }

        .saas-input {
            height: 46px;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 0 16px;
            width: 100%;
            background-color: #fff;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .saas-input:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        .dark .saas-input {
            background-color: #0f172a;
            border-color: #334155;
            color: #f1f5f9;
        }

        /* SELECT2 STYLING */
        .select2-container {
            width: 100% !important;
        }

        .select2-container .select2-selection--single {
            height: 46px !important;
            border-radius: 0.75rem !important;
            border: 1px solid #e2e8f0 !important;
            display: flex !important;
            align-items: center !important;
            background-color: #fff !important;
        }

        .dark .select2-container .select2-selection--single {
            background-color: #0f172a !important;
            border-color: #334155 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #1e293b !important;
            padding-left: 16px !important;
            font-size: 0.9rem !important;
            line-height: 1.2 !important;
        }

        .dark .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #f1f5f9 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 44px !important;
            right: 8px !important;
        }

        .select2-dropdown {
            border-radius: 0.75rem !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
            overflow: hidden !important;
            z-index: 9999;
        }

        .dark .select2-dropdown {
            background-color: #1e293b !important;
            border-color: #334155 !important;
        }

        .select2-search__field {
            border-radius: 0.5rem !important;
            padding: 6px 12px !important;
        }

        .dark .select2-search__field {
            background-color: #0f172a !important;
            border-color: #334155 !important;
            color: white !important;
        }

        .dark .select2-results__option {
            color: #f1f5f9 !important;
        }

        .dark .select2-results__option--highlighted {
            background-color: #6366f1 !important;
            color: white !important;
        }

        .color-radio:checked+label {
            ring-width: 2px;
            ring-offset-width: 2px;
            transform: scale(1.1);
        }

        .dark .color-radio:checked+label {
            ring-offset-color: #1e293b;
        }
    </style>

    <div class="flex flex-col gap-6 fade-in-up">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight">Jadwal Produksi</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Gantt Chart visualisasi batch produksi pada
                    setiap Work Center.</p>
            </div>

            <button onclick="openCreateModal()"
                class="group inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-lg shadow-indigo-500/30 transition-all active:scale-95">
                <i class="fa-solid fa-plus transition-transform group-hover:rotate-90"></i>
                <span>Plotting Batch Baru</span>
            </button>
        </div>

        {{-- TIMELINE --}}
        <div
            class="bg-white dark:bg-slate-800 rounded-[1.5rem] border border-slate-200 dark:border-slate-700 shadow-xl shadow-slate-200/40 dark:shadow-slate-900/40 overflow-hidden">
            <div
                class="p-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Live Timeline</span>
                </div>
                <div class="text-xs text-slate-400">Klik item untuk Edit / Hapus</div>
            </div>
            <div class="p-2">
                <div id="visualization" class="w-full min-h-[400px]"></div>
            </div>
        </div>
    </div>

    {{-- MODAL --}}
    <div id="modalSchedule" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>

        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-visible rounded-2xl bg-white dark:bg-slate-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100 dark:border-slate-700">

                    {{-- Header --}}
                    <div
                        class="bg-white dark:bg-slate-800 px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center rounded-t-2xl">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white leading-6" id="modal-title">Plotting
                                Jadwal</h3>
                            <p class="text-xs text-slate-500 mt-1" id="modal-subtitle">Tentukan waktu dan mesin untuk batch
                                produksi.</p>
                        </div>
                        <button type="button" onclick="closeModal()"
                            class="text-slate-400 hover:text-slate-500 dark:hover:text-slate-300 transition-colors">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>

                    {{-- Form --}}
                    <form id="scheduleForm" method="POST">
                        @csrf
                        <div id="method-field"></div>

                        <div class="px-6 py-6 space-y-5">

                            {{-- 1. Batch Selection (Fix HTML Render) --}}
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                    Pilih Batch <span class="text-rose-500">*</span>
                                </label>
                                <select name="batch_id" id="input_batch_id" class="select2-html" required
                                    data-placeholder="-- Cari Kode Batch --">
                                    <option value=""></option>
                                    @foreach ($unscheduledBatches as $b)
                                        <option value="{{ $b->id }}"
                                            data-html="<b>{{ $b->batch_code }}</b><br><span class='text-xs text-gray-500'>{{ $b->product->name }} (Qty: {{ $b->quantity }})</span>">
                                            {{ $b->batch_code }} - {{ $b->product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- 2. Machine Selection --}}
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                    Mesin (Work Center) <span class="text-rose-500">*</span>
                                </label>
                                <select name="work_center_id" id="input_work_center_id" class="select2-init" required
                                    data-placeholder="-- Cari Mesin --">
                                    <option value=""></option>
                                    @foreach ($workCenters as $wc)
                                        <option value="{{ $wc->id }}">{{ $wc->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Time & Duration --}}
                            <div class="grid grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                        Waktu Mulai <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="text" name="start_time" id="input_start_time"
                                            class="saas-input date-time-picker !pl-12" placeholder="Pilih waktu..."
                                            required>
                                        <div
                                            class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
                                            <i class="fa-regular fa-clock"></i>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                        Durasi (Jam) <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="number" step="0.5" name="duration_hours" id="input_duration_hours"
                                            class="saas-input !pl-12" placeholder="4.0" required>
                                        <div
                                            class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
                                            <i class="fa-solid fa-hourglass-half"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Visual Color --}}
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">
                                    Warna Visual <span class="text-rose-500">*</span>
                                </label>
                                <div class="flex items-center gap-4">
                                    @foreach ([
            '#3b82f6' => 'blue',
            '#10b981' => 'emerald',
            '#f59e0b' => 'amber',
            '#ef4444' => 'rose',
            '#8b5cf6' => 'violet',
        ] as $hex => $name)
                                        <div class="relative">
                                            <input type="radio" id="color-{{ $name }}" name="visual_color"
                                                value="{{ $hex }}" class="peer sr-only color-radio"
                                                {{ $name == 'blue' ? 'checked' : '' }}>
                                            <label for="color-{{ $name }}"
                                                class="block w-8 h-8 rounded-full bg-{{ $name }}-500 cursor-pointer ring-offset-2 ring-{{ $name }}-500 transition-all hover:scale-110 peer-checked:ring-2"></label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div
                            class="bg-slate-50 dark:bg-slate-700/30 px-6 py-4 flex justify-between items-center rounded-b-2xl">
                            <div>
                                <button type="button" id="btnDelete" onclick="deleteSchedule()"
                                    class="hidden text-rose-600 hover:text-rose-700 font-bold text-sm flex items-center gap-1 transition-colors">
                                    <i class="fa-solid fa-trash-can"></i> Hapus
                                </button>
                            </div>
                            <div class="flex gap-3">
                                <button type="button" onclick="closeModal()"
                                    class="px-5 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 transition-all">Batal</button>
                                <button type="submit" id="btnSave"
                                    class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold shadow-lg shadow-indigo-500/30 transition-all">Simpan
                                    Jadwal</button>
                            </div>
                        </div>
                    </form>

                    <form id="deleteForm" method="POST" class="hidden">
                        @csrf @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script>
        // 1. VIS JS INIT
        var groups = new vis.DataSet(@json($groups));
        var items = new vis.DataSet(@json($items));
        var container = document.getElementById('visualization');
        var options = {
            groupOrder: 'content',
            editable: false,
            stack: true,
            zoomMin: 1000 * 60 * 60 * 2,
            orientation: 'top',
            start: '{{ now()->subDays(1)->format('Y-m-d') }}',
            end: '{{ now()->addDays(5)->format('Y-m-d') }}',
            margin: {
                item: 10,
                axis: 5
            },
            selectable: true,
            multiselect: false,
        };
        var timeline = new vis.Timeline(container, items, groups, options);

        timeline.on('select', function(properties) {
            if (properties.items.length > 0) openEditModal(properties.items[0]);
        });

        // 2. MODAL & FORM
        function openCreateModal() {
            resetForm();
            $('#modalSchedule').removeClass('hidden');
            $('#modal-title').text('Plotting Jadwal Baru');
            $('#btnSave').text('Simpan Jadwal');
            $('#btnDelete').addClass('hidden');
            $('#scheduleForm').attr('action', "{{ route('schedule.store') }}");
            $('#method-field').html('');
            $('#input_batch_id').prop('disabled', false);
            initSelect2();
        }

        function openEditModal(id) {
            var item = items.get(id);
            if (!item) return;
            $('#modalSchedule').removeClass('hidden');
            $('#modal-title').text('Edit Jadwal');
            $('#btnSave').text('Update Jadwal');
            $('#btnDelete').removeClass('hidden');

            var updateUrl = "{{ route('schedule.update', ':id') }}".replace(':id', id);
            $('#scheduleForm').attr('action', updateUrl);
            $('#method-field').html('<input type="hidden" name="_method" value="PUT">');

            var deleteUrl = "{{ route('schedule.destroy', ':id') }}".replace(':id', id);
            $('#deleteForm').attr('action', deleteUrl);

            // Populate Batch
            if ($('#input_batch_id').find("option[value='" + item.batch_id + "']").length) {
                $('#input_batch_id').val(item.batch_id).trigger('change');
            } else {
                // Perbaiki format HTML saat inject option baru untuk edit
                var plainText = item.content.replace(/<[^>]*>?/gm, ' ');
                var newOption = new Option(plainText, item.batch_id, true, true);
                $(newOption).attr('data-html', item.content);
                $('#input_batch_id').append(newOption).trigger('change');
            }
            $('#input_batch_id').prop('disabled', true);

            $('#input_work_center_id').val(item.group).trigger('change');

            let startDate = new Date(item.start);
            let formattedDate = startDate.getFullYear() + "-" + ("0" + (startDate.getMonth() + 1)).slice(-2) + "-" + ("0" +
                startDate.getDate()).slice(-2) + " " + ("0" + startDate.getHours()).slice(-2) + ":" + ("0" + startDate
                .getMinutes()).slice(-2);
            document.querySelector(".date-time-picker")._flatpickr.setDate(formattedDate);

            let endDate = new Date(item.end);
            let diffHrs = (endDate - startDate) / (1000 * 60 * 60);
            $('#input_duration_hours').val(diffHrs);

            let color = item.style.match(/background-color:\s*(#[0-9a-fA-F]+)/);
            if (color && color[1]) $('input[name="visual_color"][value="' + color[1] + '"]').prop('checked', true);

            initSelect2();
        }

        function closeModal() {
            $('#modalSchedule').addClass('hidden');
            timeline.setSelection([]);
        }

        function resetForm() {
            $('#scheduleForm')[0].reset();
            $('.select2-init, .select2-html').val(null).trigger('change');
            document.querySelector(".date-time-picker")._flatpickr.clear();
        }

        function deleteSchedule() {
            Swal.fire({
                title: 'Hapus Jadwal?',
                text: "Batch akan kembali ke status Pending.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) $('#deleteForm').submit();
            });
        }

        // 3. SELECT2 CUSTOM RENDER
        function formatState(state) {
            if (!state.id) return state.text;
            var htmlContent = $(state.element).data('html');
            if (htmlContent) {
                return $(`<span>${htmlContent}</span>`);
            }
            return state.text;
        }

        function initSelect2() {
            $('.select2-init').select2({
                dropdownParent: $('#modalSchedule'),
                width: '100%',
                allowClear: true
            });

            $('.select2-html').select2({
                dropdownParent: $('#modalSchedule'),
                width: '100%',
                allowClear: true,
                templateResult: formatState,
                templateSelection: formatState,
                escapeMarkup: function(m) {
                    return m;
                }
            });
        }

        window.addEventListener('load', function() {
            flatpickr(".date-time-picker", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                altInput: true,
                altFormat: "d F Y, H:i",
                time_24hr: true,
                minDate: "today",
                defaultHour: 8
            });
        });

        // 4. SWEETALERT NOTIFICATION (Fixed: Added closing brace)
        document.addEventListener("DOMContentLoaded", function() {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                    background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#fff',
                    color: document.documentElement.classList.contains('dark') ? '#f1f5f9' : '#1e293b',
                    customClass: {
                        popup: 'rounded-2xl'
                    }
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: "{{ session('error') }}",
                    background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#fff',
                    color: document.documentElement.classList.contains('dark') ? '#f1f5f9' : '#1e293b',
                    customClass: {
                        popup: 'rounded-2xl'
                    }
                });
            @endif

            @if ($errors->any())
                let errorHtml = '<div class="text-left text-sm space-y-1">';
                @foreach ($errors->all() as $error)
                    errorHtml += '<div>• {{ $error }}</div>';
                @endforeach
                errorHtml += '</div>';

                Swal.fire({
                    icon: 'warning',
                    title: 'Periksa Input',
                    html: errorHtml,
                    confirmButtonText: 'Tutup',
                    confirmButtonColor: '#4f46e5',
                    background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#fff',
                    color: document.documentElement.classList.contains('dark') ? '#f1f5f9' : '#1e293b',
                    customClass: {
                        popup: 'rounded-2xl',
                        confirmButton: 'rounded-xl px-6 py-2'
                    }
                });
            @endif
        }); // <-- Fixed: Closing brace added here
    </script>
@endsection
