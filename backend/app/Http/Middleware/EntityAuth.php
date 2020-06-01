<?php
namespace App\Http\Middleware;

use App\Libraries\CustomHelper;
use Closure;
use Request;
use App\Http\Models\SYSEntityAuth;
use App\Http\Models\SYSRolePermission;
use Cache;

//use App\Http\Models\Conf;

class EntityAuth
{

    private $_model;
    //private $_entity_session_identifier = ADMIN_SESS_KEY;
    private $_entity_session_identifier;

    private $page_modules = [];

    private $module_permission;

    public function __construct()
    {
        $this->page_modules = config('panel.UNAUTH_PAGES');
        $this->_entity_session_identifier = config("panel.SESS_KEY");
        $this->_model = new SYSEntityAuth;
        $this->role_permission = new SYSRolePermission;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next, $type = "")
    {
        $out_segment = CustomHelper::getSegments($request);
        $page_module = isset($out_segment[0]) ? $out_segment[0] : '';
        $action_module = isset($out_segment[1]) ? $out_segment[1] : '';

        //$page_module  = Request()->segment(2);
        //$action_module  = Request()->segment(3);
        if (!in_array($page_module, $this->page_modules)) {
            // check Authorization
            $this->_model->checkAuth($request, $type);
        }

        //restrict to user if  already loggedin to panel then donot switch to other department panel
        $this->_model->checkRequestedDepartment($request);
        //check if current user is not deleted and status us active
        $this->_model->checkActiveSessionUser($request);

        if($page_module == "dashboard"){
            return $next($request);
        }

        if (!in_array($page_module, $this->page_modules)) {
            if ($page_module == 'entities') {
                $page_module = isset($out_segment[1]) ? $out_segment[1] : '';
                $action_module = isset($out_segment[2]) ? $out_segment[2] : '';

            }
            $role_id = \Session::get($this->_entity_session_identifier . 'entity_role_id');
            $this->role_permission->checkModuleAuth($page_module, "view", $role_id);

            if (!empty($_POST)) {
                if ($action_module) {
                    if ($action_module == "add" || $action_module == "update" || $action_module == "delete") {
                        if (!$this->role_permission->checkModuleAuth($page_module, $action_module,
                            $role_id, true)
                        ) {
                            $assignData['redirect'] = \URL::to(\Request::fullUrl());
                            //this is to show javascript error
                            $assignData['error'] = 1;
                            $assignData['message'] = 'You are not allowed to access this module';
                            echo json_encode($assignData);
                            exit();
                        }
                    }
                }
          }
        }
        // proceed
        return $next($request);
    }

}
