<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// init models
//use App\Http\Models\Conf;

class EFEntity extends Base {
	
	use SoftDeletes;
    public $table = 'ef_entity';
    public $timestamps = true;
	public $primaryKey;
    protected $dates = ['deleted_at'];
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table;
		$this->primaryKey = 'entity_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
         $this->__fields   = array($this->primaryKey, 'title', 'plural_title', 'table_sql_type', 'identifier', 'pk', 'ucword', 'related_entity_id', 'created_at', 'updated_at', 'deleted_at');
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
     * doInstall
     * @param object $data
     * @return NULL
     */
    function doInstall($data) {
		
		// if table SQL type is not none
		if($data->table_sql_type != "none") {
			// get sql installation
			$base_root = getcwd();
			$plugin = $data->table_sql_type;
			$plugin_dir = config("constants.DIR_PLUGIN")."entity/";
			$plugin_config_file = $plugin_dir.$plugin."/installation.php";
			$plugin_exists = file_exists($plugin_config_file);
			
			if($plugin_exists) {
				// get configurations
				$plugin_config = include_once $plugin_config_file;
				$entity = $data;
				
				// replace configuration wildcards
				$plugin_config = str_replace("{wildcard_identifier}",$entity->identifier, $plugin_config);
				$plugin_config = str_replace("{wildcard_ucword}",$entity->ucword, $plugin_config);
				$plugin_config = str_replace("{wildcard_title}",$entity->title, $plugin_config);
				$plugin_config = str_replace("{wildcard_plural_title}",$entity->plural_title, $plugin_config);
				$plugin_config = str_replace("{wildcard_pk}",$entity->pk, $plugin_config);
				$plugin_config = str_replace("{wildcard_datetime}",date("Y-m-d H:i:s"), $plugin_config);
				//$plugin_config = str_replace("{plugin_identifier}",$plugin_config["config"]["identifier"], $plugin_config);
				
				// execute install sql
				try {
					\DB::unprepared($plugin_config["install_sql"]);
					
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
								//$file_content = str_replace("{plugin_identifier}",$plugin_config["config"]["identifier"], $file_content);
								$file_content = str_replace("{wildcard_identifier}",$entity->identifier, $file_content);
								$file_content = str_replace("{wildcard_ucword}",$entity->ucword, $file_content);
								$file_content = str_replace("{base_entity_id}",$entity->entity_id, $file_content);
								
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
					
				} catch (\Illuminate\Database\QueryException $e) {
					var_dump($e->getMessage());
				} catch(Exception $e) {
					var_dump($e->getMessage());
				}
			}
		}
		
    }
	
	
	/**
     * doUninstall
     * @param object $data
     * @return NULL
     */
    function doUninstall($data) {
		// if table SQL type is not none
		if($data->table_sql_type != "none") {
			// load model
			$entity_plugin_model = new \App\Http\Models\EFEntityPlugin;
			
			// get sql installation
			$base_root = getcwd();
			$plugin = $data->table_sql_type;
			$plugin_dir = config("constants.DIR_PLUGIN")."entity/";
			$plugin_config_file = $plugin_dir.$plugin."/installation.php";
			$plugin_exists = file_exists($plugin_config_file);
			
			if($plugin_exists) {
				// get configurations
				$plugin_config = include_once $plugin_config_file;
				$entity = $data;
				
				// replace configuration wildcards
				$plugin_config = str_replace("{wildcard_identifier}",$entity->identifier, $plugin_config);
				$plugin_config = str_replace("{wildcard_ucword}",$entity->ucword, $plugin_config);
				$plugin_config = str_replace("{wildcard_title}",$entity->title, $plugin_config);
				$plugin_config = str_replace("{wildcard_plural_title}",$entity->plural_title, $plugin_config);
				$plugin_config = str_replace("{wildcard_pk}",$entity->pk, $plugin_config);
				$plugin_config = str_replace("{wildcard_datetime}",date("Y-m-d H:i:s"), $plugin_config);
				//$plugin_config = str_replace("{plugin_identifier}",$plugin_config["config"]["identifier"], $plugin_config);
				
				// execute install sql
				try {
					\DB::unprepared($plugin_config["uninstall_sql"]);
					
					// remove all installed files
					$files = explode("\n",$plugin_config["uninstall_files"]);
					
					// if got files
					if(isset($files[0])) {
						foreach($files as $file) {
							$file = trim($file);
							$new_file_name = str_replace("{file_ucword}",$entity->ucword,$file);
							$new_file_name = str_replace("{file_identifier}",$entity->identifier,$new_file_name);
							$file_exists = file_exists($base_root.$new_file_name);
							
							// if exists
							if($file_exists) {
								// remove file
								@unlink($base_root.$new_file_name);		
							}
						}
					}
					
				} catch (\Illuminate\Database\QueryException $e) {
					var_dump($e->getMessage());
				} catch(Exception $e) {
					var_dump($e->getMessage());
				}
			}
			// uninstall plugins installed to this entity
			$installed_plugins = $entity_plugin_model->select("plugin_id")
				->where($this->primaryKey,"=",$data->{$this->primaryKey})
				->whereNull("deleted_at")
				->get();
			// if found, uninstall all related features/webservices
			if(isset($installed_plugins[0])) {
				foreach($installed_plugins as $installed_plugin) {
					$entity_plugin_model->doUninstall($data->{$this->primaryKey}, $installed_plugin->plugin_id);
				}
			}
		}
		
    }
	
	
	/**
     * remove
     * @param integer $id
     * @return NULL
     */
    function remove($id = 0) {
		$record = $this->get($id);
		// has record ?
		if($record !== FALSE) {
			// if not deleted
			if($record->deleted_at === NULL) {
				// un-install base sql
				$this->doUninstall($record);
				// remove
				if(SOFT_DELETE === TRUE) {
					//$affected_rows = $this->where($this->__fields[0], '=', $id)->delete();
					$record->deleted_at = date("Y-m-d H:i:s");
					$this->set($record->{$this->__fields[0]},(array)$record);
				} else {
					$affected_rows = \DB::table($this->__table)->where($this->__fields[0], '=', $id)->delete();
					if($this->__useCache===TRUE) {
						$key = is_array($id) ? MEM_KEY.$this->__keyParam.implode('-',$id) : MEM_KEY.$this->__keyParam.$id;
						Cache::forget($key);
					}
				}
			}
		}
        // return
        return;
    }
	
}