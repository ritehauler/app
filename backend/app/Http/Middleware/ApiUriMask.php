<?php



namespace App\Http\Middleware;

use App\Libraries\CustomHelper;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;
use Closure;

use Illuminate\Support\Facades\Route;
use Redirect;
use App\Http\Models\SYSEntityType;

class ApiUriMask extends BaseVerifier
{

    private $_model;

    public function __construct() {
        $this->_model = new SYSEntityType();

        // model function to verify access to department
        // get entity_type with back_end_auth 1 where department get parameters
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $rout = Route::currentRouteAction();
        print  '['.Route::getCurrentRoute().']';
        print   Route::currentRouteAction().']';
        print_r($rout);
        exit;
        $parameters = $request->route()->parameters();
        $department = $parameters['department'];

        $user_entity = \Session::get(config("panel.SESS_KEY").'auth') ? \Session::get(config("panel.SESS_KEY").'auth') : NULL;

        // get segment
        $out_segment = CustomHelper::getSegments($request);
        $page_module  = isset($out_segment[0])? $out_segment[0] : '';

        if(in_array($page_module, config('panel.UNAUTH_PAGES')))
            return $next($request);

        return parent::handle($request, $next);
    }
}
