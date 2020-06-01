<?php

/**
 * Class GeneralSetting
 */
namespace App\Libraries;
use App\Http\Models\SYSTableFlat;

Class GeneralSetting
{
    private $_SYSTableFlatModel = '';

    /**
     * ProductHelper constructor.
     */
    public function __construct()
    {
        $this->_SYSTableFlatModel = new SYSTableFlat('general_setting');
    }

    /**
     * Get General Setting
     * @return bool
     */
    public function getSetting()
    {
        $data = $this->_SYSTableFlatModel->getDataByWhere();
        return isset($data[0]) ? $data[0] : new \StdClass();
    }

    /**
     * Get Currency
     * @return bool
     */
    public function getCurrency()
    {
       return $this->_SYSTableFlatModel->getColumn('currency');
    }

    /**
     * @param $amount
     * @return string
     */
    public function getPrettyPrice($amount)
    {
        if($amount > 0) return $this->getCurrency().$amount;
        return $this->getCurrency().'0.00';
    }

    /**
     * @param $column
     * @return bool
     */
    public function getColumn($column)
    {
        return $this->_SYSTableFlatModel->getColumn($column);
    }
	
	
	
}