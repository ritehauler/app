<?php
namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\Controller;
use App\Http\Models\PLAttachment;
use Illuminate\Http\Request;
use View;
use Validator;
// load models
use App\Http\Models\ApiMethod;
use App\Http\Models\ApiMethodField;
use App\Http\Models\ApiUser;
use App\Http\Models\EFEntityPlugin;
use App\Http\Models\Conf;
use App\Http\Models\SYSModule;

//use Twilio;

class AttachmentController extends Controller
{

    private $_apiData = [];
    private $_layout = "";
    private $_models = [];
    private $_jsonData = [];
    private $_model_path = "\App\Http\Models\\";
    private $_object_identifier = "sys_entity_type";
    private $_sys_entity_type_identifier = "sys_entity_type"; // usually routes path
    private $_sys_entity_type_pk = "entity_type_id";
    private $_sys_entity_type_ucfirst = "EntityType";
    private $_sys_entity_type_model = "SYSEntityType";
    private $_sys_role_model = "SYSRole";
    private $_sys_entity = "SYSEntity";
    private $_plugin_config = [];
    protected $_target_entity_identifier = "save_attachment";


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        // load sys_entity_type model
        $this->_sys_entity_type_model = $this->_model_path . $this->_sys_entity_type_model;
        $this->_sys_entity_type_model = new $this->_sys_entity_type_model;

        $this->_sys_entity_model = $this->_model_path . $this->_sys_entity;
        $this->_sys_entity_model = new $this->_sys_entity_model;

        // error response by default
        $this->_apiData['kick_user'] = 0;
        $this->_apiData['response'] = "error";

    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function index(Request $request)
    {

    }

    /**
     * attachmentTypes
     *
     * @return Response
     */
    public function attachmentTypes(Request $request)
    {
        // load models
        $pModel = $this->_model_path . "PLAttachmentType";
        $pModel = new $pModel;

        // activity reference
        $activity_reference = "attachment_types"; // table name for best referencing / navigation

        // trim/escape all
        $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));

        // param validations
        $validator = Validator::make($request->all(), [
            "order_by" => "in:title,identifier,created_at",
            "sorting" => "in:asc,desc"
        ]);

        // validations
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else {
            // success response
            $this->_apiData['response'] = "success";
            // init message
            $this->_apiData['message'] = trans('system.success');
            // init output
            $this->_apiData['data'] = $data = [];

            // other params
            $request->order_by = $request->order_by != "" ? $request->order_by : "title"; // default name
            $request->sorting = $request->sorting != "" ? strtolower($request->sorting) : "asc"; // default name

            // init record set
            $data[ $activity_reference ] = [];

            // default page_no / limit
            $page_no = $request->input('page_no', 0);
            $page_no = intval($page_no) > 0 ? intval($page_no) : 1; // set default value
            $limit = $request->input('limit', 0);
            $limit = intval($limit) > 0 ? intval($limit) : PAGE_LIMIT_API; // set default value

            // find records
            $query = $pModel->whereNull("deleted_at");
            $total_records = $query->count();

            // offfset / limits / valid pages
            $total_pages = ceil($total_records / $limit);
            $page_no = $page_no >= $total_pages ? $total_pages : $page_no;
            $page_no = $page_no <= 1 ? 1 : $page_no;
            $offset = $limit * ($page_no - 1);

            // query records
            $query = $pModel->whereNull("deleted_at");
            // limit / order
            $query->take($limit);
            $query->skip($offset);
            $query->orderBy($request->order_by, strtoupper($request->sorting));
            // get records
            $raw_records = $query->get();

            // if found
            if (isset($raw_records[0])) {
                // loop through
                foreach ($raw_records as $raw_record) {
                    $record = $pModel->getData($raw_record->{$pModel->primaryKey});
                    // set
                    $data[ $activity_reference ][] = $record;
                }
            }

            // set pagination response
            $data["page"] = [
                "current" => $page_no,
                "total" => $total_pages,
                "next" => $page_no >= $total_pages ? 0 : $page_no + 1,
                "prev" => $page_no <= 1 ? 0 : $page_no - 1
            ];

            // assign to output
            $this->_apiData['data'] = $data;
        }

        return $this->__apiResponse($request, $this->_apiData);
    }


    /**
     * saveAttachment
     *
     * @return Response
     */
    public function saveAttachment(Request $request)
    {

        // load models
       // $pModel = $this->_model_path . "PLAttachment";
        $pModel = new PLAttachment();

        $pModel2 = $this->_model_path . "PLAttachmentType";
        $pModel2 = new $pModel2;


        // history activity identifier
        $activity_identifier = "save_attachment";
        $activity_reference = $activity_navigation = "attachment"; // table name for best referencing / navigation


        $identifier = "";
        // other vars
        $file_index = "file";

        // trim/escape all
        /*$request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));*/
        $is_mobile_json = intval($request->mobile_json) > 0 ? TRUE : FALSE;
        if($is_mobile_json){
            $request->attachment_type_id = isset($request->attachment_type_id) ? $request->attachment_type_id : 8;
        }

        // param validations
        $validator = Validator::make($request->all(), [
            //"entity_type_id" => "required|exists:".$this->_sys_entity_type_model->table.",".$this->_sys_entity_type_model->primaryKey.",deleted_at,NULL",
            //"entity_id" => "required|exists:".$this->_sys_entity_model->table.",".$this->_sys_entity_model->primaryKey.",deleted_at,NULL",
            "attachment_type_id" => "required|exists:" . $pModel2->table . "," . $pModel2->primaryKey . ",deleted_at,NULL",
            //$file_index => "required"
        ]);

        if (!isset($request->entity_type_id)) $request->entity_type_id = '0';

        if (is_numeric($request->entity_type_id) && $request->entity_type_id != '0') {
            $entity_identifier = $this->_sys_entity_type_model->get($request->entity_type_id);
            $identifier = $entity_identifier->identifier;
        } elseif ($request->entity_type_id != '0' && !empty($request->entity_type_id)) {
            $entity_identifier = $this->_sys_entity_type_model->getEntityTypeByName($request->entity_type_id);
            if ($entity_identifier) {
                $request->entity_type_id = $entity_identifier->entity_type_id;
                $identifier = $entity_identifier->identifier;
            }
        } else $request->entity_type_id = '0';


        if (!isset($request->entity_id)) $request->entity_id = 0;
        if (!is_numeric($request->entity_id)) $request->entity_id = 0;

        // attachment type
        $attachment_type = $pModel2->get(@$request->attachment_type_id);

        $error_messages = array();

        //validation and error messages for images
        if ($request->attachment_type_id == 8 && !isset($request->is_image_resize)){
            $error_messages = array('dimensions' => trans('validation.dimensions', array('min_width' =>config("constants.MIN_IMAGE_WIDTH"),'min_height' =>config("constants.MIN_IMAGE_HEIGHT"))),
            );
            $rules = [
                $file_index => ['required','dimensions:min_width=300,min_height=200'],
                'extension' => 'in:' . str_replace('.', '', @strtolower($attachment_type->allowed_extensions)),

            ];
        }
        else{
            $rules = [
                $file_index => 'required',
                'extension' => 'in:' . str_replace('.', '', @strtolower($attachment_type->allowed_extensions)),

            ];
        }

        // file validation
        $file_validator = Validator::make([
            $file_index => $request->file($file_index),
            'extension' => strtolower($request->file($file_index)->getClientOriginalExtension())
        ],$rules,$error_messages);

        if ($validator->fails() && !$is_mobile_json) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else if ($attachment_type->allowed_extensions !== NULL && $file_validator->fails()) {
            $this->_apiData['message'] = $file_validator->errors()->first();
        } else {

            //check size as size check is not working in validation rules
            if ($request->attachment_type_id == 8){
                $max_size = config("constants.MAX_IMAGE_SIZE");
                $file_size = strtolower($request->file($file_index)->getClientSize());
                if($file_size > $max_size){
                    $this->_apiData['message'] = trans('system.max_size_image');
                    return $this->__apiResponse($request, $this->_apiData);
                }
            }

            // success response
            $this->_apiData['response'] = "success";
            // init message
            $this->_apiData['message'] = trans('system.success');
            // init output
            $this->_apiData['data'] = $data = [];
            $thumb_filename = "";
            $filename = "";
            $files_index = "";
            $data_packet = "";
            $file_path = "";
            // path/file name

            if ($request->attachment_type_id == 9) {
                $dir_path = config("constants.DIR_IMPORT");
            } else {
                $dir_path = config("constants.DIR_ATTACHMENT");
            }
            // $dir_path = config("constants.DIR_ATTACHMENT").(!empty($identifier)?$identifier."/":"");

            // if image not exists

            if ($request->file($file_index)) {

                $filename = $request->entity_type_id . "-" . $request->entity_id . "-" . time() . "." . $request->file($file_index)->getClientOriginalExtension();
                $files_index = $_FILES[ $file_index ]["name"];
                $data_packet = json_encode($_FILES[ $file_index ]);
                // save file in entity dir (create dir if not exists)
                if (!is_dir($dir_path)) {
                    mkdir(@$dir_path, 0777, TRUE);
                }
                $file_path = $dir_path;
                //create file
                $request->file($file_index)->move($dir_path, $filename);

                // generate thumb if column is not null
                $thumb_filename = "";
                if ($request->attachment_type_id == 8 && $attachment_type->thumb_dimension !== NULL) {
                    $thumb_prefix = config("constants.THUMB_PREFIX");
                    $thumb_filename = $thumb_prefix . $filename;
                    // generate thumb
                    $pModel->generateThumb($dir_path, $filename, $attachment_type->thumb_dimension, $thumb_prefix);
                }

                if ($request->attachment_type_id == 8){

                    //create image for mobile app
                    $mobile_image_dir = config("constants.DIR_MOBILE");
                    $mobile_prefix = config("constants.MOBILE_FILE_PREFIX");
                    $save['mobile_file']  =  $pModel->createMobileImage($dir_path, $filename,$mobile_image_dir,$mobile_prefix);

                    $compress_image_dir = config("constants.DIR_COMPRESSED");
                    $compress_prefix = config("constants.COMPRESS_PREFIX");
                    $save['compressed_file']  =  $pModel->compressImage($dir_path, $filename,$compress_image_dir,$compress_prefix);

                }

            }

            // prepare save data 'attribute_code'
            $save["attachment_type_id"] = $request->attachment_type_id;
            //$save["is_featured"] = $request->is_featured;
            if (isset($request->attribute_code) && !empty($request->attribute_code)) {
                $save["attribute_code"] = $request->attribute_code;
            }
            $save["entity_type_id"] = $request->entity_type_id;
            $save["entity_id"] = $request->entity_id;
            $save["title"] = $files_index;
            $save['content'] = NULL;
            $save['file'] = $file_path . $filename;
            //$save['file_path'] = $file_path.$filename;
            $save['thumb'] = !empty($thumb_filename) ? $file_path . $thumb_filename : "";
            $save['data_packet'] = $data_packet;
            $save['created_at'] = date('Y-m-d H:i:s');

            // Insert data
            $save[ $activity_reference . "_id" ] = $insert_id = $pModel->put($save);


            // set for history
            $activity_data = [
                "navigation_type" => $activity_navigation,
                "navigation_item_id" => $insert_id,
                "reference_module" => $activity_reference,
                "reference_id" => $insert_id,
                "against" => "user",
                "against_id" => $save["entity_id"]
            ];

            $entity_history_model = $this->_model_path . "EntityHistory";
            $pHistory = new $entity_history_model;

            /*            // put history
                        $pHistory->putEntityHistory(
                            $request->entity_type_id,
                            $request->entity_id,
                            $activity_identifier,
                            $activity_data,
                            $this->_target_entity_identifier
                        );*/

            // map saved data

            if($is_mobile_json){
                $data[$activity_reference] = $pModel->getAttachmentGallery($insert_id);
            }else{
                $data[ $activity_reference ] = $pModel->getData($insert_id, $dir_path);
            }

            // assign to output
            $this->_apiData['data'] = $data;
        }

        return $this->__apiResponse($request, $this->_apiData);
    }


    public function deleteAttachment(Request $request)
    {

        // load models
        $pModel = $this->_model_path . "PLAttachment";
        $pModel = new $pModel;

        // param validations
        $validator = Validator::make($request->all(), [
            "attachment_id" => "required|exists:" . $pModel->table . "," . $pModel->primaryKey . ",deleted_at,NULL",
        ]);
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else {
            $this->_apiData['message'] = 'success';
            $pModel->hardRemove($request->attachment_id);
        }

        return $this->__apiResponse($request, $this->_apiData);

    }


}