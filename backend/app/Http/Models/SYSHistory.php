<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// init models
//use App\Http\Models\Conf;

class SYSHistory extends Base
{

    use SoftDeletes;
    public $table = 'sys_history';
    public $timestamps = true;
    public $primaryKey;
    protected $dates = ['deleted_at'];

    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table;
        $this->primaryKey = 'history_id';
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields = array($this->primaryKey, 'identifier', "plugin_identifier", 'notification_type', 'notify_entity', 'notify_target_entity', 'is_user_viewable', 'created_at', 'updated_at', 'deleted_at');
    }

}