<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * The frontend sends its in-app locale (not the browser's) as
 * Accept-Language on every request — see lib/api.ts. This is what lets
 * every response message already arrive localized.
 */
class SetLocaleFromHeader
{
    private const SUPPORTED_LOCALES = ['ar', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->getPreferredLanguage(self::SUPPORTED_LOCALES);

        App::setLocale($locale ?? config('app.fallback_locale'));

        return $next($request);
    }
}
