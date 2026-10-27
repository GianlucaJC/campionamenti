<?php

namespace Tests\Feature;

use App\Models\MicrobiologicalCheck;
use App\Models\MonitoringSection;
use App\Models\SamplingPoint;
use App\Models\SamplingSession;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
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
            '2026_07_27_000018_create_microbiological_check_phase_logs_table',
            '2026_07_27_000020_add_reason_to_microbiological_check_phase_logs_table',
            '2026_07_28_000021_add_readings_count_to_monitoring_departments_table',
            '2026_07_28_000022_create_microbiological_check_readings_table',
            '2026_07_29_000026_add_soft_deletes_to_microbiological_checks_table',
            '2026_07_29_000027_add_soft_deletes_to_monitoring_departments_table',
            '2026_09_03_000029_add_sampling_session_id_to_microbiological_checks_table',
            '2026_09_03_000030_create_sampling_sessions_table',
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

        $document = new \DOMDocument();
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
}
