<?php
/**
 * Attachment library to save attachment
 */
namespace App\Libraries\System;

use App\Http\Models\PLAttachment;
use App\Http\Models\PLAttachmentType;
use App\Http\Models\SYSEntity;
use App\Http\Models\SYSEntityType;
use Illuminate\Support\Facades\Validator;

Class Attachment 
{
    public $_model = '';
    private $_defaultAttachmentType;
    private $_attachmentTypeModel;
    private $_sysEntityTypeModel;
    private $_sysEntityModel;
    private $_activityReference = 'attachment';
    
    
    public function __construct()
    {
        $this->_model = new PLAttachment();
        $this->_defaultAttachmentType = 12;
        $this->_attachmentTypeModel = new PLAttachmentType();
        $this->_sysEntityTypeModel = new SYSEntityType();
        $this->_sysEntityModel = new SYSEntity();
    }
    
    public function saveAttachment($request)
    {
        $is_mobile_json = intval($request->mobile_json) > 0 ? TRUE : FALSE;
        $request->attachment_type_id = isset($request->attachment_type_id) ? $request->attachment_type_id : $this->_defaultAttachmentType;

        $validator = Validator::make($request->all(), [
            //"entity_type_id" => "required|exists:".$this->_sys_entity_type_model->table.",".$this->_sys_entity_type_model->primaryKey.",deleted_at,NULL",
            //"entity_id" => "required|exists:".$this->_sys_entity_model->table.",".$this->_sys_entity_model->primaryKey.",deleted_at,NULL",
            "attachment_type_id" => "required|exists:" .  $this->_attachmentTypeModel->table . "," .  $this->_attachmentTypeModel->primaryKey . ",deleted_at,NULL",
            //$file_index => "required"
        ]);

        $file_index = "file";
        
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
        $attachment_type = $this->_attachmentTypeModel->get(@$request->attachment_type_id);

        $error_messages = array();

        //validation and error messages for images
        if ($request->attachment_type_id == 8){
            $error_messages = array('dimensions' => trans('validation.dimensions', array('min_width' =>config("constants.MIN_IMAGE_WIDTH"),'min_height' =>config("constants.MIN_IMAGE_HEIGHT"))),
            );
            $rules = [
                $file_index => ['required','dimensions:min_width=250,min_height=300'],
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
            $return['error'] = 1;
            $return['message'] = $validator->errors()->first();
        } else if ($attachment_type->allowed_extensions !== NULL && $file_validator->fails()) {
            $return['error'] = 1;
            $return['message'] = $file_validator->errors()->first();
        } else {

            //check size as size check is not working in validation rules
            if ($request->attachment_type_id == 8) {
                $max_size = config("constants.MAX_IMAGE_SIZE");
                $file_size = strtolower($request->file($file_index)->getClientSize());
                if ($file_size > $max_size) {
                    $return['error'] = 1;
                   return  $return['message'] = trans('api_errors.max_size_image');
                    
                }
            }

            // success response
            $return['error'] = 0;
            $return['response'] = "success";
            // init message
            $return['message'] = trans('api_errors.success');
            // init output
            $return['data'] = $data = [];
            $thumb_filename = "";
            $filename = "";
            $files_index = "";
            $data_packet = "";
            $file_path = "";
            // path/file name

            if ($request->attachment_type_id == 9) {
                $dir_path = config("constants.DIR_IMPORT");
            } else {
                if(isset($request->is_sys_attachment))
                     $dir_path = config("constants.DIR_ATTACHMENT_SYS");
                else
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
                    $this->_model->generateThumb($dir_path, $filename, $attachment_type->thumb_dimension, $thumb_prefix);
                }

                if ($request->attachment_type_id == 8) {

                    //create image for mobile app
                    if(isset($request->is_sys_attachment)){
                        $mobile_image_dir = config("constants.DIR_MOBILE_SYS");
                        $compress_image_dir = config("constants.DIR_COMPRESSED_SYS");
                    }else{
                        $mobile_image_dir = config("constants.DIR_MOBILE");
                        $compress_image_dir = config("constants.DIR_COMPRESSED");
                    }

                    $mobile_prefix = config("constants.MOBILE_FILE_PREFIX");
                    $save['mobile_file'] =  $this->_model->createMobileImage($dir_path, $filename, $mobile_image_dir, $mobile_prefix);


                    $compress_prefix = config("constants.COMPRESS_PREFIX");
                    $save['compressed_file'] =  $this->_model->compressImage($dir_path, $filename, $compress_image_dir, $compress_prefix);

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
            $save[ $this->_activityReference . "_id" ] = $insert_id =  $this->_model->put($save);

            // map saved data

            if ($is_mobile_json) {
                $data[ $this->_activityReference ] =  $this->_model->getAttachmentGallery($insert_id);
            } else {
                $data[ $this->_activityReference ] =  $this->_model->getData($insert_id, $dir_path);
            }

            $return['data'] = $data;
        }

        return $return;
        
    }
    
    
}