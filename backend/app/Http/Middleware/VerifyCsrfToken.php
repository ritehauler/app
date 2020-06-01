<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;
use Closure;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->except = explode(",", EXCLUDE_CSRF_ROUTES);
        //return parent::handle($request, $next);

        /*// disbale CSRF protection for DIR_API
        if ($request->is(DIR_API.'*'))
        {
            return $next($request);
        }*/
        return parent::handle($request, $next);
    }
}
