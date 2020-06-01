<?php namespace App\Http\Models;

use App\Libraries\CustomHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// init models
//use App\Http\Models\Conf;

class SYSExtension extends Base
{

    use SoftDeletes;
    public $table = 'sys_extension';
    public $timestamps = true;
    public $primaryKey;
    protected $dates = ['deleted_at'];

    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table;
        $this->primaryKey = 'extension_id';
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields = array($this->primaryKey, 'title', 'identifier', 'type', 'schema_json', 'required_extensions', 'is_required_assigning', 'created_at', 'updated_at', 'deleted_at');
    }


    /**
     * check existence (add extension)
     * @param string $extension_identifier
     * @return bool
     */
    function checkExists($extension_identifier)
    {
        $ext_dir = getcwd() . DIRECTORY_SEPARATOR . config("constants.DIR_EXTENSION") . $extension_identifier;
        $installation_file = $ext_dir . DIRECTORY_SEPARATOR . "installation.php";
        return file_exists($installation_file);
    }

    /**
     * install (add extension)
     * @param string $extension_identifier
     * @return NULL
     */
    function install($extension_identifier)
    {
        return $this->_install($extension_identifier);
    }

    /**
     * install (add extension process)
     * @param string $extension_identifier
     * @return $id
     */
    private function _install($extension_identifier)
    {
        // defaults
        $timestamp = date("Y-m-d H:i:s");

        // get plugin installation file
        $base_root = getcwd();
        $extension_dir = config("constants.DIR_EXTENSION") . $extension_identifier . "/";
        $extension_config_file = $extension_dir . "installation.php";
        $extension_exists = file_exists($extension_config_file);

        // get configurations
        $extension_config = include_once $extension_config_file;

        // set timestamp
        $extension_config = str_replace("{wildcard_datetime}", $timestamp, $extension_config);
        // replace wildcard (plugin_identifier)
        $extension_config = str_replace("{plugin_identifier}", $extension_config["config"]["identifier"], $extension_config);
        // replace wildcard {plugin_name}
        $extension_config = str_replace("{plugin_name}", $extension_config["config"]["name"], $extension_config);
        // replace wildcard (base_route)
        $extension_config = str_replace("{base_route}", $extension_config["config"]["base_route"], $extension_config);
        // replace wildcard (api_base_route)
        $extension_config = str_replace("{api_base_route}", $extension_config["config"]["api_base_route"], $extension_config);
        // replace extension configuration in sql
        $extension_config = str_replace("{config}", json_encode($extension_config["config"]), $extension_config);



        // execute install sql
        try {
            //exit($extension_config['install_sql']);
            //exit($extension_config['uninstall_sql']);
            \DB::unprepared($extension_config['install_sql']);
            // create directories
            /*if(isset($extension_config["config"]["directories"][0])) {
                foreach($extension_config["config"]["directories"] as $directory) {
                    // replace wildcards
                    $directory = str_replace("{wildcard_identifier}",$entity->identifier,$directory);
                    $directory = str_replace("{wildcard_ucword}",$entity->ucword,$directory);

                    $path_data = explode("/",$directory);

                    $dir_i = count($path_data) - 1;
                    $dir_name = $path_data[$dir_i];
                    // unset last index
                    unset($path_data[$dir_i]);
                    // get full path
                    $path = $base_root.implode("/",$path_data)."/";
                    // create dir if not created
                    if(!is_dir($path.$dir_name)) {
                        mkdir($path.$dir_name);
                    }
                }
            }*/

            // copy all base files
            $base_files = isset($extension_config["base_files"]) ? explode("\n", $extension_config["base_files"]) : array();

            // if got files
            if (isset($base_files[0])) {
                foreach ($base_files as $file) {
                    $file = $new_file_name = trim($file);
                    $file_exists = file_exists($extension_dir . $file);


                    // if exists
                    if ($file_exists) {
                        $file_content = trim(file_get_contents($extension_dir . $file));
                        $file_content = str_replace("{plugin_identifier}", $extension_config["config"]["identifier"], $file_content);
                        /* // file content
                         $file_content = str_replace("{wildcard_identifier}",$entity->identifier, $file_content);
                         $file_content = str_replace("{wildcard_ucword}",$entity->ucword, $file_content);
                         $file_content = str_replace("{wildcard_title}",$entity->title, $file_content);
                         $file_content = str_replace("{wildcard_plural_title}",$entity->plural_title, $file_content);
                         $file_content = str_replace("{wildcard_pk}",$entity->pk, $file_content);
                         $file_content = str_replace("{wildcard_datetime}",date("Y-m-d H:i:s"), $file_content);
                         $file_content = str_replace("{extension_identifier}",$extension_config["config"]["identifier"], $file_content);
                         $file_content = str_replace("{wildcard_identifier}",$entity->identifier, $file_content);
                         $file_content = str_replace("{wildcard_ucword}",$entity->ucword, $file_content);
                         $file_content = str_replace("{base_entity_id}",$entity->entity_id, $file_content);*/

                        // target entity content
                        /*if($target_entity !== FALSE) {
                            $file_content = str_replace("{target_identifier}",$target_entity->identifier, $file_content);
                            $file_content = str_replace("{target_ucword}",$target_entity->ucword, $file_content);
                            $file_content = str_replace("{target_pk}",$target_entity->pk, $file_content);
                            // filename
                            $file = str_replace("{file_target_ucword}",$target_entity->ucword,$file);
                            $file = str_replace("{file_target_identifier}",$target_entity->identifier,$file);

                        } else {*/
                        $file_content = str_replace("{target_identifier}", "", $file_content);
                        $file_content = str_replace("{target_ucword}", "", $file_content);
                        $file_content = str_replace("{target_pk}", "", $file_content);
                        // filename
                        $file = str_replace("{file_target_ucword}", "", $file);
                        $file = str_replace("{file_target_identifier}", "", $file);

                        //}

                        // new filename
                        //$new_file_name = str_replace("{file_ucword}",$entity->ucword,$file);
                        //$new_file_name = str_replace("{file_identifier}",$entity->identifier,$new_file_name);
                        $new_file_name = $base_root . $new_file_name;

                        // create file if not exists
                        if (!file_exists($new_file_name)) {
                            $fo = fopen($new_file_name, "w+");
                            fwrite($fo, $file_content);
                            fclose($fo);
                        }

                    }
                }
            }

            // copy all install files
            $files = explode("\n", $extension_config["install_files"]);

            // if got files
            if (isset($files[0])) {
                foreach ($files as $file) {
                    $file = $new_file_name = trim($file);
                    $file_exists = file_exists($extension_dir . $file);

                    // if exists
                    if ($file_exists) {
                        $file_content = trim(file_get_contents($extension_dir . $file));

                        $file_content = str_replace("{base_route}", $extension_config["config"]["base_route"], $file_content);
                        $file_content = str_replace("{api_base_route}", $extension_config["config"]["api_base_route"], $file_content);
                        $file_content = str_replace("{plugin_identifier}", $extension_config["config"]["identifier"], $file_content);

                        /* // file content
                         $file_content = str_replace("{wildcard_identifier}",$entity->identifier, $file_content);
                         $file_content = str_replace("{wildcard_ucword}",$entity->ucword, $file_content);
                         $file_content = str_replace("{wildcard_title}",$entity->title, $file_content);
                         $file_content = str_replace("{wildcard_plural_title}",$entity->plural_title, $file_content);
                         $file_content = str_replace("{wildcard_pk}",$entity->pk, $file_content);
                         $file_content = str_replace("{wildcard_datetime}",date("Y-m-d H:i:s"), $file_content);
                         $file_content = str_replace("{extension_identifier}",$extension_config["config"]["identifier"], $file_content);
                         $file_content = str_replace("{wildcard_identifier}",$entity->identifier, $file_content);
                         $file_content = str_replace("{wildcard_ucword}",$entity->ucword, $file_content);
                         $file_content = str_replace("{base_entity_id}",$entity->entity_id, $file_content);

                         // target entity content
                         if($target_entity !== FALSE) {
                             $file_content = str_replace("{target_identifier}",$target_entity->identifier, $file_content);
                             $file_content = str_replace("{target_ucword}",$target_entity->ucword, $file_content);
                             $file_content = str_replace("{target_pk}",$target_entity->pk, $file_content);
                             // filename
                             $file = str_replace("{file_target_ucword}",$target_entity->ucword,$file);
                             $file = str_replace("{file_target_identifier}",$target_entity->identifier,$file);

                         } else {
                             $file_content = str_replace("{target_identifier}","", $file_content);
                             $file_content = str_replace("{target_ucword}","", $file_content);
                             $file_content = str_replace("{target_pk}","", $file_content);
                             // filename
                             $file = str_replace("{file_target_ucword}","",$file);
                             $file = str_replace("{file_target_identifier}","",$file);

                         //}

                         // new filename
                         //$new_file_name = str_replace("{file_ucword}",$entity->ucword,$file);
                         //$new_file_name = str_replace("{file_identifier}",$entity->identifier,$new_file_name);*/
                        $new_file_name = $base_root . $new_file_name;
                        $new_file_name = str_replace(array("/", "\""), DIRECTORY_SEPARATOR, $new_file_name);
                        // create directories if not exists
                        $dirs = explode(DIRECTORY_SEPARATOR, $new_file_name);

                        if (count($dirs) > 1) {
                            $dirs = array_slice($dirs, 0, -1);
                            $dir_chain = implode(DIRECTORY_SEPARATOR, $dirs);
                            if (!is_dir($dir_chain)) {
                                mkdir($dir_chain, 0777, true);
                            }
                        }

                        // create file
                        $fo = fopen($new_file_name, "w+");
                        fwrite($fo, $file_content);
                        fclose($fo);

                    }
                }
            }


            // insert plugin module entry
            //$id = $this->put((array)$record);

        } catch (\Illuminate\Database\QueryException $e) {
            var_dump($e->getMessage());
            exit;
        } catch (Exception $e) {
            var_dump($e->getMessage());
            exit;
        }


        return;
    }


    /**
     * doUninstall (remove plugin)
     * @param string $extension_identifier
     * @return NULL
     */
    function unInstall($extension_identifier)
    {
        return $this->_unInstall($extension_identifier);
    }

    /**
     * doUpdate (update plugin)
     * @param integer $entity_id
     * @param integer $extension_id
     * @param array $features
     * @param array $webservices
     * @return NULL
     */
    function xxdoUpdate($entity_id, $extension_id, $features, $webservices)
    {
        // check exists
        $raw_records = $this->select($this->primaryKey)
            ->where("entity_id", "=", $entity_id)
            ->where("extension_id", "=", $extension_id)
            ->whereNull("deleted_at")
            ->get();
        // if exists
        if (isset($raw_records[0])) {
            foreach ($raw_records as $raw_record) {
                // get record
                $record = $this->get($raw_record->{$this->primaryKey});
                // update schema
                $schema = $record->schema === NULL ? (object)array() : json_decode($record->schema);
                // get/set feature key
                //$schema->features = isset($schema->features) ? (array)$schema->features : array();
                // make new
                $schema->features = $schema->webservices = array();
                // collect features
                if (is_array($features) && count($features) > 0) {
                    foreach ($features as $feature) {
                        $f = explode("extension_" . $extension_id . "|", $feature);
                        $schema->features[] = $f[1];
                    }
                }
                // collect webservices
                if (is_array($webservices) && count($webservices) > 0) {
                    foreach ($webservices as $webservice) {
                        $w = explode("extension_" . $extension_id . "|", $webservice);
                        $schema->webservices[] = $w[1];
                    }
                }
                // set data
                $record->schema = json_encode($schema);
                $record->updated_at = date("Y-m-d H:i:s");

                // update
                $this->set($record->{$this->primaryKey}, (array)$record);
            }
        }
    }

    /**
     * Generate Extension Field
     *
     * @return Response
     */
    public function generateField($request, $field, $entity_type_id = NULL, $show = 0, $field_data_type = 'intfield')
    {
        // load model
        $attrDataTypeModel = $this->__modelPath . 'SYSDataType';
        $attrDataTypeModel = new $attrDataTypeModel;
        $attrSetModel = $this->__modelPath . 'SYSAttributeSet';
        $attrSetModel = new $attrSetModel;
        $eTypeModel = $this->__modelPath . 'SYSEntityType';
        $eTypeModel = new $eTypeModel;

        // get attribute data type (integer field)
        $dataType = $attrDataTypeModel->getBy('identifier', $field_data_type, true);
        // get attribute data set (extension)
        $attrSet = $attrSetModel->getBy('identifier', 'extension', true);
        // other fields
        $field_name = ucwords(preg_replace('/[_\-]/', ' $3', $field));
        $entity_type_id = $entity_type_id ? $entity_type_id : $request->{'target_' . $eTypeModel->primaryKey};

        // prepare params
        $params = array(
            'use_entity_type' => 0,
            'entity_type_id' => 0,
            'show_in_list' => 0,
            //'linked_attribute_id' => null,
            'attribute_code' => $field,
            'show_in_search' => 1,
            'data_type_id' => $dataType ? $dataType->{$attrDataTypeModel->primaryKey} : 0,
            'frontend_input' => $field_name,
            'frontend_label' => $field_name,
            //'frontend_class' => ($show == 1) ? '' : 'hide',
            'is_required' => 0,
            'is_user_defined' => 0,
            'default_value' => 0, //initial value
            'is_unique' => 0
        );
        $res = CustomHelper::internalCall($request, \URL::to(DIR_API) . '/system/attribute', 'POST', $params);
        unset($params);

        // set attr id
        $attr_id = $res->error == 0 ? $res->data->attribute->attribute_id : 0;

        // get attribute id if already exists
        if ($attr_id == 0) {
            $params = array(
                'attribute_code' => $field,
                'data_type_id' => $dataType ? $dataType->{$attrDataTypeModel->primaryKey} : 0,
            );
            // get attribute id
            $res = CustomHelper::internalCall($request, \URL::to(DIR_API) . '/system/attribute/listing', 'GET', $params);
            unset($params);
            if ($res->error == 0) {
                $attr_id = isset($res->data->attribute_listing[0]) ?
                    $res->data->attribute_listing[0]->attribute_id :
                    0;
            }
        }

        // bind attribute if got attribute id
        if ($attr_id > 0) {
            // prepare params
            $params = array(
                'entity_type_id' => $entity_type_id,
                'show_in_list' => $show,
                'attribute_set_id' => $attrSet->{$attrSetModel->primaryKey},
                'attribute_id' => $attr_id,
                'sort_order' => 100, // try to allot at very last
            );
            $res2 = CustomHelper::internalCall($request, \URL::to(DIR_API) . '/system/entity_attribute', 'POST', $params);
            unset($params);
        }

        return $attr_id;
    }

    /**
     * _unInstall (remove plugin action)
     * @param integer $extension_identifier
     * @return NULL
     */
    private function _unInstall($extension_identifier)
    {
        // defaults
        $timestamp = date("Y-m-d H:i:s");

        // get plugin installation file
        $base_root = getcwd();
        $extension_dir = config("constants.DIR_EXTENSION") . $extension_identifier . "/";
        $extension_config_file = $extension_dir . "installation.php";
        $extension_exists = file_exists($extension_config_file);

        if ($extension_exists) {

            // get configurations
            $extension_config = include_once $extension_config_file;

            // replace configuration wildcards
            //$extension_config = str_replace("{wildcard_identifier}",$entity->identifier, $extension_config);
            //$extension_config = str_replace("{wildcard_ucword}",$entity->ucword, $extension_config);
            //$extension_config = str_replace("{wildcard_pk}",$entity->pk, $extension_config);
            //$extension_config = str_replace("{wildcard_datetime}",date("Y-m-d H:i:s"), $extension_config);
            $extension_config = str_replace("{plugin_identifier}", $extension_config["config"]["identifier"], $extension_config);
            /*if($target_entity !== FALSE) {
                $extension_config = str_replace("{target_identifier}",$target_entity->identifier, $extension_config);
                $extension_config = str_replace("{target_ucword}",$target_entity->ucword, $extension_config);
                $extension_config = str_replace("{target_pk}",$target_entity->pk, $extension_config);
            } else {
                $extension_config = str_replace("{target_identifier}","", $extension_config);
                $extension_config = str_replace("{target_ucword}","", $extension_config);
                $extension_config = str_replace("{target_pk}","", $extension_config);
            }*/

            // execute uninstall sql
            try {
                //exit($extension_config['uninstall_sql']);
                \DB::unprepared($extension_config['uninstall_sql']);
                \DB::unprepared($extension_config['uninstall_sql']);
                //exit($extension_config['uninstall_sql']);
                // remove all installed files
                $files = explode("\n", $extension_config["uninstall_files"]);

                // if got files
                if (isset($files[0])) {
                    foreach ($files as $file) {
                        // filename
                        $file = $new_file_name = trim($file);
                        $new_file_name = str_replace(array("/", "\""), DIRECTORY_SEPARATOR, $new_file_name);
                        /*$new_file_name = str_replace("{file_ucword}",$entity->ucword,$file);
                        $new_file_name = str_replace("{file_identifier}",$entity->identifier,$new_file_name);

                        if($target_entity !== FALSE) {
                            $extension_config = str_replace("{file_target_identifier}",$target_entity->identifier, $extension_config);
                            $extension_config = str_replace("{file_target_ucword}",$target_entity->ucword, $extension_config);
                            $extension_config = str_replace("{target_pk}",$target_entity->pk, $extension_config);
                            // new filename
                            $new_file_name = str_replace("{file_target_ucword}",$target_entity->ucword,$new_file_name);
                            $new_file_name = str_replace("{file_target_identifier}",$target_entity->identifier,$new_file_name);
                        } else {
                            $extension_config = str_replace("{file_target_identifier}","", $extension_config);
                            $extension_config = str_replace("{file_target_ucword}","", $extension_config);
                            $extension_config = str_replace("{target_pk}","", $extension_config);
                            // new filename
                            $new_file_name = str_replace("{file_target_ucword}","",$new_file_name);
                            $new_file_name = str_replace("{file_target_identifier}","",$new_file_name);
                        }*/
                        $file_exists = file_exists($base_root . $new_file_name);

                        // if exists
                        if ($file_exists) {
                            // remove file
                            @unlink($base_root . $new_file_name);
                        }
                    }
                }

                // if got directories
                if (isset($extension_config["config"]["directories"][0])) {
                    krsort($extension_config["config"]["directories"]);
                    foreach ($extension_config["config"]["directories"] as $directory) {
                        // replace wildcards
                        //$directory = str_replace("{wildcard_identifier}",$entity->identifier,$directory);
                        //$directory = str_replace("{wildcard_ucword}",$entity->ucword,$directory);

                        $path_data = explode("/", $directory);
                        $dir_i = count($path_data) - 1;
                        $dir_name = $path_data[$dir_i];
                        // unset last index
                        unset($path_data[$dir_i]);
                        // get full path
                        $path = $base_root . implode("/", $path_data) . "/";
                        // remove dir
                        $this->removeDir($path . $dir_name);
                        //@rmdir($path.$dir_name);
                    }
                }

            } catch (\Illuminate\Database\QueryException $e) {
                var_dump($e->getMessage());
                exit;
            } catch (Exception $e) {
                var_dump($e->getMessage());
                exit;
            }

        }
    }


}