# AEJ Production App

AEJ Production App adalah sistem manajemen produksi manufaktur (MMS) yang dirancang untuk mengelola alur kerja produksi, mulai dari perencanaan batch (SPK), pelaporan harian operator, hingga pemantauan kinerja melalui dashboard analitik secara real-time.

## 🚀 Fitur Utama

- **Dashboard Terintegrasi**: Visualisasi KPI produksi (Target, Output, Achievement, Yield, Efisiensi) menggunakan ApexCharts.
- **Manajemen Batch (SPK)**: Kontrol penuh terhadap surat perintah kerja, termasuk fitur penguncian otomatis (Lock/Unlock) saat target tercapai.
- **Laporan Harian (Daily Report)**: Input data produksi harian yang mendetail mencakup:
    - Output Good (FG) & Reject.
    - Dokumentasi Downtime mesin dengan alasan kendala.
    - Kalkulasi otomatis Cycle Time dan Cavity terhadap target teori.
    - Pelacakan sisa WIP (Work In Process).
- **Master Data Dinamis**: Pengelolaan data produk yang mendukung multi-mesin (mesin utama dan alternatif) dengan standar CT/Cavity yang berbeda.
- **Sistem Ekspor Laporan**: Generate laporan Batch dan Produksi ke dalam format Excel dan PDF yang siap cetak.

## 🛠️ Teknologi yang Digunakan

- **Framework**: [Laravel 12+](https://laravel.com)
- **Database**: MySQL / MariaDB
- **Frontend**: Blade Templating, CSS Modern, Font Inter.
- **Library JavaScript**:
    - [ApexCharts](https://apexcharts.com/) - Visualisasi data dan grafik.
    - [Select2](https://select2.org/) - Dropdown pencarian dinamis.
    - [Flatpickr](https://flatpickr.js.org/) - Pemilih tanggal dan waktu.
    - [SweetAlert2](https://sweetalert2.github.io/) - Notifikasi dan dialog konfirmasi.
- **Lainnya**: [Laravel Excel](https://docs.laravel-excel.com/) & [DomPDF](https://github.com/barryvdh/laravel-dompdf).

## ⚙️ Instalasi

Pastikan server atau komputer lokal Anda sudah terpasang PHP >= 8.4, Composer, dan MySQL.

1. **Clone Repository**
    ```bash
    git clone [https://github.com/username-anda/produksi-abhimata.git](https://github.com/username-anda/produksi-abhimata.git)
    cd produksi-abhimata
    ```
