<?php

/**
 * Description: Import Export Library for entities
 * Author: Samreen <samreen.quyyum@cubixlabs.com>
 * Date: 02-Feb-2018
 * Time: 12:30 PM
 * Copyright: CubixLabs
 */
namespace App\Libraries;

use Maatwebsite\Excel\Facades\Excel;

use App\Http\Models\PLAttachment;

use App\Libraries\System\Entity;
use App\Libraries\EntityHelper;


Class DataImportExport
{
    /**
     * get array of values from import file
     * and then check template is valid
     * then get attributes values of different entity type
     * then save entity data
     * @param $request
     * @param $entity_type
     * @param $attributes
     * @return mixed
     */
    public function importEntityData($request, $entity_type, $attributes)
    {
        $return = [];
        try {

            $data = [];
            if (isset($request->import_file) && !empty($request->import_file)) {
                //Get file data by import file id
                $pl_attachment_model = new PLAttachment();
                $attachment = $pl_attachment_model->get($request->import_file);

                if ($attachment) {
                    $import_data = Excel::load($attachment->file)->toArray();

                    if (count($import_data) == 0) {
                        $return['message'] = trans('panel.no_data_found_for_export');
                    } else {

                        //Check if valid attributes those input in file
                        $check_file_with_template = $this->validateAttributesWithImportData($attributes, $import_data);

                        if ($check_file_with_template->error == 1) {
                            $return['message'] = $check_file_with_template->message;
                        } else {
                            $message = "";
                            //Now prepare data for import as in import file dropdown options values are given,
                            // get associated id and values of attributes
                            $records = $this->prepareDataToImport($import_data, $attributes);
                            //Save Entity Data
                            if (count($records) > 0) {
                                $save_response = $this->_createEntity($records, $entity_type->entity_type_id);
                                $data = $save_response['data'];
                                $message = $save_response['message'];
                            }

                            $return['data'] = $data;
                            $return['response'] = $return['data'] ? 'success' : $return['data'];
                            $return['message'] = $message;

                        }

                    }
                } else {
                    $return['message'] = trans('panel.no_attachment_found');
                }

            } else {
                $return['data'] = $data;
                $return['response'] = $return['data'] ? 'success' : $return['data'];
                $return['message'] = trans('panel.upload_file');
            }

        } catch (\Exception $e) {
            //  echo $e->getTraceAsString(); exit;
             $return['message'] = $e->getMessage();
            //  $return['debug'] = 'File : ' . $e->getFile() . ' : Line ' . $e->getLine(); //" : Stack " . $e->getTraceAsString();
        }

        // fix for error flags
        $return['error'] = (isset($return['response']) && $return['response'] == 'success') ? 0 : 1;

        return json_decode(json_encode($return));

    }

    /**
     * Export entity data by getting entity all data
     * if filters are applied then export data by applying filters
     * @param $entity_type
     * @param $attributes
     * @param $requested_params
     * @return mixed
     */
    public function exportEntityData($entity_type, $attributes, $requested_params)
    {
        $return = [];
        try {
            $entity_helper_lib = new EntityHelper();
            $data = $entity_helper_lib->getEntityList($entity_type, $attributes, $requested_params);

            if (count($data) > 0){
                $data = $this->_prepareDataFroExcel($data);
                $return['data'] = $data;
                $return['response'] = $return['data'] ? 'success' : $return['data'];
                $return['message'] = "Success";
            }
            else{
                $return['message'] = "No data found with search results.";
            }

        } catch (\Exception $e) {
            //  echo $e->getTraceAsString(); exit;
            $return['message'] = $e->getMessage();
            $return['debug'] = 'File : ' . $e->getFile() . ' : Line ' . $e->getLine();
        }

        // fix for error flags
        $return['error'] = (isset($return['response']) && $return['response'] == 'success') ? 0 : 1;

        return json_decode(json_encode($return));

    }

    /**
     * get values of attribute by attribute options
     * @param $records
     * @param $attributes
     * @return array
     */
    public function prepareDataToImport($records, $attributes)
    {
        if (count($records) > 0 && count($attributes) > 0) {

            $entity_helper = new EntityHelper();
            $query_records = [];
            foreach ($records as $key => $record) {
                $record = (object)$record;

                foreach ($attributes as $attribute) {
                    //Get value from option for dropdown, entity dropdown
                    $value = $entity_helper->getFieldValueFromOption($attribute, $record->{$attribute->attribute_code});
                    $record->{$attribute->attribute_code} = "$value";
                }

                $query_records[] = $record;
                //echo "<pre>"; print_r($record);
            }

            return $query_records;

        }
    }

    /**
     * Check import file attributes with entity type attributes
     * if missing attribute then return error
     * @param $attributes
     * @param $import_data
     * @return array
     */
    private function validateAttributesWithImportData($attributes, $import_data)
    {
        $entity_attribute = [];
        if (count($attributes) > 0) {
            foreach ($attributes as $attribute) {
                $entity_attribute[] = $attribute->attribute_code;
            }
        }

        $import_data_attributes = [];

        //check if import columns are same as entity attributes
        if (count($import_data) > 0) {
            foreach ($import_data as $k => $import) {

                if ($k > 0) break;
                foreach ($import as $key => $val) {

                    $import_data_attributes[] = $key;
                    if (!in_array($key, $entity_attribute)) {
                        return (object)['error' => 1, 'message' => trans('panel.template_not_valid')];
                    }

                }
            }
        }

        if (count($entity_attribute) != count($import_data_attributes)) {
            return (object)['error' => 1, 'message' => trans('panel.template_not_valid')];
        }

        return (object)['error' => 0, 'message' => "Success"];
    }

    /**
     * First Array index for title and then
     * data in other indexes
     * @param $data
     * @return array
     */
    private function _prepareDataFroExcel($data)
    {
        $return_data = [];
        foreach ($data as $k => $value) {

            $return_value = [];
            foreach ($value as $key => $val) {

                if ($k == 0) {
                    // $return_key[] = $key;
                    $return_title[] = $val['title'];
                    $return_value[] = $val['value'];
                } else
                    $return_value[] = $val['value'];
            }

            //  $return_data[0] = $return_key;
            $return_data[0] = $return_title;
            $return_data[] = $return_value;
        }

        return $return_data;
    }

    /**
     * Create Entity
     * @param $records
     * @param $entity_type_id
     * @return array
     */
    private function _createEntity($records, $entity_type_id)
    {
        $message = "";
        $data = [];

        if (count($records) > 0) {

            $entity_lib = new Entity();
            $count = 1;
            foreach ($records as $record) {
                $record->entity_type_id = $entity_type_id;
                //Post entity
                $post_response = $entity_lib->apiPost($record);
                $post_response = json_decode(json_encode($post_response));
                if ($post_response->error == 0) {
                    $data[] = $post_response->data;
                    $message .= "Record# " . $count . " saved successfully<br>";
                } else {
                    $message .= "Record# " . $count . " couldnot save, " . $post_response->message . "<br>";
                }
                $count++;
            }
        }

        return ['message' => $message, 'data' => $data];
    }

}