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

class CoreController extends Controller
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
    private $_extIdentifier = "ext_social_package";
    private $_pModel = "ExtSocialActivity"; // extension model
    private $_pHook = "ExtSocialActivity"; // extension hook


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

        // error response by default
        $this->_apiData['kick_user'] = 0;
        $this->_apiData['response'] = "error";

        // init models
        $this->__models['conf_model'] = new Conf;

        $this->_entityTypeModel = $this->_modelPath . $this->_entityTypeModel;
        $this->_entityTypeModel = new $this->_entityTypeModel;

        $this->_extStatModel = $this->_modelPath . $this->_extStatModel;
        $this->_extStatModel = new $this->_extStatModel;

        // get ext map data
        $this->_extMapData = $this->_eTypeExtMapModel->get($request->{$this->_eTypeExtMapModel->primaryKey});

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
     * _extraFields
     *
     * @return Response
     */
    private function _generateFields(Request $request)
    {
        // if we are saving first record, then we need to create required fields
        // in target entity type
        /*$total_records = $this->_pModel
            ->where($this->_eTypeExtMapModel->primaryKey, '=', $this->_extMapData->{$this->_eTypeExtMapModel->primaryKey})
            ->whereNull('deleted_at')->count();*/

        $total_records = 0;
        if ($total_records == 0) {
            // LIKE / FavoriteFields
            // - count like
            $this->_eTypeExtMapModel->generateField($request,
                'ext_count_like',
                $this->_extMapData->{'target_' . $this->_entityTypeModel->primaryKey});
            // - count favorite
            $this->_eTypeExtMapModel->generateField($request,
                'ext_count_favorite',
                $this->_extMapData->{'target_' . $this->_entityTypeModel->primaryKey});


            // - if target and actor entity_id is not same, then we need to create fields
            // - for actor entity as-well
            if ($this->_extMapData->{'target_' . $this->_entityTypeModel->primaryKey} != $this->_extMapData->{'actor_' . $this->_entityTypeModel->primaryKey}) {
                // LIKE / Favorite
                // - count favorite (actor)
                $this->_eTypeExtMapModel->generateField($request,
                    'ext_favorite_items',
                    $this->_extMapData->{'actor_' . $this->_entityTypeModel->primaryKey});

                /*// friends_accepted
                $this->_eTypeExtMapModel->generateField($request,
                    'friend_request_accepted',
                    $this->_extMapData->{'actor_' . $this->_entityTypeModel->primaryKey});
                // friends_received
                $this->_eTypeExtMapModel->generateField($request,
                    'friend_request_received',
                    $this->_extMapData->{'actor_' . $this->_entityTypeModel->primaryKey});
                // friends_sent
                $this->_eTypeExtMapModel->generateField($request,
                    'friend_request_sent',
                    $this->_extMapData->{'actor_' . $this->_entityTypeModel->primaryKey});*/
            }

            // Rate Fields
            // - rating
            $this->_eTypeExtMapModel->generateField($request,
                'ext_rating',
                $this->_extMapData->{'target_' . $this->_entityTypeModel->primaryKey},0,'price');

            // - total rating
            $this->_eTypeExtMapModel->generateField($request,
                'ext_total_rating',
                $this->_extMapData->{'target_' . $this->_entityTypeModel->primaryKey},
                0,
                'float');

            // - total raters
            $this->_eTypeExtMapModel->generateField($request,
                'ext_total_raters',
                $this->_extMapData->{'target_' . $this->_entityTypeModel->primaryKey},
                0,
                'float');

            // - average rating
            $this->_eTypeExtMapModel->generateField($request,
                'ext_average_rating',
                $this->_extMapData->{'target_' . $this->_entityTypeModel->primaryKey},
                0,
                'price');


            // Comment Fields
            // - total_comments
            $this->_eTypeExtMapModel->generateField($request,
                'ext_total_comments',
                $this->_extMapData->{'target_' . $this->_entityTypeModel->primaryKey});

            // - total_replies
            $this->_eTypeExtMapModel->generateField($request,
                'ext_total_replies',
                $this->_extMapData->{'target_' . $this->_entityTypeModel->primaryKey});

        }

    }


    /**
     * install
     *
     * @return Response
     */
    public function install(Request $request)
    {
        // trim/escape all
        $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));

        // get mapping data
        $this->_extMapData = $this->_eTypeExtMapModel->get($request->{$this->_eTypeExtMapModel->primaryKey});

        $created_att_ids = $this->_generateFields($request);

        // if created attribute ifs are more than 0
        if ($created_att_ids > 0) {
            // get mapping configurations
            $ex_map_conf = trim($this->_extMapData->configuration) == '' ? (object)array() : json_decode($this->_extMapData->configuration);
            // decide key name for this extension
            $key = $this->_extMapData->{$this->_eTypeExtMapModel->primaryKey} . '_attr_ids';
            // create key, if not exists
            if (isset($ex_map_conf->{$key})) {
                // do nothing
            } else {
                $ex_map_conf->{$key} = null;
            }
            // assign attribute ids
            $ex_map_conf->{$key} = implode(',', $created_att_ids);

            // update extension mapping
            $this->_extMapData->configuration = json_encode($ex_map_conf);
            $this->_eTypeExtMapModel->set(
                $this->_extMapData->{$this->_eTypeExtMapModel->primaryKey},
                (array)$this->_extMapData
            );
        }


    }


    /**
     * uninstall
     *
     * @return Response
     */
    public function unInstall(Request $request)
    {
        // trim/escape all
        $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));

        // get mapping data
        $this->_extMapData = $this->_eTypeExtMapModel->get($request->{$this->_eTypeExtMapModel->primaryKey});

        // get mapping configurations
        $ex_map_conf = trim($this->_extMapData->configuration) == '' ? (object)array() : json_decode($this->_extMapData->configuration);
        // decide key name for this extension
        $key = $this->_extMapData->{$this->_eTypeExtMapModel->primaryKey} . '_attr_ids';
        // if key exists and is not empty
        if (isset($ex_map_conf->{$key}) && trim($ex_map_conf->{$key}) != '') {
            $attr_ids = explode(',', $ex_map_conf->{$key});
            // remove all ids via api call


            // remove key from mapping
            unset($ex_map_conf->{$key});
            // update extension mapping
            $this->_extMapData->configuration = json_encode($ex_map_conf);
            $this->_eTypeExtMapModel->set(
                $this->_extMapData->{$this->_eTypeExtMapModel->primaryKey},
                (array)$this->_extMapData
            );
        }
    }


}