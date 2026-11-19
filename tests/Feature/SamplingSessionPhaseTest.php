<?php

namespace Tests\Feature;

use App\Models\MicrobiologicalCheck;
use App\Models\MonitoringSection;
use App\Models\SamplingPoint;
use App\Models\SamplingSession;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class SamplingSessionPhaseTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Use the relevant migrations without the MySQL-only legacy signer migration.
        foreach ([
            '2014_10_12_000000_create_users_table',
            '2026_07_08_000001_create_monitoring_sections_table',
            '2026_07_08_000002_create_sampling_points_table',
            '2026_07_08_000003_create_microbiological_checks_table',
            '2026_07_08_000004_create_microbiological_check_points_table',
            '2026_07_09_000005_create_monitoring_departments_table',
            '2026_07_09_100000_add_role_to_users_table',
            '2026_07_13_000006_add_environment_to_monitoring_sections_table',
            '2026_07_13_000007_add_sub_environment_to_monitoring_sections_table',
            '2026_07_13_000008_extend_microbiological_check_points_for_clean_room',
            '2026_07_13_000009_add_product_batch_to_microbiological_checks_table',
            '2026_07_15_000010_add_water_header_fields_to_microbiological_checks_table',
            '2026_07_15_000011_add_water_result_fields_to_microbiological_check_points_table',
            '2026_07_15_000012_add_sampled_time_to_microbiological_checks_table',
            '2026_07_21_000013_unify_water_sections_and_add_incubation_signatures',
            '2026_07_21_000014_add_water_pdf_result_fields_to_microbiological_check_points_table',
            '2026_07_21_000015_add_water_sampling_sheet_fields_to_microbiological_checks_table',
            '2026_07_27_000018_create_microbiological_check_phase_logs_table',
            '2026_07_27_000020_add_reason_to_microbiological_check_phase_logs_table',
            '2026_07_28_000021_add_readings_count_to_monitoring_departments_table',
            '2026_07_28_000022_create_microbiological_check_readings_table',
            '2026_07_29_000025_add_default_exposure_hours_to_sampling_points_table',
            '2026_07_29_000026_add_soft_deletes_to_microbiological_checks_table',
            '2026_07_29_000027_add_soft_deletes_to_monitoring_departments_table',
            '2026_09_03_000029_add_sampling_session_id_to_microbiological_checks_table',
            '2026_09_03_000030_create_sampling_sessions_table',
            '2026_11_12_000001_add_image_path_to_monitoring_sections_table',
            '2026_11_19_000001_add_water_analyses_to_sampling_points_table',
            '2026_11_19_000002_add_water_analysis_results_to_check_points_table',
        ] as $migration) {
            (require database_path("migrations/{$migration}.php"))->up();
        }

        Schema::table('microbiological_checks', function (Blueprint $table): void {
            $table->unsignedBigInteger('sampling_completed_by_user_id')->nullable();
            $table->unsignedBigInteger('first_reading_completed_by_user_id')->nullable();
            $table->unsignedBigInteger('second_reading_completed_by_user_id')->nullable();
        });

        (require database_path('migrations/2026_07_28_000023_create_microbiological_check_phase_states_table.php'))->up();
    }

    public function test_admin_can_configure_water_analyses_for_new_and_existing_points(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $section = MonitoringSection::query()->create([
            'code' => 'water',
            'name' => 'Acque',
            'environment' => 'acque',
            'is_active' => true,
        ]);
        $existing = $section->samplingPoints()->create([
            'title' => 'Punto esistente',
            'sample_kind' => 'water',
            'requires_operational_status' => true,
            'requires_product_lot' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('monitoraggi.index', ['view' => 'gestione-punti', 'env' => 'acque']))
            ->assertOk()
            ->assertSee('Locale (opzionale)')
            ->assertDontSee('Richiede stato operativo')
            ->assertDontSee('Richiede lotto prodotto')
            ->assertDontSee('Tipologia specifica')
            ->assertSee('name="water_analyses[]" value="pseudomonas" checked', false);
        $existingForm = explode('</form>', explode('action="'.route('monitoraggi.points.update', [$section, $existing]).'"', $response->getContent())[1])[0];
        $this->assertStringContainsString('name="water_analyses[]" value="pseudomonas" checked', $existingForm);

        $this->post(route('monitoraggi.points.store', $section), [
            'title' => 'Nuovo punto',
            'area_label' => 'Locale 1',
            'water_analyses' => ['appearance', 'ph', 'r2a'],
            'insert_position' => 'end',
        ])->assertSessionHasNoErrors();

        $created = $section->samplingPoints()->where('title', 'Nuovo punto')->firstOrFail();
        $this->assertSame('water', $created->sample_kind);
        $this->assertSame(['appearance', 'ph', 'r2a'], $created->water_analyses);
        $this->assertEquals(0, $created->requires_operational_status);
        $this->assertEquals(0, $created->requires_product_lot);

        $payload = ['title' => 'Punto esistente', 'sample_kind' => 'water', 'is_active' => '1'];
        $this->patch(route('monitoraggi.points.update', [$section, $existing]), $payload)
            ->assertSessionHasNoErrors();

        $existing->refresh();
        $this->assertSame([], $existing->water_analyses);
        $this->assertEquals(1, $existing->requires_operational_status);
        $this->assertEquals(1, $existing->requires_product_lot);

        $this->patch(route('monitoraggi.points.update', [$section, $existing]), $payload + [
            'water_analyses' => ['coliforms', 'enterococci'],
        ])->assertSessionHasNoErrors();
        $this->assertSame(['coliforms', 'enterococci'], $existing->fresh()->water_analyses);

        $response = $this->get(route('monitoraggi.index', ['view' => 'gestione-punti', 'env' => 'acque']))->assertOk();
        $existingForm = explode('</form>', explode('action="'.route('monitoraggi.points.update', [$section, $existing]).'"', $response->getContent())[1])[0];
        $this->assertStringContainsString('name="water_analyses[]" value="coliforms" checked', $existingForm);
        $this->assertStringContainsString('name="water_analyses[]" value="ph" >', $existingForm);

        $this->patch(route('monitoraggi.points.update', [$section, $existing]), $payload + [
            'water_analyses' => ['not-an-analysis'],
        ])->assertSessionHasErrors('water_analyses.0');
        $this->assertSame(['coliforms', 'enterococci'], $existing->fresh()->water_analyses);
    }

    public function test_non_water_point_management_keeps_its_existing_fields(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $section = MonitoringSection::query()->create([
            'code' => 'production',
            'name' => 'Produzione',
            'environment' => 'produzione',
            'is_active' => true,
        ]);

        $this->actingAs($admin)->get(route('monitoraggi.index', ['view' => 'gestione-punti', 'env' => 'produzione']))
            ->assertOk()
            ->assertSee('Area dettagliata (opzionale)')
            ->assertSee('Richiede stato operativo')
            ->assertSee('Richiede lotto prodotto')
            ->assertDontSee('name="water_analyses[]"', false);

        $this->post(route('monitoraggi.points.store', $section), [
            'title' => 'Punto produzione',
            'sample_kind' => 'air_active',
            'requires_operational_status' => '1',
            'requires_product_lot' => '0',
            'insert_position' => 'end',
        ])->assertSessionHasNoErrors();
        $point = $section->samplingPoints()->firstOrFail();
        $this->assertSame('air_active', $point->sample_kind);
        $this->assertEquals(1, $point->requires_operational_status);
        $this->assertEquals(0, $point->requires_product_lot);
    }

    public function test_water_session_header_is_shared_without_overwriting_section_specific_data(): void
    {
        $operator = User::factory()->create(['role' => 'operatore']);
        $session = SamplingSession::query()->create([
            'id' => (string) Str::uuid(),
            'environment' => 'acque',
            'header' => [
                'sampled_on' => '2026-11-19',
                'facility_name' => 'Dato precedente della sessione',
                'incubation_started_signature' => null,
            ],
            'created_by_user_id' => $operator->id,
        ]);
        $checks = [];
        foreach (['water1', 'water2'] as $code) {
            $section = MonitoringSection::query()->create([
                'code' => $code, 'name' => $code, 'environment' => 'acque', 'is_active' => true,
            ]);
            $checks[] = MicrobiologicalCheck::query()->create([
                'monitoring_section_id' => $section->id, 'sampling_session_id' => $session->id,
                'sampled_on' => '2026-11-19', 'facility_name' => $code,
                'incubation_started_signature' => 'Firma '.$code, 'r2a_incubator_code' => 'Inc '.$code,
            ]);
        }

        $response = $this->actingAs($operator)->get(route('monitoraggi.index', [
            'view' => 'nuovo', 'env' => 'acque', 'session' => $session->id,
        ]))->assertOk();
        $headerForm = explode('</form>', explode('action="'.route('monitoraggi.sessions.update', $session).'"', $response->getContent())[1])[0];
        foreach ([
            'membrane_lot', 'bottle_sterilization_lot', 'r2a_agar_lot', 'r2a_agar_expires_on',
            'coliform_agar_lot', 'coliform_agar_expires_on', 'pseudomonas_cn_lot', 'pseudomonas_cn_expires_on',
            'slanetz_bartley_lot', 'slanetz_bartley_expires_on',
        ] as $field) {
            $this->assertStringContainsString('name="'.$field.'"', $headerForm);
        }
        $this->assertStringNotContainsString('name="media_lot"', $headerForm);
        $this->assertStringNotContainsString('name="swab_lot"', $headerForm);
        $response->assertSee("I dati condivisi si modificano nell'intestazione della sessione.", false);

        $header = [
            'sampling_session_id' => $session->id, 'environment' => 'acque',
            'sampled_on' => '2026-11-20', 'incubation_started_on' => '2026-11-21',
            'operator_name' => 'Operatore', 'cq_operator_name' => 'CQ',
            'membrane_lot' => 'M1', 'bottle_sterilization_lot' => 'S1',
            'r2a_agar_lot' => 'R1', 'r2a_agar_expires_on' => '2027-01-01',
            'coliform_agar_lot' => 'C1', 'coliform_agar_expires_on' => '2027-02-01',
            'pseudomonas_cn_lot' => 'P1', 'pseudomonas_cn_expires_on' => '2027-03-01',
            'slanetz_bartley_lot' => 'B1', 'slanetz_bartley_expires_on' => '2027-04-01',
        ];
        $this->put(route('monitoraggi.sessions.update', $session), $header)->assertSessionHasNoErrors();
        foreach (array_diff_key($header, array_flip(['sampling_session_id', 'environment'])) as $field => $value) {
            $this->assertSame($value, $session->fresh()->header[$field]);
            foreach ($checks as $check) {
                $this->assertSame($value, $check->fresh()->getAttribute($field));
            }
        }
        foreach ($checks as $index => $check) {
            $check->refresh();
            $this->assertSame('water'.($index + 1), $check->facility_name);
            $this->assertSame('Firma water'.($index + 1), $check->incubation_started_signature);
            $this->assertSame('Inc water'.($index + 1), $check->r2a_incubator_code);
        }
        $this->assertSame('Dato precedente della sessione', $session->fresh()->header['facility_name']);

        $check = $checks[0];
        $this->patch(route('monitoraggi.checks.update', [$check->section, $check]), [
            'sampled_on' => '2026-11-20', 'facility_name' => 'Nuovo stabilimento',
            'incubation_started_signature' => 'Nuova firma', 'r2a_incubator_code' => 'Nuovo incubatore',
        ])->assertSessionHasNoErrors();
        $check->refresh();
        $this->assertSame('Nuovo stabilimento', $check->facility_name);
        $this->assertSame('Nuova firma', $check->incubation_started_signature);
        $this->assertSame('Nuovo incubatore', $check->r2a_incubator_code);
        $this->assertSame('R1', $check->r2a_agar_lot);
        $this->assertSame('water2', $checks[1]->fresh()->facility_name);

        $newSection = MonitoringSection::query()->create([
            'code' => 'water3', 'name' => 'water3', 'environment' => 'acque', 'is_active' => true,
        ]);
        $this->get(route('monitoraggi.index', [
            'view' => 'nuovo', 'env' => 'acque', 'session' => $session->id,
        ]))->assertOk()->assertSee('value="R1"', false);
        $this->post(route('monitoraggi.checks.store', $newSection), [
            'sampling_session_id' => $session->id,
            'facility_name' => 'Terzo stabilimento',
            'sampled_time' => '10:30',
            'incubation_started_signature' => 'Firma terza sezione',
        ])->assertSessionHasNoErrors();
        $newCheck = $newSection->checks()->firstOrFail();
        $this->assertSame('Terzo stabilimento', $newCheck->facility_name);
        $this->assertSame('10:30', $newCheck->sampled_time);
        $this->assertSame('Firma terza sezione', $newCheck->incubation_started_signature);
        $this->assertSame('R1', $newCheck->r2a_agar_lot);

        $invalidHeader = array_merge($header, ['r2a_agar_expires_on' => 'not-a-date']);
        $this->put(route('monitoraggi.sessions.update', $session), $invalidHeader)
            ->assertSessionHasErrors('r2a_agar_expires_on');
        $this->assertSame('2027-01-01', $session->fresh()->header['r2a_agar_expires_on']);
    }

    public function test_other_session_headers_keep_their_existing_lot_fields(): void
    {
        $operator = User::factory()->create(['role' => 'operatore']);
        $response = $this->actingAs($operator)
            ->get(route('monitoraggi.index', ['view' => 'nuovo', 'env' => 'produzione']))
            ->assertOk();
        $response->assertSee('id="session_media_lot"', false)
            ->assertSee('id="session_swab_lot"', false)
            ->assertDontSee('id="session_membrane_lot"', false);
    }

    public function test_water_operator_sees_only_analyses_enabled_for_each_point(): void
    {
        $operator = User::factory()->create(['role' => 'operatore']);
        $section = MonitoringSection::query()->create([
            'code' => 'water', 'name' => 'Acque', 'environment' => 'acque', 'is_active' => true,
        ]);
        $point = $section->samplingPoints()->create([
            'title' => 'Punto acqua', 'sample_kind' => 'water',
            'water_analyses' => ['appearance', 'ph', 'r2a'], 'is_active' => true,
        ]);

        $response = $this->actingAs($operator)
            ->get(route('monitoraggi.index', ['view' => 'nuovo', 'env' => 'acque']))
            ->assertOk();
        $dialog = explode('</dialog>', explode('id="water_point_'.$section->id.'_'.$point->id.'"', $response->getContent())[1])[0];
        $this->assertStringContainsString('<option value="appearance">Aspetto</option>', $dialog);
        $this->assertStringContainsString('<option value="ph">pH</option>', $dialog);
        $this->assertStringContainsString('<option value="r2a">R2A</option>', $dialog);
        $this->assertStringNotContainsString('<option value="coliforms">', $dialog);
        $this->assertStringNotContainsString('coliform_plate_cfu', $dialog);
        $this->assertStringContainsString('type="checkbox" data-water-appearance', $dialog);
        $this->assertStringContainsString('step="0.1"', $dialog);
        $this->assertStringContainsString('data-water-calculation-result', $dialog);
    }

    public function test_water_results_are_calculated_saved_and_preserved_when_analyses_are_disabled(): void
    {
        $operator = User::factory()->create(['role' => 'operatore']);
        $section = MonitoringSection::query()->create([
            'code' => 'water', 'name' => 'Acque', 'environment' => 'acque', 'is_active' => true,
        ]);
        $point = $section->samplingPoints()->create([
            'title' => 'Punto acqua', 'sample_kind' => 'water', 'is_active' => true,
        ]);
        $session = SamplingSession::query()->create([
            'id' => (string) Str::uuid(), 'environment' => 'acque',
            'header' => ['sampled_on' => '2026-11-19'], 'created_by_user_id' => $operator->id,
        ]);
        $values = [
            'appearance_present' => '1', 'appearance_result' => ['conforme'], 'ph_value' => '6.4',
            'aerobic_plate_cfu' => '123', 'r2a_cfu_per_ml' => '999',
            'r2a_incubation_finished_on' => '2026-11-25',
            'coliform_plate_cfu' => '24', 'coliform_confirmed_cfu' => '11',
            'coliform_incubation_finished_on' => '2026-11-26',
            'pseudomonas_plate_cfu' => '12', 'pseudomonas_confirmed_cfu' => '0',
            'pseudomonas_incubation_finished_on' => '2026-11-27',
            'enterococci_plate_cfu' => '99', 'enterococci_confirmed_cfu' => '105',
            'enterococci_incubation_finished_on' => '2026-11-28',
        ];
        $this->actingAs($operator)->post(route('monitoraggi.checks.store', $section), [
            'sampling_session_id' => $session->id, 'points' => [$point->id => $values],
        ])->assertSessionHasNoErrors();

        $check = MicrobiologicalCheck::query()->firstOrFail();
        $result = $check->pointResults()->firstOrFail();
        $this->assertEquals(1.23, $result->r2a_cfu_per_ml);
        $this->assertEquals(0.11, $result->coliform_cfu_per_ml);
        $this->assertEquals(0, $result->pseudomonas_cfu_per_ml);
        $this->assertEquals(1.05, $result->enterococci_cfu_per_ml);
        $this->assertSame('2026-11-25', $result->r2a_incubation_finished_on);
        $this->assertSame('2026-11-28', $result->enterococci_incubation_finished_on);
        $this->assertSame('conforme', $result->appearance_result);
        $this->assertSame('6.4', $result->ph_value);

        $result->update(['coliform_cfu_per_100ml' => 77]);
        $point->update(['water_analyses' => ['ph']]);
        $this->patch(route('monitoraggi.checks.update', [$section, $check]), [
            'sampled_on' => '2026-11-19',
            'points' => [$point->id => ['ph_value' => '7.1', 'coliform_confirmed_cfu' => '200']],
        ])->assertSessionHasNoErrors();
        $result->refresh();
        $this->assertSame('7.1', $result->ph_value);
        $this->assertEquals(11, $result->coliform_confirmed_cfu);
        $this->assertEquals(0.11, $result->coliform_cfu_per_ml);
        $this->assertEquals(77, $result->coliform_cfu_per_100ml);
        $this->assertSame('conforme', $result->appearance_result);

        $this->get(route('monitoraggi.index', ['view' => 'archivio', 'env' => 'acque']))
            ->assertOk()->assertSee('25/11/2026')->assertSee('UFC/100 ml');

        $point->update(['water_analyses' => ['appearance', 'coliforms']]);
        $this->patch(route('monitoraggi.checks.update', [$section, $check]), [
            'sampled_on' => '2026-11-19',
            'points' => [$point->id => [
                'appearance_present' => '1', 'coliform_confirmed_cfu' => '',
            ]],
        ])->assertSessionHasNoErrors();
        $result->refresh();
        $this->assertNull($result->appearance_result);
        $this->assertNull($result->coliform_confirmed_cfu);
        $this->assertNull($result->coliform_cfu_per_ml);
        $this->assertEquals(77, $result->coliform_cfu_per_100ml);
    }

    public function test_water_results_reject_invalid_values_and_multiple_appearance_choices(): void
    {
        $operator = User::factory()->create(['role' => 'operatore']);
        $section = MonitoringSection::query()->create([
            'code' => 'water', 'name' => 'Acque', 'environment' => 'acque', 'is_active' => true,
        ]);
        $point = $section->samplingPoints()->create([
            'title' => 'Punto acqua', 'sample_kind' => 'water', 'is_active' => true,
        ]);
        $session = SamplingSession::query()->create([
            'id' => (string) Str::uuid(), 'environment' => 'acque',
            'header' => ['sampled_on' => '2026-11-19'], 'created_by_user_id' => $operator->id,
        ]);

        $this->actingAs($operator)->post(route('monitoraggi.checks.store', $section), [
            'sampling_session_id' => $session->id,
            'points' => [$point->id => [
                'appearance_present' => '1', 'appearance_result' => ['conforme', 'non_conforme'],
                'ph_value' => '6.44', 'aerobic_plate_cfu' => '1.5',
                'coliform_confirmed_cfu' => '-1', 'r2a_incubation_finished_on' => '25/11/2026',
            ]],
        ])->assertSessionHasErrors([
            "points.{$point->id}.appearance_result", "points.{$point->id}.ph_value",
            "points.{$point->id}.aerobic_plate_cfu", "points.{$point->id}.coliform_confirmed_cfu",
            "points.{$point->id}.r2a_incubation_finished_on",
        ]);
        $this->assertDatabaseCount('microbiological_checks', 0);
    }

    public function test_sampling_can_be_saved_signed_and_reopened_for_every_section(): void
    {
        $user = User::factory()->create(['role' => 'operatore']);
        $session = SamplingSession::query()->create([
            'id' => (string) Str::uuid(),
            'environment' => 'produzione',
            'header' => ['sampled_on' => '2026-09-14'],
            'created_by_user_id' => $user->id,
        ]);
        $checks = [];

        foreach (['first', 'second'] as $code) {
            $section = MonitoringSection::query()->create([
                'code' => $code,
                'name' => $code,
                'environment' => 'produzione',
                'is_active' => true,
                'sort_order' => count($checks) + 1,
            ]);
            $point = SamplingPoint::query()->create([
                'monitoring_section_id' => $section->id,
                'title' => $code,
                'sample_kind' => 'air_active',
                'is_active' => true,
            ]);
            $checks[$section->id] = [
                'notes' => '',
                'points' => [$point->id => ['sampled_at' => null]],
            ];
        }

        $url = route('monitoraggi.sessions.phase.update', $session);
        $payload = ['entry_phase' => 'sampling', 'checks' => $checks];

        $this->actingAs($user)->post($url, $payload)
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseCount('microbiological_checks', 2);
        $this->assertDatabaseCount('microbiological_check_phase_states', 0);
        $this->assertDatabaseCount('microbiological_check_phase_logs', 2);

        $this->post($url, array_merge($payload, ['sign_phase' => '1']))
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        foreach (MicrobiologicalCheck::query()->get() as $check) {
            $this->assertDatabaseHas('microbiological_check_phase_states', [
                'microbiological_check_id' => $check->id,
                'phase' => 'sampling',
                'signed_by_user_id' => $user->id,
                'reopened_at' => null,
            ]);
        }

        $this->put(route('monitoraggi.sessions.update', $session), [
            'sampling_session_id' => $session->id,
            'environment' => 'produzione',
            'sampled_on' => '2026-09-14',
            'operator_name' => 'Nome non salvato',
        ])->assertSessionHasErrors('sampling_session_id');
        $this->assertNull(SamplingSession::query()->findOrFail($session->id)->header['operator_name'] ?? null);

        $this->get(route('monitoraggi.index', [
            'view' => 'nuovo',
            'env' => 'produzione',
            'phase' => 'sampling',
            'session' => $session->id,
        ]))
            ->assertOk()
            ->assertSee('Intestazione bloccata dopo la firma del campionamento.')
            ->assertDontSee('>Salva intestazione<', false);

        $this->post($url, array_merge($payload, [
            'reopen_phase' => '1',
            'reopening_reason' => 'Correzione orari',
        ]))
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        foreach (MicrobiologicalCheck::query()->with('phaseStates')->get() as $check) {
            $state = $check->phaseStates->firstWhere('phase', 'sampling');
            $this->assertNotNull($state->reopened_at);
            $this->assertSame($user->id, $state->reopened_by_user_id);
            $this->assertSame('Correzione orari', $state->reopening_reason);
        }

        $this->get(route('monitoraggi.index', [
            'view' => 'nuovo',
            'env' => 'produzione',
            'phase' => 'sampling',
            'session' => $session->id,
        ]))
            ->assertOk()
            ->assertSee('data-session-save', false)
            ->assertSee('data-session-has-editable-sections="true"', false)
            ->assertSee('data-batch-section-id', false);

        $this->assertDatabaseCount('microbiological_checks', 2);
        $this->assertDatabaseCount('microbiological_check_phase_logs', 6);
    }

    public function test_archive_separates_sessions_on_the_same_date_and_legacy_checks(): void
    {
        $user = User::factory()->create(['role' => 'operatore']);
        $section = MonitoringSection::query()->create([
            'code' => 'archive',
            'name' => 'Reparto test',
            'environment' => 'produzione',
            'is_active' => true,
        ]);
        $otherSection = MonitoringSection::query()->create([
            'code' => 'archive-other',
            'name' => 'Altro reparto',
            'environment' => 'produzione',
            'is_active' => true,
        ]);
        $checks = [];

        foreach ([true, false, null, null] as $complete) {
            $session = $complete === null ? null : SamplingSession::query()->create([
                'id' => (string) Str::uuid(),
                'environment' => 'produzione',
                'header' => ['sampled_on' => '2026-09-29'],
                'created_by_user_id' => $user->id,
            ]);
            $otherCheck = $session ? MicrobiologicalCheck::query()->create([
                'monitoring_section_id' => $otherSection->id,
                'sampling_session_id' => $session->id,
                'sampled_on' => '2026-09-29',
                'created_by_user_id' => $user->id,
            ]) : null;
            $check = MicrobiologicalCheck::query()->create([
                'monitoring_section_id' => $section->id,
                'sampling_session_id' => $session?->id,
                'sampled_on' => '2026-09-29',
                'created_by_user_id' => $user->id,
            ]);

            if ($complete) {
                foreach ([$check, $otherCheck] as $signedCheck) {
                    foreach (['sampling', 'reading_1', 'reading_2'] as $phase) {
                        $signedCheck->phaseStates()->create([
                            'phase' => $phase,
                            'signed_by_user_id' => $user->id,
                            'signed_at' => now(),
                        ]);
                    }
                }
                $point = SamplingPoint::query()->create([
                    'monitoring_section_id' => $section->id,
                    'title' => 'Punto test',
                ]);
                $check->pointResults()->create([
                    'sampling_point_id' => $point->id,
                    'sampled_at' => '09:00',
                ]);
            }

            $checks[] = $check;
        }

        $response = $this->actingAs($user)->get(route('monitoraggi.index', [
            'view' => 'archivio',
            'env' => 'produzione',
        ]));
        $response->assertOk();
        $this->assertSame(1, $response->viewData('archiveChecks')->total());

        $document = new \DOMDocument;
        $previous = libxml_use_internal_errors(true);
        try {
            $document->loadHTML('<?xml encoding="UTF-8">'.$response->getContent());
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
        $xpath = new \DOMXPath($document);
        $cards = $xpath->query('//div[contains(concat(" ", normalize-space(@class), " "), " archive-item-sampled ")]');
        $this->assertCount(4, $cards);

        foreach ($checks as $index => $check) {
            $editUrl = route('monitoraggi.index', array_filter([
                'view' => 'nuovo',
                'env' => 'produzione',
                'edit_check' => $check->id,
                'session' => $check->sampling_session_id,
            ]));
            $links = $xpath->query('.//a[@class="archive-edit-button"]', $cards->item(3 - $index));
            $this->assertCount(1, $links);
            $this->assertSame($editUrl, $links->item(0)->getAttribute('href'));
            $text = $cards->item(3 - $index)->textContent;
            $this->assertStringContainsString($index === 0 ? 'Completo' : 'In attesa prima lettura', $text);
            $this->assertStringContainsString($index === 0 ? '1 punti compilati' : '0 punti compilati', $text);
            $this->assertStringContainsString(
                $check->sampling_session_id ? 'ID sessione: '.$check->sampling_session_id : 'ID campionamento: '.$check->id,
                $text
            );
            $tags = $xpath->query('.//span[@class="archive-section-tag"]', $cards->item(3 - $index));
            $this->assertCount($check->sampling_session_id ? 2 : 1, $tags);
        }
    }

    public function test_production_swab_reading_uses_growth_result_selection(): void
    {
        $user = User::factory()->create(['role' => 'operatore']);
        $session = SamplingSession::query()->create([
            'id' => (string) Str::uuid(),
            'environment' => 'produzione',
            'header' => ['sampled_on' => '2026-10-27'],
            'created_by_user_id' => $user->id,
        ]);
        $section = MonitoringSection::query()->create([
            'code' => 'production-swabs',
            'name' => 'Swab produzione',
            'environment' => 'produzione',
            'is_active' => true,
        ]);
        $department = $section->departments()->create([
            'code' => 'swabs',
            'name' => 'Swab',
            'readings_count' => 2,
            'is_active' => true,
        ]);
        $swab = SamplingPoint::query()->create([
            'monitoring_section_id' => $section->id,
            'monitoring_department_id' => $department->id,
            'legacy_code' => '73',
            'title' => 'Punto swab',
            'sample_kind' => 'surface_swab',
            'is_active' => true,
        ]);
        $contactPlate = SamplingPoint::query()->create([
            'monitoring_section_id' => $section->id,
            'monitoring_department_id' => $department->id,
            'legacy_code' => '86',
            'title' => 'Punto contact plate',
            'sample_kind' => 'surface_contact',
            'is_active' => true,
        ]);
        $check = MicrobiologicalCheck::query()->create([
            'monitoring_section_id' => $section->id,
            'sampling_session_id' => $session->id,
            'sampled_on' => '2026-10-27',
            'created_by_user_id' => $user->id,
        ]);
        $check->pointResults()->create([
            'sampling_point_id' => $swab->id,
            'sampled_at' => '09:00',
        ]);

        $response = $this->actingAs($user)->get(route('monitoraggi.index', [
            'view' => 'nuovo',
            'env' => 'produzione',
            'phase' => 'reading_1',
            'session' => $session->id,
        ]));

        $response->assertOk()
            ->assertSee('name="points['.$swab->id.'][reading_growth_result]"', false)
            ->assertSee('>Crescita<', false)
            ->assertSee('>Non crescita<', false)
            ->assertDontSee('Punto contact plate')
            ->assertDontSee('name="points['.$contactPlate->id.'][reading_cfu_count]"', false);
    }

    public function test_production_reading_cannot_create_a_result_for_an_unsampled_point(): void
    {
        $user = User::factory()->create(['role' => 'operatore']);
        $session = SamplingSession::query()->create([
            'id' => (string) Str::uuid(),
            'environment' => 'produzione',
            'header' => ['sampled_on' => '2026-10-27'],
            'created_by_user_id' => $user->id,
        ]);
        $section = MonitoringSection::query()->create([
            'code' => 'production-reading-validation',
            'name' => 'Letture produzione',
            'environment' => 'produzione',
            'is_active' => true,
        ]);
        $department = $section->departments()->create([
            'code' => 'surface',
            'name' => 'Superfici',
            'readings_count' => 2,
            'is_active' => true,
        ]);
        $sampledPoint = SamplingPoint::query()->create([
            'monitoring_section_id' => $section->id,
            'monitoring_department_id' => $department->id,
            'title' => 'Punto campionato',
            'sample_kind' => 'surface_contact',
            'is_active' => true,
        ]);
        $unsampledPoint = SamplingPoint::query()->create([
            'monitoring_section_id' => $section->id,
            'monitoring_department_id' => $department->id,
            'title' => 'Punto non campionato',
            'sample_kind' => 'surface_contact',
            'is_active' => true,
        ]);
        $check = MicrobiologicalCheck::query()->create([
            'monitoring_section_id' => $section->id,
            'sampling_session_id' => $session->id,
            'sampled_on' => '2026-10-27',
            'created_by_user_id' => $user->id,
        ]);
        $check->pointResults()->create([
            'sampling_point_id' => $sampledPoint->id,
            'sampled_at' => '09:00',
        ]);
        $check->phaseStates()->create([
            'phase' => 'sampling',
            'signed_by_user_id' => $user->id,
            'signed_at' => now(),
        ]);

        $this->actingAs($user)->post(route('monitoraggi.sessions.phase.update', $session), [
            'entry_phase' => 'reading_1',
            'checks' => [
                $section->id => [
                    'points' => [
                        $sampledPoint->id => ['reading_cfu_count' => 4],
                        $unsampledPoint->id => ['reading_cfu_count' => 7],
                    ],
                ],
            ],
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('microbiological_check_points', [
            'microbiological_check_id' => $check->id,
            'sampling_point_id' => $sampledPoint->id,
            'first_cfu_count' => 4,
        ]);
        $this->assertDatabaseMissing('microbiological_check_points', [
            'microbiological_check_id' => $check->id,
            'sampling_point_id' => $unsampledPoint->id,
        ]);
    }

    public function test_production_reading_without_sampled_points_shows_an_empty_state(): void
    {
        $user = User::factory()->create(['role' => 'operatore']);
        $session = SamplingSession::query()->create([
            'id' => (string) Str::uuid(),
            'environment' => 'produzione',
            'header' => ['sampled_on' => '2026-10-27'],
            'created_by_user_id' => $user->id,
        ]);
        $section = MonitoringSection::query()->create([
            'code' => 'production-empty-reading',
            'name' => 'Nessun punto campionato',
            'environment' => 'produzione',
            'is_active' => true,
        ]);
        $department = $section->departments()->create([
            'code' => 'surface',
            'name' => 'Superfici',
            'readings_count' => 2,
            'is_active' => true,
        ]);
        SamplingPoint::query()->create([
            'monitoring_section_id' => $section->id,
            'monitoring_department_id' => $department->id,
            'title' => 'Punto da non mostrare',
            'sample_kind' => 'surface_contact',
            'is_active' => true,
        ]);
        MicrobiologicalCheck::query()->create([
            'monitoring_section_id' => $section->id,
            'sampling_session_id' => $session->id,
            'sampled_on' => '2026-10-27',
            'created_by_user_id' => $user->id,
        ]);

        $this->actingAs($user)->get(route('monitoraggi.index', [
            'view' => 'nuovo',
            'env' => 'produzione',
            'phase' => 'reading_1',
            'session' => $session->id,
        ]))
            ->assertOk()
            ->assertSee('Non risultano campionamenti effettuati.')
            ->assertDontSee('Punto da non mostrare')
            ->assertDontSee('UFC/m3 (lettura 1)');
    }

    public function test_production_first_reading_can_be_saved_without_sampled_points(): void
    {
        $user = User::factory()->create(['role' => 'operatore']);
        $session = SamplingSession::query()->create([
            'id' => (string) Str::uuid(),
            'environment' => 'produzione',
            'header' => ['sampled_on' => '2026-10-27'],
            'created_by_user_id' => $user->id,
        ]);
        $section = MonitoringSection::query()->create([
            'code' => 'production-empty-first-reading',
            'name' => 'Nessun punto campionato',
            'environment' => 'produzione',
            'is_active' => true,
        ]);
        $department = $section->departments()->create([
            'code' => 'surface',
            'name' => 'Superfici',
            'readings_count' => 2,
            'is_active' => true,
        ]);
        SamplingPoint::query()->create([
            'monitoring_section_id' => $section->id,
            'monitoring_department_id' => $department->id,
            'title' => 'Punto da non mostrare',
            'sample_kind' => 'surface_contact',
            'is_active' => true,
        ]);
        $check = MicrobiologicalCheck::query()->create([
            'monitoring_section_id' => $section->id,
            'sampling_session_id' => $session->id,
            'sampled_on' => '2026-10-27',
            'created_by_user_id' => $user->id,
        ]);
        $check->phaseStates()->create([
            'phase' => 'sampling',
            'signed_by_user_id' => $user->id,
            'signed_at' => now(),
        ]);

        $this->actingAs($user)->post(route('monitoraggi.sessions.phase.update', $session), [
            'entry_phase' => 'reading_1',
            'checks' => [
                $section->id => [],
            ],
        ])->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseCount('microbiological_check_points', 0);
    }

    public function test_admin_can_upload_a_production_department_image_visible_to_operators(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $operator = User::factory()->create(['role' => 'operatore']);
        $section = MonitoringSection::query()->create([
            'code' => 'reparto-con-foto',
            'name' => 'Reparto con foto',
            'environment' => 'produzione',
            'is_active' => true,
        ]);

        $this->actingAs($admin)->patch(route('monitoraggi.sections.update', $section), [
            'name' => $section->name,
            'code' => $section->code,
            'image' => UploadedFile::fake()->image('reparto.jpg', 120, 80),
        ])->assertSessionHasNoErrors()
            ->assertRedirect();

        $imagePath = $section->fresh()->image_path;

        $this->assertNotNull($imagePath);
        Storage::disk('public')->assertExists($imagePath);

        $this->actingAs($operator)->get(route('monitoraggi.index', [
            'view' => 'nuovo',
            'env' => 'produzione',
        ]))
            ->assertOk()
            ->assertSee(Storage::disk('public')->url($imagePath), false)
            ->assertSee('Foto del reparto Reparto con foto', false);
    }
}
