<?php

namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\Controller;
use App\Http\Models\SYSEntityHistory;
use Illuminate\Http\Request;
use View;
use Validator;
// load models
use App\Http\Models\ApiMethod;
use App\Http\Models\ApiMethodField;
use App\Http\Models\ApiUser;
use App\Http\Models\EFEntityPlugin;
use App\Http\Models\Conf;

//use Twilio;

class RoleController extends Controller
{

    private $_apiData = array();
    private $_layout = "";
    private $_models = array();
    private $_jsonData = array();
    private $_model_path = "\App\Http\Models\\";
    private $_object_identifier = "role";
    private $_entity_identifier = "system_role"; // usually routes path
    private $_entity_pk = "role_id";
    private $_entity_ucfirst = "Role";
    private $_entity_model = "SYSRole";
    private $_plugin_config = array();


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        // load entity model
        $this->_entity_model = $this->_model_path . $this->_entity_model;
        $this->_entity_model = new $this->_entity_model;

        // error response by default
        $this->_apiData['kick_user'] = 0;
        $this->_apiData['response'] = "error";
    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function index()
    {

    }


    /**
     * Create
     *
     * @return Response
     */
    public function post(Request $request)
    {
        // trim/escape all
       /* $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));*/

        // extra models
        $exModel = $this->_model_path . "SYSEntityType";
        $exModel = new $exModel;

        // validations
        $rules = array(
            $exModel->primaryKey => 'required|integer:exists:' . $exModel->table . "," . $exModel->primaryKey . ",deleted_at,NULL",
         );

        if(!isset($request->is_group)){
            $rules['parent_id'] = 'required|integer|exists:' . $this->_entity_model->table . "," . $this->_entity_model->primaryKey . ",deleted_at,NULL";

            //Role - customize error message
            $error_messages = array(
                $exModel->primaryKey.".required" => "User Type field is required",
                "parent_id.required" => "Department field is required",
                "parent_id.integer" => "Department field must be integer",
                "parent_id.exists" => "Department is not exist",
                "title.required" => "Designation field is required",
                "title.string" => "Designation field must be string",
                "title.unique" => "Designation already exist",
            );

           // print_r($error_messages); exit;
        }
        else{
            //group - customize error message
            $error_messages = array(
                $exModel->primaryKey.".required" => "User Type field is required",
                "title.required" => "Department field is required",
                "title.string" => "Department name must be string",
                "title.unique" => "Department name already exist",
            );
        }

       // $rules['title'] = "required|string|alpha_custom|unique:sys_role,title,NULL,deleted_at";
        $rules['title'] = "required|string|alpha_custom";

        $validator = Validator::make($request->all(), $rules,$error_messages);

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else {
            //check if department is already exist b/c laraval unique validation was not working on it thats
            //why check by query
            $role = $this->_entity_model->getRoleByTitle(trim($request->title));
            if($role){
                if(!isset($request->is_group)){
                    $this->_apiData['message'] = "Designation name already exist";
                }else{
                    $this->_apiData['message'] = "Department name already exist";
                }

            }
            else{

                // success response
                $this->_apiData['response'] = "success";

                // init output data array
                $this->_apiData['data'] = $data = array();

                // init entity
                $entity = array();
                // map data
                foreach ($rules as $key => $val) {
                    $entity[$key] = $request->input($key, "");
                }
                // other data

                $entity["entity_type_id"] = ($request->input('entity_type_id', "")) ? $request->input('entity_type_id', "") : 0;
                $entity["description"] = ($request->input('description', "")) ? $request->input('description', "") : '';
                $entity["created_at"] = date("Y-m-d H:i:s");

                if(!empty($entity["entity_type_id"])){

                    if(!isset($request->parent_id) || (isset($request->parent_id) && empty($request->parent_id))){
                        $role_record =  $this->_entity_model->where('entity_type_id','=',$entity["entity_type_id"])->where('parent_id','=',0)->get();
                        if(isset($role_record[0])){
                            $role = $role_record[0];
                            $entity["parent_id"] = $role->role_id;
                        }

                    }
                    else{
                        $entity["parent_id"] = ($request->input('parent_id', "")) ? $request->input('parent_id', "") : 0;
                    }
                }


                //print_r($entity); exit;
                $entity["is_group"] = ($request->input('is_group', "")) ? $request->input('is_group', "") : 0;
                $entity_id = $this->_entity_model->put($entity);

                // response data
                $data[$this->_object_identifier] = $this->_entity_model->getData($entity_id);

                $this->_apiData['message'] = trans('api_errors.success');

                // assign to output
                $this->_apiData['data'] = $data;


                //Log History and save system notification
                $sys_history = new SYSEntityHistory();
                $other_data['extension_ref_table'] = 'sys_role';
                $other_data['extension_ref_id'] = $entity_id;
                $timestamp = date("Y-m-d H:i:s");
                $target_entity_id = false;
                $request_params = json_decode(json_encode($request->all()));
                $sys_history->logHistory('entity_add', $entity_id, $target_entity_id, $other_data, $timestamp, $request_params);

            }

        }

        return $this->__ApiResponse($request, $this->_apiData);
    }


    /**
     * Get
     *
     * @return Response
     */
    public function get(Request $request)
    {
        // trim/escape all
        /*$request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));*/

        // validations
        $rules = array(
            $this->_entity_pk => 'required|integer|exists:' . $this->_entity_model->table . "," . $this->_entity_model->primaryKey . ",deleted_at,NULL"
        );
        $validator = Validator::make($request->all(), $rules);

        // get data
        $entity = $this->_entity_model
            ->where($this->_entity_pk, "=", $request->{$this->_entity_pk})
            ->whereNull("deleted_at")
            ->first();

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else if (!$entity) {
            $this->_apiData['message'] = trans('api_errors.invalid_record_request');
        } else {
            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();

            // get user data
            $data[$this->_object_identifier] = $this->_entity_model->getData($entity->{$this->_entity_pk});

            // message
            $this->_apiData['message'] = trans('api_errors.success');

            // assign to output
            $this->_apiData['data'] = $data;
        }


        return $this->__ApiResponse($request, $this->_apiData);
    }


    /**
     * Update
     *
     * @return Response
     */
    public function update(Request $request)
    {
        /*$request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));*/

        // extra models
        $exModel = $this->_model_path . "SYSEntity";
        $exModel = new $exModel;

        // validations
        $rules = array(
            $this->_entity_pk => 'required|integer|exists:' . $this->_entity_model->table . "," . $this->_entity_model->primaryKey . ",deleted_at,NULL",
        );

       // $rules['title'] = "required|string|alpha_custom|unique:".$this->_entity_model->table.",title,".$request->role_id.",role_id";
        $rules['title'] = "required|string|alpha_custom";

        //If update role
        if(!isset($request->is_group)){

            $rules['parent_id'] = 'required|integer|exists:' . $this->_entity_model->table . "," . $this->_entity_model->primaryKey . ",deleted_at,NULL";

            $error_messages = array(
                "title.required" => "Designation field is required",
                "title.string" => "Designation field must be string",
                 "title.unique" => "Designation name already exist",
                "parent_id.required" => "Department field is required",
                "parent_id.integer" => "Department field must be integer",
                "parent_id.exists" => "Department is not exist",
            );
        }
        else{ //If update group
            $error_messages = array(
                "title.required" => "Department field is required",
                "title.string" => "Department field must be string",
                 "title.unique" => "Department name already exist",
            );
        }

        $validator = Validator::make($request->all(), $rules,$error_messages);

        // get data
        $entity = $this->_entity_model
            ->where($this->_entity_pk, "=", $request->{$this->_entity_pk})
            ->whereNull("deleted_at")
            ->first();

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else if (!$entity) {
            $this->_apiData['message'] = trans('api_errors.invalid_record_request');
        } else {

            //check if department is already exist b/c laraval unique validation was not working on it thats
            //why check by query
            $role = $this->_entity_model->getRoleByTitle(trim($request->title),$request->role_id);
            if ($role) {
                $this->_apiData['message'] = "Department name already exist";
            } else{

                // success response
                $this->_apiData['response'] = "success";

            // init output data array
             $this->_apiData['data'] = $data = [];

            // get
            $entity = json_decode(json_encode($entity), TRUE);
            // map data
            foreach ($rules as $key => $val) {
                $entity[ $key ] = $request->input($key, "");
            }

            // other data
            /*    if(isset($request->entity_type_id)){
                    $entity["entity_type_id"] = ($request->input('entity_type_id', "")) ? $request->input('entity_type_id', "") : 0;

                    $role_record =  $this->_entity_model->where('entity_type_id','=',$request->entity_type_id)->where('parent_id','=',0)->get();
                    $role = $role_record[0];
                    $entity["parent_id"] = $role->role_id;
                }
                else{
                    $entity["entity_type_id"] = ($entity['entity_type_id']) ? $entity['entity_type_id'] : 0;
                    $entity["parent_id"] = ($entity['parent_id']) ? $entity['parent_id'] : 0;
                }*/


            $entity["updated_at"] = date("Y-m-d H:i:s");
             $entity["description"] = ($request->input('description', "")) ? $request->input('description', "") : '';
            $entity["is_group"] = ($request->input('is_group', "")) ? $request->input('is_group', "") : 0;
            $entity["parent_id"] = $request->input('parent_id', "");

            $entity_id = $this->_entity_model->set($entity[ $this->_entity_pk ], $entity);

            // response data
            $data[ $this->_object_identifier ] = $this->_entity_model->getData($entity_id);

            $this->_apiData['message'] = trans('api_errors.success');

            // assign to output
            $this->_apiData['data'] = $data;

                //Log History and save system notification
                $sys_history = new SYSEntityHistory();
                $other_data['extension_ref_table'] = 'sys_role';
                $other_data['extension_ref_id'] = $entity[ $this->_entity_pk ];
                $timestamp = date("Y-m-d H:i:s");
                $target_entity_id = false;
                $request_params = json_decode(json_encode($request->all()));
                $sys_history->logHistory('entity_update', $entity[$this->_entity_pk], $target_entity_id, $other_data, $timestamp, $request_params);

            }
         }

        return $this->__ApiResponse($request, $this->_apiData);
    }


    /**
     * Listin / Search
     *
     * @return Response
     */
    public function listing(Request $request)
    {
	
        // trim/escape all
        /*$request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));*/

        // override object identifier
        $this->_object_identifier = $this->_object_identifier . "_" . strtolower(__FUNCTION__);
        // extra models
        $exModel = $this->_model_path . "SYSEntity";
        $exModel = new $exModel;
        // allowed order
        $allowed_ordering = $allowed_searching = $this->_entity_model->primaryKey . ",is_group,title,slug,entity_type_id,parent_id,created_by,created_at";
        $allowed_sorting = "asc,desc";


        // validations
        $rules = array(
            $this->_entity_pk => 'integer|exists:' . $this->_entity_model->table . "," . $this->_entity_model->primaryKey . ",deleted_at,NULL",
           'title' => 'string',
            //'slug' => "string|unique:".$this->_entity_model->table.",".$this->_entity_model->primaryKey.",NULL,deleted_at,parent_id,".$request->parent_id,
           // 'description' => 'string',
           // 'created_by' => 'integer:exists:' . $exModel->table . "," . $exModel->primaryKey . ",deleted_at,NULL",
            "order_by" => "in:" . $allowed_ordering,
            "sorting" => "in:" . $allowed_sorting,
            "offset" => "integer",
            "limit" => "integer"
        );
        $validator = Validator::make($request->all(), $rules);

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else {
            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();

            $listing = array();
            // init listing object
            $data[$this->_object_identifier] = array();

            // sorting defaults
            $request->order_by = $request->input("order_by", "") == "" ? explode(",", $allowed_ordering)[0] : $request->order_by;
            $request->sorting = $request->input("sorting", "") == "" ? explode(",", $allowed_sorting)[0] : $request->sorting;


            $query = $this->_entity_model->select($this->_entity_model->primaryKey);
            if(!isset($request->is_group)){
                $query->where("is_group","<>",1);
                $query->where("parent_id","<>",0);
            }

            $query->whereNull("deleted_at"); // exclude deleted
            // apply search
            $query = $this->_search($request, $query, $allowed_searching);

            // get total
            $total_records = $query->count();


            // default offset / limits
            $request->offset = 0;
            $request->limit = $total_records;
            // if need paging
            // params
            $request->limit = $request->input("limit", "") == "" ? PAGE_LIMIT_API : intval($request->input("limit", ""));
            $request->offset = intval($request->input("offset", 0));
            // offfset / limits / valid pages
            $request->offset = $request->offset < $total_records ? $request->offset : ($total_records - 1);
            $request->offset = $request->offset < 0 ? 0 : $request->offset;

            // apply order
            $query->orderBy($request->order_by, strtoupper($request->sorting));
            $query->take($request->limit);
            $query->skip($request->offset);
            //$raw_records = $query->select(explode(",", $allowed_ordering))->get();
            $raw_records = $query->get();

            // set records
            if (isset($raw_records[0])) {
                //var_dump($raw_records); exit;
                foreach ($raw_records as $raw_record) {
                    //$record = $raw_record;
                    $record = $this->_entity_model->getData($raw_record->{$this->_entity_model->primaryKey});

                    $data[$this->_object_identifier][] = $record;
                }
            }

            // set pagination response
            $data["page"] = array(
                "offset" => $request->offset,
                "limit" => $request->limit,
                "total_records" => $total_records,
                "next_offset" => ($request->offset + $request->limit),
                "prev_offset" => $request->offset > 0 ? ($request->offset - $request->limit) : $request->offset,
            );


            // message
            $this->_apiData['message'] = trans('api_errors.success');

            // assign to output
            $this->_apiData['data'] = $data;
        }


        return $this->__ApiResponse($request, $this->_apiData);
    }


    /**
     * Search
     * @param $query query
     * @return query
     */
    private function _search($request, $query, $allowed_searching = "")
    {
        // fix indexes
        $fix_indexes = array($this->_entity_pk, "target_entity_id", "created_by","entity_type_id","parent_id");
        // search
        foreach (explode(",", $allowed_searching) as $field) {
            // if in fix indexes
            if (in_array($field, $fix_indexes)) {
                // all fix searches
                if ($request->{$field} != "") {
                    $q = trim(strtolower($request->{$field}));
                    $query->where($field, '=', "$q");
                }
            } else {
                // all LIKE searches
                if ($request->{$field} != "") {
                    $q = trim(strtolower($request->{$field}));
                    $query->where($field, 'like', "%$q%");
                }
            }
        }
        return $query;
    }


    /**
     * Delete
     *
     * @return Response
     */
    public function delete(Request $request)
    {
        // trim/escape all
       /* $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));*/

        $rules = array(
            $this->_entity_pk => 'required|integer|exists:' . $this->_entity_model->table . "," . $this->_entity_model->primaryKey . ",deleted_at,NULL"
        );

        $validator = Validator::make($request->all(), $rules);

        // get data
        $entity = $this->_entity_model
            ->where($this->_entity_pk, "=", $request->{$this->_entity_pk})
            ->whereNull("deleted_at")
            ->first();



        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else if (!$entity) {
            $this->_apiData['message'] = trans('api_errors.invalid_record_request');
        } else {


            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();

            // get
            $entity = json_decode(json_encode($entity), true);


            // to-do
            // delete dependencies first
            $this->_entity_model->remove($entity[$this->_entity_pk]);

            // response data
            $data[$this->_object_identifier] = $this->_entity_model->getData($entity[$this->_entity_pk]);

            $this->_apiData['message'] = trans('api_errors.success');

            // assign to output
            $this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request, $this->_apiData);
    }

    public function groups(Request $request)
    {

        // trim/escape all
        /*$request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));*/

        // override object identifier
        $this->_object_identifier = $this->_object_identifier . "_" . strtolower(__FUNCTION__);
        // extra models
        $exModel = $this->_model_path . "SYSEntity";
        $exModel = new $exModel;
        // allowed order
        $allowed_ordering = $allowed_searching = $this->_entity_model->primaryKey . ",title,slug,entity_type_id,created_by,created_at";
        $allowed_sorting = "asc,desc";


        // validations
        $rules = array(
            $this->_entity_pk => 'integer|exists:' . $this->_entity_model->table . "," . $this->_entity_model->primaryKey . ",deleted_at,NULL",
            'title' => 'string',
            //'slug' => "string|unique:".$this->_entity_model->table.",".$this->_entity_model->primaryKey.",NULL,deleted_at,parent_id,".$request->parent_id,
            // 'description' => 'string',
            // 'created_by' => 'integer:exists:' . $exModel->table . "," . $exModel->primaryKey . ",deleted_at,NULL",
            "order_by" => "in:" . $allowed_ordering,
            "sorting" => "in:" . $allowed_sorting,
            "offset" => "integer",
            "limit" => "integer"
        );
        $validator = Validator::make($request->all(), $rules);

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else {
            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();

            $listing = array();
            // init listing object
            $data[$this->_object_identifier] = array();

            // sorting defaults
            $request->order_by = $request->input("order_by", "") == "" ? explode(",", $allowed_ordering)[0] : $request->order_by;
            $request->sorting = $request->input("sorting", "") == "" ? explode(",", $allowed_sorting)[0] : $request->sorting;


            $query = $this->_entity_model->select($this->_entity_model->primaryKey);
            $query->where("parent_id","<>",0);
            $query->where("is_group","=",1);
            $query->whereNull("deleted_at"); // exclude deleted
            // apply search
            $query = $this->_search($request, $query, $allowed_searching);

            // get total
            $total_records = $query->count();


            // default offset / limits
            $request->offset = 0;
            $request->limit = $total_records;
            // if need paging
            // params
            $request->limit = $request->input("limit", "") == "" ? PAGE_LIMIT_API : intval($request->input("limit", ""));
            $request->offset = intval($request->input("offset", 0));
            // offfset / limits / valid pages
            $request->offset = $request->offset < $total_records ? $request->offset : ($total_records - 1);
            $request->offset = $request->offset < 0 ? 0 : $request->offset;

            // apply order
            $query->orderBy($request->order_by, strtoupper($request->sorting));
            $query->take($request->limit);
            $query->skip($request->offset);
            //$raw_records = $query->select(explode(",", $allowed_ordering))->get();
            $raw_records = $query->get();

            // set records
            if (isset($raw_records[0])) {
                //var_dump($raw_records); exit;
                foreach ($raw_records as $raw_record) {
                    //$record = $raw_record;
                    $record = $this->_entity_model->getData($raw_record->{$this->_entity_model->primaryKey});

                    $data[$this->_object_identifier][] = $record;
                }
            }

            // set pagination response
            $data["page"] = array(
                "offset" => $request->offset,
                "limit" => $request->limit,
                "total_records" => $total_records,
                "next_offset" => ($request->offset + $request->limit),
                "prev_offset" => $request->offset > 0 ? ($request->offset - $request->limit) : $request->offset,
            );


            // message
            $this->_apiData['message'] = trans('api_errors.success');
            //echo '<pre>'; print_r($data); exit;
            // assign to output
            $this->_apiData['data'] = $data;
        }


        return $this->__ApiResponse($request, $this->_apiData);
    }

}