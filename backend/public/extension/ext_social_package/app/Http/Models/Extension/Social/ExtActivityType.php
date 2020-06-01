<?php namespace App\Http\Models\Extension\Social;

use App\Http\Models\Base;
use App\Libraries\CustomHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;
use Illuminate\Http\Request;

// init models
//use App\Http\Models\Conf;

class ExtActivityType extends Base
{

    use SoftDeletes;
    public $table = 'ext_activity_type';
    public $timestamps = true;
    public $primaryKey = 'activity_type_id';
    protected $dates = ['deleted_at'];
    public $objectIdentifier = "activity_type";
    public $actionIdentifier = "activity_type";
    private $_pHook = "ExtActivityTypeModel"; // extension hook
    public $currentActorID = NULL;

    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table;
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields = array($this->primaryKey, 'type', 'title', 'identifier', 'icon_src', 'description', 'json_configuration', 'data_model', 'created_at', 'updated_at', 'deleted_at');
    }


}