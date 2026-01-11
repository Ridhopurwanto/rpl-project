<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class TestTimeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah di file .env ada setting waktu tes
        if (app()->environment('local') && env('APP_TEST_TIME')) {
            // Set waktu global Carbon ke waktu yang kita tentukan
            Carbon::setTestNow(Carbon::parse(env('APP_TEST_TIME')));
        }

        return $next($request);
    }
}
