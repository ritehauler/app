<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

// models
use App\Http\Models\User;

class UserPhoto extends Base {
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table = 'user_photo';
		$this->primaryKey = $this->__table . '_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
        $this->__fields   = array($this->primaryKey, 'user_id', 'src_type', 'src', 'is_selected', 'created_at', 'updated_at', 'deleted_at');
	}
	
	/**
     * Remove
     * @param integer $id
	 * @param bool $remove_file
     * @return integer rows count
     */
	function remove($id = 0, $remove_file = false) {
		$affected_rows = 0;
		$record = $this->get($id);
		if($record !== FALSE) {
			$affected_rows = $this->where($this->__fields[0], '=', $id)->delete();
			if($this->__useCache===TRUE) {
				$key = is_array($id) ? MEM_KEY.$this->__keyParam.implode('-',$id) : MEM_KEY.$this->__keyParam.$id;
				Cache::forget($key);
			}
			// if record removed
			if($affected_rows > 0 && $record->src_type == "src") {
				@unlink(getcwd()."/",DIR_USER_IMG.$record->src);
			}
		}
		return $affected_rows;
	}
	
	/**
     * get User Photos
     * @param integer $user_id
	 * @param string $type (facebook/instagram)
     * @return NULL
     */
    function getUserPhotos($user_id, $type = "") {
		// init data
		$data = array();
		
		// validate type
		$type = trim($type);
		
		// fetch
		$query = $this->select(array($this->primaryKey));
		if($type != "") {
			$query->where("src_type", $type);
		}
		$query->where("user_id", $user_id);
		$query->whereNull("deleted_at");
		$query->orderBy("is_selected", "DESC");
		$query->orderBy($this->primaryKey, "ASC");
		$raw_records = $query->get();
		
		if(isset($raw_records[0])) {
			foreach($raw_records as $raw_record) {
				$record = $this->getData($raw_record->{$this->primaryKey});
				if($record !== FALSE) {
					// manipulate data
					if($record->src_type == "src") {
						$record->url = url("/")."/".DIR_USER_IMG.$record->src;
					} else {
						$record->url = $record->src;
					}
					
					// unset unrequired
					unset($record->src_type,$record->src);
					$data[] = $record;
				}
			}
		}

        // return
        return $data;
    }
	
	
	/**
     * Remove user Photos
     * @param integer $user_id
	 * @param string $type (facebook/instagram)
     * @return NULL
     */
    function removeUserPhotos($user_id, $type = "") {
		// validate type
		$src_type = trim($type);
		
		// fetch
		$query = $this->select(array($this->primaryKey));
		$query->where("user_id", $user_id);
		if($src_type != "") {
			$query->where("src_type", $src_type);
		}
		$query->whereNull("deleted_at");
		$raw_records = $query->get();
		
		if(isset($raw_records[0])) {
			
			foreach($raw_records as $raw_record) {
				$this->remove($raw_record->{$this->$this->primaryKey}, true);
			}
		}
        // return
        return;
    }
	
	
	/**
     * Save user photos
     * @param integer $user_id
	 * @param string $type (facebook/instagram)
	 * @param string $urls Comma seperated urls
	 * @param int $selected_first selected first image (0/1)
     * @return array
     */
    function putUserPhotoUrls($user_id, $type = "", $urls = "", $selected_first = 0) {
		// return
		$return = array();
		
		$data = array();
		if($urls != "") {
			$data = explode(",",$urls);
		}
		
		// validate type
		$src_type = trim($type);
		
		if($src_type != "" && isset($data[0])) {
			// default
			$save_data = array();
			
			$itrator = 0;
			// loop through data
			foreach($data as $record) {
				unset($save_data);
				$save_data["user_id"] = $user_id;
				// if type is src, save base64 image
				if($src_type == "src") {
					$user_img = "photo-".$user_id."-".($itrator + 1)."-".time().".jpg";
					$img_data = @file_get_contents($record);
					@file_put_contents(getcwd()."/".DIR_USER_IMG.$user_img,$img_data);
					$record = $user_img; // replace with image name
				}
				$save_data["src_type"] = $src_type;
				$save_data["src"] = trim($record);
				//$save_data["is_selected"] = $itrator == 0 ? 1 : 0;
				$save_data["is_selected"] = 0;
				if($selected_first == 1) {
					$save_data["is_selected"] = $itrator == 0 ? 1 : 0;
					// mark this as current dp
					$user_model = new User;
					$user = $user_model->get($user_id);
					
					if($src_type == "src") {
						$user_dp = $save_data["src"]; // assign photo name
						$user_thumb = str_replace("photo-","thumb-",$user_dp);
					} else {
						// choose new names
						$user_dp = "dp-".$user->user_id."-".time().".jpg";
						$user_thumb = str_replace("dp-","thumb-",$user_dp);
						// get image contents
						$img_contents = @file_get_contents($save_data["src"]);
						// put image
						$put_img = @file_put_contents(getcwd()."/".DIR_USER_IMG.$user_dp,$img_contents);
						// put thumb
						if($put_img) {
							$thumb_contents = file_get_contents(url("/") . "/" . "thumb/user/150x150/" . $user_dp);
							@file_put_contents(getcwd() . "/" . DIR_USER_IMG . $user_thumb, $thumb_contents);
							
							// remove old
							@unlink(getcwd()."/".DIR_USER_IMG.$user->image);
							@unlink(getcwd()."/".DIR_USER_IMG.$user->thumb);
						}
					}
					// replace new values
					$user->image = $user_dp;
					$user->thumb = $user_thumb;
					// update user
					$user_model->set($user->user_id,(array)$user);
					
				}
				$save_data["created_at"] = date("Y-m-d H:i:s");
				// save
				$save_data[$this->primaryKey] = $this->put($save_data);
				
				// collect in array
				$save_data["url"] = $src_type == "src" ? url("/")."/".DIR_USER_IMG.$save_data["src"] : $save_data["src"];
				$return[] = $save_data;
				
				$itrator++;
			}
		}

        // return
        return $return;
    }
	
	
	/**
     * Set as DP
     * @param integer $user_id
	 * @param integer $user_photo_id
     * @return NULL
     */
    function setAsDP($user_id, $user_photo_id = 0) {
		// get data
		$data = $this->get($user_photo_id);
		
		if($data !== FALSE) {
			// fetch
			$query = $this->select(array($this->primaryKey));
			$query->where("is_selected", 1);
			$query->where("user_id", $user_id);
			$query->orderBy($this->primaryKey, "ASC");
			$raw_records = $query->get();
			
			// mark all as un-selected
			if(isset($raw_records[0])) {
				foreach($raw_records as $raw_record) {
					$record = $this->get($raw_record->{$this->primaryKey});
					
					if($record !== FALSE) {
						// manipulate data
						$record->is_selected = 0;
						$record->updated_at = date("Y-m-d H:i:s");
						// update
						$this->set($record->{$this->primaryKey}, (array)$record);
					}
				}
			}
			
			// mark current as selected
			$data->is_selected = 1;
			$data->updated_at = date("Y-m-d H:i:s");
			$this->set($data->{$this->primaryKey}, (array)$data);
			
			// mark this as current dp
			$user_model = new User;
			$user = $user_model->get($user_id);
			
			if($data->src_type == "src") {
				$user_dp = $data->src; // assign photo name
				$user_thumb = str_replace("photo-","thumb-",$user_dp);
			} else {
				// choose new names
				$user_dp = "dp-".$user->user_id."-".time().".jpg";
				$user_thumb = str_replace("dp-","thumb-",$user_dp);
				// get image contents
				$img_contents = @file_get_contents($data->src);
				// put image
				$put_img = @file_put_contents(getcwd()."/".DIR_USER_IMG.$user_dp,$img_contents);
				// put thumb
				if($put_img) {
					$thumb_contents = file_get_contents(url("/") . "/" . "thumb/user/150x150/" . $user_dp);
					@file_put_contents(getcwd() . "/" . DIR_USER_IMG . $user_thumb, $thumb_contents);
					
					// remove old
					@unlink(getcwd()."/".DIR_USER_IMG.$user->image);
					@unlink(getcwd()."/".DIR_USER_IMG.$user->thumb);
				}
				
			}
			// replace new values
			$user->image = $user_dp;
			$user->thumb = $user_thumb;
			// update user
			$user_model->set($user->user_id,(array)$user);
		}
		
		

        // return
        return;
    }
	
}