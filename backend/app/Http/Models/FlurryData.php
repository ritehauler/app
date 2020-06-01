<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// models
//use App\Http\Models\UserDisability;

class FlurryData extends Base {
	
	use SoftDeletes;
    public $table = 'flurry_data';
    public $timestamps = true;
	public $primaryKey;
    protected $dates = ['deleted_at'];
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table;
		$this->primaryKey = $this->__table . '_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
         $this->__fields   = array($this->primaryKey, 'country', 'date', 'value', 'metric', 'app_version', 'device_type', 'created_at', 'updated_at', 'deleted_at');
	}
	
	/**** 
	* Function Get Data 
	* @Param String Device Type
	* @Param String Metric
	**/
	function getFlurryData($device_type, $metric){
		$query =  $this->selectRaw("CONCAT('[',GROUP_CONCAT(`value`),']') AS `value`, CONCAT('[',GROUP_CONCAT(CONCAT('\"',DATE_FORMAT(`date`, '%b %d'),'\"')),']') AS `date`, SUM(VALUE) AS total_value, COUNT(VALUE) AS count_value")
                  ->whereRaw("device_type =  '".$device_type."' AND metric = '".$metric."' AND created_at LIKE '%".DATE_GRAPH."-%' ");
		$query->from("flurry_data");
		$raw_records = $query->first();
		return $raw_records;
	}
	
	
	
	/**** 
	* Function Map
	* @Param String Device Type
	* @Param --
	**/
	function flurryMap($device_type){
		$query =  $this->selectRaw('country AS `code`, COUNT(`value`) AS value')
                  ->whereRaw("device_type =  '".$device_type."' AND metric = 'ActiveUsers' AND created_at LIKE '%".DATE_GRAPH."-%' ")
				  ->groupBy('country');
		$query->from("flurry_data");
		$raw_records = $query->get();
		return $raw_records;
	}

	
}