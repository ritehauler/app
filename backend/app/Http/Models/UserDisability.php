<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

// models
use App\Http\Models\Disability;

use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

class UserDisability extends Base {
	
	use SoftDeletes;
    public $table = 'user_disability';
    public $timestamps = true;
	public $primaryKey;
    protected $dates = ['created_at','updated_at','deleted_at'];
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table;
		$this->primaryKey = $this->__table . '_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
		// set fields
        $this->__fields   = array($this->primaryKey, 'user_id', 'disability_id', 'created_at', 'updated_at', 'deleted_at');
	}
	
	
	/**
     * Save user disabilities
     *
     * @return NULL
     */
    function putUserDisabilities($user_id, $disabilities_ids) {
		// init models
		$disability_model = new Disability;
		
		$disabilities_ids_data = explode(",",$disabilities_ids);
		
		if(isset($disabilities_ids_data[0])) {
			
			foreach($disabilities_ids_data as $disability_id) {
				$data = $disability_model->get($disability_id);
				if($data !== FALSE) {
					$save_data = array(
						"user_id" => $user_id,
						"disability_id" => $data->disability_id,
						"created_at" => date("Y-m-d H:i:s")
					);
					$this->put($save_data);
				}
			}
		}
		
        // return
        return;
    }
	
	
	/**
     * get User Disabilities
     * @param integer $user_id
     * @return NULL
     */
    function getUserDisabilities($user_id) {
		// init models
		$diability_model = new Disability;
		
		// init data
		$data = array();
		
		// fetch
		$query = $this->select(array("d.disability_id"));
		$query->join('disability AS d', 'd.disability_id', '=', 'ud.disability_id');
		$query->from("user_disability AS ud");
		$query->where("ud.user_id", $user_id);
		$query->whereNull("ud.deleted_at");
		$query->whereNull("d.deleted_at");
		$query->orderBy("d.name", "ASC");
		$raw_records = $query->get();
		
		if(isset($raw_records[0])) {
			foreach($raw_records as $raw_record) {
				$record = $diability_model->getData($raw_record->disability_id);
				if($record !== FALSE) {
					$data[] = $record;
				}
			}
		}

        // return
        return $data;
    }
	
	/**
     * get User Disabilities
     * @param integer $user_id
     * @return String
     */
    function getUserDisabilitiesInString($user_id) {
		$data_ids = array();
		$data = $this->getUserDisabilities($user_id);
		if(isset($data[0])) {
			foreach($data as $record) {
				$data_ids[] = $record->disability_id;
			}
		}
        // return
        return implode(",",$data_ids);
    }
	
	/**
     * Remove user disabilities
     *
     * @return NULL
     */
    function removeUserDisabilities($user_id) {
		// fetch
		$query = $this->select(array($this->primaryKey));
		$query->select($this->primaryKey);
		$query->where("user_id", $user_id);
		$query->whereNull("deleted_at");
		$raw_records = $query->get();
		
		if(isset($raw_records[0])) {
			foreach($raw_records as $raw_record) {
				$this->remove($raw_record->{$this->primaryKey});
			}
		}
        // return
        return;
    }
	
	
}