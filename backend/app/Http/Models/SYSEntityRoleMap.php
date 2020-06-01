<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// init models
//use App\Http\Models\Conf;

class SYSEntityRoleMap extends Base
{

    use SoftDeletes;
    public $table = 'sys_entity_role';
    public $timestamps = true;
    public $primaryKey;
    protected $dates = ['deleted_at'];

    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table;
        $this->primaryKey = 'entity_role_id';
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields = array($this->primaryKey, 'entity_id', "role_id",'created_at', 'updated_at', 'deleted_at');
    }

    function InsertRoleEntity($role_id , $entity_id) {

        $return = NULL;

        $count = $this->select($this->primaryKey)
            ->where("role_id","=",$role_id)
            ->where("entity_id","=",$entity_id)
            ->whereNull("deleted_at")
            ->count();
        // remove old if exists
        if($count<=0){
            // add new
            $save['role_id']=$role_id;
            $save['entity_id']=$entity_id;
           // $save['is_default']=$is_default;
            $return = $this->put($save);
        }


        return $return;


    }

    /**
     * @param $entity_id
     * @return bool
     */
    function getRoleByEntity($entity_id)
    {
        $row = $this->where("entity_id","=",$entity_id)
            ->whereNull("deleted_at")
            ->get();

        return isset($row[0]) ? $row[0]->role_id : false;
    }

    /**
     * Get Role Information by entity id
     * @param $entity_id
     * @return bool
     */
    function getRoleInfoByEntity($entity_id)
    {
        $row = $this->where("entity_id","=",$entity_id)
            ->join('sys_role AS role', 'role.role_id', '=', $this->__table.".role_id")
            ->whereNull($this->__table.".deleted_at")
            ->get();

        return isset($row[0]) ? $row[0] : false;
    }
}