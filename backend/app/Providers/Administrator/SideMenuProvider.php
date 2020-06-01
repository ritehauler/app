<?php

namespace App\Providers\Administrator;

use Illuminate\Support\ServiceProvider;

class SideMenuProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    private $_model_path = "\App\Http\Models\\";
    private $_session_identifier = ADMIN_SESS_KEY;
    private $_dir = DIR_ADMIN;
    private $_model = "AdminModule";
    private $_data = array();
    protected $__meta;


    public function boot()
    {

        $view_dir = trim(strtolower($this->_dir),"/");

        view()->composer(
            $view_dir.'.sidebar', function($view){

            // init models
            $this->_model = $this->_model_path . $this->_model;
            $this->_model = new $this->_model;
            $conf_model = $this->_model_path . "Conf";
            $conf_model = new $conf_model;

            // get meta data
            $detail = $conf_model->getBy('key', 'site');
            $detail = json_decode($detail->value);

            // init set values for view
            $this->_data["_dir"] = $this->_dir;
            $this->_data["_model"] = $this->_model;
            $this->_data["_session_identifier"] = $this->_session_identifier;
            $this->_data["_meta"] = $detail;
            ///////////////////


// get modules that are view-able
            $query = $this->_model->select("am.admin_module_id", "am.parent_id", "am.order", "am.name", "amp.admin_group_id", "amp.view", "am.is_active", "am.show_in_menu")
                ->from("admin_module AS am")
                ->join("admin_module_permission AS amp", "amp.admin_module_id", "=", "am.admin_module_id")
                ->whereRaw("am.is_active = '1'")
                ->whereRaw("am.show_in_menu = '1'")
                ->whereRaw("am.admin_module_id  > '1'")
                ->whereRaw("amp.view = '1'")
                ->whereNull("am.deleted_at")
                ->whereNull("amp.deleted_at");
// basic module sql as per alloted permissions

            $modules_sql = $query->toSql();

            $query = $this->_model->select("admin_module_id")
                ->from(\DB::raw("(".$modules_sql.") AS vtable"))
                ->where("admin_group_id","=",\Session::get(ADMIN_SESS_KEY."auth")->admin_group_id)
                ->where("parent_id","=", 0)
                ->orderBy("order", "ASC");
            $module_ids = $query->get();

            // init final array
            $final_array = array();

            foreach ($module_ids as $key => $module_id){
                $final_array[$key]['admin_module_id'] = $module_id->admin_module_id;

                $record = $this->_model->get($module_id->admin_module_id);
                $has_child = $this->_model->select("admin_module_id")->where("parent_id","=",$record->admin_module_id)->count();

                $p_record = $this->_model->get($record->parent_id);
                $final_array[$key]['admin_module_id'] = $module_id->admin_module_id;
                $final_array[$key]['class_name'] = $record->class_name;
                $final_array[$key]['icon_class'] = $record->icon_class;
                $final_array[$key]['name'] = $record->name;
                $final_array[$key]['has_child'] = 0;

                if($has_child > 0) {
                    $final_array[$key]['has_child'] = 1;

                    $query = $this->_model->select("admin_module_id")
                        ->from(\DB::raw("(" . $modules_sql . ") AS vtable"))
                        ->where("admin_group_id", "=", \Session::get($this->_session_identifier."auth")->admin_group_id)
                        ->where("parent_id", "=", $record->admin_module_id)
                        ->where("is_active", "=", 1)
                        ->orderBy("order", "ASC");
                    $c_module_ids = $query->get();

                    if (isset($c_module_ids[0])) {
                        foreach ($c_module_ids as $key_inner => $c_module_id) {
                            $c_record = $this->_model->get($c_module_id->admin_module_id);
                            //$class_name = $record->class_name . "-" . $c_record->class_name;
                            $class_name = $c_record->class_name;
                            $final_array[$key]['children'][$key_inner]['class_name'] = $class_name;
                            $final_array[$key]['children'][$key_inner]['icon_class'] = $c_record->icon_class;
                            $final_array[$key]['children'][$key_inner]['name'] = $c_record->name;
                        }
                    }
                }


            }

            $this->_data["final_array"] = $final_array;
            $view->with($this->_data);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
