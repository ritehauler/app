<?php
namespace App\Libraries\Wfs;


class WFSDictionary
{

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(){}

    public function __set($key, $value)
    {
        $this->$key = $value;
    }

    public function __get($key){
        return $this->$key;
    }
}
