<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// init models
//use App\Http\Models\EFPlugin;

class PEPost extends Base {
	
	use SoftDeletes;
    public $table = 'pe_post';
    public $timestamps = true;
	public $primaryKey;
    protected $dates = ['deleted_at'];
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table;
		$this->primaryKey = 'post_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
         $this->__fields   = array($this->primaryKey, 'user_id', 'content_type', 'title', 'content', 'location', 'latitude', 'longitude', 'count_like', 'count_share', 'count_comment', 'count_view', 'created_at', 'updated_at', 'deleted_at');
	}	
	
}