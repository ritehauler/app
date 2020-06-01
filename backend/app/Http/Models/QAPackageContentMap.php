<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// models
#use App\Http\Models\Setting;

class QAPackageContentMap extends Base {
	
	use SoftDeletes;
    public $table = 'qa_package_content_map';
    public $timestamps = true;
	public $primaryKey;
    protected $dates = ['deleted_at'];
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table;
		$this->primaryKey = 'package_content_map_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
        $this->__fields   = array($this->primaryKey, 'package_id', 'content_id', 'pattern', 'max_level', 'min_level', 'max_score', 'min_score', 'condition_type', 'condition', 'worth', 'multiplier_effect', 'order', 'created_at', 'updated_at', 'deleted_at');
	}
	
	
    /**
     * insertPackageMap
     * @param integer $id
	 * @param bool $remove_file
     * @return NULL
     */
    public function insertPackageMap($package_type_id, $package_id, $content_id, $language_id, $worth, $order){
        $qa_package_map = QAPackageContentMap::create();
        $qa_package_map->package_type_id = $package_type_id;
        $qa_package_map->package_id = $package_id;
        $qa_package_map->content_id = $content_id;
        $qa_package_map->language_id = $language_id;
        $qa_package_map->worth = $worth;
        $qa_package_map->order = $order;
        $qa_package_map->save();
        
        if($qa_package_map->save()) {
            return $qa_package_map->package_content_map_id;
        }
        return false;
    }
	
	/**
     * getWorth
     * @param integer $package_id
     * @return integer
     */
	  public function getWorth($package_id) {
        $query = $this->selectRaw("COALESCE(SUM(worth), 0) as worth");
		$query->whereNull("deleted_at");
		$query->where("package_id","=",$package_id);
		$raw_records = $query->get();
		$raw_records = json_decode(json_encode($raw_records));
        return isset($raw_records[0]->worth) ? $raw_records[0]->worth : 0;
    }
	
}