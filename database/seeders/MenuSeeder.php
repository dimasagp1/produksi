<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\MenuAccess;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bersihkan data lama jika ada
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        MenuAccess::truncate();
        Menu::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $allRoles = ['super_admin', 'admin', 'gm', 'manager', 'spv', 'leader', 'operator'];
        $staffRoles = ['super_admin', 'admin', 'gm', 'manager', 'spv'];
        $productionRoles = ['super_admin', 'admin', 'gm', 'manager', 'spv', 'leader', 'operator'];
        $leaderRoles = ['super_admin', 'admin', 'gm', 'manager', 'spv', 'leader'];
        $adminOnly = ['super_admin', 'admin'];
        $superAdminOnly = ['super_admin'];

        $menuStructure = [
            [
                'name' => 'Dashboard',
                'order' => 1,
                'roles' => $allRoles,
                'children' => [
                    [
                        'name' => 'Dashboard',
                        'route' => 'dashboard',
                        'icon' => 'fa-solid fa-gauge-high',
                        'order' => 1,
                        'roles' => $allRoles,
                    ],
                    [
                        'name' => 'Log Aktivitas',
                        'route' => 'activity-logs.index',
                        'icon' => 'fa-solid fa-clock-rotate-left',
                        'order' => 2,
                        'roles' => $adminOnly,
                    ],
                ],
            ],
            [
                'name' => 'PPIC',
                'order' => 2,
                'roles' => $staffRoles,
                'children' => [
                    [
                        'name' => 'Master Production Schedule',
                        'route' => 'mps.index',
                        'icon' => 'fa-solid fa-calendar-days',
                        'order' => 1,
                        'roles' => $staffRoles,
                    ],
                    [
                        'name' => 'Jadwal Produksi',
                        'route' => 'schedule.index',
                        'icon' => 'fa-solid fa-timeline',
                        'order' => 2,
                        'roles' => $staffRoles,
                    ],
                    [
                        'name' => 'SPK / Batch',
                        'route' => 'batches.index',
                        'icon' => 'fa-solid fa-boxes-stacked',
                        'order' => 3,
                        'roles' => $staffRoles,
                    ],
                    [
                        'name' => 'Bill of Materials (BOM)',
                        'route' => 'bom.index',
                        'icon' => 'fa-solid fa-layer-group',
                        'order' => 4,
                        'roles' => $staffRoles,
                    ],
                    [
                        'name' => 'Production Routing',
                        'route' => 'routing.index',
                        'icon' => 'fa-solid fa-route',
                        'order' => 5,
                        'roles' => $staffRoles,
                    ],
                ],
            ],
            [
                'name' => 'Produksi',
                'order' => 3,
                'roles' => $productionRoles,
                'children' => [
                    [
                        'name' => 'Monitoring Produksi',
                        'route' => 'monitoring-produksi.index',
                        'icon' => 'fa-solid fa-desktop',
                        'order' => 1,
                        'roles' => $productionRoles,
                    ],
                    [
                        'name' => 'Andon Digital',
                        'route' => 'andon.index',
                        'icon' => 'fa-solid fa-bullhorn',
                        'order' => 2,
                        'roles' => $productionRoles,
                    ],
                    [
                        'name' => 'Laporan Harian',
                        'route' => 'daily-reports.index',
                        'icon' => 'fa-solid fa-clipboard-list',
                        'order' => 3,
                        'roles' => $productionRoles,
                    ],
                    [
                        'name' => 'Output vs Target',
                        'route' => 'output-target.index',
                        'icon' => 'fa-solid fa-chart-line',
                        'order' => 4,
                        'roles' => $leaderRoles,
                    ],
                    [
                        'name' => 'OEE Dashboard',
                        'route' => 'oee-dashboard.index',
                        'icon' => 'fa-solid fa-chart-pie',
                        'order' => 5,
                        'roles' => $leaderRoles,
                    ],
                    [
                        'name' => 'Downtime Tracking',
                        'route' => 'downtime-tracking.index',
                        'icon' => 'fa-solid fa-stopwatch',
                        'order' => 6,
                        'roles' => $leaderRoles,
                    ],
                    [
                        'name' => 'Standar vs Aktual',
                        'route' => 'standard-actual.index',
                        'icon' => 'fa-solid fa-scale-balanced',
                        'order' => 7,
                        'roles' => $staffRoles,
                    ],
                ],
            ],
            [
                'name' => 'Quality Control',
                'order' => 4,
                'roles' => $staffRoles,
                'children' => [
                    [
                        'name' => 'IQC (Bahan Baku)',
                        'route' => 'iqc.index',
                        'icon' => 'fa-solid fa-vial',
                        'order' => 1,
                        'roles' => $staffRoles,
                    ],
                    [
                        'name' => 'IPQC (Patrol)',
                        'route' => 'ipqc.index',
                        'icon' => 'fa-solid fa-clipboard-check',
                        'order' => 2,
                        'roles' => $staffRoles,
                    ],
                    [
                        'name' => 'OQC (Final Check)',
                        'route' => 'oqc.index',
                        'icon' => 'fa-solid fa-check-double',
                        'order' => 3,
                        'roles' => $staffRoles,
                    ],
                    [
                        'name' => 'Analisis QC & Pareto',
                        'route' => 'qc-analysis.index',
                        'icon' => 'fa-solid fa-chart-simple',
                        'order' => 4,
                        'roles' => $staffRoles,
                    ],
                    [
                        'name' => 'CAPA (Perbaikan)',
                        'route' => 'capa.index',
                        'icon' => 'fa-solid fa-wrench',
                        'order' => 5,
                        'roles' => $staffRoles,
                    ],
                    [
                        'name' => 'COA (Sertifikat)',
                        'route' => 'coa.index',
                        'icon' => 'fa-solid fa-certificate',
                        'order' => 6,
                        'roles' => $staffRoles,
                    ],
                ],
            ],
            [
                'name' => 'Engineering',
                'order' => 5,
                'roles' => $staffRoles,
                'children' => [
                    [
                        'name' => 'Breakdown Mesin',
                        'route' => 'breakdown.index',
                        'icon' => 'fa-solid fa-triangle-exclamation',
                        'order' => 1,
                        'roles' => $staffRoles,
                    ],
                    [
                        'name' => 'Work Order Maintenance',
                        'route' => 'work-order.index',
                        'icon' => 'fa-solid fa-file-signature',
                        'order' => 2,
                        'roles' => $staffRoles,
                    ],
                    [
                        'name' => 'Preventive Maintenance',
                        'route' => 'preventive.index',
                        'icon' => 'fa-solid fa-screwdriver-wrench',
                        'order' => 3,
                        'roles' => $staffRoles,
                    ],
                    [
                        'name' => 'Manajemen Sparepart',
                        'route' => 'sparepart.index',
                        'icon' => 'fa-solid fa-gears',
                        'order' => 4,
                        'roles' => $staffRoles,
                    ],
                    [
                        'name' => 'Analisis MTBF & MTTR',
                        'route' => 'reliability.index',
                        'icon' => 'fa-solid fa-chart-column',
                        'order' => 5,
                        'roles' => $staffRoles,
                    ],
                    [
                        'name' => 'Riwayat Mesin',
                        'route' => 'machine-history.index',
                        'icon' => 'fa-solid fa-clock-rotate-left',
                        'order' => 6,
                        'roles' => $staffRoles,
                    ],
                ],
            ],
            [
                'name' => 'Master Data',
                'order' => 6,
                'roles' => $staffRoles,
                'children' => [
                    [
                        'name' => 'Data Produk',
                        'route' => 'products.index',
                        'icon' => 'fa-solid fa-box',
                        'order' => 1,
                        'roles' => $staffRoles,
                    ],
                    [
                        'name' => 'Data Mesin',
                        'route' => 'machines.index',
                        'icon' => 'fa-solid fa-industry',
                        'order' => 2,
                        'roles' => $staffRoles,
                    ],
                    [
                        'name' => 'Data Warna',
                        'route' => 'colors.index',
                        'icon' => 'fa-solid fa-palette',
                        'order' => 3,
                        'roles' => $staffRoles,
                    ],
                    [
                        'name' => 'Jenis Kemasan',
                        'route' => 'packaging-types.index',
                        'icon' => 'fa-solid fa-box-archive',
                        'order' => 4,
                        'roles' => $staffRoles,
                    ],
                    [
                        'name' => 'Reject Items',
                        'route' => 'reject-items.index',
                        'icon' => 'fa-solid fa-ban',
                        'order' => 5,
                        'roles' => $staffRoles,
                    ],
                    [
                        'name' => 'Data Downtime',
                        'route' => 'downtimes.index',
                        'icon' => 'fa-solid fa-hourglass-half',
                        'order' => 6,
                        'roles' => $staffRoles,
                    ],
                    [
                        'name' => 'Data Shift',
                        'route' => 'shifts.index',
                        'icon' => 'fa-solid fa-clock',
                        'order' => 7,
                        'roles' => $staffRoles,
                    ],
                    [
                        'name' => 'Data Koordinator',
                        'route' => 'coordinators.index',
                        'icon' => 'fa-solid fa-user-tie',
                        'order' => 8,
                        'roles' => $staffRoles,
                    ],
                    [
                        'name' => 'Data Operator',
                        'route' => 'operators.index',
                        'icon' => 'fa-solid fa-users',
                        'order' => 9,
                        'roles' => $staffRoles,
                    ],
                ],
            ],
            [
                'name' => 'Pengaturan',
                'order' => 7,
                'roles' => $superAdminOnly,
                'children' => [
                    [
                        'name' => 'Identitas Website',
                        'route' => 'settings.app.index',
                        'icon' => 'fa-solid fa-sliders',
                        'order' => 1,
                        'roles' => $superAdminOnly,
                    ],
                    [
                        'name' => 'Manajemen Pengguna',
                        'route' => 'users.index',
                        'icon' => 'fa-solid fa-users-gear',
                        'order' => 2,
                        'roles' => $superAdminOnly,
                    ],
                    [
                        'name' => 'Konfigurasi Akses Menu',
                        'route' => 'settings.menus.index',
                        'icon' => 'fa-solid fa-bars-staggered',
                        'order' => 3,
                        'roles' => $superAdminOnly,
                    ],
                ],
            ],
        ];

        foreach ($menuStructure as $group) {
            $parentMenu = Menu::create([
                'name' => $group['name'],
                'route' => null,
                'icon' => null,
                'parent_id' => 0,
                'order' => $group['order'],
                'is_active' => true,
            ]);

            foreach ($group['roles'] as $role) {
                MenuAccess::create([
                    'menu_id' => $parentMenu->id,
                    'role_name' => $role,
                ]);
            }

            if (!empty($group['children'])) {
                foreach ($group['children'] as $child) {
                    $childMenu = Menu::create([
                        'name' => $child['name'],
                        'route' => $child['route'],
                        'icon' => $child['icon'],
                        'parent_id' => $parentMenu->id,
                        'order' => $child['order'],
                        'is_active' => true,
                    ]);

                    foreach ($child['roles'] as $role) {
                        MenuAccess::create([
                            'menu_id' => $childMenu->id,
                            'role_name' => $role,
                        ]);
                    }
                }
            }
        }
    }
}
