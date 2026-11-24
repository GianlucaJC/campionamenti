<details class="section" open>
    <summary>
        <div>
            <p class="section-title">Anagrafiche operatori</p>
            <p class="section-desc">Gestisci divise, reparti e operatori campionati.</p>
        </div>
        <span class="badge soft">Admin</span>
    </summary>
    <div class="section-body">
        <p class="hint">Le voci utilizzate da campionamenti o intestazioni non possono essere eliminate.</p>
        <div class="department-table">
            <div class="department-row">
                <form action="{{ route('monitoraggi.operator-catalog.uniforms.delete') }}" method="POST" onsubmit="return confirm('Eliminare la divisa selezionata?');">
                    @csrf
                    @method('DELETE')
                    <div class="field">
                        <label for="catalog_uniform">Divisa</label>
                        <select id="catalog_uniform" name="uniform_id" required>
                            <option value="">Seleziona</option>
                            @foreach ($samplingUniforms as $uniform)
                                <option value="{{ $uniform->id }}">{{ $uniform->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn-small danger-btn">Elimina</button>
                </form>
                <form action="{{ route('monitoraggi.operator-catalog.uniforms.store') }}" method="POST">
                    @csrf
                    <div class="field">
                        <label for="catalog_new_uniform">Nuova divisa</label>
                        <input id="catalog_new_uniform" name="name" maxlength="120" required>
                    </div>
                    <button type="submit" class="btn-small">Aggiungi divisa</button>
                </form>
            </div>
            <div class="department-row">
                <form action="{{ route('monitoraggi.operator-catalog.sections.delete') }}" method="POST" onsubmit="return confirm('Eliminare il reparto e la sua configurazione di punti?');">
                    @csrf
                    @method('DELETE')
                    <div class="field">
                        <label for="catalog_section">Reparto</label>
                        <select id="catalog_section" name="section_id" required>
                            <option value="">Seleziona</option>
                            @foreach ($filteredSections as $section)
                                <option value="{{ $section->id }}">{{ str_replace('Operatori - ', '', $section->name) }}{{ $section->is_active ? '' : ' (oscurato)' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn-small danger-btn">Elimina</button>
                </form>
                <form action="{{ route('monitoraggi.operator-catalog.sections.store') }}" method="POST">
                    @csrf
                    <div class="field">
                        <label for="catalog_new_section">Nuovo reparto</label>
                        <input id="catalog_new_section" name="name" maxlength="240" required>
                    </div>
                    <div class="field">
                        <label for="catalog_source_section">Copia punti e configurazione da</label>
                        <select id="catalog_source_section" name="source_section_id" required>
                            <option value="">Seleziona</option>
                            @foreach ($filteredSections->where('is_active', true) as $section)
                                <option value="{{ $section->id }}">{{ str_replace('Operatori - ', '', $section->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn-small">Aggiungi reparto</button>
                </form>
            </div>
            <div class="department-row">
                <form action="{{ route('monitoraggi.operator-catalog.operators.delete') }}" method="POST" onsubmit="return confirm('Eliminare l’operatore selezionato?');">
                    @csrf
                    @method('DELETE')
                    <div class="field">
                        <label for="catalog_operator">Operatore</label>
                        <select id="catalog_operator" name="operator_id" required>
                            <option value="">Seleziona</option>
                            @foreach ($samplingOperators as $samplingOperator)
                                <option value="{{ $samplingOperator->id }}">{{ $samplingOperator->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn-small danger-btn">Elimina</button>
                </form>
                <form action="{{ route('monitoraggi.operator-catalog.operators.store') }}" method="POST">
                    @csrf
                    <div class="field">
                        <label for="catalog_new_operator">Nuovo operatore</label>
                        <input id="catalog_new_operator" name="name" maxlength="120" required>
                    </div>
                    <button type="submit" class="btn-small">Aggiungi operatore</button>
                </form>
            </div>
        </div>
    </div>
</details>
