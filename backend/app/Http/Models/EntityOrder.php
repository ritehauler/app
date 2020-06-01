<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// models
#use App\Http\Models\Setting;

class EntityOrder extends Base {
	
	use SoftDeletes;
    public $table = 'entity_order';
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
        $this->__fields   = array($this->primaryKey, 'payment_conf_id', 'transaction_id', 'base_price', 'fee', 'discount', 'total_price', 'params', 'created_at', 'updated_at', 'deleted_at');
	}
	
}
