<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - {{ app_setting('app_name', 'AEJ Manufactra') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- === FAVICON & MANIFEST === --}}
    <link rel="shortcut icon" href="{{ app_favicon_url() }}" type="image/x-icon">
    <link rel="icon" href="{{ app_favicon_url() }}" type="image/png">
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    {{-- Fonts & Libraries --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Config --}}
    <script>
        if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia(
                '(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Inter"', 'sans-serif']
                    },
                    colors: {
                        // Mosaic Brand Color (Indigo)
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                            950: '#1e1b4b',
                        },
                        // Mosaic Dark Shades
                        slate: {
                            850: '#151f32',
                            900: '#0f172a'
                        }
                    },
                    boxShadow: {
                        'sidebar': '4px 0 24px rgba(0,0,0,0.04)'
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Modern Scrollbar */
        .custom-scroll::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .custom-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background: #475569;
            border-radius: 4px;
        }

        .custom-scroll::-webkit-scrollbar-thumb:hover {
            background: #6366f1;
        }

        /* Loader Animation */
        .loader-ring {
            border: 3px solid rgba(99, 102, 241, 0.2);
            border-left-color: #6366f1;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .fade-in {
            animation: fadeIn 0.4s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* SweetAlert */
        div:where(.swal2-container) div:where(.swal2-popup) {
            border-radius: 1rem !important;
            font-family: 'Inter', sans-serif !important;
        }

        .dark div:where(.swal2-container) div:where(.swal2-popup) {
            background: #1e293b !important;
            color: #f8fafc !important;
            border: 1px solid #334155;
        }
    </style>
</head>

<body class="font-sans antialiased text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-900"
    x-data="{
        darkMode: localStorage.getItem('darkMode') === 'true',
        sidebarOpen: false,
        sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
        toggleTheme() {
            this.darkMode = !this.darkMode;
            localStorage.setItem('darkMode', this.darkMode);
            if (this.darkMode) document.documentElement.classList.add('dark');
            else document.documentElement.classList.remove('dark');
        },
        toggleSidebar() {
            this.sidebarCollapsed = !this.sidebarCollapsed;
            localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
        }
    }">

    {{-- === LOADER === --}}
    <div id="loader"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-white dark:bg-slate-900 transition-opacity duration-300">
        <div class="flex flex-col items-center gap-4">
            <div class="loader-ring"></div>
            <div class="font-semibold text-slate-600 dark:text-slate-300 text-sm tracking-wide uppercase">Memuat...
            </div>
        </div>
    </div>

    <div class="flex h-screen overflow-hidden">

        {{-- BACKDROP MOBILE --}}
        <div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak x-transition.opacity
            class="fixed inset-0 z-40 bg-slate-900/80 backdrop-blur-sm lg:hidden"></div>

        {{-- === SIDEBAR (MOSAIC STYLE) === --}}
        <aside
            class="fixed inset-y-0 left-0 z-50 flex flex-col transition-all duration-300 ease-in-out transform bg-slate-900 text-slate-100 border-r border-slate-800"
            :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0', sidebarCollapsed ? 'lg:w-20' :
                'lg:w-64', 'w-64'
            ]">

            {{-- LOGO AREA --}}
            <div class="flex items-center justify-between h-16 px-4 bg-slate-900 border-b border-slate-800 shrink-0">
                <a href="/" class="flex items-center gap-3 group">
                    <img src="{{ app_logo_url() }}" alt="Logo"
                        class="object-contain w-12 h-12 rounded-lg transition-transform group-hover:scale-110">
                    <div x-show="!sidebarCollapsed" class="flex flex-col transition-opacity duration-200">
                        <span class="text-base font-bold tracking-tight text-white leading-none">{{ app_setting('app_name', 'AEJ Manufactra') }}</span>
                        <span class="text-[10px] text-slate-500 font-medium tracking-widest uppercase mt-0.5">{{ app_setting('app_tagline', 'Unified System') }}</span>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-500 hover:text-white">
                    <i class="fa-solid fa-arrow-left"></i>
                </button>
            </div>

            {{-- MENU LIST --}}
            <nav id="sidebar-nav" class="flex flex-col flex-1 px-4 py-4 overflow-y-auto custom-scroll space-y-1">

                {{-- INSTALL PWA --}}
                <div id="install-app-container" class="hidden mb-6" x-show="!sidebarCollapsed">
                    <button id="install-app-btn"
                        class="flex items-center w-full px-3 py-2.5 text-sm font-medium text-white transition-all bg-gradient-to-r from-brand-600 to-brand-500 rounded-lg shadow-lg hover:shadow-brand-500/25 group">
                        <i class="fa-solid fa-download w-5 text-center"></i>
                        <span class="ml-3 flex-1 text-left">Install App</span>
                    </button>
                </div>
                <div id="install-app-mini" class="hidden mb-6 justify-center" x-show="sidebarCollapsed">
                    <button id="install-app-btn-mini"
                        class="flex items-center justify-center w-10 h-10 text-white transition-colors bg-brand-600 rounded-lg hover:bg-brand-500 shadow-lg"
                        title="Install App">
                        <i class="fa-solid fa-download"></i>
                    </button>
                </div>

                {{-- DYNAMIC MENUS --}}
                @foreach ($sidebarMenus ?? [] as $groupMenu)
                    @if ($groupMenu->children->isNotEmpty())
                        {{-- Group Title --}}
                        <div x-show="!sidebarCollapsed"
                            class="mt-6 mb-2 ml-1 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            {{ $groupMenu->name }}
                        </div>
                        <div x-show="sidebarCollapsed" class="my-4 border-t border-slate-700 mx-2"></div>

                        {{-- Menu Items --}}
                        @foreach ($groupMenu->children as $menu)
                            @php
                                $url = Illuminate\Support\Facades\Route::has($menu->route) ? route($menu->route) : '#';
                                $isActive = request()->routeIs($menu->route . '*');
                                // Style: Active = Brand BG, Inactive = Transparent with Hover
                                $activeClass = $isActive
                                    ? 'bg-brand-600 text-white shadow-md shadow-brand-500/10'
                                    : 'text-slate-400 hover:text-white hover:bg-slate-800';
                            @endphp
                            <a href="{{ $url }}"
                                class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 group {{ $activeClass }}"
                                :class="sidebarCollapsed ? 'justify-center' : ''" title="{{ $menu->name }}">
                                <i
                                    class="{{ $menu->icon }} w-5 text-center text-lg transition-colors {{ $isActive ? 'text-white' : 'text-slate-500 group-hover:text-white' }}"></i>
                                <span class="ml-3 text-sm font-medium transition-opacity duration-200"
                                    x-show="!sidebarCollapsed">{{ $menu->name }}</span>
                            </a>
                        @endforeach
                    @endif
                @endforeach

                {{-- SYSTEM LOCK --}}
                @if (auth()->check() && auth()->user()->role === 'super_admin')
                    <div x-show="!sidebarCollapsed"
                        class="mt-6 mb-2 ml-1 text-[11px] font-bold text-slate-500 uppercase tracking-wider">System
                    </div>
                    <div x-show="sidebarCollapsed" class="my-4 border-t border-slate-700 mx-2"></div>
                    @php $isSystemLocked = \Illuminate\Support\Facades\Storage::exists('system_locked'); @endphp
                    <form id="lock-system-form"
                        action="{{ Route::has('system.toggle-lock') ? route('system.toggle-lock') : '#' }}"
                        method="POST">
                        @csrf
                        <button type="button" onclick="confirmLockSystem({{ $isSystemLocked ? 'true' : 'false' }})"
                            class="flex items-center w-full px-3 py-2 rounded-lg transition-all duration-200 group {{ $isSystemLocked ? 'bg-red-500/10 text-red-400 hover:bg-red-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}"
                            :class="sidebarCollapsed ? 'justify-center' : ''">
                            <i
                                class="fa-solid {{ $isSystemLocked ? 'fa-lock-open' : 'fa-lock' }} w-5 text-center text-lg"></i>
                            <span class="ml-3 text-sm font-medium"
                                x-show="!sidebarCollapsed">{{ $isSystemLocked ? 'Unlock System' : 'Lock System' }}</span>
                        </button>
                    </form>
                @endif

            </nav>

            {{-- SIDEBAR FOOTER (PROFILE) --}}
            <div class="flex items-center justify-between p-4 border-t border-slate-800 bg-slate-900 shrink-0">
                <div class="flex items-center w-full gap-3 cursor-pointer group" onclick="openAboutModal()"
                    :class="sidebarCollapsed ? 'justify-center' : ''">
                    <div
                        class="flex items-center justify-center w-9 h-9 font-bold text-white rounded-lg bg-brand-600 shadow-lg shadow-brand-900/20 shrink-0">
                        {{ substr(auth()->user()->name ?? 'G', 0, 1) }}
                    </div>
                    <div class="overflow-hidden" x-show="!sidebarCollapsed">
                        <p
                            class="text-sm font-semibold text-slate-200 truncate group-hover:text-white transition-colors">
                            {{ auth()->user()->name ?? 'Guest' }}</p>
                        <p class="text-xs text-slate-500 truncate capitalize">
                            {{ str_replace('_', ' ', auth()->user()->role ?? 'User') }}</p>
                    </div>
                </div>
                <form id="logout-form" method="POST" action="{{ route('logout') }}" x-show="!sidebarCollapsed">
                    @csrf
                    <button type="button" onclick="confirmLogout()"
                        class="text-slate-500 hover:text-white transition-colors p-2" title="Logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        </aside>

        {{-- === CONTENT AREA === --}}
        <div class="flex flex-col flex-1 min-w-0 overflow-hidden bg-slate-50 dark:bg-slate-950 transition-all duration-300"
            :class="sidebarCollapsed ? 'lg:ml-20' : 'lg:ml-64'">

            {{-- HEADER (Dengan Toggle Switch Animasi) --}}
            <header
                class="sticky top-0 z-30 px-4 sm:px-6 lg:px-8 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 h-16 flex items-center justify-between shadow-sm transition-colors duration-300">

                {{-- Left: Toggle Sidebar & Title --}}
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true"
                        class="text-slate-500 hover:text-slate-600 lg:hidden transition-colors">
                        <span class="sr-only">Open sidebar</span>
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                    <button @click="toggleSidebar()"
                        class="hidden text-slate-400 hover:text-brand-600 lg:block transition-colors">
                        <span class="sr-only">Collapse sidebar</span>
                        <i class="fa-solid" :class="sidebarCollapsed ? 'fa-indent' : 'fa-outdent'"></i>
                    </button>
                    <div class="hidden md:block h-6 w-px bg-slate-200 dark:bg-slate-700 mx-2"></div>
                    <h1 class="text-lg font-bold text-slate-800 dark:text-slate-100 tracking-tight">@yield('title')
                    </h1>
                </div>

                {{-- Right: Widgets --}}
                <div class="flex items-center gap-3 md:gap-4">

                    {{-- GREETING (Hidden on small screens) --}}
                    <div class="hidden lg:flex flex-col items-end mr-2">
                        @php
                            $h = now()->setTimezone('Asia/Jakarta')->hour;
                            $greet =
                                $h < 11
                                    ? 'Selamat Pagi'
                                    : ($h < 15
                                        ? 'Selamat Siang'
                                        : ($h < 18
                                            ? 'Selamat Sore'
                                            : 'Selamat Malam'));
                        @endphp
                        <span
                            class="text-[10px] setence case tracking-wider font-bold text-slate-400">{{ $greet }}</span>
                        <span
                            class="text-sm font-bold text-brand-600 dark:text-brand-400 leading-none">{{ auth()->user()->name ?? 'User' }}</span>
                    </div>

                    {{-- WIDGET JAM & TANGGAL --}}
                    <div
                        class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 shadow-sm">
                        <i class="fa-regular fa-calendar text-xs text-brand-500"></i>
                        <span
                            class="text-xs font-semibold mr-2">{{ now()->locale('id')->translatedFormat('d M Y') }}</span>
                        <div class="w-px h-3 bg-slate-300 dark:bg-slate-600"></div>
                        <span class="text-xs font-mono font-bold tracking-widest ml-1" id="clock">--:--</span>
                    </div>

                    <div class="h-6 w-px bg-slate-200 dark:bg-slate-700 hidden md:block mx-1"></div>

                    {{-- === ANIMATED THEME TOGGLE === --}}
                    <button @click="toggleTheme()"
                        class="relative inline-flex h-8 w-14 items-center rounded-full transition-colors duration-300 focus:outline-none shadow-inner border border-slate-200 dark:border-slate-700"
                        :class="darkMode ? 'bg-slate-800' : 'bg-slate-200'">
                        <span class="sr-only">Toggle Dark Mode</span>
                        <span
                            class="inline-block h-6 w-6 transform rounded-full bg-white shadow-md transition duration-300 ease-in-out flex items-center justify-center"
                            :class="darkMode ? 'translate-x-7' : 'translate-x-1'">
                            <i class="fa-solid text-xs transition-all duration-300"
                                :class="darkMode ? 'fa-moon text-brand-500 rotate-0' : 'fa-sun text-amber-500 -rotate-90'">
                            </i>
                        </span>
                    </button>

                    {{-- MOBILE PROFILE ICON --}}
                    <div class="lg:hidden w-8 h-8 rounded-full bg-brand-600 flex items-center justify-center text-white text-xs font-bold shadow-md cursor-pointer"
                        onclick="openAboutModal()">
                        {{ substr(auth()->user()->name ?? 'G', 0, 1) }}
                    </div>
                </div>
            </header>

            {{-- MAIN CONTENT --}}
            <main
                class="flex-1 p-4 overflow-y-auto overflow-x-hidden custom-scroll sm:p-6 lg:p-8 flex flex-col justify-between">

                {{-- Konten Halaman --}}
                <div class="max-w-[1600px] mx-auto w-full fade-in mb-auto">
                    @yield('content')
                </div>

                {{-- FOOTER DASHBOARD --}}
                <div class="max-w-[1600px] mx-auto w-full mt-10 border-t border-slate-200 dark:border-slate-800 pt-6">
                    <div
                        class="flex flex-col md:flex-row justify-between items-center text-xs font-medium text-slate-500">

                        {{-- Bagian Kiri: Copyright --}}
                        <div class="text-center md:text-left mb-4 md:mb-0">
                            <p>&copy; {{ date('Y') }} {{ app_setting('footer_text', (app_setting('company_name') ? app_setting('company_name') . '. All rights reserved.' : app_setting('app_name', 'AEJ Manufactra') . '. All rights reserved.')) }}</p>
                        </div>

                        {{-- Bagian Kanan: Link & Versi --}}
                        <div class="flex items-center gap-6">
                            @if (Route::has('about'))
                                <a href="{{ route('about') }}"
                                    class="hover:text-brand-600 transition-colors cursor-pointer">
                                    Tentang Sistem
                                </a>
                            @else
                                <button type="button" class="hover:text-brand-600 transition-colors cursor-pointer">
                                    Tentang Sistem
                                </button>
                            @endif
                            <span
                                class="bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded text-[10px] font-bold text-slate-400">
                                v5.2.0
                            </span>
                        </div>

                    </div>
                </div>
            </main>

        </div>
    </div>

    {{-- MODALS & OVERLAYS --}}

    <div id="about-modal" class="fixed inset-0 z-[100] hidden">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeAboutModal()">
        </div>
        <div class="relative z-10 flex items-center justify-center min-h-screen px-4 p-4 text-center sm:p-0">
            <div
                class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl transform transition-all sm:my-8 sm:w-full sm:max-w-sm p-8 overflow-hidden border border-slate-100 dark:border-slate-700">
                <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-brand-500 to-brand-600"></div>
                <div
                    class="mx-auto flex items-center justify-center h-16 w-16 rounded-2xl bg-brand-50 dark:bg-brand-900/20 mb-6 shadow-sm">
                    <img src="{{ app_logo_url() }}" class="h-10 w-10 object-contain" alt="Logo">
                </div>
                <h3 class="text-xl font-extrabold text-slate-900 dark:text-white">{{ app_setting('app_name', 'AEJ Manufactra') }}</h3>
                <p class="text-xs text-brand-600 font-bold tracking-widest uppercase mb-6">{{ app_setting('app_sub_tagline', 'Production System') }}</p>
                <div class="text-sm text-slate-500 dark:text-slate-400 mb-8 leading-relaxed">
                    {{ app_setting('app_description', 'Sistem ERP manufaktur terintegrasi untuk efisiensi produksi dan pemantauan real-time.') }}
                </div>
                <div class="text-xs text-slate-400 mb-6 flex justify-center gap-1">
                    <span>Developed by</span>
                    <a href="#" class="font-bold text-slate-700 dark:text-slate-300 hover:text-brand-600">EK
                        Dev</a>
                </div>
                <button type="button" onclick="closeAboutModal()"
                    class="w-full inline-flex justify-center rounded-xl shadow-lg shadow-brand-500/20 px-4 py-3 bg-brand-600 text-sm font-bold text-white hover:bg-brand-700 focus:outline-none transition-all hover:-translate-y-0.5">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <div id="offline-overlay"
        class="fixed inset-0 z-[9999] hidden bg-slate-900/80 backdrop-blur-md flex items-center justify-center">
        <div
            class="bg-white dark:bg-slate-800 p-8 rounded-2xl text-center shadow-2xl border border-red-100 dark:border-red-900/30 max-w-sm">
            <div
                class="w-16 h-16 bg-red-50 dark:bg-red-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-wifi-slash text-2xl text-red-500 animate-pulse"></i>
            </div>
            <h2 class="text-lg font-bold text-slate-800 dark:text-white">Koneksi Terputus</h2>
            <p class="text-slate-500 mt-2 text-sm">Mohon periksa koneksi internet Anda.</p>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script>
        // Loader
        const loader = document.getElementById('loader');
        window.addEventListener('load', () => setTimeout(() => {
            loader.classList.add('opacity-0', 'pointer-events-none');
        }, 300));

        // PWA Logic
        let deferredPrompt;
        const installContainer = document.getElementById('install-app-container');
        const installMini = document.getElementById('install-app-mini');
        const installBtn = document.getElementById('install-app-btn');
        const installBtnMini = document.getElementById('install-app-btn-mini');

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            if (installContainer) installContainer.classList.remove('hidden');
            if (installMini) {
                installMini.classList.remove('hidden');
                installMini.classList.add('flex');
            }
        });

        function triggerInstall() {
            if (installContainer) installContainer.classList.add('hidden');
            if (installMini) {
                installMini.classList.add('hidden');
                installMini.classList.remove('flex');
            }
            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then((choiceResult) => {
                    deferredPrompt = null;
                });
            }
        }
        if (installBtn) installBtn.addEventListener('click', triggerInstall);
        if (installBtnMini) installBtnMini.addEventListener('click', triggerInstall);

        // Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/service-worker.js');
            });
        }

        // Clock
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-GB', {
                hour12: false
            });
            const clockEl = document.getElementById('clock');
            if (clockEl) clockEl.innerText = timeString + " WIB";
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Actions
        function confirmLogout() {
            Swal.fire({
                title: 'Akhiri Sesi?',
                text: "Anda akan keluar dari aplikasi.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Keluar',
                cancelButtonText: 'Batal',
                background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#fff' : '#1e293b'
            }).then((result) => {
                if (result.isConfirmed) document.getElementById('logout-form').submit();
            })
        }

        function confirmLockSystem(isLocked) {
            Swal.fire({
                title: isLocked ? 'Buka Kunci?' : 'Kunci Sistem?',
                text: isLocked ? 'Akses user akan dibuka kembali.' : 'User lain tidak bisa login.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                confirmButtonText: 'Ya, Lanjutkan',
                background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#fff' : '#1e293b'
            }).then((result) => {
                if (result.isConfirmed) document.getElementById('lock-system-form').submit();
            });
        }

        // Modals & Utils
        function openAboutModal() {
            document.getElementById('about-modal').classList.remove('hidden');
        }

        function closeAboutModal() {
            document.getElementById('about-modal').classList.add('hidden');
        }
        window.addEventListener('offline', () => document.getElementById('offline-overlay').classList.remove('hidden'));
        window.addEventListener('online', () => document.getElementById('offline-overlay').classList.add('hidden'));

        const sidebarNav = document.getElementById('sidebar-nav');
        if (sidebarNav) {
            const savedScroll = localStorage.getItem('sidebarScrollTop');
            if (savedScroll) sidebarNav.scrollTop = savedScroll;
            sidebarNav.addEventListener('scroll', () => {
                localStorage.setItem('sidebarScrollTop', sidebarNav.scrollTop);
            });
        }
    </script>
</body>

</html>
