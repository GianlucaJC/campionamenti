@php
    $waterMedia = [
        ['title' => 'R2A Agar', 'lot' => 'r2a_agar_lot', 'expires' => 'r2a_agar_expires_on', 'incubator' => 'r2a_incubator_code', 'started' => 'r2a_incubation_started_on', 'finished' => 'r2a_incubation_finished_on', 'temperature' => '30-35 °C', 'duration' => '5 giorni'],
        ['title' => 'Chromatic Coliform Agar ISO', 'lot' => 'coliform_agar_lot', 'expires' => 'coliform_agar_expires_on', 'incubator' => 'coliform_incubator_code', 'started' => 'coliform_incubation_started_on', 'finished' => 'coliform_incubation_finished_on', 'temperature' => '36 ± 2 °C', 'duration' => '21 ± 3 h'],
        ['title' => 'Pseudomonas CN Agar', 'lot' => 'pseudomonas_cn_lot', 'expires' => 'pseudomonas_cn_expires_on', 'incubator' => 'pseudomonas_incubator_code', 'started' => 'pseudomonas_incubation_started_on', 'finished' => 'pseudomonas_incubation_finished_on', 'temperature' => '36 ± 2 °C', 'duration' => '44 ± 4 h'],
        ['title' => 'Slanetz and Bartley Medium', 'lot' => 'slanetz_bartley_lot', 'expires' => 'slanetz_bartley_expires_on', 'incubator' => 'enterococci_incubator_code', 'started' => 'enterococci_incubation_started_on', 'finished' => 'enterococci_incubation_finished_on', 'temperature' => '36 ± 2 °C', 'duration' => '44 ± 4 h'],
    ];
@endphp

<section class="water-sampling-sheet">
    <h3 class="water-sheet-title">Dati campionamento</h3>
    <div class="water-sheet-header">
        <div class="field">
            <label for="facility_name_{{ $section->id }}">Stabilimento</label>
            <input id="facility_name_{{ $section->id }}" type="text" name="facility_name" value="{{ old('facility_name', $isEditingSection ? $editingCheck->facility_name : null) }}" maxlength="120">
        </div>
        <div class="field">
            <label for="sampled_on_{{ $section->id }}">Data prelievo</label>
            <input id="sampled_on_{{ $section->id }}" type="date" name="sampled_on" value="{{ old('sampled_on', $isEditingSection ? $editingCheck->sampled_on : now()->toDateString()) }}" required>
        </div>
        <div class="field">
            <label for="sampled_time_{{ $section->id }}">Ora prelievo</label>
            <input id="sampled_time_{{ $section->id }}" type="time" name="sampled_time" value="{{ old('sampled_time', $isEditingSection ? $editingCheck->sampled_time : null) }}">
        </div>
        <div class="field">
            <label for="membrane_lot_{{ $section->id }}">Lotto membrana filtrante</label>
            <input id="membrane_lot_{{ $section->id }}" type="text" name="membrane_lot" value="{{ old('membrane_lot', $isEditingSection ? $editingCheck->membrane_lot : null) }}" maxlength="120">
        </div>
        <div class="field">
            <label for="bottle_sterilization_lot_{{ $section->id }}">Sterilizzazione materiali</label>
            <input id="bottle_sterilization_lot_{{ $section->id }}" type="text" name="bottle_sterilization_lot" value="{{ old('bottle_sterilization_lot', $isEditingSection ? $editingCheck->bottle_sterilization_lot : null) }}" maxlength="120">
        </div>
    </div>

    <div class="water-signature-row">
        <div class="field">
            <label for="operator_name_{{ $section->id }}">Campionatore - firma</label>
            <input id="operator_name_{{ $section->id }}" type="text" name="operator_name" value="{{ old('operator_name', $isEditingSection ? $editingCheck->operator_name : auth()->user()?->name) }}" maxlength="120">
        </div>
        <div class="field" data-water-step-content="results">
            <label for="incubation_started_signature_{{ $section->id }}">Firma inizio incubazione</label>
            <input id="incubation_started_signature_{{ $section->id }}" type="text" name="incubation_started_signature" value="{{ old('incubation_started_signature', $isEditingSection ? $editingCheck->incubation_started_signature : null) }}" maxlength="120">
        </div>
        <div class="field" data-water-step-content="results">
            <label for="incubation_finished_signature_{{ $section->id }}">Firma fine incubazione</label>
            <input id="incubation_finished_signature_{{ $section->id }}" type="text" name="incubation_finished_signature" value="{{ old('incubation_finished_signature', $isEditingSection ? $editingCheck->incubation_finished_signature : null) }}" maxlength="120">
        </div>
    </div>

    <div class="water-media-sheet" data-water-step-content="results">
        <div class="water-media-cell water-media-label">Terreno di coltura</div>
        @foreach ($waterMedia as $media)
            <div class="water-media-cell water-media-heading">{{ $media['title'] }}</div>
        @endforeach
        <div class="water-media-cell water-media-label">Lotto</div>
        @foreach ($waterMedia as $media)
            <div class="water-media-cell"><input type="text" name="{{ $media['lot'] }}" value="{{ old($media['lot'], $isEditingSection ? data_get($editingCheck, $media['lot']) : null) }}" maxlength="120"></div>
        @endforeach
        <div class="water-media-cell water-media-label">Scadenza</div>
        @foreach ($waterMedia as $media)
            <div class="water-media-cell"><input type="date" name="{{ $media['expires'] }}" value="{{ old($media['expires'], $isEditingSection ? data_get($editingCheck, $media['expires']) : null) }}"></div>
        @endforeach
        <div class="water-media-cell water-media-label">Temperatura incubazione</div>
        @foreach ($waterMedia as $media)
            <div class="water-media-cell water-media-fixed">{{ $media['temperature'] }}</div>
        @endforeach
        <div class="water-media-cell water-media-label">Tempo di incubazione</div>
        @foreach ($waterMedia as $media)
            <div class="water-media-cell water-media-fixed">{{ $media['duration'] }}</div>
        @endforeach
        <div class="water-media-cell water-media-label">Incubatore (codice)</div>
        @foreach ($waterMedia as $media)
            <div class="water-media-cell"><input type="text" name="{{ $media['incubator'] }}" value="{{ old($media['incubator'], $isEditingSection ? data_get($editingCheck, $media['incubator']) : null) }}" maxlength="120"></div>
        @endforeach
        <div class="water-media-cell water-media-label">Inizio incubazione</div>
        @foreach ($waterMedia as $media)
            <div class="water-media-cell"><input type="date" name="{{ $media['started'] }}" value="{{ old($media['started'], $isEditingSection ? data_get($editingCheck, $media['started']) : null) }}"></div>
        @endforeach
        <div class="water-media-cell water-media-label">Fine incubazione</div>
        @foreach ($waterMedia as $media)
            <div class="water-media-cell"><input type="date" name="{{ $media['finished'] }}" value="{{ old($media['finished'], $isEditingSection ? data_get($editingCheck, $media['finished']) : null) }}"></div>
        @endforeach
    </div>

    <div class="water-signature-row" data-water-step-content="results">
        <div class="field">
            <label for="cq_operator_name_{{ $section->id }}">Controllo qualità</label>
            <input id="cq_operator_name_{{ $section->id }}" type="text" name="cq_operator_name" value="{{ old('cq_operator_name', $isEditingSection ? $editingCheck->cq_operator_name : null) }}" maxlength="120">
        </div>
    </div>
</section>
