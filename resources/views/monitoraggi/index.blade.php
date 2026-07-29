<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Campionamenti microbiologici</title>
    @vite('resources/js/app.js')
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
            margin: 0;
            font-size: clamp(1.4rem, 1.8vw, 2rem);
            letter-spacing: 0.3px;
        }

        .hero p {
            margin: 10px 0 0;
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

        .menu {
            margin-top: 16px;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .menu-stack {
            margin-top: 16px;
            display: grid;
            gap: 10px;
        }

        .menu-admin-row {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .menu-admin-label {
            font-size: 0.74rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #8a735d;
            padding-right: 4px;
        }

        .menu-link {
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 999px;
            border: 1px solid #c9baa8;
            background: #fff6ed;
            color: #5a4a3a;
            font-weight: 700;
            font-size: 0.86rem;
        }

        .menu-link.active {
            border-color: var(--accent);
            background: #e8f4f3;
            color: #114b47;
        }

        .env-switch {
            margin-top: 12px;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .production-phase-switch {
            margin-bottom: 20px;
        }

        .production-phase-locked {
            opacity: 0.62;
            pointer-events: none;
        }

        .env-link {
            text-decoration: none;
            padding: 7px 11px;
            border-radius: 9px;
            border: 1px solid #d4c9ba;
            color: #6a5b49;
            background: #fff;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .env-link.active {
            border-color: #1f7a75;
            color: #0f5a56;
            background: #eef9f8;
        }

        .env-link[aria-disabled="true"] {
            color: #9b8d7f;
            background: #f5f0e9;
            cursor: not-allowed;
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

        .water-wizard {
            margin-bottom: 14px;
            border: 1px solid #b8d2cb;
            border-radius: 8px;
            background: #f3faf8;
            padding: 12px;
        }

        .water-wizard-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .water-wizard-title {
            margin: 0;
            font-size: 0.95rem;
            font-weight: 700;
        }

        .water-step-indicator {
            color: #0f5a56;
            font-size: 0.82rem;
            font-weight: 700;
        }

        .water-sampling-sheet {
            border: 1px solid #b7aaa0;
            border-radius: 8px;
            background: #fff;
            margin-bottom: 14px;
            overflow: hidden;
        }

        .water-sheet-title {
            margin: 0;
            background: #f1e7dc;
            border-bottom: 1px solid #b7aaa0;
            font-size: 0.92rem;
            font-weight: 700;
            padding: 10px 12px;
        }

        .water-sheet-header {
            display: grid;
            grid-template-columns: repeat(4, minmax(140px, 1fr));
            gap: 0;
            border-bottom: 1px solid #d9cec2;
        }

        .water-sheet-header .field {
            border-right: 1px solid #d9cec2;
            padding: 10px;
        }

        .water-sheet-header .field:last-child {
            border-right: 0;
        }

        .water-media-sheet {
            display: grid;
            grid-template-columns: 150px repeat(4, minmax(150px, 1fr));
            overflow-x: auto;
        }

        .water-media-cell {
            border-right: 1px solid #d9cec2;
            border-bottom: 1px solid #d9cec2;
            min-width: 150px;
            padding: 8px;
        }

        .water-media-cell:nth-child(5n) {
            border-right: 0;
        }

        .water-media-label {
            align-items: center;
            background: #fbf6f0;
            color: #5f554a;
            display: flex;
            font-size: 0.78rem;
            font-weight: 700;
        }

        .water-media-heading {
            background: #f6ede2;
            font-size: 0.8rem;
            font-weight: 700;
            min-height: 66px;
        }

        .water-media-fixed {
            color: #4d625e;
            font-size: 0.82rem;
            font-weight: 600;
        }

        .water-signature-row {
            display: grid;
            grid-template-columns: repeat(3, minmax(160px, 1fr));
            gap: 10px;
            padding: 10px;
        }

        .water-point-list {
            display: grid;
            gap: 8px;
        }

        .water-point-card {
            width: 100%;
            border: 1px solid #d6c8b6;
            border-radius: 8px;
            background: #fff;
            color: var(--ink);
            cursor: pointer;
            display: grid;
            grid-template-columns: minmax(190px, 1.5fr) repeat(5, minmax(90px, 1fr));
            gap: 10px;
            padding: 12px;
            text-align: left;
        }

        .water-point-card:hover,
        .water-point-card:focus-visible {
            border-color: var(--accent);
            outline: 2px solid #b9dfda;
            outline-offset: 2px;
        }

        .water-point-name,
        .water-point-summary {
            display: grid;
            gap: 3px;
        }

        .water-point-name strong {
            font-size: 0.9rem;
        }

        .water-point-summary span:first-child {
            color: var(--muted);
            font-size: 0.72rem;
            font-weight: 600;
        }

        .water-point-summary span:last-child {
            font-size: 0.82rem;
            font-weight: 700;
        }

        dialog.water-point-dialog {
            width: min(760px, calc(100vw - 24px));
            max-height: calc(100vh - 24px);
            border: 1px solid #b8d2cb;
            border-radius: 8px;
            color: var(--ink);
            padding: 0;
            box-shadow: 0 18px 48px rgba(31, 42, 48, 0.28);
        }

        dialog.water-point-dialog::backdrop {
            background: rgba(31, 42, 48, 0.48);
        }

        .water-dialog-body {
            display: grid;
            gap: 14px;
            max-height: calc(100vh - 24px);
            overflow-y: auto;
            padding: 18px;
        }

        .water-dialog-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
        }

        .water-dialog-head h3,
        .water-dialog-head p {
            margin: 0;
        }

        .water-result-group {
            border: 1px solid #ded3c6;
            border-radius: 8px;
            margin: 0;
            padding: 10px;
        }

        .water-result-group legend {
            padding: 0 5px;
            font-size: 0.84rem;
            font-weight: 700;
        }

        .water-modal-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(130px, 1fr));
            gap: 10px;
        }

        @media (max-width: 760px) {
            .water-sheet-header,
            .water-signature-row {
                grid-template-columns: 1fr;
            }

            .water-sheet-header .field {
                border-bottom: 1px solid #d9cec2;
                border-right: 0;
            }

            .water-point-card {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .water-point-name {
                grid-column: 1 / -1;
            }

            .water-modal-grid {
                grid-template-columns: 1fr;
            }
        }

        .field {
            display: grid;
            gap: 4px;
        }

        .field[hidden] {
            display: none !important;
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

        .archive-card,
        .trend-card {
            border: 1px solid var(--line);
            border-radius: 14px;
            background: #fffdf9;
            padding: 14px;
        }

        .archive-grid {
            display: grid;
            gap: 10px;
            margin-top: 12px;
        }

        .archive-filters {
            margin-top: 12px;
            border: 1px solid #ddd0c1;
            border-radius: 12px;
            background: #fff;
            padding: 10px;
            display: grid;
            gap: 10px;
        }

        .archive-filters-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(120px, 1fr));
            gap: 10px;
            align-items: end;
        }

        .archive-pagination {
            margin-top: 12px;
            border: 1px solid #ddd0c1;
            border-radius: 12px;
            background: #fff;
            padding: 10px;
        }

        .archive-date-group {
            border: 1px solid #ddd0c1;
            border-radius: 12px;
            background: #fff;
            padding: 10px;
        }

        .archive-date-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 8px;
        }

        .archive-date-title {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            color: #304149;
        }

        .archive-section-tags {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            margin-top: 6px;
        }

        .archive-section-tag {
            background: #2f8f5f;
            color: #fff;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 3px 10px;
        }

        .archive-item {
            border: 1px solid #e2d7ca;
            border-radius: 12px;
            background: #fff;
            padding: 10px;
            display: grid;
            gap: 5px;
        }

        .archive-water-meta {
            margin-top: 8px;
            display: grid;
            grid-template-columns: repeat(3, minmax(180px, 1fr));
            gap: 8px;
        }

        .archive-water-meta .hint {
            display: block;
        }

        .archive-water-table {
            margin-top: 10px;
        }

        .trend-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(220px, 1fr));
            gap: 12px;
            margin-top: 12px;
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

        details.department-manager > summary,
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

        .water-phase-panel {
            display: grid;
            grid-template-columns: minmax(220px, 280px) 1fr;
            gap: 14px;
            align-items: end;
            margin: 14px 0 10px;
        }

        .water-phase-panel .hint {
            margin: 0;
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

        @keyframes reveal {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 920px) {
            .meta-grid,
            .creator-grid,
            .department-grid,
            .trend-grid,
            .archive-filters-grid {
                grid-template-columns: repeat(1, minmax(140px, 1fr));
            }

            .water-phase-panel {
                grid-template-columns: 1fr;
                align-items: stretch;
            }

            .department-row {
                grid-template-columns: 1fr;
                align-items: stretch;
            }

            .actions {
                flex-direction: column;
                align-items: stretch;
            }

            button { width: 100%; }
        }
    </style>
</head>
<body data-server-time="{{ now()->format('H:i') }}">
<div class="wrap">
    <header class="hero">
        <div class="hero-top">
            <h1>Controlli microbiologici</h1>
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

        <p>Entry point applicativo con menu funzioni e selezione ambiente di campionamento.</p>

        <div class="menu-stack">
            <nav class="menu" aria-label="Menu principale">
                @foreach ($menuItems as $menuItem)
                    <a
                        href="{{ route('monitoraggi.index', array_filter(['view' => $menuItem['key'], 'env' => $currentEnvironment, 'sub' => $currentSubEnvironment])) }}"
                        class="menu-link {{ $currentView === $menuItem['key'] ? 'active' : '' }}"
                    >
                        {{ $menuItem['label'] }}
                    </a>
                @endforeach
            </nav>

            @if (! empty($adminMenuItems))
                <div class="menu-admin-row">
                    <span class="menu-admin-label">Amministrazione</span>
                    <nav class="menu" aria-label="Menu amministrazione" style="margin-top: 0;">
                        @foreach ($adminMenuItems as $menuItem)
                            <a
                                href="{{ route('monitoraggi.index', array_filter(['view' => $menuItem['key'], 'env' => $currentEnvironment, 'sub' => $currentSubEnvironment])) }}"
                                class="menu-link {{ $currentView === $menuItem['key'] ? 'active' : '' }}"
                            >
                                {{ $menuItem['label'] }}
                            </a>
                        @endforeach
                    </nav>
                </div>
            @endif
        </div>

        <nav class="env-switch" aria-label="Selezione ambiente">
            @foreach ($availableEnvironments as $envCode)
                <a
                    href="{{ route('monitoraggi.index', ['view' => $currentView, 'env' => $envCode]) }}"
                    class="env-link {{ $currentEnvironment === $envCode ? 'active' : '' }}"
                >
                    {{ $environmentLabels[$envCode] ?? ucfirst(str_replace('_', ' ', $envCode)) }}
                </a>
            @endforeach
        </nav>

        @if ($availableSubEnvironments->isNotEmpty())
            <nav class="env-switch" aria-label="Selezione sotto-ambiente" style="margin-top: 10px;">
                @foreach ($availableSubEnvironments as $subCode => $subLabel)
                    <a
                        href="{{ route('monitoraggi.index', ['view' => $currentView, 'env' => $currentEnvironment, 'sub' => $subCode]) }}"
                        class="env-link {{ $currentSubEnvironment === $subCode ? 'active' : '' }}"
                    >
                        {{ $subLabel }}
                    </a>
                @endforeach
            </nav>
        @endif

        @if (session('status'))
            <div class="status">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="errors">Verifica i dati inseriti: alcuni campi non sono validi.</div>
        @endif
    </header>

    <section class="section-list">
        @if ($currentView === 'archivio')
            <article class="archive-card">
                <h2 class="section-title" style="margin-bottom: 8px;">Archivio campionamenti</h2>
                <p class="hint">Ambiente attivo: {{ $environmentLabels[$currentEnvironment] ?? $currentEnvironment }}. Ricerca su data campionamento e paginazione server-side.</p>

                <form method="GET" action="{{ route('monitoraggi.index') }}" class="archive-filters">
                    <input type="hidden" name="view" value="archivio">
                    <input type="hidden" name="env" value="{{ $currentEnvironment }}">
                    @if (filled($currentSubEnvironment))
                        <input type="hidden" name="sub" value="{{ $currentSubEnvironment }}">
                    @endif

                    <div class="archive-filters-grid">
                        <div class="field">
                            <label for="archive_from">Da data campionamento</label>
                            <input id="archive_from" type="date" name="archive_from" value="{{ $archiveFrom }}">
                        </div>
                        <div class="field">
                            <label for="archive_to">A data campionamento</label>
                            <input id="archive_to" type="date" name="archive_to" value="{{ $archiveTo }}">
                        </div>
                        <div class="field">
                            <label for="archive_per_page">Record per pagina</label>
                            <select id="archive_per_page" name="archive_per_page">
                                <option value="10" @selected((int) $archivePerPage === 10)>10</option>
                                <option value="20" @selected((int) $archivePerPage === 20)>20</option>
                                <option value="50" @selected((int) $archivePerPage === 50)>50</option>
                                <option value="100" @selected((int) $archivePerPage === 100)>100</option>
                            </select>
                        </div>
                        @if (auth()->user()?->isAdmin())
                            <div class="field">
                                <label for="archive_status">Stato</label>
                                <select id="archive_status" name="archive_status">
                                    <option value="active" @selected($archiveStatus === 'active')>Attivi</option>
                                    <option value="deleted" @selected($archiveStatus === 'deleted')>Eliminati</option>
                                </select>
                            </div>
                        @endif
                        <div class="field">
                            <button type="submit">Cerca</button>
                        </div>
                    </div>

                    <div class="actions" style="margin-top:0;">
                        <p class="hint">Intervallo date facoltativo. Vuoto = tutte le date disponibili.</p>
                        <a class="menu-link" href="{{ route('monitoraggi.index', array_filter(['view' => 'archivio', 'env' => $currentEnvironment, 'sub' => $currentSubEnvironment])) }}">Reset filtri</a>
                    </div>
                </form>

                <div class="archive-grid">
                    @php
                        $archiveItems = method_exists($archiveChecks, 'getCollection') ? $archiveChecks->getCollection() : collect($archiveChecks);
                        $checksByDate = $archiveItems->groupBy('sampled_on');
                    @endphp
                    @forelse ($checksByDate as $sampledOn => $checksOnDate)
                        <div class="archive-date-group">
                            <div class="archive-date-head">
                                <h3 class="archive-date-title">{{ \Carbon\Carbon::parse($sampledOn)->format('d-m-Y') }}</h3>
                                <span class="hint">{{ $checksOnDate->count() }} sezioni compilate</span>
                            </div>

                            <div class="archive-section-tags">
                                @foreach ($checksOnDate->pluck('section.name')->filter()->unique() as $sectionName)
                                    <span class="archive-section-tag">{{ $sectionName }}</span>
                                @endforeach
                            </div>

                            @foreach ($checksOnDate as $check)
                                <div class="archive-item" style="margin-top:8px;">
                                    @php
                                        $hasCheckSignature = $check->phaseStates->contains(fn ($state) => filled($state->signed_at))
                                            || collect([
                                                $check->sampling_completed_signature,
                                                $check->first_reading_completed_signature,
                                                $check->second_reading_completed_signature,
                                                $check->incubation_started_signature,
                                                $check->incubation_finished_signature,
                                                $check->sampling_completed_by_user_id,
                                                $check->first_reading_completed_by_user_id,
                                                $check->second_reading_completed_by_user_id,
                                            ])->contains(fn ($value) => filled($value));
                                    @endphp
                                    <strong>
                                        @if (! $check->trashed())
                                            <a href="{{ route('monitoraggi.index', array_filter(['view' => 'nuovo', 'env' => $currentEnvironment, 'sub' => $currentSubEnvironment, 'edit_check' => $check->id])) }}" style="color: inherit; text-decoration: none;">
                                                {{ $check->section?->name ?? 'Sezione rimossa' }}
                                            </a>
                                        @else
                                            {{ $check->section?->name ?? 'Sezione rimossa' }}
                                        @endif
                                    </strong>
                                    @if ($check->trashed())
                                        <span class="badge soft">Eliminato</span>
                                    @endif
                                    @if ($currentEnvironment === 'acque')
                                        <span class="badge soft">Acque</span>
                                    @endif
                                    <span class="hint">Punti compilati: {{ $check->point_results_count }}</span>
                                    <span class="hint">Operatore: {{ $check->operator_name ?: ($check->author?->name ?: '-') }}</span>
                                    @if ($currentEnvironment === 'acque' && $check->sampled_time)
                                        <span class="hint">Ora prelievo: {{ substr((string) $check->sampled_time, 0, 5) }}</span>
                                    @endif
                                    <span class="hint">Salvato il: {{ optional($check->created_at)->format('d-m-Y H:i') ?: '-' }}</span>
                                    @if (! $check->trashed())
                                        <span class="hint"><a href="{{ route('monitoraggi.index', array_filter(['view' => 'nuovo', 'env' => $currentEnvironment, 'sub' => $currentSubEnvironment, 'edit_check' => $check->id])) }}">Apri in modifica</a></span>
                                    @endif

                                    <div class="actions" style="margin-top:8px;">
                                        @if (auth()->user()?->isOperatore() && ! $check->trashed())
                                            <form action="{{ route('monitoraggi.checks.delete', $check) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                @if ($hasCheckSignature)
                                                    <label class="hint" for="delete_reason_{{ $check->id }}">Motivazione eliminazione</label>
                                                    <input id="delete_reason_{{ $check->id }}" type="text" name="deletion_reason" maxlength="1000" required placeholder="Motivazione obbligatoria">
                                                @endif
                                                <button type="submit" class="btn-small danger-btn" onclick="return confirm('Confermi l\'eliminazione del campionamento? Un admin potra ripristinarlo.');">{{ $hasCheckSignature ? 'Elimina con motivazione' : 'Elimina' }}</button>
                                            </form>
                                        @endif
                                        @if (auth()->user()?->isAdmin() && $check->trashed())
                                            <form action="{{ route('monitoraggi.checks.restore', $check) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn-small">Ripristina</button>
                                            </form>
                                            <span class="hint">Eliminato da {{ $check->deletedBy?->name ?: '-' }} il {{ $check->deleted_at?->format('d-m-Y H:i') ?: '-' }}</span>
                                        @endif
                                    </div>

                                    @if ($currentEnvironment === 'acque')
                                        <div class="archive-water-meta">
                                            <span class="hint">Operatore CQ: {{ $check->cq_operator_name ?: '-' }}</span>
                                            <span class="hint">Firma inizio incubazione: {{ $check->incubation_started_signature ?: '-' }}</span>
                                            <span class="hint">Firma fine incubazione: {{ $check->incubation_finished_signature ?: '-' }}</span>
                                            <span class="hint">Membrana: {{ $check->membrane_lot ?: '-' }}</span>
                                            <span class="hint">Sterilizzazione flaconi: {{ $check->bottle_sterilization_lot ?: '-' }}</span>
                                            <span class="hint">R2A Agar: {{ $check->r2a_agar_lot ?: '-' }}</span>
                                            <span class="hint">Coliform Agar/TTC: {{ $check->coliform_agar_lot ?: '-' }}</span>
                                            <span class="hint">Pseudomonas CN: {{ $check->pseudomonas_cn_lot ?: '-' }}</span>
                                            <span class="hint">Slanetz Bartley: {{ $check->slanetz_bartley_lot ?: '-' }}</span>
                                        </div>

                                        @if ($check->pointResults->isNotEmpty())
                                            <div class="table-scroll archive-water-table">
                                                <table>
                                                    <thead>
                                                    <tr>
                                                        <th>Punto</th>
                                                        <th>Aerobi<br>UFC/piastra</th>
                                                        <th>Aerobi<br>UFC/ml</th>
                                                        <th>Coliformi<br>UFC/piastra</th>
                                                        <th>Coliformi<br>UFC confermate</th>
                                                        <th>Coliformi<br>UFC/100 ml</th>
                                                        <th>Pseudomonas<br>UFC/piastra</th>
                                                        <th>Pseudomonas<br>UFC confermate</th>
                                                        <th>Pseudomonas<br>UFC/100 ml</th>
                                                        <th>Enterococchi<br>UFC/piastra</th>
                                                        <th>Enterococchi<br>UFC confermate</th>
                                                        <th>Enterococchi<br>UFC/100 ml</th>
                                                        <th>pH</th>
                                                        <th>Aspetto</th>
                                                        <th>Risultato finale</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach ($check->pointResults->sortBy(fn ($result) => $result->point?->sort_order ?? 999999) as $pointResult)
                                                        <tr>
                                                            <td>{{ $pointResult->point?->title ?: 'Punto rimosso' }}</td>
                                                            <td>{{ $pointResult->aerobic_plate_cfu ?? '-' }}</td>
                                                            <td>{{ $pointResult->aerobic_cfu_per_ml ?? $pointResult->cfu_count ?? '-' }}</td>
                                                            <td>{{ $pointResult->coliform_plate_cfu ?? '-' }}</td>
                                                            <td>{{ $pointResult->coliform_confirmed_cfu ?? '-' }}</td>
                                                            <td>{{ $pointResult->coliform_cfu_per_100ml ?? '-' }}</td>
                                                            <td>{{ $pointResult->pseudomonas_plate_cfu ?? '-' }}</td>
                                                            <td>{{ $pointResult->pseudomonas_confirmed_cfu ?? '-' }}</td>
                                                            <td>{{ $pointResult->pseudomonas_cfu_per_100ml ?? '-' }}</td>
                                                            <td>{{ $pointResult->enterococci_plate_cfu ?? '-' }}</td>
                                                            <td>{{ $pointResult->enterococci_confirmed_cfu ?? '-' }}</td>
                                                            <td>{{ $pointResult->enterococci_cfu_per_100ml ?? '-' }}</td>
                                                            <td>{{ $pointResult->ph_value ?: '-' }}</td>
                                                            <td>{{ $pointResult->appearance_result === 'conforme' ? 'Conforme' : ($pointResult->appearance_result === 'non_conforme' ? 'Non conforme' : '-') }}</td>
                                                            <td>{{ $pointResult->final_result === 'conforme' ? 'Conforme' : ($pointResult->final_result === 'non_conforme' ? 'Non conforme' : '-') }}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    @endif

                                    @if (auth()->user()?->isAdmin() && $check->phaseLogs->isNotEmpty())
                                        <details style="margin-top: 10px;">
                                            <summary class="hint">Tracciamenti fasi ({{ $check->phaseLogs->count() }})</summary>
                                            <div style="display: grid; gap: 6px; margin-top: 8px;">
                                                @foreach ($check->phaseLogs as $phaseLog)
                                                    <span class="hint">
                                                        {{ $phaseLog->phase === 'archive' ? 'Archivio' : ($productionPhases[$phaseLog->phase] ?? $phaseLog->phase) }}: {{ $phaseLog->action === 'saved_and_signed' ? 'salvata e firmata' : ($phaseLog->action === 'reopened' ? 'riaperta' : ($phaseLog->action === 'soft_deleted' ? 'eliminata' : ($phaseLog->action === 'restored' ? 'ripristinata' : 'salvata'))) }} da {{ $phaseLog->performedBy?->name ?: ('Utente #' . $phaseLog->performed_by_user_id) }} il {{ $phaseLog->logged_at?->format('d-m-Y H:i') ?: '-' }}@if (in_array($phaseLog->action, ['reopened', 'soft_deleted'], true) && $phaseLog->reason) - Motivazione: {{ $phaseLog->reason }}@endif
                                                    </span>
                                                @endforeach
                                            </div>
                                        </details>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @empty
                        <p class="hint">Nessun campionamento presente per l'ambiente selezionato.</p>
                    @endforelse
                </div>

                @if (method_exists($archiveChecks, 'links'))
                    <div class="archive-pagination">
                        {{ $archiveChecks->links() }}
                    </div>
                @endif
            </article>
        @endif

        @if ($currentView === 'trend' && auth()->user()?->isAdmin())
            <article class="trend-card">
                <h2 class="section-title" style="margin-bottom: 8px;">Trend ultimi 90 giorni</h2>
                <p class="hint">Sintesi quantitativa per ambiente e sezione.</p>

                <div class="trend-grid">
                    <div>
                        <h3 style="margin: 0 0 8px;">Per ambiente</h3>
                        @forelse ($trendByEnvironment as $row)
                            <div class="archive-item" style="margin-bottom: 8px;">
                                <strong>{{ $environmentLabels[$row->environment] ?? ucfirst(str_replace('_', ' ', $row->environment)) }}</strong>
                                <span class="hint">Campionamenti: {{ $row->checks_count }}</span>
                            </div>
                        @empty
                            <p class="hint">Nessun dato disponibile.</p>
                        @endforelse
                    </div>
                    <div>
                        <h3 style="margin: 0 0 8px;">Per sezione</h3>
                        @forelse (($trendBySection[$currentEnvironment] ?? collect()) as $row)
                            <div class="archive-item" style="margin-bottom: 8px;">
                                <strong>{{ $row->section_name }}</strong>
                                <span class="hint">Campionamenti: {{ $row->checks_count }}</span>
                            </div>
                        @empty
                            <p class="hint">Nessun dato disponibile per l'ambiente selezionato.</p>
                        @endforelse
                    </div>
                </div>
            </article>
        @endif

        @if ($currentView === 'gestione-reparti' && auth()->user()?->isAdmin())
                <details class="section" open>
                    <summary>
                        <div>
                            <p class="section-title">Gestione reparti per sezione</p>
                            <p class="section-desc">Configurazione reparti nell'ambiente {{ $environmentLabels[$currentEnvironment] ?? $currentEnvironment }}.</p>
                        </div>
                        <span class="badge soft">Admin</span>
                    </summary>

                    <div class="section-body">
                        @foreach ($filteredSections as $section)
                            <details class="department-manager" open>
                                <summary>
                                    <span class="creator-title">
                                        {{ $section->name }}
                                        @if (! $section->is_active)
                                            <span class="badge soft">Oscurato per operatori</span>
                                        @endif
                                    </span>
                                </summary>
                                <div class="department-body">
                                    <form action="{{ route('monitoraggi.sections.visibility', $section) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <div class="actions" style="margin-bottom:10px;">
                                            <p class="hint">Oscurando la sezione, gli operatori non vedranno {{ $section->name }} ne i relativi reparti e punti.</p>
                                            @if ($section->is_active)
                                                <button type="submit" class="btn-small soft-btn" name="visibility_action" value="hide">Oscura sezione</button>
                                            @else
                                                <button type="submit" class="btn-small" name="visibility_action" value="show">Riattiva sezione</button>
                                            @endif
                                        </div>
                                    </form>
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
                                            <div class="field">
                                                <label for="new_department_readings_global_{{ $section->id }}">Numero letture</label>
                                                <input id="new_department_readings_global_{{ $section->id }}" type="number" name="readings_count" min="1" max="10" value="2" required>
                                            </div>
                                        </div>
                                        <div class="actions" style="margin-top:10px;">
                                            <p class="hint">I reparti sono specifici della sezione corrente.</p>
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
                                                    <input id="department_name_global_{{ $section->id }}_{{ $department->id }}" type="text" name="name" maxlength="120" value="{{ $department->name }}" required @disabled($department->trashed())>
                                                </div>

                                                <div class="field">
                                                    <label for="department_code_global_{{ $section->id }}_{{ $department->id }}">Codice</label>
                                                    <input id="department_code_global_{{ $section->id }}_{{ $department->id }}" type="text" name="code" maxlength="50" value="{{ $department->code }}" @disabled($department->trashed())>
                                                </div>

                                                <div class="field">
                                                    <label for="department_readings_global_{{ $section->id }}_{{ $department->id }}">Numero letture</label>
                                                    <input id="department_readings_global_{{ $section->id }}_{{ $department->id }}" type="number" name="readings_count" min="1" max="10" value="{{ $department->readings_count }}" required @disabled($department->trashed())>
                                                </div>

                                                <div class="field">
                                                    <label for="department_active_global_{{ $section->id }}_{{ $department->id }}">Attivo</label>
                                                    <select id="department_active_global_{{ $section->id }}_{{ $department->id }}" name="is_active" @disabled($department->trashed())>
                                                        <option value="1" @selected($department->is_active)>Si</option>
                                                        <option value="0" @selected(! $department->is_active)>No</option>
                                                    </select>
                                                </div>

                                                <div class="department-move">
                                                    @if ($department->trashed())
                                                        <span class="badge soft">Eliminato</span>
                                                        <button type="submit" class="btn-small" formaction="{{ route('monitoraggi.departments.restore', [$section, $department]) }}">Ripristina</button>
                                                    @else
                                                        <button type="submit" class="btn-small" title="Sposta su" name="direction" value="up" formaction="{{ route('monitoraggi.departments.move', [$section, $department]) }}">↑</button>
                                                        <button type="submit" class="btn-small" title="Sposta giu" name="direction" value="down" formaction="{{ route('monitoraggi.departments.move', [$section, $department]) }}">↓</button>
                                                        @if ($department->is_active)
                                                            <button type="submit" class="btn-small soft-btn" name="quick_action" value="hide">Oscura per operatori</button>
                                                        @else
                                                            <button type="submit" class="btn-small soft-btn" name="quick_action" value="show">Riattiva</button>
                                                        @endif
                                                        <button type="submit" class="btn-small danger-btn" name="quick_action" value="delete" data-admin-confirm data-confirm-title="Eliminare il reparto?" data-confirm-text="Il reparto sara rimosso dai dati operativi e potra essere recuperato solo dal pulsante Ripristina.">Elimina (ripristinabile)</button>
                                                    @endif
                                                </div>

                                                @if (! $department->trashed())
                                                    <button type="submit" class="btn-small">Salva reparto</button>
                                                @endif
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

        @if ($currentView === 'nuovo' || $currentView === 'gestione-punti')
            @forelse ($filteredSections as $section)
                @php
                    $groupedPoints = $section->samplingPoints->groupBy(fn ($point) => $point->department?->name ?: 'Senza reparto');
                    $sectionMaximumReadings = max(1, (int) $section->departments->max('readings_count'));
                    $sectionProductionPhases = ['sampling' => 'Fase campionamento'];
                    foreach (range(1, $sectionMaximumReadings) as $readingNumber) {
                        $sectionProductionPhases["reading_{$readingNumber}"] = "Lettura {$readingNumber}";
                    }
                    $currentReadingNumber = preg_match('/^reading_([1-9][0-9]*)$/', $productionPhase, $readingMatches) ? (int) $readingMatches[1] : null;
                    if ($currentReadingNumber) {
                        $groupedPoints = $groupedPoints->map(fn ($points) => $points->filter(
                            fn ($point) => $currentReadingNumber <= (int) ($point->department?->readings_count ?? 2)
                        ))->filter(fn ($points) => $points->isNotEmpty());
                    }
                    $sectionSampleKind = $section->samplingPoints
                        ->where('legacy_code', 'not like', 'NEG%')
                        ->pluck('sample_kind')
                        ->filter()
                        ->first() ?? $section->samplingPoints->pluck('sample_kind')->filter()->first();
                    $isEditingSection = $editingCheck && (int) $editingCheck->monitoring_section_id === (int) $section->id;
                    $editingPointResults = $isEditingSection ? $editingCheck->pointResults->keyBy('sampling_point_id') : collect();
                    $phaseStates = $isEditingSection ? $editingCheck->phaseStates->keyBy('phase') : collect();
                    $activePhaseState = $phaseStates->get($productionPhase);
                    $phaseSignerId = $activePhaseState?->signed_by_user_id;
                    $phaseReopenedAt = $activePhaseState?->reopened_at;
                    $phaseSigned = filled($phaseSignerId);
                    $productionPhaseLocked = $isEditingSection && $phaseSigned && (! filled($phaseReopenedAt) || (int) $phaseSignerId !== (int) auth()->id());
                @endphp
                <details class="section" @if($isEditingSection) open @endif>
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
                        @if ($currentView === 'gestione-punti' && auth()->user()?->isAdmin())
                            <details class="point-creator">
                                <summary>
                                    <span class="creator-title">Definisci nuovo punto campionamento</span>
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
                                                <input id="new_title_{{ $section->id }}" type="text" name="title" maxlength="255" required placeholder="Nuovo punto campionamento">
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
                                                <select id="new_kind_{{ $section->id }}" name="sample_kind" data-sample-kind-select>
                                                    <option value="air_passive">Aria passiva</option>
                                                    <option value="air_active">Aria attiva</option>
                                                    <option value="surface_contact">Superficie contact plate</option>
                                                    <option value="surface_swab">Superficie swab</option>
                                                    <option value="water">Acqua</option>
                                                </select>
                                            </div>
                                            <div class="field" data-sample-setting="volume">
                                                <label for="new_volume_{{ $section->id }}">Volume standard (L)</label>
                                                <input id="new_volume_{{ $section->id }}" type="number" min="0" name="default_volume_liters" placeholder="1000">
                                            </div>
                                            <div class="field" data-sample-setting="exposure">
                                                <label for="new_exposure_{{ $section->id }}">Tempo di esposizione</label>
                                                <select id="new_exposure_{{ $section->id }}" name="default_exposure_hours">
                                                    <option value="">Seleziona</option>
                                                    <option value="3">3 ore</option>
                                                    <option value="4">4 ore</option>
                                                </select>
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
                                                <label for="new_anchor_{{ $section->id }}">Punto riferimento (per prima/dopo)</label>
                                                <select id="new_anchor_{{ $section->id }}" name="anchor_point_id" data-anchor-select data-section-id="{{ $section->id }}">
                                                    <option value="">Nessuno (usa in fondo)</option>
                                                    @foreach ($section->samplingPoints->where('is_active', true) as $point)
                                                        <option value="{{ $point->id }}" data-department-id="{{ $point->monitoring_department_id ?? '' }}">{{ $point->department?->name ?: 'Senza reparto' }}: {{ $point->title }} {{ $sampleKindLabels[$point->sample_kind] ?? $point->sample_kind }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="actions" style="margin-top:10px;">
                                            <p class="hint">Creazione nuovi punti direttamente da interfaccia.</p>
                                            <button type="submit">Aggiungi punto</button>
                                        </div>
                                    </form>

                                    <div class="department-table" style="margin-top: 16px;">
                                        @forelse ($section->samplingPoints as $point)
                                            <form class="department-row" action="{{ route('monitoraggi.points.update', [$section, $point]) }}" method="POST">
                                                @csrf
                                                @method('PATCH')

                                                <div class="field">
                                                    <label for="point_legacy_{{ $section->id }}_{{ $point->id }}">ID legacy</label>
                                                    <input id="point_legacy_{{ $section->id }}_{{ $point->id }}" type="text" name="legacy_code" maxlength="50" value="{{ $point->legacy_code }}">
                                                </div>

                                                <div class="field">
                                                    <label for="point_title_{{ $section->id }}_{{ $point->id }}">Descrizione</label>
                                                    <input id="point_title_{{ $section->id }}_{{ $point->id }}" type="text" name="title" maxlength="255" value="{{ $point->title }}" required>
                                                </div>

                                                <div class="field">
                                                    <label for="point_department_{{ $section->id }}_{{ $point->id }}">Reparto</label>
                                                    <select id="point_department_{{ $section->id }}_{{ $point->id }}" name="monitoring_department_id">
                                                        <option value="">Senza reparto</option>
                                                        @foreach ($section->departments as $department)
                                                            <option value="{{ $department->id }}" @selected((int) $point->monitoring_department_id === (int) $department->id)>
                                                                {{ $department->name }}@if (! $department->is_active) (inattivo) @endif
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="field">
                                                    <label for="point_area_{{ $section->id }}_{{ $point->id }}">Area</label>
                                                    <input id="point_area_{{ $section->id }}_{{ $point->id }}" type="text" name="area_label" maxlength="255" value="{{ $point->area_label }}">
                                                </div>

                                                <div class="field">
                                                    <label for="point_kind_{{ $section->id }}_{{ $point->id }}">Tipo</label>
                                                    <select id="point_kind_{{ $section->id }}_{{ $point->id }}" name="sample_kind" data-sample-kind-select>
                                                        <option value="air_passive" @selected($point->sample_kind === 'air_passive')>Aria passiva</option>
                                                        <option value="air_active" @selected($point->sample_kind === 'air_active')>Aria attiva</option>
                                                        <option value="surface_contact" @selected($point->sample_kind === 'surface_contact')>Superficie contact plate</option>
                                                        <option value="surface_swab" @selected($point->sample_kind === 'surface_swab')>Superficie swab</option>
                                                        <option value="water" @selected($point->sample_kind === 'water')>Acqua</option>
                                                    </select>
                                                </div>

                                                <div class="field" data-sample-setting="volume">
                                                    <label for="point_volume_{{ $section->id }}_{{ $point->id }}">Volume (L)</label>
                                                    <input id="point_volume_{{ $section->id }}_{{ $point->id }}" type="number" min="0" name="default_volume_liters" value="{{ $point->default_volume_liters }}">
                                                </div>
                                                <div class="field" data-sample-setting="exposure">
                                                    <label for="point_exposure_{{ $section->id }}_{{ $point->id }}">Tempo di esposizione</label>
                                                    <select id="point_exposure_{{ $section->id }}_{{ $point->id }}" name="default_exposure_hours">
                                                        <option value="">Seleziona</option>
                                                        <option value="3" @selected((int) $point->default_exposure_hours === 3)>3 ore</option>
                                                        <option value="4" @selected((int) $point->default_exposure_hours === 4)>4 ore</option>
                                                    </select>
                                                </div>

                                                <div class="field">
                                                    <label for="point_operational_{{ $section->id }}_{{ $point->id }}">Richiede stato operativo</label>
                                                    <select id="point_operational_{{ $section->id }}_{{ $point->id }}" name="requires_operational_status">
                                                        <option value="1" @selected($point->requires_operational_status)>Si</option>
                                                        <option value="0" @selected(! $point->requires_operational_status)>No</option>
                                                    </select>
                                                </div>

                                                <div class="field">
                                                    <label for="point_product_lot_{{ $section->id }}_{{ $point->id }}">Richiede lotto prodotto</label>
                                                    <select id="point_product_lot_{{ $section->id }}_{{ $point->id }}" name="requires_product_lot">
                                                        <option value="1" @selected($point->requires_product_lot)>Si</option>
                                                        <option value="0" @selected(! $point->requires_product_lot)>No</option>
                                                    </select>
                                                </div>

                                                <div class="field">
                                                    <label for="point_active_{{ $section->id }}_{{ $point->id }}">Attivo</label>
                                                    <select id="point_active_{{ $section->id }}_{{ $point->id }}" name="is_active">
                                                        <option value="1" @selected($point->is_active)>Si</option>
                                                        <option value="0" @selected(! $point->is_active)>No</option>
                                                    </select>
                                                </div>

                                                <div class="department-move">
                                                    <button type="submit" class="btn-small" title="Sposta su" name="direction" value="up" formaction="{{ route('monitoraggi.points.move', [$section, $point]) }}">↑</button>
                                                    <button type="submit" class="btn-small" title="Sposta giu" name="direction" value="down" formaction="{{ route('monitoraggi.points.move', [$section, $point]) }}">↓</button>
                                                </div>

                                                <div class="department-move">
                                                    @if ($point->is_active)
                                                        <button type="submit" class="btn-small soft-btn" name="quick_action" value="hide">Oscura</button>
                                                    @else
                                                        <button type="submit" class="btn-small soft-btn" name="quick_action" value="show">Riattiva</button>
                                                    @endif
                                                    <button type="submit" class="btn-small danger-btn" name="quick_action" value="delete" data-admin-confirm data-confirm-title="Eliminare il punto?" data-confirm-text="Se e presente nello storico verra solo oscurato.">Elimina</button>
                                                </div>

                                                <button type="submit" class="btn-small">Salva punto</button>
                                            </form>
                                        @empty
                                            <div class="department-row">
                                                <p class="hint" style="grid-column: 1 / -1; margin: 0;">Nessun punto configurato per questa sezione.</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </details>
                        @endif

                        @if ($currentEnvironment === 'clean_room' && auth()->user()?->isOperatore() && $currentView === 'nuovo')
                            <form action="{{ $isEditingSection ? route('monitoraggi.checks.update', [$section, $editingCheck]) : route('monitoraggi.checks.store', $section) }}" method="POST">
                                @csrf
                                @if ($isEditingSection)
                                    @method('PATCH')
                                @endif

                                <input type="hidden" name="entry_phase" value="{{ $productionPhase }}">

                                <div class="meta-grid">
                                    <div class="field">
                                        <label for="sampled_on_{{ $section->id }}">Data prelievo / inizio incubazione</label>
                                        <input id="sampled_on_{{ $section->id }}" type="date" name="sampled_on" value="{{ old('sampled_on', $isEditingSection ? $editingCheck->sampled_on : now()->toDateString()) }}" required>
                                    </div>
                                    <div class="field">
                                        <label for="operator_name_{{ $section->id }}">Operatore in clean room</label>
                                        <input id="operator_name_{{ $section->id }}" type="text" name="operator_name" value="{{ old('operator_name', $isEditingSection ? $editingCheck->operator_name : null) }}" maxlength="120">
                                    </div>
                                    <div class="field">
                                        <label for="cq_operator_name_{{ $section->id }}">Operatore CQ</label>
                                        <input id="cq_operator_name_{{ $section->id }}" type="text" name="cq_operator_name" value="{{ old('cq_operator_name', $isEditingSection ? $editingCheck->cq_operator_name : null) }}" maxlength="120">
                                    </div>
                                    <div class="field">
                                        <label for="product_lot_{{ $section->id }}">Lotto prodotto</label>
                                        <input id="product_lot_{{ $section->id }}" type="text" name="product_batch" value="{{ old('product_batch', $isEditingSection ? $editingCheck->product_batch : null) }}" maxlength="120">
                                    </div>
                                    @if ($sectionSampleKind === 'surface_swab')
                                        <div class="field">
                                            <label for="swab_lot_{{ $section->id }}">Lotto provette</label>
                                            <input id="swab_lot_{{ $section->id }}" type="text" name="swab_lot" value="{{ old('swab_lot', $isEditingSection ? $editingCheck->swab_lot : null) }}" maxlength="120">
                                        </div>
                                    @else
                                        <div class="field">
                                            <label for="media_lot_{{ $section->id }}">Lotto piastre</label>
                                            <input id="media_lot_{{ $section->id }}" type="text" name="media_lot" value="{{ old('media_lot', $isEditingSection ? $editingCheck->media_lot : null) }}" maxlength="120">
                                        </div>
                                    @endif
                                        @if ($isEditingSection)
                                            <div class="field" style="align-self: end;">
                                                <button type="submit" name="save_header" value="1">Salva intestazione</button>
                                            </div>
                                        @endif
                                </div>

                                    <div class="env-switch production-phase-switch" aria-label="Fase di inserimento">
                                        @foreach ($sectionProductionPhases as $phaseKey => $phaseLabel)
                                            @php($previousPhaseKey = $phaseKey === 'sampling' ? null : ($phaseKey === 'reading_1' ? 'sampling' : 'reading_'.((int) str_replace('reading_', '', $phaseKey) - 1)))
                                            @if (! $previousPhaseKey || filled($phaseStates->get($previousPhaseKey)?->signed_by_user_id))
                                                <a class="env-link @if ($productionPhase === $phaseKey) active @endif" href="{{ route('monitoraggi.index', array_filter(['view' => 'nuovo', 'env' => 'clean_room', 'sub' => $currentSubEnvironment, 'phase' => $phaseKey, 'edit_check' => $isEditingSection ? $editingCheck->id : null])) }}">{{ $phaseLabel }}</a>
                                            @else
                                                <span class="env-link" aria-disabled="true">{{ $phaseLabel }}</span>
                                            @endif
                                        @endforeach
                                    </div>

                                    <div class="table-scroll @if ($productionPhaseLocked) production-phase-locked @endif">
                                    <table>
                                        <thead>
                                            @if ($productionPhase === 'sampling' && $sectionSampleKind === 'air_passive')
                                            <tr>
                                                <th>ID legacy</th>
                                                <th>Locale / macchina</th>
                                                <th>Reparto</th>
                                                <th>Grado</th>
                                                <th>Ora apertura</th>
                                                <th>Ora chiusura</th>
                                            </tr>
                                        @elseif ($productionPhase === 'sampling')
                                            <tr>
                                                <th>ID legacy</th>
                                                <th>Locale / macchina</th>
                                                <th>Reparto</th>
                                                <th>Grado</th>
                                                <th>Ora campionamento</th>
                                            </tr>
                                        @elseif ($sectionSampleKind === 'surface_swab')
                                            <tr><th>ID legacy</th><th>Locale / macchina</th><th>Reparto</th><th>Grado</th><th>Lettura {{ $currentReadingNumber }}</th></tr>
                                        @else
                                            <tr><th>ID legacy</th><th>Locale / macchina</th><th>Reparto</th><th>Grado</th><th>UFC lettura {{ $currentReadingNumber }}</th></tr>
                                        @endif
                                        </thead>
                                        <tbody>
                                        @foreach ($groupedPoints as $departmentName => $points)
                                            <tr class="group-row">
                                                <td colspan="{{ $productionPhase === 'sampling' && $sectionSampleKind === 'air_passive' ? 6 : 5 }}">Reparto: {{ $departmentName }}</td>
                                            </tr>

                                            @foreach ($points as $point)
                                                <tr>
                                                    <td>{{ $point->legacy_code ?: '-' }}</td>
                                                    <td>
                                                        {{ $point->title }}
                                                        <div class="kind">{{ $sampleKindLabels[$point->sample_kind] ?? $point->sample_kind }}</div>
                                                    </td>
                                                    <td>{{ $point->department?->name ?: 'Senza reparto' }}</td>
                                                    <td>{{ $point->area_label ?: '-' }}</td>
                                                    @php($pointResult = $editingPointResults->get($point->id))
                                                    @php($pointReading = $currentReadingNumber && $pointResult ? $pointResult->readings->firstWhere('reading_number', $currentReadingNumber) : null)
                                                    @if ($productionPhase === 'sampling')
                                                        <td>
                                                            <input type="time" name="points[{{ $point->id }}][sampled_at]" value="{{ substr((string) old("points.{$point->id}.sampled_at", data_get($editingPointResults->get($point->id), 'sampled_at')), 0, 5) }}">
                                                        </td>
                                                    @endif
                                                    @if ($productionPhase === 'sampling' && $sectionSampleKind === 'air_passive')
                                                        <td>
                                                            <input type="time" name="points[{{ $point->id }}][exposure_ended_at]" value="{{ substr((string) old("points.{$point->id}.exposure_ended_at", data_get($editingPointResults->get($point->id), 'exposure_ended_at')), 0, 5) }}">
                                                        </td>
                                                    @elseif ($currentReadingNumber && $sectionSampleKind === 'surface_swab')
                                                        <td>
                                                            <select name="points[{{ $point->id }}][reading_growth_result]"><option value="">-</option><option value="growth" @selected(old("points.{$point->id}.reading_growth_result", $pointReading?->growth_result) === 'growth')>Crescita</option><option value="no_growth" @selected(old("points.{$point->id}.reading_growth_result", $pointReading?->growth_result) === 'no_growth')>Non crescita</option></select>
                                                        </td>
                                                    @elseif ($currentReadingNumber)
                                                        <td>
                                                            <input type="number" min="0" name="points[{{ $point->id }}][reading_cfu_count]" value="{{ old("points.{$point->id}.reading_cfu_count", $pointReading?->cfu_count) }}">
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="field @if ($productionPhaseLocked) production-phase-locked @endif" style="margin-top: 12px;">
                                    <label for="notes_{{ $section->id }}">Note sezione</label>
                                    <textarea id="notes_{{ $section->id }}" name="notes">{{ old('notes', $isEditingSection ? $editingCheck->notes : null) }}</textarea>
                                </div>

                                @if ($productionPhaseLocked)
                                    <div class="actions">
                                        @if ((int) $phaseSignerId === (int) auth()->id() && ! filled($phaseReopenedAt))
                                            <div class="field" style="min-width: min(100%, 360px);">
                                                <label for="reopening_reason_{{ $section->id }}">Motivazione riapertura</label>
                                                <textarea id="reopening_reason_{{ $section->id }}" name="reopening_reason" maxlength="1000">{{ old('reopening_reason') }}</textarea>
                                            </div>
                                            <button type="submit" name="reopen_phase" value="1">Riapri {{ $sectionProductionPhases[$productionPhase] }}</button>
                                        @else
                                            <p class="hint">Questa fase e firmata e bloccata. Solo l'operatore che l'ha firmata puo riaprirla indicando una motivazione.</p>
                                        @endif
                                    </div>
                                @else
                                    <div class="actions">
                                        <p class="hint">Salvataggio puntuale per singola fase Clean room.</p>
                                        <button type="submit">{{ $isEditingSection ? 'Aggiorna fase' : 'Salva fase' }}</button>
                                        @if (! $phaseSigned || filled($phaseReopenedAt))
                                            <button type="submit" name="sign_phase" value="1">Firma {{ $sectionProductionPhases[$productionPhase] }}</button>
                                        @endif
                                    </div>
                                @endif
                            </form>
                        @elseif (auth()->user()?->isOperatore() && $currentView === 'nuovo')
                            <form action="{{ $isEditingSection ? route('monitoraggi.checks.update', [$section, $editingCheck]) : route('monitoraggi.checks.store', $section) }}" method="POST">
                                @csrf
                                @if ($isEditingSection)
                                    @method('PATCH')
                                @endif

                                @if (in_array($currentEnvironment, ['produzione', 'operatori'], true))
                                    <input type="hidden" name="entry_phase" value="{{ $productionPhase }}">
                                @endif

                                @if ($currentEnvironment === 'acque')
                                    @include('monitoraggi.partials.water-sampling-sheet')
                                @else
                                <div class="meta-grid">
                                    <div class="field">
                                        <label for="sampled_on_{{ $section->id }}">Data prelievo</label>
                                        <input id="sampled_on_{{ $section->id }}" type="date" name="sampled_on" value="{{ old('sampled_on', $isEditingSection ? $editingCheck->sampled_on : now()->toDateString()) }}" required>
                                    </div>
                                    @if ($currentEnvironment === 'acque')
                                        <div class="field">
                                            <label for="sampled_time_{{ $section->id }}">Ora prelievo</label>
                                            <input id="sampled_time_{{ $section->id }}" type="time" name="sampled_time" value="{{ old('sampled_time', $isEditingSection ? $editingCheck->sampled_time : null) }}">
                                        </div>
                                    @endif
                                    <div class="field" @if ($currentEnvironment === 'acque') data-water-step-content="results" @endif>
                                        <label for="incubation_started_on_{{ $section->id }}">Inizio incubazione</label>
                                        <input id="incubation_started_on_{{ $section->id }}" type="date" name="incubation_started_on" value="{{ old('incubation_started_on', $isEditingSection ? $editingCheck->incubation_started_on : null) }}">
                                    </div>
                                    @if (! in_array($currentEnvironment, ['produzione', 'operatori'], true))
                                        <div class="field" @if ($currentEnvironment === 'acque') data-water-step-content="results" @endif>
                                            <label for="first_reading_on_{{ $section->id }}">1a lettura</label>
                                            <input id="first_reading_on_{{ $section->id }}" type="date" name="first_reading_on" value="{{ old('first_reading_on', $isEditingSection ? $editingCheck->first_reading_on : null) }}">
                                        </div>
                                        <div class="field" @if ($currentEnvironment === 'acque') data-water-step-content="results" @endif>
                                            <label for="second_reading_on_{{ $section->id }}">2a lettura</label>
                                            <input id="second_reading_on_{{ $section->id }}" type="date" name="second_reading_on" value="{{ old('second_reading_on', $isEditingSection ? $editingCheck->second_reading_on : null) }}">
                                        </div>
                                    @endif
                                    <div class="field">
                                        <label for="operator_name_{{ $section->id }}">Firma campionatore</label>
                                        <input id="operator_name_{{ $section->id }}" type="text" name="operator_name" value="{{ old('operator_name', $isEditingSection ? $editingCheck->operator_name : auth()->user()?->name) }}" maxlength="120">
                                    </div>
                                    <div class="field" @if ($currentEnvironment === 'acque') data-water-step-content="results" @endif>
                                        <label for="cq_operator_name_{{ $section->id }}">Operatore CQ</label>
                                        <input id="cq_operator_name_{{ $section->id }}" type="text" name="cq_operator_name" value="{{ old('cq_operator_name', $isEditingSection ? $editingCheck->cq_operator_name : null) }}" maxlength="120">
                                    </div>
                                    @if ($currentEnvironment === 'acque')
                                        <div class="field" data-water-step-content="results">
                                            <label for="incubation_started_signature_{{ $section->id }}">Firma inizio incubazione</label>
                                            <input id="incubation_started_signature_{{ $section->id }}" type="text" name="incubation_started_signature" value="{{ old('incubation_started_signature', $isEditingSection ? $editingCheck->incubation_started_signature : null) }}" maxlength="120">
                                        </div>
                                        <div class="field" data-water-step-content="results">
                                            <label for="incubation_finished_signature_{{ $section->id }}">Firma fine incubazione</label>
                                            <input id="incubation_finished_signature_{{ $section->id }}" type="text" name="incubation_finished_signature" value="{{ old('incubation_finished_signature', $isEditingSection ? $editingCheck->incubation_finished_signature : null) }}" maxlength="120">
                                        </div>
                                        <div class="field" data-water-step-content="results">
                                            <label for="membrane_lot_{{ $section->id }}">Membrana filtrante lotto</label>
                                            <input id="membrane_lot_{{ $section->id }}" type="text" name="membrane_lot" value="{{ old('membrane_lot', $isEditingSection ? $editingCheck->membrane_lot : null) }}" maxlength="120">
                                        </div>
                                        <div class="field" data-water-step-content="results">
                                            <label for="bottle_sterilization_lot_{{ $section->id }}">Lotto sterilizzazione flaconi</label>
                                            <input id="bottle_sterilization_lot_{{ $section->id }}" type="text" name="bottle_sterilization_lot" value="{{ old('bottle_sterilization_lot', $isEditingSection ? $editingCheck->bottle_sterilization_lot : null) }}" maxlength="120">
                                        </div>
                                        <div class="field" data-water-step-content="results">
                                            <label for="r2a_agar_lot_{{ $section->id }}">R2A Agar Lotto</label>
                                            <input id="r2a_agar_lot_{{ $section->id }}" type="text" name="r2a_agar_lot" value="{{ old('r2a_agar_lot', $isEditingSection ? $editingCheck->r2a_agar_lot : null) }}" maxlength="120">
                                        </div>
                                        <div class="field" data-water-step-content="results">
                                            <label for="coliform_agar_lot_{{ $section->id }}">Chromatic Coliform Agar ISO / TTC Lotto</label>
                                            <input id="coliform_agar_lot_{{ $section->id }}" type="text" name="coliform_agar_lot" value="{{ old('coliform_agar_lot', $isEditingSection ? $editingCheck->coliform_agar_lot : null) }}" maxlength="120">
                                        </div>
                                        <div class="field" data-water-step-content="results">
                                            <label for="pseudomonas_cn_lot_{{ $section->id }}">Pseudomonas CN Agar Lotto</label>
                                            <input id="pseudomonas_cn_lot_{{ $section->id }}" type="text" name="pseudomonas_cn_lot" value="{{ old('pseudomonas_cn_lot', $isEditingSection ? $editingCheck->pseudomonas_cn_lot : null) }}" maxlength="120">
                                        </div>
                                        <div class="field" data-water-step-content="results">
                                            <label for="slanetz_bartley_lot_{{ $section->id }}">Slanetz and Bartley Medium lotto</label>
                                            <input id="slanetz_bartley_lot_{{ $section->id }}" type="text" name="slanetz_bartley_lot" value="{{ old('slanetz_bartley_lot', $isEditingSection ? $editingCheck->slanetz_bartley_lot : null) }}" maxlength="120">
                                        </div>
                                    @else
                                        <div class="field">
                                            <label for="media_lot_{{ $section->id }}">Lotto piastre</label>
                                            <input id="media_lot_{{ $section->id }}" type="text" name="media_lot" value="{{ old('media_lot', $isEditingSection ? $editingCheck->media_lot : null) }}" maxlength="120">
                                        </div>
                                        <div class="field">
                                            <label for="swab_lot_{{ $section->id }}">Lotto provette/swab</label>
                                            <input id="swab_lot_{{ $section->id }}" type="text" name="swab_lot" value="{{ old('swab_lot', $isEditingSection ? $editingCheck->swab_lot : null) }}" maxlength="120">
                                        </div>
                                    @endif
                                    @if (in_array($currentEnvironment, ['produzione', 'operatori'], true) && $isEditingSection)
                                        <div class="field" style="align-self: end;">
                                            <button type="submit" name="save_header" value="1">Salva intestazione</button>
                                        </div>
                                    @endif
                                </div>
                                @endif

                                @if ($currentEnvironment === 'acque')
                                    <div class="water-wizard" data-water-wizard data-section-id="{{ $section->id }}">
                                        <div class="water-wizard-head">
                                            <div>
                                                <p class="water-wizard-title">Campionamento acqua</p>
                                                <p class="hint">Registra prima il prelievo e la firma del campionatore, poi completa terreni, incubazione e risultati dei punti.</p>
                                            </div>
                                            <span class="water-step-indicator" data-water-step-indicator>1 di 2</span>
                                        </div>
                                        <div class="actions">
                                            <button type="button" data-water-next>Continua ai punti</button>
                                            <button type="button" class="soft-btn" data-water-previous hidden>Modifica dati prelievo</button>
                                        </div>
                                    </div>
                                @endif

                                @if ($currentEnvironment === 'acque')
                                    <div class="water-point-list" data-water-step-content="results">
                                        @foreach ($groupedPoints as $departmentName => $points)
                                            <p class="kind">Reparto: {{ $departmentName }}</p>
                                            @foreach ($points as $point)
                                                @php($pointResult = $editingPointResults->get($point->id))
                                                <button type="button" class="water-point-card" data-water-open="water_point_{{ $section->id }}_{{ $point->id }}">
                                                    <span class="water-point-name">
                                                        <strong>{{ $point->legacy_code ?: '-' }}</strong>
                                                        <span>{{ $point->title }}</span>
                                                    </span>
                                                    <span class="water-point-summary"><span>Aerobi</span><span data-water-summary="aerobic">-</span></span>
                                                    <span class="water-point-summary"><span>Coliformi</span><span data-water-summary="coliform">-</span></span>
                                                    <span class="water-point-summary"><span>Pseudomonas</span><span data-water-summary="pseudomonas">-</span></span>
                                                    <span class="water-point-summary"><span>Enterococchi</span><span data-water-summary="enterococci">-</span></span>
                                                    <span class="water-point-summary"><span>Esito</span><span data-water-summary="final">-</span></span>
                                                </button>

                                                <dialog id="water_point_{{ $section->id }}_{{ $point->id }}" class="water-point-dialog" data-water-dialog data-point-id="{{ $point->id }}">
                                                    <div class="water-dialog-body">
                                                        <div class="water-dialog-head">
                                                            <div>
                                                                <h3>{{ $point->legacy_code ?: 'Punto' }} - {{ $point->title }}</h3>
                                                                <p class="hint">Inserisci i risultati riportati nel modulo di analisi acqua.</p>
                                                            </div>
                                                            <button type="button" class="soft-btn" data-water-close aria-label="Chiudi">Chiudi</button>
                                                        </div>

                                                        <fieldset class="water-result-group">
                                                            <legend>Microrganismi Aerobi Totali</legend>
                                                            <div class="water-modal-grid">
                                                                <div class="field">
                                                                    <label>UFC/piastra</label>
                                                                    <input type="number" min="0" data-water-result-input name="points[{{ $point->id }}][aerobic_plate_cfu]" value="{{ old("points.{$point->id}.aerobic_plate_cfu", data_get($pointResult, 'aerobic_plate_cfu')) }}">
                                                                </div>
                                                                <div class="field">
                                                                    <label>UFC/ml</label>
                                                                    <input type="number" min="0" data-water-result-input name="points[{{ $point->id }}][aerobic_cfu_per_ml]" value="{{ old("points.{$point->id}.aerobic_cfu_per_ml", data_get($pointResult, 'aerobic_cfu_per_ml')) }}">
                                                                </div>
                                                            </div>
                                                        </fieldset>

                                                        <fieldset class="water-result-group">
                                                            <legend>Coliformi Totali</legend>
                                                            <div class="water-modal-grid">
                                                                <div class="field"><label>UFC/piastra</label><input type="number" min="0" data-water-result-input name="points[{{ $point->id }}][coliform_plate_cfu]" value="{{ old("points.{$point->id}.coliform_plate_cfu", data_get($pointResult, 'coliform_plate_cfu')) }}"></div>
                                                                <div class="field"><label>UFC confermate</label><input type="number" min="0" data-water-result-input name="points[{{ $point->id }}][coliform_confirmed_cfu]" value="{{ old("points.{$point->id}.coliform_confirmed_cfu", data_get($pointResult, 'coliform_confirmed_cfu')) }}"></div>
                                                                <div class="field"><label>UFC/100 ml</label><input type="number" min="0" data-water-result-input name="points[{{ $point->id }}][coliform_cfu_per_100ml]" value="{{ old("points.{$point->id}.coliform_cfu_per_100ml", data_get($pointResult, 'coliform_cfu_per_100ml')) }}"></div>
                                                            </div>
                                                        </fieldset>

                                                        <fieldset class="water-result-group">
                                                            <legend>Pseudomonas aeruginosa</legend>
                                                            <div class="water-modal-grid">
                                                                <div class="field"><label>UFC/piastra</label><input type="number" min="0" data-water-result-input name="points[{{ $point->id }}][pseudomonas_plate_cfu]" value="{{ old("points.{$point->id}.pseudomonas_plate_cfu", data_get($pointResult, 'pseudomonas_plate_cfu')) }}"></div>
                                                                <div class="field"><label>UFC confermate</label><input type="number" min="0" data-water-result-input name="points[{{ $point->id }}][pseudomonas_confirmed_cfu]" value="{{ old("points.{$point->id}.pseudomonas_confirmed_cfu", data_get($pointResult, 'pseudomonas_confirmed_cfu')) }}"></div>
                                                                <div class="field"><label>UFC/100 ml</label><input type="number" min="0" data-water-result-input name="points[{{ $point->id }}][pseudomonas_cfu_per_100ml]" value="{{ old("points.{$point->id}.pseudomonas_cfu_per_100ml", data_get($pointResult, 'pseudomonas_cfu_per_100ml')) }}"></div>
                                                            </div>
                                                        </fieldset>

                                                        <fieldset class="water-result-group">
                                                            <legend>Enterococchi</legend>
                                                            <div class="water-modal-grid">
                                                                <div class="field"><label>UFC/piastra</label><input type="number" min="0" data-water-result-input name="points[{{ $point->id }}][enterococci_plate_cfu]" value="{{ old("points.{$point->id}.enterococci_plate_cfu", data_get($pointResult, 'enterococci_plate_cfu')) }}"></div>
                                                                <div class="field"><label>UFC confermate</label><input type="number" min="0" data-water-result-input name="points[{{ $point->id }}][enterococci_confirmed_cfu]" value="{{ old("points.{$point->id}.enterococci_confirmed_cfu", data_get($pointResult, 'enterococci_confirmed_cfu')) }}"></div>
                                                                <div class="field"><label>UFC/100 ml</label><input type="number" min="0" data-water-result-input name="points[{{ $point->id }}][enterococci_cfu_per_100ml]" value="{{ old("points.{$point->id}.enterococci_cfu_per_100ml", data_get($pointResult, 'enterococci_cfu_per_100ml')) }}"></div>
                                                            </div>
                                                        </fieldset>

                                                        <div class="water-modal-grid">
                                                            <div class="field">
                                                                <label>pH</label>
                                                                <input type="text" data-water-result-input name="points[{{ $point->id }}][ph_value]" value="{{ old("points.{$point->id}.ph_value", data_get($pointResult, 'ph_value')) }}" maxlength="20">
                                                            </div>
                                                            <div class="field">
                                                                <label>Aspetto</label>
                                                                <select data-water-result-input name="points[{{ $point->id }}][appearance_result]">
                                                                    <option value="">-</option>
                                                                    <option value="conforme" @selected(old("points.{$point->id}.appearance_result", data_get($pointResult, 'appearance_result')) === 'conforme')>Conforme</option>
                                                                    <option value="non_conforme" @selected(old("points.{$point->id}.appearance_result", data_get($pointResult, 'appearance_result')) === 'non_conforme')>Non conforme</option>
                                                                </select>
                                                            </div>
                                                            <div class="field">
                                                                <label>Risultato finale</label>
                                                                <select data-water-result-input name="points[{{ $point->id }}][final_result]">
                                                                    <option value="">-</option>
                                                                    <option value="conforme" @selected(old("points.{$point->id}.final_result", data_get($pointResult, 'final_result')) === 'conforme')>Conforme</option>
                                                                    <option value="non_conforme" @selected(old("points.{$point->id}.final_result", data_get($pointResult, 'final_result')) === 'non_conforme')>Non conforme</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="actions">
                                                            <span class="hint">I dati restano modificabili fino al salvataggio del campionamento.</span>
                                                            <button type="button" data-water-close>Conferma punto</button>
                                                        </div>
                                                    </div>
                                                </dialog>
                                            @endforeach
                                        @endforeach
                                    </div>
                                @else
                                @if (in_array($currentEnvironment, ['produzione', 'operatori'], true))
                                    <div class="env-switch production-phase-switch" aria-label="Fase di inserimento">
                                        @foreach ($sectionProductionPhases as $phaseKey => $phaseLabel)
                                            @php($previousPhaseKey = $phaseKey === 'sampling' ? null : ($phaseKey === 'reading_1' ? 'sampling' : 'reading_'.((int) str_replace('reading_', '', $phaseKey) - 1)))
                                            @if (! $previousPhaseKey || filled($phaseStates->get($previousPhaseKey)?->signed_by_user_id))
                                                <a class="env-link @if ($productionPhase === $phaseKey) active @endif" href="{{ route('monitoraggi.index', array_filter(['view' => 'nuovo', 'env' => $currentEnvironment, 'phase' => $phaseKey, 'edit_check' => $isEditingSection ? $editingCheck->id : null])) }}">{{ $phaseLabel }}</a>
                                            @else
                                                <span class="env-link" aria-disabled="true">{{ $phaseLabel }}</span>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif

                                <div class="table-scroll @if (in_array($currentEnvironment, ['produzione', 'operatori'], true) && $productionPhaseLocked) production-phase-locked @endif">
                                    <table>
                                        <thead>
                                        @if ($currentEnvironment === 'acque')
                                            <tr>
                                                <th rowspan="2">Punto</th>
                                                <th colspan="2">Microrganismi Aerobi Totali</th>
                                                <th colspan="3">Coliformi Totali</th>
                                                <th colspan="3"><em>Pseudomonas aeruginosa</em></th>
                                                <th colspan="3">Enterococchi</th>
                                                <th rowspan="2">pH</th>
                                                <th rowspan="2">Aspetto</th>
                                                <th rowspan="2">Risultato finale</th>
                                            </tr>
                                            <tr>
                                                <th>UFC/piastra</th>
                                                <th>UFC/ml</th>
                                                <th>UFC/piastra</th>
                                                <th>UFC confermate</th>
                                                <th>UFC/100 ml</th>
                                                <th>UFC/piastra</th>
                                                <th>UFC confermate</th>
                                                <th>UFC/100 ml</th>
                                                <th>UFC/piastra</th>
                                                <th>UFC confermate</th>
                                                <th>UFC/100 ml</th>
                                            </tr>
                                        @elseif (in_array($currentEnvironment, ['produzione', 'operatori'], true))
                                            <tr>
                                                <th>ID legacy</th>
                                                <th>Descrizione punto</th>
                                                <th>Reparto</th>
                                                <th>Area dettagliata</th>
                                                @if ($productionPhase === 'sampling')
                                                    <th>Ora</th>
                                                    @if ($currentEnvironment === 'produzione')
                                                        <th>Operativo</th>
                                                        <th>Lotto prodotto</th>
                                                    @endif
                                                @elseif ($currentReadingNumber)
                                                    <th>{{ $currentEnvironment === 'produzione' ? 'UFC/m3' : 'UFC/piastra' }} (lettura {{ $currentReadingNumber }})</th>
                                                @else
                                                    <th>Valore lettura</th>
                                                @endif
                                            </tr>
                                        @else
                                            <tr>
                                                <th>ID legacy</th>
                                                <th>Descrizione punto</th>
                                                <th>Reparto</th>
                                                <th>Area dettagliata</th>
                                                <th>Ora</th>
                                                <th>Operativo</th>
                                                <th>Lotto prodotto</th>
                                                <th>Parametro campionamento</th>
                                                <th>CFU</th>
                                                <th>Note</th>
                                            </tr>
                                        @endif
                                        </thead>
                                        <tbody>
                                        @foreach ($groupedPoints as $departmentName => $points)
                                            <tr class="group-row">
                                                <td colspan="{{ $currentEnvironment === 'acque' ? 15 : (in_array($currentEnvironment, ['produzione', 'operatori'], true) ? ($productionPhase === 'sampling' ? ($currentEnvironment === 'produzione' ? 7 : 5) : 5) : 10) }}">Reparto: {{ $departmentName }}</td>
                                            </tr>

                                            @foreach ($points as $point)
                                                <tr>
                                                    @if ($currentEnvironment === 'acque')
                                                        <td>
                                                            <strong>{{ $point->legacy_code ?: '-' }}</strong>
                                                            <div class="kind">{{ $point->title }}</div>
                                                        </td>
                                                        <td>
                                                            <input type="number" min="0" name="points[{{ $point->id }}][aerobic_plate_cfu]" value="{{ old("points.{$point->id}.aerobic_plate_cfu", data_get($editingPointResults->get($point->id), 'aerobic_plate_cfu')) }}">
                                                        </td>
                                                        <td>
                                                            <input type="number" min="0" name="points[{{ $point->id }}][aerobic_cfu_per_ml]" value="{{ old("points.{$point->id}.aerobic_cfu_per_ml", data_get($editingPointResults->get($point->id), 'aerobic_cfu_per_ml')) }}">
                                                        </td>
                                                        <td>
                                                            <input type="number" min="0" name="points[{{ $point->id }}][coliform_plate_cfu]" value="{{ old("points.{$point->id}.coliform_plate_cfu", data_get($editingPointResults->get($point->id), 'coliform_plate_cfu')) }}">
                                                        </td>
                                                        <td>
                                                            <input type="number" min="0" name="points[{{ $point->id }}][coliform_confirmed_cfu]" value="{{ old("points.{$point->id}.coliform_confirmed_cfu", data_get($editingPointResults->get($point->id), 'coliform_confirmed_cfu')) }}">
                                                        </td>
                                                        <td>
                                                            <input type="number" min="0" name="points[{{ $point->id }}][coliform_cfu_per_100ml]" value="{{ old("points.{$point->id}.coliform_cfu_per_100ml", data_get($editingPointResults->get($point->id), 'coliform_cfu_per_100ml')) }}">
                                                        </td>
                                                        <td>
                                                            <input type="number" min="0" name="points[{{ $point->id }}][pseudomonas_plate_cfu]" value="{{ old("points.{$point->id}.pseudomonas_plate_cfu", data_get($editingPointResults->get($point->id), 'pseudomonas_plate_cfu')) }}">
                                                        </td>
                                                        <td>
                                                            <input type="number" min="0" name="points[{{ $point->id }}][pseudomonas_confirmed_cfu]" value="{{ old("points.{$point->id}.pseudomonas_confirmed_cfu", data_get($editingPointResults->get($point->id), 'pseudomonas_confirmed_cfu')) }}">
                                                        </td>
                                                        <td>
                                                            <input type="number" min="0" name="points[{{ $point->id }}][pseudomonas_cfu_per_100ml]" value="{{ old("points.{$point->id}.pseudomonas_cfu_per_100ml", data_get($editingPointResults->get($point->id), 'pseudomonas_cfu_per_100ml')) }}">
                                                        </td>
                                                        <td>
                                                            <input type="number" min="0" name="points[{{ $point->id }}][enterococci_plate_cfu]" value="{{ old("points.{$point->id}.enterococci_plate_cfu", data_get($editingPointResults->get($point->id), 'enterococci_plate_cfu')) }}">
                                                        </td>
                                                        <td>
                                                            <input type="number" min="0" name="points[{{ $point->id }}][enterococci_confirmed_cfu]" value="{{ old("points.{$point->id}.enterococci_confirmed_cfu", data_get($editingPointResults->get($point->id), 'enterococci_confirmed_cfu')) }}">
                                                        </td>
                                                        <td>
                                                            <input type="number" min="0" name="points[{{ $point->id }}][enterococci_cfu_per_100ml]" value="{{ old("points.{$point->id}.enterococci_cfu_per_100ml", data_get($editingPointResults->get($point->id), 'enterococci_cfu_per_100ml')) }}">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="points[{{ $point->id }}][ph_value]" value="{{ old("points.{$point->id}.ph_value", data_get($editingPointResults->get($point->id), 'ph_value')) }}" maxlength="20">
                                                        </td>
                                                        <td>
                                                            <select name="points[{{ $point->id }}][appearance_result]">
                                                                <option value="">-</option>
                                                                <option value="conforme" @selected(old("points.{$point->id}.appearance_result", data_get($editingPointResults->get($point->id), 'appearance_result')) === 'conforme')>Conforme</option>
                                                                <option value="non_conforme" @selected(old("points.{$point->id}.appearance_result", data_get($editingPointResults->get($point->id), 'appearance_result')) === 'non_conforme')>Non conforme</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select name="points[{{ $point->id }}][final_result]">
                                                                <option value="">-</option>
                                                                <option value="conforme" @selected(old("points.{$point->id}.final_result", data_get($editingPointResults->get($point->id), 'final_result')) === 'conforme')>Conforme</option>
                                                                <option value="non_conforme" @selected(old("points.{$point->id}.final_result", data_get($editingPointResults->get($point->id), 'final_result')) === 'non_conforme')>Non conforme</option>
                                                            </select>
                                                        </td>
                                                    @elseif (in_array($currentEnvironment, ['produzione', 'operatori'], true))
                                                        <td>{{ $point->legacy_code ?: '-' }}</td>
                                                        <td>
                                                            {{ $point->title }}
                                                            <div class="kind">{{ $sampleKindLabels[$point->sample_kind] ?? $point->sample_kind }}</div>
                                                        </td>
                                                        <td>{{ $point->department?->name ?: 'Senza reparto' }}</td>
                                                        <td>{{ $point->area_label ?: '-' }}</td>
                                                        @php($pointResult = $editingPointResults->get($point->id))
                                                        @php($pointReading = $currentReadingNumber && $pointResult ? $pointResult->readings->firstWhere('reading_number', $currentReadingNumber) : null)
                                                        @if ($productionPhase === 'sampling')
                                                            <td>
                                                                <input type="time" name="points[{{ $point->id }}][sampled_at]" value="{{ substr((string) old("points.{$point->id}.sampled_at", data_get($editingPointResults->get($point->id), 'sampled_at')), 0, 5) }}">
                                                            </td>
                                                            @if ($currentEnvironment === 'produzione')
                                                                <td>
                                                                    @if ($point->requires_operational_status)
                                                                        <select name="points[{{ $point->id }}][is_operational]" data-production-operational>
                                                                            <option value="">-</option>
                                                                            <option value="1" @selected((string) old("points.{$point->id}.is_operational", data_get($editingPointResults->get($point->id), 'is_operational')) === '1')>Si</option>
                                                                            <option value="0" @selected((string) old("points.{$point->id}.is_operational", data_get($editingPointResults->get($point->id), 'is_operational')) === '0')>No</option>
                                                                        </select>
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </td>
                                                                <td data-production-product-lot hidden>
                                                                    @if ($point->requires_product_lot && $point->requires_operational_status)
                                                                        <input type="text" name="points[{{ $point->id }}][product_lot]" value="{{ old("points.{$point->id}.product_lot", data_get($editingPointResults->get($point->id), 'product_lot')) }}" maxlength="120" disabled>
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </td>
                                                            @endif
                                                        @elseif ($currentReadingNumber)
                                                            <td>
                                                                <input type="number" min="0" name="points[{{ $point->id }}][reading_cfu_count]" value="{{ old("points.{$point->id}.reading_cfu_count", $pointReading?->cfu_count) }}">
                                                            </td>
                                                        @endif
                                                    @else
                                                        <td>{{ $point->legacy_code ?: '-' }}</td>
                                                        <td>
                                                            {{ $point->title }}
                                                            <div class="kind">{{ $sampleKindLabels[$point->sample_kind] ?? $point->sample_kind }}</div>
                                                        </td>
                                                        <td>{{ $point->department?->name ?: 'Senza reparto' }}</td>
                                                        <td>{{ $point->area_label ?: '-' }}</td>
                                                        <td>
                                                            <input type="time" name="points[{{ $point->id }}][sampled_at]" value="{{ old("points.{$point->id}.sampled_at", data_get($editingPointResults->get($point->id), 'sampled_at')) }}">
                                                        </td>
                                                        <td>
                                                            @if ($point->requires_operational_status)
                                                                <select name="points[{{ $point->id }}][is_operational]">
                                                                    <option value="">-</option>
                                                                    <option value="1" @selected((string) old("points.{$point->id}.is_operational", data_get($editingPointResults->get($point->id), 'is_operational')) === '1')>Si</option>
                                                                    <option value="0" @selected((string) old("points.{$point->id}.is_operational", data_get($editingPointResults->get($point->id), 'is_operational')) === '0')>No</option>
                                                                </select>
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($point->requires_product_lot)
                                                                <input type="text" name="points[{{ $point->id }}][product_lot]" value="{{ old("points.{$point->id}.product_lot", data_get($editingPointResults->get($point->id), 'product_lot')) }}" maxlength="120">
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($point->sample_kind === 'air_passive')
                                                                {{ $point->default_exposure_hours ? $point->default_exposure_hours . ' h' : '-' }}
                                                            @elseif ($point->sample_kind === 'air_active')
                                                                {{ $point->default_volume_liters ? $point->default_volume_liters . ' L' : '-' }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <input type="number" min="0" name="points[{{ $point->id }}][cfu_count]" value="{{ old("points.{$point->id}.cfu_count", data_get($editingPointResults->get($point->id), 'cfu_count')) }}">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="points[{{ $point->id }}][notes]" value="{{ old("points.{$point->id}.notes", data_get($editingPointResults->get($point->id), 'notes')) }}" maxlength="500">
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @endif

                                <div class="field @if (in_array($currentEnvironment, ['produzione', 'operatori'], true) && $productionPhaseLocked) production-phase-locked @endif" style="margin-top: 12px;" @if ($currentEnvironment === 'acque') data-water-step-content="results" @endif>
                                    <label for="notes_{{ $section->id }}">Note sezione</label>
                                    <textarea id="notes_{{ $section->id }}" name="notes">{{ old('notes', $isEditingSection ? $editingCheck->notes : null) }}</textarea>
                                </div>

                                @if (in_array($currentEnvironment, ['produzione', 'operatori'], true) && $productionPhaseLocked)
                                    <div class="actions">
                                        @if ((int) $phaseSignerId === (int) auth()->id() && ! filled($phaseReopenedAt))
                                            <div class="field" style="min-width: min(100%, 360px);">
                                                <label for="reopening_reason_{{ $section->id }}">Motivazione riapertura</label>
                                                <textarea id="reopening_reason_{{ $section->id }}" name="reopening_reason" maxlength="1000">{{ old('reopening_reason') }}</textarea>
                                            </div>
                                            <button type="submit" name="reopen_phase" value="1">Riapri {{ $sectionProductionPhases[$productionPhase] }}</button>
                                        @else
                                            <p class="hint">Questa fase e firmata e bloccata. Solo l'operatore che l'ha firmata puo riaprirla indicando una motivazione.</p>
                                        @endif
                                    </div>
                                @else
                                    <div class="actions" @if ($currentEnvironment === 'acque') data-water-step-content="results" @endif>
                                        <p class="hint">Salvataggio puntuale per singola sezione.</p>
                                        <button type="submit">{{ $isEditingSection ? 'Aggiorna sezione' : 'Salva sezione' }}</button>
                                        @if (in_array($currentEnvironment, ['produzione', 'operatori'], true) && (! $phaseSigned || filled($phaseReopenedAt)))
                                            <button type="submit" name="sign_phase" value="1">Firma {{ $sectionProductionPhases[$productionPhase] }}</button>
                                        @endif
                                    </div>
                                @endif
                            </form>
                        @elseif ($currentView === 'nuovo')
                            <div class="actions" style="margin-top:12px;">
                                <p class="hint">La compilazione del campionamento e consentita solo agli utenti con ruolo operatore.</p>
                            </div>
                        @endif
                    </div>
                </details>
            @empty
                <details class="section" open>
                    <summary>
                        <div>
                            <p class="section-title">Nessuna sezione configurata</p>
                            <p class="section-desc">Nessun template trovato per l'ambiente selezionato.</p>
                        </div>
                        <span class="badge">0 punti</span>
                    </summary>
                </details>
            @endforelse
        @endif
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var serverTime = document.body.dataset.serverTime;

        document.addEventListener('focusin', function (event) {
            var timeInput = event.target;

            if (timeInput.matches('input[type="time"]') && timeInput.value === '') {
                timeInput.value = serverTime;
            }
        });

        document.addEventListener('click', function (event) {
            var button = event.target.closest('[data-admin-confirm]');

            if (!button || !button.form) {
                return;
            }

            event.preventDefault();

            Swal.fire({
                title: button.dataset.confirmTitle,
                text: button.dataset.confirmText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Elimina',
                cancelButtonText: 'Annulla',
                confirmButtonColor: '#b83232',
            }).then(function (result) {
                if (result.isConfirmed) {
                    button.form.requestSubmit(button);
                }
            });
        });

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

        document.querySelectorAll('[data-sample-kind-select]').forEach(function (sampleKindSelect) {
            var form = sampleKindSelect.closest('form');
            var volumeField = form ? form.querySelector('[data-sample-setting="volume"]') : null;
            var exposureField = form ? form.querySelector('[data-sample-setting="exposure"]') : null;

            var syncSampleSettings = function () {
                var showVolume = sampleKindSelect.value === 'air_active';
                var showExposure = sampleKindSelect.value === 'air_passive';

                [
                    [volumeField, showVolume],
                    [exposureField, showExposure],
                ].forEach(function (setting) {
                    var field = setting[0];
                    var isVisible = setting[1];

                    if (!field) {
                        return;
                    }

                    field.hidden = !isVisible;
                    field.querySelectorAll('input, select').forEach(function (input) {
                        input.disabled = !isVisible;
                    });
                });
            };

            sampleKindSelect.addEventListener('change', syncSampleSettings);
            syncSampleSettings();
        });

        document.querySelectorAll('[data-production-operational]').forEach(function (operationalSelect) {
            var row = operationalSelect.closest('tr');
            var productLotCell = row ? row.querySelector('[data-production-product-lot]') : null;

            if (!productLotCell) {
                return;
            }

            var productLotInput = productLotCell.querySelector('input');
            var syncProductLot = function () {
                var isOperational = operationalSelect.value === '1';

                productLotCell.hidden = !isOperational;

                if (productLotInput) {
                    productLotInput.disabled = !isOperational;

                    if (!isOperational) {
                        productLotInput.value = '';
                    }
                }
            };

            operationalSelect.addEventListener('change', syncProductLot);
            syncProductLot();
        });

        document.querySelectorAll('[data-water-wizard]').forEach(function (wizard) {
            var form = wizard.closest('form');
            var nextButton = wizard.querySelector('[data-water-next]');
            var previousButton = wizard.querySelector('[data-water-previous]');
            var indicator = wizard.querySelector('[data-water-step-indicator]');
            var resultFields = form.querySelectorAll('[data-water-step-content="results"]');

            form.noValidate = true;

            form.addEventListener('submit', function (event) {
                var invalidField = Array.from(form.elements).find(function (field) {
                    return field.willValidate && !field.validity.valid;
                });

                if (!invalidField) {
                    return;
                }

                event.preventDefault();

                var dialog = invalidField.closest('[data-water-dialog]');

                if (dialog && !dialog.open) {
                    dialog.showModal();
                }

                invalidField.focus();
                invalidField.reportValidity();
            });

            var setStep = function (step) {
                var isResultsStep = step === 'results';

                resultFields.forEach(function (field) {
                    field.hidden = !isResultsStep;
                });

                nextButton.hidden = isResultsStep;
                previousButton.hidden = !isResultsStep;
                indicator.textContent = isResultsStep ? '2 di 2' : '1 di 2';
            };

            nextButton.addEventListener('click', function () {
                var requiredInput = form.querySelector('[name="sampled_on"]');

                if (!requiredInput.reportValidity()) {
                    return;
                }

                setStep('results');
            });

            previousButton.addEventListener('click', function () {
                setStep('sampling');
            });

            setStep('sampling');
        });

        document.querySelectorAll('[data-water-dialog]').forEach(function (dialog) {
            var pointId = dialog.getAttribute('data-point-id');
            var card = document.querySelector('[data-water-open="' + dialog.id + '"]');

            if (!card) {
                return;
            }

            var valueFor = function (field) {
                var input = dialog.querySelector('[name="points[' + pointId + '][' + field + ']"]');
                return input && input.value !== '' ? input.value : '-';
            };

            var resultLabel = function (field) {
                var input = dialog.querySelector('[name="points[' + pointId + '][' + field + ']"]');

                if (!input || input.value === '') {
                    return '-';
                }

                return input.value === 'conforme' ? 'Conforme' : 'Non conforme';
            };

            var updateSummary = function () {
                card.querySelector('[data-water-summary="aerobic"]').textContent = 'P: ' + valueFor('aerobic_plate_cfu') + ' | ml: ' + valueFor('aerobic_cfu_per_ml');
                card.querySelector('[data-water-summary="coliform"]').textContent = '100 ml: ' + valueFor('coliform_cfu_per_100ml');
                card.querySelector('[data-water-summary="pseudomonas"]').textContent = '100 ml: ' + valueFor('pseudomonas_cfu_per_100ml');
                card.querySelector('[data-water-summary="enterococci"]').textContent = '100 ml: ' + valueFor('enterococci_cfu_per_100ml');
                card.querySelector('[data-water-summary="final"]').textContent = resultLabel('final_result');
            };

            card.addEventListener('click', function () {
                dialog.showModal();
            });

            dialog.querySelectorAll('[data-water-close]').forEach(function (button) {
                button.addEventListener('click', function () {
                    dialog.close();
                });
            });

            dialog.querySelectorAll('[data-water-result-input]').forEach(function (input) {
                input.addEventListener('input', updateSummary);
                input.addEventListener('change', updateSummary);
            });

            updateSummary();
        });
    });
</script>
</body>
</html>
