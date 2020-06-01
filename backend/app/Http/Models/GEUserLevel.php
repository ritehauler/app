<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// init models
use App\Http\Models\Conf;
use App\Http\Models\UserHistory;

class GEUserLevel extends Base {
	
	use SoftDeletes;
    public $table = 'ge_user_level';
    public $timestamps = true;
	public $primaryKey;
    protected $dates = ['deleted_at'];
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table;
		$this->primaryKey = 'user_level_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
         $this->__fields   = array($this->primaryKey, 'level_id', 'user_id', 'created_at', 'updated_at', 'deleted_at');
	}
	
	
	/**
     * Save user package
     *
     * @return NULL
     */
    function putUserData($user_id) {
		// init models
		$conf_model = new Conf;
		
		// validate key
		$key = "game_config";
		$key_data = $conf_model->getSchemaByKey($key);
		
		if($key_data !== FALSE) {
			// if default level is set
			if(isset($key_data->default_level_id)) {
				$save_data["user_id"] = $user_id;
				$save_data["level_id"] = $key_data->default_level_id;
				$save_data["created_at"] = date("Y-m-d H:i:s");
				
				// save
				$record_id = $this->put($save_data);
			}
		}

        // return
        return;
    }
	
	
	 /**
     * get User Data
     * @param integer $user_id
     * @return NULL
     */
    function getUserData($user_id) {
		// init models
		$level_model = new Level;
		
		// init data
		$data = FALSE;
		
		// fetch
		$query = $this->select(array("l.level_id"));
		$query->join('level AS l', 'l.level_id', '=', "ul.level_id");
		$query->from($this->__table." AS ul");
		$query->where("ul.user_id", $user_id);
		$query->whereNull("ul.deleted_at");
		$query->whereNull("l.deleted_at");
		$raw_records = $query->get();
		
		if(isset($raw_records[0])) {
			$data = $level_model->getData($raw_records[0]->level_id);
		}

        // return
        return $data;
    }
	
	
	 /**
     * change Data
     * @param integer $user_id
	 * @param integer $level_id
     * @return NULL
     */
    function changeData($user_id, $level_id) {
		// init models
		$level_model = new Level;
		$user_history_model = new UserHistory;
		
		// fetch
		$query = $this->select(array("ul.user_level_id","l.level_id"));
		$query->join('level AS l', 'l.level_id', '=', "ul.level_id");
		$query->from($this->__table." AS ul");
		$query->where("ul.user_id", $user_id);
		$query->whereNull("ul.deleted_at");
		$query->whereNull("l.deleted_at");
		$raw_records = $query->get();
		
		if(isset($raw_records[0])) {
			// update level
			$record = $this->get($raw_records[0]->{$this->primaryKey});
			
			if($record !== FALSE) {
				$save = (array)$record;
				// update user current level
				$save["level_id"] = $level_id;
				$save["updated_at"] = date("Y-m-d H:i:s");
				$this->set($record->{$this->primaryKey}, $save);
				
				// save history
				$history_identifier = "change_level";
				$history_raw_data = array(
					"reference_module" => "level",
					"reference_id" => $level_id
				);
				$user_history_model->putUserHistory($user_id, $history_identifier, $history_raw_data);
			}
		}

        // return
        return;
    }
}