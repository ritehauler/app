<?php namespace App\Http\Models\Extension\Post;

use App\Http\Models\Base;
use App\Libraries\CustomHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;
use Illuminate\Http\Request;

// init models
//use App\Http\Models\Conf;

class PostType extends Base
{

    use SoftDeletes;
    public $table = 'ext_post_type';
    public $timestamps = true;
    public $primaryKey = 'post_type_id';
    protected $dates = ['deleted_at'];
    public $objectIdentifier = "post_type";
    public $actionIdentifier = "post_type";
    private $_pHook = "ExtPostType"; // extension hook
    public $currentActorID = NULL;

    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table;
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields = array($this->primaryKey, 'title', 'identifier', 'created_at', 'updated_at', 'deleted_at');
    }


}