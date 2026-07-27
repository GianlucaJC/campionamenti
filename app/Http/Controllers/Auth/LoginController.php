<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string', 'max:120'],
            'password' => ['required', 'string'],
        ]);

        $apiToken = config('services.liof_login.token');

        if (blank($apiToken)) {
            return back()
                ->withErrors(['username' => 'Configurazione del servizio di accesso non disponibile.'])
                ->onlyInput('username');
        }

        try {
            $response = Http::acceptJson()
                ->withOptions(['verify' => config('services.liof_login.verify_ssl')])
                ->timeout(10)
                ->post(config('services.liof_login.url'), [
                    'action' => 'login',
                    'app_name' => 'campionamenti',
                    'api_token' => $apiToken,
                    'username' => $credentials['username'],
                    'password' => $credentials['password'],
                ]);
            $externalUser = $response->json();
        } catch (Throwable) {
            return back()
                ->withErrors(['username' => 'Servizio di accesso temporaneamente non disponibile.'])
                ->onlyInput('username');
        }

        if (! $response->successful() || ! is_array($externalUser) || empty($externalUser['success'])) {
            return back()
                ->withErrors(['username' => 'Credenziali non valide.'])
                ->onlyInput('username');
        }

        $role = match ((int) ($externalUser['user_camp'] ?? 0)) {
            1 => 'admin',
            2 => 'operatore',
            default => null,
        };

        $externalUserId = (int) ($externalUser['id'] ?? $externalUser['user_id'] ?? 0);

        if (! $role || $externalUserId < 1) {
            return back()
                ->withErrors(['username' => 'Utente non autorizzato ad accedere all\'applicazione.'])
                ->onlyInput('username');
        }

        $email = "liof-{$externalUserId}@users.invalid";
        $user = User::query()->where('liof_user_id', $externalUserId)->first();

        $attributes = [
            'liof_user_id' => $externalUserId,
            'name' => (string) ($externalUser['operatore'] ?? $externalUser['username']),
            'email' => $email,
            'role' => $role,
        ];

        if ($user) {
            $user->update($attributes);
        } else {
            $user = User::query()->create(array_merge($attributes, [
                'password' => Hash::make(Str::random(64)),
            ]));
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('monitoraggi.index'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
