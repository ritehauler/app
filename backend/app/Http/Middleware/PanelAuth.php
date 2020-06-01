<?php

namespace App\Http\Middleware;

use App\Libraries\CustomHelper;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;
use Closure;
use Redirect;
use App\Http\Models\SYSEntityType;

class PanelAuth extends BaseVerifier
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
        $parameters = $request->route()->parameters();
        $department = $parameters['department'];

        $user_entity = \Session::get(config("panel.SESS_KEY").'auth') ? \Session::get(config("panel.SESS_KEY").'auth') : NULL;

        // get segment
        $out_segment = CustomHelper::getSegments($request);
        $page_module  = isset($out_segment[0])? $out_segment[0] : '';


        if(in_array($page_module, config('panel.UNAUTH_PAGES')))
            return $next($request);

        // if entity exists
        if($user_entity) {
           /* if(in_array($page_module, config('panel.UNAUTH_PAGES')))
                return $next($request);*/

            // verify $dapartment and session department are same, and exist in session
            if($department === \Session::get(config("panel.SESS_KEY").'department')) {

                /*
                 * implementation required
                 * $user_entity->entity_id = $response->entity_id; // department id
                $user_entity->entity_type_id = $response->entity_type_id; // department id

                $user_entity->panel = $department;
                // set in session
                \Session::put(config("panel.SESS_KEY") . 'entity_type_id', $response->entity_type_id);
                \Session::put(config("panel.SESS_KEY") . 'entity_role_id', $response->role_id);

                \Session::push('entity', $user_entity);
                \Session::save();*/
                return $next($request);
            }


            // if not found or does not exist then hit on db and
            // from department name, have to get entity_type_id and bind with entity_type

            // verify user has rights to access
            // verify department exist, if yes then continue else return to default site. or from where
            // on
            if ($request->is(config("panel.DIR").'*'))
            {
                $response = $this->_model->checkPanelAccess($department, $user_entity);
                if($response !== false){

                    /*$entity_id = $response->entity_id;
                    $user_entity->{$department} = new \stdClass();
                    $user_entity->entity_id = $response->entity_id; // department id
                    $user_entity->entity_type_id = $response->entity_type_id; // department id

                    $user_entity->{$department}->entity_id = $response->entity_id; // department id
                    $user_entity->{$department}->entity_type_id = $response->entity_type_id; // department id
                    $user_entity->{$department}->entity_type_title = $department;
                    $user_entity->panel = $department;

                    // set in session
                    \Session::put(config("panel.SESS_KEY") . 'entity_type_id', $response->entity_type_id);
                    \Session::put(config("panel.SESS_KEY") . 'entity_role_id', $response->role_id);


                    \Session::push('entity', $user_entity);
                    \Session::save();*/

                    // Where to SAVE ENTITY Id in session where behaviour get change of the panel
                    // redirection on other panel dashboard on success
                    // else return back to panel (where all panel lists are listed from which user is subscribed)

                    return $next($request);
                }
            }
            //exit;
            //return Redirect::to(URL::previous());
            //return Redirect::back();
            return $next($request);
        }
        return parent::handle($request, $next);
    }
}
