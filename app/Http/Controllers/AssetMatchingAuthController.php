<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AssetMatchingAuthController extends Controller
{
    public function showLogin(Request $request)
    {
        $this->rememberSafeRedirect($request);

        return view('asset-matching.auth.login');
    }

    public function showRegister(Request $request)
    {
        $this->rememberSafeRedirect($request);

        return view('asset-matching.auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);
        $user = User::create($data);
        Auth::login($user);
        $request->session()->regenerate();
        $intended = $request->session()->pull('url.intended');
        if ($this->isSafeRedirect($intended)) {
            $request->session()->put('matching.profile_redirect', $intended);
        }

        return redirect()->route('matching.profile.edit');
    }

    public function showProfile()
    {
        return view('asset-matching.profile.edit');
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'company' => ['nullable', 'string', 'max:100'],
            'whatsapp' => ['nullable', 'regex:/^[0-9+()\-\s]{8,20}$/'],
        ]);
        $request->user()->update([
            'company' => filled($data['company'] ?? null) ? trim($data['company']) : null,
            'whatsapp' => filled($data['whatsapp'] ?? null) ? trim($data['whatsapp']) : null,
        ]);

        return $this->redirectAfterProfile($request)->with('success', 'Profil berhasil diperbarui.');
    }

    public function skipProfile(Request $request)
    {
        return $this->redirectAfterProfile($request)->with('success', 'Profil dapat dilengkapi kapan saja.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required']]);
        if (! Auth::attempt([...$credentials, 'is_active' => true], $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email atau password tidak sesuai.'])->onlyInput('email');
        }
        $request->session()->regenerate();

        return redirect()->intended(route('matching.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('matching.index');
    }

    public function showForgotPassword()
    {
        return view('asset-matching.auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);
        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::ResetLinkSent
            ? back()->with('success', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPassword(Request $request, string $token)
    {
        return view('asset-matching.auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'token' => ['required'], 'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);
        $status = Password::reset($data, function (User $user, string $password) {
            $user->forceFill(['password' => Hash::make($password), 'remember_token' => Str::random(60)])->save();
            event(new PasswordReset($user));
        });

        return $status === Password::PasswordReset
            ? redirect()->route('matching.login')->with('success', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    private function rememberSafeRedirect(Request $request): void
    {
        $redirect = $request->string('redirect')->toString();
        if ($this->isSafeRedirect($redirect)) {
            $request->session()->put('url.intended', $redirect);
        }
    }

    private function redirectAfterProfile(Request $request)
    {
        $redirect = $request->session()->pull('matching.profile_redirect');

        return $this->isSafeRedirect($redirect)
            ? redirect()->to($redirect)
            : redirect()->route('matching.dashboard');
    }

    private function isSafeRedirect(?string $redirect): bool
    {
        return filled($redirect) && (
            str_starts_with($redirect, url('/capital-connect'))
            || str_starts_with($redirect, url('/capital'))
            || str_starts_with($redirect, url('/asset-matching'))
        );
    }
}
