<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// init models
//use App\Http\Models\Conf;

class SYSTableFlat extends Base
{
    use SoftDeletes;
	public $table;
	public $__table;
    public $timestamps = true;
    public $primaryKey;
	public $__fields;
    protected $dates = ['deleted_at'];

    public function __construct($table,$primaryKey='id')
    {
        // set tables and keys
		$this->table = $table.'_flat';
		$this->__table = $this->table;
 
        $this->primaryKey = $primaryKey;
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields[] = $primaryKey;
    }



    /**
     * Get Flat Table data
     * @return mixed
     */
    public function getAll($limit = false,$order_by = ''){
        $table_name = $this->__table;
        $limit = ($limit) ? $limit = "LIMIT 0,$limit" : "";
        $data = \DB::select("SELECT * FROM $table_name WHERE `deleted_at` IS NULL $order_by $limit");
        return $data;
    }

    /**
     * @param bool $where_column
     * @param bool $where_value
     * @param $get_column
     * @param string $order
     * @return bool
     */
    public function columnValueByWhere($where_column = false,$where_value = false,$get_column,$order = 'ASC')
    {
        if($where_column && $where_value)
        {
            $table_name = $this->__table;
          //  echo "SELECT $get_column FROM $table_name WHERE $where_column = '$where_value' AND `deleted_at` IS NULL ORDER BY id $order";
            $row = \DB::select("SELECT $get_column FROM $table_name WHERE $where_column = '$where_value' AND `deleted_at` IS NULL ORDER BY id $order");

            return isset($row[0]->{$get_column}) ? $row[0]->{$get_column} : false;
        }

    }

    /**
     * Get columns from flat table
     * @param bool $where_column
     * @param bool $where_value
     * @param array $get_columns
     * @return bool
     */
    public function getColumnsByWhere($where_column=false, $where_value = false, $get_columns=array())
    {
        if($where_column && $where_value)
        {
            $get_columns = (count($get_columns)>0) ? implode(',',$get_columns) : '*';

            $table_name = $this->__table;
             $row = \DB::select("SELECT $get_columns FROM $table_name WHERE $where_column = '$where_value' AND `deleted_at` IS NULL");
            if($row)
                return isset($row[0]) ? $row[0] : false;
            else
                return false;
        }

    }

    /**
     * @param bool $where_condition
     * @param array $get_columns
     * @return bool
     */
    public function getDataByWhere($where_condition = false,$get_columns=array(),$order = 'ASC')
    {
        $where = 'WHERE `deleted_at` IS NULL';
        $where = ($where_condition) ?  $where." AND ".$where_condition : $where;

        $get_columns = (count($get_columns)>0) ? implode(',',$get_columns) : '*';

           // echo "SELECT $get_columns FROM ".$this->__table." $where ORDER BY id $order";
            $row = \DB::select("SELECT $get_columns FROM ".$this->__table." $where ORDER BY id $order");
            if($row)
                return isset($row[0]) ? $row : false;


        return false;

    }

    /**
     * @param bool $where_condition
     * @param array $get_columns
     * @return bool
     */
    public function getJoinDataByWhere($where_condition = false, $join = [], $get_columns=array(),$order = 'ASC')
    {
        $where = 'WHERE ' . $this->__table . '.deleted_at IS NULL';
        $where = ($where_condition) ?  $where." AND ".$where_condition : $where;

        $get_columns = (count($get_columns)>0) ? implode(',',$get_columns) : '*';

            //echo "SELECT $get_columns FROM ".$this->__table." $where ORDER BY id $order";
            $row = \DB::select("SELECT $get_columns FROM ".$this->__table. ' ' . implode(' ', $join) ." $where ORDER BY " . $this->__table. ".id $order");
            if($row)
                return isset($row[0]) ? $row : false;


        return false;

    }


    /**
     * @param $get_column
     * @return bool
     */
    public function getColumn($get_column){
        $table_name = $this->__table;
        $row = \DB::select("SELECT $get_column FROM $table_name WHERE `deleted_at` IS NULL LIMIT 0,1");
        return isset($row[0]->{$get_column}) ? $row[0]->{$get_column} : false;
    }

    /**
     * @param bool $where_condition
     * @param array $get_columns
     * @return bool
     */
    public function getColumnByWhere($where_condition = false,$get_column,$order = 'ASC')
    {
        $where = 'WHERE `deleted_at` IS NULL';
        $where = ($where_condition) ?  $where." AND ".$where_condition : $where;
       //echo "SELECT $get_column FROM ".$this->__table." $where"; exit;
        $row = \DB::select("SELECT $get_column FROM ".$this->__table." $where");
        if($row)
            return isset($row[0]) ? $row[0] : false;


        return false;

    }

    public function getEntityIdentifier($entity_id)
    {
        $row = \DB::select("SELECT sys_entity_type.identifier FROM sys_entity
LEFT JOIN sys_entity_type ON sys_entity_type.entity_type_id = sys_entity.entity_type_id
WHERE sys_entity.entity_id = $entity_id");

        if($row)
            return isset($row[0]) ? strtolower($row[0]->identifier) : '';


        return '';
    }

}