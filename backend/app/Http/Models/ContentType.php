<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

class ContentType extends Base {
	
	use SoftDeletes;
    public $table = 'content_type';
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
         $this->__fields   = array($this->primaryKey, 'title','created_at', 'updated_at', 'deleted_at');
	}
	
	
	/**
     * Remove
     *
     * @return NULL
     */
    /*function remove($id = 0) {
		$record = $this->get($id);
		if($record !== FALSE) {
			$record->deleted_at = date("Y-m-d H:i:s");
			$this->set($record->{$this->primaryKey},(array)$record);
			// dependency (user collection)
			$user_disability_model = new UserDisability;
			$user_disability_model->where($this->primaryKey,"=",$record->{$this->primaryKey})->delete();
		}
        // return
        return;
    }*/
	
}