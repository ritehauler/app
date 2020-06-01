<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// models
#use App\Http\Models\Setting;

class EntityCartItem extends Base {
	
	use SoftDeletes;
    public $table = 'entity_order_item';
    public $timestamps = true;
	public $primaryKey;
    protected $dates = ['deleted_at'];
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table;
		$this->primaryKey = $this->table.'_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
        $this->__fields   = array($this->primaryKey, 'item_id', 'entity_id', 'quantity', 'base_price', 'total_price', 'base_discount', 'total_discount', 'status', 'created_at', 'updated_at', 'deleted_at');
	}
	
}
