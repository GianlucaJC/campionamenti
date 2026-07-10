<?php

namespace Database\Seeders;

use App\Models\MonitoringDepartment;
use App\Models\MonitoringSection;
use App\Models\SamplingPoint;
use Illuminate\Database\Seeder;

class MonitoringTemplateSeeder extends Seeder
{
    /**
     * Seed base dynamic sections and sample points.
     */
    public function run(): void
    {
        $sections = [
            [
                'section' => [
                    'code' => 'reparto_piastre',
                    'name' => 'Reparto Piastre',
                    'description' => 'Monitoraggio microbiologico reparto piastre: campionamento aria attivo.',
                    'sort_order' => 10,
                    'is_active' => true,
                ],
                'departments' => [
                    ['code' => 'laminar', 'name' => 'Laminar flow', 'sort_order' => 10],
                    ['code' => 'ambiente', 'name' => 'Ambiente', 'sort_order' => 20],
                    ['code' => 'controlli', 'name' => 'Controlli', 'sort_order' => 30],
                ],
                'points' => [
                    ['legacy_code' => '1', 'title' => 'Laminar flow: Macchina piastre MP02 Modulo 01A', 'department_code' => 'laminar', 'default_volume_liters' => 1000, 'sort_order' => 10],
                    ['legacy_code' => '2', 'title' => 'Laminar flow: Macchina piastre MP02 Modulo 01B', 'department_code' => 'laminar', 'default_volume_liters' => 1000, 'sort_order' => 20],
                    ['legacy_code' => '17', 'title' => 'Laminar flow: Cappa CA 09', 'department_code' => 'laminar', 'default_volume_liters' => 1000, 'sort_order' => 30],
                    ['legacy_code' => '21', 'title' => 'Ambiente: centro locale Piastre', 'department_code' => 'ambiente', 'default_volume_liters' => 200, 'sort_order' => 40],
                    ['legacy_code' => '300', 'title' => 'Controllo negativo', 'department_code' => 'controlli', 'default_volume_liters' => 1000, 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 50],
                ],
            ],
            [
                'section' => [
                    'code' => 'reparti_vari',
                    'name' => 'Reparti Vari',
                    'description' => 'Monitoraggio microbiologico trasversale su reparti diversi.',
                    'sort_order' => 20,
                    'is_active' => true,
                ],
                'departments' => [
                    ['code' => 'laminar', 'name' => 'Laminar flow', 'sort_order' => 10],
                    ['code' => 'ambiente', 'name' => 'Ambiente', 'sort_order' => 20],
                    ['code' => 'controlli', 'name' => 'Controlli', 'sort_order' => 30],
                ],
                'points' => [
                    ['legacy_code' => '22', 'title' => 'Laminar flow: Macchina slide MS01 Modulo 1', 'department_code' => 'laminar', 'area_label' => '7 Slide', 'default_volume_liters' => 1000, 'sort_order' => 10],
                    ['legacy_code' => '32', 'title' => 'Ambiente: centro locale Slide', 'department_code' => 'ambiente', 'area_label' => '7 Slide', 'default_volume_liters' => 200, 'sort_order' => 20],
                    ['legacy_code' => '49', 'title' => 'Laminar flow: Cappa CO 01', 'department_code' => 'laminar', 'area_label' => 'Antibiotici/MIC impreg.', 'default_volume_liters' => 1000, 'sort_order' => 30],
                    ['legacy_code' => '87', 'title' => 'Ambiente: Antibiotici fustellatura/confez.', 'department_code' => 'ambiente', 'area_label' => 'Antibiotici', 'default_volume_liters' => 200, 'sort_order' => 40],
                    ['legacy_code' => '301', 'title' => 'Controllo negativo', 'department_code' => 'controlli', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 50],
                ],
            ],
            [
                'section' => [
                    'code' => 'reparto_enteropluritest',
                    'name' => 'Reparto Enteropluritest',
                    'description' => 'Monitoraggio microbiologico reparto Enteropluritest.',
                    'sort_order' => 30,
                    'is_active' => true,
                ],
                'departments' => [
                    ['code' => 'ambiente', 'name' => 'Ambiente', 'sort_order' => 10],
                    ['code' => 'laminar', 'name' => 'Laminar flow', 'sort_order' => 20],
                    ['code' => 'controlli', 'name' => 'Controlli', 'sort_order' => 30],
                ],
                'points' => [
                    ['legacy_code' => '52', 'title' => 'Ambiente: centro locale Enteropluritest', 'department_code' => 'ambiente', 'default_volume_liters' => 200, 'sort_order' => 10],
                    ['legacy_code' => '53', 'title' => 'Laminar flow: MN01 Modulo 1A', 'department_code' => 'laminar', 'default_volume_liters' => 1000, 'sort_order' => 20],
                    ['legacy_code' => '54', 'title' => 'Laminar flow: MN01 Modulo 2A', 'department_code' => 'laminar', 'default_volume_liters' => 1000, 'sort_order' => 30],
                    ['legacy_code' => '60', 'title' => 'Laminar flow: MN01 Modulo 4B', 'department_code' => 'laminar', 'default_volume_liters' => 1000, 'sort_order' => 40],
                    ['legacy_code' => '303', 'title' => 'Controllo negativo', 'department_code' => 'controlli', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 50],
                ],
            ],
            [
                'section' => [
                    'code' => 'superfici_contact_plates',
                    'name' => 'Reparto Piastre Superfici 1',
                    'description' => 'Campionamento superfici con contact plates.',
                    'sort_order' => 40,
                    'is_active' => true,
                ],
                'departments' => [
                    ['code' => 'superfici', 'name' => 'Superfici', 'sort_order' => 10],
                    ['code' => 'controlli', 'name' => 'Controlli', 'sort_order' => 20],
                ],
                'points' => [
                    ['legacy_code' => '61', 'title' => 'Pavimento vicino alla cappa di caricamento', 'department_code' => 'superfici', 'sample_kind' => 'surface_contact', 'sort_order' => 10],
                    ['legacy_code' => '64', 'title' => 'Tavolo piastre vuote TAV01 (piano superiore)', 'department_code' => 'superfici', 'sample_kind' => 'surface_contact', 'sort_order' => 20],
                    ['legacy_code' => '68', 'title' => 'MP02_CA09 bandella interna', 'department_code' => 'superfici', 'sample_kind' => 'surface_contact', 'sort_order' => 30],
                    ['legacy_code' => '100', 'title' => 'Tavolo piastre vuote TAV02 (piano inferiore)', 'department_code' => 'superfici', 'sample_kind' => 'surface_contact', 'sort_order' => 40],
                    ['legacy_code' => '304', 'title' => 'Controllo negativo', 'department_code' => 'controlli', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 50],
                ],
            ],
            [
                'section' => [
                    'code' => 'superfici_swab',
                    'name' => 'Reparto Piastre Superfici 2',
                    'description' => 'Campionamento superfici con swab.',
                    'sort_order' => 50,
                    'is_active' => true,
                ],
                'departments' => [
                    ['code' => 'superfici', 'name' => 'Superfici', 'sort_order' => 10],
                    ['code' => 'controlli', 'name' => 'Controlli', 'sort_order' => 20],
                ],
                'points' => [
                    ['legacy_code' => '73', 'title' => 'CA09_Nastro_Linea 1', 'department_code' => 'superfici', 'sample_kind' => 'surface_swab', 'requires_product_lot' => false, 'sort_order' => 10],
                    ['legacy_code' => '77', 'title' => 'CA09_Nastro_Linea 5', 'department_code' => 'superfici', 'sample_kind' => 'surface_swab', 'requires_product_lot' => false, 'sort_order' => 20],
                    ['legacy_code' => '81', 'title' => 'MP02_Corde_Linea 3', 'department_code' => 'superfici', 'sample_kind' => 'surface_swab', 'requires_product_lot' => false, 'sort_order' => 30],
                    ['legacy_code' => '85', 'title' => 'MP02_Moduli 7-8-9-10_Nastro', 'department_code' => 'superfici', 'sample_kind' => 'surface_swab', 'requires_product_lot' => false, 'sort_order' => 40],
                    ['legacy_code' => '305', 'title' => 'Controllo negativo', 'department_code' => 'controlli', 'sample_kind' => 'surface_swab', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 50],
                ],
            ],
        ];

        foreach ($sections as $payload) {
            $section = MonitoringSection::query()->updateOrCreate(
                ['code' => $payload['section']['code']],
                $payload['section']
            );

            $departmentMap = [];
            $now = now();

            $departmentRecords = array_map(function (array $department) use ($section, $now) {
                return [
                    'monitoring_section_id' => $section->id,
                    'code' => $department['code'],
                    'name' => $department['name'],
                    'sort_order' => $department['sort_order'],
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }, $payload['departments']);

            MonitoringDepartment::query()->upsert(
                $departmentRecords,
                ['monitoring_section_id', 'name'],
                ['code', 'sort_order', 'is_active', 'updated_at']
            );

            $departmentsByName = MonitoringDepartment::query()
                ->where('monitoring_section_id', $section->id)
                ->whereIn('name', array_column($payload['departments'], 'name'))
                ->get()
                ->keyBy('name');

            foreach ($payload['departments'] as $department) {
                $departmentModel = $departmentsByName->get($department['name']);
                $departmentMap[$department['code']] = $departmentModel?->id;
            }

            foreach ($payload['points'] as $point) {
                $departmentCode = $point['department_code'] ?? null;
                $pointData = $point;
                unset($pointData['department_code']);

                SamplingPoint::query()->updateOrCreate(
                    [
                        'monitoring_section_id' => $section->id,
                        'legacy_code' => $point['legacy_code'],
                    ],
                    array_merge([
                        'monitoring_section_id' => $section->id,
                        'monitoring_department_id' => $departmentCode ? ($departmentMap[$departmentCode] ?? null) : null,
                        'sample_kind' => 'air_active',
                        'default_volume_liters' => null,
                        'requires_operational_status' => true,
                        'requires_product_lot' => true,
                        'is_active' => true,
                    ], $pointData)
                );
            }
        }
    }
}
