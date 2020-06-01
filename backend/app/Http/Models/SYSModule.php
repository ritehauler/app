<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// init models
//use App\Http\Models\Conf;

class SYSModule extends Base
{

    use SoftDeletes;
    public $table = 'sys_module';
    public $timestamps = true;
    public $primaryKey;
    protected $dates = ['deleted_at'];

    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table;
        $this->primaryKey = 'module_id';
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields = array($this->primaryKey, 'parent_id', "title", 'slug', 'order', 'entity_type_id','identifier', 'css_class' , 'icon', "show_in_menu","is_active", 'created_at', 'updated_at', 'deleted_at');
    }

}