<?php
namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\Controller;
use App\Libraries\CustomHelper;
use Illuminate\Http\Request;
use Mockery\CountValidator\Exception;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use View;
use Validator;
// load models
use App\Http\Models\ApiMethod;
use App\Http\Models\ApiMethodField;
use App\Http\Models\ApiUser;
use App\Http\Models\Conf;

//use Twilio;

class ExtensionController extends Controller
{
    protected $_assignData = array(
        'p_dir' => '',
        'dir' => DIR_API
    );
    protected $_apiData = array();
    protected $_layout = "";
    protected $_models = array();
    protected $_jsonData = array();
    protected $_entityUcFirst;
    protected $_entityRoleMapModel;
    protected $_entityConfFile;
    //protected $_entity_id = "1";
    protected $_plugin_identifier = NULL;
    protected $_plugin_config = array();
    private $_mobile_json = false;
    private $_entityTypeData = NULL;
    protected $_objectIdentifier = 'entity_type_extension';
    protected $_entityIdentifier = 'entity_type_extension';
    private $_entityTypeModel = "SYSEntityType";
    protected $_pModel = 'SYSExtension';
    protected $_entityModel = 'SYSEntity';
    protected $_targetEntityModel = 'SYSEntityTypeExtensionMap';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        // load entity model
        // get all webservices data
        //$this->__models['entity_plugin_model'] = new EFEntityPlugin;

        // init models
        $this->__models['api_method_model'] = new ApiMethod;
        $this->__models['api_user_model'] = new ApiUser;
        $this->__models['conf_model'] = new Conf;

        $this->_entityTypeModel = $this->_modelPath . $this->_entityTypeModel;
        $this->_entityTypeModel = new $this->_entityTypeModel;

        $this->_pModel = $this->_modelPath . $this->_pModel;
        $this->_pModel = new $this->_pModel;

        $this->_entityModel = $this->_modelPath . $this->_entityModel;
        $this->_entityModel = new $this->_entityModel;

        $this->_targetEntityModel = $this->_modelPath . $this->_targetEntityModel;
        $this->_targetEntityModel = new $this->_targetEntityModel;


        $this->_mobile_json = (isset($request->mobile_json)) ? true : false;

        // handle entity type id vs identifier
        // - actor entity type ID
        if (!is_numeric(trim($request->{'actor_' . $this->_entityTypeModel->primaryKey}))) {
            $et_data = $this->_entityTypeModel->getBy("identifier", trim($request->{'actor_' . $this->_entityTypeModel->primaryKey}));
            // assign to request
            $et_id = isset($et_data->{$this->_entityTypeModel->primaryKey}) ?
                $et_data->{$this->_entityTypeModel->primaryKey} : 0;
            $request->merge(array('actor_' . $this->_entityTypeModel->primaryKey => $et_id));
            unset($et_id, $et_data);
        }
        // - target entity type ID
        if (!is_numeric(trim($request->{"target_" . $this->_entityTypeModel->primaryKey}))) {
            $et_data = $this->_entityTypeModel->getBy("identifier", trim($request->{'actor_' . $this->_entityTypeModel->primaryKey}));
            // assign to request
            $et_id = isset($et_data->{$this->_entityTypeModel->primaryKey}) ?
                $et_data->{$this->_entityTypeModel->primaryKey} : 0;
            $request->merge(array("target_" . $this->_entityTypeModel->primaryKey => $et_id));
            unset($et_id, $et_data);
        }
        // - data_ entity type ID
        if (isset($request->{'data_' . $this->_entityTypeModel->primaryKey}) && !is_numeric(trim($request->{'data_' . $this->_entityTypeModel->primaryKey}))) {
            $et_data = $this->_entityTypeModel->getBy("identifier", trim($request->{'actor_' . $this->_entityTypeModel->primaryKey}));
            // assign to request
            $et_id = isset($et_data->{$this->_entityTypeModel->primaryKey}) ?
                $et_data->{$this->_entityTypeModel->primaryKey} : 0;
            $request->merge(array('data_' . $this->_entityTypeModel->primaryKey => $et_id));
            unset($et_id, $et_data);
        }

        if ($this->_mobile_json) {
            $this->_objectIdentifier = isset($this->_entityTypeData->identifier) ?
                $this->_entityTypeData->identifier : "extension"; // default
        }

        $this->_pModel->_mobile_json = $this->_mobile_json;

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
     * Install Extension
     *
     * @return Response
     */
    public function install(Request $request)
    {
        // trim/escape all
        $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));

        // optional fields
        $optionalFields = array();


        // defaults
        $prevent_process = 0;

        // validations
        $validator = Validator::make($request->all(), array(
            //"plugin" => 'required|string|unique:' . $this->_pModel->table . ",identifier,NULL,deleted_at"
            "plugin" => 'required|string'
        ));

        // check existence
        $row_type_exists = $this->_pModel
            ->where("identifier", '=', $request->{"plugin"})
            ->whereNull('deleted_at')
            ->get();

        $exists_id = isset($row_type_exists[0]) ? $row_type_exists[0]->{$this->_pModel->primaryKey} : 0;

        // check extension exists
        $check_plugin = $this->_pModel->checkExists($request->plugin);

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } elseif (!$check_plugin) {
            $this->_apiData['message'] = trans('api_errors.invalid_entity_request', array("entity" => $this->_pModel->table));
        } else if ($exists_id > 0) {
            $this->_apiData['message'] = trans('api_errors.entity_already_exists', array("entity" => $this->_pModel->table));
        } else {
            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();

            // init data
            $entity = array();
            // install
            $this->_pModel->install(trim($request->plugin));


            // insert
            $record = $this->_pModel->getBy("identifier", $request->plugin, TRUE);

            if ($record) {
                // get
                $entity = $this->_pModel->getData($record->{$this->_pModel->primaryKey});
            }

            // response data
            $data[$this->_objectIdentifier] = $entity;

            // assign to output
            $this->_apiData['data'] = $data;

        }

        return $this->__ApiResponse($request, $this->_apiData);
    }


    /**
     * Install Extension
     *
     * @return Response
     */
    public function unInstall(Request $request)
    {
        // trim/escape all
        $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));

        // optional fields
        $optionalFields = array();


        // defaults
        $prevent_process = 0;

        // validations
        $validator = Validator::make($request->all(), array(
            "plugin" => 'required|string|exists:' . $this->_pModel->table . ",identifier"
        ));

        // check existence
        $row_type_exists = $this->_pModel
            ->where("identifier", '=', $request->{"plugin"})
            ->whereNull('deleted_at')
            ->get();

        $exists_id = isset($row_type_exists[0]) ? $row_type_exists[0]->{$this->_pModel->primaryKey} : 0;


        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else if ($exists_id == 0) {
            $this->_apiData['message'] = trans('api_errors.invalid_entity_request', array("entity" => $this->_pModel->table));
        } else {
            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();

            // init data
            $entity = array();
            // install
            $this->_pModel->unInstall(trim($request->plugin));


            // insert
            $record = $this->_pModel->get($exists_id);

            if ($record) {
                // remove
                $this->_pModel->remove($record->{$this->_pModel->primaryKey});
                // get
                $entity = $this->_pModel->getData($record->{$this->_pModel->primaryKey});
            }

            // response data
            $data[$this->_objectIdentifier] = $entity;

            // assign to output
            $this->_apiData['data'] = $data;

        }

        return $this->__ApiResponse($request, $this->_apiData);
    }


    /**
     * Assign Extension
     *
     * @return Response
     */
    public function assign(Request $request)
    {
        // trim/escape all
        $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));

        // optional fields
        $optionalFields = array(
            //"entity_type_id",
            //"extension_id",
            "data_entity_type_id",
            "aggregate_field",
            "listing_attributes",
            "search_attributes",
            "create_attributes",
            "update_attributes",
            "configuration",
        );


        // defaults
        $prevent_process = 0;

        // validations
        $rules = array(
            'actor_' . $this->_entityTypeModel->primaryKey => 'required|integer|exists:' . $this->_entityTypeModel->table . ',' . $this->_entityTypeModel->primaryKey . ",allow_auth,1,deleted_at,NULL",
            "target_" . $this->_entityTypeModel->primaryKey => 'required|integer|exists:' . $this->_entityTypeModel->table . ',' . $this->_entityTypeModel->primaryKey . ",deleted_at,NULL",
        );
        // if data entity type exists
        if ($request->input('data_' . $this->_entityTypeModel->primaryKey) > 0) {
            $rules['data_' . $this->_entityTypeModel->primaryKey] = 'filled|integer|exists:' . $this->_entityTypeModel->table . ',' . $this->_entityTypeModel->primaryKey . ",deleted_at,NULL";
        }
        $rules = array_merge($rules, array(
            $this->_pModel->primaryKey => 'required|int|exists:' . $this->_pModel->table . ',' . $this->_pModel->primaryKey . ',is_required_assigning,1,deleted_at,NULL',
            'listing_attributes' => 'string',
            'search_attributes' => 'string',
            'create_attributes' => 'string',
            "update_attributes" => 'string',
            "configuration" => 'json',
        ));
        $validator = Validator::make($request->all(), $rules);

        // check existence
        $query = $this->_targetEntityModel
            ->where('target_' . $this->_entityTypeModel->primaryKey, '=', $request->{'target_' . $this->_entityTypeModel->primaryKey})
            ->where('actor_' . $this->_entityTypeModel->primaryKey, '=', $request->{'actor_' . $this->_entityTypeModel->primaryKey})
            ->where($this->_pModel->primaryKey, '=', $request->{$this->_pModel->primaryKey})
            ->whereNull('deleted_at');
        // if got data entity
        if ($request->input('data_' . $this->_entityTypeModel->primaryKey, null) != null) {
            $query->where('data_' . $this->_entityTypeModel->primaryKey, '=', $request->{'data_' . $this->_entityTypeModel->primaryKey});
        } else {
            $query->whereNull('data_' . $this->_entityTypeModel->primaryKey);
        }
        $row_type_exists = $query->get();

        $exists_id = isset($row_type_exists[0]) ? $row_type_exists[0]->{$this->_targetEntityModel->primaryKey} : 0;


        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else if ($exists_id > 0) {
            $this->_apiData['message'] = trans('api_errors.entity_already_exists', array("entity" => $this->_pModel->table));
        } else {
            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();

            // init data
            $entity = array();
            // set data
            // optional params if available
            if (isset($optionalFields[0])) {
                foreach ($optionalFields as $optionalField) {
                    if ($request->input($optionalField, "") != "") {
                        $entity[$optionalField] = $request->{$optionalField};
                    }
                }
            }

            // set prefix to aggregate field if exists
            if (isset($entity['aggregate_field']) && !preg_match('/^(ext_)/', $entity['aggregate_field'])) {
                $entity['aggregate_field'] = 'ext_' . $entity['aggregate_field'];
            }

            // required params
            $entity["target_" . $this->_entityTypeModel->primaryKey] = $request->{"target_" . $this->_entityTypeModel->primaryKey};
            $entity['actor_' . $this->_entityTypeModel->primaryKey] = $request->{'actor_' . $this->_entityTypeModel->primaryKey};
            $entity[$this->_pModel->primaryKey] = $request->{$this->_pModel->primaryKey};
            $entity['created_at'] = date("Y-m-d H:i:s");

            // insert
            $id = $this->_targetEntityModel->put($entity);


            // generate aggregate attribute
            if (isset($entity['aggregate_field'])) {
                $this->_pModel->generateField($request, $entity['aggregate_field']);
            }
            // generate other attributes for target entity (not needed)
            /*// - target entity id
            $this->_pModel->generateField($request, 'target_'.$this->_entityModel->primaryKey);
            // - actor entity id
            $this->_pModel->generateField($request, 'actor_'.$this->_entityModel->primaryKey);*/

            // generate fields for data entity if exists
            if (intval($request->data_entity_type_id) > 0) {
                // - target entity id
                $this->_pModel->generateField($request, 'target_' . $this->_entityModel->primaryKey, $request->data_entity_type_id);
                // - actor entity id
                $this->_pModel->generateField($request, 'actor_' . $this->_entityModel->primaryKey, $request->data_entity_type_id);
            }

            //try{
                $m_record = $this->_targetEntityModel->get($id);
                $record = $this->_pModel->get($m_record->{$this->_pModel->primaryKey});
                $config = json_decode($record->schema_json);
                $params = array(
                    $this->_targetEntityModel->primaryKey => $id
                );
                CustomHelper::appCall($request, \URL::to(DIR_API) . '/' . $config->api_base_route . '/core/install', 'POST', $params);
            /*} catch(NotFoundHttpException $e) {
                // route not found
            } catch(FatalThrowableError $e2) {
                // fatal error in call
            }*/


            // get
            $entity = $this->_targetEntityModel->getData($id);

            // response data
            $data[$this->_objectIdentifier] = $entity;

            // assign to output
            $this->_apiData['data'] = $data;

        }

        return $this->__ApiResponse($request, $this->_apiData);
    }


    /**
     * Un-assign Extension
     *
     * @return Response
     */
    public function unAssign(Request $request)
    {
        // trim/escape all
        $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));

        // defaults
        $prevent_process = 0;

        // validations
        $rules = array(
            'actor_' . $this->_entityTypeModel->primaryKey => 'required|integer|exists:' . $this->_entityTypeModel->table . ',' . $this->_entityTypeModel->primaryKey . ",allow_auth,1,deleted_at,NULL",
            "target_" . $this->_entityTypeModel->primaryKey => 'required|integer|exists:' . $this->_entityTypeModel->table . ',' . $this->_entityTypeModel->primaryKey . ",deleted_at,NULL",
        );
        // if data entity type exists
        if ($request->input('data_' . $this->_entityTypeModel->primaryKey) > 0) {
            $rules['data_' . $this->_entityTypeModel->primaryKey] = 'filled|integer|exists:' . $this->_entityTypeModel->table . ',' . $this->_entityTypeModel->primaryKey . ",deleted_at,NULL";
        }

        $rules[$this->_pModel->primaryKey] = 'required|int|exists:' . $this->_pModel->table . ',' . $this->_pModel->primaryKey . ',is_required_assigning,1,deleted_at,NULL';


        // validations
        $validator = Validator::make($request->all(), $rules);

        // check existence
        $query = $this->_targetEntityModel
            ->where('target_' . $this->_entityTypeModel->primaryKey, '=', $request->{'target_' . $this->_entityTypeModel->primaryKey})
            ->where('actor_' . $this->_entityTypeModel->primaryKey, '=', $request->{'actor_' . $this->_entityTypeModel->primaryKey})
            ->where($this->_pModel->primaryKey, '=', $request->{$this->_pModel->primaryKey})
            ->whereNull('deleted_at');
        // if got data entity
        if ($request->input('data_' . $this->_entityTypeModel->primaryKey, null) != null) {
            $query->where('data_' . $this->_entityTypeModel->primaryKey, '=', $request->{'data_' . $this->_entityTypeModel->primaryKey});
        } else {
            $query->whereNull('data_' . $this->_entityTypeModel->primaryKey);
        }
        $row_type_exists = $query->get();

        $exists_id = isset($row_type_exists[0]) ? $row_type_exists[0]->{$this->_targetEntityModel->primaryKey} : 0;

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else if ($exists_id == 0) {
            $this->_apiData['message'] = trans('api_errors.invalid_entity_request', array("entity" => $this->_pModel->table));
        } else {
            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();

            // parse un-installer inside extension
            try {
                $m_record = $this->_targetEntityModel->get($exists_id);
                $record = $this->_pModel->get($m_record->{$this->_pModel->primaryKey});
                $config = json_decode($record->schema_json);
                $params = array(
                    $this->_targetEntityModel->primaryKey => $exists_id,
                    $this->_pModel->primaryKey => $request->{$this->_pModel->primaryKey}
                );
                CustomHelper::appCall($request, \URL::to(DIR_API) . '/' . $config->api_base_route . '/core/uninstall', 'POST', $params);
            } catch (Exception $e) {

            }

            // remove
            $id = $this->_targetEntityModel->remove($exists_id);

            // get
            $entity = $this->_targetEntityModel->getData($exists_id);

            // message
            $this->_apiData['message'] = trans('entity_successfully_removed');

            // response data
            $data[$this->_objectIdentifier] = $entity;

            // assign to output
            $this->_apiData['data'] = $data;

        }

        return $this->__ApiResponse($request, $this->_apiData);
    }

}

