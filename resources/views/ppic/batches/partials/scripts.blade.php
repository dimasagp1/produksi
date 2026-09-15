<script>
    // --- MODAL HELPERS ---
    window.closeModal = function(id) {
        document.getElementById(id).classList.add('hidden');
    }
    window.openModal = function(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    // --- EXPORT MENU ---
    window.toggleExportMenu = function(e) {
        e.stopPropagation();
        const menu = document.getElementById('exportMenu');
        const arrow = document.getElementById('exportArrow');
        if (menu.classList.contains('hidden')) {
            menu.classList.remove('hidden');
            menu.classList.add('opacity-100', 'translate-y-0');
            menu.classList.remove('opacity-0', '-translate-y-2');
            arrow.classList.add('rotate-180');
        } else {
            closeExportMenu();
        }
    }

    function closeExportMenu() {
        const menu = document.getElementById('exportMenu');
        const arrow = document.getElementById('exportArrow');
        if (!menu.classList.contains('hidden')) {
            menu.classList.add('hidden');
            arrow.classList.remove('rotate-180');
        }
    }
    document.addEventListener('click', function(e) {
        const menu = document.getElementById('exportMenu');
        if (menu && !menu.contains(e.target) && !e.target.closest(
                'button[onclick="toggleExportMenu(event)"]')) {
            closeExportMenu();
        }
    });

    // --- TOGGLE ACTIVE (AJAX) ---
    window.updateActiveStatus = function(batchId) {
        const checkbox = document.getElementById('toggle_' + batchId);
        const isActive = checkbox.checked;

        $.ajax({
            // [PERBAIKAN 1]: Hapus /ppic
            url: '/batches/' + batchId + '/toggle-active',
            type: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: isActive ? 'Batch Diaktifkan' : 'Batch Dinonaktifkan',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false,
                    background: document.documentElement.classList.contains('dark') ?
                        '#1e293b' : '#fff',
                    color: document.documentElement.classList.contains('dark') ? '#e2e8f0' :
                        '#334155',
                    customClass: {
                        popup: 'rounded-2xl'
                    }
                });
            },
            error: function(xhr) {
                checkbox.checked = !isActive; // Kembalikan posisi toggle
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Terjadi kesalahan saat mengubah status.'
                });
            }
        });
    }

    // --- TOGGLE LOCK (AJAX & SWAL FIX) ---
    window.toggleLock = function(batchId, btnElement) {
        Swal.fire({
            title: 'Konfirmasi Status',
            text: "Apakah Anda yakin ingin mengubah status lock batch ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#4b5563',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Ya, Ubah!',
            cancelButtonText: 'Batal',
            background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#fff',
            color: document.documentElement.classList.contains('dark') ? '#e2e8f0' : '#334155',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-xl',
                cancelButton: 'rounded-xl'
            }
        }).then((result) => {
            if (result && result.isConfirmed) {
                $.ajax({
                    // [PERBAIKAN 2]: Hapus /ppic
                    url: '/batches/' + batchId + '/toggle-lock',
                    type: 'PATCH',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: response.message,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false,
                                background: document.documentElement.classList.contains(
                                    'dark') ? '#1e293b' : '#fff',
                                color: document.documentElement.classList.contains(
                                    'dark') ? '#e2e8f0' : '#334155',
                                customClass: {
                                    popup: 'rounded-2xl'
                                }
                            });

                            // Update UI Tanpa Reload
                            let btn = $(btnElement);
                            let icon = btn.find('i');
                            let row = btn.closest('.batch-card');
                            let actionsContainer = row.find('.group-actions-' + batchId);
                            let statusBadge = $('#status-badge-' + batchId);

                            statusBadge.text(response.new_status);

                            if (response.is_locked) {
                                icon.removeClass('fa-lock-open').addClass('fa-lock');
                                btn.attr('title', 'Unlock (Ubah Status ke Running)');
                                actionsContainer.find('.btn-edit, .delete-form').hide();
                                statusBadge.removeClass(
                                        'bg-emerald-100 text-emerald-700 bg-blue-100 text-blue-700'
                                    )
                                    .addClass('bg-indigo-100 text-indigo-700');
                            } else {
                                icon.removeClass('fa-lock').addClass('fa-lock-open');
                                btn.attr('title', 'Lock (Ubah Status ke Completed)');
                                actionsContainer.find('.btn-edit, .delete-form').show();
                                statusBadge.removeClass('bg-indigo-100 text-indigo-700')
                                    .addClass('bg-emerald-100 text-emerald-700');
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Gagal mengubah status lock.'
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan sistem.'
                        });
                    }
                });
            }
        });
    }

    // --- MODAL OPENERS ---
    window.openViewModal = function(batch) {
        document.getElementById('view_batch_code').value = batch.batch_code || '-';
        $('#view_product_id').val(batch.product_id).trigger('change');
        $('#view_color_id').val(batch.color_id).trigger('change');
        $('#view_machine_id').val(batch.machine_id).trigger('change');
        $('#view_priority').val(batch.priority).trigger('change');
        document.getElementById('view_status').value = batch.status;
        document.getElementById('view_target_quantity').value = new Intl.NumberFormat('id-ID').format(batch
            .target_quantity);
        document.getElementById('view_start_date').value = batch.start_date ? batch.start_date : '-';
        document.getElementById('view_deadline_date').value = batch.deadline_date ? batch.deadline_date : '-';
        window.openModal('viewModal');
    }

    window.openEditModal = function(batch) {
        document.getElementById('edit_batch_code').value = batch.batch_code;
        document.getElementById('edit_target_quantity').value = new Intl.NumberFormat('id-ID').format(batch
            .target_quantity);

        $('#edit_product_id').val(batch.product_id).trigger('change');
        $('#edit_color_id').val(batch.color_id).trigger('change');
        $('#edit_machine_id').val(batch.machine_id).trigger('change');
        $('#edit_priority').val(batch.priority).trigger('change');
        $('#edit_status').val(batch.status).trigger('change');

        const startDateInput = document.querySelector("#edit_start_date");
        if (startDateInput && startDateInput._flatpickr) {
            // 1. Hapus batasan minDate agar tanggal lama bisa masuk
            startDateInput._flatpickr.set('minDate', null);
            // 2. Set tanggalnya
            startDateInput._flatpickr.setDate(batch.start_date);
        }

        const deadlineInput = document.querySelector("#edit_deadline_date");
        if (deadlineInput && deadlineInput._flatpickr) {
            // 1. Hapus batasan minDate agar tanggal lama bisa masuk
            deadlineInput._flatpickr.set('minDate', null);

            if (batch.deadline_date) {
                deadlineInput._flatpickr.setDate(batch.deadline_date);
            } else {
                deadlineInput._flatpickr.clear();
            }
        }

        document.getElementById('editForm').action = '/batches/' + batch.id;
        window.openModal('editModal');
    }

    // --- CONFIRM DELETE ---
    window.confirmDelete = function(button) {
        Swal.fire({
            title: 'Hapus Batch?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#fff',
            color: document.documentElement.classList.contains('dark') ? '#e2e8f0' : '#334155',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-xl',
                cancelButton: 'rounded-xl'
            }
        }).then((result) => {
            if (result && result.isConfirmed) {
                button.closest('form').submit();
            }
        });
    }

    // --- DOCUMENT READY & INITIALIZATION ---
    $(document).ready(function() {
        // Init Select2
        $('.select2-modal').each(function() {
            $(this).select2({
                width: '100%',
                dropdownParent: $(this).closest('.fixed'),
                placeholder: $(this).data('placeholder'),
                allowClear: true
            });
        });

        $('#view_product_id, #view_color_id, #view_machine_id, #view_priority').select2({
            width: '100%',
            dropdownParent: $('#viewModal'),
            placeholder: "Pilih...",
            disabled: true
        });

        // Init Flatpickr
        const startDateInput = $("#filter_start_date").flatpickr({
            dateFormat: "Y-m-d",
            // maxDate: "today", // Opsional, boleh dihapus jika ingin cari tanggal masa depan
            allowInput: true, // Izinkan ketik manual/hapus
            onChange: function(selectedDates, dateStr, instance) {
                endDateInput.set('minDate', dateStr);
            }
        });

        const endDateInput = $("#filter_end_date").flatpickr({
            dateFormat: "Y-m-d",
            // maxDate: "today", // Opsional
            allowInput: true,
            minDate: "{{ request('filter_start_date') }}"
        });

        // Init Flatpickr untuk FORM MODAL (Create/Edit)
        // Disini BARU boleh pakai defaultDate: "today" karena untuk input data baru
        flatpickr(".datepicker:not(#filter_start_date):not(#filter_end_date)", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d/m/Y",

            locale: {
                firstDayOfWeek: 1
            },
            allowInput: true,
            onReady: function(selectedDates, dateStr, instance) {
                if (instance.element.hasAttribute('required')) {
                    instance.altInput.setAttribute('required', 'required');
                }
            }
        });

        // Input Rupiah (Numeric)
        $(document).on('keyup', '.rupiah-input', function() {
            let val = $(this).val().replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            $(this).val(val);
        });
    });

    // --- FLASH MESSAGES ---
    @if ($errors->any())
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian!',
            html: '<ul class="text-left text-sm">@foreach ($errors->all() as $error)<li>• {{ $error }}</li>@endforeach</ul>',
            background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#fff',
            color: document.documentElement.classList.contains('dark') ? '#e2e8f0' : '#334155',
            customClass: {
                popup: 'rounded-2xl'
            }
        });
    @endif

    @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: "{{ session('success') }}",
            timer: 2000,
            showConfirmButton: false,
            background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#fff',
            color: document.documentElement.classList.contains('dark') ? '#e2e8f0' : '#334155',
            customClass: {
                popup: 'rounded-2xl'
            }
        });
    @endif

    @if (session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: "{{ session('error') }}",
            background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#fff',
            color: document.documentElement.classList.contains('dark') ? '#e2e8f0' : '#334155'
        });
    @endif
</script>
