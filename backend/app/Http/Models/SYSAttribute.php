<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// init models
//use App\Http\Models\Conf;

class SYSAttribute extends Base
{

    use SoftDeletes;
    public $table = 'sys_attribute';
    public $timestamps = true;
    public $primaryKey;
    protected $dates = ['deleted_at'];

    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table;
        $this->primaryKey = 'attribute_id';
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields = array($this->primaryKey,  'entity_type_id', 'attribute_code','linked_entity_type_id', 'data_type_id','show_in_search','show_in_list', 'backend_table','backend_table_option','backend_table_value', 'frontend_input', 'frontend_label', 'frontend_class','use_entity_type','linked_attribute_id', 'is_required', 'is_user_defined', 'default_value', 'is_unique',  'created_at', 'updated_at', 'deleted_at');
    }

	public function getEntityTypeByName($attribute_code){
		$row = $this->where('attribute_code', '=', $attribute_code)
                ->whereNull("deleted_at")
                ->get();
		return isset($row[0])?$row[0]:false;
	}

	public static function getLinkedAttributeCode($attribute_code){

        if(is_numeric($attribute_code)){
            $where = " sa.attribute_id = $attribute_code";
        }else{
            $where = " sa.attribute_code = '$attribute_code'";
        }

		$row = \DB::select("SELECT sa.attribute_code 
          FROM sys_attribute sa
        WHERE $where");

		return isset($row[0])?$row[0]:false;
	}
	
}