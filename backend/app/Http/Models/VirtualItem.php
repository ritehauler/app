<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
// models
//use App\Http\Models\Setting;

class VirtualItem extends Base {

    public function __construct() {
        // set tables and keys
        $this->__table = $this->table = 'virtual_item';
        $this->primaryKey = $this->__table . '_id';
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields = array($this->primaryKey, 'image', 'title', 'type', 'required_value', 'schema', 'ios_key', 'android_key', 'created_at', 'updated_at', 'deleted_at');
    }
	
	
	/**
     * Get Data
     * @param integer $pk
     * @return Object
     */
    public function getData($id = 0) {
		// init target
        $data = $this->get($id);
		// got data
		if($data) {
			// set image path
			$data->image = \URL::to(config("constants.VIRTUAL_ITEM_PATH").$data->image);
			// decode schema
			$data->schema = $data->schema == "" ? (object)array() : json_decode($data->schema);
			// unset unrequired
			unset($data->deleted_at);
			
		}
        return $data;
    }

}
