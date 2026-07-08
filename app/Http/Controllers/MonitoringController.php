<?php

namespace App\Http\Controllers;

use App\Models\MicrobiologicalCheck;
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
            ->with(['samplingPoints' => function ($query) {
                $query->where('is_active', true)->orderBy('sort_order');
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
        $data = $request->validate([
            'legacy_code' => ['nullable', 'string', 'max:50'],
            'title' => ['required', 'string', 'max:255'],
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

        $section->samplingPoints()->create([
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
