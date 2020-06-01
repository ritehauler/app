<?php namespace App\Http\Models\Extension\Post;

use App\Http\Models\Base;
use App\Libraries\CustomHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;
use Illuminate\Http\Request;

// init models
//use App\Http\Models\Conf;

class PostTagMap extends Base
{

    use SoftDeletes;
    public $table = 'ext_post_tag_map';
    public $timestamps = true;
    public $primaryKey = 'post_tag_map_id';
    protected $dates = ['deleted_at'];
    public $objectIdentifier = "post_tag_map";
    public $actionIdentifier = "post_tag_map";
    private $_pHook = "ExtPostTagMap"; // extension hook
    public $currentActorID = NULL;

    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table;
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields = array($this->primaryKey, 'post_id', 'post_tag_id', 'label', 'created_at', 'updated_at', 'deleted_at');
    }


}