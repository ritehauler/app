<?php namespace App\Http\Models\Extension\Validator;

use App\Http\Models\Base;
use App\Libraries\CustomHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;
use Illuminate\Http\Request;

// init models
//use App\Http\Models\Conf;

class FormFieldMap extends Base
{

    use SoftDeletes;
    public $table = 'fb_form_field_map';
    public $timestamps = true;
    public $primaryKey = 'form_field_map_id';
    protected $dates = ['deleted_at'];
    public $objectIdentifier = "form_field";
    public $actionIdentifier = "form_field";
    //private $_pHook = "ExtActivityTypeModel"; // extension hook
    public $currentActorID = NULL;

    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table;
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields = array($this->primaryKey, 'entity_type_form_map_id', 'field_type_id', 'field_name', 'title', 'created_at', 'description', 'hint', 'html5_input_type', 'js_validation_type', 'js_validation_rule', 'js_validation_event', 'php_validation_type', 'php_validation_rule', 'updated_at', 'deleted_at');
    }


}