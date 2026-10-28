<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMlReportVisible
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(config('app.show_ml_report'), 404);

        return $next($request);
    }
}
