{{-- LIBRARIES --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://npmcdn.com/flatpickr/dist/themes/dark.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

{{-- CUSTOM CSS --}}
<style>
    .saas-input {
        height: 46px;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        padding: 0 16px;
        font-size: 0.875rem;
        width: 100%;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        background-color: #f8fafc;
        color: #1e293b;
    }

    .dark .saas-input {
        background-color: #1e293b;
        border-color: #334155;
        color: #f1f5f9;
    }

    .saas-input:focus {
        border-color: #3b82f6;
        outline: none;
        background-color: #ffffff;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    .dark .saas-input:focus {
        background-color: #0f172a;
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
    }

    .saas-input.with-icon {
        padding-left: 2.75rem !important;
    }

    .saas-input:disabled,
    .saas-input[readonly] {
        background-color: #f1f5f9;
        color: #64748b;
        border-color: #cbd5e1;
        cursor: not-allowed;
    }

    .dark .saas-input:disabled,
    .dark .saas-input[readonly] {
        background-color: #334155;
        color: #94a3b8;
        border-color: #475569;
    }

    .saas-input.datepicker[readonly] {
        cursor: pointer;
    }

    .select2-container {
        width: 100% !important;
    }

    .select2-container .select2-selection--single {
        height: 46px !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.75rem !important;
        display: flex !important;
        align-items: center !important;
        background-color: #f8fafc !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 44px !important;
        right: 10px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #475569 !important;
        font-size: 0.875rem;
        padding-left: 16px !important;
        font-weight: 500;
    }

    .select2-dropdown {
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.75rem !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        z-index: 9999 !important;
        overflow: hidden;
    }

    .dark .select2-container .select2-selection--single {
        background-color: #1e293b !important;
        border-color: #334155 !important;
    }

    .dark .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #f1f5f9 !important;
    }

    .dark .select2-dropdown {
        background-color: #1e293b !important;
        border-color: #334155 !important;
    }

    .dark .select2-search__field {
        background-color: #0f172a !important;
        color: #f1f5f9 !important;
        border-color: #334155 !important;
    }

    .dark .select2-results__option {
        color: #f1f5f9 !important;
    }

    .dark .select2-results__option--highlighted[aria-selected] {
        background-color: #3b82f6 !important;
        color: #fff !important;
    }

    .toggle-checkbox:checked {
        right: 0;
        border-color: #3b82f6;
    }

    .toggle-checkbox:checked+.toggle-label {
        background-color: #3b82f6;
    }

    .toggle-checkbox:checked+.toggle-label:before {
        transform: translateX(100%);
    }

    .toggle-label {
        width: 44px;
        height: 24px;
        position: relative;
        display: block;
        background: #cbd5e1;
        border-radius: 9999px;
        cursor: pointer;
        transition: 0.3s;
    }

    .dark .toggle-label {
        background: #475569;
    }

    .toggle-label:before {
        content: '';
        position: absolute;
        top: 2px;
        left: 2px;
        width: 20px;
        height: 20px;
        background: #fff;
        border-radius: 9999px;
        transition: 0.3s;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
    }

    .batch-card {
        transition: all 0.3s ease;
    }

    .batch-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        border-color: #3b82f6;
    }

    .summary-card {
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        position: relative;
        overflow: hidden;
    }

    .summary-card:hover {
        transform: translateY(-2px);
    }

    .summary-card.active {
        border-color: #3b82f6;
        background-color: #eff6ff;
    }

    .dark .summary-card.active {
        border-color: #3b82f6;
        background-color: rgba(59, 130, 246, 0.1);
    }

    .summary-card.active .icon-box {
        background-color: #3b82f6;
        color: white;
    }
</style>
