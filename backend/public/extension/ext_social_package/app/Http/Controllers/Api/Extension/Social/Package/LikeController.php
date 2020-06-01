<?php
namespace App\Http\Controllers\Api\Extension\Social\Package;

use App\Http\Controllers\Controller;
use App\Libraries\CustomHelper;
use Illuminate\Http\Request;
use View;
use Validator;
// load models
use App\Http\Models\ApiMethod;
use App\Http\Models\ApiMethodField;
use App\Http\Models\ApiUser;
use App\Http\Models\Conf;

//use Twilio;

class LikeController extends Controller
{
    protected $_assignData = array(
        'p_dir' => '',
        'dir' => DIR_API
    );
    protected $_apiData = array();
    protected $_layout = "";
    protected $_models = array();
    protected $_jsonData = array();
    private $_mobileJSON = false;
    private $_pModelPath = "Extension\\Social\\";
    protected $_entityModel = "SYSEntity";
    protected $_eHistoryModel = "SYSEntityHistory";
    // system
    private $_entityTypeModel = "SYSEntityType";
    private $_extStatModel = "SYSExtensionStat";
    private $_eTypeExtMapModel = "SYSEntityTypeExtensionMap";
    // extension
    private $_extMapData; // extension mapping data
    private $_activityTypeData; // activity type data
    private $_extIdentifier = "ext_social_package";
    private $_activityIdentifier = "like";
    private $_activityTypeModel = "ExtActivityType"; // extension required model
    private $_pModel = "ExtPackageLike"; // extension model
    private $_pHook = "ExtPackageLike"; // extension hook


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        // set extension path
        $this->_pModelPath = $this->_modelPath . $this->_pModelPath;
        // load extension model
        $this->_pModel = $this->_pModelPath . $this->_pModel;
        $this->_pModel = new $this->_pModel;
        // load extension map model
        $this->_eTypeExtMapModel = $this->_modelPath . $this->_eTypeExtMapModel;
        $this->_eTypeExtMapModel = new $this->_eTypeExtMapModel;

        // load activity model
        $this->_activityTypeModel = $this->_pModelPath . $this->_activityTypeModel;
        $this->_activityTypeModel = new $this->_activityTypeModel;

        // error response by default
        $this->_apiData['kick_user'] = 0;
        $this->_apiData['response'] = "error";

        // check access before proceeding, (get extension ID on access)
        $this->_extMapData = $this->_eTypeExtMapModel->checkAPIAccess(
            $this->_extIdentifier,
            $request
        );

        // save activity type data
        $this->_activityTypeData = $this->_activityTypeModel->getBy('identifier', $this->_activityIdentifier);

        // init models
        $this->__models['conf_model'] = new Conf;

        $this->_entityTypeModel = $this->_modelPath . $this->_entityTypeModel;
        $this->_entityTypeModel = new $this->_entityTypeModel;

        $this->_extStatModel = $this->_modelPath . $this->_extStatModel;
        $this->_extStatModel = new $this->_extStatModel;

        // handle entity type id vs identifier
        // - actor entity type ID
        if (!is_numeric(trim($request->{"actor_" . $this->_entityTypeModel->primaryKey}))) {
            $et_data = $this->_entityTypeModel->getBy("identifier", trim($request->{"actor_" . $this->_entityTypeModel->primaryKey}));
            // assign to request
            $et_id = isset($et_data->{$this->_entityTypeModel->primaryKey}) ?
                $et_data->{$this->_entityTypeModel->primaryKey} : 0;
            $request->merge(array("actor_" . $this->_entityTypeModel->primaryKey => $et_id));
            unset($et_id, $et_data);
        }
        // - target entity type ID
        if (!is_numeric(trim($request->{"target_" . $this->_entityTypeModel->primaryKey}))) {
            $et_data = $this->_entityTypeModel->getBy("identifier", trim($request->{"target_" . $this->_entityTypeModel->primaryKey}));
            // assign to request
            $et_id = isset($et_data->{$this->_entityTypeModel->primaryKey}) ?
                $et_data->{$this->_entityTypeModel->primaryKey} : 0;
            $request->merge(array("target_" . $this->_entityTypeModel->primaryKey => $et_id));
            unset($et_id, $et_data);
        }

        $this->_entityModel = $this->_modelPath . $this->_entityModel;
        $this->_entityModel = new $this->_entityModel;

        $this->_mobileJSON = (isset($request->mobile_json)) ? true : false;
        CustomHelper::$mobileJson = $this->_mobileJSON; // set to helper var

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
     * post
     *
     * @return Response
     */
    public function post(Request $request)
    {
        // trim/escape all
        $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));

        // optional fields
        $optionalFields = array();

        // validations
        $validator = Validator::make($request->all(), array(
            // entity vs entity type
            'target_' . $this->_entityModel->primaryKey => 'required|int|exists:' . $this->_entityModel->table . "," . $this->_entityModel->primaryKey . "," . $this->_entityTypeModel->primaryKey . "," . $request->input("target_" . $this->_entityTypeModel->primaryKey),
            // actory entity vs actor entity type
            'actor_' . $this->_entityModel->primaryKey => 'required|int|exists:' . $this->_entityModel->table . "," . $this->_entityModel->primaryKey . "," . $this->_entityTypeModel->primaryKey . "," . $request->input("actor_" . $this->_entityTypeModel->primaryKey),
            // switch (like/unlike)
            "switch" => "int|in:1,0",
            "type" => "string|in:public,private"
        ));

        // set
        $request->switch = $request->input("switch", "") == "" ? 1 : intval($request->input("switch", ""));
        $request->switch = $request->switch > 0 ? 1 : 0; // default : 1
        $request->type = trim($request->input("type", "")) == "" ? 'public' : $request->input("type", "public");
        // get record
        $entity_type = $this->_entityTypeModel->get($request->{'target_' . $this->_entityTypeModel->primaryKey});

        // check existence
        $row_type_exists = $this->_pModel
            ->where('target_' . $this->_entityModel->primaryKey, "=", $request->{'target_' . $this->_entityModel->primaryKey})
            ->where("actor_" . $this->_entityModel->primaryKey, "=", $request->{"actor_" . $this->_entityModel->primaryKey})
            ->where('type', '=', $request->type)
            ->where($this->_activityTypeModel->primaryKey, '=', $this->_activityTypeData->{$this->_activityTypeModel->primaryKey})
            ->where($this->_eTypeExtMapModel->primaryKey, $this->_extMapData->{$this->_eTypeExtMapModel->primaryKey})// only data for this extension mapping
            ->whereNull('deleted_at')
            ->get();
        $id = isset($row_type_exists[0]) ? $row_type_exists[0]->{$this->_pModel->primaryKey} : 0;

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } elseif ($request->switch > 0 && $id > 0) {
            $this->_apiData['message'] = (
            $request->type == 'public' ?
                trans($this->_extIdentifier . '.already_liked', array("entity" => $entity_type->identifier)) :
                trans($this->_extIdentifier . '.already_marked_fav', array("entity" => $entity_type->identifier))
            );
        } elseif ($request->switch == 0 && $id == 0) {
            $this->_apiData['message'] = (
            $request->type == 'public' ?
                trans($this->_extIdentifier . '.have_not_liked', array("entity" => $entity_type->identifier)) :
                trans($this->_extIdentifier . '.have_not_marked_fav', array("entity" => $entity_type->identifier))
            );
        } else {
            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();

            // init data
            $entity = array();
            $timestamp = date("Y-m-d H:i:s");

            // remove if exists
            if ($id > 0) {
                // remove data
                $this->_pModel->removeData($this->_extMapData, $id, $timestamp);

                // api msg
                $this->_apiData['message'] = ($request->type == 'public' ?
                    trans($this->_extIdentifier . '.entity_unlike_success', array("entity" => $entity_type->identifier)) :
                    trans($this->_extIdentifier . '.entity_unmark_fav_success', array("entity" => $entity_type->identifier))
                );

            } else {
                // prepare data
                $save = array(
                    'target_' . $this->_entityModel->primaryKey => $request->{'target_' . $this->_entityModel->primaryKey},
                    'actor_' . $this->_entityModel->primaryKey => $request->{'actor_' . $this->_entityModel->primaryKey},
                    'type' => $request->type,
                    $this->_activityTypeModel->primaryKey => $this->_activityTypeData->{$this->_activityTypeModel->primaryKey},
                    "created_at" => $timestamp
                );
                // insert
                $id = $this->_pModel->saveData($this->_extMapData, $save, $timestamp);

                // api msg
                $this->_apiData['message'] = ($request->type == 'public' ?
                    trans($this->_extIdentifier . '.entity_like_success', array("entity" => $entity_type->identifier)) :
                    trans($this->_extIdentifier . '.entity_mark_fav_success', array("entity" => $entity_type->identifier))
                );
            }

            // set current actor
            $this->_pModel->currentActorID = $request->{'actor_' . $this->_entityModel->primaryKey};

            // get data
            $entity = $this->_pModel->getData($id);

            // log history STARTS
            // - init model
            $this->_eHistoryModel = $this->_modelPath . $this->_eHistoryModel;
            $this->_eHistoryModel = new $this->_eHistoryModel;
            // - identifier
            $h_identifier = $request->switch > 0 ?
                $this->_pModel->objectIdentifier . '_add' : $this->_pModel->objectIdentifier . '_delete';
            // - log
            $other_data = array(
                "extension_ref_table" => $this->_pModel->table,
                "extension_ref_id" => $id,
                "against_entity_type_id" => $this->_extMapData->data_entity_type_id,
                "against_entity_id" => $entity->{'data_' . $this->_entityModel->primaryKey},
            );
            $this->_eHistoryModel->logHistory($h_identifier,
                $request->{'target_' . $this->_entityModel->primaryKey},
                $request->{'actor_' . $this->_entityModel->primaryKey},
                $other_data,
                $timestamp,
                $request->all()
            );
            // log history ENDS


            // response data
            $data[$this->_pModel->objectIdentifier] = $entity;

            // assign to output
            $this->_apiData['data'] = $data;

        }

        // call hook
        $this->_apiData = CustomHelper::hookData($this->_pHook, __FUNCTION__, $request, $this->_apiData);

        return $this->__ApiResponse($request, $this->_apiData);

    }


    /**
     * listing
     *
     * @return Response
     */
    public function listing(Request $request)
    {
        // trim/escape all
        $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));

        // validations
        $validator = Validator::make($request->all(), array(
            // entity vs entity type
            'target_' . $this->_entityModel->primaryKey => 'int|exists:' . $this->_entityModel->table . ',' . $this->_entityModel->primaryKey . ',' . $this->_entityTypeModel->primaryKey . ',' . $request->input('target_' . $this->_entityTypeModel->primaryKey),
            'type' => 'string|in:public,private',
            'actor_' . $this->_entityModel->primaryKey => 'int|exists:' . $this->_entityModel->table . ',' . $this->_entityModel->primaryKey . ',' . $this->_entityTypeModel->primaryKey . ',' . $request->input('actor_' . $this->_entityTypeModel->primaryKey),
        ));

        $request->type = trim($request->input("type", "")) == "" ? 'public' : $request->input("type", "public");

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else if ($request->type == 'public' && intval($request->input('target_' . $this->_entityModel->primaryKey, 0)) == 0) {
            $this->_apiData['message'] = trans($this->_extIdentifier . '.target_entity_required');
        } else if ($request->type == 'private' && intval($request->input('actor_' . $this->_entityModel->primaryKey, 0)) == 0) {
            $this->_apiData['message'] = trans($this->_extIdentifier . '.actor_entity_required');
        } else {
            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();

            // defaults
            $data[$this->_pModel->objectIdentifier . "_listing"] = array();

            // allowed ordering/sorting
            $allowed_sorting = "desc,asc";
            $allowed_ordering = $this->_pModel->primaryKey . ",created_at";
            $primary_alias = 'a.';

            // sorting defaults
            $request->order_by = $request->input("order_by", "") == "" ? explode(",", $allowed_ordering)[0] : $request->order_by;
            $request->sorting = $request->input("sorting", "") == "" ? explode(",", $allowed_sorting)[0] : $request->sorting;
            $request->sorting = strtolower($request->sorting);


            // make query
            $query = $this->_pModel->select($primary_alias . $this->_pModel->primaryKey)
                ->from($this->_pModel->table . ' AS a')
                ->leftJoin($this->_entityModel->table . ' AS b', 'b.' . $this->_entityModel->primaryKey, '=', $primary_alias . 'data_' . $this->_entityModel->primaryKey)
                ->where($primary_alias . 'type', '=', $request->type)
                ->where($primary_alias . $this->_activityTypeModel->primaryKey, $this->_activityTypeData->{$this->_activityTypeModel->primaryKey})
                ->where($primary_alias . $this->_eTypeExtMapModel->primaryKey, $this->_extMapData->{$this->_eTypeExtMapModel->primaryKey})// only data for this extension mapping
                ->whereNull($primary_alias . 'deleted_at')
                ->whereNull('b.deleted_at');
            // if private, show ffavorites
            if ($request->type == 'private') {
                $query->where($primary_alias . 'actor_' . $this->_entityModel->primaryKey, $request->{'actor_' . $this->_entityModel->primaryKey});
            } else {
                $query->where($primary_alias . 'target_' . $this->_entityModel->primaryKey, $request->{'target_' . $this->_entityModel->primaryKey});
            }
            // get total
            $total_records = $query->count();


            // get paging query
            $query = $this->_pagingQuery($request, $query, $total_records, $primary_alias);

            // apply ordering
            $query->orderBy($primary_alias . $request->order_by, strtoupper($request->sorting));

            // get records
            $raw_records = $query->select($primary_alias . $this->_pModel->primaryKey)->get();

            // remove object mapping
            $raw_records = json_decode(json_encode($raw_records));

            // fetch records
            if (isset($raw_records[0])) {
                $records = array();
                // set actor ID to class
                $this->_pModel->currentActorID = $request->{'actor_' . $this->_entityModel->primaryKey};

                foreach ($raw_records as $raw_record) {
                    $records[] = $this->_pModel->getData($raw_record->{$this->_pModel->primaryKey});
                }
                // replace raw records
                $raw_records = $records;
            }

            // response data
            $data[$this->_pModel->objectIdentifier . "_listing"] = $raw_records;

            // get paging data
            $data["paging"] = $this->_paging($request, $raw_records);
            // assign to output
            $this->_apiData['data'] = $data;

        }

        // call hook
        $this->_apiData = CustomHelper::hookData($this->_pHook, __FUNCTION__, $request, $this->_apiData);

        return $this->__ApiResponse($request, $this->_apiData);

    }


    /**
     * paging query (init paging data)
     *
     * @return Response
     */
    private function _pagingQuery($request, $query, $total_records, $primary_alias = '')
    {
        // set vars
        $primary_alias = trim($primary_alias) == "" ? $primary_alias : $primary_alias . '.';
        $primary_alias = str_replace('..', '.', $primary_alias); // take care of provided param error
        // get paging/params
        $limit = trim(strip_tags($request->input('limit', "")));
        $limit = $limit == "" ? PAGE_LIMIT_API : intval($limit);
        $offset = intval($request->input("offset", 0));
        $offset = $offset < 0 ? 0 : $offset;
        $next_offset = $prev_offset = $offset; // - init


        // datatables request
        if (intval($request->input("dt_request", 0)) == 1) {
            // offfset / limits
            $offset = $offset < $total_records ? $offset : ($total_records - 1);
            $offset = $offset < 0 ? 0 : $offset;
            // next offset
            $next_offset = ($offset + $limit) > $total_records ? ($total_records - $offset) : ($offset + $limit);
            // prev offset
            $prev_offset = $offset > 0 ? ($offset - $limit) : $offset;
            $prev_offset = $prev_offset > 0 ? $prev_offset : 0;
            // apply offset
            $query->skip($offset);
        } else {
            // apply new paging offset
            if ($request->sorting == "asc") {
                $query->where($primary_alias . $this->_pModel->primaryKey, ">", $offset);
            } else {
                if ($offset > 0) {
                    $query->where($primary_alias . $this->_pModel->primaryKey, "<", $offset);
                }
            }
        }

        // apply limit
        $query->take($limit);

        // set pagination response
        $paging = array(
            "limit" => $limit,
            "offset" => $offset,
            "total_records" => $total_records,
            "next_offset" => $next_offset,
            "prev_offset" => $prev_offset
        );

        // assign to output (for internal sharing)
        $this->_apiData['data']["paging"] = $paging;

        return $query;
    }


    /**
     * paging
     *
     * @return Response
     */
    private function _paging($request, $raw_records)
    {
        // init data
        $paging = $this->_apiData['data']["paging"];


        // if not datatables request
        if (intval($request->input("dt_request", 0)) == 0) {
            $r_index = count($raw_records);
            $r_index = $r_index > 0 ? ($r_index - 1) : $r_index;
            if (count($raw_records) > 0) {
                $paging["next_offset"] = $raw_records[$r_index]->{$this->_pModel->primaryKey};
                //$prev_offset = $raw_records[0]->{$this->_pModel->primaryKey};
            }
        }

        // set pagination response
        $paging = array(
            "limit" => $paging["limit"],
            "offset" => $paging["offset"],
            "total_records" => $paging["total_records"],
            "next_offset" => $paging["next_offset"],
            "prev_offset" => intval($request->offset),
            "page_records" => count($raw_records)
        );

        // assign to output (for internal sharing)
        return $paging;
    }


}