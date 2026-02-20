<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class PinLoginController extends Controller
{
    // 5 pokušaja -> lock 15 minuta
    private int $maxAttempts = 5;
    private int $decaySeconds = 15 * 60;

    public function create()
    {
        return view('auth.pin-login');
    }

    public function store(Request $request)
    {
        $request->validate([
            'pin' => ['required', 'digits:4'],
        ]);

        $key = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($key, $this->maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);

            throw ValidationException::withMessages([
                'pin' => "Previše pokušaja. Pokušaj ponovo za {$seconds} sekundi.",
            ]);
        }

        // Jedan nalog u sistemu
        $user = User::query()->where('email', 'callcenter@local')->first();

        if (! $user || ! $user->pin_hash || ! Hash::check($request->string('pin'), $user->pin_hash)) {
            RateLimiter::hit($key, $this->decaySeconds);

            throw ValidationException::withMessages([
                'pin' => 'Pogrešan PIN.',
            ]);
        }

        // uspeh -> reset limiter
        RateLimiter::clear($key);

        Auth::login($user, remember: false);

        $request->session()->regenerate();

        return redirect()->intended('/app');
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('pin.login');
    }

    private function throttleKey(Request $request): string
    {
        // Bez IP allowlist; ali koristimo IP+UA da otežamo bruteforce.
        // Ako želiš strože: samo IP.
        return 'pin-login:' . sha1($request->ip().'|'.$request->userAgent());
    }
}