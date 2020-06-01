<?php
namespace App\Libraries\Wfs;


class WFSVariableRights
{

    const READABLE = 'readable';
    const CHANGEABLE = 'changeable';
    private $name, $value, $right;
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct($key, $value, $right)
    {
        $rights = array(self::READABLE, self::CHANGEABLE);

        if(!in_array($right, $rights))
            $right = self::READABLE;
        $this->name = $key;
        $this->value = $value;
        $this->right = $right;
    }

    public function resetValue($value)
    {
        if($this->right == self::CHANGEABLE)
            $this->value = $value;
        return $this->value;
    }

    public function getValue()
    {
        return $this->value;
    }

}
