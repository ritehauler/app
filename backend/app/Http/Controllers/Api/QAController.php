<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Input;
use Session;
use DB;
// load models
use App\Http\Models\ApiMethod;
use App\Http\Models\ApiMethodField;
use App\Http\Models\ApiUser;
use App\Http\Models\Level;
use App\Http\Models\QAUserInput;
use App\Http\Models\User;
use App\Http\Models\QAPackageContentMap;
use App\Http\Models\GEUserLevel;
use App\Http\Models\QALevelTag;
use App\Http\Models\Setting;
use App\Http\Models\QAQuestionTracker;


class QAController extends Controller {

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
		
		// error response by default
		$this->_apiData['kick_user'] = 0;
		$this->_apiData['response'] = "error";
		
		// check access
		$this->__models['api_user_model']->checkAccess($request);
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
	 * getQuestions
	 *
	 * @return JSON
	*/
	public function getQuestions(Request $request){
		// init models
		$level_model = new Level;
		$setting_model = new Setting;
		$question_tracker_model = new QAQuestionTracker;
		$user_input_model = new QAUserInput;

		//$level_id = $request->level_id;
		$user_id = $request->user_id;
		$limit = intval($request->input("limit", 1000));
		$level_ids = $request->input("level_ids",0);
		
		// param validations
		$validator = Validator::make($request->all(), array(
			//'level_id' => 'required',
			'level_ids' => 'required',
			'user_id' => 'required'

		));

		if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else {
			// success response
			$this->_apiData['response'] = "success";
			
			// get settings
			$setting_qa_auto_refresh = $setting_model->getBy("key","qa_question_auto_refresh");
			
			// if auto-refresh is on
			if($setting_qa_auto_refresh->value == 1) {
				// get question count in user's bucket
				$user_bucket_qa_count = $question_tracker_model
					->whereRaw("level_id IN (".$level_ids.")")
					->whereRaw("container_id  NOT IN (
						SELECT container_id FROM qa_user_input
						WHERE user_id = ".$user_id."
						AND deleted_at IS NULL
					)")
					->count();
				
				// if limit is greater than user bucket
				if($limit > $user_bucket_qa_count) {
					// flush user input
					$user_input_model->removeUserRecords($user_id, $level_ids);
				}
			}
			
			
				
			
			// init output data array
			$this->_apiData['data'] = $data = array();
	
			$containers = DB::select("SELECT qbcm.`container_id`,qc.`title` AS container_title,qcb.`identifier` AS container_type,qbcm.`bag_id`,qbcm.`tag_id`,qlt.`level_tag_id`,qlt.`level_id`
			FROM `qa_level_tag` qlt 
			INNER JOIN `qa_bag_container_map` qbcm
				ON qbcm.`tag_id` = qlt.`tag_id` AND qbcm.`bag_id` = qlt.`bag_id`
			INNER JOIN `qa_container` qc
				ON qc.`container_id` = qbcm.`container_id`
			INNER JOIN `qa_container_behavior` qcb
				ON qcb.`container_behavior_id` = qc.`container_behavior_id`
			LEFT JOIN `qa_user_input` qui
				ON qc.`container_id` = qui.`container_id`
			WHERE qlt.`level_id` IN (".$level_ids.")
			AND qbcm.`container_id` NOT IN(
				SELECT container_id FROM qa_user_input
				WHERE user_id = ".$user_id."
				AND deleted_at IS NULL
			)
			GROUP BY qbcm.`container_id` ORDER BY RAND()
			LIMIT ".$limit."");
			
			
			// loop through
			for ($c=0; $c < count($containers); $c++) {
				
				$data[$c]['container']['container_id'] = $containers[$c]->container_id;
				$data[$c]['container']['container_title'] = $containers[$c]->container_title;
				$data[$c]['container']['container_type'] = $containers[$c]->container_type;
				$data[$c]['container']['level_tag_id'] = $containers[$c]->level_tag_id;
				$data[$c]['container']['level_id'] = $containers[$c]->level_id;
				$data[$c]['container']['tag_id'] = $containers[$c]->tag_id;
				$data[$c]['container']['bag_id'] = $containers[$c]->bag_id;
					$package = DB::select("SELECT 
						  qc.`container_id`,	
						  qp.`package_id`,
						  qp.`title` AS package_title,
						  qpt.`identifier` AS package_type,
						  qpcmm.`worth`
						FROM
						  `qa_container` qc 
						  INNER JOIN `qa_package_container_map` qpcm 
							ON qpcm.`container_id` = qc.`container_id` 
						  INNER JOIN `qa_package_content_map` qpcmm 
							ON qpcmm.`package_id` = qpcm.`package_id` 
						  INNER JOIN `qa_package` qp 
							ON qp.`package_id` = qpcmm.`package_id` 
						  INNER JOIN `qa_package_type` qpt 
							ON qpt.`package_type_id` = qpcm.`package_type_id` 
						WHERE qc.`container_id` = ".$containers[$c]->container_id."");
				for ($p=0;$p<count($package);$p++) {
					// if
					if($package[$p]->container_id == $containers[$c]->container_id){
					
						unset($package[$p]->container_id);
						$data[$c]['container']['packages'][] = $package[$p];
						$contents = DB::select("SELECT qc.`content_id`,qc.`title`, qc.`content`, qct.`identifier` AS content_type, qpcm.`package_content_map_id`,qpcm.`package_id`
						FROM `qa_content` qc 
						INNER JOIN `qa_package_content_map` qpcm ON qpcm.`content_id` = qc.`content_id`
						INNER JOIN `qa_content_type` qct ON qct.`content_type_id` = qc.`content_type_id`
						WHERE qpcm.`package_id` = '".$package[$p]->package_id."'");
						// loop through
						for ($cc=0;$cc<count($contents);$cc++) {
							if($contents[$cc]->package_id == $package[$p]->package_id){
								unset($contents[$cc]->package_id);
								$data[$c]['container']['packages'][$p]->content[] = $contents[$cc];
							}
						}
							
					}
				} 
			}
			
			
			// assign to output
			$this->_apiData['data'] = $data;	
		
		}
		return $this->__ApiResponse($request, $this->_apiData);
	}


	/**
	 * submitAnswers
	 *
	 * @return JSON
	*/
	public function submitAnswers(Request $request){
		// init models
		$this->__models['user_model'] = new User;

		// trim/escape all
		$request->merge(array_map('strip_tags', $request->all()));
		$request->merge(array_map('trim', $request->all()));

		// param validations
		$validator = Validator::make($request->all(), array(
			'user_id' => 'required|exists:user,user_id',
			'answer_array' => 'required'
		));
		
		// other params
		$request->input = isset($request->input) ? $request->input : "";
		$request->input_time = isset($request->input_time) ? intval($request->input_time) : 0;

		// get data
		$user = $this->__models['user_model']->getData($request->user_id);
		$answer_array = $request->answer_array;
        $d_answer_array = json_decode($answer_array,TRUE);

		if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } elseif($user !== FALSE && $user->status == 0){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
		} elseif(!is_array($d_answer_array)){
			$this->_apiData['message'] = 'Answer array field is not an Array';
		} else {
			// validate further
			if(!array_key_exists('package_id', $d_answer_array[0]) || !array_key_exists('container_id', $d_answer_array[0]) || !array_key_exists('level_tag_id', $d_answer_array[0])){
				$this->_apiData['message'] = 'Invalid Answer Array Format';
			} else{
				// init models
				$this->__models['qa_user_input'] = new QAUserInput;
				$this->__models['qa_package_content_map'] = new QAPackageContentMap;
				/*
				// get user answered content
				$user_input_content = \Cache::get("user_input_content-".$request->user_id, array(0));
				// if answers are > 10, reset array
				if(count($user_input_content) > 10) {
					//$user_input_content = array(0);
				}
				*/
				// success response
				$this->_apiData['response'] = "success";
				// init output data array
				$this->_apiData['data'] = $data = array();
				// init vars
				$correct_answers_count = 0;
				$user_level_changed = FALSE;

				
				foreach ($d_answer_array as $ans_array) {
					$answer["user_id"] = $user->user_id;
					$answer["package_id"] = $ans_array['package_id'];
					$answer["container_id"] = $ans_array['container_id'];
					$answer["level_tag_id"] = $ans_array['level_tag_id'];
					$answer["input"] = $request->input;
					$answer["input_time"] = $request->input_time;
					$answer["created_at"] = date('Y-m-d H:i:s');
					$answer_id = $this->__models["qa_user_input"]->put($answer);
					
					// get user given answer's worth
					$package_worth = $this->__models['qa_package_content_map']->getWorth($ans_array['package_id']);
					if($package_worth > 0) {
						$correct_answers_count++;
					}
					
					// put in user input array
					//$user_input_content[] = $ans_array['container_id'];
				}
				
				// put in user input array (for next 5 minutes)
				//\Cache::put("user_input_content-".$user->user_id, $user_input_content, 5);
				
				// get user current level successcor type
				if(isset($user->user_level->successor_type)) {
					// init model
					$this->__models['user_level_model'] = new GEUserLevel;
					$this->__models['level_model'] = new Level;
					
					// get data
					$user_level = $this->__models['level_model']->get($user->user_level->level_id);
					
					// if level successor
					if($user->user_level->successor_type == "levels" && $user->user_level->level_type == "qa" ) {
						// switch to level if current level schema has required_correct_answers key
						if(isset($user->user_level->schema->required_correct_answers)) {
							// if meets required score
							if($user->user_level->schema->required_correct_answers >= $correct_answers_count) {
								// switch user to next level
								//$this->__models['user_level_model']->changeData($user->user_id, $user_level->successor_check); // prefer change level service
								//$user_level_changed = TRUE;
								$user_level_changed = FALSE;
							}
						}
					}
				}
				
				// if correct count meets requirement
				
				
				// response data
				$data['user'] = $this->__models['user_model']->getData($user->user_id);
				$data['user_level_changed'] = $user_level_changed;
				
				// message
				$this->_apiData['message'] = 'Successfully Submitted Answers';
	
				// assign to output
				$this->_apiData['data'] = $data;
			}
		

		}

		return $this->__ApiResponse($this->_apiData);	

	}
	

}
