<?php

namespace App\Http\Middleware\Social;

use Closure;

class SocialLoginMiddleware
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
        if (!in_array(strtolower($request->service), array_keys(config('social.social.services')))):
            return back()->with('danger', "Sorry, this Social link integration does not exist yet.");
        endif;

        return $next($request);
    }
}
