<?php

namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\Controller;
use App\Http\Models\SYSEntity;
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

class CategoryController extends Controller
{

    private $_apiData = array();
    private $_layout = "";
    private $_models = array();
    private $_jsonData = array();
    private $_model_path = "\App\Http\Models\\";
    private $_object_identifier = "category";
    private $_entity_identifier = "system_category"; // usually routes path
    private $_entity_pk = "category_id";
    private $_entity_ucfirst = "Category";
    private $_entity_model = "SYSCategory";
    private $_plugin_config = array();
    private $_PLAttachment = "PLAttachment";
    private $_mobile_json = false;


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

        $this->_PLAttachment = $this->_modelPath . $this->_PLAttachment;
        $this->_PLAttachment = new $this->_PLAttachment;

        // error response by default
        $this->_apiData['kick_user'] = 0;
        $this->_apiData['response'] = "error";
        $this->_mobile_json = intval($request->input('mobile_json', 0)) > 0 ? true : false;
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
        $exModel = $this->_model_path . "SYSEntity";
        $exModel = new $exModel;

        // validations
        $rules = array(
            'is_parent' => "required",
            'title' => "required|string|alpha_custom|unique:".$this->_entity_model->table.",".$this->_entity_model->primaryKey.",NULL,deleted_at,parent_id,".$request->parent_id,
            'parent_id' => 'required_if:is_parent,0',
            'is_featured' => 'required_if:is_parent,0',
           // 'featured_type' => 'required_if:is_featured,1'
         );

        //Category - customize error message
        $error_messages = array(
            "is_parent.required" => "Please choose the main category or sub category",
            'parent_id.required_if' => 'The Parent field is required',
            'is_featured.required_if' => 'The Is Featured field is required',
            //'featured_type.required_if' => 'The Featured type field is required',
        );


        $validator = Validator::make($request->all(), $rules,$error_messages);

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else {
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


            if($request->is_parent == 0 && $request->parent_id != ""){
               $category_type = $this->_entity_model->getCategoryType($request->parent_id);
                $entity["category_type"] = $category_type;
                $entity["parent_id"] = ($request->input('is_parent', "")) ? $request->input('is_parent', "") : 0;
                $entity["level"] = 2;
            }
            else{
                $entity["category_type"] = ($request->input('category_type', "")) ? $request->input('category_type', "") : 0;
                $entity["parent_id"] = 0;
                $entity["level"] = 1;
            }


            // other data
            $entity["is_featured"] = ($request->input('is_featured', "")) ? $request->input('is_featured', "") : 0;
		    $entity["parent_id"] = ($request->input('parent_id', "")) ? $request->input('parent_id', "") : 0;
            $entity["description"] = ($request->input('description', "")) ? $request->input('description', "") : '';
            $entity["status"] = 1;
            $entity["featured_type"] = ($request->input('featured_type', "")) ? $request->input('featured_type', "") : '';
            $entity["created_at"] = date("Y-m-d H:i:s");

            $entity_id = $this->_entity_model->put($entity);
            $this->_entity_model->addParentList($entity_id);


            // response data
            $data[$this->_object_identifier] = $this->_entity_model->getData($entity_id);

            if (isset($request->gallery_items) && !empty($request->gallery_items)) {

                $attachments = $request->gallery_items;
                if (!is_array($attachments)) $attachments = explode(",", $attachments);
                $gallery_featured_item = 0;
                if (isset($request->gallery_featured_item) && !empty($request->gallery_featured_item)) $gallery_featured_item = $request->gallery_featured_item;
                $this->_PLAttachment->updateAttachmentByEntityID($entity_id, $attachments, $gallery_featured_item);
            }
           // $record->gallery = array();
           // $record->gallery = $this->_PLAttachment->getAttachmentByEntityID($entity_id);

            $this->_apiData['message'] = trans('api_errors.success');

            // assign to output
            $this->_apiData['data'] = $data;

            //Log History and save system notification
            $sys_history = new SYSEntityHistory();
            $other_data['extension_ref_table'] = 'sys_category';
            $other_data['extension_ref_id'] = $entity_id;
            $timestamp = date("Y-m-d H:i:s");
            $target_entity_id = false;
            $request_params = json_decode(json_encode($request->all()));
            $sys_history->logHistory('entity_add', $entity_id, $target_entity_id, $other_data, $timestamp, $request_params);
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
        $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));

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

		
           /* Get attachment data*/
            $gallery = $this->_PLAttachment->getAttachmentByEntityID($entity->{$this->_entity_pk});
            $data[$this->_object_identifier]->image = (object)array();
            if(count($gallery) >0){

                $data_packet = json_decode($gallery[0]->data_packet,true);

                $data[$this->_object_identifier]->image->attachment_id = $gallery[0]->attachment_id;
                $data[$this->_object_identifier]->image->title = $gallery[0]->title;
                $data[$this->_object_identifier]->image->file = $gallery[0]->file;
                $data[$this->_object_identifier]->image->thumb = $gallery[0]->thumb;
                $data[$this->_object_identifier]->image->size = $data_packet['size'];
            }

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
        // trim/escape all
      /*  $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));*/

        // extra models
        $exModel = $this->_model_path . "SYSEntity";
        $exModel = new $exModel;

        // validations
        $rules = array(
            $this->_entity_pk => 'required|integer|exists:' . $this->_entity_model->table . "," . $this->_entity_model->primaryKey . ",deleted_at,NULL",
            'title' => 'required|string||alpha_custom',
            'parent_id' => 'required_if:is_parent,0',
            'is_featured' => 'required_if:is_parent,0',
           // 'featured_type' => 'required_if:is_featured,1',
            'status' => 'required'
         );

        //Category - customize error message
        $error_messages = array(
            'parent_id.required_if' => 'The Parent field is required',
            'is_featured.required_if' => 'The Is Featured field is required',
           // 'featured_type.required_if' => 'The Featured type field is required',
        );


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
            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();

            // get
            $entity = json_decode(json_encode($entity), true);
            // map data
            foreach ($rules as $key => $val) {
                $entity[$key] = $request->input($key, "");
            }

            // other data
            $entity["parent_id"] = ($request->input('parent_id', "")) ? $request->input('parent_id', "") : 0;
            $entity["is_featured"] = ($request->input('is_featured', "")) ? $request->input('is_featured', "") : 0;
            $entity["description"] = ($request->input('description', "")) ? $request->input('description', "") : '';
            $entity["status"] = ($request->input('status', "")) ? $request->input('status', "") : '';
            $entity["featured_type"] = ($request->input('featured_type', "")) ? $request->input('featured_type', "") : '';
            $entity["updated_at"] = date("Y-m-d H:i:s");


            $entity_id = $this->_entity_model->set($entity[$this->_entity_pk], $entity);
            $this->_entity_model->addParentList($entity_id);

            //if parent category is marked as inactive update sub categories as inactive
            if($entity["parent_id"] == 0 && !empty($entity["status"])){
                $child_categories = $this->_entity_model->getChild($entity[$this->_entity_pk]);
                if($child_categories){

                    foreach($child_categories as $child){

                        $child = json_decode(json_encode($child),true);
                        $child['status'] = $entity["status"];
                        $child['updated_at']= date('Y-m-d H:i:s');
                        unset($child['child']);
                        $this->_entity_model->set($child[$this->_entity_pk], $child);
                    }
                }

                //update product category count

            }

            if (isset($request->gallery_items) && !empty($request->gallery_items)) {
                $attachments = $request->gallery_items;
                if (!is_array($attachments)) $attachments = explode(",", $attachments);
                $gallery_featured_item = 0;
                if (isset($request->gallery_featured_item) && !empty($request->gallery_featured_item)) $gallery_featured_item = $request->gallery_featured_item;
                //First delete previous image then upload new one
                $this->_PLAttachment->deleteAttachmentByEntityID($request->{$this->_entity_pk});
                $this->_PLAttachment->updateAttachmentByEntityID($request->{$this->_entity_pk}, $attachments, $gallery_featured_item);
            }

            // response data
            $data[$this->_object_identifier] = $this->_entity_model->getData($entity_id);
            //$data[$this->_object_identifier]->child = $this->_entity_model->getChild($entity_id);
            $this->_apiData['message'] = trans('api_errors.success');

            // assign to output
            $this->_apiData['data'] = $data;

            //Log History and save system notification
            $sys_history = new SYSEntityHistory();
            $other_data['extension_ref_table'] = 'sys_category';
            $other_data['extension_ref_id'] = $entity[$this->_entity_pk];
            $timestamp = date("Y-m-d H:i:s");
            $target_entity_id = false;
            $request_params = json_decode(json_encode($request->all()));
            $sys_history->logHistory('entity_update', $entity[$this->_entity_pk], $target_entity_id, $other_data, $timestamp, $request_params);
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
        $allowed_ordering = $allowed_searching = $this->_entity_model->primaryKey . ",parent_id,title,slug,description,created_by,created_at,status,level";
        $allowed_sorting = "asc,desc";


        // validations
        $rules = array(
            $this->_entity_pk => 'integer|exists:' . $this->_entity_model->table . "," . $this->_entity_model->primaryKey . ",deleted_at,NULL",
           'title' => 'string',
            //'slug' => "string|unique:".$this->_entity_model->table.",".$this->_entity_model->primaryKey.",NULL,deleted_at,parent_id,".$request->parent_id,
           // 'description' => 'string',
           // 'created_by' => 'integer:exists:' . $exModel->table . "," . $exModel->primaryKey . ",deleted_at,NULL",
            "is_featured"=>"integer",
			"category_type"=>"integer",
			"level" => "integer",
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
            $query->whereNull("deleted_at"); // exclude deleted
           
			if($request->input("level"))
			{
				$query->where('level',"=",$request->input("level",""));
		
			}
			if($request->input("is_featured"))
			{
				$query->where('is_featured',"=",$request->input("is_featured",""));
		
			}
			if($request->input("category_type"))
			{
				$query->where('category_type',"=",$request->input("category_type",""));
		
			}
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
           // if($request->input("level", "")) $query->where('level',"=",$request->input("level",""));

            //$raw_records = $query->select(explode(",", $allowed_ordering))->get();
            $raw_records = $query->get();

            $status = isset($request->status) ? $request->status : false;

            // set records
            if (isset($raw_records[0])) {
                //var_dump($raw_records); exit;
                foreach ($raw_records as $raw_record) {
                    //$record = $raw_record;
                    $record = $this->_entity_model->getData($raw_record->{$this->_entity_model->primaryKey},$request->all(),$status);
                    //$record->child = $this->_entity_model->getChild($raw_record->{$this->_entity_model->primaryKey});
                    /* Get attachment data*/
                    $gallery = $this->_PLAttachment->getAttachmentByEntityID($raw_record->{$this->_entity_model->primaryKey});
                    $record->image = (object)array();
                    if(count($gallery) >0){

                        $data_packet = json_decode($gallery[0]->data_packet,true);

                        $record->image->attachment_id = $gallery[0]->attachment_id;
                        $record->image->title = $gallery[0]->title;
                        $record->image->file = $gallery[0]->file;
                        $record->image->thumb = $gallery[0]->thumb;
                        $record->image->compressed_file = $gallery[0]->compressed_file;
                        $record->image->mobile_file = $gallery[0]->mobile_file;
                        $record->image->size = $data_packet['size'];
                    }

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
            $return = $data;

            //list data if hooks are entity type
            if(isset($request->list_hook)){
                $hooks = explode(',', $request->list_hook);
                if(count($hooks) > 0){

                    unset($return);
                    $return['category'] = $data;
                    $limit = isset($request->list_hook_limit) ? $request->list_hook_limit : 5;

                    foreach($hooks as $hook){
                        $return[$hook] = $exModel->listHookData($request,$hook,$limit);
                    }
                }
            }

            // assign to output
            $this->_apiData['data'] = $return;
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
        $fix_indexes = array($this->_entity_pk, "target_entity_id", "created_by");
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

}