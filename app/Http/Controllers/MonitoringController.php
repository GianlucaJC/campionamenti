<?php

namespace App\Http\Controllers;

use App\Models\MicrobiologicalCheck;
use App\Models\MonitoringDepartment;
use App\Models\MonitoringSection;
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
    public function index(): View
    {
        $sections = MonitoringSection::query()
            ->where('is_active', true)
            ->with(['departments' => function ($query) {
                $query->orderBy('sort_order')->orderBy('name');
            }])
            ->with(['samplingPoints' => function ($query) {
                $query
                    ->where('is_active', true)
                    ->with('department')
                    ->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();

        return view('monitoraggi.index', [
            'sections' => $sections,
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

        $rules = [
            'sampled_on' => ['required', 'date'],
            'incubation_started_on' => ['nullable', 'date'],
            'first_reading_on' => ['nullable', 'date'],
            'second_reading_on' => ['nullable', 'date'],
            'operator_name' => ['nullable', 'string', 'max:120'],
            'cq_operator_name' => ['nullable', 'string', 'max:120'],
            'media_lot' => ['nullable', 'string', 'max:120'],
            'swab_lot' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:4000'],
            'points' => ['required', 'array'],
        ];

        foreach ($pointCollection as $point) {
            $prefix = "points.{$point->id}";
            $rules["{$prefix}.sampled_at"] = ['nullable', 'date_format:H:i'];
            $rules["{$prefix}.cfu_count"] = ['nullable', 'integer', 'min:0'];
            $rules["{$prefix}.notes"] = ['nullable', 'string', 'max:500'];

            if ($point->requires_operational_status) {
                $rules["{$prefix}.is_operational"] = ['nullable', 'boolean'];
            }

            if ($point->requires_product_lot) {
                $rules["{$prefix}.product_lot"] = ['nullable', 'string', 'max:120'];
            }
        }

        $data = $request->validate($rules);

        DB::transaction(function () use ($data, $pointCollection, $section): void {
            $check = MicrobiologicalCheck::query()->create([
                'monitoring_section_id' => $section->id,
                'sampled_on' => $data['sampled_on'],
                'incubation_started_on' => $data['incubation_started_on'] ?? null,
                'first_reading_on' => $data['first_reading_on'] ?? null,
                'second_reading_on' => $data['second_reading_on'] ?? null,
                'operator_name' => $data['operator_name'] ?? null,
                'cq_operator_name' => $data['cq_operator_name'] ?? null,
                'media_lot' => $data['media_lot'] ?? null,
                'swab_lot' => $data['swab_lot'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by_user_id' => Auth::id(),
            ]);

            foreach ($pointCollection as $point) {
                $pointInput = $data['points'][$point->id] ?? [];

                $hasValue = collect($pointInput)
                    ->filter(fn ($value) => $value !== null && $value !== '')
                    ->isNotEmpty();

                if (! $hasValue) {
                    continue;
                }

                $check->pointResults()->create([
                    'sampling_point_id' => $point->id,
                    'sampled_at' => $pointInput['sampled_at'] ?? null,
                    'is_operational' => $pointInput['is_operational'] ?? null,
                    'product_lot' => $pointInput['product_lot'] ?? null,
                    'cfu_count' => $pointInput['cfu_count'] ?? null,
                    'notes' => $pointInput['notes'] ?? null,
                ]);
            }
        });

        return redirect()
            ->route('monitoraggi.index')
            ->with('status', "Sezione '{$section->name}' salvata con successo.");
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
                ->route('monitoraggi.index')
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
            ->route('monitoraggi.index')
            ->with('status', "Nuovo punto campionamento aggiunto in '{$section->name}'.");
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
                ->route('monitoraggi.index')
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
            ->route('monitoraggi.index')
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
                ->route('monitoraggi.index')
                ->withErrors(['department' => 'Il reparto selezionato non appartiene alla sezione scelta.']);
        }

        $quickAction = $request->input('quick_action');
        if (in_array($quickAction, ['hide', 'show', 'delete'], true)) {
            if ($quickAction === 'delete') {
                $departmentName = $department->name;
                $department->delete();

                return redirect()
                    ->route('monitoraggi.index')
                    ->with('status', "Reparto '{$departmentName}' eliminato da '{$section->name}'.");
            }

            $targetActive = $quickAction === 'show';

            $department->update([
                'is_active' => $targetActive,
            ]);

            return redirect()
                ->route('monitoraggi.index')
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
                ->route('monitoraggi.index')
                ->withErrors(['name' => 'Esiste gia un reparto con questo nome nella sezione selezionata.']);
        }

        $department->update([
            'name' => $name,
            'code' => filled($data['code'] ?? null) ? trim((string) $data['code']) : null,
            'is_active' => (bool) $data['is_active'],
        ]);

        return redirect()
            ->route('monitoraggi.index')
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
                ->route('monitoraggi.index')
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
                ->route('monitoraggi.index')
                ->withErrors(['department' => 'Reparto non trovato in elenco.']);
        }

        $targetIndex = $data['direction'] === 'up' ? $index - 1 : $index + 1;

        if (! isset($departments[$targetIndex])) {
            return redirect()
                ->route('monitoraggi.index')
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
            ->route('monitoraggi.index')
            ->with('status', "Ordine reparti aggiornato in '{$section->name}'.");
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
