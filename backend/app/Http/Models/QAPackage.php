<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// models
#use App\Http\Models\Setting;

class QAPackage extends Base {
	
	use SoftDeletes;
    public $table = 'qa_package';
    public $timestamps = true;
	public $primaryKey;
    protected $dates = ['deleted_at'];
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table;
		$this->primaryKey = 'package_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
        $this->__fields   = array($this->primaryKey, 'title', 'order', 'created_at', 'updated_at', 'deleted_at');
	}

    public function insertPackage($title, $order){
        //Inserting QA Package
        $qa_package = QAPackage::create();
        $qa_package->title = $title;
        $qa_package->order = $order;
        $qa_package->save();
        
        if($qa_package->save()){
            return $qa_package->package_id;
        }else{
            return false;
        }
    }
	
}
