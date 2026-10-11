<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class HandleRedirects
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->isMethod('GET') && ! $request->isMethod('HEAD')) {
            return $next($request);
        }

        $path = Redirect::normalizePath(rawurldecode($request->getPathInfo()));

        if (Redirect::isProtectedPath($path)) {
            return $next($request);
        }

        $redirect = Redirect::activeMap()[$path] ?? null;

        if ($redirect === null) {
            return $next($request);
        }

        DB::table('redirects')->where('id', $redirect['id'])->update([
            'hits' => DB::raw('hits + 1'),
            'last_hit_at' => now(),
        ]);

        $target = $redirect['to_url'];
        $query = $request->server->get('QUERY_STRING');

        if ($redirect['preserve_query_string'] && is_string($query) && $query !== '') {
            $target .= (Str::contains($target, '?') ? '&' : '?').$query;
        }

        return redirect()->away($target, $redirect['status_code']);
    }
}
