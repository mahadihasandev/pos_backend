<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the admin dashboard login page.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('admin.orders.index');
        }

        return view('admin.auth.login');
    }

    /**
     * Authenticate an administrator session.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt(array_merge($credentials, ['is_active' => true]), $remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('admin.orders.index'));
        }

        throw ValidationException::withMessages([
            'email' => ['The provided credentials do not match our records or account is inactive.'],
        ]);
    }

    /**
     * Log out the admin user and invalidate session.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
