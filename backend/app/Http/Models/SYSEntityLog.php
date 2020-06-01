<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// init models
//use App\Http\Models\Conf;

class SYSEntityLog extends Base
{

    use SoftDeletes;
    public $table = 'sys_entity_log';
    public $timestamps = true;
    public $primaryKey;
    protected $dates = ['deleted_at'];

    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table;
        $this->primaryKey = 'entity_log_id';
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields = array($this->primaryKey, 'log_id', "plugin_id", 'actor_entity', 'actor_id', 'reference_module', 'reference_id', 'against', 'against_id', "tracking_id", "navigation_type", "navigation_item_id", "is_read", 'created_at', 'updated_at', 'deleted_at');
    }

}