<?php

namespace Tests\Feature;

use App\Models\MicrobiologicalCheck;
use App\Models\MonitoringSection;
use App\Models\SamplingOperator;
use App\Models\SamplingPoint;
use App\Models\SamplingSession;
use App\Models\SamplingUniform;
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
            '2026_11_24_000001_add_operator_sampling_details',
            '2026_11_24_000002_create_sampling_uniforms_table',
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
            ->assertDontSee('id="session_product_batch"', false)
            ->assertDontSee('id="session_membrane_lot"', false);
    }

    public function test_admin_can_open_operator_environment_without_a_sampling_session(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        MonitoringSection::query()->create([
            'code' => 'operatori_slide',
            'name' => 'Operatori - Slide',
            'environment' => 'operatori',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('monitoraggi.index', ['env' => 'operatori']))
            ->assertOk()
            ->assertSee('Operatori - Slide')
            ->assertDontSee('class="operator-session-form"', false)
            ->assertSee('La compilazione del campionamento e consentita solo agli utenti con ruolo operatore.');
    }

    public function test_operator_sampling_uses_one_section_picker_and_saves_operator_details(): void
    {
        $operator = User::factory()->create(['role' => 'operatore']);
        $session = SamplingSession::query()->create([
            'id' => (string) Str::uuid(),
            'environment' => 'operatori',
            'header' => ['sampled_on' => '2026-11-24'],
            'created_by_user_id' => $operator->id,
        ]);
        $sections = collect(['Enteropluri-Test', 'Slide'])->map(function (string $name): MonitoringSection {
            $section = MonitoringSection::query()->create([
                'code' => str_replace('-', '_', strtolower($name)),
                'name' => 'Operatori - '.$name,
                'environment' => 'operatori',
                'is_active' => true,
            ]);
            $section->samplingPoints()->create([
                'legacy_code' => '501',
                'title' => 'Mano destra',
                'sample_kind' => 'surface_contact',
                'requires_operational_status' => false,
                'requires_product_lot' => false,
                'is_active' => true,
            ]);

            return $section;
        });
        $sampledOperator = SamplingOperator::query()->where('name', 'PAMELA ASSOGNA')->firstOrFail();

        $response = $this->actingAs($operator)->get(route('monitoraggi.index', [
            'view' => 'nuovo',
            'env' => 'operatori',
            'session' => $session->id,
        ]))->assertOk();
        $response->assertSee('data-operator-section-select', false)
            ->assertSee('name="operator_section"', false)
            ->assertSee('Operatore campionato')
            ->assertSee('Divisa indossata')
            ->assertSee('Tuta blu')
            ->assertSee('PAMELA ASSOGNA')
            ->assertSee('name="notes" value=""', false)
            ->assertDontSee('Visualizza firme')
            ->assertDontSee('<details class="section"', false)
            ->assertDontSee('Firma campionatore')
            ->assertDontSee('Inizio incubazione');
        $page = $response->getContent();
        $this->assertSame(2, substr_count($page, 'data-session-phase-form data-session-has-editable-sections='));
        $this->assertSame(2, substr_count($page, 'data-batch-section-id='));
        $this->assertGreaterThan(strpos($page, 'name="sampled_operator_id"'), strpos($page, 'data-session-phase-form data-session-has-editable-sections='));
        foreach ($sections as $section) {
            $response->assertSee('data-operator-section-panel="'.$section->id.'"', false)
                ->assertSee('name="operator_section" value="'.$section->id.'"', false)
                ->assertSee('data-batch-section-id="'.$section->id.'"', false);
        }

        $header = [
            'sampling_session_id' => $session->id,
            'environment' => 'operatori',
            'sampled_on' => '2026-11-24',
            'cq_operator_name' => 'Operatore CQ',
            'media_lot' => 'PL-123',
            'product_batch' => 'LOT-456',
            'sampled_operator_id' => $sampledOperator->id,
            'operator_uniform' => 'Tuta blu',
            'operator_section' => $sections->last()->id,
        ];
        $this->put(route('monitoraggi.sessions.update', $session), $header)->assertSessionHasNoErrors();
        $this->assertSame($sections->last()->id, $session->fresh()->header['operator_section']);

        $this->post(route('monitoraggi.checks.store', $sections->first()), [
            'sampling_session_id' => $session->id,
        ])->assertSessionHasErrors('sampling_session_id');

        $this->post(route('monitoraggi.sessions.phase.update', $session), [
            'entry_phase' => 'sampling',
            'operator_section' => $sections->last()->id,
            'checks' => $sections->mapWithKeys(fn (MonitoringSection $section): array => [$section->id => []])->all(),
        ])->assertSessionHasErrors('checks');

        $this->post(route('monitoraggi.sessions.phase.update', $session), [
            'entry_phase' => 'sampling',
            'operator_section' => $sections->last()->id,
            'checks' => [$sections->last()->id => []],
        ])->assertSessionHasNoErrors();

        $this->assertSame(0, $sections->first()->checks()->count());
        $check = $sections->last()->checks()->firstOrFail();
        $this->assertSame($session->id, $check->sampling_session_id);
        $this->assertSame($sampledOperator->id, $check->sampled_operator_id);
        $this->assertSame('Tuta blu', $check->operator_uniform);
        $this->assertSame('PL-123', $check->media_lot);
        $this->assertSame('LOT-456', $check->product_batch);
        $this->assertSame('Operatore CQ', $check->cq_operator_name);
        $this->assertSame(1, $check->pointResults()->count());
        $this->post(route('monitoraggi.checks.store', $sections->last()), [
            'sampling_session_id' => $session->id,
        ])->assertSessionHasErrors('sampling_session_id');

        $this->assertSame($sampledOperator->id, $session->fresh()->header['sampled_operator_id']);
        $this->assertSame('Tuta blu', $session->fresh()->header['operator_uniform']);
        $this->put(route('monitoraggi.sessions.update', $session), array_merge($header, [
            'operator_section' => $sections->first()->id,
            'media_lot' => 'MODIFIED',
        ]))->assertSessionHasErrors('sampling_session_id');
        $this->assertSame($sections->last()->id, $session->fresh()->header['operator_section']);
        $this->assertSame('PL-123', $session->fresh()->header['media_lot']);
        $this->get(route('monitoraggi.index', [
            'view' => 'nuovo',
            'env' => 'operatori',
            'phase' => 'reading_1',
            'session' => $session->id,
            'operator_section' => $sections->first()->id,
        ]))->assertOk()
            ->assertSee('Intestazione e reparto bloccati dopo il salvataggio della fase di campionamento.')
            ->assertSee('<fieldset disabled', false);

        $this->get(route('monitoraggi.index', [
            'view' => 'nuovo',
            'env' => 'operatori',
            'phase' => 'reading_1',
            'session' => $session->id,
            'operator_section' => $sections->last()->id,
        ]))->assertOk()
            ->assertSee('UFC Piastra 1a Lettura')
            ->assertSee('Mano destra (1)')
            ->assertDontSee('<details class="section"', false);

        $secondSession = SamplingSession::query()->create([
            'id' => (string) Str::uuid(),
            'environment' => 'operatori',
            'header' => ['sampled_on' => '2026-11-24'],
            'created_by_user_id' => $operator->id,
        ]);
        $secondHeader = array_merge($header, [
            'sampling_session_id' => $secondSession->id,
            'operator_section' => $sections->first()->id,
        ]);
        $this->put(route('monitoraggi.sessions.update', $secondSession), $secondHeader)->assertSessionHasNoErrors();
        $this->post(route('monitoraggi.sessions.phase.update', $secondSession), [
            'entry_phase' => 'sampling',
            'operator_section' => $sections->first()->id,
            'checks' => [$sections->first()->id => []],
        ])->assertSessionHasNoErrors();

        $firstDepartmentCheck = $sections->first()->checks()->firstOrFail();
        $this->assertSame($secondSession->id, $firstDepartmentCheck->sampling_session_id);
        $this->assertNotSame($check->sampling_session_id, $firstDepartmentCheck->sampling_session_id);

        $this->get(route('monitoraggi.index', ['view' => 'archivio', 'env' => 'operatori']))
            ->assertOk()
            ->assertSee('2 registrazioni')
            ->assertSee('Registrazione reparto');
    }

    public function test_admin_can_delete_a_whole_session_in_every_environment(): void
    {
        $operator = User::factory()->create(['role' => 'operatore']);
        $admin = User::factory()->create(['role' => 'admin']);

        foreach (['produzione', 'clean_room', 'acque', 'operatori'] as $environment) {
            $section = MonitoringSection::query()->create([
                'code' => 'archive-'.$environment,
                'name' => 'Sezione '.$environment,
                'environment' => $environment,
                'sub_environment' => $environment === 'clean_room' ? 's1' : null,
                'is_active' => true,
            ]);
            $session = SamplingSession::query()->create([
                'id' => (string) Str::uuid(),
                'environment' => $environment,
                'sub_environment' => $environment === 'clean_room' ? 's1' : null,
                'header' => ['sampled_on' => '2026-11-24'],
                'created_by_user_id' => $operator->id,
            ]);
            $check = MicrobiologicalCheck::query()->create([
                'monitoring_section_id' => $section->id,
                'sampling_session_id' => $session->id,
                'sampled_on' => '2026-11-24',
                'created_by_user_id' => $operator->id,
            ]);
            $otherSection = MonitoringSection::query()->create([
                'code' => 'archive-other-'.$environment,
                'name' => 'Altra sezione '.$environment,
                'environment' => $environment,
                'sub_environment' => $environment === 'clean_room' ? 's1' : null,
                'is_active' => true,
            ]);
            $signedCheck = MicrobiologicalCheck::query()->create([
                'monitoring_section_id' => $otherSection->id,
                'sampling_session_id' => $session->id,
                'sampled_on' => '2026-11-23',
                'created_by_user_id' => $operator->id,
            ]);
            $signedCheck->phaseStates()->create([
                'phase' => 'reading_1',
                'signed_by_user_id' => $operator->id,
                'signed_at' => now(),
            ]);
            $unrelated = MicrobiologicalCheck::query()->create([
                'monitoring_section_id' => $section->id,
                'sampled_on' => '2026-11-24',
                'created_by_user_id' => $operator->id,
            ]);
            $previouslyDeleted = MicrobiologicalCheck::query()->create([
                'monitoring_section_id' => $section->id,
                'sampling_session_id' => $session->id,
                'sampled_on' => '2026-11-22',
                'created_by_user_id' => $operator->id,
            ]);
            $previouslyDeleted->delete();
            $archive = ['view' => 'archivio', 'env' => $environment];

            $this->actingAs($operator)->get(route('monitoraggi.index', $archive))
                ->assertOk()
                ->assertDontSee('action="'.route('monitoraggi.checks.delete', $check).'"', false)
                ->assertDontSee('Gestione amministrativa');
            $this->delete(route('monitoraggi.checks.delete', $check))->assertForbidden();
            $this->actingAs($admin)->get(route('monitoraggi.index', $archive))
                ->assertOk()
                ->assertSee('Gestione amministrativa')
                ->assertSee('action="'.route('monitoraggi.checks.delete', $check).'"', false)
                ->assertSee('Elimina intera sessione');

            $this->delete(route('monitoraggi.checks.delete', $check))
                ->assertSessionHasErrors('deletion_reason');
            $this->assertNotSoftDeleted($check);
            $this->assertNotSoftDeleted($signedCheck);
            $this->delete(route('monitoraggi.checks.delete', $check), ['deletion_reason' => 'Inserimento errato'])
                ->assertSessionHasNoErrors();
            $this->assertSoftDeleted($check);
            $this->assertSoftDeleted($signedCheck);
            $this->assertNotSoftDeleted($unrelated);
            $this->assertDatabaseMissing('microbiological_check_phase_logs', [
                'microbiological_check_id' => $previouslyDeleted->id,
                'action' => 'soft_deleted',
            ]);
            $this->assertDatabaseHas('microbiological_check_phase_logs', [
                'microbiological_check_id' => $check->id,
                'action' => 'soft_deleted',
                'reason' => 'Inserimento errato',
                'performed_by_user_id' => $admin->id,
            ]);
            $this->assertDatabaseHas('microbiological_check_phase_logs', [
                'microbiological_check_id' => $signedCheck->id,
                'action' => 'soft_deleted',
                'reason' => 'Inserimento errato',
                'performed_by_user_id' => $admin->id,
            ]);

            $this->actingAs($operator)->get(route('monitoraggi.index', [
                'view' => 'nuovo', 'env' => $environment, 'session' => $session->id,
            ]))->assertOk()->assertSee('Le sezioni eliminate possono essere ripristinate da un admin');
            $this->put(route('monitoraggi.sessions.update', $session), [
                'sampling_session_id' => $session->id,
                'environment' => $environment,
                'sampled_on' => '2026-11-24',
                'operator_section' => $section->id,
            ])->assertSessionHasErrors('sampling_session_id');
            $this->post(route('monitoraggi.checks.store', $section), [
                'sampling_session_id' => $session->id,
            ])->assertSessionHasErrors('sampling_session_id');

            $this->actingAs($admin)->get(route('monitoraggi.index', $archive + ['archive_status' => 'deleted']))
                ->assertOk()
                ->assertSee('action="'.route('monitoraggi.checks.restore', $check).'"', false);
            $this->patch(route('monitoraggi.checks.restore', $signedCheck))->assertSessionHasNoErrors();
            $this->assertNotSoftDeleted($signedCheck);
            $this->assertSoftDeleted($check);
            $this->assertDatabaseHas('microbiological_check_phase_states', [
                'microbiological_check_id' => $signedCheck->id,
                'phase' => 'reading_1',
                'signed_by_user_id' => $operator->id,
            ]);
        }
    }

    public function test_partially_restored_session_does_not_recreate_other_deleted_sections(): void
    {
        $operator = User::factory()->create(['role' => 'operatore']);
        $admin = User::factory()->create(['role' => 'admin']);
        $session = SamplingSession::query()->create([
            'id' => (string) Str::uuid(),
            'environment' => 'produzione',
            'header' => ['sampled_on' => '2026-11-24'],
            'created_by_user_id' => $operator->id,
        ]);
        $sections = collect(['A', 'B'])->map(fn ($name) => MonitoringSection::query()->create([
            'code' => 'archive-section-'.$name,
            'name' => 'Reparto '.$name,
            'environment' => 'produzione',
            'is_active' => true,
        ]));
        $checks = $sections->map(fn ($section) => MicrobiologicalCheck::query()->create([
            'monitoring_section_id' => $section->id,
            'sampling_session_id' => $session->id,
            'sampled_on' => '2026-11-24',
            'created_by_user_id' => $operator->id,
        ]));

        $archive = $this->actingAs($admin)->get(route('monitoraggi.index', ['view' => 'archivio', 'env' => 'produzione']));
        $this->assertSame(1, substr_count($archive->getContent(), 'Elimina intera sessione'));
        $this->actingAs($admin)->delete(route('monitoraggi.checks.delete', $checks[0]))->assertSessionHasNoErrors();
        $this->assertSoftDeleted($checks[0]);
        $this->assertSoftDeleted($checks[1]);
        $this->patch(route('monitoraggi.checks.restore', $checks[1]))->assertSessionHasNoErrors();

        $this->actingAs($operator)->get(route('monitoraggi.index', ['view' => 'nuovo', 'env' => 'produzione', 'session' => $session->id]))
            ->assertOk()
            ->assertDontSee('data-batch-section-id="'.$sections[0]->id.'"', false)
            ->assertSee('data-batch-section-id="'.$sections[1]->id.'"', false);
        $this->post(route('monitoraggi.sessions.phase.update', $session), [
            'entry_phase' => 'sampling',
            'checks' => [$sections[0]->id => [], $sections[1]->id => []],
        ])->assertSessionHasErrors('checks');
        $this->post(route('monitoraggi.sessions.phase.update', $session), [
            'entry_phase' => 'sampling',
            'checks' => [$sections[1]->id => []],
        ])->assertSessionHasNoErrors();
        $this->assertSame(1, MicrobiologicalCheck::withTrashed()->where('sampling_session_id', $session->id)
            ->where('monitoring_section_id', $sections[0]->id)->count());

        $this->actingAs($admin)->patch(route('monitoraggi.checks.restore', $checks[0]))->assertSessionHasNoErrors();
        $this->actingAs($operator)->get(route('monitoraggi.index', ['view' => 'nuovo', 'env' => 'produzione', 'session' => $session->id]))
            ->assertSee('data-batch-section-id="'.$sections[0]->id.'"', false)
            ->assertSee('data-batch-section-id="'.$sections[1]->id.'"', false);
    }

    public function test_deleted_operator_session_cannot_be_reused_before_restoration(): void
    {
        $operator = User::factory()->create(['role' => 'operatore']);
        $admin = User::factory()->create(['role' => 'admin']);
        $sections = collect(['A', 'B'])->map(fn ($name) => MonitoringSection::query()->create([
            'code' => 'deleted-operator-'.$name,
            'name' => 'Operatori - '.$name,
            'environment' => 'operatori',
            'is_active' => true,
        ]));
        $session = SamplingSession::query()->create([
            'id' => (string) Str::uuid(),
            'environment' => 'operatori',
            'header' => ['sampled_on' => '2026-11-24', 'operator_section' => $sections[0]->id],
            'created_by_user_id' => $operator->id,
        ]);
        $check = MicrobiologicalCheck::query()->create([
            'monitoring_section_id' => $sections[0]->id,
            'sampling_session_id' => $session->id,
            'sampled_on' => '2026-11-24',
            'created_by_user_id' => $operator->id,
        ]);

        $this->actingAs($admin)->delete(route('monitoraggi.checks.delete', $check))->assertSessionHasNoErrors();
        $this->actingAs($operator)->get(route('monitoraggi.index', ['view' => 'nuovo', 'env' => 'operatori', 'session' => $session->id]))
            ->assertOk()
            ->assertSee('Le sezioni eliminate possono essere ripristinate da un admin')
            ->assertDontSee('<div class="operator-section-panel"', false);
        $this->put(route('monitoraggi.sessions.update', $session), [
            'sampling_session_id' => $session->id,
            'environment' => 'operatori',
            'sampled_on' => '2026-11-24',
            'operator_section' => $sections[1]->id,
        ])->assertSessionHasErrors('sampling_session_id');
        $this->post(route('monitoraggi.sessions.phase.update', $session), [
            'entry_phase' => 'sampling',
            'operator_section' => $sections[0]->id,
            'checks' => [$sections[0]->id => []],
        ])->assertSessionHasErrors('sampling_session_id');
        $this->post(route('monitoraggi.checks.store', $sections[1]), [
            'sampling_session_id' => $session->id,
        ])->assertSessionHasErrors('sampling_session_id');
    }

    public function test_restore_rejects_an_existing_active_check_for_the_same_session_section(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $section = MonitoringSection::query()->create([
            'code' => 'restore-conflict',
            'name' => 'Reparto',
            'environment' => 'produzione',
            'is_active' => true,
        ]);
        $sessionId = (string) Str::uuid();
        $attributes = [
            'monitoring_section_id' => $section->id,
            'sampling_session_id' => $sessionId,
            'sampled_on' => '2026-11-24',
        ];
        $deleted = MicrobiologicalCheck::query()->create($attributes);
        $deleted->delete();
        MicrobiologicalCheck::query()->create($attributes);

        $this->actingAs($admin)->patch(route('monitoraggi.checks.restore', $deleted))
            ->assertSessionHasErrors('check');
        $this->assertSoftDeleted($deleted);
    }

    public function test_admin_can_delete_an_unsigned_legacy_check_without_a_session(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $section = MonitoringSection::query()->create([
            'code' => 'legacy-delete',
            'name' => 'Reparto storico',
            'environment' => 'produzione',
            'is_active' => true,
        ]);
        $check = MicrobiologicalCheck::query()->create([
            'monitoring_section_id' => $section->id,
            'sampled_on' => '2026-11-24',
        ]);

        $this->actingAs($admin)->get(route('monitoraggi.index', ['view' => 'archivio', 'env' => 'produzione']))
            ->assertSee('Elimina campionamento');
        $this->delete(route('monitoraggi.checks.delete', $check))->assertSessionHasNoErrors();
        $this->assertSoftDeleted($check);
    }

    public function test_admin_can_permanently_delete_a_whole_trashed_session(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $operator = User::factory()->create(['role' => 'operatore']);
        $section = MonitoringSection::query()->create([
            'code' => 'permanent-operator',
            'name' => 'Operatori - Reparto',
            'environment' => 'operatori',
            'is_active' => true,
        ]);
        $session = SamplingSession::query()->create([
            'id' => (string) Str::uuid(),
            'environment' => 'operatori',
            'header' => ['sampled_on' => '2026-11-24'],
            'created_by_user_id' => $operator->id,
        ]);
        $checks = collect([1, 2])->map(fn () => MicrobiologicalCheck::query()->create([
            'monitoring_section_id' => $section->id,
            'sampling_session_id' => $session->id,
            'sampled_on' => '2026-11-24',
        ]));
        $point = $section->samplingPoints()->create(['title' => 'Mano', 'sample_kind' => 'surface_contact']);
        $result = $checks[0]->pointResults()->create(['sampling_point_id' => $point->id]);
        $reading = $result->readings()->create(['reading_number' => 1, 'cfu_count' => 2]);
        $state = $checks[0]->phaseStates()->create(['phase' => 'sampling', 'signed_by_user_id' => $operator->id, 'signed_at' => now()]);

        $this->actingAs($admin)->delete(route('monitoraggi.checks.delete', $checks[0]), ['deletion_reason' => 'Dati di prova'])
            ->assertSessionHasNoErrors();
        $this->get(route('monitoraggi.index', ['view' => 'archivio', 'env' => 'operatori', 'archive_status' => 'deleted']))
            ->assertSee('Eliminazione definitiva');
        $this->actingAs($operator)->delete(route('monitoraggi.checks.permanent-delete', $checks[0]))->assertForbidden();

        $this->actingAs($admin)->patch(route('monitoraggi.checks.restore', $checks[1]))->assertSessionHasNoErrors();
        $this->delete(route('monitoraggi.checks.permanent-delete', $checks[0]))->assertSessionHasErrors('check');
        $this->assertSoftDeleted($checks[0]);
        $this->delete(route('monitoraggi.checks.delete', $checks[1]))->assertSessionHasNoErrors();
        $this->delete(route('monitoraggi.checks.permanent-delete', $checks[0]))->assertSessionHasNoErrors();

        foreach ($checks as $check) {
            $this->assertDatabaseMissing('microbiological_checks', ['id' => $check->id]);
            $this->assertDatabaseMissing('microbiological_check_phase_logs', ['microbiological_check_id' => $check->id]);
        }
        $this->assertDatabaseMissing('sampling_sessions', ['id' => $session->id]);
        $this->assertDatabaseMissing('microbiological_check_points', ['id' => $result->id]);
        $this->assertDatabaseMissing('microbiological_check_readings', ['id' => $reading->id]);
        $this->assertDatabaseMissing('microbiological_check_phase_states', ['id' => $state->id]);
        $this->assertDatabaseHas('sampling_points', ['id' => $point->id]);
    }

    public function test_operator_catalog_allows_admin_to_manage_unused_entries_and_copy_a_department(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $operator = User::factory()->create(['role' => 'operatore']);
        $source = MonitoringSection::query()->create([
            'code' => 'operator-source',
            'name' => 'Operatori - Fonte',
            'environment' => 'operatori',
            'is_active' => true,
        ]);
        $department = $source->departments()->create(['name' => 'Operatori', 'readings_count' => 3, 'is_active' => true]);
        $source->samplingPoints()->create([
            'monitoring_department_id' => $department->id,
            'title' => 'Mano',
            'sample_kind' => 'surface_contact',
            'is_active' => true,
        ]);

        $this->actingAs($operator)->post(route('monitoraggi.operator-catalog.uniforms.store'), ['name' => 'Test'])->assertForbidden();
        $this->delete(route('monitoraggi.operator-catalog.sections.delete'), ['section_id' => $source->id])->assertForbidden();
        $this->actingAs($admin)->get(route('monitoraggi.index', ['view' => 'gestione-operatori', 'env' => 'operatori']))
            ->assertOk()->assertSee('Anagrafiche operatori')->assertSee('Copia punti e configurazione da');
        $this->get(route('monitoraggi.index', ['view' => 'nuovo', 'env' => 'produzione']))
            ->assertSee(route('monitoraggi.index', ['view' => 'gestione-operatori', 'env' => 'operatori']));
        $this->post(route('monitoraggi.operator-catalog.uniforms.store'), ['name' => 'Divisa nuova'])->assertSessionHasNoErrors();
        $this->post(route('monitoraggi.operator-catalog.uniforms.store'), ['name' => 'DIVISA NUOVA'])->assertSessionHasErrors('name');
        $this->post(route('monitoraggi.operator-catalog.operators.store'), ['name' => 'Operatore nuovo'])->assertSessionHasNoErrors();
        $this->post(route('monitoraggi.operator-catalog.operators.store'), ['name' => 'OPERATORE NUOVO'])->assertSessionHasErrors('name');
        $this->post(route('monitoraggi.operator-catalog.sections.store'), [
            'name' => 'Nuovo',
            'source_section_id' => $source->id,
        ])->assertSessionHasNoErrors();
        $copy = MonitoringSection::query()->where('name', 'Operatori - Nuovo')->firstOrFail();
        $copiedDepartment = $copy->departments()->firstOrFail();
        $this->assertSame(3, $copiedDepartment->readings_count);
        $this->assertDatabaseHas('sampling_points', [
            'monitoring_section_id' => $copy->id,
            'monitoring_department_id' => $copiedDepartment->id,
            'title' => 'Mano',
        ]);
        $this->post(route('monitoraggi.operator-catalog.sections.store'), [
            'name' => 'NUOVO',
            'source_section_id' => $source->id,
        ])->assertSessionHasErrors('name');
        $this->actingAs($operator)->get(route('monitoraggi.index', ['env' => 'operatori']))
            ->assertSee('Divisa nuova')->assertSee('Operatore nuovo')->assertSee('Nuovo');
        $sessionId = (string) Str::uuid();
        $newOperator = SamplingOperator::query()->where('name', 'Operatore nuovo')->firstOrFail();
        $this->put(route('monitoraggi.sessions.update', $sessionId), [
            'sampling_session_id' => $sessionId,
            'environment' => 'operatori',
            'sampled_on' => '2026-11-24',
            'operator_section' => $copy->id,
            'sampled_operator_id' => $newOperator->id,
            'operator_uniform' => 'Divisa nuova',
        ])->assertSessionHasNoErrors();
        $this->assertSame('Divisa nuova', SamplingSession::query()->findOrFail($sessionId)->header['operator_uniform']);
        SamplingSession::query()->whereKey($sessionId)->delete();

        $this->actingAs($admin)->delete(route('monitoraggi.operator-catalog.uniforms.delete'), [
            'uniform_id' => SamplingUniform::query()->where('name', 'Divisa nuova')->firstOrFail()->id,
        ])->assertSessionHasNoErrors();
        $this->delete(route('monitoraggi.operator-catalog.operators.delete'), [
            'operator_id' => SamplingOperator::query()->where('name', 'Operatore nuovo')->firstOrFail()->id,
        ])->assertSessionHasNoErrors();
        $this->delete(route('monitoraggi.operator-catalog.sections.delete'), ['section_id' => $copy->id])->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('sampling_uniforms', ['name' => 'Divisa nuova']);
        $this->assertDatabaseMissing('sampling_operators', ['name' => 'Operatore nuovo']);
        $this->assertDatabaseMissing('monitoring_sections', ['id' => $copy->id]);
        $this->assertDatabaseMissing('sampling_points', ['monitoring_section_id' => $copy->id]);
    }

    public function test_operator_catalog_refuses_to_delete_used_entries(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $operator = SamplingOperator::query()->firstOrFail();
        $uniform = SamplingUniform::query()->firstOrFail();
        $section = MonitoringSection::query()->create([
            'code' => 'operator-in-use',
            'name' => 'Operatori - Usato',
            'environment' => 'operatori',
            'is_active' => true,
        ]);
        $session = SamplingSession::query()->create([
            'id' => (string) Str::uuid(),
            'environment' => 'operatori',
            'header' => ['sampled_operator_id' => (string) $operator->id, 'operator_uniform' => $uniform->name, 'operator_section' => $section->id],
        ]);
        $this->actingAs($admin)->delete(route('monitoraggi.operator-catalog.operators.delete'), ['operator_id' => $operator->id])->assertSessionHasErrors('operator_id');
        $this->delete(route('monitoraggi.operator-catalog.uniforms.delete'), ['uniform_id' => $uniform->id])->assertSessionHasErrors('uniform_id');
        $this->delete(route('monitoraggi.operator-catalog.sections.delete'), ['section_id' => $section->id])->assertSessionHasErrors('section_id');

        $check = MicrobiologicalCheck::query()->create([
            'monitoring_section_id' => $section->id,
            'sampling_session_id' => $session->id,
            'sampled_on' => '2026-11-24',
            'sampled_operator_id' => $operator->id,
            'operator_uniform' => $uniform->name,
        ]);
        $check->delete();
        $session->delete();
        $this->delete(route('monitoraggi.operator-catalog.operators.delete'), ['operator_id' => $operator->id])->assertSessionHasErrors('operator_id');
        $this->delete(route('monitoraggi.operator-catalog.uniforms.delete'), ['uniform_id' => $uniform->id])->assertSessionHasErrors('uniform_id');
        $this->delete(route('monitoraggi.operator-catalog.sections.delete'), ['section_id' => $section->id])->assertSessionHasErrors('section_id');
        $this->assertDatabaseHas('sampling_operators', ['id' => $operator->id]);
        $this->assertDatabaseHas('sampling_uniforms', ['id' => $uniform->id]);
        $this->assertDatabaseHas('monitoring_sections', ['id' => $section->id]);
    }

    public function test_clean_room_product_batch_is_shared_with_existing_and_new_checks(): void
    {
        $operator = User::factory()->create(['role' => 'operatore']);
        $session = SamplingSession::query()->create([
            'id' => (string) Str::uuid(), 'environment' => 'clean_room', 'sub_environment' => 's1',
            'header' => ['sampled_on' => '2026-11-19'], 'created_by_user_id' => $operator->id,
        ]);
        $sections = [];
        foreach (['existing', 'phase', 'single'] as $code) {
            $sections[$code] = MonitoringSection::query()->create([
                'code' => $code, 'name' => $code, 'environment' => 'clean_room',
                'sub_environment' => 's1', 'is_active' => true,
            ]);
        }
        $existing = MicrobiologicalCheck::query()->create([
            'monitoring_section_id' => $sections['existing']->id, 'sampling_session_id' => $session->id,
            'sampled_on' => '2026-11-19', 'created_by_user_id' => $operator->id,
        ]);
        $header = [
            'sampling_session_id' => $session->id, 'environment' => 'clean_room', 'sub_environment' => 's1',
            'sampled_on' => '2026-11-19', 'product_batch' => 'LOT-123',
        ];

        $this->actingAs($operator)->put(route('monitoraggi.sessions.update', $session), $header)
            ->assertSessionHasNoErrors();
        $this->assertSame('LOT-123', $session->fresh()->header['product_batch']);
        $this->assertSame('LOT-123', $existing->fresh()->product_batch);
        $this->get(route('monitoraggi.index', [
            'view' => 'nuovo', 'env' => 'clean_room', 'sub' => 's1', 'session' => $session->id,
        ]))->assertOk()->assertSee('id="session_product_batch" type="text" name="product_batch" value="LOT-123"', false);

        $this->put(route('monitoraggi.sessions.update', $session), array_merge($header, [
            'product_batch' => str_repeat('x', 121),
        ]))->assertSessionHasErrors('product_batch');
        $this->assertSame('LOT-123', $session->fresh()->header['product_batch']);

        $this->post(route('monitoraggi.checks.store', $sections['single']), [
            'sampling_session_id' => $session->id, 'entry_phase' => 'sampling',
        ])->assertSessionHasNoErrors();
        $this->assertSame('LOT-123', $sections['single']->checks()->firstOrFail()->product_batch);

        $this->post(route('monitoraggi.sessions.phase.update', $session), [
            'entry_phase' => 'sampling',
            'checks' => [
                $sections['existing']->id => [],
                $sections['phase']->id => [],
                $sections['single']->id => [],
            ],
        ])->assertSessionHasNoErrors();
        $this->assertSame('LOT-123', $sections['phase']->checks()->firstOrFail()->product_batch);
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

    public function test_admin_can_upload_clean_room_and_water_section_images_visible_to_operators(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $operator = User::factory()->create(['role' => 'operatore']);

        foreach ([
            ['environment' => 'clean_room', 'sub_environment' => 's1', 'name' => 'Clean Room S1'],
            ['environment' => 'acque', 'sub_environment' => null, 'name' => 'Sezione Acque'],
        ] as $index => $sectionData) {
            $section = MonitoringSection::query()->create([
                'code' => 'sezione-immagine-'.$index,
                'name' => $sectionData['name'],
                'environment' => $sectionData['environment'],
                'sub_environment' => $sectionData['sub_environment'],
                'is_active' => true,
            ]);

            $this->actingAs($admin)->get(route('monitoraggi.index', [
                'view' => 'gestione-sezioni',
                'env' => $sectionData['environment'],
                'sub' => $sectionData['sub_environment'],
            ]))
                ->assertOk()
                ->assertSee('name="image"', false);

            $this->patch(route('monitoraggi.sections.update', $section), [
                'name' => $section->name,
                'code' => $section->code,
                'image' => UploadedFile::fake()->image('sezione.jpg', 120, 80),
            ])->assertSessionHasNoErrors()
                ->assertRedirect();

            $imagePath = $section->fresh()->image_path;

            $this->assertNotNull($imagePath);
            Storage::disk('public')->assertExists($imagePath);

            $this->actingAs($operator)->get(route('monitoraggi.index', [
                'view' => 'nuovo',
                'env' => $sectionData['environment'],
                'sub' => $sectionData['sub_environment'],
            ]))
                ->assertOk()
                ->assertSee(Storage::disk('public')->url($imagePath), false)
                ->assertSee($sectionData['name'], false)
                ->assertSee($sectionData['environment'] === 'clean_room' ? 'Visualizza foto clean room' : 'Visualizza foto sezione acque', false);
        }
    }
}
