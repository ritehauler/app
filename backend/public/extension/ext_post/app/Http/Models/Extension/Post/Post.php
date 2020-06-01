<?php namespace App\Http\Models\Extension\Post;

use App\Http\Models\Base;
use App\Libraries\CustomHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;
use Illuminate\Http\Request;

// init models
//use App\Http\Models\Conf;

class Post extends Base
{

    use SoftDeletes;
    public $table = 'ext_post';
    public $timestamps = true;
    public $primaryKey = 'post_id';
    protected $dates = ['deleted_at'];
    public $objectIdentifier = "post";
    public $actionIdentifier = "post";
    private $_pHook = "ExtPost"; // extension hook
    public $currentActorID = NULL;

    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table;
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields = array($this->primaryKey, 'entity_type_extension_map_id', 'actor_entity_id', 'ref_post_id', 'post_type_id', 'title', 'search_term', 'content', 'data_packet', 'location', 'latitude', 'longitude', 'count_share', 'count_view', 'is_share_enabled,', 'starting_at', 'ending_at', 'status', 'created_at', 'updated_at', 'deleted_at');
    }


}