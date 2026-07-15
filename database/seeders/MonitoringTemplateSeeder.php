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
        $waterDepartments = [
            ['code' => 'punti_uso', 'name' => 'Punti uso', 'sort_order' => 10],
        ];

        $waterSections = [
            [
                'section' => [
                    'code' => 'acque_sede_1',
                    'environment' => 'acque',
                    'sub_environment' => 's1',
                    'name' => 'Acque - Sede 1',
                    'description' => 'Punti acqua legacy della Sede 1. Nel modello attuale la sezione copre il tracciamento puntuale dei campioni acqua.',
                    'sort_order' => 120,
                    'is_active' => true,
                ],
                'departments' => $waterDepartments,
                'points' => [
                    ['legacy_code' => 'D-1', 'title' => 'D-1_Drinking Water', 'department_code' => 'punti_uso', 'sample_kind' => 'water', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 10],
                    ['legacy_code' => 'P-2', 'title' => 'P-2_Purified Water a valle dell\'impianto di Osmosi Inversa', 'department_code' => 'punti_uso', 'sample_kind' => 'water', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 20],
                    ['legacy_code' => 'P-3', 'title' => 'P-3_Purified Water in uscita dal serbatoio', 'department_code' => 'punti_uso', 'sample_kind' => 'water', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 30],
                    ['legacy_code' => 'P-4', 'title' => 'P-4_Purified Water a valle del trattamento con resine a Scambio Ionico', 'department_code' => 'punti_uso', 'sample_kind' => 'water', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 40],
                    ['legacy_code' => 'P-5', 'title' => 'P-5: Purified Water locale n.22 "Autoclavi/Preparatori"', 'department_code' => 'punti_uso', 'sample_kind' => 'water', 'area_label' => 'Sede 1', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 50],
                    ['legacy_code' => 'P-6', 'title' => 'P-6: Purified Water locale n.24 "Provette e Flaconi"', 'department_code' => 'punti_uso', 'sample_kind' => 'water', 'area_label' => 'Sede 1', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 60],
                    ['legacy_code' => 'P-7', 'title' => 'P-7: Purified Water locale n.25 "Autoclavi"', 'department_code' => 'punti_uso', 'sample_kind' => 'water', 'area_label' => 'Sede 1', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 70],
                    ['legacy_code' => 'P-8', 'title' => 'P-8: Purified Water locale n.19 "Bioindicatori/Fiale"', 'department_code' => 'punti_uso', 'sample_kind' => 'water', 'area_label' => 'Sede 1', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 80],
                ],
            ],
            [
                'section' => [
                    'code' => 'acque_sede_2',
                    'environment' => 'acque',
                    'sub_environment' => 's2',
                    'name' => 'Acque - Sede 2',
                    'description' => 'Punti acqua legacy della Sede 2. Nel modello attuale la sezione copre il tracciamento puntuale dei campioni acqua.',
                    'sort_order' => 130,
                    'is_active' => true,
                ],
                'departments' => $waterDepartments,
                'points' => [
                    ['legacy_code' => 'D-1', 'title' => 'D-1_Drinking Water', 'department_code' => 'punti_uso', 'sample_kind' => 'water', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 10],
                    ['legacy_code' => 'P-2a', 'title' => 'P-2a_Purified Water a valle dell\'impianto di Osmosi Inversa', 'department_code' => 'punti_uso', 'sample_kind' => 'water', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 20],
                    ['legacy_code' => 'P-3', 'title' => 'P-3: Purified Water in uscita dal serbatoio e a monte dell\'impianto di Osmosi Inversa', 'department_code' => 'punti_uso', 'sample_kind' => 'water', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 30],
                    ['legacy_code' => 'P-4', 'title' => 'P-4_Purified Water a valle del trattamento con resine a Scambio Ionico', 'department_code' => 'punti_uso', 'sample_kind' => 'water', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 40],
                    ['legacy_code' => 'P-5', 'title' => 'P-5: Purified Water locale n.10 "Autoclavi", building S2', 'department_code' => 'punti_uso', 'sample_kind' => 'water', 'area_label' => 'Building S2', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 50],
                    ['legacy_code' => 'P-6', 'title' => 'P-6_Purified Water Locale n.32 "Laboratorio Chimico"', 'department_code' => 'punti_uso', 'sample_kind' => 'water', 'area_label' => 'Building S2', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 60],
                    ['legacy_code' => 'P-7', 'title' => 'P-7: Purified Water locale "Laboratorio Controllo Qualita", building S2', 'department_code' => 'punti_uso', 'sample_kind' => 'water', 'area_label' => 'Building S2', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 70],
                    ['legacy_code' => 'P-8', 'title' => 'P-8: Purified Water locale n.27 "Prodotti Speciali", building S2', 'department_code' => 'punti_uso', 'sample_kind' => 'water', 'area_label' => 'Building S2', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 80],
                    ['legacy_code' => 'P-9', 'title' => 'P-9: Purified Water locale n.11 "Laboratorio MTS", building S3', 'department_code' => 'punti_uso', 'sample_kind' => 'water', 'area_label' => 'Building S3', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 90],
                    ['legacy_code' => 'P-10', 'title' => 'P-10: Purified Water locale n.34 Laboratorio Sistemi, building S2', 'department_code' => 'punti_uso', 'sample_kind' => 'water', 'area_label' => 'Building S2', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 100],
                ],
            ],
        ];

        $operatorDepartments = [
            ['code' => 'operatori', 'name' => 'Operatori', 'sort_order' => 10],
            ['code' => 'controlli', 'name' => 'Controlli', 'sort_order' => 20],
        ];

        $operatorPoints = [
            ['legacy_code' => '501', 'title' => 'Mano destra', 'department_code' => 'operatori', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 10],
            ['legacy_code' => '502', 'title' => 'Mano sinistra', 'department_code' => 'operatori', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 20],
            ['legacy_code' => '503', 'title' => 'Mascherina', 'department_code' => 'operatori', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 30],
            ['legacy_code' => '504', 'title' => 'Petto', 'department_code' => 'operatori', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 40],
            ['legacy_code' => '505', 'title' => 'Avambraccio destro', 'department_code' => 'operatori', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 50],
            ['legacy_code' => '506', 'title' => 'Avambraccio sinistro', 'department_code' => 'operatori', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 60],
            ['legacy_code' => '507', 'title' => 'Ginocchio destro', 'department_code' => 'operatori', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 70],
            ['legacy_code' => '508', 'title' => 'Ginocchio sinistro', 'department_code' => 'operatori', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 80],
            ['legacy_code' => '509', 'title' => 'Controllo negativo', 'department_code' => 'controlli', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 90],
        ];

        $operatorSections = [
            ['code' => 'operatori_enteropluri_test', 'name' => 'Operatori - Enteropluri-Test', 'description' => 'Campionamenti microbiologici su operatori del reparto Enteropluri-Test.'],
            ['code' => 'operatori_slide', 'name' => 'Operatori - Slide', 'description' => 'Campionamenti microbiologici su operatori del reparto Slide.'],
            ['code' => 'operatori_piastre', 'name' => 'Operatori - Piastre', 'description' => 'Campionamenti microbiologici su operatori del reparto Piastre.'],
            ['code' => 'operatori_clean_room_s1', 'name' => 'Operatori - Clean Room S1', 'description' => 'Campionamenti microbiologici su operatori della Clean Room S1.'],
            ['code' => 'operatori_clean_room_s2', 'name' => 'Operatori - Clean Room S2', 'description' => 'Campionamenti microbiologici su operatori della Clean Room S2.'],
            ['code' => 'operatori_clean_room_s3', 'name' => 'Operatori - Clean Room S3', 'description' => 'Campionamenti microbiologici su operatori della Clean Room S3.'],
            ['code' => 'operatori_manutenzione', 'name' => 'Operatori - Manutenzione', 'description' => 'Campionamenti microbiologici su operatori del reparto Manutenzione.'],
            ['code' => 'operatori_mic_test_strip', 'name' => 'Operatori - MIC Test Strip', 'description' => 'Campionamenti microbiologici su operatori del reparto MIC Test Strip.'],
        ];

        $sections = [
            [
                'section' => [
                    'code' => 'reparto_piastre',
                    'environment' => 'produzione',
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
                    ['legacy_code' => '3', 'title' => 'Laminar flow: Macchina piastre MP02 Modulo 02A', 'department_code' => 'laminar', 'default_volume_liters' => 1000, 'sort_order' => 30],
                    ['legacy_code' => '4', 'title' => 'Laminar flow: Macchina piastre MP02 Modulo 02B', 'department_code' => 'laminar', 'default_volume_liters' => 1000, 'sort_order' => 40],
                    ['legacy_code' => '5', 'title' => 'Laminar flow: Macchina piastre MP02 Modulo 03A', 'department_code' => 'laminar', 'default_volume_liters' => 1000, 'sort_order' => 50],
                    ['legacy_code' => '6', 'title' => 'Laminar flow: Macchina piastre MP02 Modulo 03B', 'department_code' => 'laminar', 'default_volume_liters' => 1000, 'sort_order' => 60],
                    ['legacy_code' => '7', 'title' => 'Laminar flow: Macchina piastre MP02 Modulo 04A', 'department_code' => 'laminar', 'default_volume_liters' => 1000, 'sort_order' => 70],
                    ['legacy_code' => '8', 'title' => 'Laminar flow: Macchina piastre MP02 Modulo 04B', 'department_code' => 'laminar', 'default_volume_liters' => 1000, 'sort_order' => 80],
                    ['legacy_code' => '9', 'title' => 'Laminar flow: Macchina piastre MP02 Modulo 05A', 'department_code' => 'laminar', 'default_volume_liters' => 1000, 'sort_order' => 90],
                    ['legacy_code' => '10', 'title' => 'Laminar flow: Macchina piastre MP02 Modulo 05B', 'department_code' => 'laminar', 'default_volume_liters' => 1000, 'sort_order' => 100],
                    ['legacy_code' => '11', 'title' => 'Laminar flow: Macchina piastre MP02 Modulo 06A', 'department_code' => 'laminar', 'default_volume_liters' => 1000, 'sort_order' => 110],
                    ['legacy_code' => '12', 'title' => 'Laminar flow: Macchina piastre MP02 Modulo 06B', 'department_code' => 'laminar', 'default_volume_liters' => 1000, 'sort_order' => 120],
                    ['legacy_code' => '13', 'title' => 'Laminar flow: Macchina piastre MP02 Modulo 07', 'department_code' => 'laminar', 'default_volume_liters' => 1000, 'sort_order' => 130],
                    ['legacy_code' => '14', 'title' => 'Laminar flow: Macchina piastre MP02 Modulo 08', 'department_code' => 'laminar', 'default_volume_liters' => 1000, 'sort_order' => 140],
                    ['legacy_code' => '15', 'title' => 'Laminar flow: Macchina piastre MP02 Modulo 09', 'department_code' => 'laminar', 'default_volume_liters' => 1000, 'sort_order' => 150],
                    ['legacy_code' => '16', 'title' => 'Laminar flow: Macchina piastre MP02 Modulo 10', 'department_code' => 'laminar', 'default_volume_liters' => 1000, 'sort_order' => 160],
                    ['legacy_code' => '17', 'title' => 'Laminar flow: Cappa CA 09', 'department_code' => 'laminar', 'default_volume_liters' => 1000, 'sort_order' => 170],
                    ['legacy_code' => '18', 'title' => 'Laminar flow: Cappa dinamica CD01', 'department_code' => 'laminar', 'default_volume_liters' => 1000, 'sort_order' => 180],
                    ['legacy_code' => '20', 'title' => 'Laminar flow: Cappa CA 14', 'department_code' => 'laminar', 'default_volume_liters' => 1000, 'sort_order' => 190],
                    ['legacy_code' => '21', 'title' => 'Ambiente: centro locale Piastre', 'department_code' => 'ambiente', 'default_volume_liters' => 200, 'sort_order' => 200],
                    ['legacy_code' => '96', 'title' => 'Laminar flow: Cappa CA 16 Impacchettatrice IR03', 'department_code' => 'laminar', 'default_volume_liters' => 1000, 'sort_order' => 210],
                    ['legacy_code' => '97', 'title' => 'Laminar flow: Cappa dinamica CD02', 'department_code' => 'laminar', 'default_volume_liters' => 1000, 'sort_order' => 220],
                    ['legacy_code' => '98', 'title' => 'Laminar flow: Cappa CA 17 Impacchettatrice IR04', 'department_code' => 'laminar', 'default_volume_liters' => 1000, 'sort_order' => 230],
                    ['legacy_code' => '102', 'title' => 'Laminar flow: Cappa CA 19 Cappa Autoclavi', 'department_code' => 'laminar', 'default_volume_liters' => 1000, 'sort_order' => 240],
                    ['legacy_code' => '300', 'title' => 'Controllo negativo', 'department_code' => 'controlli', 'default_volume_liters' => 1000, 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 250],
                ],
            ],
            [
                'section' => [
                    'code' => 'reparti_vari',
                    'environment' => 'produzione',
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
                    ['legacy_code' => '23', 'title' => 'Laminar flow: Macchina slide MS01 Modulo 2', 'department_code' => 'laminar', 'area_label' => '7 Slide', 'default_volume_liters' => 1000, 'sort_order' => 20],
                    ['legacy_code' => '24', 'title' => 'Laminar flow: Macchina slide MS03 Modulo 1A', 'department_code' => 'laminar', 'area_label' => '7 Slide', 'default_volume_liters' => 1000, 'sort_order' => 30],
                    ['legacy_code' => '25', 'title' => 'Laminar flow: Macchina slide MS03 Modulo 2A', 'department_code' => 'laminar', 'area_label' => '7 Slide', 'default_volume_liters' => 1000, 'sort_order' => 40],
                    ['legacy_code' => '26', 'title' => 'Laminar flow: Macchina slide MS03 Modulo 3A', 'department_code' => 'laminar', 'area_label' => '7 Slide', 'default_volume_liters' => 1000, 'sort_order' => 50],
                    ['legacy_code' => '27', 'title' => 'Laminar flow: Macchina slide MS03 Modulo 4A', 'department_code' => 'laminar', 'area_label' => '7 Slide', 'default_volume_liters' => 1000, 'sort_order' => 60],
                    ['legacy_code' => '28', 'title' => 'Laminar flow: Macchina slide MS03 Modulo 1B', 'department_code' => 'laminar', 'area_label' => '7 Slide', 'default_volume_liters' => 1000, 'sort_order' => 70],
                    ['legacy_code' => '29', 'title' => 'Laminar flow: Macchina slide MS03 Modulo 2B', 'department_code' => 'laminar', 'area_label' => '7 Slide', 'default_volume_liters' => 1000, 'sort_order' => 80],
                    ['legacy_code' => '30', 'title' => 'Laminar flow: Macchina slide MS03 Modulo 3B', 'department_code' => 'laminar', 'area_label' => '7 Slide', 'default_volume_liters' => 1000, 'sort_order' => 90],
                    ['legacy_code' => '31', 'title' => 'Laminar flow: Macchina slide MS03 Modulo 4B', 'department_code' => 'laminar', 'area_label' => '7 Slide', 'default_volume_liters' => 1000, 'sort_order' => 100],
                    ['legacy_code' => '32', 'title' => 'Ambiente: centro locale Slide', 'department_code' => 'ambiente', 'area_label' => '7 Slide', 'default_volume_liters' => 200, 'sort_order' => 110],
                    ['legacy_code' => '33', 'title' => 'Laminar flow: Cappa CA 02', 'department_code' => 'laminar', 'area_label' => '6 Enteropluritest', 'default_volume_liters' => 1000, 'sort_order' => 120],
                    ['legacy_code' => '36', 'title' => 'Laminar flow: Cappa CB 06 (Reparto Liofilizzazione)', 'department_code' => 'laminar', 'area_label' => '20 Liofilizzazione', 'default_volume_liters' => 1000, 'sort_order' => 130],
                    ['legacy_code' => '49', 'title' => 'Laminar flow: Cappa CO 01 (Reparto antibiotici/MIC impregnazione)', 'department_code' => 'laminar', 'area_label' => '30 Antibiotici/MIC impregnazione', 'default_volume_liters' => 1000, 'sort_order' => 140],
                    ['legacy_code' => '50', 'title' => 'Laminar flow: Cappa CO 02 (Reparto antibiotici/MIC impregnazione)', 'department_code' => 'laminar', 'area_label' => '30 Antibiotici/MIC impregnazione', 'default_volume_liters' => 1000, 'sort_order' => 150],
                    ['legacy_code' => '87', 'title' => 'Ambiente: centro locale Antibiotici fustellatura / confezionamento', 'department_code' => 'ambiente', 'area_label' => '29 Antibiotici Fustellatura / Confezionamento', 'default_volume_liters' => 200, 'sort_order' => 160],
                    ['legacy_code' => '88', 'title' => 'Ambiente: centro locale Antibiotici/MIC impregnazione', 'department_code' => 'ambiente', 'area_label' => '30 Antibiotici/MIC impregnazione', 'default_volume_liters' => 200, 'sort_order' => 170],
                    ['legacy_code' => '122', 'title' => 'Laminar flow: Cappa CA12 (Macchina Impregnatrice Easy Dry IE01)', 'department_code' => 'laminar', 'area_label' => '32 Laboratorio', 'default_volume_liters' => 1000, 'sort_order' => 180],
                    ['legacy_code' => '123', 'title' => 'Laminar flow: Cappa CB 12 (Lab. Chimico)', 'department_code' => 'laminar', 'area_label' => '32 Laboratorio', 'default_volume_liters' => 1000, 'sort_order' => 190],
                    ['legacy_code' => '124', 'title' => 'Laminar flow: Cappa CB 13 (Lab. Chimico)', 'department_code' => 'laminar', 'area_label' => '32 Laboratorio', 'default_volume_liters' => 1000, 'sort_order' => 200],
                    ['legacy_code' => '125', 'title' => 'Laminar flow: Cappa CB 14 (Lab. Chimico)', 'department_code' => 'laminar', 'area_label' => '32 Laboratorio', 'default_volume_liters' => 1000, 'sort_order' => 210],
                    ['legacy_code' => '126', 'title' => 'Laminar flow: Macchina slide MS04 Modulo 1A', 'department_code' => 'laminar', 'area_label' => '7 Slide', 'default_volume_liters' => 1000, 'sort_order' => 220],
                    ['legacy_code' => '127', 'title' => 'Laminar flow: Macchina slide MS04 Modulo 2A', 'department_code' => 'laminar', 'area_label' => '7 Slide', 'default_volume_liters' => 1000, 'sort_order' => 230],
                    ['legacy_code' => '128', 'title' => 'Laminar flow: Macchina slide MS04 Modulo 3A', 'department_code' => 'laminar', 'area_label' => '7 Slide', 'default_volume_liters' => 1000, 'sort_order' => 240],
                    ['legacy_code' => '129', 'title' => 'Laminar flow: Macchina slide MS04 Modulo 4A', 'department_code' => 'laminar', 'area_label' => '7 Slide', 'default_volume_liters' => 1000, 'sort_order' => 250],
                    ['legacy_code' => '130', 'title' => 'Laminar flow: Macchina slide MS04 Modulo 1B', 'department_code' => 'laminar', 'area_label' => '7 Slide', 'default_volume_liters' => 1000, 'sort_order' => 260],
                    ['legacy_code' => '131', 'title' => 'Laminar flow: Macchina slide MS04 Modulo 2B', 'department_code' => 'laminar', 'area_label' => '7 Slide', 'default_volume_liters' => 1000, 'sort_order' => 270],
                    ['legacy_code' => '132', 'title' => 'Laminar flow: Macchina slide MS04 Modulo 3B', 'department_code' => 'laminar', 'area_label' => '7 Slide', 'default_volume_liters' => 1000, 'sort_order' => 280],
                    ['legacy_code' => '133', 'title' => 'Laminar flow: Macchina slide MS04 Modulo 4B', 'department_code' => 'laminar', 'area_label' => '7 Slide', 'default_volume_liters' => 1000, 'sort_order' => 290],
                    ['legacy_code' => '134', 'title' => 'Laminar flow: Cappa CB 15 (Lab. Sistemi)', 'department_code' => 'laminar', 'area_label' => '34 Laboratorio Sistemi', 'default_volume_liters' => 1000, 'sort_order' => 300],
                    ['legacy_code' => '153', 'title' => 'Laminar flow: Cappa CA 23 (Lab. Sistemi)', 'department_code' => 'laminar', 'area_label' => '34 Laboratorio Sistemi', 'default_volume_liters' => 1000, 'sort_order' => 310],
                    ['legacy_code' => '301', 'title' => 'Controllo negativo', 'department_code' => 'controlli', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 320],
                ],
            ],
            [
                'section' => [
                    'code' => 'reparto_provette_flaconi',
                    'environment' => 'produzione',
                    'name' => 'Reparto Provette e Flaconi',
                    'description' => 'Monitoraggio microbiologico reparto provette e flaconi: campionamento aria attivo.',
                    'sort_order' => 30,
                    'is_active' => true,
                ],
                'departments' => [
                    ['code' => 'laminar', 'name' => 'Laminar flow', 'sort_order' => 10],
                    ['code' => 'ambiente', 'name' => 'Ambiente', 'sort_order' => 20],
                    ['code' => 'controlli', 'name' => 'Controlli', 'sort_order' => 30],
                ],
                'points' => [
                    ['legacy_code' => '44', 'title' => 'Laminar flow: Cappa CA 01', 'department_code' => 'laminar', 'area_label' => 'S2 - Laboratorio Controllo Qualita', 'default_volume_liters' => 1000, 'sort_order' => 10],
                    ['legacy_code' => '45', 'title' => 'Laminar flow: Cappa CA 03', 'department_code' => 'laminar', 'area_label' => 'S2 - Laboratorio Controllo Qualita', 'default_volume_liters' => 1000, 'sort_order' => 20],
                    ['legacy_code' => '48', 'title' => 'Ambiente: centro locale Provette', 'department_code' => 'ambiente', 'area_label' => 'S2 - Laboratorio Controllo Qualita', 'default_volume_liters' => 200, 'sort_order' => 30],
                    ['legacy_code' => '149', 'title' => 'Laminar flow: Cappa CA 22 Modulo 1', 'department_code' => 'laminar', 'area_label' => '33 - Camera pulita provette', 'default_volume_liters' => 1000, 'sort_order' => 40],
                    ['legacy_code' => '150', 'title' => 'Laminar flow: Cappa CA 22 Modulo 2', 'department_code' => 'laminar', 'area_label' => '33 - Camera pulita provette', 'default_volume_liters' => 1000, 'sort_order' => 50],
                    ['legacy_code' => '151', 'title' => 'Ambiente: centro locale Camera pulita provette', 'department_code' => 'ambiente', 'area_label' => '33 - Camera pulita provette', 'default_volume_liters' => 200, 'sort_order' => 60],
                    ['legacy_code' => '152', 'title' => 'Laminar flow: Cappa CB 21', 'department_code' => 'laminar', 'area_label' => 'Provette', 'default_volume_liters' => 1000, 'sort_order' => 70],
                    ['legacy_code' => '302', 'title' => 'Controllo negativo', 'department_code' => 'controlli', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 80],
                ],
            ],
            [
                'section' => [
                    'code' => 'reparto_enteropluritest',
                    'environment' => 'produzione',
                    'name' => 'Reparto Enteropluritest',
                    'description' => 'Monitoraggio microbiologico reparto Enteropluritest.',
                    'sort_order' => 40,
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
                    ['legacy_code' => '55', 'title' => 'Laminar flow: MN01 Modulo 3A', 'department_code' => 'laminar', 'default_volume_liters' => 1000, 'sort_order' => 40],
                    ['legacy_code' => '56', 'title' => 'Laminar flow: MN01 Modulo 4A', 'department_code' => 'laminar', 'default_volume_liters' => 1000, 'sort_order' => 50],
                    ['legacy_code' => '57', 'title' => 'Laminar flow: MN01 Modulo 1B', 'department_code' => 'laminar', 'default_volume_liters' => 1000, 'sort_order' => 60],
                    ['legacy_code' => '58', 'title' => 'Laminar flow: MN01 Modulo 2B', 'department_code' => 'laminar', 'default_volume_liters' => 1000, 'sort_order' => 70],
                    ['legacy_code' => '59', 'title' => 'Laminar flow: MN01 Modulo 3B', 'department_code' => 'laminar', 'default_volume_liters' => 1000, 'sort_order' => 80],
                    ['legacy_code' => '60', 'title' => 'Laminar flow: MN01 Modulo 4B', 'department_code' => 'laminar', 'default_volume_liters' => 1000, 'sort_order' => 90],
                    ['legacy_code' => '303', 'title' => 'Controllo negativo', 'department_code' => 'controlli', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 100],
                ],
            ],
            [
                'section' => [
                    'code' => 'superfici_contact_plates',
                    'environment' => 'produzione',
                    'name' => 'Reparto Piastre Superfici 1',
                    'description' => 'Campionamento superfici con contact plates.',
                    'sort_order' => 50,
                    'is_active' => true,
                ],
                'departments' => [
                    ['code' => 'ambiente', 'name' => 'Ambiente', 'sort_order' => 10],
                    ['code' => 'superfici', 'name' => 'Superfici', 'sort_order' => 20],
                    ['code' => 'controlli', 'name' => 'Controlli', 'sort_order' => 30],
                ],
                'points' => [
                    ['legacy_code' => '61', 'title' => 'Ambiente: Pavimento vicino alla cappa di caricamento', 'department_code' => 'ambiente', 'sample_kind' => 'surface_contact', 'sort_order' => 10],
                    ['legacy_code' => '62', 'title' => 'Ambiente: Pavimento al centro della stanza (tra IR03-IR04)', 'department_code' => 'ambiente', 'sample_kind' => 'surface_contact', 'sort_order' => 20],
                    ['legacy_code' => '63', 'title' => 'Ambiente: Pavimento vicino all\'ultimo modulo', 'department_code' => 'ambiente', 'sample_kind' => 'surface_contact', 'sort_order' => 30],
                    ['legacy_code' => '64', 'title' => 'Superfici: Tavolo per piastre vuote TAV01 (piano superiore)', 'department_code' => 'superfici', 'sample_kind' => 'surface_contact', 'sort_order' => 40],
                    ['legacy_code' => '65', 'title' => 'Superfici: Tavolo per piastre vuote TAV01 (piano inferiore)', 'department_code' => 'superfici', 'sample_kind' => 'surface_contact', 'sort_order' => 50],
                    ['legacy_code' => '67', 'title' => 'Superfici: MP02_CA09 piano di caricamento', 'department_code' => 'superfici', 'sample_kind' => 'surface_contact', 'sort_order' => 60],
                    ['legacy_code' => '68', 'title' => 'Superfici: MP02_CA09 bandella interna', 'department_code' => 'superfici', 'sample_kind' => 'surface_contact', 'sort_order' => 70],
                    ['legacy_code' => '69', 'title' => 'Superfici: MP02_CA09 bandella esterna', 'department_code' => 'superfici', 'sample_kind' => 'surface_contact', 'sort_order' => 80],
                    ['legacy_code' => '70', 'title' => 'Superfici: MP02_Modulo 01A finestra lato interno', 'department_code' => 'superfici', 'sample_kind' => 'surface_contact', 'sort_order' => 90],
                    ['legacy_code' => '71', 'title' => 'Superfici: MP02_Modulo 06A finestra lato interno', 'department_code' => 'superfici', 'sample_kind' => 'surface_contact', 'sort_order' => 100],
                    ['legacy_code' => '72', 'title' => 'Superfici: MP02_Modulo 08 bandella interna', 'department_code' => 'superfici', 'sample_kind' => 'surface_contact', 'sort_order' => 110],
                    ['legacy_code' => '99', 'title' => 'Superfici: Tavolo con piastre vuote TAV02 (piano superiore)', 'department_code' => 'superfici', 'sample_kind' => 'surface_contact', 'sort_order' => 120],
                    ['legacy_code' => '100', 'title' => 'Superfici: Tavolo con piastre vuote TAV02 (piano inferiore)', 'department_code' => 'superfici', 'sample_kind' => 'surface_contact', 'sort_order' => 130],
                    ['legacy_code' => '304', 'title' => 'Controllo negativo', 'department_code' => 'controlli', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 140],
                ],
            ],
            [
                'section' => [
                    'code' => 'superfici_swab',
                    'environment' => 'produzione',
                    'name' => 'Reparto Piastre Superfici 2',
                    'description' => 'Campionamento superfici con swab.',
                    'sort_order' => 60,
                    'is_active' => true,
                ],
                'departments' => [
                    ['code' => 'superfici', 'name' => 'Superfici', 'sort_order' => 10],
                    ['code' => 'controlli', 'name' => 'Controlli', 'sort_order' => 20],
                ],
                'points' => [
                    ['legacy_code' => '73', 'title' => 'CA09_Nastro_Linea 1', 'department_code' => 'superfici', 'sample_kind' => 'surface_swab', 'sort_order' => 10],
                    ['legacy_code' => '74', 'title' => 'CA09_Nastro_Linea 2', 'department_code' => 'superfici', 'sample_kind' => 'surface_swab', 'sort_order' => 20],
                    ['legacy_code' => '75', 'title' => 'CA09_Nastro_Linea 3', 'department_code' => 'superfici', 'sample_kind' => 'surface_swab', 'sort_order' => 30],
                    ['legacy_code' => '76', 'title' => 'CA09_Nastro_Linea 4', 'department_code' => 'superfici', 'sample_kind' => 'surface_swab', 'sort_order' => 40],
                    ['legacy_code' => '77', 'title' => 'CA09_Nastro_Linea 5', 'department_code' => 'superfici', 'sample_kind' => 'surface_swab', 'sort_order' => 50],
                    ['legacy_code' => '78', 'title' => 'CA09_Nastro_Linea 6', 'department_code' => 'superfici', 'sample_kind' => 'surface_swab', 'sort_order' => 60],
                    ['legacy_code' => '79', 'title' => 'MP02_Corde_Linea 1', 'department_code' => 'superfici', 'sample_kind' => 'surface_swab', 'sort_order' => 70],
                    ['legacy_code' => '80', 'title' => 'MP02_Corde_Linea 2', 'department_code' => 'superfici', 'sample_kind' => 'surface_swab', 'sort_order' => 80],
                    ['legacy_code' => '81', 'title' => 'MP02_Corde_Linea 3', 'department_code' => 'superfici', 'sample_kind' => 'surface_swab', 'sort_order' => 90],
                    ['legacy_code' => '82', 'title' => 'MP02_Corde_Linea 4', 'department_code' => 'superfici', 'sample_kind' => 'surface_swab', 'sort_order' => 100],
                    ['legacy_code' => '83', 'title' => 'MP02_Corde_Linea 5', 'department_code' => 'superfici', 'sample_kind' => 'surface_swab', 'sort_order' => 110],
                    ['legacy_code' => '84', 'title' => 'MP02_Corde_Linea 6', 'department_code' => 'superfici', 'sample_kind' => 'surface_swab', 'sort_order' => 120],
                    ['legacy_code' => '85', 'title' => 'MP02_Moduli 7-8-9-10_Nastro', 'department_code' => 'superfici', 'sample_kind' => 'surface_swab', 'sort_order' => 130],
                    ['legacy_code' => '305', 'title' => 'Controllo negativo', 'department_code' => 'controlli', 'sample_kind' => 'surface_swab', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 140],
                ],
            ],
            [
                'section' => [
                    'code' => 'reparto_cq_bioindicatori_1',
                    'environment' => 'produzione',
                    'name' => 'Reparto CQ e Bioindicatori 1',
                    'description' => 'Monitoraggio microbiologico CQ e bioindicatori: aria attiva e superfici.',
                    'sort_order' => 70,
                    'is_active' => true,
                ],
                'departments' => [
                    ['code' => 'laminar', 'name' => 'Laminar flow', 'sort_order' => 10],
                    ['code' => 'ambiente', 'name' => 'Ambiente', 'sort_order' => 20],
                    ['code' => 'superfici', 'name' => 'Superfici', 'sort_order' => 30],
                    ['code' => 'controlli', 'name' => 'Controlli', 'sort_order' => 40],
                ],
                'points' => [
                    ['legacy_code' => '34', 'title' => 'Laminar flow: Cappa CB 05 (Reparto Bioindicatori/Fiale)', 'department_code' => 'laminar', 'area_label' => 'S1-19 Reparto Bioindicatori/Fiale', 'default_volume_liters' => 1000, 'sort_order' => 10],
                    ['legacy_code' => '35', 'title' => 'Laminar flow: Cappa CB 07 (Reparto Prodotti Speciali)', 'department_code' => 'laminar', 'area_label' => 'S2-27 Reparto Prodotti Speciali', 'default_volume_liters' => 1000, 'sort_order' => 20],
                    ['legacy_code' => '86', 'title' => 'Ambiente: centro locale Prodotti Speciali', 'department_code' => 'ambiente', 'area_label' => 'S2-27 Reparto Prodotti Speciali', 'default_volume_liters' => 200, 'sort_order' => 30],
                    ['legacy_code' => '92', 'title' => 'Ambiente: centro locale Bioindicatori/Fiale', 'department_code' => 'ambiente', 'area_label' => 'S1-19 Reparto Bioindicatori/Fiale', 'default_volume_liters' => 200, 'sort_order' => 40],
                    ['legacy_code' => '108', 'title' => 'Superfici: CB 05 piano di lavoro sx', 'department_code' => 'superfici', 'area_label' => 'S1-19 Reparto Bioindicatori/Fiale', 'sample_kind' => 'surface_contact', 'sort_order' => 50],
                    ['legacy_code' => '109', 'title' => 'Superfici: CB 05 piano di lavoro dx', 'department_code' => 'superfici', 'area_label' => 'S1-19 Reparto Bioindicatori/Fiale', 'sample_kind' => 'surface_contact', 'sort_order' => 60],
                    ['legacy_code' => '110', 'title' => 'Superfici: CB 05 parete frontale', 'department_code' => 'superfici', 'area_label' => 'S1-19 Reparto Bioindicatori/Fiale', 'sample_kind' => 'surface_contact', 'sort_order' => 70],
                    ['legacy_code' => '111', 'title' => 'Superfici: CB 07 piano di lavoro sx', 'department_code' => 'superfici', 'area_label' => 'S2-27 Reparto Prodotti Speciali', 'sample_kind' => 'surface_contact', 'sort_order' => 80],
                    ['legacy_code' => '112', 'title' => 'Superfici: CB 07 piano di lavoro dx', 'department_code' => 'superfici', 'area_label' => 'S2-27 Reparto Prodotti Speciali', 'sample_kind' => 'surface_contact', 'sort_order' => 90],
                    ['legacy_code' => '113', 'title' => 'Superfici: CB 07 parete frontale', 'department_code' => 'superfici', 'area_label' => 'S2-27 Reparto Prodotti Speciali', 'sample_kind' => 'surface_contact', 'sort_order' => 100],
                    ['legacy_code' => '135', 'title' => 'Laminar flow: Cappa CB 16 (Lab. CQ)', 'department_code' => 'laminar', 'area_label' => 'S2 - Laboratorio Controllo Qualita', 'default_volume_liters' => 1000, 'sort_order' => 110],
                    ['legacy_code' => '136', 'title' => 'Laminar flow: Cappa CB 19 (Lab. CQ)', 'department_code' => 'laminar', 'area_label' => 'S2 - Laboratorio Controllo Qualita', 'default_volume_liters' => 1000, 'sort_order' => 120],
                    ['legacy_code' => '137', 'title' => 'Ambiente: centro locale Lab CQ', 'department_code' => 'ambiente', 'area_label' => 'S2 - Laboratorio Controllo Qualita', 'default_volume_liters' => 200, 'sort_order' => 130],
                    ['legacy_code' => '138', 'title' => 'Superfici: CB 16 piano di lavoro', 'department_code' => 'superfici', 'area_label' => 'S2 - Laboratorio Controllo Qualita', 'sample_kind' => 'surface_contact', 'sort_order' => 140],
                    ['legacy_code' => '139', 'title' => 'Superfici: CB 16 parete frontale', 'department_code' => 'superfici', 'area_label' => 'S2 - Laboratorio Controllo Qualita', 'sample_kind' => 'surface_contact', 'sort_order' => 150],
                    ['legacy_code' => '140', 'title' => 'Superfici: CB 16 parete laterale sx', 'department_code' => 'superfici', 'area_label' => 'S2 - Laboratorio Controllo Qualita', 'sample_kind' => 'surface_contact', 'sort_order' => 160],
                    ['legacy_code' => '141', 'title' => 'Superfici: CB 16 parete laterale dx', 'department_code' => 'superfici', 'area_label' => 'S2 - Laboratorio Controllo Qualita', 'sample_kind' => 'surface_contact', 'sort_order' => 170],
                    ['legacy_code' => '142', 'title' => 'Superfici: CB 19 piano di lavoro', 'department_code' => 'superfici', 'area_label' => 'S2 - Laboratorio Controllo Qualita', 'sample_kind' => 'surface_contact', 'sort_order' => 180],
                    ['legacy_code' => '143', 'title' => 'Superfici: CB 19 parete frontale', 'department_code' => 'superfici', 'area_label' => 'S2 - Laboratorio Controllo Qualita', 'sample_kind' => 'surface_contact', 'sort_order' => 190],
                    ['legacy_code' => '144', 'title' => 'Superfici: CB 19 parete laterale sx', 'department_code' => 'superfici', 'area_label' => 'S2 - Laboratorio Controllo Qualita', 'sample_kind' => 'surface_contact', 'sort_order' => 200],
                    ['legacy_code' => '145', 'title' => 'Superfici: CB 19 parete laterale dx', 'department_code' => 'superfici', 'area_label' => 'S2 - Laboratorio Controllo Qualita', 'sample_kind' => 'surface_contact', 'sort_order' => 210],
                    ['legacy_code' => '801', 'title' => 'Controllo negativo aria', 'department_code' => 'controlli', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 220],
                    ['legacy_code' => '802', 'title' => 'Controllo negativo superfici', 'department_code' => 'controlli', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 230],
                ],
            ],
            [
                'section' => [
                    'code' => 'clean_room_s1_aria_passiva',
                    'environment' => 'clean_room',
                    'sub_environment' => 's1',
                    'name' => 'S1 - Campionamento aria passivo',
                    'description' => 'Monitoraggio microbiologico clean room: campionamento dell\'aria passivo mediante piastre da esposizione.',
                    'sort_order' => 60,
                    'is_active' => true,
                ],
                'departments' => [
                    ['code' => 'grado_d1', 'name' => 'Grado D1', 'sort_order' => 10],
                    ['code' => 'grado_c1', 'name' => 'Grado C1', 'sort_order' => 20],
                    ['code' => 'grado_a1', 'name' => 'Grado A1', 'sort_order' => 30],
                    ['code' => 'controlli', 'name' => 'Controlli', 'sort_order' => 40],
                ],
                'points' => [
                    ['legacy_code' => 'SP03', 'title' => '20A', 'department_code' => 'grado_d1', 'area_label' => 'd1', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'sort_order' => 10],
                    ['legacy_code' => 'SP01', 'title' => '20B', 'department_code' => 'grado_d1', 'area_label' => 'd1', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'sort_order' => 20],
                    ['legacy_code' => 'SP05', 'title' => '20', 'department_code' => 'grado_c1', 'area_label' => 'c1', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'sort_order' => 30],
                    ['legacy_code' => 'SP06', 'title' => '20', 'department_code' => 'grado_c1', 'area_label' => 'c1', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'sort_order' => 40],
                    ['legacy_code' => 'SP08', 'title' => '20', 'department_code' => 'grado_c1', 'area_label' => 'c1', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'sort_order' => 50],
                    ['legacy_code' => 'SP10', 'title' => '20', 'department_code' => 'grado_c1', 'area_label' => 'c1', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'sort_order' => 60],
                    ['legacy_code' => 'SP11', 'title' => '20', 'department_code' => 'grado_c1', 'area_label' => 'c1', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'sort_order' => 70],
                    ['legacy_code' => 'SP13', 'title' => 'CB09', 'department_code' => 'grado_a1', 'area_label' => 'a1', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'sort_order' => 80],
                    ['legacy_code' => 'SP16', 'title' => 'CB11', 'department_code' => 'grado_a1', 'area_label' => 'a1', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'sort_order' => 90],
                    ['legacy_code' => 'SP15', 'title' => 'FF01', 'department_code' => 'grado_c1', 'area_label' => 'c1', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'sort_order' => 100],
                    ['legacy_code' => 'SP14', 'title' => 'FF03', 'department_code' => 'grado_c1', 'area_label' => 'c1', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'sort_order' => 110],
                    ['legacy_code' => 'NEG_PASSIVO', 'title' => 'Controllo negativo', 'department_code' => 'controlli', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 120],
                ],
            ],
            [
                'section' => [
                    'code' => 'clean_room_s1_aria_attiva',
                    'environment' => 'clean_room',
                    'sub_environment' => 's1',
                    'name' => 'S1 - Campionamento aria attivo',
                    'description' => 'Monitoraggio microbiologico clean room: campionamento dell\'aria attivo.',
                    'sort_order' => 70,
                    'is_active' => true,
                ],
                'departments' => [
                    ['code' => 'grado_d1', 'name' => 'Grado D1', 'sort_order' => 10],
                    ['code' => 'grado_c1', 'name' => 'Grado C1', 'sort_order' => 20],
                    ['code' => 'grado_a1', 'name' => 'Grado A1', 'sort_order' => 30],
                    ['code' => 'controlli', 'name' => 'Controlli', 'sort_order' => 40],
                ],
                'points' => [
                    ['legacy_code' => 'SAS03', 'title' => '20A', 'department_code' => 'grado_d1', 'area_label' => 'd1', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'sort_order' => 10],
                    ['legacy_code' => 'SAS01', 'title' => '20B', 'department_code' => 'grado_d1', 'area_label' => 'd1', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'sort_order' => 20],
                    ['legacy_code' => 'SAS05', 'title' => '20', 'department_code' => 'grado_c1', 'area_label' => 'c1', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'sort_order' => 30],
                    ['legacy_code' => 'SAS06', 'title' => '20', 'department_code' => 'grado_c1', 'area_label' => 'c1', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'sort_order' => 40],
                    ['legacy_code' => 'SAS08', 'title' => '20', 'department_code' => 'grado_c1', 'area_label' => 'c1', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'sort_order' => 50],
                    ['legacy_code' => 'SAS10', 'title' => '20', 'department_code' => 'grado_c1', 'area_label' => 'c1', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'sort_order' => 60],
                    ['legacy_code' => 'SAS11', 'title' => '20', 'department_code' => 'grado_c1', 'area_label' => 'c1', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'sort_order' => 70],
                    ['legacy_code' => 'SAS13', 'title' => 'CB09', 'department_code' => 'grado_a1', 'area_label' => 'a1', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'sort_order' => 80],
                    ['legacy_code' => 'SAS16', 'title' => 'CB11', 'department_code' => 'grado_a1', 'area_label' => 'a1', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'sort_order' => 90],
                    ['legacy_code' => 'SAS15', 'title' => 'FF01', 'department_code' => 'grado_c1', 'area_label' => 'c1', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'sort_order' => 100],
                    ['legacy_code' => 'SAS14', 'title' => 'FF03', 'department_code' => 'grado_c1', 'area_label' => 'c1', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'sort_order' => 110],
                    ['legacy_code' => 'NEG_ATTIVO', 'title' => 'Controllo negativo', 'department_code' => 'controlli', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 120],
                ],
            ],
            [
                'section' => [
                    'code' => 'clean_room_s1_superfici_locali',
                    'environment' => 'clean_room',
                    'sub_environment' => 's1',
                    'name' => 'S1 - Campionamento superfici locali',
                    'description' => 'Monitoraggio microbiologico clean room: campionamento delle superfici dei locali.',
                    'sort_order' => 80,
                    'is_active' => true,
                ],
                'departments' => [
                    ['code' => 'grado_d1', 'name' => 'Grado D1', 'sort_order' => 10],
                    ['code' => 'grado_c1', 'name' => 'Grado C1', 'sort_order' => 20],
                    ['code' => 'controlli', 'name' => 'Controlli', 'sort_order' => 30],
                ],
                'points' => [
                    ['legacy_code' => 'PAV03', 'title' => '20A', 'department_code' => 'grado_d1', 'area_label' => 'd1', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 10],
                    ['legacy_code' => 'PAV04', 'title' => '20A', 'department_code' => 'grado_d1', 'area_label' => 'd1', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 20],
                    ['legacy_code' => 'PAV01', 'title' => '20B', 'department_code' => 'grado_d1', 'area_label' => 'd1', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 30],
                    ['legacy_code' => 'PAV02', 'title' => '20B', 'department_code' => 'grado_d1', 'area_label' => 'd1', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 40],
                    ['legacy_code' => 'PAV05', 'title' => '20', 'department_code' => 'grado_c1', 'area_label' => 'c1', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 50],
                    ['legacy_code' => 'PAV06', 'title' => '20', 'department_code' => 'grado_c1', 'area_label' => 'c1', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 60],
                    ['legacy_code' => 'PAV08', 'title' => '20', 'department_code' => 'grado_c1', 'area_label' => 'c1', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 70],
                    ['legacy_code' => 'PAV10', 'title' => '20', 'department_code' => 'grado_c1', 'area_label' => 'c1', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 80],
                    ['legacy_code' => 'PAV11', 'title' => '20', 'department_code' => 'grado_c1', 'area_label' => 'c1', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 90],
                    ['legacy_code' => 'NEG_LOCALI', 'title' => 'Controllo negativo', 'department_code' => 'controlli', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 100],
                ],
            ],
            [
                'section' => [
                    'code' => 'clean_room_s1_macchine_1',
                    'environment' => 'clean_room',
                    'sub_environment' => 's1',
                    'name' => 'S1 - Campionamento macchine 1',
                    'description' => 'Monitoraggio microbiologico clean room: campionamento delle superfici delle macchine.',
                    'sort_order' => 90,
                    'is_active' => true,
                ],
                'departments' => [
                    ['code' => 'superfici_interne', 'name' => 'Superfici interne', 'sort_order' => 10],
                    ['code' => 'superfici_esterne', 'name' => 'Superfici esterne', 'sort_order' => 20],
                    ['code' => 'controlli', 'name' => 'Controlli', 'sort_order' => 30],
                ],
                'points' => [
                    ['legacy_code' => 'INT01', 'title' => 'FF03 - Pannello anteriore', 'department_code' => 'superfici_interne', 'area_label' => 'c1', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 10],
                    ['legacy_code' => 'INT03', 'title' => 'FF01 - Piano di lavoro', 'department_code' => 'superfici_interne', 'area_label' => 'c1', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 20],
                    ['legacy_code' => 'INT04', 'title' => 'FF01 - Pannello', 'department_code' => 'superfici_interne', 'area_label' => 'c1', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 30],
                    ['legacy_code' => 'INT05', 'title' => 'CB09 - SX', 'department_code' => 'superfici_interne', 'area_label' => 'a1', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 40],
                    ['legacy_code' => 'INT06', 'title' => 'CB09 - DX', 'department_code' => 'superfici_interne', 'area_label' => 'a1', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 50],
                    ['legacy_code' => 'INT07', 'title' => 'CB11 - SX', 'department_code' => 'superfici_interne', 'area_label' => 'a1', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 60],
                    ['legacy_code' => 'INT08', 'title' => 'CB11 - DX', 'department_code' => 'superfici_interne', 'area_label' => 'a1', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 70],
                    ['legacy_code' => 'EXT01', 'title' => 'FF03 - Pannello anteriore', 'department_code' => 'superfici_esterne', 'area_label' => 'c1', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 80],
                    ['legacy_code' => 'EXT03', 'title' => 'FF01 - Piano di lavoro', 'department_code' => 'superfici_esterne', 'area_label' => 'c1', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 90],
                    ['legacy_code' => 'EXT04', 'title' => 'FF01 - Pannello', 'department_code' => 'superfici_esterne', 'area_label' => 'c1', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 100],
                    ['legacy_code' => 'EXT05', 'title' => 'CB09 - SX', 'department_code' => 'superfici_esterne', 'area_label' => 'c1', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 110],
                    ['legacy_code' => 'EXT06', 'title' => 'CB09 - DX', 'department_code' => 'superfici_esterne', 'area_label' => 'c1', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 120],
                    ['legacy_code' => 'EXT07', 'title' => 'CB11 - SX', 'department_code' => 'superfici_esterne', 'area_label' => 'c1', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 130],
                    ['legacy_code' => 'EXT08', 'title' => 'CB11 - DX', 'department_code' => 'superfici_esterne', 'area_label' => 'c1', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 140],
                    ['legacy_code' => 'NEG_MACCHINE_1', 'title' => 'Controllo negativo', 'department_code' => 'controlli', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 150],
                ],
            ],
            [
                'section' => [
                    'code' => 'clean_room_s1_macchine_2',
                    'environment' => 'clean_room',
                    'sub_environment' => 's1',
                    'name' => 'S1 - Campionamento macchine 2',
                    'description' => 'Monitoraggio microbiologico clean room: campionamento delle superfici delle macchine con swab.',
                    'sort_order' => 100,
                    'is_active' => true,
                ],
                'departments' => [
                    ['code' => 'swab_macchine', 'name' => 'Swab macchine', 'sort_order' => 10],
                    ['code' => 'controlli', 'name' => 'Controlli', 'sort_order' => 20],
                ],
                'points' => [
                    ['legacy_code' => 'SW01', 'title' => 'FF03 - Apertura fiale', 'department_code' => 'swab_macchine', 'area_label' => 'c1', 'sample_kind' => 'surface_swab', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 10],
                    ['legacy_code' => 'SW02', 'title' => 'FF03 - Distribuzione', 'department_code' => 'swab_macchine', 'area_label' => 'c1', 'sample_kind' => 'surface_swab', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 20],
                    ['legacy_code' => 'SW03', 'title' => 'FF03 - Chiusura fiale', 'department_code' => 'swab_macchine', 'area_label' => 'c1', 'sample_kind' => 'surface_swab', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 30],
                    ['legacy_code' => 'SW04', 'title' => 'FF03 - Bocchetta chiusura fiale', 'department_code' => 'swab_macchine', 'area_label' => 'c1', 'sample_kind' => 'surface_swab', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 40],
                    ['legacy_code' => 'SW05', 'title' => 'FF01 - Distribuzione', 'department_code' => 'swab_macchine', 'area_label' => 'c1', 'sample_kind' => 'surface_swab', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 50],
                    ['legacy_code' => 'SW06', 'title' => 'FF01 - Chiusura fiale', 'department_code' => 'swab_macchine', 'area_label' => 'c1', 'sample_kind' => 'surface_swab', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 60],
                    ['legacy_code' => 'NEG_MACCHINE_2', 'title' => 'Controllo negativo', 'department_code' => 'controlli', 'sample_kind' => 'surface_swab', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 70],
                ],
            ],
            [
                'section' => [
                    'code' => 'clean_room_s2_aria_passiva',
                    'environment' => 'clean_room',
                    'sub_environment' => 's2',
                    'name' => 'S2 - Campionamento aria passivo',
                    'description' => 'Monitoraggio microbiologico clean room S2: campionamento dell\'aria passivo mediante piastre da esposizione.',
                    'sort_order' => 110,
                    'is_active' => true,
                ],
                'departments' => [
                    ['code' => 'grado_d2', 'name' => 'Grado D2', 'sort_order' => 10],
                    ['code' => 'grado_c2', 'name' => 'Grado C2', 'sort_order' => 20],
                    ['code' => 'grado_a2', 'name' => 'Grado A2', 'sort_order' => 30],
                    ['code' => 'controlli', 'name' => 'Controlli', 'sort_order' => 40],
                ],
                'points' => [
                    ['legacy_code' => 'SP13', 'title' => '11A', 'department_code' => 'grado_d2', 'area_label' => 'd2', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'sort_order' => 10],
                    ['legacy_code' => 'SP01', 'title' => '11B', 'department_code' => 'grado_d2', 'area_label' => 'd2', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'sort_order' => 20],
                    ['legacy_code' => 'SP03', 'title' => '11', 'department_code' => 'grado_c2', 'area_label' => 'c2', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'sort_order' => 30],
                    ['legacy_code' => 'SP04', 'title' => '11', 'department_code' => 'grado_c2', 'area_label' => 'c2', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'sort_order' => 40],
                    ['legacy_code' => 'SP05', 'title' => '11', 'department_code' => 'grado_c2', 'area_label' => 'c2', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'sort_order' => 50],
                    ['legacy_code' => 'SP14', 'title' => 'MP01_CA04', 'department_code' => 'grado_a2', 'area_label' => 'a2', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'sort_order' => 60],
                    ['legacy_code' => 'SP15', 'title' => 'MP01_Modulo 1', 'department_code' => 'grado_a2', 'area_label' => 'a2', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'sort_order' => 70],
                    ['legacy_code' => 'SP16', 'title' => 'MP01_Modulo 2', 'department_code' => 'grado_a2', 'area_label' => 'a2', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'sort_order' => 80],
                    ['legacy_code' => 'SP17', 'title' => 'MP01_Modulo 3', 'department_code' => 'grado_a2', 'area_label' => 'a2', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'sort_order' => 90],
                    ['legacy_code' => 'SP18', 'title' => 'MP01_Modulo 4', 'department_code' => 'grado_a2', 'area_label' => 'a2', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'sort_order' => 100],
                    ['legacy_code' => 'SP19', 'title' => 'MP01_Modulo 5', 'department_code' => 'grado_a2', 'area_label' => 'a2', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'sort_order' => 110],
                    ['legacy_code' => 'SP20', 'title' => 'MP01_Modulo 6', 'department_code' => 'grado_a2', 'area_label' => 'a2', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'sort_order' => 120],
                    ['legacy_code' => 'SP21', 'title' => 'IR06_CA20', 'department_code' => 'grado_a2', 'area_label' => 'a2', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'sort_order' => 130],
                    ['legacy_code' => 'SP22', 'title' => 'BL04_CA13', 'department_code' => 'grado_a2', 'area_label' => 'a2', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'sort_order' => 140],
                    ['legacy_code' => 'SP23', 'title' => 'CB10', 'department_code' => 'grado_a2', 'area_label' => 'a2', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'sort_order' => 150],
                    ['legacy_code' => 'NEG_PASSIVO_S2', 'title' => 'Controllo negativo', 'department_code' => 'controlli', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 160],
                ],
            ],
            [
                'section' => [
                    'code' => 'clean_room_s2_aria_attiva',
                    'environment' => 'clean_room',
                    'sub_environment' => 's2',
                    'name' => 'S2 - Campionamento aria attivo',
                    'description' => 'Monitoraggio microbiologico clean room S2: campionamento dell\'aria attivo.',
                    'sort_order' => 120,
                    'is_active' => true,
                ],
                'departments' => [
                    ['code' => 'grado_d2', 'name' => 'Grado D2', 'sort_order' => 10],
                    ['code' => 'grado_c2', 'name' => 'Grado C2', 'sort_order' => 20],
                    ['code' => 'grado_a2', 'name' => 'Grado A2', 'sort_order' => 30],
                    ['code' => 'controlli', 'name' => 'Controlli', 'sort_order' => 40],
                ],
                'points' => [
                    ['legacy_code' => 'SAS13', 'title' => '11A', 'department_code' => 'grado_d2', 'area_label' => 'd2', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'sort_order' => 10],
                    ['legacy_code' => 'SAS01', 'title' => '11B', 'department_code' => 'grado_d2', 'area_label' => 'd2', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'sort_order' => 20],
                    ['legacy_code' => 'SAS06', 'title' => '11', 'department_code' => 'grado_c2', 'area_label' => 'c2', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'sort_order' => 30],
                    ['legacy_code' => 'SAS07', 'title' => '11', 'department_code' => 'grado_c2', 'area_label' => 'c2', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'sort_order' => 40],
                    ['legacy_code' => 'SAS08', 'title' => '11', 'department_code' => 'grado_c2', 'area_label' => 'c2', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'sort_order' => 50],
                    ['legacy_code' => 'SAS14', 'title' => 'MP01_CA04', 'department_code' => 'grado_a2', 'area_label' => 'a2', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'sort_order' => 60],
                    ['legacy_code' => 'SAS15', 'title' => 'MP01_Modulo 1', 'department_code' => 'grado_a2', 'area_label' => 'a2', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'sort_order' => 70],
                    ['legacy_code' => 'SAS16', 'title' => 'MP01_Modulo 2', 'department_code' => 'grado_a2', 'area_label' => 'a2', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'sort_order' => 80],
                    ['legacy_code' => 'SAS17', 'title' => 'MP01_Modulo 3', 'department_code' => 'grado_a2', 'area_label' => 'a2', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'sort_order' => 90],
                    ['legacy_code' => 'SAS18', 'title' => 'MP01_Modulo 4', 'department_code' => 'grado_a2', 'area_label' => 'a2', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'sort_order' => 100],
                    ['legacy_code' => 'SAS19', 'title' => 'MP01_Modulo 5', 'department_code' => 'grado_a2', 'area_label' => 'a2', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'sort_order' => 110],
                    ['legacy_code' => 'SAS20', 'title' => 'MP01_Modulo 6', 'department_code' => 'grado_a2', 'area_label' => 'a2', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'sort_order' => 120],
                    ['legacy_code' => 'SAS21', 'title' => 'IR06_CA20', 'department_code' => 'grado_a2', 'area_label' => 'a2', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'sort_order' => 130],
                    ['legacy_code' => 'SAS22', 'title' => 'BL04_CA13', 'department_code' => 'grado_a2', 'area_label' => 'a2', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'sort_order' => 140],
                    ['legacy_code' => 'SAS23', 'title' => 'CB10', 'department_code' => 'grado_a2', 'area_label' => 'a2', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'sort_order' => 150],
                    ['legacy_code' => 'NEG_ATTIVO_S2', 'title' => 'Controllo negativo', 'department_code' => 'controlli', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 160],
                ],
            ],
            [
                'section' => [
                    'code' => 'clean_room_s2_superfici_locali',
                    'environment' => 'clean_room',
                    'sub_environment' => 's2',
                    'name' => 'S2 - Campionamento superfici locali',
                    'description' => 'Monitoraggio microbiologico clean room S2: campionamento delle superfici dei locali.',
                    'sort_order' => 130,
                    'is_active' => true,
                ],
                'departments' => [
                    ['code' => 'grado_d2', 'name' => 'Grado D2', 'sort_order' => 10],
                    ['code' => 'grado_c2', 'name' => 'Grado C2', 'sort_order' => 20],
                    ['code' => 'controlli', 'name' => 'Controlli', 'sort_order' => 30],
                ],
                'points' => [
                    ['legacy_code' => 'PAV13', 'title' => '11A', 'department_code' => 'grado_d2', 'area_label' => 'd2', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 10],
                    ['legacy_code' => 'PAV12', 'title' => '11A', 'department_code' => 'grado_d2', 'area_label' => 'd2', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 20],
                    ['legacy_code' => 'PAV01', 'title' => '11B', 'department_code' => 'grado_d2', 'area_label' => 'd2', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 30],
                    ['legacy_code' => 'PAV02', 'title' => '11B', 'department_code' => 'grado_d2', 'area_label' => 'd2', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 40],
                    ['legacy_code' => 'PAV03', 'title' => '11', 'department_code' => 'grado_c2', 'area_label' => 'c2', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 50],
                    ['legacy_code' => 'PAV05', 'title' => '11', 'department_code' => 'grado_c2', 'area_label' => 'c2', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 60],
                    ['legacy_code' => 'PAV08', 'title' => '11', 'department_code' => 'grado_c2', 'area_label' => 'c2', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 70],
                    ['legacy_code' => 'NEG_LOCALI_S2', 'title' => 'Controllo negativo', 'department_code' => 'controlli', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 80],
                ],
            ],
            [
                'section' => [
                    'code' => 'clean_room_s2_macchine_1',
                    'environment' => 'clean_room',
                    'sub_environment' => 's2',
                    'name' => 'S2 - Campionamento macchine 1',
                    'description' => 'Monitoraggio microbiologico clean room S2: campionamento delle superfici delle macchine con contact plate.',
                    'sort_order' => 140,
                    'is_active' => true,
                ],
                'departments' => [
                    ['code' => 'superfici_interne', 'name' => 'Superfici interne', 'sort_order' => 10],
                    ['code' => 'superfici_esterne', 'name' => 'Superfici esterne', 'sort_order' => 20],
                    ['code' => 'controlli', 'name' => 'Controlli', 'sort_order' => 30],
                ],
                'points' => [
                    ['legacy_code' => 'INT01', 'title' => 'MP01_CA04', 'department_code' => 'superfici_interne', 'area_label' => 'a2', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 10],
                    ['legacy_code' => 'INT02', 'title' => 'MP01_Modulo 1', 'department_code' => 'superfici_interne', 'area_label' => 'a2', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 20],
                    ['legacy_code' => 'INT06', 'title' => 'MP01_Modulo 5', 'department_code' => 'superfici_interne', 'area_label' => 'a2', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 30],
                    ['legacy_code' => 'INT10', 'title' => 'CB10 SX', 'department_code' => 'superfici_interne', 'area_label' => 'a2', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 40],
                    ['legacy_code' => 'INT11', 'title' => 'CB10 DX', 'department_code' => 'superfici_interne', 'area_label' => 'a2', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 50],
                    ['legacy_code' => 'EXT01', 'title' => 'MP01_CA04', 'department_code' => 'superfici_esterne', 'area_label' => 'c2', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 60],
                    ['legacy_code' => 'EXT02', 'title' => 'MP01_Modulo 1', 'department_code' => 'superfici_esterne', 'area_label' => 'c2', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 70],
                    ['legacy_code' => 'EXT06', 'title' => 'MP01_Modulo 5', 'department_code' => 'superfici_esterne', 'area_label' => 'c2', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 80],
                    ['legacy_code' => 'EXT10', 'title' => 'CB10 SX', 'department_code' => 'superfici_esterne', 'area_label' => 'c2', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 90],
                    ['legacy_code' => 'EXT11', 'title' => 'CB10 DX', 'department_code' => 'superfici_esterne', 'area_label' => 'c2', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 100],
                    ['legacy_code' => 'NEG_MACCHINE_1_S2', 'title' => 'Controllo negativo', 'department_code' => 'controlli', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 110],
                ],
            ],
            [
                'section' => [
                    'code' => 'clean_room_s2_macchine_2',
                    'environment' => 'clean_room',
                    'sub_environment' => 's2',
                    'name' => 'S2 - Campionamento macchine 2',
                    'description' => 'Monitoraggio microbiologico clean room S2: campionamento delle superfici delle macchine con swab.',
                    'sort_order' => 150,
                    'is_active' => true,
                ],
                'departments' => [
                    ['code' => 'swab_macchine', 'name' => 'Swab macchine', 'sort_order' => 10],
                    ['code' => 'controlli', 'name' => 'Controlli', 'sort_order' => 20],
                ],
                'points' => [
                    ['legacy_code' => 'SW01', 'title' => 'CA04_Nastro_Linea 1', 'department_code' => 'swab_macchine', 'area_label' => 'a2', 'sample_kind' => 'surface_swab', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 10],
                    ['legacy_code' => 'SW02', 'title' => 'CA04_Nastro_Linea 2', 'department_code' => 'swab_macchine', 'area_label' => 'a2', 'sample_kind' => 'surface_swab', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 20],
                    ['legacy_code' => 'SW03', 'title' => 'CA04_Nastro_Linea 3', 'department_code' => 'swab_macchine', 'area_label' => 'a2', 'sample_kind' => 'surface_swab', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 30],
                    ['legacy_code' => 'SW04', 'title' => 'CA04_Nastro_Linea 4', 'department_code' => 'swab_macchine', 'area_label' => 'a2', 'sample_kind' => 'surface_swab', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 40],
                    ['legacy_code' => 'SW05', 'title' => 'MP01_Corde_Linea 1', 'department_code' => 'swab_macchine', 'area_label' => 'a2', 'sample_kind' => 'surface_swab', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 50],
                    ['legacy_code' => 'SW06', 'title' => 'MP01_Corde_Linea 1', 'department_code' => 'swab_macchine', 'area_label' => 'a2', 'sample_kind' => 'surface_swab', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 60],
                    ['legacy_code' => 'SW07', 'title' => 'MP01_Corde_Linea 2', 'department_code' => 'swab_macchine', 'area_label' => 'a2', 'sample_kind' => 'surface_swab', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 70],
                    ['legacy_code' => 'SW08', 'title' => 'MP01_Corde_Linea 2', 'department_code' => 'swab_macchine', 'area_label' => 'a2', 'sample_kind' => 'surface_swab', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 80],
                    ['legacy_code' => 'SW09', 'title' => 'MP01_Corde_Linea 3', 'department_code' => 'swab_macchine', 'area_label' => 'a2', 'sample_kind' => 'surface_swab', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 90],
                    ['legacy_code' => 'SW10', 'title' => 'MP01_Corde_Linea 3', 'department_code' => 'swab_macchine', 'area_label' => 'a2', 'sample_kind' => 'surface_swab', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 100],
                    ['legacy_code' => 'SW11', 'title' => 'MP01_Corde_Linea 4', 'department_code' => 'swab_macchine', 'area_label' => 'a2', 'sample_kind' => 'surface_swab', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 110],
                    ['legacy_code' => 'SW12', 'title' => 'MP01_Corde_Linea 4', 'department_code' => 'swab_macchine', 'area_label' => 'a2', 'sample_kind' => 'surface_swab', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 120],
                    ['legacy_code' => 'NEG_MACCHINE_2_S2', 'title' => 'Controllo negativo', 'department_code' => 'controlli', 'sample_kind' => 'surface_swab', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 130],
                ],
            ],
            [
                'section' => [
                    'code' => 'clean_room_s3_aria_passiva',
                    'environment' => 'clean_room',
                    'sub_environment' => 's3',
                    'name' => 'S3 - Campionamento aria passivo',
                    'description' => 'Monitoraggio microbiologico clean room S3: campionamento dell\'aria passivo mediante piastre da esposizione.',
                    'sort_order' => 160,
                    'is_active' => true,
                ],
                'departments' => [
                    ['code' => 'grado_d3', 'name' => 'Grado D3', 'sort_order' => 10],
                    ['code' => 'grado_c3', 'name' => 'Grado C3', 'sort_order' => 20],
                    ['code' => 'grado_a3', 'name' => 'Grado A3', 'sort_order' => 30],
                    ['code' => 'controlli', 'name' => 'Controlli', 'sort_order' => 40],
                ],
                'points' => [
                    ['legacy_code' => 'SP01', 'title' => '10A', 'department_code' => 'grado_d3', 'area_label' => 'd3', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'sort_order' => 10],
                    ['legacy_code' => 'SP03', 'title' => '10B', 'department_code' => 'grado_d3', 'area_label' => 'd3', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'sort_order' => 20],
                    ['legacy_code' => 'SP14', 'title' => 'Pass Box 10D', 'department_code' => 'grado_d3', 'area_label' => 'd3', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'sort_order' => 30],
                    ['legacy_code' => 'SP05', 'title' => '10C', 'department_code' => 'grado_c3', 'area_label' => 'c3', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'sort_order' => 40],
                    ['legacy_code' => 'SP07', 'title' => '10', 'department_code' => 'grado_c3', 'area_label' => 'c3', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'sort_order' => 50],
                    ['legacy_code' => 'SP08', 'title' => '10', 'department_code' => 'grado_c3', 'area_label' => 'c3', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'sort_order' => 60],
                    ['legacy_code' => 'SP09', 'title' => '10', 'department_code' => 'grado_c3', 'area_label' => 'c3', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'sort_order' => 70],
                    ['legacy_code' => 'SP10', 'title' => '10', 'department_code' => 'grado_c3', 'area_label' => 'c3', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'sort_order' => 80],
                    ['legacy_code' => 'SP11', 'title' => '10', 'department_code' => 'grado_c3', 'area_label' => 'c3', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'sort_order' => 90],
                    ['legacy_code' => 'SP12', 'title' => '10', 'department_code' => 'grado_c3', 'area_label' => 'c3', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'sort_order' => 100],
                    ['legacy_code' => 'SP13', 'title' => 'CA18_UM01', 'department_code' => 'grado_a3', 'area_label' => 'a3', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'sort_order' => 110],
                    ['legacy_code' => 'SP15', 'title' => 'CA21_Modulo 1', 'department_code' => 'grado_a3', 'area_label' => 'a3', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'sort_order' => 120],
                    ['legacy_code' => 'SP16', 'title' => 'CA21_Modulo 2', 'department_code' => 'grado_a3', 'area_label' => 'a3', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'sort_order' => 130],
                    ['legacy_code' => 'SP17', 'title' => 'CA21_Modulo 3', 'department_code' => 'grado_a3', 'area_label' => 'a3', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'sort_order' => 140],
                    ['legacy_code' => 'SP18', 'title' => 'CA21_Modulo 4', 'department_code' => 'grado_a3', 'area_label' => 'a3', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'sort_order' => 150],
                    ['legacy_code' => 'CN1', 'title' => 'Controllo negativo', 'department_code' => 'controlli', 'sample_kind' => 'air_passive', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 160],
                ],
            ],
            [
                'section' => [
                    'code' => 'clean_room_s3_aria_attiva',
                    'environment' => 'clean_room',
                    'sub_environment' => 's3',
                    'name' => 'S3 - Campionamento aria attivo',
                    'description' => 'Monitoraggio microbiologico clean room S3: campionamento dell\'aria attivo.',
                    'sort_order' => 170,
                    'is_active' => true,
                ],
                'departments' => [
                    ['code' => 'grado_d3', 'name' => 'Grado D3', 'sort_order' => 10],
                    ['code' => 'grado_c3', 'name' => 'Grado C3', 'sort_order' => 20],
                    ['code' => 'grado_a3', 'name' => 'Grado A3', 'sort_order' => 30],
                    ['code' => 'controlli', 'name' => 'Controlli', 'sort_order' => 40],
                ],
                'points' => [
                    ['legacy_code' => 'SAS01', 'title' => '10A', 'department_code' => 'grado_d3', 'area_label' => 'd3', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'sort_order' => 10],
                    ['legacy_code' => 'SAS03', 'title' => '10B', 'department_code' => 'grado_d3', 'area_label' => 'd3', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'sort_order' => 20],
                    ['legacy_code' => 'SAS14', 'title' => 'Pass Box 10D', 'department_code' => 'grado_d3', 'area_label' => 'd3', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'sort_order' => 30],
                    ['legacy_code' => 'SAS05', 'title' => '10C', 'department_code' => 'grado_c3', 'area_label' => 'c3', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'sort_order' => 40],
                    ['legacy_code' => 'SAS07', 'title' => '10', 'department_code' => 'grado_c3', 'area_label' => 'c3', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'sort_order' => 50],
                    ['legacy_code' => 'SAS08', 'title' => '10', 'department_code' => 'grado_c3', 'area_label' => 'c3', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'sort_order' => 60],
                    ['legacy_code' => 'SAS09', 'title' => '10', 'department_code' => 'grado_c3', 'area_label' => 'c3', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'sort_order' => 70],
                    ['legacy_code' => 'SAS10', 'title' => '10', 'department_code' => 'grado_c3', 'area_label' => 'c3', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'sort_order' => 80],
                    ['legacy_code' => 'SAS11', 'title' => '10', 'department_code' => 'grado_c3', 'area_label' => 'c3', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'sort_order' => 90],
                    ['legacy_code' => 'SAS12', 'title' => '10', 'department_code' => 'grado_c3', 'area_label' => 'c3', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'sort_order' => 100],
                    ['legacy_code' => 'SAS13', 'title' => 'CA18_UM01', 'department_code' => 'grado_a3', 'area_label' => 'a3', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'sort_order' => 110],
                    ['legacy_code' => 'SAS15', 'title' => 'CA21_Modulo 1', 'department_code' => 'grado_a3', 'area_label' => 'a3', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'sort_order' => 120],
                    ['legacy_code' => 'SAS16', 'title' => 'CA21_Modulo 2', 'department_code' => 'grado_a3', 'area_label' => 'a3', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'sort_order' => 130],
                    ['legacy_code' => 'SAS17', 'title' => 'CA21_Modulo 3', 'department_code' => 'grado_a3', 'area_label' => 'a3', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'sort_order' => 140],
                    ['legacy_code' => 'SAS18', 'title' => 'CA21_Modulo 4', 'department_code' => 'grado_a3', 'area_label' => 'a3', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'sort_order' => 150],
                    ['legacy_code' => 'CN2', 'title' => 'Controllo negativo', 'department_code' => 'controlli', 'sample_kind' => 'air_active', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 160],
                ],
            ],
            [
                'section' => [
                    'code' => 'clean_room_s3_superfici_locali',
                    'environment' => 'clean_room',
                    'sub_environment' => 's3',
                    'name' => 'S3 - Campionamento superfici locali',
                    'description' => 'Monitoraggio microbiologico clean room S3: campionamento delle superfici dei locali.',
                    'sort_order' => 180,
                    'is_active' => true,
                ],
                'departments' => [
                    ['code' => 'grado_d3', 'name' => 'Grado D3', 'sort_order' => 10],
                    ['code' => 'grado_c3', 'name' => 'Grado C3', 'sort_order' => 20],
                    ['code' => 'controlli', 'name' => 'Controlli', 'sort_order' => 30],
                ],
                'points' => [
                    ['legacy_code' => 'PAV01', 'title' => '10A', 'department_code' => 'grado_d3', 'area_label' => 'd3', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 10],
                    ['legacy_code' => 'PAV02', 'title' => '10A', 'department_code' => 'grado_d3', 'area_label' => 'd3', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 20],
                    ['legacy_code' => 'PAV03', 'title' => '10B', 'department_code' => 'grado_d3', 'area_label' => 'd3', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 30],
                    ['legacy_code' => 'PAV04', 'title' => '10B', 'department_code' => 'grado_d3', 'area_label' => 'd3', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 40],
                    ['legacy_code' => 'PAV05', 'title' => '10C', 'department_code' => 'grado_c3', 'area_label' => 'c3', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 50],
                    ['legacy_code' => 'PAV07', 'title' => '10', 'department_code' => 'grado_c3', 'area_label' => 'c3', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 60],
                    ['legacy_code' => 'PAV09', 'title' => '10', 'department_code' => 'grado_c3', 'area_label' => 'c3', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 70],
                    ['legacy_code' => 'PAV11', 'title' => '10', 'department_code' => 'grado_c3', 'area_label' => 'c3', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 80],
                    ['legacy_code' => 'CN3', 'title' => 'Controllo negativo', 'department_code' => 'controlli', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 90],
                ],
            ],
            [
                'section' => [
                    'code' => 'clean_room_s3_macchine_1',
                    'environment' => 'clean_room',
                    'sub_environment' => 's3',
                    'name' => 'S3 - Campionamento macchine 1',
                    'description' => 'Monitoraggio microbiologico clean room S3: campionamento delle superfici delle macchine con contact plate.',
                    'sort_order' => 190,
                    'is_active' => true,
                ],
                'departments' => [
                    ['code' => 'superfici_interne', 'name' => 'Superfici interne', 'sort_order' => 10],
                    ['code' => 'superfici_esterne', 'name' => 'Superfici esterne', 'sort_order' => 20],
                    ['code' => 'controlli', 'name' => 'Controlli', 'sort_order' => 30],
                ],
                'points' => [
                    ['legacy_code' => 'INT01', 'title' => 'CA18_UM01_Bandella SX interna', 'department_code' => 'superfici_interne', 'area_label' => 'a3', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 10],
                    ['legacy_code' => 'INT02', 'title' => 'CA18_UM01_Bandella DX interna', 'department_code' => 'superfici_interne', 'area_label' => 'a3', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 20],
                    ['legacy_code' => 'INT03', 'title' => 'CA21_SX', 'department_code' => 'superfici_interne', 'area_label' => 'a3', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 30],
                    ['legacy_code' => 'INT04', 'title' => 'CA21_DX', 'department_code' => 'superfici_interne', 'area_label' => 'a3', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 40],
                    ['legacy_code' => 'EXT01', 'title' => 'CA18_UM01_Bandella SX esterna', 'department_code' => 'superfici_esterne', 'area_label' => 'c3', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 50],
                    ['legacy_code' => 'EXT02', 'title' => 'CA18_UM01_Bandella DX esterna', 'department_code' => 'superfici_esterne', 'area_label' => 'c3', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 60],
                    ['legacy_code' => 'EXT03', 'title' => 'CA21_SX', 'department_code' => 'superfici_esterne', 'area_label' => 'c3', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 70],
                    ['legacy_code' => 'EXT04', 'title' => 'CA21_DX', 'department_code' => 'superfici_esterne', 'area_label' => 'c3', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'sort_order' => 80],
                    ['legacy_code' => 'CN5', 'title' => 'Controllo negativo', 'department_code' => 'controlli', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 90],
                ],
            ],
            [
                'section' => [
                    'code' => 'clean_room_s3_macchine_2',
                    'environment' => 'clean_room',
                    'sub_environment' => 's3',
                    'name' => 'S3 - Campionamento macchine 2',
                    'description' => 'Monitoraggio microbiologico clean room S3: campionamento delle superfici delle macchine con swab.',
                    'sort_order' => 200,
                    'is_active' => true,
                ],
                'departments' => [
                    ['code' => 'swab_macchine', 'name' => 'Swab macchine', 'sort_order' => 10],
                    ['code' => 'controlli', 'name' => 'Controlli', 'sort_order' => 20],
                ],
                'points' => [
                    ['legacy_code' => 'SW01', 'title' => 'IF01_Piano di caricamento', 'department_code' => 'swab_macchine', 'area_label' => 'a3', 'sample_kind' => 'surface_swab', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 10],
                    ['legacy_code' => 'SW02', 'title' => 'IF01_Essiccazione', 'department_code' => 'swab_macchine', 'area_label' => 'a3', 'sample_kind' => 'surface_swab', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 20],
                    ['legacy_code' => 'SW03', 'title' => 'IF01_Fustellatrice', 'department_code' => 'swab_macchine', 'area_label' => 'a3', 'sample_kind' => 'surface_swab', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 30],
                    ['legacy_code' => 'SW04', 'title' => 'UM01_Alloggi Blisteraggio', 'department_code' => 'swab_macchine', 'area_label' => 'a3', 'sample_kind' => 'surface_swab', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 40],
                    ['legacy_code' => 'SW05', 'title' => 'UM01_Piano di appoggio', 'department_code' => 'swab_macchine', 'area_label' => 'a3', 'sample_kind' => 'surface_swab', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 50],
                    ['legacy_code' => 'CN4', 'title' => 'Controllo negativo', 'department_code' => 'controlli', 'sample_kind' => 'surface_swab', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 60],
                ],
            ],
            [
                'section' => [
                    'code' => 'acque_processo',
                    'environment' => 'acque',
                    'name' => 'Acque di processo (storico)',
                    'description' => 'Sezione legacy generica mantenuta per preservare eventuale storico precedente.',
                    'sort_order' => 110,
                    'is_active' => false,
                ],
                'departments' => [
                    ['code' => 'punti_uso', 'name' => 'Punti uso', 'sort_order' => 10],
                    ['code' => 'controlli', 'name' => 'Controlli', 'sort_order' => 20],
                ],
                'points' => [
                    ['legacy_code' => '451', 'title' => 'Rubinetto produzione', 'department_code' => 'punti_uso', 'sample_kind' => 'surface_swab', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 10],
                    ['legacy_code' => '452', 'title' => 'Punto campionamento loop acqua', 'department_code' => 'punti_uso', 'sample_kind' => 'surface_swab', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 20],
                    ['legacy_code' => '453', 'title' => 'Controllo negativo acque', 'department_code' => 'controlli', 'sample_kind' => 'surface_swab', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 30],
                ],
            ],
            [
                'section' => [
                    'code' => 'operatori_controllo',
                    'environment' => 'operatori',
                    'name' => 'Controllo operatori (storico)',
                    'description' => 'Sezione storica generica mantenuta per preservare eventuali registrazioni pregresse.',
                    'sort_order' => 120,
                    'is_active' => false,
                ],
                'departments' => [
                    ['code' => 'operatori', 'name' => 'Operatori', 'sort_order' => 10],
                    ['code' => 'controlli', 'name' => 'Controlli', 'sort_order' => 20],
                ],
                'points' => [
                    ['legacy_code' => '501', 'title' => 'Guanto mano destra', 'department_code' => 'operatori', 'sample_kind' => 'surface_contact', 'requires_product_lot' => false, 'sort_order' => 10],
                    ['legacy_code' => '502', 'title' => 'Guanto mano sinistra', 'department_code' => 'operatori', 'sample_kind' => 'surface_contact', 'requires_product_lot' => false, 'sort_order' => 20],
                    ['legacy_code' => '503', 'title' => 'Controllo negativo operatori', 'department_code' => 'controlli', 'sample_kind' => 'surface_contact', 'requires_operational_status' => false, 'requires_product_lot' => false, 'sort_order' => 30],
                ],
            ],
        ];

        foreach ($operatorSections as $index => $operatorSection) {
            $sections[] = [
                'section' => [
                    'code' => $operatorSection['code'],
                    'environment' => 'operatori',
                    'name' => $operatorSection['name'],
                    'description' => $operatorSection['description'],
                    'sort_order' => 130 + ($index * 10),
                    'is_active' => true,
                ],
                'departments' => $operatorDepartments,
                'points' => $operatorPoints,
            ];
        }

        foreach ($waterSections as $waterSection) {
            $sections[] = $waterSection;
        }

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
