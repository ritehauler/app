<?php
namespace App\Libraries;
/**
 * Class WalletTransaction
 * @package App\Libraries
 */
Class WalletTransaction
{
    private $_modelPath = '';
    private $_table = '';
    private $_SYSTableFlatModel = '';

    /**
     * WalletTransaction constructor.
     */
    public function __construct()
    {
        $this->_modelPath = config("system.MODEL_PATH");
        $this->_table = 'wallet_transaction';
        $this->_SYSTableFlatModel = $this->_modelPath . "SYSTableFlat";
        $this->_SYSTableFlatModel = new $this->_SYSTableFlatModel($this->_table);
    }

    /**
     * @param $customer_id
     * @return int
     */
    public function getCurrentBalance($customer_id)
    {
        $current_balance =  $this->_SYSTableFlatModel->columnValueByWhere('customer_id', $customer_id, 'balance','DESC');
        return ($current_balance) ? $current_balance : 0;

    }

    /**
     * @param $customer_id
     * @param int $credit
     * @param int $debit
     * @return int
     */
    public function calculateBalance($customer_id,$credit = 0, $debit = 0)
    {
        //get customer current balance
        $current_balance = $this->getCurrentBalance($customer_id);
        $balance = $this->balanceWalletAmount($current_balance,$credit,$debit);
        return $balance;

    }

    /**
     * balance Wallet Amount
     * @param $current_balance
     * @param int $credit
     * @param int $debit
     * @return mixed
     */
    public function balanceWalletAmount($current_balance,$credit = 0, $debit = 0)
    {
        $balance = $current_balance;

        if($credit > 0)
            $balance  = $current_balance + $credit;

        if($debit > 0)
            $balance  = $current_balance - $debit;

        return $balance;

    }



}