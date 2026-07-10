<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Controlli Microbiologici</title>
    <style>
        @import url('https://fonts.bunny.net/css?family=ibm-plex-sans:300,400,500,600,700');

        :root {
            --bg: #f4efe8;
            --panel: #fff9f2;
            --ink: #1f2a30;
            --muted: #5f6a6f;
            --accent: #12706b;
            --accent-2: #d8702f;
            --line: #d6ccc1;
            --ok: #276749;
            --error: #b83232;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'IBM Plex Sans', sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at 5% 10%, #e7d8c6 0, transparent 32%),
                radial-gradient(circle at 90% 0%, #e7efe2 0, transparent 35%),
                linear-gradient(180deg, #f7f1e7 0%, var(--bg) 100%);
            min-height: 100vh;
        }

        .wrap {
            width: min(1320px, 95vw);
            margin: 28px auto 48px;
        }

        .hero {
            border: 1px solid var(--line);
            background: linear-gradient(135deg, #fffaf4 0%, #f7efe5 60%, #eef6ee 100%);
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 14px 30px rgba(31, 42, 48, 0.08);
            animation: reveal 450ms ease-out;
        }

        .hero h1 {
            margin: 0 0 10px;
            font-size: clamp(1.4rem, 1.8vw, 2rem);
            letter-spacing: 0.3px;
        }

        .hero p {
            margin: 0;
            color: var(--muted);
            line-height: 1.4;
        }

        .hero-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 14px;
            flex-wrap: wrap;
        }

        .user-tools {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .user-pill {
            border: 1px solid #d9c7b0;
            color: #6f5b44;
            background: #f8f1e7;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 700;
            padding: 5px 10px;
            white-space: nowrap;
        }

        .logout-btn {
            border: 1px solid #b65d29;
            color: #fff;
            background: linear-gradient(120deg, #cd6b31, #b8571f);
            border-radius: 10px;
            padding: 8px 12px;
            font-size: 0.85rem;
            font-weight: 700;
            cursor: pointer;
        }

        .logout-btn:hover {
            filter: brightness(1.06);
        }

        .status {
            margin-top: 14px;
            color: var(--ok);
            font-weight: 600;
        }

        .errors {
            margin-top: 14px;
            color: var(--error);
            font-weight: 600;
        }

        .section-list {
            margin-top: 20px;
            display: grid;
            gap: 14px;
        }

        details.section {
            border: 1px solid var(--line);
            border-radius: 16px;
            background: var(--panel);
            overflow: clip;
            box-shadow: 0 8px 18px rgba(31, 42, 48, 0.07);
        }

        details.section[open] {
            border-color: #bfb09f;
        }

        summary {
            list-style: none;
            cursor: pointer;
            padding: 16px 18px;
            background: linear-gradient(90deg, #fff6eb 0%, #f3f8f2 100%);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
        }

        summary::-webkit-details-marker { display: none; }

        .section-title {
            margin: 0;
            font-weight: 700;
            font-size: 1.04rem;
        }

        .section-desc {
            margin: 4px 0 0;
            font-size: 0.9rem;
            color: var(--muted);
        }

        .badge {
            border: 1px solid var(--accent);
            color: var(--accent);
            font-size: 0.76rem;
            font-weight: 700;
            border-radius: 999px;
            padding: 4px 10px;
            white-space: nowrap;
            background: #f2fbfa;
        }

        .badge.soft {
            border-color: #d9c7b0;
            color: #6f5b44;
            background: #f8f1e7;
        }

        .section-body {
            padding: 16px;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(140px, 1fr));
            gap: 10px;
            margin-bottom: 14px;
        }

        .field {
            display: grid;
            gap: 4px;
        }

        .field label {
            font-size: 0.8rem;
            color: var(--muted);
        }

        input[type='text'],
        input[type='date'],
        input[type='time'],
        input[type='number'],
        select,
        textarea {
            width: 100%;
            border: 1px solid #cdbfae;
            border-radius: 9px;
            padding: 8px 10px;
            font: inherit;
            background: #fff;
            color: var(--ink);
        }

        textarea {
            min-height: 70px;
            resize: vertical;
        }

        .table-scroll {
            overflow-x: auto;
            border: 1px solid #d6c8b6;
            border-radius: 10px;
            background: #fff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 980px;
        }

        th, td {
            border-bottom: 1px solid #ece0d3;
            padding: 8px;
            text-align: left;
            vertical-align: top;
            font-size: 0.88rem;
        }

        th {
            background: #f6ede2;
            color: #534f49;
            font-weight: 600;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        tr:nth-child(even) td {
            background: #fcfaf7;
        }

        .kind {
            font-size: 0.75rem;
            font-weight: 600;
            color: #7e6d54;
            display: inline-block;
            margin-top: 4px;
        }

        .actions {
            margin-top: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .hint {
            color: var(--muted);
            font-size: 0.84rem;
        }

        details.point-creator {
            border: 1px dashed #cabca9;
            border-radius: 10px;
            background: #fffefb;
            margin-bottom: 14px;
        }

        details.department-manager {
            border: 1px dashed #c5d2c4;
            border-radius: 10px;
            background: #f9fdf8;
            margin-bottom: 14px;
        }

        details.department-manager > summary {
            padding: 10px 12px;
            background: transparent;
            justify-content: flex-start;
            gap: 10px;
        }

        details.point-creator > summary {
            padding: 10px 12px;
            background: transparent;
            justify-content: flex-start;
            gap: 10px;
        }

        .creator-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: #314447;
        }

        .creator-body {
            padding: 10px 12px 12px;
            border-top: 1px dashed #d9cab8;
        }

        .department-body {
            padding: 10px 12px 12px;
            border-top: 1px dashed #c5d2c4;
        }

        .creator-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(140px, 1fr));
            gap: 10px;
        }

        .department-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(140px, 1fr));
            gap: 10px;
        }

        .department-table {
            margin-top: 10px;
            border: 1px solid #dbe6d8;
            border-radius: 10px;
            background: #fff;
            overflow: clip;
        }

        .department-row {
            display: grid;
            grid-template-columns: 1.2fr 1fr 0.8fr auto auto auto;
            gap: 10px;
            padding: 10px;
            border-bottom: 1px solid #edf4eb;
            align-items: end;
        }

        .department-move {
            display: flex;
            gap: 6px;
        }

        .danger-btn {
            border: 1px solid #a73f33;
            background: #c64a3d;
            color: #fff;
        }

        .soft-btn {
            border: 1px solid #9e8a71;
            background: #f4ecdf;
            color: #5b4c3a;
        }

        .btn-small {
            padding: 8px 12px;
            border-radius: 8px;
            font-weight: 700;
            min-width: 44px;
        }

        .department-row:last-child {
            border-bottom: 0;
        }

        .department-row .field {
            margin: 0;
        }

        .group-row td {
            background: #eef7ef;
            font-weight: 700;
            color: #25452c;
            border-top: 1px solid #d4e8d6;
            border-bottom: 1px solid #d4e8d6;
        }

        button {
            border: 0;
            border-radius: 10px;
            padding: 10px 18px;
            font: inherit;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(135deg, var(--accent) 0%, #0f5b57 100%);
            cursor: pointer;
            box-shadow: 0 8px 16px rgba(18, 112, 107, 0.22);
        }

        button:hover {
            filter: brightness(1.04);
        }

        .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--accent-2);
            display: inline-block;
            margin-right: 6px;
        }

        @keyframes reveal {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 920px) {
            .meta-grid { grid-template-columns: repeat(2, minmax(140px, 1fr)); }
            .creator-grid { grid-template-columns: repeat(2, minmax(140px, 1fr)); }
            .department-grid { grid-template-columns: repeat(2, minmax(140px, 1fr)); }
            .department-row { grid-template-columns: 1fr; align-items: stretch; }
            .actions { flex-direction: column; align-items: stretch; }
            button { width: 100%; }
        }
    </style>
</head>
<body>
<div class="wrap">
    <header class="hero">
        <div class="hero-top">
            <h1>Controlli microbiologici dinamici</h1>
            <div class="user-tools">
                @if (auth()->check())
                    <span class="user-pill">{{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
                    <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                        @csrf
                        <button class="logout-btn" type="submit">Logout</button>
                    </form>
                @endif
            </div>
        </div>
        <p>
            Le sezioni e i punti di campionamento arrivano da database.
            Per aggiungere o riordinare i punti (anche prima/dopo altri), basta aggiornare le tabelle
            <span class="dot"></span>senza riscrivere il form.
        </p>

        @if (session('status'))
            <div class="status">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="errors">Verifica i dati inseriti: alcuni campi non sono validi.</div>
        @endif
    </header>

    <section class="section-list">
        @if (auth()->user()?->isAdmin())
            <details class="section" open>
                <summary>
                    <div>
                        <p class="section-title">Gestione reparti per sezione</p>
                        <p class="section-desc">Configurazione centralizzata reparti per tutte le sezioni.</p>
                    </div>
                    <span class="badge soft">Admin</span>
                </summary>

                <div class="section-body">
                    @foreach ($sections as $section)
                        <details class="department-manager">
                            <summary>
                                <span class="creator-title">{{ $section->name }}</span>
                            </summary>
                            <div class="department-body">
                                <form action="{{ route('monitoraggi.departments.store', $section) }}" method="POST">
                                    @csrf
                                    <div class="department-grid">
                                        <div class="field">
                                            <label for="new_department_name_global_{{ $section->id }}">Nome reparto</label>
                                            <input id="new_department_name_global_{{ $section->id }}" type="text" name="name" maxlength="120" required placeholder="es. Laminar flow">
                                        </div>
                                        <div class="field">
                                            <label for="new_department_code_global_{{ $section->id }}">Codice reparto (opzionale)</label>
                                            <input id="new_department_code_global_{{ $section->id }}" type="text" name="code" maxlength="50" placeholder="es. laminar">
                                        </div>
                                    </div>
                                    <div class="actions" style="margin-top:10px;">
                                        <p class="hint">I reparti sono specifici della sezione corrente e non vengono condivisi automaticamente tra sezioni diverse.</p>
                                        <button type="submit">Aggiungi reparto</button>
                                    </div>
                                </form>

                                <div class="department-table">
                                    @forelse ($section->departments as $department)
                                        <form class="department-row" action="{{ route('monitoraggi.departments.update', [$section, $department]) }}" method="POST">
                                            @csrf
                                            @method('PATCH')

                                            <div class="field">
                                                <label for="department_name_global_{{ $section->id }}_{{ $department->id }}">Nome reparto</label>
                                                <input id="department_name_global_{{ $section->id }}_{{ $department->id }}" type="text" name="name" maxlength="120" value="{{ $department->name }}" required>
                                            </div>

                                            <div class="field">
                                                <label for="department_code_global_{{ $section->id }}_{{ $department->id }}">Codice</label>
                                                <input id="department_code_global_{{ $section->id }}_{{ $department->id }}" type="text" name="code" maxlength="50" value="{{ $department->code }}">
                                            </div>

                                            <div class="field">
                                                <label for="department_active_global_{{ $section->id }}_{{ $department->id }}">Attivo</label>
                                                <select id="department_active_global_{{ $section->id }}_{{ $department->id }}" name="is_active">
                                                    <option value="1" @selected($department->is_active)>Si</option>
                                                    <option value="0" @selected(! $department->is_active)>No</option>
                                                </select>
                                            </div>

                                            <div class="department-move">
                                                <button
                                                    type="submit"
                                                    class="btn-small"
                                                    title="Sposta su"
                                                    name="direction"
                                                    value="up"
                                                    formaction="{{ route('monitoraggi.departments.move', [$section, $department]) }}"
                                                >↑</button>
                                                <button
                                                    type="submit"
                                                    class="btn-small"
                                                    title="Sposta giu"
                                                    name="direction"
                                                    value="down"
                                                    formaction="{{ route('monitoraggi.departments.move', [$section, $department]) }}"
                                                >↓</button>
                                            </div>

                                            <div class="department-move">
                                                @if ($department->is_active)
                                                    <button type="submit" class="btn-small soft-btn" name="quick_action" value="hide">Oscura</button>
                                                @else
                                                    <button type="submit" class="btn-small soft-btn" name="quick_action" value="show">Riattiva</button>
                                                @endif
                                                <button type="submit" class="btn-small danger-btn" name="quick_action" value="delete" onclick="return confirm('Confermi eliminazione reparto? I punti collegati andranno in Senza reparto.');">Elimina</button>
                                            </div>

                                            <button type="submit" class="btn-small">Salva reparto</button>
                                        </form>
                                    @empty
                                        <div class="department-row">
                                            <p class="hint" style="grid-column: 1 / -1; margin: 0;">Nessun reparto configurato per questa sezione.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </details>
                    @endforeach
                </div>
            </details>
        @endif

        @forelse ($sections as $section)
            @php
                $groupedPoints = $section->samplingPoints->groupBy(
                    fn ($point) => $point->department?->name ?: 'Senza reparto'
                );
            @endphp
            <details class="section">
                <summary>
                    <div>
                        <p class="section-title">{{ $section->name }}</p>
                        <p class="section-desc">{{ $section->description }}</p>
                    </div>
                    <div style="display:flex; gap:8px; flex-wrap:wrap; justify-content:flex-end;">
                        <span class="badge soft">{{ $section->departments->count() }} reparti</span>
                        <span class="badge">{{ $section->samplingPoints->count() }} punti</span>
                    </div>
                </summary>

                <div class="section-body">
                    @if (auth()->user()?->isAdmin())
                    <details class="point-creator">
                        <summary>
                            <span class="creator-title">Definisci nuovo campionamento (punto dinamico)</span>
                        </summary>
                        <div class="creator-body">
                            <form action="{{ route('monitoraggi.points.store', $section) }}" method="POST">
                                @csrf
                                <div class="creator-grid">
                                    <div class="field">
                                        <label for="new_legacy_{{ $section->id }}">ID legacy (opzionale)</label>
                                        <input id="new_legacy_{{ $section->id }}" type="text" name="legacy_code" maxlength="50" placeholder="es. 999">
                                    </div>
                                    <div class="field" style="grid-column: span 2;">
                                        <label for="new_title_{{ $section->id }}">Descrizione punto</label>
                                        <input id="new_title_{{ $section->id }}" type="text" name="title" maxlength="255" required placeholder="Nuovo punto campionamento demo">
                                    </div>
                                    <div class="field">
                                        <label for="new_department_{{ $section->id }}">Reparto</label>
                                        <select id="new_department_{{ $section->id }}" name="monitoring_department_id" data-department-select data-section-id="{{ $section->id }}">
                                            <option value="">Senza reparto</option>
                                            @foreach ($section->departments as $department)
                                                @if ($department->is_active)
                                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="field">
                                        <label for="new_area_{{ $section->id }}">Area dettagliata (opzionale)</label>
                                        <input id="new_area_{{ $section->id }}" type="text" name="area_label" maxlength="255" placeholder="es. Reparto test">
                                    </div>
                                    <div class="field">
                                        <label for="new_kind_{{ $section->id }}">Tipo campionamento</label>
                                        <select id="new_kind_{{ $section->id }}" name="sample_kind">
                                            <option value="air_active">Aria attiva</option>
                                            <option value="surface_contact">Superficie contact plate</option>
                                            <option value="surface_swab">Superficie swab</option>
                                        </select>
                                    </div>
                                    <div class="field">
                                        <label for="new_volume_{{ $section->id }}">Volume standard (L)</label>
                                        <input id="new_volume_{{ $section->id }}" type="number" min="0" name="default_volume_liters" placeholder="1000">
                                    </div>
                                    <div class="field">
                                        <label for="new_op_{{ $section->id }}">Richiede stato operativo</label>
                                        <select id="new_op_{{ $section->id }}" name="requires_operational_status">
                                            <option value="1" selected>Si</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                    <div class="field">
                                        <label for="new_lot_{{ $section->id }}">Richiede lotto prodotto</label>
                                        <select id="new_lot_{{ $section->id }}" name="requires_product_lot">
                                            <option value="1" selected>Si</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                    <div class="field">
                                        <label for="new_pos_{{ $section->id }}">Posizione</label>
                                        <select id="new_pos_{{ $section->id }}" name="insert_position">
                                            <option value="end" selected>In fondo</option>
                                            <option value="before">Prima del punto selezionato</option>
                                            <option value="after">Dopo il punto selezionato</option>
                                        </select>
                                    </div>
                                    <div class="field" style="grid-column: span 2;">
                                        <label for="new_anchor_{{ $section->id }}">Punto di riferimento (per prima/dopo)</label>
                                        <select id="new_anchor_{{ $section->id }}" name="anchor_point_id" data-anchor-select data-section-id="{{ $section->id }}">
                                            <option value="">Nessuno (usa in fondo)</option>
                                            @foreach ($section->samplingPoints as $point)
                                                <option value="{{ $point->id }}" data-department-id="{{ $point->monitoring_department_id ?? '' }}">{{ $point->department?->name ?: 'Senza reparto' }}: {{ $point->title }} {{ $point->sample_kind }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="actions" style="margin-top:10px;">
                                    <p class="hint">Questo bottone e solo demo: crea un nuovo punto campionamento direttamente da interfaccia.</p>
                                    <button type="submit">Aggiungi nuovo campionamento</button>
                                </div>
                            </form>
                        </div>
                    </details>
                    @endif

                    @if (auth()->user()?->isOperatore())
                    <form action="{{ route('monitoraggi.checks.store', $section) }}" method="POST">
                        @csrf

                        <div class="meta-grid">
                            <div class="field">
                                <label for="sampled_on_{{ $section->id }}">Data prelievo</label>
                                <input id="sampled_on_{{ $section->id }}" type="date" name="sampled_on" value="{{ old('sampled_on', now()->toDateString()) }}" required>
                            </div>
                            <div class="field">
                                <label for="incubation_started_on_{{ $section->id }}">Inizio incubazione</label>
                                <input id="incubation_started_on_{{ $section->id }}" type="date" name="incubation_started_on" value="{{ old('incubation_started_on') }}">
                            </div>
                            <div class="field">
                                <label for="first_reading_on_{{ $section->id }}">1a lettura</label>
                                <input id="first_reading_on_{{ $section->id }}" type="date" name="first_reading_on" value="{{ old('first_reading_on') }}">
                            </div>
                            <div class="field">
                                <label for="second_reading_on_{{ $section->id }}">2a lettura</label>
                                <input id="second_reading_on_{{ $section->id }}" type="date" name="second_reading_on" value="{{ old('second_reading_on') }}">
                            </div>
                            <div class="field">
                                <label for="operator_name_{{ $section->id }}">Operatore</label>
                                <input id="operator_name_{{ $section->id }}" type="text" name="operator_name" value="{{ old('operator_name') }}" maxlength="120">
                            </div>
                            <div class="field">
                                <label for="cq_operator_name_{{ $section->id }}">Operatore CQ</label>
                                <input id="cq_operator_name_{{ $section->id }}" type="text" name="cq_operator_name" value="{{ old('cq_operator_name') }}" maxlength="120">
                            </div>
                            <div class="field">
                                <label for="media_lot_{{ $section->id }}">Lotto piastre</label>
                                <input id="media_lot_{{ $section->id }}" type="text" name="media_lot" value="{{ old('media_lot') }}" maxlength="120">
                            </div>
                            <div class="field">
                                <label for="swab_lot_{{ $section->id }}">Lotto provette/swab</label>
                                <input id="swab_lot_{{ $section->id }}" type="text" name="swab_lot" value="{{ old('swab_lot') }}" maxlength="120">
                            </div>
                        </div>

                        <div class="table-scroll">
                            <table>
                                <thead>
                                <tr>
                                    <th>ID legacy</th>
                                    <th>Descrizione punto</th>
                                    <th>Reparto</th>
                                    <th>Area dettagliata</th>
                                    <th>Ora</th>
                                    <th>Operativo</th>
                                    <th>Lotto prodotto</th>
                                    <th>Volume (L)</th>
                                    <th>CFU</th>
                                    <th>Note</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($groupedPoints as $departmentName => $points)
                                    <tr class="group-row">
                                        <td colspan="10">Reparto: {{ $departmentName }}</td>
                                    </tr>

                                    @foreach ($points as $point)
                                        <tr>
                                            <td>{{ $point->legacy_code ?: '-' }}</td>
                                            <td>
                                                {{ $point->title }}
                                                <div class="kind">{{ $point->sample_kind }}</div>
                                            </td>
                                            <td>{{ $point->department?->name ?: 'Senza reparto' }}</td>
                                            <td>{{ $point->area_label ?: '-' }}</td>
                                            <td>
                                                <input type="time" name="points[{{ $point->id }}][sampled_at]" value="{{ old("points.{$point->id}.sampled_at") }}">
                                            </td>
                                            <td>
                                                @if ($point->requires_operational_status)
                                                    <select name="points[{{ $point->id }}][is_operational]">
                                                        <option value="">-</option>
                                                        <option value="1" @selected(old("points.{$point->id}.is_operational") === '1')>Si</option>
                                                        <option value="0" @selected(old("points.{$point->id}.is_operational") === '0')>No</option>
                                                    </select>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if ($point->requires_product_lot)
                                                    <input type="text" name="points[{{ $point->id }}][product_lot]" value="{{ old("points.{$point->id}.product_lot") }}" maxlength="120">
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $point->default_volume_liters ?? '-' }}</td>
                                            <td>
                                                <input type="number" min="0" name="points[{{ $point->id }}][cfu_count]" value="{{ old("points.{$point->id}.cfu_count") }}">
                                            </td>
                                            <td>
                                                <input type="text" name="points[{{ $point->id }}][notes]" value="{{ old("points.{$point->id}.notes") }}" maxlength="500">
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="field" style="margin-top: 12px;">
                            <label for="notes_{{ $section->id }}">Note sezione</label>
                            <textarea id="notes_{{ $section->id }}" name="notes">{{ old('notes') }}</textarea>
                        </div>

                        <div class="actions">
                            <p class="hint">
                                Ogni form salva solo questa sezione. L'ordinamento dei punti e la loro attivazione sono gestiti da DB.
                            </p>
                            <button type="submit">Salva sezione</button>
                        </div>
                    </form>
                    @else
                    <div class="actions" style="margin-top:12px;">
                        <p class="hint">
                            La compilazione del campionamento e consentita solo agli utenti con ruolo operatore.
                        </p>
                    </div>
                    @endif
                </div>
            </details>
        @empty
            <details class="section" open>
                <summary>
                    <div>
                        <p class="section-title">Nessuna sezione configurata</p>
                        <p class="section-desc">Esegui migration + seeder per popolare i template iniziali.</p>
                    </div>
                    <span class="badge">0 punti</span>
                </summary>
            </details>
        @endforelse
    </section>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-department-select]').forEach(function (departmentSelect) {
            var sectionId = departmentSelect.getAttribute('data-section-id');
            var anchorSelect = document.querySelector('[data-anchor-select][data-section-id="' + sectionId + '"]');

            if (!anchorSelect) {
                return;
            }

            var syncAnchorOptions = function () {
                var selectedDepartmentId = departmentSelect.value;

                Array.from(anchorSelect.options).forEach(function (option, index) {
                    if (index === 0) {
                        option.hidden = false;
                        return;
                    }

                    var optionDepartmentId = option.getAttribute('data-department-id') || '';
                    var visible = selectedDepartmentId === '' || optionDepartmentId === selectedDepartmentId;
                    option.hidden = !visible;
                });

                if (anchorSelect.selectedIndex > 0 && anchorSelect.options[anchorSelect.selectedIndex].hidden) {
                    anchorSelect.value = '';
                }
            };

            departmentSelect.addEventListener('change', syncAnchorOptions);
            syncAnchorOptions();
        });
    });
</script>
</body>
</html>
