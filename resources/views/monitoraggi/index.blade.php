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
            .actions { flex-direction: column; align-items: stretch; }
            button { width: 100%; }
        }
    </style>
</head>
<body>
<div class="wrap">
    <header class="hero">
        <h1>Controlli microbiologici dinamici</h1>
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
        @forelse ($sections as $section)
            <details class="section" @if($loop->first) open @endif>
                <summary>
                    <div>
                        <p class="section-title">{{ $section->name }}</p>
                        <p class="section-desc">{{ $section->description }}</p>
                    </div>
                    <span class="badge">{{ $section->samplingPoints->count() }} punti</span>
                </summary>

                <div class="section-body">
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
                                    <th>Reparto / Area</th>
                                    <th>Ora</th>
                                    <th>Operativo</th>
                                    <th>Lotto prodotto</th>
                                    <th>Volume (L)</th>
                                    <th>CFU</th>
                                    <th>Note</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($section->samplingPoints as $point)
                                    <tr>
                                        <td>{{ $point->legacy_code ?: '-' }}</td>
                                        <td>
                                            {{ $point->title }}
                                            <div class="kind">{{ $point->sample_kind }}</div>
                                        </td>
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
</body>
</html>
