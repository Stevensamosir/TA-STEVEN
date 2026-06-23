<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Jika akun dinonaktifkan oleh admin, paksa logout seketika
        if (!$request->user()->is_active) {
            \Illuminate\Support\Facades\Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')
                ->withErrors(['email' => 'Akun Anda telah dinonaktifkan. Hubungi admin.']);
        }

        if (!in_array($request->user()->role, $roles)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
        return $next($request);
    }
}
