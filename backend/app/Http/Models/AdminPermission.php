<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;
use DB;
use URL;

class AdminPermission extends Model {

    use SoftDeletes;

    public $table = 'admin_permissions';
    protected $dates = ['deleted_at'];
    public $timestamps = true;
	public $primaryKey = 'admin_permission_id';

    /**
     * ckeck admin permissions on given module id
     * 
     * @param type $module
     * @param type $action
     * @param type $group_id
     * @return type boolean (TRUE | FALSE)
     */
    function checkAccess($module, $action, $group_id) { die;	
		if($group_id=="1") return true;
        $group_permissions = DB::table('admin_modules AS am')
                ->join('admin_permissions AS ap', 'ap.admin_module_id', '=', 'am.admin_module_id')
                ->select('am.name', 'am.class_name', 'ap.view', 'ap.update', 'ap.delete', 'ap.add')
                ->where('ap.admin_group_id', '=', $group_id)
                ->where($action, '=', '1')
                ->where('am.class_name', '=', $module)
                ->get();
        return count($group_permissions) > 0 ? TRUE : FALSE;
    }

    /**
     * Redirect un authenticated admin user to access module
     * 
     * @param type $module
     * @param type $action
     * @param type $group_id
     */
    function checkModuleAuth($module, $action, $group_id = 1) {
        if ($this->checkAccess($module, $action, $group_id) === FALSE) {
            \Request::session()->flash('auth_msg', 'You are not allowed to access this module.!');
            $url_redirect = URL::previous();
            header("location:" . $url_redirect);
        }
    }

}
