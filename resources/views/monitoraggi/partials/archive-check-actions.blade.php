@if (auth()->user()?->isAdmin())
    <div class="actions" style="margin-top:8px;">
        @if (($showSectionName ?? false) && $check->trashed())
            <span class="hint">{{ $check->section?->name ?? 'Sezione rimossa' }}</span>
        @endif
        @if (! $check->trashed())
            <details>
                <summary class="hint">Gestione amministrativa</summary>
                <form action="{{ route('monitoraggi.checks.delete', $check) }}" method="POST" onsubmit="return confirm('{{ $check->sampling_session_id ? 'Eliminare l’intera sessione e tutte le sue sezioni, anche quelle non visibili? Sarà possibile ripristinarle dall’archivio.' : 'Eliminare questo campionamento? Sarà possibile ripristinarlo dall’archivio.' }}');">
                    @csrf
                    @method('DELETE')
                    <label class="hint" for="delete_reason_{{ $check->id }}">Motivazione eliminazione</label>
                    <input id="delete_reason_{{ $check->id }}" type="text" name="deletion_reason" maxlength="1000" placeholder="Obbligatoria se presente una firma">
                    <button type="submit" class="btn-small soft-btn">{{ $check->sampling_session_id ? 'Elimina intera sessione' : 'Elimina campionamento' }}</button>
                </form>
            </details>
        @else
            <form action="{{ route('monitoraggi.checks.restore', $check) }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn-small">Ripristina</button>
            </form>
            <span class="hint">Eliminato da {{ $check->deletedBy?->name ?: '-' }} il {{ $check->deleted_at?->format('d-m-Y H:i') ?: '-' }}</span>
            <details>
                <summary class="hint">Eliminazione definitiva</summary>
                <form action="{{ route('monitoraggi.checks.permanent-delete', $check) }}" method="POST" onsubmit="return confirm('Eliminare DEFINITIVAMENTE l’intera sessione, risultati, letture, firme e tracciamenti? Questa operazione non può essere annullata.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-small danger-btn">Elimina definitivamente</button>
                </form>
            </details>
        @endif
    </div>
@endif
