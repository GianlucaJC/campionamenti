<?php

namespace App\Http\Controllers;

use App\Models\MicrobiologicalCheck;
use App\Models\MonitoringSection;
use App\Models\SamplingOperator;
use App\Models\SamplingSession;
use App\Models\SamplingUniform;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OperatorCatalogController extends Controller
{
    public function storeOperator(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:120']]);
        $name = trim($data['name']);
        if (SamplingOperator::query()->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->exists()) {
            throw ValidationException::withMessages(['name' => 'Questo operatore è già presente.']);
        }

        SamplingOperator::query()->create(['name' => $name]);

        return $this->redirectToCatalog('Operatore aggiunto.');
    }

    public function deleteOperator(Request $request): RedirectResponse
    {
        $data = $request->validate(['operator_id' => ['required', 'integer', Rule::exists('sampling_operators', 'id')]]);
        $operator = SamplingOperator::query()->findOrFail($data['operator_id']);
        $inChecks = MicrobiologicalCheck::withTrashed()->where('sampled_operator_id', $operator->id)->exists();
        $inSessions = SamplingSession::query()->cursor()
            ->contains(fn (SamplingSession $session) => (string) data_get($session->header, 'sampled_operator_id') === (string) $operator->id);

        if ($inChecks || $inSessions) {
            throw ValidationException::withMessages(['operator_id' => 'Operatore utilizzato in una registrazione o intestazione: non è possibile eliminarlo.']);
        }

        $operator->delete();

        return $this->redirectToCatalog('Operatore eliminato.');
    }

    public function storeUniform(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:120']]);
        $name = trim($data['name']);
        if (SamplingUniform::query()->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->exists()) {
            throw ValidationException::withMessages(['name' => 'Questa divisa è già presente.']);
        }

        SamplingUniform::query()->create(['name' => $name]);

        return $this->redirectToCatalog('Divisa aggiunta.');
    }

    public function deleteUniform(Request $request): RedirectResponse
    {
        $data = $request->validate(['uniform_id' => ['required', 'integer', Rule::exists('sampling_uniforms', 'id')]]);
        $uniform = SamplingUniform::query()->findOrFail($data['uniform_id']);
        $inChecks = MicrobiologicalCheck::withTrashed()->where('operator_uniform', $uniform->name)->exists();
        $inSessions = SamplingSession::query()->cursor()
            ->contains(fn (SamplingSession $session) => data_get($session->header, 'operator_uniform') === $uniform->name);

        if ($inChecks || $inSessions) {
            throw ValidationException::withMessages(['uniform_id' => 'Divisa utilizzata in una registrazione o intestazione: non è possibile eliminarla.']);
        }

        $uniform->delete();

        return $this->redirectToCatalog('Divisa eliminata.');
    }

    public function storeSection(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:240'],
            'source_section_id' => ['required', 'integer', Rule::exists('monitoring_sections', 'id')->where('environment', 'operatori')->where('is_active', true)],
        ]);
        $name = trim($data['name']);
        if (Str::startsWith(mb_strtolower($name), 'operatori - ')) {
            $name = trim(mb_substr($name, mb_strlen('Operatori - ')));
        }
        if ($name === '') {
            throw ValidationException::withMessages(['name' => 'Inserisci il nome del reparto.']);
        }
        $sectionName = 'Operatori - '.$name;
        if (MonitoringSection::query()->where('environment', 'operatori')
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($sectionName)])->exists()) {
            throw ValidationException::withMessages(['name' => 'Questo reparto è già presente.']);
        }

        DB::transaction(function () use ($data, $name, $sectionName): void {
            $source = MonitoringSection::query()->with(['departments', 'samplingPoints'])->findOrFail($data['source_section_id']);
            $baseCode = Str::slug('operatori-'.$name) ?: 'operatori-reparto';
            $code = $baseCode;
            $suffix = 2;
            while (MonitoringSection::query()->where('code', $code)->exists()) {
                $code = $baseCode.'-'.$suffix++;
            }
            $section = MonitoringSection::query()->create([
                'code' => $code,
                'name' => $sectionName,
                'environment' => 'operatori',
                'sort_order' => (float) (MonitoringSection::query()->where('environment', 'operatori')->max('sort_order') ?? 0) + 10,
                'is_active' => true,
            ]);
            $departmentIds = [];
            foreach ($source->departments as $department) {
                $copy = $department->replicate();
                $copy->monitoring_section_id = $section->id;
                $copy->save();
                $departmentIds[$department->id] = $copy->id;
            }
            foreach ($source->samplingPoints as $point) {
                if ($point->monitoring_department_id && ! isset($departmentIds[$point->monitoring_department_id])) {
                    continue;
                }
                $copy = $point->replicate();
                $copy->monitoring_section_id = $section->id;
                $copy->monitoring_department_id = $departmentIds[$point->monitoring_department_id] ?? null;
                $copy->save();
            }
        });

        return $this->redirectToCatalog('Reparto aggiunto con i punti del reparto scelto.');
    }

    public function deleteSection(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'section_id' => ['required', 'integer', Rule::exists('monitoring_sections', 'id')->where('environment', 'operatori')],
        ]);
        $section = MonitoringSection::query()->findOrFail($data['section_id']);
        $inChecks = MicrobiologicalCheck::withTrashed()->where('monitoring_section_id', $section->id)->exists()
            || $section->samplingPoints()->whereHas('checkPoints')->exists();
        $inSessions = SamplingSession::query()->cursor()
            ->contains(fn (SamplingSession $session) => (string) data_get($session->header, 'operator_section') === (string) $section->id);

        if ($inChecks || $inSessions) {
            throw ValidationException::withMessages(['section_id' => 'Reparto utilizzato in una registrazione o intestazione: non è possibile eliminarlo.']);
        }

        $section->delete();

        return $this->redirectToCatalog('Reparto e relativa configurazione eliminati.');
    }

    private function redirectToCatalog(string $status): RedirectResponse
    {
        return redirect()->route('monitoraggi.index', ['view' => 'gestione-operatori', 'env' => 'operatori'])
            ->with('status', $status);
    }
}
