<?php namespace App\Http\Models;
use App\Http\Models\SYSPermission; 
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

class SYSRolePermission extends Base {
	
	use SoftDeletes;
    public $table = 'sys_role_permission_map';
    public $timestamps = true;
	public $primaryKey;
    protected $dates = ['deleted_at'];
	public $sys_permission;
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table;
		$this->primaryKey = 'role_permission_map_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		$this->sys_permission = new SYSPermission();
		
        // set fields
        $this->__fields   = array($this->primaryKey, 'role_id', 'entity_id','module_id','permission_id','do_allow', 'created_at', 'updated_at', 'deleted_at');
	}

	function removeRolePermissions($role_id) {
		$this->where("role_id","=",$role_id)->update(array('do_allow'=>'0'));
	}
	
	/**
     *  
     * 
     * @param string $module_id
     * @param string $group_id
     * @param int $columns
     * @return object $result
     */
	function updatePermission($role_id, $module_id,$permission_id,$save=array()) {	
		$return = NULL;
		
		$count = $this->select($this->primaryKey)
		->where("module_id","=",$module_id)
		->where("permission_id","=",$permission_id)
		->where("role_id","=",$role_id)
		->whereNull("deleted_at")
		->count();
		// remove old if exists
		if($count>0){
			$this->where("role_id","=",$role_id)
			->where("permission_id","=",$permission_id)
			->where("module_id","=",$module_id)
			->update(array('do_allow'=>'1'));
		} else {
			// add new
			$save['module_id']=$module_id;
			$save['permission_id']=$permission_id;
			$save['role_id']=$role_id;
			$save['do_allow']="1";
			$return = $this->put($save);
		}
		return $return;
	}	
	
	/**
     * ckeck admin permissions on given module id
     * 
     * @param string $module_id
     * @param string $group_id
     * @param int $columns
     * @return object $result
     */
	function removePermissions($role_id) {	
		$return = NULL;
		
		$raw_ids = $this->select($this->primaryKey)
		->where("role_id","=",$role_id)
		->whereNull("deleted_at")
		->get();
		// remove old if exists
		if(isset($raw_ids[0])) {
			/*$record = $this->get($raw_ids[0]->{$this->primaryKey});
			$record->deleted_at = date("Y-m-d H:i:s");
			$this->set($record->{$this->primaryKey},(array)$record);*/
			foreach($raw_ids as $raw_id) {
				$this->remove($raw_id->{$this->primaryKey});
			}
		}
		return $return;
	}
	
	
	
	/**
     * ckeck admin permissions on given module id
     * 
     * @param string $module
     * @param string $action
     * @param int $group_id
     * @return boolean (TRUE | FALSE)
     */
    function checkAccess($module, $action, $role_id) {
		if($role_id=="1") return true;
		 
		if(!is_numeric($action)){ 
			$permission = $this->sys_permission
			->where("identifier", "=",$action)
			->whereNull("deleted_at")
			->first();
			 
			if($permission) $action = $permission->permission_id; else $action=0;
		}
		 
		$count = $this
		->where("permission_id", "=",$action)
		->where('ap.role_id', '=', $role_id)
		
		->where("do_allow", "=","1")
		->whereNull("am.deleted_at")
		->whereNull("ap.deleted_at");
		
		if(!is_numeric($module)) $count->where('am.slug', '=', $module);
		else $count->where('am.module_id', '=', $module);
		
		$count = $count->
		join('sys_role_permission_map AS ap', 'ap.module_id', '=', 'am.module_id')
		->from('sys_module AS am')->count();
		 
        return ($count>0)?true:false;
    }

    /**
     * Redirect un authenticated admin user to access module
     * 
     * @param string $module
     * @param string $action
     * @param int $group_id
     */
    function checkModuleAuth($module, $action, $group_id = 1,$return=false) {

        if ($this->checkAccess($module, $action, $group_id) === FALSE) {
			\Session::put(ADMIN_SESS_KEY.'error_msg', 'You are not allowed to access this module');
			\Session::save();
			//$url_redirect = \URL::previous();
			$url_redirect = (\URL::previous() == \URL::current())? \URL::to($this->__getPanelPath()) : \URL::previous();
			
			if(!$return){
				header("location:" . $url_redirect);
				exit;
			}
			return false;
        } 
		return true;
    }

    /**
     * Get list of entities having right of module
     * @param $module
     * @param $action
     * @param $actor_entity_id
     * @return bool
     */
    function entityListByModulePermission($module, $action,$actor_entity_id)
    {
        $row = \DB::select("SELECT e.*,
                        m.module_id,p.permission_id FROM sys_entity_role er
                            LEFT JOIN sys_role_permission_map rp ON er.role_id = rp.role_id
                            LEFT JOIN sys_permission p ON rp.permission_id = p.permission_id
                            LEFT JOIN sys_module m ON (rp.module_id = m.module_id)
                            LEFT JOIN sys_entity e ON e.entity_id = er.entity_id
                            LEFT JOIN sys_entity_auth a ON a.entity_auth_id = e.entity_auth_id
                            WHERE 
                             m.slug = '$module'
                            AND p.identifier = '$action'
                            AND e.deleted_at IS NULL 
                            AND (e.entity_type_id not IN (11,3))
                            AND e.entity_id <> $actor_entity_id
                            AND er.entity_id <> $actor_entity_id
                            AND a.status = 1
                            AND a.is_verified = 1
                            AND rp.do_allow = 1");

        return isset($row[0]) ? $row : false;
    }

}