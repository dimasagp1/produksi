{{-- MODAL CREATE --}}
@if (in_array(auth()->user()->role, ['admin', 'super_admin', 'spv']))
    <div id="createModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-slate-900/60 dark:bg-slate-900/80 transition-opacity backdrop-blur-sm"
                onclick="window.closeModal('createModal')"></div>
            <div
                class="relative inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-slate-100 dark:border-slate-700">
                <div
                    class="bg-white dark:bg-slate-800 px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white">Buat Batch Baru</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Isi form berikut untuk membuat SPK
                            baru.</p>
                    </div>
                    <button onclick="window.closeModal('createModal')"
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"><i
                            class="fas fa-times text-lg"></i></button>
                </div>
                <div class="px-6 py-6 max-h-[80vh] overflow-y-auto custom-scroll">
                    <form action="{{ route('batches.store') }}" method="POST" id="createForm">@csrf
                        <div class="mb-5"><label
                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Kode Batch
                                (Opsional)</label><input type="text" name="batch_code" class="saas-input"
                                placeholder="Otomatis generate jika kosong">
                            <p class="text-[10px] text-slate-400 dark:text-slate-300 mt-1">Biarkan kosong untuk
                                auto-generate (Format: EKDEV-DDMMYY-XXX)</p>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                            <div><label
                                    class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Produk
                                    <span class="text-rose-500">*</span></label><select name="product_id"
                                    id="create_product_id" class="saas-input select2-modal" required
                                    data-placeholder="Pilih Produk">
                                    <option value=""></option>
                                    @foreach ($products as $p)
                                        <option value="{{ $p->id }}"
                                            data-machines="{{ json_encode($p->machines ?? []) }}">{{ $p->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div><label
                                    class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Warna
                                    <span class="text-rose-500">*</span></label><select name="color_id"
                                    class="saas-input select2-modal" required data-placeholder="Pilih Warna">
                                    <option value=""></option>
                                    @foreach ($colors as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                            <div><label
                                    class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Mesin
                                    <span class="text-rose-500">*</span></label><select name="machine_id"
                                    id="create_machine_id" class="saas-input select2-modal" required
                                    data-placeholder="Pilih Mesin">
                                    <option value=""></option>
                                    {{-- Opsi mesin diisi via JavaScript berdasarkan Produk --}}
                                </select>
                                <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">Jika pilihan mesin tidak
                                    ada, atur pada <a href="{{ route('products.index') }}" target="_blank"
                                        class="text-brand-600 hover:underline">Master Data Produk</a>.</p>
                            </div>
                            <div><label
                                    class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Prioritas
                                    <span class="text-rose-500">*</span></label><select name="priority"
                                    class="saas-input cursor-pointer select2-modal" required
                                    data-placeholder="Pilih Prioritas">
                                    <option value=""></option>
                                    <option value="low">Low (Rendah)</option>
                                    <option value="medium">Medium (Sedang)</option>
                                    <option value="high">High (Tinggi)</option>
                                </select></div>
                        </div>
                        <div class="mb-5"><label
                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Batch Size
                                <span class="text-rose-500">*</span></label>
                            <div class="relative"><input type="text" name="target_quantity"
                                    class="saas-input rupiah-input pr-10" required placeholder="0"><span
                                    class="absolute right-3 top-2.5 text-xs text-slate-400 font-bold bg-slate-50 dark:bg-slate-700 px-2 py-0.5 rounded">Pcs</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-2">
                            <div><label
                                    class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Tanggal
                                    Mulai <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i
                                            class="fas fa-calendar-day text-slate-400"></i></div><input type="text"
                                        name="start_date" class="saas-input with-icon datepicker" required
                                        placeholder="Pilih Tanggal Mulai">
                                </div>
                            </div>
                            <div><label
                                    class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Tanggal
                                    Selesai <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i
                                            class="fas fa-flag-checkered text-slate-400"></i></div><input type="text"
                                        name="deadline_date" class="saas-input with-icon datepicker" required
                                        placeholder="Pilih Tanggal Selesai">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div
                    class="bg-slate-5 dark:bg-slate-700/50 px-6 py-4 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                    <button type="button" onclick="window.closeModal('createModal')"
                        class="px-5 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-xl text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-600 transition-all">Batal</button><button
                        type="submit" form="createForm"
                        class="px-5 py-2.5 bg-brand-600 text-white rounded-xl text-sm font-bold hover:bg-brand-700 shadow-lg shadow-brand-500/30 transition-all">Simpan
                        Batch</button>
                </div>
            </div>
        </div>
    </div>
@endif

{{-- MODAL EDIT --}}
<div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 bg-slate-900/60 dark:bg-slate-900/80 transition-opacity backdrop-blur-sm"
            onclick="window.closeModal('editModal')"></div>
        <div
            class="relative inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-slate-100 dark:border-slate-700">
            <div
                class="bg-white dark:bg-slate-800 px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Edit Batch</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Perbarui informasi batch produksi.</p>
                </div>
                <button onclick="window.closeModal('editModal')"
                    class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"><i
                        class="fas fa-times text-lg"></i></button>
            </div>
            <div class="px-6 py-6 max-h-[80vh] overflow-y-auto custom-scroll">
                <form id="editForm" method="POST">@csrf @method('PUT')
                    <div class="mb-5"><label
                            class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Kode
                            Batch</label><input type="text" name="batch_code" id="edit_batch_code"
                            class="saas-input" placeholder="Kode Batch"></div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                        <div><label
                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Produk
                                <span class="text-rose-500">*</span></label><select name="product_id"
                                id="edit_product_id" class="saas-input select2-modal" required
                                data-placeholder="Pilih Produk">
                                <option value=""></option>
                                @foreach ($products as $p)
                                    <option value="{{ $p->id }}"
                                        data-machines="{{ json_encode($p->machines ?? []) }}">{{ $p->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div><label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Warna
                                <span class="text-rose-500">*</span></label><select name="color_id"
                                id="edit_color_id" class="saas-input select2-modal" required
                                data-placeholder="Pilih Warna">
                                <option value=""></option>
                                @foreach ($colors as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                        <div><label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Mesin
                                <span class="text-rose-500">*</span></label><select name="machine_id"
                                id="edit_machine_id" class="saas-input select2-modal" required
                                data-placeholder="Pilih Mesin">
                                <option value=""></option>
                                {{-- Opsi mesin diisi via JavaScript berdasarkan Produk --}}
                            </select>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">Jika pilihan mesin tidak
                                ada, atur pada <a href="{{ route('products.index') }}" target="_blank"
                                    class="text-brand-600 hover:underline">Master Data Produk</a>.</p>
                        </div>
                        <div><label
                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Prioritas
                                <span class="text-rose-500">*</span></label><select name="priority"
                                id="edit_priority" class="saas-input cursor-pointer select2-modal" required
                                data-placeholder="Pilih Prioritas">
                                <option value=""></option>
                                <option value="low">Low (Rendah)</option>
                                <option value="medium">Medium (Sedang)</option>
                                <option value="high">High (Tinggi)</option>
                            </select></div>
                    </div>
                    <div class="mb-5"><label
                            class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Status
                            Produksi</label><select name="status" id="edit_status"
                            class="saas-input cursor-pointer select2-modal" required
                            data-placeholder="Pilih Status Produksi">
                            <option value=""></option>
                            <option value="planning">Planning</option>
                            <option value="running">Running</option>
                            <option value="hold">Hold</option>
                            <option value="completed">Completed</option>
                            <option value="canceled">Canceled</option>
                        </select></div>
                    <div class="mb-5"><label
                            class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Batch Size
                            <span class="text-rose-500">*</span></label>
                        <div class="relative"><input type="text" name="target_quantity" id="edit_target_quantity"
                                class="saas-input rupiah-input pr-10" required><span
                                class="absolute right-3 top-2.5 text-xs text-slate-400 font-bold bg-slate-50 dark:bg-slate-700 px-2 py-0.5 rounded">Pcs</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-2">
                        <div><label
                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Tanggal
                                Mulai <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i
                                        class="fas fa-calendar-day text-slate-400"></i></div><input type="text"
                                    name="start_date" id="edit_start_date" class="saas-input with-icon datepicker"
                                    required>
                            </div>
                        </div>
                        <div><label
                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Tanggal
                                Selesai <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i
                                        class="fas fa-flag-checkered text-slate-400"></i></div><input type="text"
                                    name="deadline_date" id="edit_deadline_date"
                                    class="saas-input with-icon datepicker" required>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div
                class="bg-slate-5 dark:bg-slate-700/50 px-6 py-4 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                <button type="button" onclick="window.closeModal('editModal')"
                    class="px-5 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-xl text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-600 transition-all">Batal</button><button
                    type="submit" form="editForm"
                    class="px-5 py-2.5 bg-brand-600 text-white rounded-xl text-sm font-bold hover:bg-brand-700 shadow-lg shadow-brand-500/30 transition-all">Update
                    Batch</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL VIEW --}}
<div id="viewModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
    aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 bg-slate-900/60 dark:bg-slate-900/80 transition-opacity backdrop-blur-sm"
            onclick="window.closeModal('viewModal')"></div>
        <div
            class="relative inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-slate-100 dark:border-slate-700">
            <div
                class="bg-white dark:bg-slate-800 px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Detail Batch</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Informasi detail SPK produksi.</p>
                </div>
                <button onclick="window.closeModal('viewModal')"
                    class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"><i
                        class="fas fa-times text-lg"></i></button>
            </div>
            <div class="px-6 py-6 max-h-[80vh] overflow-y-auto custom-scroll">
                <form id="viewForm">
                    <div class="mb-5"><label
                            class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Kode
                            Batch</label><input type="text" id="view_batch_code" class="saas-input" disabled>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                        <div><label
                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Produk</label><select
                                id="view_product_id" class="saas-input select2-modal" disabled>
                                @foreach ($products as $p)
                                    <option value="{{ $p->id }}"
                                        data-machines="{{ json_encode($p->machines ?? []) }}">{{ $p->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div><label
                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Warna</label><select
                                id="view_color_id" class="saas-input select2-modal" disabled>
                                @foreach ($colors as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                        <div><label
                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Mesin</label><select
                                id="view_machine_id" class="saas-input select2-modal" disabled>
                                <option value="">Belum Ditentukan</option>
                                @foreach ($machines as $m)
                                    <option value="{{ $m->id }}">{{ $m->name }}</option>
                                @endforeach
                            </select></div>
                        <div><label
                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Prioritas</label><select
                                id="view_priority" class="saas-input select2-modal" disabled>
                                <option value="low">Low (Rendah)</option>
                                <option value="medium">Medium (Sedang)</option>
                                <option value="high">High (Tinggi)</option>
                            </select></div>
                    </div>
                    <div class="mb-5"><label
                            class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Status</label><input
                            type="text" id="view_status" class="saas-input uppercase font-bold" disabled></div>
                    <div class="mb-5"><label
                            class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Batch
                            Size</label>
                        <div class="relative"><input type="text" id="view_target_quantity"
                                class="saas-input rupiah-input pr-10" disabled><span
                                class="absolute right-3 top-2.5 text-xs text-slate-400 font-bold bg-slate-50 dark:bg-slate-700 px-2 py-0.5 rounded">Pcs</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-2">
                        <div><label
                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Tanggal
                                Mulai</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i
                                        class="fas fa-calendar-day text-slate-400"></i></div><input type="text"
                                    id="view_start_date" class="saas-input with-icon" disabled>
                            </div>
                        </div>
                        <div><label
                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Tanggal
                                Selesai</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i
                                        class="fas fa-flag-checkered text-slate-400"></i></div><input type="text"
                                    id="view_deadline_date" class="saas-input with-icon" disabled>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div
                class="bg-slate-5 dark:bg-slate-700/50 px-6 py-4 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                <button type="button" onclick="window.closeModal('viewModal')"
                    class="px-5 py-2.5 bg-brand-600 text-white rounded-xl text-sm font-bold hover:bg-brand-700 transition-all">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL IMPORT (Tetap) --}}
@if (in_array(auth()->user()->role, ['admin', 'super_admin']))
    <div id="importModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-slate-900/60 dark:bg-slate-900/80 backdrop-blur-sm transition-opacity"
                onclick="window.closeModal('importModal')"></div>
            <div
                class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl max-w-md w-full p-8 border border-slate-100 dark:border-slate-700 transform transition-all scale-100">
                <div class="text-center">
                    <div
                        class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-emerald-50 dark:bg-emerald-900/20 mb-5">
                        <i class="fas fa-file-excel text-emerald-500 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 dark:text-white">Import Batch Excel</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Upload file .xlsx sesuai template untuk
                        import data massal.</p>
                </div>
                <form action="{{ route('batches.import') }}" method="POST" enctype="multipart/form-data"
                    class="mt-8">@csrf
                    <div
                        class="flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 dark:border-slate-600 border-dashed rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 hover:border-brand-400 dark:hover:border-brand-500 transition-all group cursor-pointer relative">
                        <input id="file-upload" name="file" type="file"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" required
                            accept=".xlsx,.xls">
                        <div class="space-y-1 text-center"><i
                                class="fas fa-cloud-arrow-up text-slate-400 text-3xl group-hover:text-brand-500 transition-colors"></i>
                            <div class="text-sm text-slate-600 dark:text-slate-300 font-medium"><span
                                    class="text-brand-600 dark:text-brand-400 group-hover:underline">Klik untuk
                                    upload</span> atau drag file</div>
                            <p class="text-xs text-slate-400">XLSX up to 5MB</p>
                        </div>
                    </div>
                    <div class="mt-4 text-right"><a href="{{ route('batches.template') }}" target="_blank"
                            class="text-xs text-brand-600 dark:text-brand-400 hover:text-brand-700 font-bold flex items-center justify-end gap-1"><i
                                class="fas fa-download"></i> Download Template</a></div>
                    <div class="mt-8 flex gap-3"><button type="button" onclick="window.closeModal('importModal')"
                            class="w-full py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-700 dark:text-slate-200 font-bold hover:bg-slate-50 dark:hover:bg-slate-600 transition-all">Batal</button><button
                            type="submit"
                            class="w-full py-2.5 bg-emerald-600 text-white rounded-xl font-bold hover:bg-emerald-700 shadow-lg shadow-emerald-500/30 transition-all">Import
                            Data</button></div>
                </form>
            </div>
        </div>
    </div>
@endif

{{-- Script Dinamis Dropdown Mesin --}}
<script>
    $(document).ready(function() {
        // Logika untuk menampilkan mesin khusus produk yang dipilih
        $('#create_product_id, #edit_product_id').on('change', function() {
            let isEdit = $(this).attr('id') === 'edit_product_id';
            let machineSelectId = isEdit ? 'edit_machine_id' : 'create_machine_id';
            let $machineSelect = $('#' + machineSelectId);

            // Ambil array mesin dari data-machines opsi produk yang dipilih
            let machines = $(this).find(':selected').data('machines') || [];

            // Simpan value saat ini untuk mencegah reset otomatis jika dipanggil oleh JS Edit Detail
            let currentValue = $machineSelect.val();

            // Bersihkan opsi lama
            $machineSelect.empty().append('<option value=""></option>');

            // Tambahkan opsi baru
            machines.forEach(m => {
                $machineSelect.append(new Option(m.name, m.id));
            });

            // Jika nilai mesin sebelumnya valid/ada di opsi baru, setel kembali
            if (currentValue && $machineSelect.find(`option[value="${currentValue}"]`).length) {
                $machineSelect.val(currentValue).trigger('change.select2');
            } else {
                $machineSelect.val(null).trigger('change.select2');
            }
        });
    });
</script>
