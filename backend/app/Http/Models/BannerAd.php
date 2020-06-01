<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// models
use App\Http\Models\UserDisability;

class BannerAd extends Base {
	
	use SoftDeletes;
    public $table = 'banner_ad';
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
         $this->__fields   = array($this->primaryKey, 'url', 'image', 'created_at', 'updated_at', 'deleted_at');
	}
	
	
	/**
     * Remove
     *
     * @return NULL
     */
	/**
	Method	:	delete
	Reason	:	delete data
	**/
	function remove($id = 0, $remove_file = TRUE) {
		$affected_rows = NULL;
		// get record
		$record = $this->get($id);
		
		if($record !== FALSE) {
			if(SOFT_DELETE === TRUE) {
				//$affected_rows = $this->where($this->__fields[0], '=', $id)->delete();
				$record->deleted_at = date("Y-m-d H:i:s");
				$this->set($record->{$this->__fields[0]},(array)$record);
			} else {
				// remove if file exists
				if(file_exists(BANNER_AD_PATH.$record->image)) {
					@unlink(BANNER_AD_PATH.$record->image);
				}
				$affected_rows = DB::table($this->__table)->where($this->__fields[0], '=', $id)->delete();
				if($this->__useCache===TRUE) {
					$key = is_array($id) ? MEM_KEY.$this->__keyParam.implode('-',$id) : MEM_KEY.$this->__keyParam.$id;
					Cache::forget($key);
				}
			}
		}
	
		return $affected_rows;
	}
	
}