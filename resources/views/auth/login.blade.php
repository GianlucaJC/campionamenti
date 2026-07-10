<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Campionamenti</title>
    <style>
        @import url('https://fonts.bunny.net/css?family=ibm-plex-sans:300,400,500,600,700');

        :root {
            --bg: #f4efe8;
            --panel: #fff9f2;
            --ink: #1f2a30;
            --muted: #5f6a6f;
            --accent: #12706b;
            --line: #d6ccc1;
            --error: #b83232;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            font-family: 'IBM Plex Sans', sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at 8% 8%, #e7d8c6 0, transparent 34%),
                radial-gradient(circle at 90% 0%, #e7efe2 0, transparent 35%),
                linear-gradient(180deg, #f7f1e7 0%, var(--bg) 100%);
        }

        .card {
            width: min(460px, 100%);
            border: 1px solid var(--line);
            border-radius: 16px;
            background: var(--panel);
            padding: 24px;
            box-shadow: 0 14px 30px rgba(31, 42, 48, 0.08);
        }

        h1 {
            margin: 0 0 8px;
            font-size: 1.6rem;
        }

        p {
            margin: 0 0 20px;
            color: var(--muted);
        }

        .field {
            display: grid;
            gap: 6px;
            margin-bottom: 14px;
        }

        label {
            font-size: 0.92rem;
            color: var(--muted);
        }

        input[type='email'],
        input[type='password'] {
            width: 100%;
            border: 1px solid #cdbfae;
            border-radius: 9px;
            padding: 10px 12px;
            font: inherit;
            background: #fff;
            color: var(--ink);
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 18px;
            font-size: 0.92rem;
        }

        .error {
            margin-bottom: 14px;
            color: var(--error);
            font-weight: 600;
        }

        button {
            width: 100%;
            border: 0;
            border-radius: 10px;
            background: var(--accent);
            color: #fff;
            padding: 11px 14px;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
        }

        button:hover {
            filter: brightness(1.05);
        }
    </style>
</head>
<body>
    <main class="card">
        <h1>Accesso Campionamenti</h1>
        <p>Inserisci le credenziali del tuo account.</p>

        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login.attempt') }}">
            @csrf

            <div class="field">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" required>
            </div>

            <label class="remember" for="remember">
                <input id="remember" name="remember" type="checkbox" value="1">
                Ricordami
            </label>

            <button type="submit">Accedi</button>
        </form>
    </main>
</body>
</html>
