<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;
use Closure;
use App\Http\Models\ApiUser;

class APIAuth extends BaseVerifier
{

    private $_model;

    public function __construct() {
        $this->_model = new ApiUser;
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

        // disbale CSRF protection for DIR_API
        if ($request->is(DIR_API.'*'))
        {
            $this->_model->checkAccess($request);
            $uri_mask_response = \App\Http\Models\SYSEntityType::getUriMask();
            if(!empty($uri_mask_response['entity_type_id'])) {
                $params['entity_type_id'] = $uri_mask_response['entity_type_id'];
                $request->merge($params);
            }
            return $next($request);
        }

        return parent::handle($request, $next);
    }
}
