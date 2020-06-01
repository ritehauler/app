<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// models
#use App\Http\Models\Setting;

class CMSPage extends Base {
	
	use SoftDeletes;
    public $table = 'cms_page';
    public $timestamps = true;
	public $primaryKey;
    protected $dates = ['deleted_at'];
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table;
		$this->primaryKey = $this->__table . '_id';
		$this->__keyParam = $this->primaryKey.'-';
        $this->hidden = array();
        // set fields
        $this->__fields   = array($this->primaryKey, 'slug', 'title', 'meta_keywords', 'meta_description', 'content', 'created_at', 'updated_at', 'deleted_at');
    }
	
}