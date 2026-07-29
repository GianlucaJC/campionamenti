<?php

namespace App\Http\Controllers;

use App\Models\MicrobiologicalCheck;
use App\Models\MicrobiologicalCheckPoint;
use App\Models\MicrobiologicalCheckPhaseLog;
use App\Models\MicrobiologicalCheckPhaseState;
use App\Models\MonitoringDepartment;
use App\Models\MonitoringSection;
use App\Models\SamplingPoint;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MonitoringController extends Controller
{
    /**
     * Show all monitoring sections and their dynamic points.
     */
    public function index(Request $request): View
    {
        $menuItems = [
            ['key' => 'nuovo', 'label' => 'Nuovo campionamento'],
            ['key' => 'archivio', 'label' => 'Archivio campionamenti'],
        ];

        $adminMenuItems = [];

        if ($request->user()?->isAdmin()) {
            $adminMenuItems = [
                ['key' => 'gestione-reparti', 'label' => 'Gestione reparti'],
                ['key' => 'gestione-punti', 'label' => 'Gestione punti campionamento'],
                ['key' => 'trend', 'label' => 'Trend'],
            ];
        }

        $allowedViews = collect(array_merge($menuItems, $adminMenuItems))->pluck('key')->all();
        $currentView = (string) $request->query('view', 'nuovo');

        if (! in_array($currentView, $allowedViews, true)) {
            $currentView = 'nuovo';
        }

        $environmentLabels = [
            'produzione' => 'Produzione',
            'clean_room' => 'Clean room',
            'acque' => 'Acque',
            'operatori' => 'Operatori',
        ];

        $subEnvironmentLabels = [
            'clean_room' => [
                's1' => 'S1',
                's2' => 'S2',
                's3' => 'S3',
            ],
        ];

        $sampleKindLabels = [
            'air_active' => 'Aria attiva',
            'air_passive' => 'Aria passiva',
            'surface_contact' => 'Superficie contact plate',
            'surface_swab' => 'Superficie swab',
            'water' => 'Acqua',
        ];

        $includeInactivePoints = $request->user()?->isAdmin() && $currentView === 'gestione-punti';
        $includeInactiveSections = $request->user()?->isAdmin() && $currentView === 'gestione-reparti';
        $includeDeletedDepartments = $request->user()?->isAdmin() && $currentView === 'gestione-reparti';

        $sections = MonitoringSection::query()
            ->when(! $includeInactiveSections, fn ($query) => $query->where('is_active', true))
            ->with(['departments' => function ($query) use ($includeDeletedDepartments) {
                if ($includeDeletedDepartments) {
                    $query->withTrashed();
                }

                $query->orderBy('sort_order')->orderBy('name');
            }])
            ->with(['samplingPoints' => function ($query) use ($includeInactivePoints) {
                if (! $includeInactivePoints) {
                    $query->where('is_active', true);
                }

                $query
                    ->with('department')
                    ->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();

        $availableEnvironments = collect(array_keys($environmentLabels));

        $currentEnvironment = (string) $request->query('env', 'produzione');
        if (! $availableEnvironments->contains($currentEnvironment)) {
            $currentEnvironment = (string) $availableEnvironments->first();
        }

        $availableSubEnvironments = collect($subEnvironmentLabels[$currentEnvironment] ?? []);
        $currentSubEnvironment = null;
        $productionPhase = (string) $request->query('phase', 'sampling');

        if (! preg_match('/^(sampling|reading_[1-9][0-9]*)$/', $productionPhase)) {
            $productionPhase = 'sampling';
        }

        if ($availableSubEnvironments->isNotEmpty()) {
            $currentSubEnvironment = (string) $request->query('sub', (string) $availableSubEnvironments->keys()->first());

            if (! $availableSubEnvironments->has($currentSubEnvironment)) {
                $currentSubEnvironment = (string) $availableSubEnvironments->keys()->first();
            }
        }

        $filteredSections = $sections
            ->filter(function (MonitoringSection $section) use ($currentEnvironment, $currentSubEnvironment, $availableSubEnvironments): bool {
                $matchesEnvironment = (($section->environment ?: 'produzione') === $currentEnvironment);

                if (! $matchesEnvironment) {
                    return false;
                }

                if ($availableSubEnvironments->isEmpty()) {
                    return true;
                }

                return (($section->sub_environment ?: null) === $currentSubEnvironment);
            })
            ->values();

        $maximumReadings = max(1, (int) $filteredSections
            ->flatMap(fn (MonitoringSection $section) => $section->departments)
            ->max('readings_count'));
        $productionPhases = ['sampling' => 'Fase campionamento'];

        foreach (range(1, $maximumReadings) as $readingNumber) {
            $productionPhases["reading_{$readingNumber}"] = "Lettura {$readingNumber}";
        }

        if (! array_key_exists($productionPhase, $productionPhases)) {
            $productionPhase = 'sampling';
        }

        $archiveFrom = $request->query('archive_from');
        $archiveTo = $request->query('archive_to');
        $archivePerPage = (int) $request->query('archive_per_page', 20);
        $archiveStatus = $request->user()?->isAdmin() && $request->query('archive_status') === 'deleted'
            ? 'deleted'
            : 'active';

        if (! in_array($archivePerPage, [10, 20, 50, 100], true)) {
            $archivePerPage = 20;
        }

        $archiveChecks = collect();
        $editingCheck = null;
        if ($currentView === 'archivio') {
            $archiveQuery = MicrobiologicalCheck::query()
                ->with(['section:id,name,environment,sub_environment', 'author:id,name', 'phaseStates'])
                ->withCount('pointResults')
                ->whereHas('section', function ($query) use ($currentEnvironment, $currentSubEnvironment, $availableSubEnvironments): void {
                    $query
                        ->where('is_active', true)
                        ->where(function ($envQuery) use ($currentEnvironment): void {
                            $envQuery
                                ->where('environment', $currentEnvironment)
                                ->orWhere(function ($legacyQuery) use ($currentEnvironment): void {
                                    if ($currentEnvironment === 'produzione') {
                                        $legacyQuery->whereNull('environment');
                                    }
                                });
                        });

                    if ($availableSubEnvironments->isNotEmpty()) {
                        $query->where('sub_environment', $currentSubEnvironment);
                    }
                })
                ->orderByDesc('sampled_on')
                ->orderByDesc('id');

            if ($request->user()?->isAdmin()) {
                $archiveQuery
                    ->withTrashed()
                    ->with(['phaseLogs.performedBy:id,name', 'deletedBy:id,name']);

                if ($archiveStatus === 'deleted') {
                    $archiveQuery->onlyTrashed();
                } else {
                    $archiveQuery->withoutTrashed();
                }
            }

            if ($currentEnvironment === 'acque') {
                $archiveQuery->with(['pointResults.point']);
            }

            if (filled($archiveFrom)) {
                $archiveQuery->whereDate('sampled_on', '>=', $archiveFrom);
            }

            if (filled($archiveTo)) {
                $archiveQuery->whereDate('sampled_on', '<=', $archiveTo);
            }

            $archiveChecks = $archiveQuery
                ->paginate($archivePerPage)
                ->withQueryString();
        }

        $editingCheckId = $request->query('edit_check');
        if ($currentView === 'nuovo' && filled($editingCheckId)) {
            $editingCheck = MicrobiologicalCheck::query()
                ->with(['pointResults.readings', 'phaseStates'])
                ->whereKey($editingCheckId)
                ->first();

            if ($editingCheck) {
                $editingSection = $sections->firstWhere('id', $editingCheck->monitoring_section_id);

                $matchesEnvironment = $editingSection
                    && (($editingSection->environment ?: 'produzione') === $currentEnvironment)
                    && ($availableSubEnvironments->isEmpty() || (($editingSection->sub_environment ?: null) === $currentSubEnvironment));

                if (! $matchesEnvironment) {
                    $editingCheck = null;
                }
            }
        }

        $trendEnvironments = collect($request->query('trend_environments', [$currentEnvironment]))
            ->filter(fn ($environment) => is_string($environment) && array_key_exists($environment, $environmentLabels))
            ->unique()
            ->values();
        if ($trendEnvironments->isEmpty()) {
            $trendEnvironments = collect([$currentEnvironment]);
        }

        $trendFrom = $request->query('trend_from', Carbon::now()->subDays(90)->toDateString());
        $trendTo = $request->query('trend_to', Carbon::now()->toDateString());
        if (! filled($trendFrom) || ! filled($trendTo) || $trendFrom > $trendTo) {
            $trendFrom = Carbon::now()->subDays(90)->toDateString();
            $trendTo = Carbon::now()->toDateString();
        }

        $trendAvailablePoints = collect();
        $trendPointIds = collect($request->query('trend_points', []))
            ->map(fn ($pointId) => (int) $pointId)
            ->filter()
            ->unique()
            ->values();
        $loadedTrendEnvironments = collect($request->query('trend_loaded_environments', []))
            ->filter(fn ($environment) => is_string($environment) && array_key_exists($environment, $environmentLabels))
            ->unique()
            ->values();
        $trendSeries = ['labels' => [], 'datasets' => []];

        if ($currentView === 'trend' && $request->user()?->isAdmin()) {
            $trendAvailablePoints = SamplingPoint::query()
                ->with('section:id,name,environment')
                ->where('is_active', true)
                ->whereHas('section', function ($query) use ($trendEnvironments): void {
                    $query->where('is_active', true)
                        ->whereIn('environment', $trendEnvironments);
                })
                ->orderBy('monitoring_section_id')
                ->orderBy('sort_order')
                ->get();

            $trendPointIds = $trendPointIds
                ->intersect($trendAvailablePoints->pluck('id'))
                ->values();

            $newlySelectedEnvironments = $trendEnvironments->diff($loadedTrendEnvironments);
            if ($newlySelectedEnvironments->isNotEmpty()) {
                $trendPointIds = $trendPointIds
                    ->merge($trendAvailablePoints
                        ->filter(fn (SamplingPoint $point) => $newlySelectedEnvironments->contains($point->section?->environment ?: 'produzione'))
                        ->pluck('id'))
                    ->unique()
                    ->values();
            }

            if ($trendPointIds->isNotEmpty()) {
                $trendResults = MicrobiologicalCheckPoint::query()
                    ->with([
                        'check:id,sampled_on,deleted_at',
                        'point:id,title,sample_kind,monitoring_section_id',
                        'point.section:id,name,environment',
                        'readings:id,microbiological_check_point_id,reading_number,cfu_count',
                    ])
                    ->whereIn('sampling_point_id', $trendPointIds)
                    ->whereHas('check', function ($query) use ($trendFrom, $trendTo): void {
                        $query->whereBetween('sampled_on', [$trendFrom, $trendTo]);
                    })
                    ->get()
                    ->filter(fn (MicrobiologicalCheckPoint $result) => $result->check && ! $result->check->trashed())
                    ->sortBy(fn (MicrobiologicalCheckPoint $result) => $result->check->sampled_on);

                $measurements = [
                    'cfu_count' => 'UFC',
                    'first_cfu_count' => 'Lettura 1',
                    'second_cfu_count' => 'Lettura 2',
                    'aerobic_plate_cfu' => 'Aerobi UFC/piastra',
                    'aerobic_cfu_per_ml' => 'Aerobi UFC/ml',
                    'coliform_plate_cfu' => 'Coliformi UFC/piastra',
                    'coliform_confirmed_cfu' => 'Coliformi UFC confermate',
                    'coliform_cfu_per_100ml' => 'Coliformi UFC/100 ml',
                    'pseudomonas_plate_cfu' => 'Pseudomonas UFC/piastra',
                    'pseudomonas_confirmed_cfu' => 'Pseudomonas UFC confermate',
                    'pseudomonas_cfu_per_100ml' => 'Pseudomonas UFC/100 ml',
                    'enterococci_plate_cfu' => 'Enterococchi UFC/piastra',
                    'enterococci_confirmed_cfu' => 'Enterococchi UFC confermate',
                    'enterococci_cfu_per_100ml' => 'Enterococchi UFC/100 ml',
                    'ph_value' => 'pH',
                ];
                $seriesByLabel = [];

                foreach ($trendResults as $result) {
                    $date = Carbon::parse($result->check->sampled_on)->toDateString();
                    $pointLabel = ($result->point->section?->name ?: 'Sezione') . ' - ' . $result->point->title;

                    foreach ($result->readings as $reading) {
                        if ($reading->cfu_count === null) {
                            continue;
                        }

                        $seriesByLabel["{$pointLabel} - Lettura {$reading->reading_number}"][$date] = (float) $reading->cfu_count;
                    }

                    foreach ($measurements as $column => $label) {
                        $value = $result->{$column};
                        if ($value === null || $value === '' || ! is_numeric($value)) {
                            continue;
                        }

                        $seriesByLabel["{$pointLabel} - {$label}"][$date] = (float) $value;
                    }
                }

                $labels = collect($seriesByLabel)
                    ->flatMap(fn (array $series) => array_keys($series))
                    ->unique()
                    ->sort()
                    ->values();
                $palette = ['#12706b', '#d8702f', '#426f94', '#8b4f2f', '#6c8f3f', '#a33e65', '#6b5c98', '#1f7a75'];

                $trendSeries = [
                    'labels' => $labels->map(fn ($date) => Carbon::parse($date)->format('d/m/Y'))->all(),
                    'datasets' => collect($seriesByLabel)->values()->map(function (array $series, int $index) use ($seriesByLabel, $labels, $palette): array {
                        $label = array_keys($seriesByLabel)[$index];

                        return [
                            'label' => $label,
                            'data' => $labels->map(fn ($date) => $series[$date] ?? null)->all(),
                            'borderColor' => $palette[$index % count($palette)],
                            'backgroundColor' => $palette[$index % count($palette)],
                        ];
                    })->all(),
                ];
            }
        }

        return view('monitoraggi.index', [
            'menuItems' => $menuItems,
            'adminMenuItems' => $adminMenuItems,
            'currentView' => $currentView,
            'environmentLabels' => $environmentLabels,
            'availableEnvironments' => $availableEnvironments,
            'currentEnvironment' => $currentEnvironment,
            'availableSubEnvironments' => $availableSubEnvironments,
            'currentSubEnvironment' => $currentSubEnvironment,
            'productionPhases' => $productionPhases,
            'productionPhase' => $productionPhase,
            'sampleKindLabels' => $sampleKindLabels,
            'sections' => $sections,
            'filteredSections' => $filteredSections,
            'archiveChecks' => $archiveChecks,
            'archiveFrom' => $archiveFrom,
            'archiveTo' => $archiveTo,
            'archivePerPage' => $archivePerPage,
            'archiveStatus' => $archiveStatus,
            'editingCheck' => $editingCheck,
            'trendEnvironments' => $trendEnvironments,
            'trendFrom' => $trendFrom,
            'trendTo' => $trendTo,
            'trendAvailablePoints' => $trendAvailablePoints,
            'trendPointIds' => $trendPointIds,
            'trendSeries' => $trendSeries,
        ]);
    }

    /**
     * Store one section form submission.
     */
    public function store(Request $request, MonitoringSection $section): RedirectResponse
    {
        if (! $request->user() || ! $request->user()->isOperatore()) {
            abort(403, 'Solo un operatore puo compilare e salvare il campionamento.');
        }

        $pointCollection = $section->samplingPoints()
            ->with('department')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $this->normalizeTimeInputs($request, $pointCollection);
        $data = $request->validate($this->buildCheckRules($pointCollection));
        $userId = (int) Auth::id();
        $isPhasedEnvironment = $this->isPhasedEnvironment($section);

        if ($isPhasedEnvironment) {
            $this->ensureProductionPhaseCanBeAccessed($data, $section);
        }

        $checkId = DB::transaction(function () use ($data, $pointCollection, $section, $userId, $isPhasedEnvironment): int {
            $check = MicrobiologicalCheck::query()->create([
                'monitoring_section_id' => $section->id,
                'sampled_on' => $data['sampled_on'],
                'created_by_user_id' => $userId,
            ]);

            $this->persistCheck($check, $data, $pointCollection);
            if ($isPhasedEnvironment) {
                $this->signProductionPhase($check, $data, $userId);
                $this->recordProductionPhaseLog($check, $data, $userId);
            }

            return $check->id;
        });

        return redirect()
            ->route('monitoraggi.index', array_filter([
                'view' => 'nuovo',
                'env' => $section->environment ?: 'produzione',
                'sub' => $section->sub_environment ?: null,
                'phase' => $data['entry_phase'] ?? null,
                'edit_check' => $checkId,
            ]))
            ->with('status', "Sezione '{$section->name}' salvata con successo.");
    }

    /**
     * Update an existing saved check.
     */
    public function updateCheck(
        Request $request,
        MonitoringSection $section,
        MicrobiologicalCheck $check
    ): RedirectResponse {
        if (! $request->user() || ! $request->user()->isOperatore()) {
            abort(403, 'Solo un operatore puo modificare il campionamento.');
        }

        if ((int) $check->monitoring_section_id !== (int) $section->id) {
            abort(404, 'Campionamento non coerente con la sezione richiesta.');
        }

        $pointCollection = $section->samplingPoints()
            ->with('department')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $this->normalizeTimeInputs($request, $pointCollection);
        $data = $request->validate($this->buildCheckRules($pointCollection));

        $isPhasedEnvironment = $this->isPhasedEnvironment($section);

        if ($isPhasedEnvironment && ! empty($data['save_header']) && ! empty($data['entry_phase'])) {
            $this->persistCheckHeader($check, $data);

            return redirect()
                ->route('monitoraggi.index', array_filter([
                    'view' => 'nuovo',
                    'env' => $section->environment ?: 'produzione',
                    'sub' => $section->sub_environment ?: null,
                    'edit_check' => $check->id,
                    'phase' => $data['entry_phase'],
                ]))
                ->with('status', "Intestazione della sezione '{$section->name}' aggiornata con successo.");
        }

        $userId = (int) Auth::id();
        if (! $isPhasedEnvironment) {
            DB::transaction(function () use ($check, $data, $pointCollection): void {
                $this->persistCheck($check, $data, $pointCollection);
            });

            return redirect()
                ->route('monitoraggi.index', array_filter([
                    'view' => 'nuovo',
                    'env' => $section->environment ?: 'produzione',
                    'sub' => $section->sub_environment ?: null,
                    'edit_check' => $check->id,
                ]))
                ->with('status', "Sezione '{$section->name}' aggiornata con successo.");
        }

        $this->ensureProductionPhaseCanBeAccessed($data, $section, $check);
        $isReopening = $this->ensureProductionPhaseCanBeWritten($data, $check, $userId);

        if ($isReopening) {
            DB::transaction(function () use ($check, $data, $userId): void {
                $this->reopenProductionPhase($check, $data, $userId);
                $this->recordProductionPhaseLog($check, $data, $userId, 'reopened');
            });

            return redirect()
                ->route('monitoraggi.index', array_filter([
                    'view' => 'nuovo',
                    'env' => $section->environment ?: 'produzione',
                    'sub' => $section->sub_environment ?: null,
                    'edit_check' => $check->id,
                    'phase' => $data['entry_phase'] ?? null,
                ]))
                ->with('status', "Fase della sezione '{$section->name}' riaperta in scrittura.");
        }

        DB::transaction(function () use ($check, $data, $pointCollection, $userId): void {
            $this->persistCheck($check, $data, $pointCollection);
            $this->signProductionPhase($check, $data, $userId);
            $this->recordProductionPhaseLog($check, $data, $userId);
        });

        return redirect()
            ->route('monitoraggi.index', array_filter([
                'view' => 'nuovo',
                'env' => $section->environment ?: 'produzione',
                'sub' => $section->sub_environment ?: null,
                'edit_check' => $check->id,
                'phase' => $data['entry_phase'] ?? null,
            ]))
            ->with('status', "Sezione '{$section->name}' aggiornata con successo.");
    }

    /**
     * Soft-delete a check and require a justification when it has signatures.
     */
    public function deleteCheck(Request $request, MicrobiologicalCheck $check): RedirectResponse
    {
        $hasSignature = $this->checkHasSignature($check);
        $data = $request->validate([
            'deletion_reason' => [$hasSignature ? 'required' : 'nullable', 'string', 'max:1000'],
        ]);
        $reason = filled($data['deletion_reason'] ?? null) ? trim($data['deletion_reason']) : null;

        DB::transaction(function () use ($check, $request, $reason): void {
            $check->phaseLogs()->create([
                'phase' => 'archive',
                'action' => 'soft_deleted',
                'reason' => $reason,
                'performed_by_user_id' => $request->user()->id,
                'logged_at' => now(),
            ]);

            $check->update(['deleted_by_user_id' => $request->user()->id]);
            $check->delete();
        });

        return $this->redirectToArchive($check)
            ->with('status', 'Campionamento eliminato. Un admin puo ripristinarlo dall\'archivio eliminati.');
    }

    /**
     * Restore a soft-deleted check and record the administrative action.
     */
    public function restoreCheck(Request $request, MicrobiologicalCheck $check): RedirectResponse
    {
        if (! $check->trashed()) {
            return $this->redirectToArchive($check)
                ->withErrors(['check' => 'Il campionamento selezionato non e eliminato.']);
        }

        DB::transaction(function () use ($check, $request): void {
            $check->restore();
            $check->update(['deleted_by_user_id' => null]);
            $check->phaseLogs()->create([
                'phase' => 'archive',
                'action' => 'restored',
                'performed_by_user_id' => $request->user()->id,
                'logged_at' => now(),
            ]);
        });

        return $this->redirectToArchive($check)
            ->with('status', 'Campionamento ripristinato correttamente.');
    }

    /**
     * Build validation rules for a check payload.
     */
    private function buildCheckRules($pointCollection): array
    {
        $rules = [
            'entry_phase' => ['nullable', 'regex:/^(sampling|reading_[1-9][0-9]*)$/'],
            'sign_phase' => ['nullable', 'boolean'],
            'save_header' => ['nullable', 'boolean'],
            'reopen_phase' => ['nullable', 'boolean'],
            'reopening_reason' => ['nullable', 'string', 'max:1000'],
            'facility_name' => ['nullable', 'string', 'max:120'],
            'sampled_on' => ['required', 'date'],
            'sampled_time' => ['nullable', 'date_format:H:i'],
            'incubation_started_on' => ['nullable', 'date'],
            'first_reading_on' => ['nullable', 'date'],
            'second_reading_on' => ['nullable', 'date'],
            'operator_name' => ['nullable', 'string', 'max:120'],
            'incubation_started_signature' => ['nullable', 'string', 'max:120'],
            'incubation_finished_signature' => ['nullable', 'string', 'max:120'],
            'cq_operator_name' => ['nullable', 'string', 'max:120'],
            'product_batch' => ['nullable', 'string', 'max:120'],
            'media_lot' => ['nullable', 'string', 'max:120'],
            'swab_lot' => ['nullable', 'string', 'max:120'],
            'membrane_lot' => ['nullable', 'string', 'max:120'],
            'bottle_sterilization_lot' => ['nullable', 'string', 'max:120'],
            'r2a_agar_lot' => ['nullable', 'string', 'max:120'],
            'r2a_agar_expires_on' => ['nullable', 'date'],
            'r2a_incubator_code' => ['nullable', 'string', 'max:120'],
            'r2a_incubation_started_on' => ['nullable', 'date'],
            'r2a_incubation_finished_on' => ['nullable', 'date'],
            'coliform_agar_lot' => ['nullable', 'string', 'max:120'],
            'coliform_agar_expires_on' => ['nullable', 'date'],
            'coliform_incubator_code' => ['nullable', 'string', 'max:120'],
            'coliform_incubation_started_on' => ['nullable', 'date'],
            'coliform_incubation_finished_on' => ['nullable', 'date'],
            'pseudomonas_cn_lot' => ['nullable', 'string', 'max:120'],
            'pseudomonas_cn_expires_on' => ['nullable', 'date'],
            'pseudomonas_incubator_code' => ['nullable', 'string', 'max:120'],
            'pseudomonas_incubation_started_on' => ['nullable', 'date'],
            'pseudomonas_incubation_finished_on' => ['nullable', 'date'],
            'slanetz_bartley_lot' => ['nullable', 'string', 'max:120'],
            'slanetz_bartley_expires_on' => ['nullable', 'date'],
            'enterococci_incubator_code' => ['nullable', 'string', 'max:120'],
            'enterococci_incubation_started_on' => ['nullable', 'date'],
            'enterococci_incubation_finished_on' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:4000'],
            'points' => ['required', 'array'],
        ];

        foreach ($pointCollection as $point) {
            $prefix = "points.{$point->id}";
            $rules["{$prefix}.sampled_at"] = ['nullable', 'date_format:H:i'];
            $rules["{$prefix}.cfu_count"] = ['nullable', 'integer', 'min:0'];
            $rules["{$prefix}.first_cfu_count"] = ['nullable', 'integer', 'min:0'];
            $rules["{$prefix}.second_cfu_count"] = ['nullable', 'integer', 'min:0'];
            $rules["{$prefix}.reading_cfu_count"] = ['nullable', 'integer', 'min:0'];
            $rules["{$prefix}.reading_growth_result"] = ['nullable', 'in:growth,no_growth'];
            $rules["{$prefix}.notes"] = ['nullable', 'string', 'max:500'];

            if ($point->sample_kind === 'air_passive') {
                $rules["{$prefix}.exposure_ended_at"] = ['nullable', 'date_format:H:i'];
                $rules["{$prefix}.first_cfu_count"] = ['nullable', 'integer', 'min:0'];
                $rules["{$prefix}.second_cfu_count"] = ['nullable', 'integer', 'min:0'];
            }

            if (in_array($point->sample_kind, ['air_active', 'surface_contact'], true)) {
                $rules["{$prefix}.first_cfu_count"] = ['nullable', 'integer', 'min:0'];
                $rules["{$prefix}.second_cfu_count"] = ['nullable', 'integer', 'min:0'];
            }

            if ($point->sample_kind === 'surface_swab') {
                $rules["{$prefix}.first_growth_result"] = ['nullable', 'in:growth,no_growth'];
                $rules["{$prefix}.second_growth_result"] = ['nullable', 'in:growth,no_growth'];
            }

            if ($point->sample_kind === 'water') {
                $rules["{$prefix}.aerobic_plate_cfu"] = ['nullable', 'integer', 'min:0'];
                $rules["{$prefix}.aerobic_cfu_per_ml"] = ['nullable', 'integer', 'min:0'];
                $rules["{$prefix}.coliform_plate_cfu"] = ['nullable', 'integer', 'min:0'];
                $rules["{$prefix}.coliform_confirmed_cfu"] = ['nullable', 'integer', 'min:0'];
                $rules["{$prefix}.coliform_cfu_per_100ml"] = ['nullable', 'integer', 'min:0'];
                $rules["{$prefix}.pseudomonas_plate_cfu"] = ['nullable', 'integer', 'min:0'];
                $rules["{$prefix}.pseudomonas_confirmed_cfu"] = ['nullable', 'integer', 'min:0'];
                $rules["{$prefix}.pseudomonas_cfu_per_100ml"] = ['nullable', 'integer', 'min:0'];
                $rules["{$prefix}.enterococci_plate_cfu"] = ['nullable', 'integer', 'min:0'];
                $rules["{$prefix}.enterococci_confirmed_cfu"] = ['nullable', 'integer', 'min:0'];
                $rules["{$prefix}.enterococci_cfu_per_100ml"] = ['nullable', 'integer', 'min:0'];
                $rules["{$prefix}.ph_value"] = ['nullable', 'string', 'max:20'];
                $rules["{$prefix}.appearance_result"] = ['nullable', 'in:conforme,non_conforme'];
                $rules["{$prefix}.final_result"] = ['nullable', 'in:conforme,non_conforme'];
            }

            if ($point->requires_operational_status) {
                $rules["{$prefix}.is_operational"] = ['nullable', 'boolean'];
            }

            if ($point->requires_product_lot) {
                $rules["{$prefix}.product_lot"] = ['nullable', 'string', 'max:120'];
            }
        }

        return $rules;
    }

    private function isPhasedEnvironment(MonitoringSection $section): bool
    {
        return in_array($section->environment ?: 'produzione', ['produzione', 'clean_room', 'operatori'], true);
    }

    private function checkHasSignature(MicrobiologicalCheck $check): bool
    {
        if ($check->phaseStates()->whereNotNull('signed_at')->exists()) {
            return true;
        }

        return collect([
            $check->sampling_completed_signature,
            $check->first_reading_completed_signature,
            $check->second_reading_completed_signature,
            $check->incubation_started_signature,
            $check->incubation_finished_signature,
            $check->sampling_completed_by_user_id,
            $check->first_reading_completed_by_user_id,
            $check->second_reading_completed_by_user_id,
        ])->contains(fn ($value) => filled($value));
    }

    private function redirectToArchive(MicrobiologicalCheck $check): RedirectResponse
    {
        $section = $check->section;

        return redirect()->route('monitoraggi.index', array_filter([
            'view' => 'archivio',
            'env' => $section?->environment ?: 'produzione',
            'sub' => $section?->sub_environment ?: null,
        ]));
    }

    /**
     * HTML time controls submit hours and minutes while existing MySQL TIME values include seconds.
     */
    private function normalizeTimeInputs(Request $request, $pointCollection): void
    {
        $points = $request->input('points', []);

        foreach ($pointCollection as $point) {
            $pointInput = $points[$point->id] ?? null;

            if (! is_array($pointInput)) {
                continue;
            }

            foreach (['sampled_at', 'exposure_ended_at'] as $field) {
                if (isset($pointInput[$field]) && is_string($pointInput[$field])) {
                    $points[$point->id][$field] = substr($pointInput[$field], 0, 5);
                }
            }
        }

        $sampledTime = $request->input('sampled_time');

        $request->merge([
            'sampled_time' => is_string($sampledTime) ? substr($sampledTime, 0, 5) : $sampledTime,
            'points' => $points,
        ]);
    }

    /**
     * Prevent access to production reading phases before their preceding phase is signed.
     */
    private function ensureProductionPhaseCanBeAccessed(
        array $data,
        MonitoringSection $section,
        ?MicrobiologicalCheck $check = null
    ): void
    {
        $phase = $data['entry_phase'] ?? null;

        if (! $phase) {
            return;
        }

        $readingNumber = $this->readingNumber($phase);
        $maximumReadings = max(1, (int) $section->departments()->max('readings_count'));

        if ($readingNumber && $readingNumber > $maximumReadings) {
            throw ValidationException::withMessages([
                'entry_phase' => 'La lettura selezionata non e configurata per questa sezione.',
            ]);
        }

        $previousPhase = $this->previousProductionPhase($phase);

        if ($previousPhase && ! $this->isProductionPhaseSigned($check, $previousPhase)) {
            throw ValidationException::withMessages([
                'entry_phase' => "Completa e firma prima {$this->productionPhaseLabel($previousPhase)}.",
            ]);
        }
    }

    private function signProductionPhase(MicrobiologicalCheck $check, array $data, int $userId): void
    {
        if (empty($data['sign_phase']) || empty($data['entry_phase'])) {
            return;
        }

        $check->phaseStates()->updateOrCreate(
            ['phase' => $data['entry_phase']],
            [
                'signed_by_user_id' => $userId,
                'signed_at' => now(),
                'reopened_by_user_id' => null,
                'reopened_at' => null,
                'reopening_reason' => null,
            ]
        );
    }

    /**
     * Enforce that signed phases remain read-only until their signer reopens them with a reason.
     */
    private function ensureProductionPhaseCanBeWritten(array $data, MicrobiologicalCheck $check, int $userId): bool
    {
        $phase = $data['entry_phase'] ?? null;

        if (! $phase) {
            return false;
        }

        $state = $this->productionPhaseState($check, $phase);
        $signerId = $state?->signed_by_user_id;

        if (! $signerId) {
            return false;
        }

        if ((int) $signerId !== $userId) {
            throw ValidationException::withMessages([
                'entry_phase' => 'Solo l\'operatore che ha firmato questa fase puo modificarla o riaprirla.',
            ]);
        }

        if (filled($state?->reopened_at)) {
            return false;
        }

        if (empty($data['reopen_phase'])) {
            throw ValidationException::withMessages([
                'entry_phase' => 'La fase e firmata e bloccata. Riaprila indicando una motivazione per poterla modificare.',
            ]);
        }

        if (blank($data['reopening_reason'] ?? null)) {
            throw ValidationException::withMessages([
                'reopening_reason' => 'Indica la motivazione della riapertura.',
            ]);
        }

        return true;
    }

    /**
     * Mark a signed phase writable again while preserving the signing operator and audit reason.
     */
    private function reopenProductionPhase(MicrobiologicalCheck $check, array $data, int $userId): void
    {
        $this->productionPhaseState($check, $data['entry_phase'])?->update([
            'reopened_by_user_id' => $userId,
            'reopened_at' => now(),
            'reopening_reason' => $data['reopening_reason'],
        ]);
    }

    /**
     * Keep an immutable audit row for every production-phase submission.
     */
    private function recordProductionPhaseLog(MicrobiologicalCheck $check, array $data, int $userId, ?string $action = null): void
    {
        $phase = $data['entry_phase'] ?? null;

        if (! $phase) {
            return;
        }

        MicrobiologicalCheckPhaseLog::query()->create([
            'microbiological_check_id' => $check->id,
            'phase' => $phase,
            'action' => $action ?? (empty($data['sign_phase']) ? 'saved' : 'saved_and_signed'),
            'reason' => $action === 'reopened' ? $data['reopening_reason'] : null,
            'performed_by_user_id' => $userId,
            'logged_at' => now(),
        ]);
    }

    /**
     * Persist only section-header fields, independently from production-phase locks.
     */
    private function persistCheckHeader(MicrobiologicalCheck $check, array $data): void
    {
        $check->update($this->checkHeaderPayload($data));
    }

    /**
     * @return array<string, mixed>
     */
    private function checkHeaderPayload(array $data): array
    {
        $payload = [
            'facility_name' => $data['facility_name'] ?? null,
            'sampled_on' => $data['sampled_on'],
            'sampled_time' => $data['sampled_time'] ?? null,
            'incubation_started_on' => $data['incubation_started_on'] ?? null,
            'operator_name' => $data['operator_name'] ?? null,
            'incubation_started_signature' => $data['incubation_started_signature'] ?? null,
            'incubation_finished_signature' => $data['incubation_finished_signature'] ?? null,
            'cq_operator_name' => $data['cq_operator_name'] ?? null,
            'product_batch' => $data['product_batch'] ?? null,
            'media_lot' => $data['media_lot'] ?? null,
            'swab_lot' => $data['swab_lot'] ?? null,
            'membrane_lot' => $data['membrane_lot'] ?? null,
            'bottle_sterilization_lot' => $data['bottle_sterilization_lot'] ?? null,
            'r2a_agar_lot' => $data['r2a_agar_lot'] ?? null,
            'r2a_agar_expires_on' => $data['r2a_agar_expires_on'] ?? null,
            'r2a_incubator_code' => $data['r2a_incubator_code'] ?? null,
            'r2a_incubation_started_on' => $data['r2a_incubation_started_on'] ?? null,
            'r2a_incubation_finished_on' => $data['r2a_incubation_finished_on'] ?? null,
            'coliform_agar_lot' => $data['coliform_agar_lot'] ?? null,
            'coliform_agar_expires_on' => $data['coliform_agar_expires_on'] ?? null,
            'coliform_incubator_code' => $data['coliform_incubator_code'] ?? null,
            'coliform_incubation_started_on' => $data['coliform_incubation_started_on'] ?? null,
            'coliform_incubation_finished_on' => $data['coliform_incubation_finished_on'] ?? null,
            'pseudomonas_cn_lot' => $data['pseudomonas_cn_lot'] ?? null,
            'pseudomonas_cn_expires_on' => $data['pseudomonas_cn_expires_on'] ?? null,
            'pseudomonas_incubator_code' => $data['pseudomonas_incubator_code'] ?? null,
            'pseudomonas_incubation_started_on' => $data['pseudomonas_incubation_started_on'] ?? null,
            'pseudomonas_incubation_finished_on' => $data['pseudomonas_incubation_finished_on'] ?? null,
            'slanetz_bartley_lot' => $data['slanetz_bartley_lot'] ?? null,
            'slanetz_bartley_expires_on' => $data['slanetz_bartley_expires_on'] ?? null,
            'enterococci_incubator_code' => $data['enterococci_incubator_code'] ?? null,
            'enterococci_incubation_started_on' => $data['enterococci_incubation_started_on'] ?? null,
            'enterococci_incubation_finished_on' => $data['enterococci_incubation_finished_on'] ?? null,
        ];

        foreach (['first_reading_on', 'second_reading_on'] as $field) {
            if (array_key_exists($field, $data)) {
                $payload[$field] = $data[$field];
            }
        }

        return $payload;
    }

    /**
     * Persist header and point-level data for a check.
     */
    private function persistCheck(MicrobiologicalCheck $check, array $data, $pointCollection): void
    {
        $checkPayload = array_merge($this->checkHeaderPayload($data), [
            'notes' => $data['notes'] ?? null,
        ]);

        $phase = $data['entry_phase'] ?? null;
        if ($phase) {
            $phasePayloadFields = ['sampled_on'];

            $checkPayload = array_intersect_key($checkPayload, array_flip($phasePayloadFields));
        }

        $check->update($checkPayload);

        $existingResults = $check->pointResults()->get()->keyBy('sampling_point_id');
        $touchedPointIds = [];

        foreach ($pointCollection as $point) {
            $pointInput = $data['points'][$point->id] ?? [];
            $readingNumber = $this->readingNumber($data['entry_phase'] ?? null);

            if ($readingNumber && $readingNumber > (int) ($point->department?->readings_count ?? 2)) {
                continue;
            }

            $hasValue = collect($pointInput)
                ->filter(fn ($value) => $value !== null && $value !== '')
                ->isNotEmpty();

            /** @var MicrobiologicalCheckPoint|null $existingResult */
            $existingResult = $existingResults->get($point->id);

            if (! $hasValue) {
                if ($existingResult && empty($data['entry_phase'])) {
                    $existingResult->delete();
                }

                continue;
            }

            $payload = [
                'sampled_at' => $pointInput['sampled_at'] ?? null,
                'exposure_ended_at' => $pointInput['exposure_ended_at'] ?? null,
                'is_operational' => $pointInput['is_operational'] ?? null,
                'product_lot' => $pointInput['product_lot'] ?? null,
                'cfu_count' => $pointInput['second_cfu_count']
                    ?? $pointInput['cfu_count']
                    ?? $pointInput['aerobic_cfu_per_ml']
                    ?? $pointInput['first_cfu_count']
                    ?? null,
                'aerobic_plate_cfu' => $pointInput['aerobic_plate_cfu'] ?? null,
                'aerobic_cfu_per_ml' => $pointInput['aerobic_cfu_per_ml'] ?? null,
                'first_cfu_count' => $pointInput['first_cfu_count'] ?? null,
                'second_cfu_count' => $pointInput['second_cfu_count'] ?? null,
                'first_growth_result' => $pointInput['first_growth_result'] ?? null,
                'second_growth_result' => $pointInput['second_growth_result'] ?? null,
                'coliform_result' => $pointInput['coliform_result'] ?? null,
                'coliform_plate_cfu' => $pointInput['coliform_plate_cfu'] ?? null,
                'coliform_confirmed_cfu' => $pointInput['coliform_confirmed_cfu'] ?? null,
                'coliform_cfu_per_100ml' => $pointInput['coliform_cfu_per_100ml'] ?? null,
                'pseudomonas_result' => $pointInput['pseudomonas_result'] ?? null,
                'pseudomonas_plate_cfu' => $pointInput['pseudomonas_plate_cfu'] ?? null,
                'pseudomonas_confirmed_cfu' => $pointInput['pseudomonas_confirmed_cfu'] ?? null,
                'pseudomonas_cfu_per_100ml' => $pointInput['pseudomonas_cfu_per_100ml'] ?? null,
                'enterococci_result' => $pointInput['enterococci_result'] ?? null,
                'enterococci_plate_cfu' => $pointInput['enterococci_plate_cfu'] ?? null,
                'enterococci_confirmed_cfu' => $pointInput['enterococci_confirmed_cfu'] ?? null,
                'enterococci_cfu_per_100ml' => $pointInput['enterococci_cfu_per_100ml'] ?? null,
                'ph_value' => $pointInput['ph_value'] ?? null,
                'appearance_result' => $pointInput['appearance_result'] ?? null,
                'final_result' => $pointInput['final_result'] ?? null,
                'notes' => $pointInput['notes'] ?? null,
            ];

            if (($data['entry_phase'] ?? null) === 'sampling') {
                $phaseFields = [
                    'sampled_at',
                    'is_operational',
                    'product_lot',
                ];

                if (($check->section?->environment ?: 'produzione') === 'clean_room') {
                    $phaseFields[] = 'exposure_ended_at';
                }

                $payload = array_intersect_key($payload, array_flip($phaseFields));

                if (($pointInput['is_operational'] ?? null) !== '1' && ($pointInput['is_operational'] ?? null) !== 1) {
                    $payload['product_lot'] = null;
                }
            }

            if ($readingNumber === 1) {
                $payload = array_intersect_key($payload, array_flip([
                    'first_cfu_count',
                    'first_growth_result',
                ]));
                $payload['first_cfu_count'] = $pointInput['reading_cfu_count'] ?? null;
                $payload['first_growth_result'] = $pointInput['reading_growth_result'] ?? null;
            }

            if ($readingNumber === 2) {
                $payload = array_intersect_key($payload, array_flip([
                    'second_cfu_count',
                    'second_growth_result',
                    'cfu_count',
                ]));

                if (array_key_exists('second_cfu_count', $pointInput)) {
                    $payload['cfu_count'] = $pointInput['second_cfu_count'];
                }
                $payload['second_cfu_count'] = $pointInput['reading_cfu_count'] ?? null;
                $payload['second_growth_result'] = $pointInput['reading_growth_result'] ?? null;
                $payload['cfu_count'] = $pointInput['reading_cfu_count'] ?? null;
            }

            if ($readingNumber && $readingNumber > 2) {
                $payload = [];
            }

            if ($existingResult) {
                $payload = array_merge(
                    array_intersect_key($existingResult->getAttributes(), array_flip(array_keys($payload))),
                    $payload
                );
            }

            if ($existingResult) {
                $existingResult->update($payload);
            } else {
                $existingResult = $check->pointResults()->create(array_merge($payload, [
                    'sampling_point_id' => $point->id,
                ]));
            }

            if ($readingNumber) {
                $readingPayload = [
                    'cfu_count' => $pointInput['reading_cfu_count'] ?? null,
                    'growth_result' => $pointInput['reading_growth_result'] ?? null,
                ];

                if (collect($readingPayload)->filter(fn ($value) => $value !== null && $value !== '')->isNotEmpty()) {
                    $existingResult->readings()->updateOrCreate(
                        ['reading_number' => $readingNumber],
                        $readingPayload
                    );
                }
            }

            $touchedPointIds[] = $point->id;
        }
    }

    private function previousProductionPhase(string $phase): ?string
    {
        $readingNumber = $this->readingNumber($phase);

        if (! $readingNumber) {
            return null;
        }

        return $readingNumber === 1 ? 'sampling' : 'reading_'.($readingNumber - 1);
    }

    private function readingNumber(?string $phase): ?int
    {
        if (! $phase || ! preg_match('/^reading_([1-9][0-9]*)$/', $phase, $matches)) {
            return null;
        }

        return (int) $matches[1];
    }

    private function productionPhaseLabel(string $phase): string
    {
        $readingNumber = $this->readingNumber($phase);

        return $readingNumber ? "la lettura {$readingNumber}" : 'la fase di campionamento';
    }

    private function productionPhaseState(MicrobiologicalCheck $check, string $phase): ?MicrobiologicalCheckPhaseState
    {
        if ($check->relationLoaded('phaseStates')) {
            return $check->phaseStates->firstWhere('phase', $phase);
        }

        return $check->phaseStates()->where('phase', $phase)->first();
    }

    private function isProductionPhaseSigned(?MicrobiologicalCheck $check, string $phase): bool
    {
        return $check && filled($this->productionPhaseState($check, $phase)?->signed_by_user_id);
    }

    /**
     * Store a new sampling point in a section for demo purposes.
     */
    public function storePoint(Request $request, MonitoringSection $section): RedirectResponse
    {
        if (! $request->user() || ! $request->user()->isAdmin()) {
            abort(403, 'Solo un admin puo definire un nuovo punto campionamento.');
        }

        $data = $request->validate([
            'legacy_code' => ['nullable', 'string', 'max:50'],
            'title' => ['required', 'string', 'max:255'],
            'monitoring_department_id' => ['nullable', 'integer', 'exists:monitoring_departments,id'],
            'area_label' => ['nullable', 'string', 'max:255'],
            'sample_kind' => ['required', 'string', 'max:50'],
            'default_volume_liters' => ['nullable', 'integer', 'min:0'],
            'default_exposure_hours' => ['nullable', 'integer', 'in:3,4'],
            'requires_operational_status' => ['required', 'boolean'],
            'requires_product_lot' => ['required', 'boolean'],
            'anchor_point_id' => ['nullable', 'integer'],
            'insert_position' => ['required', 'in:before,after,end'],
        ]);

        $points = $section->samplingPoints()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'sort_order']);

        $sortOrder = $this->resolveSortOrder(
            $points,
            $data['insert_position'],
            $data['anchor_point_id'] ?? null
        );

        $departmentId = $data['monitoring_department_id'] ?? null;
        if ($departmentId && ! $section->departments()->whereKey($departmentId)->exists()) {
            return redirect()
                ->route('monitoraggi.index', [
                    'view' => 'gestione-punti',
                    'env' => $section->environment ?: 'produzione',
                ])
                ->withErrors(['monitoring_department_id' => 'Il reparto selezionato non appartiene alla sezione scelta.']);
        }

        $section->samplingPoints()->create([
            'monitoring_department_id' => $departmentId,
            'legacy_code' => $data['legacy_code'] ?: null,
            'title' => $data['title'],
            'area_label' => $data['area_label'] ?: null,
            'sample_kind' => $data['sample_kind'],
            'default_volume_liters' => $data['sample_kind'] === 'air_active'
                ? ($data['default_volume_liters'] ?? null)
                : null,
            'default_exposure_hours' => $data['sample_kind'] === 'air_passive'
                ? ($data['default_exposure_hours'] ?? null)
                : null,
            'requires_operational_status' => (bool) $data['requires_operational_status'],
            'requires_product_lot' => (bool) $data['requires_product_lot'],
            'sort_order' => $sortOrder,
            'is_active' => true,
        ]);

        return redirect()
            ->route('monitoraggi.index', [
                'view' => 'gestione-punti',
                'env' => $section->environment ?: 'produzione',
            ])
            ->with('status', "Nuovo punto campionamento aggiunto in '{$section->name}'.");
    }

    /**
     * Update an existing sampling point in a section.
     */
    public function updatePoint(
        Request $request,
        MonitoringSection $section,
        SamplingPoint $point
    ): RedirectResponse {
        if ((int) $point->monitoring_section_id !== (int) $section->id) {
            return redirect()
                ->route('monitoraggi.index', [
                    'view' => 'gestione-punti',
                    'env' => $section->environment ?: 'produzione',
                ])
                ->withErrors(['point' => 'Il punto selezionato non appartiene alla sezione scelta.']);
        }

        $quickAction = $request->input('quick_action');
        if (in_array($quickAction, ['hide', 'show', 'delete'], true)) {
            if ($quickAction === 'delete') {
                if ($point->checkPoints()->exists()) {
                    $point->update(['is_active' => false]);

                    return redirect()
                        ->route('monitoraggi.index', [
                            'view' => 'gestione-punti',
                            'env' => $section->environment ?: 'produzione',
                        ])
                        ->with('status', "Punto '{$point->title}' oscurato: e gia usato nello storico e non puo essere eliminato.");
                }

                $pointTitle = $point->title;
                $point->delete();

                return redirect()
                    ->route('monitoraggi.index', [
                        'view' => 'gestione-punti',
                        'env' => $section->environment ?: 'produzione',
                    ])
                    ->with('status', "Punto '{$pointTitle}' eliminato da '{$section->name}'.");
            }

            $targetActive = $quickAction === 'show';

            $point->update([
                'is_active' => $targetActive,
            ]);

            return redirect()
                ->route('monitoraggi.index', [
                    'view' => 'gestione-punti',
                    'env' => $section->environment ?: 'produzione',
                ])
                ->with('status', $targetActive
                    ? "Punto '{$point->title}' riattivato in '{$section->name}'."
                    : "Punto '{$point->title}' oscurato in '{$section->name}'.");
        }

        $data = $request->validate([
            'legacy_code' => ['nullable', 'string', 'max:50'],
            'title' => ['required', 'string', 'max:255'],
            'monitoring_department_id' => ['nullable', 'integer', 'exists:monitoring_departments,id'],
            'area_label' => ['nullable', 'string', 'max:255'],
            'sample_kind' => ['required', 'string', 'max:50'],
            'default_volume_liters' => ['nullable', 'integer', 'min:0'],
            'default_exposure_hours' => ['nullable', 'integer', 'in:3,4'],
            'requires_operational_status' => ['required', 'boolean'],
            'requires_product_lot' => ['required', 'boolean'],
            'is_active' => ['required', 'boolean'],
        ]);

        $departmentId = $data['monitoring_department_id'] ?? null;
        if ($departmentId && ! $section->departments()->whereKey($departmentId)->exists()) {
            return redirect()
                ->route('monitoraggi.index', [
                    'view' => 'gestione-punti',
                    'env' => $section->environment ?: 'produzione',
                ])
                ->withErrors(['monitoring_department_id' => 'Il reparto selezionato non appartiene alla sezione scelta.']);
        }

        $point->update([
            'monitoring_department_id' => $departmentId,
            'legacy_code' => $data['legacy_code'] ?: null,
            'title' => $data['title'],
            'area_label' => $data['area_label'] ?: null,
            'sample_kind' => $data['sample_kind'],
            'default_volume_liters' => $data['sample_kind'] === 'air_active'
                ? ($data['default_volume_liters'] ?? null)
                : null,
            'default_exposure_hours' => $data['sample_kind'] === 'air_passive'
                ? ($data['default_exposure_hours'] ?? null)
                : null,
            'requires_operational_status' => (bool) $data['requires_operational_status'],
            'requires_product_lot' => (bool) $data['requires_product_lot'],
            'is_active' => (bool) $data['is_active'],
        ]);

        return redirect()
            ->route('monitoraggi.index', [
                'view' => 'gestione-punti',
                'env' => $section->environment ?: 'produzione',
            ])
            ->with('status', "Punto '{$point->title}' aggiornato in '{$section->name}'.");
    }

    /**
     * Create a new department for a section.
     */
    public function storeDepartment(Request $request, MonitoringSection $section): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'code' => ['nullable', 'string', 'max:50'],
            'readings_count' => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        $name = trim($data['name']);

        $exists = $section->departments()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->exists();

        if ($exists) {
            return redirect()
                ->route('monitoraggi.index', [
                    'view' => 'gestione-reparti',
                    'env' => $section->environment ?: 'produzione',
                ])
                ->withErrors(['name' => 'Esiste gia un reparto con questo nome nella sezione selezionata.']);
        }

        $lastSortOrder = (float) ($section->departments()->max('sort_order') ?? 0);

        $section->departments()->create([
            'name' => $name,
            'code' => filled($data['code'] ?? null) ? trim((string) $data['code']) : null,
            'readings_count' => $data['readings_count'],
            'sort_order' => $lastSortOrder + 10,
            'is_active' => true,
        ]);

        return redirect()
            ->route('monitoraggi.index', [
                'view' => 'gestione-reparti',
                'env' => $section->environment ?: 'produzione',
            ])
            ->with('status', "Nuovo reparto aggiunto in '{$section->name}'.");
    }

    /**
     * Update an existing department in a section.
     */
    public function updateDepartment(
        Request $request,
        MonitoringSection $section,
        MonitoringDepartment $department
    ): RedirectResponse {
        if ((int) $department->monitoring_section_id !== (int) $section->id) {
            return redirect()
                ->route('monitoraggi.index', [
                    'view' => 'gestione-reparti',
                    'env' => $section->environment ?: 'produzione',
                ])
                ->withErrors(['department' => 'Il reparto selezionato non appartiene alla sezione scelta.']);
        }

        $quickAction = $request->input('quick_action');
        if (in_array($quickAction, ['hide', 'show', 'delete'], true)) {
            if ($quickAction === 'delete') {
                $departmentName = $department->name;
                $department->delete();

                return redirect()
                    ->route('monitoraggi.index', [
                        'view' => 'gestione-reparti',
                        'env' => $section->environment ?: 'produzione',
                    ])
                    ->with('status', "Reparto '{$departmentName}' eliminato da '{$section->name}'.");
            }

            $targetActive = $quickAction === 'show';

            $department->update([
                'is_active' => $targetActive,
            ]);

            return redirect()
                ->route('monitoraggi.index', [
                    'view' => 'gestione-reparti',
                    'env' => $section->environment ?: 'produzione',
                ])
                ->with('status', $targetActive
                    ? "Reparto '{$department->name}' riattivato in '{$section->name}'."
                    : "Reparto '{$department->name}' oscurato in '{$section->name}'.");
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'code' => ['nullable', 'string', 'max:50'],
            'readings_count' => ['required', 'integer', 'min:1', 'max:10'],
            'is_active' => ['required', 'boolean'],
        ]);

        $name = trim($data['name']);

        $duplicate = $section->departments()
            ->where('id', '!=', $department->id)
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->exists();

        if ($duplicate) {
            return redirect()
                ->route('monitoraggi.index', [
                    'view' => 'gestione-reparti',
                    'env' => $section->environment ?: 'produzione',
                ])
                ->withErrors(['name' => 'Esiste gia un reparto con questo nome nella sezione selezionata.']);
        }

        $department->update([
            'name' => $name,
            'code' => filled($data['code'] ?? null) ? trim((string) $data['code']) : null,
            'readings_count' => $data['readings_count'],
            'is_active' => (bool) $data['is_active'],
        ]);

        return redirect()
            ->route('monitoraggi.index', [
                'view' => 'gestione-reparti',
                'env' => $section->environment ?: 'produzione',
            ])
            ->with('status', "Reparto aggiornato in '{$section->name}'.");
    }

    /**
     * Restore a soft-deleted department and make it available again.
     */
    public function restoreDepartment(
        MonitoringSection $section,
        MonitoringDepartment $department
    ): RedirectResponse {
        if ((int) $department->monitoring_section_id !== (int) $section->id || ! $department->trashed()) {
            abort(404, 'Reparto non disponibile per il ripristino.');
        }

        $department->restore();
        $department->update(['is_active' => true]);

        return redirect()
            ->route('monitoraggi.index', [
                'view' => 'gestione-reparti',
                'env' => $section->environment ?: 'produzione',
            ])
            ->with('status', "Reparto '{$department->name}' ripristinato in '{$section->name}'.");
    }

    /**
     * Hide or restore an entire monitoring section without changing its departments or points.
     */
    public function updateSectionVisibility(Request $request, MonitoringSection $section): RedirectResponse
    {
        $data = $request->validate([
            'visibility_action' => ['required', 'in:hide,show'],
        ]);

        $isActive = $data['visibility_action'] === 'show';

        $section->update(['is_active' => $isActive]);

        return redirect()
            ->route('monitoraggi.index', array_filter([
                'view' => 'gestione-reparti',
                'env' => $section->environment ?: 'produzione',
                'sub' => $section->sub_environment ?: null,
            ]))
            ->with('status', $isActive
                ? "Sezione '{$section->name}' riattivata."
                : "Sezione '{$section->name}' oscurata per gli operatori.");
    }

    /**
     * Move department up or down inside a section.
     */
    public function moveDepartment(
        Request $request,
        MonitoringSection $section,
        MonitoringDepartment $department
    ): RedirectResponse {
        if ((int) $department->monitoring_section_id !== (int) $section->id) {
            return redirect()
                ->route('monitoraggi.index', [
                    'view' => 'gestione-reparti',
                    'env' => $section->environment ?: 'produzione',
                ])
                ->withErrors(['department' => 'Il reparto selezionato non appartiene alla sezione scelta.']);
        }

        $data = $request->validate([
            'direction' => ['required', 'in:up,down'],
        ]);

        $departments = $section->departments()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'sort_order']);

        $index = $departments->search(fn ($item) => (int) $item->id === (int) $department->id);
        if ($index === false) {
            return redirect()
                ->route('monitoraggi.index', [
                    'view' => 'gestione-reparti',
                    'env' => $section->environment ?: 'produzione',
                ])
                ->withErrors(['department' => 'Reparto non trovato in elenco.']);
        }

        $targetIndex = $data['direction'] === 'up' ? $index - 1 : $index + 1;

        if (! isset($departments[$targetIndex])) {
            return redirect()
                ->route('monitoraggi.index', [
                    'view' => 'gestione-reparti',
                    'env' => $section->environment ?: 'produzione',
                ])
                ->with('status', 'Il reparto e gia alla posizione limite.');
        }

        $targetDepartment = $departments[$targetIndex];

        DB::transaction(function () use ($department, $targetDepartment): void {
            $currentOrder = (float) $department->sort_order;
            $targetOrder = (float) $targetDepartment->sort_order;

            $department->update(['sort_order' => $targetOrder]);

            MonitoringDepartment::query()
                ->whereKey($targetDepartment->id)
                ->update(['sort_order' => $currentOrder]);
        });

        return redirect()
            ->route('monitoraggi.index', [
                'view' => 'gestione-reparti',
                'env' => $section->environment ?: 'produzione',
            ])
            ->with('status', "Ordine reparti aggiornato in '{$section->name}'.");
    }

    /**
     * Move a sampling point up or down inside a section.
     */
    public function movePoint(
        Request $request,
        MonitoringSection $section,
        SamplingPoint $point
    ): RedirectResponse {
        if ((int) $point->monitoring_section_id !== (int) $section->id) {
            return redirect()
                ->route('monitoraggi.index', [
                    'view' => 'gestione-punti',
                    'env' => $section->environment ?: 'produzione',
                ])
                ->withErrors(['point' => 'Il punto selezionato non appartiene alla sezione scelta.']);
        }

        $data = $request->validate([
            'direction' => ['required', 'in:up,down'],
        ]);

        $points = $section->samplingPoints()
            ->orderBy('sort_order')
            ->get(['id', 'sort_order']);

        $index = $points->search(fn ($item) => (int) $item->id === (int) $point->id);
        if ($index === false) {
            return redirect()
                ->route('monitoraggi.index', [
                    'view' => 'gestione-punti',
                    'env' => $section->environment ?: 'produzione',
                ])
                ->withErrors(['point' => 'Punto non trovato in elenco.']);
        }

        $targetIndex = $data['direction'] === 'up' ? $index - 1 : $index + 1;

        if (! isset($points[$targetIndex])) {
            return redirect()
                ->route('monitoraggi.index', [
                    'view' => 'gestione-punti',
                    'env' => $section->environment ?: 'produzione',
                ])
                ->with('status', 'Il punto e gia alla posizione limite.');
        }

        $targetPoint = $points[$targetIndex];

        DB::transaction(function () use ($point, $targetPoint): void {
            $currentOrder = (float) $point->sort_order;
            $targetOrder = (float) $targetPoint->sort_order;

            $point->update(['sort_order' => $targetOrder]);

            SamplingPoint::query()
                ->whereKey($targetPoint->id)
                ->update(['sort_order' => $currentOrder]);
        });

        return redirect()
            ->route('monitoraggi.index', [
                'view' => 'gestione-punti',
                'env' => $section->environment ?: 'produzione',
            ])
            ->with('status', "Ordine punti aggiornato in '{$section->name}'.");
    }

    /**
     * Resolve decimal ordering to place a point before/after another one.
     */
    private function resolveSortOrder($points, string $position, ?int $anchorPointId): float
    {
        if ($points->isEmpty()) {
            return 100.000;
        }

        if ($position === 'end' || ! $anchorPointId) {
            return round((float) $points->last()->sort_order + 10, 3);
        }

        $index = $points->search(fn ($point) => (int) $point->id === (int) $anchorPointId);
        if ($index === false) {
            return round((float) $points->last()->sort_order + 10, 3);
        }

        $anchor = (float) $points[$index]->sort_order;

        if ($position === 'before') {
            $previous = $index > 0 ? (float) $points[$index - 1]->sort_order : $anchor - 10;
            return round(($previous + $anchor) / 2, 3);
        }

        $next = $index < ($points->count() - 1)
            ? (float) $points[$index + 1]->sort_order
            : $anchor + 10;

        return round(($anchor + $next) / 2, 3);
    }
}
