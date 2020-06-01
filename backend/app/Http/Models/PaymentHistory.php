<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentHistory extends Base
{

    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table = 'payment_history';
        $this->primaryKey = $this->__table . '_id';
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();
        // set fields
        $this->__fields = array($this->primaryKey, 'transaction_id', 'lender_id', 'order_id', 'product_id', 'borrower_id','payment_method','application_fee','ntrust_fee', 'response','created_at');
    }


    /**
     * Save Stripe History
     * @param array $data
     *
     * @return record_id
     */
    function putStripeHistory($data)
    {

        $data = (object)$data;

        $data->created_at = date('Y-m-d H:i:s');

        // insert record
        $id = $this->put((array)$data);

        // return new id
        return $id;
    }

}