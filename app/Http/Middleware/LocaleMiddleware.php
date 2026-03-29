<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class LocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session()->has('locale')) {
            App::setLocale(session('locale'));
        } else {
            // Auto detect from browser if missing
            $userLangs = preg_split('/,|;/', $request->server('HTTP_ACCEPT_LANGUAGE'));
            
            // Check if es or en matches first preferred language loosely
            $preferred = 'en'; // default
            foreach ($userLangs as $lang) {
                if (strpos($lang, 'es') === 0) {
                    $preferred = 'es';
                    break;
                }
                if (strpos($lang, 'en') === 0) {
                    $preferred = 'en';
                    break;
                }
            }
            App::setLocale($preferred);
            session(['locale' => $preferred]);
        }

        return $next($request);
    }
}
