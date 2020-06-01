<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// init models
//use App\Http\Models\Conf;

class SYSChating extends Base
{
    use SoftDeletes;
    public $table = 'sys_chating';
    public $timestamps = true;
    public $primaryKey;
	private $_modelPath = "\App\Http\Models\\";
	 
    protected $dates = ['deleted_at'];

    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table;
        $this->primaryKey = 'chating_id';
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields = array($this->primaryKey, 'entity_type_id', 'entity_id' , 'message' ,  'user_id' , 'target_user_id' , 'is_read' ,    'created_at', 'updated_at', 'deleted_at');
    }

    public function deleteChating($id){
        $this->where('chating_id', $id)  // find your user by their email
        ->limit(1)  // optional - to ensure only one record is updated.
        ->update(array('deleted_at' => date("Y-m-d H:i:s")));  // update the record in the DB.
    }
	
}