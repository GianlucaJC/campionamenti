<?php

namespace App\Http\Controllers;

use App\Models\MicrobiologicalCheck;
use App\Models\MicrobiologicalCheckPoint;
use App\Models\MonitoringDepartment;
use App\Models\MonitoringSection;
use App\Models\SamplingPoint;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            'acque' => [
                's1' => 'Sede 1',
                's2' => 'Sede 2',
            ],
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

        $sections = MonitoringSection::query()
            ->where('is_active', true)
            ->with(['departments' => function ($query) {
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

        $archiveFrom = $request->query('archive_from');
        $archiveTo = $request->query('archive_to');
        $archivePerPage = (int) $request->query('archive_per_page', 20);

        if (! in_array($archivePerPage, [10, 20, 50, 100], true)) {
            $archivePerPage = 20;
        }

        $archiveChecks = collect();
        $editingCheck = null;
        if ($currentView === 'archivio') {
            $archiveQuery = MicrobiologicalCheck::query()
                ->with(['section:id,name,environment,sub_environment', 'author:id,name'])
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
                ->with(['pointResults'])
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

        $trendByEnvironment = collect();
        $trendBySection = collect();

        if ($currentView === 'trend' && $request->user()?->isAdmin()) {
            $sinceDate = Carbon::now()->subDays(90)->toDateString();

            $trendByEnvironment = MicrobiologicalCheck::query()
                ->join('monitoring_sections', 'monitoring_sections.id', '=', 'microbiological_checks.monitoring_section_id')
                ->whereDate('microbiological_checks.sampled_on', '>=', $sinceDate)
                ->when($availableSubEnvironments->isNotEmpty(), function ($query) use ($currentSubEnvironment): void {
                    $query->where('monitoring_sections.sub_environment', $currentSubEnvironment);
                })
                ->selectRaw("COALESCE(monitoring_sections.environment, 'produzione') as environment")
                ->selectRaw('COUNT(*) as checks_count')
                ->groupBy('environment')
                ->orderByDesc('checks_count')
                ->get();

            $trendBySection = MicrobiologicalCheck::query()
                ->join('monitoring_sections', 'monitoring_sections.id', '=', 'microbiological_checks.monitoring_section_id')
                ->whereDate('microbiological_checks.sampled_on', '>=', $sinceDate)
                ->when($availableSubEnvironments->isNotEmpty(), function ($query) use ($currentSubEnvironment): void {
                    $query->where('monitoring_sections.sub_environment', $currentSubEnvironment);
                })
                ->selectRaw("COALESCE(monitoring_sections.environment, 'produzione') as environment")
                ->selectRaw('monitoring_sections.name as section_name')
                ->selectRaw('COUNT(*) as checks_count')
                ->groupBy('environment', 'section_name')
                ->orderByDesc('checks_count')
                ->orderBy('section_name')
                ->get()
                ->groupBy('environment');
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
            'sampleKindLabels' => $sampleKindLabels,
            'sections' => $sections,
            'filteredSections' => $filteredSections,
            'archiveChecks' => $archiveChecks,
            'archiveFrom' => $archiveFrom,
            'archiveTo' => $archiveTo,
            'archivePerPage' => $archivePerPage,
            'editingCheck' => $editingCheck,
            'trendByEnvironment' => $trendByEnvironment,
            'trendBySection' => $trendBySection,
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
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $data = $request->validate($this->buildCheckRules($pointCollection));

        DB::transaction(function () use ($data, $pointCollection, $section): void {
            $check = MicrobiologicalCheck::query()->create([
                'monitoring_section_id' => $section->id,
                'sampled_on' => $data['sampled_on'],
                'created_by_user_id' => Auth::id(),
            ]);

            $this->persistCheck($check, $data, $pointCollection);
        });

        return redirect()
            ->route('monitoraggi.index', [
                'view' => 'nuovo',
                'env' => $section->environment ?: 'produzione',
                'sub' => $section->sub_environment ?: null,
            ])
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
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $data = $request->validate($this->buildCheckRules($pointCollection));

        DB::transaction(function () use ($check, $data, $pointCollection): void {
            $this->persistCheck($check, $data, $pointCollection);
        });

        return redirect()
            ->route('monitoraggi.index', [
                'view' => 'nuovo',
                'env' => $section->environment ?: 'produzione',
                'sub' => $section->sub_environment ?: null,
                'edit_check' => $check->id,
            ])
            ->with('status', "Sezione '{$section->name}' aggiornata con successo.");
    }

    /**
     * Build validation rules for a check payload.
     */
    private function buildCheckRules($pointCollection): array
    {
        $rules = [
            'sampled_on' => ['required', 'date'],
            'sampled_time' => ['nullable', 'date_format:H:i'],
            'incubation_started_on' => ['nullable', 'date'],
            'first_reading_on' => ['nullable', 'date'],
            'second_reading_on' => ['nullable', 'date'],
            'operator_name' => ['nullable', 'string', 'max:120'],
            'cq_operator_name' => ['nullable', 'string', 'max:120'],
            'product_batch' => ['nullable', 'string', 'max:120'],
            'media_lot' => ['nullable', 'string', 'max:120'],
            'swab_lot' => ['nullable', 'string', 'max:120'],
            'membrane_lot' => ['nullable', 'string', 'max:120'],
            'bottle_sterilization_lot' => ['nullable', 'string', 'max:120'],
            'r2a_agar_lot' => ['nullable', 'string', 'max:120'],
            'coliform_agar_lot' => ['nullable', 'string', 'max:120'],
            'pseudomonas_cn_lot' => ['nullable', 'string', 'max:120'],
            'slanetz_bartley_lot' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:4000'],
            'points' => ['required', 'array'],
        ];

        foreach ($pointCollection as $point) {
            $prefix = "points.{$point->id}";
            $rules["{$prefix}.sampled_at"] = ['nullable', 'date_format:H:i'];
            $rules["{$prefix}.cfu_count"] = ['nullable', 'integer', 'min:0'];
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
                $rules["{$prefix}.coliform_result"] = ['nullable', 'in:present,not_present'];
                $rules["{$prefix}.pseudomonas_result"] = ['nullable', 'in:present,not_present'];
                $rules["{$prefix}.enterococci_result"] = ['nullable', 'in:present,not_present'];
                $rules["{$prefix}.ph_value"] = ['nullable', 'string', 'max:20'];
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

    /**
     * Persist header and point-level data for a check.
     */
    private function persistCheck(MicrobiologicalCheck $check, array $data, $pointCollection): void
    {
        $check->update([
            'sampled_on' => $data['sampled_on'],
            'sampled_time' => $data['sampled_time'] ?? null,
            'incubation_started_on' => $data['incubation_started_on'] ?? null,
            'first_reading_on' => $data['first_reading_on'] ?? null,
            'second_reading_on' => $data['second_reading_on'] ?? null,
            'operator_name' => $data['operator_name'] ?? null,
            'cq_operator_name' => $data['cq_operator_name'] ?? null,
            'product_batch' => $data['product_batch'] ?? null,
            'media_lot' => $data['media_lot'] ?? null,
            'swab_lot' => $data['swab_lot'] ?? null,
            'membrane_lot' => $data['membrane_lot'] ?? null,
            'bottle_sterilization_lot' => $data['bottle_sterilization_lot'] ?? null,
            'r2a_agar_lot' => $data['r2a_agar_lot'] ?? null,
            'coliform_agar_lot' => $data['coliform_agar_lot'] ?? null,
            'pseudomonas_cn_lot' => $data['pseudomonas_cn_lot'] ?? null,
            'slanetz_bartley_lot' => $data['slanetz_bartley_lot'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        $existingResults = $check->pointResults()->get()->keyBy('sampling_point_id');
        $touchedPointIds = [];

        foreach ($pointCollection as $point) {
            $pointInput = $data['points'][$point->id] ?? [];

            $hasValue = collect($pointInput)
                ->filter(fn ($value) => $value !== null && $value !== '')
                ->isNotEmpty();

            /** @var MicrobiologicalCheckPoint|null $existingResult */
            $existingResult = $existingResults->get($point->id);

            if (! $hasValue) {
                if ($existingResult) {
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
                    ?? $pointInput['first_cfu_count']
                    ?? null,
                'first_cfu_count' => $pointInput['first_cfu_count'] ?? null,
                'second_cfu_count' => $pointInput['second_cfu_count'] ?? null,
                'first_growth_result' => $pointInput['first_growth_result'] ?? null,
                'second_growth_result' => $pointInput['second_growth_result'] ?? null,
                'coliform_result' => $pointInput['coliform_result'] ?? null,
                'pseudomonas_result' => $pointInput['pseudomonas_result'] ?? null,
                'enterococci_result' => $pointInput['enterococci_result'] ?? null,
                'ph_value' => $pointInput['ph_value'] ?? null,
                'notes' => $pointInput['notes'] ?? null,
            ];

            if ($existingResult) {
                $existingResult->update($payload);
            } else {
                $check->pointResults()->create(array_merge($payload, [
                    'sampling_point_id' => $point->id,
                ]));
            }

            $touchedPointIds[] = $point->id;
        }
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
            'default_volume_liters' => $data['default_volume_liters'] ?? null,
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
            'default_volume_liters' => $data['default_volume_liters'] ?? null,
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
        ]);

        $name = trim($data['name']);

        $exists = $section->departments()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->exists();

        if ($exists) {
            return redirect()
                ->route('monitoraggi.index', [
                    'view' => 'gestione-punti',
                    'env' => $section->environment ?: 'produzione',
                ])
                ->withErrors(['name' => 'Esiste gia un reparto con questo nome nella sezione selezionata.']);
        }

        $lastSortOrder = (float) ($section->departments()->max('sort_order') ?? 0);

        $section->departments()->create([
            'name' => $name,
            'code' => filled($data['code'] ?? null) ? trim((string) $data['code']) : null,
            'sort_order' => $lastSortOrder + 10,
            'is_active' => true,
        ]);

        return redirect()
            ->route('monitoraggi.index', [
                'view' => 'gestione-punti',
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
                    'view' => 'gestione-punti',
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
                        'view' => 'gestione-punti',
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
                    'view' => 'gestione-punti',
                    'env' => $section->environment ?: 'produzione',
                ])
                ->with('status', $targetActive
                    ? "Reparto '{$department->name}' riattivato in '{$section->name}'."
                    : "Reparto '{$department->name}' oscurato in '{$section->name}'.");
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'code' => ['nullable', 'string', 'max:50'],
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
                    'view' => 'gestione-punti',
                    'env' => $section->environment ?: 'produzione',
                ])
                ->withErrors(['name' => 'Esiste gia un reparto con questo nome nella sezione selezionata.']);
        }

        $department->update([
            'name' => $name,
            'code' => filled($data['code'] ?? null) ? trim((string) $data['code']) : null,
            'is_active' => (bool) $data['is_active'],
        ]);

        return redirect()
            ->route('monitoraggi.index', [
                'view' => 'gestione-punti',
                'env' => $section->environment ?: 'produzione',
            ])
            ->with('status', "Reparto aggiornato in '{$section->name}'.");
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
                    'view' => 'gestione-punti',
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
                    'view' => 'gestione-punti',
                    'env' => $section->environment ?: 'produzione',
                ])
                ->withErrors(['department' => 'Reparto non trovato in elenco.']);
        }

        $targetIndex = $data['direction'] === 'up' ? $index - 1 : $index + 1;

        if (! isset($departments[$targetIndex])) {
            return redirect()
                ->route('monitoraggi.index', [
                    'view' => 'gestione-punti',
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
                'view' => 'gestione-punti',
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
