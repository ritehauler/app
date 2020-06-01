<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// models
#use App\Http\Models\Setting;

class FBUserForm extends Base {
	
	use SoftDeletes;
    public $table = 'fb_user_form';
    public $timestamps = true;
	public $primaryKey;
    protected $dates = ['deleted_at'];
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table;
		$this->primaryKey = 'user_form_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
        $this->__fields   = array($this->primaryKey, 'form_id', 'user_id', 'created_at', 'updated_at', 'deleted_at');
	}


	/**
     * remove
     * @param integer $user_id
	 * @return NULL
     */
    /*function remove($id = 0) {		
		// remove user views (given/received)
		$user_view_model = new UserView;
		$query = $user_view_model->select(array("user_view_id"));
		$query->where("user_id", "=", $id);
		$query->whereNull("deleted_at");
		$query_records = $query->get();
		if(isset($query_records[0])) {
			foreach($query_records as $query_record) {
				// remove
				$user_view_model->remove($query_record->user_view_id);
				//$user_view_model->hardRemove($query_record->user_view_id);
				
			}
		}
		
        // return
        return NULL;
    }
    */

	
}
