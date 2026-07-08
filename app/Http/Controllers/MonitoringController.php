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
}
