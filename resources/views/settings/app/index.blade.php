@extends('layouts.app')

@section('title', 'Identitas Website')

@section('content')
    <div class="flex flex-col gap-6" x-data="settingApp()">

        {{-- PAGE HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white tracking-tight">Pengaturan Identitas Website</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Kelola nama website, logo, favicon, dan identitas visual aplikasi untuk seluruh sistem.
                </p>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-xs font-semibold border border-indigo-100 dark:border-indigo-800">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>Khusus Super Admin</span>
                </span>
            </div>
        </div>

        {{-- ALERT MESSAGES --}}
        @if (session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-sm flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        @if (isset($errors) && $errors->any())
            <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-900/30 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-sm shadow-sm">
                <div class="flex items-center gap-2 font-bold mb-2">
                    <i class="fa-solid fa-triangle-exclamation text-rose-500"></i>
                    <span>Terjadi kesalahan pada input:</span>
                </div>
                <ul class="list-disc list-inside space-y-1 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- MAIN FORM & PREVIEW GRID --}}
        <form action="{{ route('settings.app.update') }}" method="POST" enctype="multipart/form-data" id="settingForm" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            @csrf

            {{-- LEFT COLUMN: FORM CONTROLS (7 COLS) --}}
            <div class="lg:col-span-7 flex flex-col gap-6">

                {{-- CARD 1: INFORMASI UMUM & NAMA WEBSITE --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-brand-500/10 text-brand-600 dark:text-brand-400 flex items-center justify-center font-bold">
                            <i class="fa-solid fa-globe"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-slate-800 dark:text-white">Identitas & Informasi Aplikasi</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Nama sistem, instansi, dan teks deskripsi</p>
                        </div>
                    </div>

                    <div class="p-6 space-y-5">
                        {{-- Nama Website --}}
                        <div>
                            <label for="app_name" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                Nama Website / Aplikasi <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-heading"></i>
                                </span>
                                <input type="text" name="app_name" id="app_name" required
                                    x-model="appName"
                                    value="{{ old('app_name', $settings['app_name'] ?? 'HBT Produksi') }}"
                                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 text-sm font-medium focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all">
                            </div>
                            <p class="mt-1 text-[11px] text-slate-400">
                                Ditampilkan di tab browser, header sidebar, login, dan <strong>nama aplikasi saat di-install (PWA)</strong>.
                            </p>
                        </div>

                        {{-- Grid 2 Kolom: Singkatan & Nama Perusahaan --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="app_short_name" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                    Singkatan / Short Name
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                        <i class="fa-solid fa-tag"></i>
                                    </span>
                                    <input type="text" name="app_short_name" id="app_short_name"
                                        x-model="appShortName"
                                        value="{{ old('app_short_name', $settings['app_short_name'] ?? 'AEJ App') }}"
                                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 text-sm font-medium focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all">
                                </div>
                            </div>

                            <div>
                                <label for="company_name" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                    Nama Perusahaan / Organisasi
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                        <i class="fa-solid fa-building"></i>
                                    </span>
                                    <input type="text" name="company_name" id="company_name"
                                        x-model="companyName"
                                        @input="onCompanyChange()"
                                        value="{{ old('company_name', $settings['company_name'] ?? 'PT Abhimata Emas Juara') }}"
                                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 text-sm font-medium focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all">
                                </div>
                            </div>
                        </div>

                        {{-- Grid 2 Kolom: Tagline & Sub-Tagline --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="app_tagline" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                    Tagline Sidebar
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                        <i class="fa-solid fa-quote-left"></i>
                                    </span>
                                    <input type="text" name="app_tagline" id="app_tagline"
                                        x-model="appTagline"
                                        value="{{ old('app_tagline', $settings['app_tagline'] ?? 'Unified System') }}"
                                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 text-sm font-medium focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all">
                                </div>
                            </div>

                            <div>
                                <label for="app_sub_tagline" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                    Sub-Tagline Modal
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                        <i class="fa-solid fa-award"></i>
                                    </span>
                                    <input type="text" name="app_sub_tagline" id="app_sub_tagline"
                                        x-model="appSubTagline"
                                        value="{{ old('app_sub_tagline', $settings['app_sub_tagline'] ?? 'Production System') }}"
                                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 text-sm font-medium focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all">
                                </div>
                            </div>
                        </div>

                        {{-- Deskripsi Sistem --}}
                        <div>
                            <label for="app_description" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                Deskripsi Aplikasi / Sistem
                            </label>
                            <textarea name="app_description" id="app_description" rows="2"
                                x-model="appDesc"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 text-sm font-medium focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all">{{ old('app_description', $settings['app_description'] ?? 'Sistem ERP manufaktur terintegrasi untuk efisiensi produksi dan pemantauan real-time.') }}</textarea>
                        </div>

                        {{-- Footer Text --}}
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label for="footer_text" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                    Teks Copyright Footer
                                </label>
                                <button type="button" @click="footerText = (companyName || appName) + '. All rights reserved.'"
                                    class="text-[11px] text-brand-600 dark:text-brand-400 hover:underline font-semibold flex items-center gap-1">
                                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                                    <span>Sesuaikan dengan Nama Perusahaan</span>
                                </button>
                            </div>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fa-regular fa-copyright"></i>
                                </span>
                                <input type="text" name="footer_text" id="footer_text"
                                    x-model="footerText"
                                    @input="footerManuallyChanged = true"
                                    value="{{ old('footer_text', app_footer_text()) }}"
                                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 text-sm font-medium focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all">
                            </div>
                            <p class="mt-1 text-[11px] text-slate-400">Teks hak cipta yang muncul di bagian paling bawah website dan halaman login.</p>
                        </div>
                    </div>
                </div>

                {{-- CARD 2: PENGATURAN LOGO & FAVICON --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
                                <i class="fa-solid fa-image"></i>
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-slate-800 dark:text-white">Aset Visual (Logo & Favicon)</h2>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Upload dan kelola gambar branding sistem</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">

                        {{-- 1. PENGATURAN LOGO --}}
                        <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/40">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h3 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                                        <span>Logo Utama Website</span>
                                        @if (!empty($settings['app_logo']))
                                            <span class="px-2 py-0.5 text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 rounded-full">Kustom</span>
                                        @else
                                            <span class="px-2 py-0.5 text-[10px] font-bold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 rounded-full">Default</span>
                                        @endif
                                    </h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                        Ditampilkan pada sidebar, halaman login, modal sistem, dan <strong>ikon aplikasi saat di-install (PWA)</strong>.
                                    </p>
                                </div>

                                @if (!empty($settings['app_logo']))
                                    <button type="button" onclick="confirmResetLogo()"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold text-rose-600 hover:text-rose-700 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition-colors border border-rose-200 dark:border-rose-900">
                                        <i class="fa-solid fa-rotate-left"></i>
                                        <span>Reset Default</span>
                                    </button>
                                @endif
                            </div>

                            <div class="flex flex-col sm:flex-row items-center gap-4">
                                {{-- Logo Preview Box --}}
                                <div class="w-24 h-24 rounded-xl border-2 border-dashed border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 p-2 flex items-center justify-center shrink-0 shadow-inner">
                                    <img :src="logoPreviewUrl" alt="Logo Preview" class="max-h-full max-w-full object-contain">
                                </div>

                                {{-- File Input --}}
                                <div class="flex-1 w-full">
                                    <label class="block">
                                        <input type="file" name="app_logo" id="app_logo" accept="image/png,image/jpeg,image/jpg,image/svg+xml,image/webp"
                                            @change="handleLogoChange($event)"
                                            class="block w-full text-xs text-slate-500 dark:text-slate-400
                                                file:mr-4 file:py-2 file:px-4
                                                file:rounded-xl file:border-0
                                                file:text-xs file:font-semibold
                                                file:bg-brand-50 file:text-brand-700
                                                hover:file:bg-brand-100
                                                dark:file:bg-slate-700 dark:file:text-slate-200
                                                cursor-pointer">
                                    </label>
                                    <div class="mt-2 text-[11px] text-slate-400 flex items-center gap-2">
                                        <i class="fa-solid fa-circle-info"></i>
                                        <span>Rekomendasi: Format PNG (Transparan), Max 2MB, Rasio proporsional.</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 2. PENGATURAN FAVICON --}}
                        <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/40">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h3 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                                        <span>Favicon Browser</span>
                                        @if (!empty($settings['app_favicon']))
                                            <span class="px-2 py-0.5 text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 rounded-full">Kustom</span>
                                        @else
                                            <span class="px-2 py-0.5 text-[10px] font-bold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 rounded-full">Default</span>
                                        @endif
                                    </h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                        Icon kecil yang muncul pada tab browser dan bookmark halaman.
                                    </p>
                                </div>

                                @if (!empty($settings['app_favicon']))
                                    <button type="button" onclick="confirmResetFavicon()"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold text-rose-600 hover:text-rose-700 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition-colors border border-rose-200 dark:border-rose-900">
                                        <i class="fa-solid fa-rotate-left"></i>
                                        <span>Reset Default</span>
                                    </button>
                                @endif
                            </div>

                            <div class="flex flex-col sm:flex-row items-center gap-4">
                                {{-- Favicon Preview Box --}}
                                <div class="w-16 h-16 rounded-xl border-2 border-dashed border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 p-2 flex items-center justify-center shrink-0 shadow-inner">
                                    <img :src="faviconPreviewUrl" alt="Favicon Preview" class="w-8 h-8 object-contain">
                                </div>

                                {{-- File Input --}}
                                <div class="flex-1 w-full">
                                    <label class="block">
                                        <input type="file" name="app_favicon" id="app_favicon" accept="image/x-icon,image/png,image/jpeg,image/jpg,image/svg+xml,image/webp"
                                            @change="handleFaviconChange($event)"
                                            class="block w-full text-xs text-slate-500 dark:text-slate-400
                                                file:mr-4 file:py-2 file:px-4
                                                file:rounded-xl file:border-0
                                                file:text-xs file:font-semibold
                                                file:bg-brand-50 file:text-brand-700
                                                hover:file:bg-brand-100
                                                dark:file:bg-slate-700 dark:file:text-slate-200
                                                cursor-pointer">
                                    </label>
                                    <div class="mt-2 text-[11px] text-slate-400 flex items-center gap-2">
                                        <i class="fa-solid fa-circle-info"></i>
                                        <span>Rekomendasi: Ukuran 32x32 atau 64x64 px (1:1), format ICO/PNG, Max 1MB.</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- SUBMIT BUTTON BAR --}}
                    <div class="px-6 py-4 bg-slate-50/70 dark:bg-slate-800/80 border-t border-slate-100 dark:border-slate-700 flex justify-end items-center gap-3">
                        <button type="submit" id="saveBtn"
                            class="inline-flex items-center px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-brand-500/30 transition-all transform hover:-translate-y-0.5">
                            <i class="fa-solid fa-floppy-disk mr-2"></i>
                            <span>Simpan Perubahan Identitas</span>
                        </button>
                    </div>
                </div>

            </div>

            {{-- RIGHT COLUMN: REAL-TIME LIVE PREVIEW (5 COLS) --}}
            <div class="lg:col-span-5 flex flex-col gap-6">

                <div class="sticky top-20 flex flex-col gap-6">

                    {{-- PREVIEW CARD --}}
                    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 flex items-center justify-between">
                            <div class="flex items-center gap-2 font-bold text-sm text-slate-800 dark:text-white">
                                <i class="fa-solid fa-eye text-brand-500"></i>
                                <span>Live Preview Interaktif</span>
                            </div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Real-time</span>
                        </div>

                        <div class="p-6 space-y-6">

                            {{-- PREVIEW 1: TAB BROWSER --}}
                            <div>
                                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block mb-2">
                                    1. Tampilan Tab Browser
                                </span>
                                <div class="bg-slate-200 dark:bg-slate-900 rounded-xl p-2.5 pt-3 shadow-inner border border-slate-300 dark:border-slate-700">
                                    <div class="bg-white dark:bg-slate-800 rounded-t-lg px-3 py-2 flex items-center gap-2 max-w-[240px] shadow-sm border border-slate-200 dark:border-slate-700 border-b-0">
                                        <img :src="faviconPreviewUrl" alt="Favicon" class="w-4 h-4 object-contain shrink-0">
                                        <span class="text-xs font-medium text-slate-700 dark:text-slate-200 truncate" x-text="'Dashboard - ' + (appName || 'AEJ Manufactra')"></span>
                                        <span class="ml-auto text-slate-400 hover:text-slate-600 text-[10px]"><i class="fa-solid fa-xmark"></i></span>
                                    </div>
                                    <div class="bg-white dark:bg-slate-800 h-3 rounded-b-lg border border-slate-200 dark:border-slate-700 border-t-0"></div>
                                </div>
                            </div>

                            {{-- PREVIEW 2: SIDEBAR BRAND HEADER --}}
                            <div>
                                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block mb-2">
                                    2. Tampilan Header Sidebar
                                </span>
                                <div class="bg-slate-900 text-white rounded-xl p-4 border border-slate-800 shadow-md">
                                    <div class="flex items-center gap-3">
                                        <img :src="logoPreviewUrl" alt="Logo" class="w-12 h-12 object-contain shrink-0">
                                        <div class="flex flex-col">
                                            <span class="text-base font-bold tracking-tight text-white leading-none" x-text="appName || 'AEJ Manufactra'"></span>
                                            <span class="text-[10px] text-slate-500 font-medium tracking-widest uppercase mt-1" x-text="appTagline || 'Unified System'"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- PREVIEW 3: HEADER LOGIN SCREEN --}}
                            <div>
                                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block mb-2">
                                    3. Tampilan Halaman Login
                                </span>
                                <div class="bg-gradient-to-br from-indigo-50/80 to-blue-50/80 dark:from-slate-900 dark:to-slate-800 rounded-xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
                                    <div class="flex items-center gap-3 mb-3">
                                        <img :src="logoPreviewUrl" alt="Logo" class="h-12 w-auto object-contain rounded-lg">
                                        <span class="font-bold text-lg text-slate-900 dark:text-white tracking-tight">
                                            <span x-text="appName || 'AEJ ProductionApp'"></span>
                                        </span>
                                    </div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                                        Selamat Datang, silakan masuk ke sistem <strong class="text-slate-700 dark:text-slate-300" x-text="companyName || 'PT Abhimata Emas Juara'"></strong>.
                                    </div>
                                </div>
                            </div>

                            {{-- PREVIEW 4: FOOTER COPYRIGHT --}}
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">
                                        4. Tampilan Copyright Footer
                                    </span>
                                    <span class="text-[10px] text-brand-600 dark:text-brand-400 font-medium">Klik untuk edit</span>
                                </div>
                                <div @click="focusFooterInput()"
                                    class="bg-slate-50 hover:bg-slate-100 dark:bg-slate-900 dark:hover:bg-slate-850 rounded-xl p-3 border border-dashed border-brand-300 dark:border-brand-700 text-center text-xs text-slate-600 dark:text-slate-400 cursor-pointer transition-all duration-200 hover:border-brand-500 hover:shadow-sm group">
                                    &copy; {{ date('Y') }} <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="footerText || ((companyName || appName) + '. All rights reserved.')"></span>
                                    <div class="text-[10px] text-brand-500 mt-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <i class="fa-solid fa-pen-to-square mr-1"></i>Klik untuk ubah teks ini di formulir
                                    </div>
                                </div>
                            </div>

                            {{-- PREVIEW 5: PWA INSTALL MODAL --}}
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">
                                        5. Tampilan Saat Install App (PWA)
                                    </span>
                                    <span class="px-2 py-0.5 text-[10px] font-bold bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300 rounded-full">PWA Modal</span>
                                </div>
                                <div class="bg-slate-900 text-white rounded-2xl p-4 shadow-xl border border-slate-800">
                                    <div class="text-sm font-semibold text-slate-200 mb-3 flex items-center justify-between">
                                        <span>Install app</span>
                                        <i class="fa-solid fa-download text-slate-500 text-xs"></i>
                                    </div>
                                    <div class="flex items-center gap-3.5 mb-4">
                                        <div class="w-12 h-12 rounded-xl bg-slate-800 p-1.5 flex items-center justify-center shrink-0 border border-slate-700 shadow">
                                            <img :src="logoPreviewUrl" alt="PWA Icon" class="max-h-full max-w-full object-contain">
                                        </div>
                                        <div class="flex flex-col min-w-0">
                                            <span class="text-sm font-bold text-white truncate" x-text="appName || 'HBT Produksi'"></span>
                                            <span class="text-xs text-indigo-400 truncate">{{ request()->getHost() }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-800">
                                        <span class="px-3.5 py-1.5 rounded-full text-xs font-semibold text-slate-300 bg-slate-800/80 border border-slate-700">Cancel</span>
                                        <span class="px-4 py-1.5 rounded-full text-xs font-bold text-white bg-indigo-600 shadow-md">Install</span>
                                    </div>
                                </div>
                                <p class="mt-1.5 text-[11px] text-slate-400">
                                    <i class="fa-solid fa-circle-check text-emerald-500 mr-1"></i>Nama & ikon di atas langsung mengikuti Nama Website & Logo yang Anda simpan.
                                </p>
                            </div>

                        </div>
                    </div>

                </div>

            </div>

        </form>

        {{-- HIDDEN FORMS FOR RESET --}}
        <form id="resetLogoForm" action="{{ route('settings.app.reset-logo') }}" method="POST" class="hidden">
            @csrf
        </form>
        <form id="resetFaviconForm" action="{{ route('settings.app.reset-favicon') }}" method="POST" class="hidden">
            @csrf
        </form>

    </div>

    {{-- ALPINE.JS & SCRIPTS --}}
    <script>
        function settingApp() {
            return {
                appName: '{{ addslashes($settings['app_name'] ?? 'HBT Produksi') }}',
                appShortName: '{{ addslashes($settings['app_short_name'] ?? 'HBT Produksi') }}',
                companyName: '{{ addslashes($settings['company_name'] ?? 'Herbatech') }}',
                appTagline: '{{ addslashes($settings['app_tagline'] ?? 'Unified Production System') }}',
                appSubTagline: '{{ addslashes($settings['app_sub_tagline'] ?? 'Production System') }}',
                appDesc: '{{ addslashes($settings['app_description'] ?? 'Sistem ERP manufaktur terintegrasi untuk efisiensi produksi dan pemantauan real-time.') }}',
                footerText: '{{ addslashes(old('footer_text', app_footer_text())) }}',
                logoPreviewUrl: '{{ app_logo_url() }}',
                faviconPreviewUrl: '{{ app_favicon_url() }}',
                footerManuallyChanged: false,

                onCompanyChange() {
                    if (!this.footerManuallyChanged) {
                        this.footerText = (this.companyName || this.appName) + '. All rights reserved.';
                    }
                },

                focusFooterInput() {
                    const el = document.getElementById('footer_text');
                    if (el) {
                        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        el.focus();
                        el.classList.add('ring-4', 'ring-brand-500/30');
                        setTimeout(() => el.classList.remove('ring-4', 'ring-brand-500/30'), 2000);
                    }
                },

                handleLogoChange(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.logoPreviewUrl = URL.createObjectURL(file);
                    }
                },

                handleFaviconChange(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.faviconPreviewUrl = URL.createObjectURL(file);
                    }
                }
            }
        }

        function confirmResetLogo() {
            Swal.fire({
                title: 'Reset Logo ke Default?',
                text: "Logo yang telah diunggah akan dihapus dan dikembalikan ke logo bawaan sistem.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6366f1',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Reset Logo!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('resetLogoForm').submit();
                }
            });
        }

        function confirmResetFavicon() {
            Swal.fire({
                title: 'Reset Favicon ke Default?',
                text: "Favicon yang telah diunggah akan dihapus dan dikembalikan ke favicon bawaan sistem.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6366f1',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Reset Favicon!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('resetFaviconForm').submit();
                }
            });
        }
    </script>
@endsection
