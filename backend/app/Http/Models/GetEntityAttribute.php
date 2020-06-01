<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// init models
//use App\Http\Models\Conf;

class GetEntityAttribute extends Base
{

    use SoftDeletes;
    public $table = 'get-entity-attribute';
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
        $this->__fields = array($this->primaryKey,'attribute_set_id','entity_attribute_id',' entity_type_id','attribute_code','identifier','backend_table','frontend_class','frontend_input','frontend_label','is_required','attribute_set_name','entity_title','options');
    }

}