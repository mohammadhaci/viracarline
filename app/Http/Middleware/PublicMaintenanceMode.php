<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin-controlled maintenance mode for the PUBLIC site only;
 * the five back-office panels stay reachable.
 */
class PublicMaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Setting::get('site_maintenance_mode') === true) {
            abort(503);
        }

        return $next($request);
    }
}
