<?php namespace App\Http\Models\Extension\Validator;

use App\Http\Models\Base;
use App\Libraries\CustomHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;
use Illuminate\Http\Request;

// init models
//use App\Http\Models\Conf;

class FieldType extends Base
{

    use SoftDeletes;
    public $table = 'fb_field_type';
    public $timestamps = true;
    public $primaryKey = 'field_type_id';
    protected $dates = ['deleted_at'];
    public $objectIdentifier = "field_type";
    public $actionIdentifier = "field_type";
    //private $_pHook = "ExtActivityTypeModel"; // extension hook
    public $currentActorID = NULL;

    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table;
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields = array($this->primaryKey, 'identifier', 'title', 'input_type', 'created_at', 'updated_at', 'deleted_at');
    }


}