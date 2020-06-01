<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
// load models
use App\Http\Models\ApiMethod;
use App\Http\Models\ApiMethodField;
use App\Http\Models\ApiUser;
use App\Http\Models\Conf;
use App\Http\Models\SEResource;


class SEController extends Controller {

	private $_assignData = array(
		'pDir' => '',
		'dir' => DIR_API
	);
	private $_apiData = array();
	private $_layout = "";
	private $_models = array();
	private $_jsonData = array();

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(Request $request)
	{
		// init models
		$this->__models['api_method_model'] = new ApiMethod;
		$this->__models['api_user_model'] = new ApiUser;
		$this->__models['resource_model'] = new SEResource;

		
		
		// check access
		//$this->__models['api_user_model']->checkAccess($request);
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
	 * virtualItems
	 *
	 * @return JSON
	*/
	public function getAll(Request $request)
	{	
		// trim/escape all
		$request->merge(array_map('strip_tags', $request->all()));
		$request->merge(array_map('trim', $request->all()));
		
		// get params
		$request->language = $request->language == "" ? config("constants.DEF_LANGUAGE_IDENTIFIER") : $request->language;
		// param validations
		/*$language_validator = Validator::make($request->language, array(
			'language' => 'required|exists:language,identifier'
		));*/
		$language_validator = Validator::make(array('language' => $request->language), array('language' =>'required|exists:language,identifier'));
		
		if($request->language != "" && $language_validator->fails()) {
			$this->_apiData['message'] = $language_validator->errors()->first();
		} else {
			// success response
			$this->_apiData['response'] = "success";
			// init output data array
			$this->_apiData['data'] = $data = array();
			// init models
			$this->__models['conf_model'] = new Conf;
			
			// get app config
			//$se_conf = $this->__models['conf_model']->getSchemaByKey("sync_engine");
			$se_conf = $this->__models['conf_model']->getBy("key","sync_engine");
			
			// assign update data
			$data["updated_at"] = $se_conf->updated_at;
			// assign default data
			$data["sync_data"] = array();
			
			// query
			$lang_query = "AND (
				  se_r.`language` = '".config("constants.DEF_LANGUAGE_IDENTIFIER")."' || se_r.`language` = '".$request->language."'
				) ";
			$lang_query = $request->language != config("constants.DEF_LANGUAGE_IDENTIFIER") ? $lang_query : "AND (se_r.`language` = '".config("constants.DEF_LANGUAGE_IDENTIFIER")."')";
			//$query = $this->__models['resource_model']->select("data_type","type","key","value");
			$sql = "SELECT 
				l.`language_id`, se_r.*
			  FROM
				`se_resource` AS se_r
			  
				INNER JOIN `language` AS l ON l.identifier = se_r.`language` 
				WHERE se_r.deleted_at IS NULL
				AND l.deleted_at IS NULL
				".$lang_query."
			  ORDER BY l.language_id DESC";
			$query = $this->__models['resource_model']->selectRaw("t.data_type,t.type,t.key,t.value");
			$query->from(\DB::raw("(".$sql.") AS t"));
			
			if($request->type != "") {
				$query->whereIn("t.type", explode($request->type));
			}
			if($request->updated_at != "") {
				$query->where("t.updated_at", ">", $request->updated_at);
			}
			if($request->language != config("constants.DEF_LANGUAGE_IDENTIFIER")) {
				//$query->where("t.language", "=", $request->language);
			}
			
			$query->whereNull("t.deleted_at");
			$query->groupBy("t.key");
			$query->orderBy("t.key","ASC");
			//$query->orderBy("t.resource_id", "DESC");
			$raw_records = $query->get();
			//exit($query->toSql());
			
			// init all data types
			foreach(config("constants.SE_RESOURCE_TYPES") as $se_resource_type) {
				$data["sync_data"][$se_resource_type] = (object)array();
			}
			
			// set records
			if(isset($raw_records[0])) {
				//var_dump($raw_records); exit;
				foreach($raw_records as $raw_record) {
					// set custom type
					if($raw_record->type == "custom") {
						$raw_record->value = $raw_record->value == "" ? (object)array() : json_decode($raw_record->value);
					}
					
					// set data type - integer
					if($raw_record->data_type == "integer") {
						$raw_record->value = intval($raw_record->value);
					}
					// set data type - float
					if($raw_record->data_type == "float") {
						$raw_record->value = floatval($raw_record->value);
					}
					// set data type - bool
					if($raw_record->data_type == "bool") {
						$raw_record->value = $raw_record->value == "true" ? true : false;
					}
					
					$data["sync_data"][$raw_record->type]->{$raw_record->{"key"}} = $raw_record->value;
				}
			}
			
			
			
			// assign to output
			$this->_apiData['data'] = $data;		
		}
		
		
		return $this->__ApiResponse($request,$this->_apiData);
	}
	

}
