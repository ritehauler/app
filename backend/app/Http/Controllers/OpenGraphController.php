<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Models\SYSEntity;
use App\Libraries\ConfigCollection;
use App\Libraries\Mobile_Detect;
use App\Libraries\System\Entity;
use GuzzleHttp\Url;
use View;
use Cache;
use Input;
use Validator;
use Illuminate\Http\Request;

// load models
use App\Http\Models\User;
use App\Http\Models\Conf;
use App\Http\Models\Setting;

class OpenGraphController extends Controller {

	private $_assign_data = array(
		'p_dir' => '',
		'dir' => 'og/'
	);
	private $_header_data = array();
	private $_footer_data = array();
	private $_layout = "";

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(Request $request)
	{
		//$this->middleware('guest');
		// construct parent
		parent::__construct($request);
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
		return "Hello World";		
	}
	
	
	/**
	 * Share
	 *
	 * @return Response
	*/
	public function share()
	{
		// init models
		$conf_model = new Conf;
		$setting_model = new Setting;
		
		// get site data
		$config = $conf_model->getBy("key","site");
		
		// assign configurations
		$this->_assign_data["config"] = json_decode($config->value,false);
		// assign model
		$this->_assign_data["setting_model"] = $setting_model;
		
		return view($this->_assign_data["dir"].__FUNCTION__, $this->_assign_data)->with($this->__models);
	}

	/**
	 * Share url for product and order
	 * @param $slug
	 * @param $id
	 * @return \Illuminate\Http\RedirectResponse|View
	 */
	public function shareView($slug,$id)
	{
		$data = $this->_getDataByIdentifier($slug,$id);

		if(!empty($data))
		{
			$data = json_decode(json_encode($data));
			//echo '<pre>'; print_r($data); exit;
			if(isset($data->data->{$slug})){

				$this->_getSource();
				$this->_getSettingsConfig();

				$this->_assign_data["data"] = (array)$data->data->{$slug};
				return view('share/'.$slug ,$this->_assign_data);
			}
			else{
				return redirect()->away(url('/'));
			}

		}
	}

	/**
	 * Get data by identifier and id
	 * @param $slug
	 * @param $id
	 * @return array
	 */
	private function _getDataByIdentifier($slug,$id)
	{
		$entity_lib = new Entity();
		$pos_arr = [];
		$pos_arr['entity_type_id'] = $slug;
		$pos_arr['entity_id'] = $id;
		$pos_arr['in_detail'] = 0;
		$pos_arr['inner_response'] = 1;
		$pos_arr['mobile_json'] = 1;
		$data = $entity_lib->apiGet($pos_arr);
		return $data;
	}

	/**
	 * set source
	 */
	private function _getSource()
	{
		$detect = new Mobile_Detect();
		if($detect->isiOS()){
			$this->_assign_data["source"] = 'ios';
		}
		elseif($detect->isAndroidOS()){
			$this->_assign_data["source"] =  'android';
		}else{
			$this->_assign_data["source"] = 'web';
		}
	}

	/**
	 * get config setting
	 */
	private function _getSettingsConfig()
	{
		// init models
		$conf_model = new Conf();
		$setting_model = new Setting();

		// get site data
		$config_raw = $conf_model->getBy("key","site");
		$config = json_decode($config_raw->value,false);

		// ios_app_url
		$setting = $setting_model->getBy("key","ios_app_url");
		$ios_app_url = $setting ? $setting->value : FALSE;
		// android_app_url
		$setting = $setting_model->getBy("key","android_app_url");
		$android_app_url = $setting ? $setting->value : FALSE;
		// ios_store_id
		$setting = $setting_model->getBy("key","ios_store_id");
		$ios_store_id = $setting ? $setting->value : FALSE;

		// android_store_id
		$setting = $setting_model->getBy("key","android_store_id");
		$android_store_id = $setting ? $setting->value : FALSE;

		// og_schema_share
		$setting = $setting_model->getBy("key","og_schema_share");
		$og_schema_share = $setting ? $setting->value : FALSE;

		// app related
		$this->_assign_data['app_name'] = $config->site_name;
		$this->_assign_data['app_title'] = $config->site_slogan;
		$this->_assign_data['app_description'] = $config->app_description;
		$this->_assign_data['app_logo']  = \URL::to(config('constants.LOGO_PATH').$config->site_logo);

		// stores url
		$this->_assign_data['appstore_url']  = $ios_app_url ? $ios_app_url : "";
		$this->_assign_data['appstore_id'] = $appstore_id = $ios_store_id ? $ios_store_id : "";
		$this->_assign_data['appstore_url2'] = "itms-apps://itunes.apple.com/app/id".$appstore_id;

		$this->_assign_data['playstore_url']   = $android_app_url ? $android_app_url : "";
		$this->_assign_data['playstore_keystore'] = $playstore_keystore = $android_store_id ? $android_store_id : "";
		$this->_assign_data['playstore_url2']   = "market://details?id=".$playstore_keystore;


		// mobile schema
		$this->_assign_data['schema']  = $og_schema_share ? $og_schema_share : "";
	}

}
