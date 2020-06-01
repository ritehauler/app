<?php
/**
 *
 */
namespace App\Libraries;

use App\Http\Models\FlatTable;
use App\Http\Models\PLAttachment;
use App\Http\Models\SYSAttributeOption;
use App\Http\Models\SYSEntityAuth;
use App\Http\Models\SYSEntityType;
use App\Http\Models\SYSTableFlat;
use Validator;

Class EntityCustomer
{
    private $_SYSTableFlatModel = '';
    private $_table;

    /**
     * EntityCustomer constructor.
     */
    public function __construct()
    {
        $this->_table = "customer";
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

    /**
     * @param $rating_option
     * @return array
     */
    public function getReviewOptions($rating_option)
    {
        $reviews = array();

        if(!empty($rating_option)){

            $rating_option = json_decode($rating_option,true);

            $sys_attribute_option = new SYSAttributeOption();

            if(count($rating_option) > 0){
                foreach($rating_option as $option => $value){

                    $review_option = $sys_attribute_option->getByAttributeCode('customer_review',$option);

                    if(isset($review_option->attribute_option_id)){
                        $review_option->count = $value;


                        $file = new \StdClass();
                        if($review_option->file != ''){
                            $pl_attachment = new PLAttachment();
                            $file =  $pl_attachment->getAttachmentGallery($review_option->file);
                        }

                        $review_option->file = $file;
                        $reviews[] = $review_option;
                    }


                }
            }
        }
        //echo "<pre>"; print_r($reviews); exit;
        return $reviews;
    }


}