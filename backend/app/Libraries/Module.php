<?php namespace App\Libraries;

use App\Http\Models\SYSModule;
use App\Http\Models\SYSRolePermission;

/**
 * Simple Fields Library
 *
 *
 * @category   Libraries
 * @package    Menu
 * @subpackage Libraries
 */
class Module
{
    /**
     * Constructor
     *
     * @param string $url URL
     */
    private $_entity_session_identifier;

    public function __construct()
    {
        $this->_entity_session_identifier = config("panel.SESS_KEY");
    }

    public function getAllMenus($parent_id = "0")
    {
        $moduleModel = new SYSModule;

        $menu = $moduleModel
            ->where("parent_id", "=", $parent_id)
            ->where("is_active", "=", "1")
            ->where("show_in_menu", "=", "1")
            ->where("slug", "<>", "dashboard")
            ->where("module_id", "<>", 1)
            ->whereNull("deleted_at")
            ->orderBy("order", "ASC")
            ->get();
        if ($menu->count() > 0) return $menu;
        return false;
    }

    public function getRoleMenus($parent_id = "0")
    {
        $role_id = \Session::get($this->_entity_session_identifier . 'entity_role_id');
        $moduleModel = new SYSModule;
        $menu = $moduleModel
            ->select("am.*")
            ->where("parent_id", "=", $parent_id);
        $menu->where("is_active", "=", "1");
        $menu->where("show_in_menu", "=", "1")
            ->whereNull("am.deleted_at");
        if ($role_id != 1) {
            $menu->where("do_allow", "=", "1")
                ->whereNull("pm.deleted_at")
                ->where("role_id", "=", $role_id)
                ->join('sys_role_permission_map AS pm', 'pm.module_id', '=', 'am.module_id');
        }
        $menu->from('sys_module AS am')
            ->groupBy("module_id")
            ->orderBy("order", "ASC");
        $menu = $menu->get();
        if ($menu->count() > 0) return $menu;
        return false;
    }

    /**
     * Get Module by slug
     * @param string $slug
     * @return bool
     */
    public function getModuleBySlug($slug = "0")
    {
        $moduleModel = new SYSModule;

        $menu = $moduleModel
            ->where("slug", "=", $slug)
            ->where("is_active", "=", "1")
            ->whereNull("deleted_at")
            ->orderBy("order", "ASC")
            ->get();
        if ($menu->count() > 0) return $menu[0];
        return false;
    }

    /**
     *  Check action permission for advance template
     * where add action is calling inside listing action
     * @param $page
     * @param $action
     * @return mixed
     */
    public function checkActionPermission($page,$action)
    {
        $role_id = \Session::get($this->_entity_session_identifier . 'entity_role_id');
        $role_permission_model = new SYSRolePermission();
        if (!$role_permission_model->checkModuleAuth($page, $action,
            $role_id, true)
        ) {
            $assignData['error'] = 1;
            $assignData['message'] = 'You are not allowed to access this module';

            return $assignData;
        }
    }

}

?>