<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

// models
//use App\Http\Models\User;

class StoredCard extends Base {
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table = 'stored_card';
		$this->primaryKey = $this->__table . '_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
        $this->__fields   = array($this->primaryKey, 'user_id','stripe_card_id', 'card_number', 'card_type', 'pay_type', 'product_id' ,'order_id', 'created_at', 'updated_at', 'deleted_at');
	}

	public function getData($storedCardId=0){

		$data = $this->get($storedCardId);	// card data

		if ($data !== FALSE) {

			$cardObj = (object) array();

			$cardObj->card_id = $data->stored_card_id;
			$cardObj->card_type = $data->card_type;
			$cardObj->card_number = $data->card_number;
			$cardObj->pay_type = $data->pay_type;
			$cardObj->user_id = $data->user_id;
			$cardObj->product_id = $data->product_id;
			$cardObj->order_id = $data->order_id;

			$data = $cardObj;
		}else{
			$data = (object) array();
		}

		return $data;
	}
}