<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// models
#use App\Http\Models\Setting;

class EntityOrderInfo extends Base {
	
	use SoftDeletes;
    public $table = 'entity_order_info';
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
        $this->__fields   = array($this->primaryKey, 'cc_title', 'cc_number', 'cc_expiry_date', 'cc_address','created_at', 'updated_at', 'deleted_at');
	}
	
}
