<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// init models
//use App\Http\Models\Conf;

class SYSPermission extends Base
{

    use SoftDeletes;
    public $table = 'sys_permission';
    public $timestamps = true;
    public $primaryKey;
    protected $dates = ['deleted_at'];

    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table;
        $this->primaryKey = 'permission_id';
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields = array($this->primaryKey, 'title', "plugin_id", 'identifier', 'created_at', 'updated_at', 'deleted_at');
    }

    /**
     * Get All permission identifier
     * @return array
     */
    public function getAllPermission(){

        $return = array();

        $row = $this->select('identifier')
            ->whereNull("deleted_at")
            ->get();


        if(isset($row[0])){
            foreach($row as $record){
                $return[] = $record->identifier;
            }
        }
        return $return;
    }

}