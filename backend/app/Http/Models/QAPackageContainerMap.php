<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// models
#use App\Http\Models\Setting;

class QAPackageContainerMap extends Base {
	
	use SoftDeletes;
    public $table = 'qa_package_container_map';
    public $timestamps = true;
	public $primaryKey;
    protected $dates = ['deleted_at'];
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table;
		$this->primaryKey = 'package_container_map_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
        $this->__fields   = array($this->primaryKey, 'container_id', 'package_id', 'package_type_id', 'title', 'created_at', 'updated_at', 'deleted_at');
	}
    
    public function insertPackageContainerMap($container_id, $package_id, $title){
        $qa_package_container_map = QAPackageContainerMap::create();
        $qa_package_container_map->container_id = $container_id;
        $qa_package_container_map->package_id = $package_id;
        $qa_package_container_map->title = $title;
        $qa_package_container_map->save();
        
        if($qa_package_container_map->save()) {
            return true;
        }
        return false;
    }
	
}