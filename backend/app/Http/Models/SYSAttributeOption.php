<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// init models
//use App\Http\Models\Conf;

class SYSAttributeOption extends Base
{

    use SoftDeletes;
    public $table = 'sys_attribute_option';
    public $timestamps = true;
    public $primaryKey;
    protected $dates = ['deleted_at'];

    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table;
        $this->primaryKey = 'attribute_option_id';
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields = array($this->primaryKey, 'attribute_id', 'value', 'option','is_other','file','sort_order', 'created_at', 'updated_at', 'deleted_at');
    }
	
	function getOptionData($attribute_option_id='0') {

		$optionData = $this->
		select($this->primaryKey,'at.attribute_id','at.attribute_code','at.frontend_label','ao.option','ao.value','ao.is_other','ao.file')->
		join('sys_attribute_option AS ao', 'ao.attribute_id', '=', 'at.attribute_id')
		->from('sys_attribute AS at')
		->whereNull("ao.deleted_at")
		->whereNull("at.deleted_at")
		->orderBy("ao.sort_order", "ASC");
		if($attribute_option_id!='0'){
			$optionData->where("ao.attribute_option_id","=",$attribute_option_id);
		}
		$optionData = $optionData->get();
		 
        return isset($optionData[0])?$optionData[0]:false;

    }

	public function getAttributeByName($identifier){
		$row = $this->where('identifier', '=', $identifier)
                ->whereNull("deleted_at")
                ->get();
		return isset($row[0])?$row[0]:false;
	}

	public function getAttributeById($attribute_id,$value=""){
		$row = $this->select(array('attribute_option_id','option','value','is_other'))
				->where('attribute_id', '=', $attribute_id)
                ->whereNull("deleted_at")
				->orderBy("sort_order", "ASC");
		if($value!=="") $row = $row->where('value', '=',$value);
		$data =   $row->get();  
		 
		return isset($data[0])?$data[0]:false;
	}
	// Duplicate "getAttributeById" this function to Re-arrage keys
	public function getAttributeOptionValueById($attribute_id,$value="",$option=''){
		$row = $this->select(array('attribute_option_id as id','option','value','is_other'))
			->where('attribute_id', '=', $attribute_id)
			->whereNull("deleted_at")
			->orderBy("sort_order", "ASC");
		if($value!=="") $row = $row->where('value', '=',$value);
		if($option!=="") $row = $row->where('option', '=',"$option");
		$data =   $row->get();

		return isset($data[0])?$data[0]:false;
	}

	/**
	 * Get attribute option by attribute code
	 * @param $attribute_code
	 * @param string $value
	 * @return bool
	 */
	public function getAttributeOptionByAttribute($attribute_code,$value=""){

		$row = $this->select(array('attribute_option_id','option','value','is_other'))
			->join('sys_attribute AS attribute', 'attribute.attribute_id', '=', $this->__table.".attribute_id")
			->where('attribute.attribute_code', '=', $attribute_code)
			->where($this->__table.'.value', '=', $value)
			->whereNull($this->__table.".deleted_at");

		$data =   $row->get();

		return isset($data[0])?$data[0]:false;
	}

	/**
	 * @param $attribute_code
	 * @return bool
	 */
	public function getByAttributeCode($attribute_code,$value)
	{
		$data = \DB::select("SELECT ao.*
			FROM sys_attribute a
			LEFT JOIN sys_attribute_option ao ON ao.attribute_id = a.attribute_id
			WHERE a.attribute_code = '$attribute_code' AND ao.value = '$value'");

		return isset($data[0])?$data[0]:false;
	}

    /**
     * get Attribute option by attr code and value
     * @param $attribute_code
     * @param $value
     * @return bool
     */
    public function getOptionByAttributeCode($attribute_code,$value)
    {
        $data = \DB::select("SELECT ao.option
			FROM sys_attribute a
			LEFT JOIN sys_attribute_option ao ON ao.attribute_id = a.attribute_id
			WHERE a.attribute_code = '$attribute_code' AND ao.value = '$value'");

        return isset($data[0]->option)?$data[0]->option:false;
    }

    /**
     * Get attribute options by attribute code
     * @param $attribute_code
     * @return bool
     */
    public function getDataByAttributeCode($attribute_code)
    {
        $data = \DB::select("SELECT ao.*
			FROM sys_attribute a
			LEFT JOIN sys_attribute_option ao ON ao.attribute_id = a.attribute_id
			WHERE a.attribute_code = '$attribute_code'");

        return isset($data[0]->option)? $data :false;
    }

    /**
     * @param $attribute_code
     * @param $options
     * @return array|bool
     */
    public function checkValidOptions($attribute_code,$options)
    {
        $query = "SELECT ao.value
			FROM sys_attribute a
			LEFT JOIN sys_attribute_option ao 
			ON ao.attribute_id = a.attribute_id
			WHERE a.attribute_code = '$attribute_code'
			AND ao.value IN ($options)";
        $data = \DB::select($query);

        if(isset($data[0])){
            foreach($data as $raw){
                $return[] = $raw->value;
            }

            return $return;
        }
        return false;
    }

}