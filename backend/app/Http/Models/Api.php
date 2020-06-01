<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Api extends Base {
	
	private $_token_prefix;
	
	public function __construct()
	{
		$this->_token_prefix = API_SALT."api_token-";
	}
	
}
