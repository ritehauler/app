<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// init models
//use App\Http\Models\Conf;

class SYSRolePermissionMap extends Base
{

    use SoftDeletes;
    public $table = 'sys_role_permission_map';
    public $timestamps = true;
    public $primaryKey;
    protected $dates = ['deleted_at'];

    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table;
        $this->primaryKey = 'role_permission_map_id';
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields = array($this->primaryKey, 'role_id', "entity_id", 'module_id', "permission_id", "do_allow", 'created_at', 'updated_at', 'deleted_at');
    }

}