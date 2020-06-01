<?php
/**
 *
 */
namespace App\Libraries;

use App\Http\Models\FlatTable;
use App\Http\Models\SYSEntityAuth;
use App\Http\Models\SYSEntityType;
use App\Http\Models\SYSTableFlat;
use Validator;

Class EntityDriver
{
    private $_SYSTableFlatModel = '';
    private $_table;

    /**
     * EntityCustomer constructor.
     */
    public function __construct()
    {
        $this->_table = "driver";
        $this->_SYSTableFlatModel = new SYSTableFlat($this->_table);

    }

    /**
     * Get Total customer count
     * @param $start_date
     * @param $end_date
     * @return mixed
     */
    public function totalCount($start_date,$end_date)
    {
        $where_condition = " AND f.created_at >= '$start_date' AND f.created_at <= '$end_date'";
        $flat_table_model = new FlatTable();
        return $flat_table_model->totalAuthCount($this->_table,$where_condition);
    }

    /**
     * @param $request_params
     * @return mixed
     */
    public function validateBasicAuth($request_params)
    {
	 
        $assignData['error'] = 0;
        $assignData['message'] = 'success';
        $request_params = ($request_params && is_object($request_params)) ? (array)$request_params : $request_params;

        $entity_type_model = new SYSEntityType();
        $entity_auth_model = new SYSEntityAuth();

        $rules = array(
            $entity_type_model->primaryKey => 'required|integer|exists:' . $entity_type_model->table . "," . $entity_type_model->primaryKey . ",allow_auth,1,deleted_at,NULL",
            'email' => 'email|required|unique:' . $entity_auth_model->table . ',email,NULL,entity_auth_id,is_verified,1,deleted_at,NULL',
            'password' => 'required|min:6',
        );

        // validations
        $validator = Validator::make($request_params, $rules);

        if ($validator->fails()) {
            $assignData['error'] = 1;
            $assignData['message'] = $validator->errors()->first();
        }

        return $assignData;
    }

    /**
     * @param $status
     * @return string
     */
    public static function getRequestedIdentifierByStatus($status)
    {
        if($status == 3) return  'blacklist_customer';
        return 'customer';

    }

}