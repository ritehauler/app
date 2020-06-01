<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// init models
use App\Http\Models\EFPlugin;
use App\Http\Models\EFEntity;

class EFEntityPlugin extends Base {
	
	use SoftDeletes;
    public $table = 'ef_entity_plugin';
    public $timestamps = true;
	public $primaryKey;
    protected $dates = ['deleted_at'];
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table;
		$this->primaryKey = 'entity_plugin_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
         $this->__fields   = array($this->primaryKey, 'entity_id', 'plugin_id', 'version', 'schema', 'created_at', 'updated_at', 'deleted_at');
	}
	
	/**
     * Get Data
     * @param integer $pk
     * @return Object
     */
    public function getData($id = 0) {
		// init target
        $data = $this->get($id);
		// got data
		if($data) {
			// decode schema
			$data->schema = $data->schema !== NULL ? json_decode(trim($data->schema)) : (object)array();
			// unset unrequired
			unset($data->deleted_at);
			
		}
        return $data;
    }

	/**
     * safeEntry (if not exists)
     * @param integer $entity_id
     * @param integer $plugin_id
     * @param string $version
     * @return NULL
     */
    function safeEntry($entity_id, $plugin_id, $version = "1") {
    	$id = FALSE;
    	// check exists
		$exists = $this->select($this->primaryKey)
			->where("entity_id", "=",$entity_id)
			->where("plugin_id","=", $plugin_id)
			->whereNull("deleted_at")
			->count();
		if($exists == 0) {
			$save = array(
				"entity_id" => $entity_id,
				"plugin_id" => $plugin_id,
				"created_at" => date("Y-m-d H:i:s")
			);
			$id = $this->put($save);
		}
		return $id;
    }


    /**
     * safeRemove (remove if features dont exists)
     * @param integer $entity_id
     * @return NULL
     */
    function safeRemove($entity_id) {

    	// get plugins, those features dont exists
    	$raw_records = $this->select($this->primaryKey)
    		->whereRaw("plugin_id NOT IN (
				SELECT plugin_id FROM `ef_plugin_feature` WHERE `plugin_feature_id` IN (
					SELECT plugin_feature_id FROM `ef_entity_plugin_feature`
				))
			")
    		->whereNull("deleted_at")
    		->get();
    	// remove if exists
    	if(isset($raw_records[0])) {
    		foreach($raw_records as $raw_record) {
    			$this->remove($raw_record->{$this->primaryKey});
    		}
    	}
		return;
    }
	
	
	/**
     * doUninstall (remove plugin)
     * @param integer $entity_id
	 * @param integer $plugin_id
     * @return NULL
     */
    function doUninstall($entity_id, $plugin_id) {
		// check exists
		$raw_records = $this->select($this->primaryKey)
			->where("entity_id", "=",$entity_id)
			->where("plugin_id","=", $plugin_id)
			->whereNull("deleted_at")
			->get();
		// if exists
		if(isset($raw_records[0])) {
			foreach($raw_records as $raw_record) {
				// uninstall procedures
				$this->_doUninstall($entity_id, $plugin_id);
				
				// remove entry
				$this->remove($raw_record->{$this->primaryKey});
			}
		}
    }
	
	
	/**
     * _doUninstall (remove plugin)
     * @param integer $entity_id
	 * @param integer $plugin_id
     * @return NULL
     */
    private function _doUninstall($entity_id, $plugin_id) {
		$id = FALSE;
		// init model
		$plugin_model = new EFPlugin;
		// plugin
		$plugin = $plugin_model->get($plugin_id);
		
		if($plugin !== FALSE) {
			// get plugin installation file
			$base_root = getcwd();
			$plugin_dir = config("constants.DIR_PLUGIN").$plugin->identifier."/";
			$plugin_config_file = $plugin_dir."installation.php";
			$plugin_exists = file_exists($plugin_config_file);
			
			if($plugin_exists) {
				// init model
				$entity_model = new EFEntity;
				// get configurations
				$plugin_config = include_once $plugin_config_file;
				// get entity
				$entity = $entity_model->get($entity_id);
				// get target entity
				$target_entity_id = $entity->related_entity_id !== NULL ? $entity->related_entity_id : 0;
				$target_entity = $entity_model->get($target_entity_id);
				
				// replace configuration wildcards
				$plugin_config = str_replace("{wildcard_identifier}",$entity->identifier, $plugin_config);
				$plugin_config = str_replace("{wildcard_ucword}",$entity->ucword, $plugin_config);
				$plugin_config = str_replace("{wildcard_pk}",$entity->pk, $plugin_config);
				$plugin_config = str_replace("{wildcard_datetime}",date("Y-m-d H:i:s"), $plugin_config);
				$plugin_config = str_replace("{plugin_identifier}",$plugin_config["config"]["identifier"], $plugin_config);
				if($target_entity !== FALSE) {
					$plugin_config = str_replace("{target_identifier}",$target_entity->identifier, $plugin_config);
					$plugin_config = str_replace("{target_ucword}",$target_entity->ucword, $plugin_config);
					$plugin_config = str_replace("{target_pk}",$target_entity->pk, $plugin_config);
				} else {
					$plugin_config = str_replace("{target_identifier}","", $plugin_config);
					$plugin_config = str_replace("{target_ucword}","", $plugin_config);
					$plugin_config = str_replace("{target_pk}","", $plugin_config);
				}
				
				// execute uninstall sql
				try {
					\DB::unprepared($plugin_config["uninstall_sql"]);
					\DB::unprepared($plugin_config["uninstall_sql"]);
					//exit($plugin_config["uninstall_sql"]);
					// remove all installed files
					$files = explode("\n",$plugin_config["uninstall_files"]);
					
					// if got files
					if(isset($files[0])) {
						foreach($files as $file) {
							// filename
							$file = trim($file);
							$new_file_name = str_replace("{file_ucword}",$entity->ucword,$file);
							$new_file_name = str_replace("{file_identifier}",$entity->identifier,$new_file_name);
							
							if($target_entity !== FALSE) {
								$plugin_config = str_replace("{file_target_identifier}",$target_entity->identifier, $plugin_config);
								$plugin_config = str_replace("{file_target_ucword}",$target_entity->ucword, $plugin_config);
								$plugin_config = str_replace("{target_pk}",$target_entity->pk, $plugin_config);
								// new filename
								$new_file_name = str_replace("{file_target_ucword}",$target_entity->ucword,$new_file_name);
								$new_file_name = str_replace("{file_target_identifier}",$target_entity->identifier,$new_file_name);
							} else {
								$plugin_config = str_replace("{file_target_identifier}","", $plugin_config);
								$plugin_config = str_replace("{file_target_ucword}","", $plugin_config);
								$plugin_config = str_replace("{target_pk}","", $plugin_config);
								// new filename
								$new_file_name = str_replace("{file_target_ucword}","",$new_file_name);
								$new_file_name = str_replace("{file_target_identifier}","",$new_file_name);
							}
							$file_exists = file_exists($base_root.$new_file_name);
							
							// if exists
							if($file_exists) {
								// remove file
								@unlink($base_root.$new_file_name);		
							}
						}
					}
					
					// if got directories
					if(isset($plugin_config["config"]["directories"][0])) {
						krsort($plugin_config["config"]["directories"]);
						foreach($plugin_config["config"]["directories"] as $directory) {
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
							// remove dir
							$this->removeDir($path.$dir_name);
							//@rmdir($path.$dir_name);
						}
					}
				
				} catch (\Illuminate\Database\QueryException $e) {
					var_dump($e->getMessage()); exit;
				} catch(Exception $e) {
					var_dump($e->getMessage()); exit;
				}
				
			}
		
		}
	}
	
	/**
     * doUpdate (update plugin)
     * @param integer $entity_id
	 * @param integer $plugin_id
	 * @param array $features
	 * @param array $webservices
     * @return NULL
     */
    function doUpdate($entity_id, $plugin_id, $features, $webservices) {
		// check exists
		$raw_records = $this->select($this->primaryKey)
			->where("entity_id", "=",$entity_id)
			->where("plugin_id","=", $plugin_id)
			->whereNull("deleted_at")
			->get();
		// if exists
		if(isset($raw_records[0])) {
			foreach($raw_records as $raw_record) {
				// get record
				$record = $this->get($raw_record->{$this->primaryKey});
				// update schema
				$schema = $record->schema === NULL ? (object)array() : json_decode($record->schema);
				// get/set feature key
				//$schema->features = isset($schema->features) ? (array)$schema->features : array();
				// make new
				$schema->features = $schema->webservices = array();
				// collect features
				if(is_array($features) && count($features) > 0) {
					foreach($features as $feature) {
						$f = explode("plugin_".$plugin_id."|",$feature);
						$schema->features[] = $f[1];
					}
				}
				// collect webservices
				if(is_array($webservices) && count($webservices) > 0) {
					foreach($webservices as $webservice) {
						$w = explode("plugin_".$plugin_id."|",$webservice);
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
     * doInstall (add plugin)
     * @param integer $entity_id
	 * @param integer $plugin_id
	 * @param array $features
	 * @param array $webservices
     * @return NULL
     */
    function doInstall($entity_id, $plugin_id, $features, $webservices) {
		// init model
		$plugin_model = new EFPlugin;
		// plugin
		$plugin = $plugin_model->get($plugin_id);
		
		// check exists
		$record = (object)array(
			"entity_id" => $entity_id,
			"plugin_id" => $plugin->plugin_id,
			"schema" => $plugin->schema,
			"version" => $plugin->version,
			"created_at" => date("Y-m-d H:i:s")
		);
		// update schema
		$schema = $record->schema === NULL ? (object)array() : json_decode($record->schema);
		// get/set feature key
		//$schema->features = isset($schema->features) ? (array)$schema->features : array();
		// make new
		$schema->features = $schema->webservices = array();
		// collect features
		if(is_array($features) && count($features) > 0) {
			foreach($features as $feature) {
				$f = explode("plugin_".$plugin_id."|",$feature);
				$schema->features[] = $f[1];
			}
		}
		// collect webservices
		if(is_array($webservices) && count($webservices) > 0) {
			foreach($webservices as $webservice) {
				$w = explode("plugin_".$plugin_id."|",$webservice);
				$schema->webservices[] = $w[1];
			}
		}
		// set schema
		$record->schema = json_encode($schema);
		
		$id = $this->_doInstall($entity_id, $plugin, $record);
		
		return $id;
    }
	
	/**
     * doInstall (add plugin)
     * @param integer $entity_id
	 * @param object $plugin
	 * @param array $record
     * @return $id
     */
    private function _doInstall($entity_id, $plugin, $record) {
		$id = FALSE;
		
		if($plugin !== FALSE) {
			// get plugin installation file
			$base_root = getcwd();
			$plugin_dir = config("constants.DIR_PLUGIN").$plugin->identifier."/";
			$plugin_config_file = $plugin_dir."installation.php";
			$plugin_exists = file_exists($plugin_config_file);
			
			if($plugin_exists) {
				// init model
				$entity_model = new EFEntity;
				// get configurations
				$plugin_config = include_once $plugin_config_file;
				// get entity
				$entity = $entity_model->get($entity_id);
				// get target entity
				$target_entity_id = $entity->related_entity_id !== NULL ? $entity->related_entity_id : 0;
				$target_entity = $entity_model->get($target_entity_id);
				
				// replace configuration wildcards
				$plugin_config = str_replace("{wildcard_identifier}",$entity->identifier, $plugin_config);
				$plugin_config = str_replace("{wildcard_ucword}",$entity->ucword, $plugin_config);
				$plugin_config = str_replace("{wildcard_title}",$entity->title, $plugin_config);
				$plugin_config = str_replace("{wildcard_plural_title}",$entity->plural_title, $plugin_config);
				$plugin_config = str_replace("{wildcard_pk}",$entity->pk, $plugin_config);
				$plugin_config = str_replace("{wildcard_datetime}",date("Y-m-d H:i:s"), $plugin_config);
				$plugin_config = str_replace("{plugin_identifier}",$plugin_config["config"]["identifier"], $plugin_config);
				if($target_entity !== FALSE) {
					$plugin_config = str_replace("{target_identifier}",$target_entity->identifier, $plugin_config);
					$plugin_config = str_replace("{target_ucword}",$target_entity->ucword, $plugin_config);
					$plugin_config = str_replace("{target_pk}",$target_entity->pk, $plugin_config);
				} else {
					$plugin_config = str_replace("{target_identifier}","", $plugin_config);
					$plugin_config = str_replace("{target_ucword}","", $plugin_config);
					$plugin_config = str_replace("{target_pk}","", $plugin_config);
				}
				
				// execute install sql
				try {
					//exit($plugin_config["install_sql"]);
					//exit($plugin_config["uninstall_sql"]);
					\DB::unprepared($plugin_config["install_sql"]);
					// create directories
					if(isset($plugin_config["config"]["directories"][0])) {
						foreach($plugin_config["config"]["directories"] as $directory) {
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
					}
					
					// copy all base files
					$base_files = isset($plugin_config["base_files"]) ? explode("\n",$plugin_config["base_files"]) : array();
					
					// if got files
					if(isset($base_files[0])) {
						foreach($base_files as $file) {
							$file = trim($file);
							$file_exists = file_exists($plugin_dir.$file);
							
							// if exists
							if($file_exists) {
								$file_content = trim(file_get_contents($plugin_dir.$file));
								// file content
								$file_content = str_replace("{wildcard_identifier}",$entity->identifier, $file_content);
								$file_content = str_replace("{wildcard_ucword}",$entity->ucword, $file_content);
								$file_content = str_replace("{wildcard_title}",$entity->title, $file_content);
								$file_content = str_replace("{wildcard_plural_title}",$entity->plural_title, $file_content);
								$file_content = str_replace("{wildcard_pk}",$entity->pk, $file_content);
								$file_content = str_replace("{wildcard_datetime}",date("Y-m-d H:i:s"), $file_content);
								$file_content = str_replace("{plugin_identifier}",$plugin_config["config"]["identifier"], $file_content);
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
									
								}
								
								// new filename
								$new_file_name = str_replace("{file_ucword}",$entity->ucword,$file);
								$new_file_name = str_replace("{file_identifier}",$entity->identifier,$new_file_name);
								$new_file_name = $base_root.$new_file_name;
								
								// create file if not exists
								if(!file_exists($new_file_name)) {
									$fo = fopen($new_file_name,"w+");
									fwrite($fo,$file_content);
									fclose($fo);
								}
								
							}
						}
					}
					
					// copy all install files
					$files = explode("\n",$plugin_config["install_files"]);
					
					// if got files
					if(isset($files[0])) {
						foreach($files as $file) {
							$file = trim($file);
							$file_exists = file_exists($plugin_dir.$file);
							
							// if exists
							if($file_exists) {
								$file_content = trim(file_get_contents($plugin_dir.$file));
								// file content
								$file_content = str_replace("{wildcard_identifier}",$entity->identifier, $file_content);
								$file_content = str_replace("{wildcard_ucword}",$entity->ucword, $file_content);
								$file_content = str_replace("{wildcard_title}",$entity->title, $file_content);
								$file_content = str_replace("{wildcard_plural_title}",$entity->plural_title, $file_content);
								$file_content = str_replace("{wildcard_pk}",$entity->pk, $file_content);
								$file_content = str_replace("{wildcard_datetime}",date("Y-m-d H:i:s"), $file_content);
								$file_content = str_replace("{plugin_identifier}",$plugin_config["config"]["identifier"], $file_content);
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
									
								}
								
								// new filename
								$new_file_name = str_replace("{file_ucword}",$entity->ucword,$file);
								$new_file_name = str_replace("{file_identifier}",$entity->identifier,$new_file_name);
								$new_file_name = $base_root.$new_file_name;
								
								// create file
								$fo = fopen($new_file_name,"w+");
								fwrite($fo,$file_content);
								fclose($fo);
								
							}
						}
					}
					
					
					// insert plugin module entry
					$id = $this->put((array)$record);
				
				} catch (\Illuminate\Database\QueryException $e) {
					var_dump($e->getMessage()); exit;
				} catch(Exception $e) {
					var_dump($e->getMessage()); exit;
				}				
			}
		}
		
		return $id;
	}
	
	
	/**
     * Get Plugin Schema
     * @param string $entity
	 * @param string $plugin
     * @return Object
     */
    public function xgetPluginSchema($entity, $plugin) {
		$return = FALSE;
		// prepare query
		$query = $this->select($this->primaryKey);
		$query->join("ef_entity AS e", 'e.entity_id', '=', 'ef.entity_id');
		$query->join('ef_plugin AS p', 'p.plugin_id', '=', 'ef.plugin_id');
		$query->from($this->table." AS ef");
		$query->where("e.identifier","=",$entity);
		$query->where("p.identifier","=",$plugin);
		$query->whereNull("ef.deleted_at");
		$query->whereNull("e.deleted_at");
		$query->whereNull("p.deleted_at");
		// init target
        $raw_data = $query->get();
		if(isset($raw_data[0])) {
			$data = $this->getData($raw_data[0]->{$this->primaryKey});
			$return = $data->schema;
		}
        return $return;
    }
	
	
	/**
     * Get Plugin Schema
     * @param int $entity_id
	 * @param int $plugin_id
     * @return Object
     */
    public function xxgetPluginSchema($entity_id, $plugin_id) {
		$return = FALSE;
		// prepare query
		$query = $this->select($this->primaryKey);
		$query->join("ef_entity AS e", 'e.entity_id', '=', 'ef.entity_id');
		$query->join('ef_plugin AS p', 'p.plugin_id', '=', 'ef.plugin_id');
		$query->from($this->table." AS ef");
		$query->where("e.entity_id","=",$entity_id);
		$query->where("p.plugin_id","=",$plugin_id);
		$query->whereNull("ef.deleted_at");
		$query->whereNull("e.deleted_at");
		$query->whereNull("p.deleted_at");
		// init target
        $raw_data = $query->get();
		if(isset($raw_data[0])) {
			$data = $this->getData($raw_data[0]->{$this->primaryKey});
			$return = $data->schema;
		}
        return $return;
    }
	
	
	/**
     * Get Plugin Schema
     * @param int $entity_id
	 * @param string $plugin
     * @return Object
     */
    public function getPluginSchema($entity_id, $plugin) {
		$return = FALSE;
		// prepare query
		$query = $this->select($this->primaryKey);
		$query->join("ef_entity AS e", 'e.entity_id', '=', 'ef.entity_id');
		$query->join('ef_plugin AS p', 'p.plugin_id', '=', 'ef.plugin_id');
		$query->from($this->table." AS ef");
		$query->where("e.entity_id","=",$entity_id);
		$query->where("p.identifier","=",$plugin);
		$query->whereNull("ef.deleted_at");
		$query->whereNull("e.deleted_at");
		$query->whereNull("p.deleted_at");
		// init target
        $raw_data = $query->get();
		if(isset($raw_data[0])) {
			$data = $this->getData($raw_data[0]->{$this->primaryKey});
			$return = $data->schema;
		}
        return $return;
    }
	
	
	
	
	
}