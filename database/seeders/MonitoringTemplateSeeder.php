<?php

namespace Database\Seeders;

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
                'points' => [
                    ['legacy_code' => '1', 'title' => 'Laminar flow: Macchina piastre MP02 Modulo 01A', 'default_volume_liters' => 1000, 'sort_order' => 10],
                    ['legacy_code' => '2', 'title' => 'Laminar flow: Macchina piastre MP02 Modulo 01B', 'default_volume_liters' => 1000, 'sort_order' => 20],
                    ['legacy_code' => '17', 'title' => 'Laminar flow: Cappa CA 09', 'default_volume_liters' => 1000, 'sort_order' => 30],
                    ['legacy_code' => '21', 'title' => 'Ambiente: centro locale Piastre', 'default_volume_liters' => 200, 'sort_order' => 40],
                    ['legacy_code' => '300', 'title' => 'Controllo negativo', 'default_volume_liters' => 1000, 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 50],
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
                'points' => [
                    ['legacy_code' => '22', 'title' => 'Laminar flow: Macchina slide MS01 Modulo 1', 'area_label' => '7 Slide', 'default_volume_liters' => 1000, 'sort_order' => 10],
                    ['legacy_code' => '32', 'title' => 'Ambiente: centro locale Slide', 'area_label' => '7 Slide', 'default_volume_liters' => 200, 'sort_order' => 20],
                    ['legacy_code' => '49', 'title' => 'Laminar flow: Cappa CO 01', 'area_label' => 'Antibiotici/MIC impreg.', 'default_volume_liters' => 1000, 'sort_order' => 30],
                    ['legacy_code' => '87', 'title' => 'Ambiente: Antibiotici fustellatura/confez.', 'area_label' => 'Antibiotici', 'default_volume_liters' => 200, 'sort_order' => 40],
                    ['legacy_code' => '301', 'title' => 'Controllo negativo', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 50],
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
                'points' => [
                    ['legacy_code' => '52', 'title' => 'Ambiente: centro locale Enteropluritest', 'default_volume_liters' => 200, 'sort_order' => 10],
                    ['legacy_code' => '53', 'title' => 'Laminar flow: MN01 Modulo 1A', 'default_volume_liters' => 1000, 'sort_order' => 20],
                    ['legacy_code' => '54', 'title' => 'Laminar flow: MN01 Modulo 2A', 'default_volume_liters' => 1000, 'sort_order' => 30],
                    ['legacy_code' => '60', 'title' => 'Laminar flow: MN01 Modulo 4B', 'default_volume_liters' => 1000, 'sort_order' => 40],
                    ['legacy_code' => '303', 'title' => 'Controllo negativo', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 50],
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
                'points' => [
                    ['legacy_code' => '61', 'title' => 'Pavimento vicino alla cappa di caricamento', 'sample_kind' => 'surface_contact', 'sort_order' => 10],
                    ['legacy_code' => '64', 'title' => 'Tavolo piastre vuote TAV01 (piano superiore)', 'sample_kind' => 'surface_contact', 'sort_order' => 20],
                    ['legacy_code' => '68', 'title' => 'MP02_CA09 bandella interna', 'sample_kind' => 'surface_contact', 'sort_order' => 30],
                    ['legacy_code' => '100', 'title' => 'Tavolo piastre vuote TAV02 (piano inferiore)', 'sample_kind' => 'surface_contact', 'sort_order' => 40],
                    ['legacy_code' => '304', 'title' => 'Controllo negativo', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 50],
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
                'points' => [
                    ['legacy_code' => '73', 'title' => 'CA09_Nastro_Linea 1', 'sample_kind' => 'surface_swab', 'requires_product_lot' => false, 'sort_order' => 10],
                    ['legacy_code' => '77', 'title' => 'CA09_Nastro_Linea 5', 'sample_kind' => 'surface_swab', 'requires_product_lot' => false, 'sort_order' => 20],
                    ['legacy_code' => '81', 'title' => 'MP02_Corde_Linea 3', 'sample_kind' => 'surface_swab', 'requires_product_lot' => false, 'sort_order' => 30],
                    ['legacy_code' => '85', 'title' => 'MP02_Moduli 7-8-9-10_Nastro', 'sample_kind' => 'surface_swab', 'requires_product_lot' => false, 'sort_order' => 40],
                    ['legacy_code' => '305', 'title' => 'Controllo negativo', 'sample_kind' => 'surface_swab', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 50],
                ],
            ],
        ];

        foreach ($sections as $payload) {
            $section = MonitoringSection::query()->updateOrCreate(
                ['code' => $payload['section']['code']],
                $payload['section']
            );

            foreach ($payload['points'] as $point) {
                SamplingPoint::query()->updateOrCreate(
                    [
                        'monitoring_section_id' => $section->id,
                        'legacy_code' => $point['legacy_code'],
                    ],
                    array_merge([
                        'monitoring_section_id' => $section->id,
                        'sample_kind' => 'air_active',
                        'default_volume_liters' => null,
                        'requires_operational_status' => true,
                        'requires_product_lot' => true,
                        'is_active' => true,
                    ], $point)
                );
            }
        }
    }
}
