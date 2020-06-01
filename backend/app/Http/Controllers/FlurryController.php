<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use View;
use Cache;
use Input;
use Validator;
use Request;

// load models
use App\Http\Models\Conf;
use App\Http\Models\FlurryData;

class FlurryController extends Controller {

	private $_assignData = array(
		'pDir' => '',
		'dir' => 'flurry/'
	);
	private $_headerData = array();
	private $_footerData = array();
	private $_layout = "";
	private $_model;
	private $_flurry_details;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//$this->middleware('guest');
		
		// init models
		//$this->__models['user_model'] = new User;
		
		// set model path for views
		$this->_assign_data["model_path"] = "App\Http\Models\\";
		// init current module model
		$this->_model = new FlurryData;
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
	 * Pull Anlytics Data
	 *
	 * @return HTML
	*/
	public function pullAnalyticsData(){
		set_time_limit(0);
		
		// init models
		$conf_model = new Conf;
		$flurry_data_model = new FlurryData;
		
		// get data
		$conf_data = $conf_model->getBy("key","flurry");
		$this->_flurry_details = json_decode($conf_data->value);
		// - temp
		//$this->_flurry_details->api_access_code = "GPFKKGNF4DFW3XDGY7B2";
		//$this->_flurry_details->android_key = "ZJN9GK76JRSQS97FMKMX";
		
		// other data
		// - devices
		$devices = array("Android", "iPhone");
		// - metrices
		$metrices = array("Sessions", "NewUsers", "ActiveUsers");
		//$metrices = array("Sessions", "NewUsers", "ActiveUsers");
		
		$date = date("Y-m-d"); // current date
		$current_year  = date('Y',strtotime($date));
		$current_month = date('m',strtotime($date));
		$current_day   = date('d',strtotime($date));
		$start_date = $current_year.'-'.$current_month.'-01';
		//$start_date = $current_year.'-02-29'; // temp
		$end_date = $current_year."-".$current_month."-".$current_day;
		
		
		$metrics_data = array();
		
		// loop through devices
		foreach($devices as $device) {
				
			// loop through metrices
				foreach($metrices as $metric) {
					$this->_getMetric($device, $metric, $start_date, $end_date, "All");
					sleep(1);
					echo $metric."<br />";
				}
		}
		echo "All done...";
	}
	
	/**
	 * Get Metric Data
	 *
	 * @return HTML
	*/
	public function _getMetric($device, $metric, $startDate, $endDate, $country_sign=FALSE, $versionName=FALSE){
		// decice device
		if($device == 'Android')
			$api_key = $this->_flurry_details->android_key;
		else if($device == 'iPhone')
			$api_key = $this->_flurry_details->ios_key;
		
		// prepare URL
		$URLRequest = "http://api.flurry.com/appMetrics/".
			$metric.
			"?apiAccessCode=".
			$this->_flurry_details->api_access_code.
			"&apiKey=".$api_key.
			"&startDate=".$startDate.
			"&endDate=".$endDate;
		// apply other params
		if ($country_sign)
			$URLRequest .= "&country=".$country_sign;
		if ($versionName)
			$URLRequest .= "&versionName=".$versionName;
		
		$config = array(
			'http' => array(
				'header' => 'Accept: application/xml',
				//'header' => 'Accept: application/json',
				'method' => 'GET',
				'ignore_errors' => true
			)
		);
		$stream = stream_context_create($config);
		$xml = file_get_contents($URLRequest,false,$stream);
		//$json = trim(file_get_contents($URLRequest,false,$stream));
		
		
		if($xml != "") {
			// parse json
			//$response = json_decode($json,TRUE);
			// parse xml
			$xml = preg_replace('/<([^ ]+) ([^>]*)>([^<>]*)<\/\\1>/i', '<$1 $2><value>$3</value></$1>', $xml);
			$response = simplexml_load_string($xml);
			// fix response in array shape
			$response = json_decode(json_encode($response),TRUE);
			
			// if got attributes
			if(isset($response["@attributes"])) {
				
				// init save data
				$save['device_type'] = $device;
				$save['metric'] = $metric;
				$save['app_version'] = $response["@attributes"]["version"];
				
				// if single country
				if(isset($response["country"]["@attributes"]["country"])) {
					$save['country'] = $response["country"]["@attributes"]["country"];
					
					// assign as country data
					$country_data = $response["country"];
					
					// single day
					if(isset($country_data["day"]["@attributes"])) {
						// set data
						$save["date"] = $country_data["day"]["@attributes"]["date"];
						$save["value"] = $country_data["day"]["@attributes"]["value"];
							
						$data_exists = $this->_model
						->where("date", "=", $save["date"])
						->where("metric","=", $save["metric"])
						->where("country","=", $save["country"])
						->get();
					
						// if data exists
						if(isset($data_exists[0])) {
							$save['updated_at'] = date('Y-m-d H:i:s');
							// update
							$this->_model->set($data_exists[0]->flurry_data_id,$save);
							
						} else {
							unset($save['updated_at']);
							$save['created_at'] = date('Y-m-d H:i:s');
							// save
							$this->_model->put($save);
						}
						
					} else {
						// multiple days
						
						// parse day-wise
						foreach($country_data["day"] as $day_data) {
							// set data
							$save["date"] = $day_data["@attributes"]["date"];
							$save["value"] = $day_data["@attributes"]["value"];
							
							$data_exists = $this->_model
							->where("date", "=", $save["date"])
							->where("metric","=", $save["metric"])
							->where("country","=", $save["country"])
							->get();
						
							// if data exists
							if(isset($data_exists[0])) {
								$save['updated_at'] = date('Y-m-d H:i:s');
								// update
								$this->_model->set($data_exists[0]->flurry_data_id,$save);
								
							} else {
								unset($save['updated_at']);
								$save['created_at'] = date('Y-m-d H:i:s');
								// save
								$this->_model->put($save);
							}
						} // end loop through
						
						
					} // end multiple days
					/*echo "<pre>";
					print_r($response["country"]["day"]);
					echo "</pre>";
					exit;*/
					
				} else {
					if(isset($response["country"])) {
						
						// loop through
						foreach($response["country"] as $country_data) {
							
							// country
							$save['country'] = $country_data["@attributes"]["country"];
							
							// single day
							if(isset($country_data["day"]["@attributes"])) {
								// set data
								$save["date"] = $country_data["day"]["@attributes"]["date"];
								$save["value"] = $country_data["day"]["@attributes"]["value"];
									
								$data_exists = $this->_model
								->where("date", "=", $save["date"])
								->where("country","=", $save["country"])
								->where("metric","=", $save["metric"])
								->get();
							
								// if data exists
								if(isset($data_exists[0])) {
									$save['updated_at'] = date('Y-m-d H:i:s');
									// update
									$this->_model->set($data_exists[0]->flurry_data_id,$save);
									
								} else {
									unset($save['updated_at']);
									$save['created_at'] = date('Y-m-d H:i:s');
									// save
									$this->_model->put($save);
								}
								
							} else {
								// multiple days
								
								// parse day-wise
								foreach($country_data["day"] as $day_data) {
									// set data
									$save["date"] = $day_data["@attributes"]["date"];
									$save["value"] = $day_data["@attributes"]["value"];
									
									$data_exists = $this->_model
									->where("date", "=", $save["date"])
									->where("country","=", $save["country"])
									->where("metric","=", $save["metric"])
									->get();
								
									// if data exists
									if(isset($data_exists[0])) {
										$save['updated_at'] = date('Y-m-d H:i:s');
										// update
										$this->_model->set($data_exists[0]->flurry_data_id,$save);
										
									} else {
										unset($save['updated_at']);
										$save['created_at'] = date('Y-m-d H:i:s');
										// save
										$this->_model->put($save);
									}
								} // end loop through
								
								
							} // end multiple days
							
						} // loop through
					} else {
						echo "<pre>";
						print_r($response);
						echo "</pre>";
						//exit;
					}
					
					
				} // end country based parsing
			} else {
				echo "<pre>";
				print_r($response);
				echo "</pre>";
				//exit;
			}
			
			
			
			
			/*echo "<pre>";
			print_r($response);
			echo "</pre>";
			exit;*/
			
			
		}
		
	}
	
	
	

}
