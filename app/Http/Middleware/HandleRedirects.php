<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Redirects manager (plan §3.1) — critical for the upcoming domain change.
 * Only consulted on the public site, before route resolution.
 */
class HandleRedirects
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('GET')) {
            $path = '/'.ltrim($request->path(), '/');

            $redirect = Redirect::query()
                ->where('from_path', $path)
                ->where('is_active', true)
                ->first();

            if ($redirect) {
                return redirect($redirect->to_path, $redirect->status_code);
            }
        }

        return $next($request);
    }
}
