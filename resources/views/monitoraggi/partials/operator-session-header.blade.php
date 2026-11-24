<form action="{{ route('monitoraggi.sessions.update', $samplingSessionId) }}" method="POST" class="operator-session-form">
    @csrf
    @method('PUT')
    <input type="hidden" name="sampling_session_id" value="{{ $samplingSessionId }}">
    <input type="hidden" name="environment" value="{{ $currentEnvironment }}">
    <input type="hidden" name="sub_environment" value="{{ $currentSubEnvironment }}">

    <fieldset @disabled($samplingSessionHeaderLocked) style="display: contents;">
        <div class="field">
            <label for="session_operator_section_{{ $section->id }}">Reparto</label>
            <select id="session_operator_section_{{ $section->id }}" name="operator_section" data-operator-section-select data-session-id="{{ $samplingSessionId }}">
                @foreach ($operatorSections as $operatorSection)
                    <option value="{{ $operatorSection->id }}" @selected($operatorSectionId === (int) $operatorSection->id)>{{ str_replace('Operatori - ', '', $operatorSection->name) }}</option>
                @endforeach
            </select>
        </div>

        <div class="operator-session-fields">
            <div class="field">
                <label for="session_sampled_on_{{ $section->id }}">Data prelievo</label>
                <input id="session_sampled_on_{{ $section->id }}" type="date" name="sampled_on" value="{{ old('sampled_on', data_get($sessionHeader, 'sampled_on', now()->toDateString())) }}" required>
            </div>
            <div class="field">
                <label for="session_cq_operator_name_{{ $section->id }}">Operatore CQ</label>
                <input id="session_cq_operator_name_{{ $section->id }}" type="text" name="cq_operator_name" value="{{ old('cq_operator_name', data_get($sessionHeader, 'cq_operator_name')) }}" maxlength="120">
            </div>
            <div class="field">
                <label for="session_media_lot_{{ $section->id }}">Lotto piastre</label>
                <input id="session_media_lot_{{ $section->id }}" type="text" name="media_lot" value="{{ old('media_lot', data_get($sessionHeader, 'media_lot')) }}" maxlength="120">
            </div>
        </div>

        <div class="operator-session-strip">
            <div class="field">
                <label for="session_product_batch_{{ $section->id }}">Lotto prodotto</label>
                <input id="session_product_batch_{{ $section->id }}" type="text" name="product_batch" value="{{ old('product_batch', data_get($sessionHeader, 'product_batch')) }}" maxlength="120">
            </div>
            <div class="field">
                <label for="session_sampled_operator_id_{{ $section->id }}">Operatore campionato</label>
                <select id="session_sampled_operator_id_{{ $section->id }}" name="sampled_operator_id">
                    <option value="">Seleziona</option>
                    @foreach ($samplingOperators as $samplingOperator)
                        <option value="{{ $samplingOperator->id }}" @selected((string) old('sampled_operator_id', data_get($sessionHeader, 'sampled_operator_id')) === (string) $samplingOperator->id)>{{ $samplingOperator->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="session_operator_uniform_{{ $section->id }}">Divisa indossata</label>
                <select id="session_operator_uniform_{{ $section->id }}" name="operator_uniform">
                    <option value="">Seleziona</option>
                    @foreach (\App\Models\SamplingOperator::UNIFORMS as $uniform)
                        <option value="{{ $uniform }}" @selected(old('operator_uniform', data_get($sessionHeader, 'operator_uniform')) === $uniform)>{{ $uniform }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        @if (! $samplingSessionHeaderLocked)
            <div class="field" style="align-self:end;">
                <button type="submit">Salva intestazione</button>
            </div>
        @endif
    </fieldset>
</form>

@if ($samplingSessionHeaderLocked)
    <p class="hint">Intestazione e reparto bloccati dopo il salvataggio della fase di campionamento.</p>
@endif
