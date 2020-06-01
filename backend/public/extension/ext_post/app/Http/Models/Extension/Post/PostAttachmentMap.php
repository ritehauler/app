<?php namespace App\Http\Models\Extension\Post;

use App\Http\Models\Base;
use App\Libraries\CustomHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;
use Illuminate\Http\Request;

// init models
//use App\Http\Models\Conf;

class PostAttachmentMap extends Base
{

    use SoftDeletes;
    public $table = 'ext_post_attachment_map';
    public $timestamps = true;
    public $primaryKey = 'post_attachment_map_id';
    protected $dates = ['deleted_at'];
    public $objectIdentifier = "post_attachment_map";
    public $actionIdentifier = "post_attachment_map";
    private $_pHook = "ExtPost"; // extension hook
    public $currentActorID = NULL;

    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table;
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields = array($this->primaryKey, 'post_id', 'attachment_id', 'search_term', 'created_at', 'updated_at', 'deleted_at');
    }


}