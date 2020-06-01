<?php
namespace App\Http\Controllers\Api\Extension\Post;

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

class PostTypeController extends Controller
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
    private $_pModelPath;
    protected $_entityModel = "SYSEntity";
    protected $_eHistoryModel = "SYSEntityHistory";
    // system
    private $_entityTypeModel = "SYSEntityType";
    private $_extStatModel = "SYSExtensionStat";
    private $_eTypeExtMapModel = "SYSEntityTypeExtensionMap";
    // extension
    private $_extMapData; // extension mapping data
    private $_extIdentifier = "ext_post";
    private $_pModel = "PostType"; // extension model
    private $_pHook = "PostType"; // extension hook


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        // set extension path
        $this->_pModelPath = $this->_modelPath . config($this->_extIdentifier . '.MODEL_PATH');
        // load extension model
        $this->_pModel = $this->_pModelPath . $this->_pModel;
        $this->_pModel = new $this->_pModel;
        // load extension map model
        $this->_eTypeExtMapModel = $this->_modelPath . $this->_eTypeExtMapModel;
        $this->_eTypeExtMapModel = new $this->_eTypeExtMapModel;

        // error response by default
        $this->_apiData['kick_user'] = 0;
        $this->_apiData['response'] = "error";

        // check access before proceeding, (get extension ID on access)
        $this->_extMapData = $this->_eTypeExtMapModel->checkAPIAccess(
            $this->_extIdentifier,
            $request
        );

        // init models
        $this->__models['conf_model'] = new Conf;

        $this->_entityTypeModel = $this->_modelPath . $this->_entityTypeModel;
        $this->_entityTypeModel = new $this->_entityTypeModel;

        $this->_extStatModel = $this->_modelPath . $this->_extStatModel;
        $this->_extStatModel = new $this->_extStatModel;

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
            /*// entity vs entity type
            'introduced_by_' . $this->_entityModel->primaryKey => 'int|exists:' . $this->_entityModel->table . ',' . $this->_entityModel->primaryKey . ',' . $this->_entityTypeModel->primaryKey . ',' . $request->input('actor_' . $this->_entityTypeModel->primaryKey) . ',allow_auth,1',*/
        ));


        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
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
            $request->order_by = $request->input("order_by", "") == "" ? explode(',', $allowed_ordering)[0] : $request->order_by;
            $request->sorting = $request->input("sorting", "") == "" ? explode(',', $allowed_sorting)[0] : $request->sorting;
            $request->sorting = strtolower($request->sorting);


            // make query
            $query = $this->_pModel->select($primary_alias . $this->_pModel->primaryKey)
                ->from($this->_pModel->table . ' AS a')
                ->whereNull($primary_alias . 'deleted_at');

            // get total
            $total_records = $query->count();


            // get paging query
            //$query = $this->_pagingQuery($request, $query, $total_records, $primary_alias);

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
            //$data["paging"] = $this->_paging($request, $raw_records);
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


    /**
     * _extraFields
     *
     * @return Response
     */
    private function _extraFields(Request $request)
    {
        // if we are saving first record, then we need to create required fields
        // in target entity type
        $total_records = $this->_pModel
            ->where($this->_eTypeExtMapModel->primaryKey, '=', $this->_extMapData->{$this->_eTypeExtMapModel->primaryKey})
            ->whereNull('deleted_at')->count();

        if ($total_records == 0) {

            // rating
            $this->_eTypeExtMapModel->generateField($request,
                'rating',
                $this->_extMapData->{'target_' . $this->_entityTypeModel->primaryKey});

            // total rating
            $this->_eTypeExtMapModel->generateField($request,
                'total_rating',
                $this->_extMapData->{'target_' . $this->_entityTypeModel->primaryKey},
                0,
                'float');

            // total raters
            $this->_eTypeExtMapModel->generateField($request,
                'total_raters',
                $this->_extMapData->{'target_' . $this->_entityTypeModel->primaryKey},
                0,
                'float');

            // average rating
            $this->_eTypeExtMapModel->generateField($request,
                'average_rating',
                $this->_extMapData->{'target_' . $this->_entityTypeModel->primaryKey},
                0,
                'float');
        }

    }


}