<?php


namespace App\Http\Middleware;
use Session;
use Closure;

class WebAuth  
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
		
        
        if (Session::has('users') || isset($_SESSION['fbUserProfile'])) 
		{
			return $next($request);
		}
		else
		{
            //return $next($request);
			return redirect('/');
		}

    }
}
