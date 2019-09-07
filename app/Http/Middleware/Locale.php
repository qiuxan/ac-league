<?php

namespace App\Http\Middleware;

use App\Utils;
use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class Locale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->session()->has('locale')) {
            $locale = $request->session()->get('locale', Config::get('app.locale'));
        } else {
            $locale = substr($request->server('HTTP_ACCEPT_LANGUAGE'), 0, 2);

            if ($locale != 'zh' && $locale != 'cn' && $locale != 'en') {
                $locale = 'en';
            }
            else if($locale == 'zh' || $locale == 'cn')
            {
                $locale = 'cn';

                $full_locale = strtolower(substr($request->server('HTTP_ACCEPT_LANGUAGE'), 0, 5));
                if($full_locale == 'zh-hk' || $full_locale == 'zh-tw' || $full_locale == 'zh-mo')
                {
                    $locale = 'tr';
                }
                else
                {
                    $full_locale = strtolower(substr($request->server('HTTP_ACCEPT_LANGUAGE'), 0, 7));
                    if($full_locale == 'zh-hant')
                    {
                        $locale = 'tr';
                    }
                }
            }
        }

        App::setLocale($locale);

        return $next($request);
    }
}
