<?php

namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use View;
use Validator;
// load models
use App\Http\Models\ApiMethod;
use App\Http\Models\ApiMethodField;
use App\Http\Models\ApiUser;
use App\Http\Models\EFEntityPlugin;
use App\Http\Models\Conf;

//use Twilio;

class ChatingController extends Controller
{

    private $_apiData = array();
    private $_layout = "";
    private $_models = array();
    private $_jsonData = array();
    private $_model_path = "\App\Http\Models\\";
    private $_object_identifier = "sys_chating";
    private $_sys_entity_gallery_identifier = "system_entity_gallery"; // usually routes path
    private $_sys_entity_gallery_pk = "sys_entity_gallery_id";
    private $_sys_entity_gallery_ucfirst = "EntityGallery";
    private $_sys__model = "SYSChating";
    private $_plugin_config = array();
    private $_entityModel = "SYSEntity";
    private $_entityTypeModel = "SYSEntityType";
    private $_mobile_json = false;
    private $_objectIdentifier = "entity";


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        // load sys_entity_gallery model
        $this->_sys_chating_model = $this->_model_path . $this->_sys__model;
        $this->_sys_chating_model = new $this->_sys_chating_model;

        // load entity model
        $this->_entityModel = $this->_model_path . $this->_entityModel;
        $this->_entityModel = new $this->_entityModel;

        $this->_entityTypeModel = $this->_model_path . $this->_entityTypeModel;
        $this->_entityTypeModel = new $this->_entityTypeModel;

        $this->_entityAuth = $this->_model_path . "SYSEntityAuth";
        $this->_entityAuth = new $this->_entityAuth;

        $this->_messageModel = $this->_model_path . "Message";
        $this->_messageModel = new $this->_messageModel;

        $this->__models['user_model'] = $this->_entityAuth;


        $this->__models['message_model'] = $this->_messageModel;

        if (is_numeric(trim($request->entity_type_id))) {
            $entityTypeData = $this->_entityTypeModel->getEntityTypeById($request->entity_type_id);
        } elseif (isset($request->entity_type_id)) {
            $entityTypeData = $this->_entityTypeModel->getEntityTypeByName($request->entity_type_id);
            if ($entityTypeData) $request->entity_type_id = $entityTypeData->entity_type_id;
        }

        if ($this->_mobile_json && isset($entityTypeData)) {
            $this->_objectIdentifier = $entityTypeData->identifier;
        }

        // error response by default
        $this->_apiData['kick_user'] = 0;
        $this->_apiData['response'] = "error";

    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function index(Request $request)
    {

    }



    /**
     * Get Chat List
     *
     * @return Response
     */
    public function chatList(Request $request)
    {
        // init models
		
		$allowed_sorting = "asc,desc";
        // get params
        $user_id = intval(trim(strip_tags($request->entity_id)));
        $keyword = trim(strip_tags($request->input('keyword', "")));
        $keyword = strtolower($keyword); // set default value
        //$page_no = (int)trim(strip_tags($request->input('page_no', 0)));
		$limit    = (int)trim(strip_tags($request->input('limit', 0)));
        $limit    = $limit == "" ? PAGE_LIMIT_API : $limit;
		 
		$offset = (int)trim(strip_tags($request->input('offset', 0)));
		
		if(!is_numeric($offset)) $offset = 0;

    	$offset = $offset < 0 ? 0 : $offset;
   		$next_offset = $offset; // - new pagination flow
	
		//$order_by = ($request->input('order_by', "") == "") ? explode(",", $allowed_ordering)[0] : $order_by;
    	$sorting = ($request->input('sorting', "") == "") ? explode(",", $allowed_sorting)[0] :$request->input('sorting');
	
        $user = $this->entityToUserID($user_id);

        if($user === FALSE || count($user)<=0) {
            $this->_apiData['message'] = 'Invalid user Request';
        }
        elseif($user->status == 0){
            // kick user
            $this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
        }
        elseif($user->status > 1){
            // kick user
            $this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
        }
        else {
            // success response
            $this->_apiData['response'] = "success";
            // init output data array
            $this->_apiData['data'] = $data = array();

            // set initial array for records
            $data["chatting"] = array();

            $sql = "MAX(m.message_id) as message_id,
			COUNT(message_id) AS messages,
			IF(m.sender_id = '".$user->entity_id."', m.`receiver_id`, m.`sender_id`) AS target_user_id,
			(SELECT COUNT(*) FROM message
				WHERE ((sender_id = m.`sender_id` AND receiver_id = m.`receiver_id`) OR (sender_id = m.`receiver_id` AND receiver_id = m.`sender_id`))
				AND message_id NOT IN (SELECT message_id FROM message_trash WHERE user_id = '".$user->entity_id."')
				AND `is_unread` = 1
			) AS count_unread,
IF(m.`sender_id` > m.`receiver_id`, CONCAT(m.`receiver_id`,'-',m.`sender_id`), CONCAT(m.`sender_id`,'-',m.`receiver_id`)) AS chat_code";
            $query = $this->__models['message_model']->selectRaw($sql);
            $query->whereRaw("(m.receiver_id = '".$user->entity_id."' OR m.sender_id = '".$user->entity_id."')")
                ->whereRaw("m.message_id NOT IN (SELECT message_id FROM message_trash WHERE user_id = '".$user->entity_id."')");
            $query->from("message AS m");
            $query->join("sys_entity_auth AS u","u.entity_id", "=", \DB::raw("IF(m.sender_id = '".$user->entity_id."', m.`receiver_id`, m.`sender_id`)"));
            $query->whereNull("m.deleted_at");
            $query->whereNull("u.deleted_at");
            $query->where("u.status", "=", 1);
            $query->having("messages", ">", 0);
            $query->havingRaw("target_user_id NOT IN (
				SELECT target_user_id FROM user_block
				WHERE user_id = '".$user->entity_id."'
			) AND target_user_id NOT IN (
				SELECT user_id FROM user_block
				WHERE target_user_id = '".$user->entity_id."'
			)");
            if($keyword != "") {
                $query->where("u.name","like","%".$keyword."%");
            }
			
			$query->orderBy("message_id", strtoupper($sorting));
			 
			if ($offset > 0) {
				$operator = strtolower($sorting) == "asc" ? ">" : "<";
				$query->where("message_id", $operator, $offset);
			}
			
            //$query->orderBy("message_id", "DESC");
            $query->groupBy(array("chat_code"));
			$query->take($limit);
			


           //echo $query->toSql(); die;
            $raw_records = $query->get();
			
            //$total_records = $raw_records->count();
            $total_records = count($raw_records);
            //var_dump($raw_records);exit;
            //var_dump($query->toSql()); exit;
            // offfset / limits / valid pages
            //$total_pages = ceil($total_records / PAGE_LIMIT_API);
            //$page_no = $page_no >= $total_pages ? $total_pages : $page_no;
            //$page_no = $page_no <= 1 ? 1 : $page_no;
            //$offset = PAGE_LIMIT_API * ($page_no - 1);

            //$raw_records = $raw_records->splice($offset, $limit);
			//print_r($raw_records); die;
            /*var_dump($query->toSql());
            print_r($raw_records);
            exit;*/
            // set records
            $image_path = url('/') . '/' .config("pl_user.DIR_IMG");
            if(isset($raw_records[0])) {
                //var_dump($raw_records); exit;
                foreach($raw_records as $raw_record) {

                    $message = $this->__models['message_model']->getData($raw_record->message_id);

                    $user = $this->__models['user_model']->where("entity_id" , "=" ,$raw_record->target_user_id)->get();
                    $user = $user[0];

                    //var_dump($raw_record);exit;
                    //var_dump($user);exit;

                    // set chat name and picture
                    $message->user_name = $user->name;
                    //var_dump($message);exit;
                    $message->target_user_id = $raw_record->target_user_id;


                    $message->is_unread = $message->sender_id == $user_id ? 0 : $raw_record->count_unread;

                    $message->count_unread = $message->is_unread;
                    // set remote img path
                    $user->image = $user->thumb == "" ? "" : $image_path . $user->thumb;
                    //$message->chat_image = preg_match("@^http@",$user->image) ? $user->image : url("/")."/".DIR_USER_IMG.$user->image;
                    //$message->chat_image = preg_match("@^http@",$user->image) ? $user->image : url("/")."/"."thumb/user/150x150/".$user->image;
                    //var_dump($message);exit;

                    $message->user_image = $user->image;

                    // sort keys ascendingly to find easily
                    $message = (array)$message;
                    ksort($message);
                    // back to original
                    $message = (object)$message;

                    $data["chatting"][] = $message;
					$next_offset = $message->message_id; // new pagination flow
                }
            }


            // set pagination response
            $data["page"] = array(
				
				"limit" => $limit,
                //"current" => $page_no,
                "total_records" => $total_records,
                //"next" => $page_no >= $total_pages ? 0 : $page_no + 1,
                //"prev" => $page_no <= 1 ? 0 : $page_no - 1
				"next_offset" => $next_offset,
				"prev_offset" => $offset
            );

            // assign to output
            $this->_apiData['data'] = $data;
        }


        return $this->__apiResponse($request,$this->_apiData);
    }


    /**
     * Remove Chat
     *
     * @return Response
     */
    public function removeChat(Request $request)
    {
        // get params
        $user_id = trim(strip_tags($request->input('entity_id', 0)));
        $user_id = $user_id == "" ? 0 : $user_id; // set default value
        $target_user_id = trim(strip_tags($request->input('target_entity_id', 0)));
        $target_user_id = $target_user_id == "" ? 0 : $target_user_id; // set default value

        // get data
        //$user = $this->__models['user_model']->get($user_id);

        $user = $this->entityToUserID($request->entity_id);

        $target_user = $this->entityToUserID($request->target_entity_id);

        //$target_user = $this->__models['user_model']->get($target_user_id);

        if($user_id == 0) {
            $this->_apiData['message'] = 'Please enter User ID';
        }
        else if($user === FALSE || count($user)<=0) {
            $this->_apiData['message'] = 'Invalid User Request';
        }
        elseif($user->status == 0){
            // kick user
            $this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
        }
        elseif($user->status > 1){
            // kick user
            $this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
        }
        else if($target_user_id == 0) {
            $this->_apiData['message'] = 'Please enter Target User ID';
        }
        else if($target_user === FALSE) {
            $this->_apiData['message'] = 'Invalid Target User Request';
        }
        elseif($target_user->status != 1){
            $this->_apiData['message'] = 'Target user profile is not available';
        }
        else {
            // success response
            $this->_apiData['response'] = "success";
            // init output data array
            $this->_apiData['data'] = $data = array();

            // remove chat
            $this->__models['message_model']->removeChat($user->entity_id, $target_user->entity_id);

            // message
            $this->_apiData['message'] = "Successfully removed chat";

            // assign to output
            $this->_apiData['data'] = $data;
        }


        return $this->__apiResponse($request,$this->_apiData);
    }


    /**
     * Send Message
     *
     * @return Response
     */
    public function sendMessage(Request $request)
    {


        // get params
        $message = trim(strip_tags($request->input("message")));

        // param validations
        $validator = Validator::make($request->all(), array(
            'entity_id' => 'required|exists:user,user_id',
            'target_entity_id' => 'required|exists:user,user_id',
            'message' => 'required'
        ));



        // get data

        $user = $this->entityToUserID($request->entity_id);

        $target_user = $this->entityToUserID($request->target_entity_id);


        /*$this->__models['user_block_model'] = new UserBlock;
        $record_id = $this->__models['user_block_model']->check($request->input('user_id'), $request->input('target_user_id'));
        var_dump($record_id);
        exit;*/

        //file_put_contents(getcwd()."/test_errors.log",date("c")." : ".json_encode($_REQUEST)."\n\n");

        if($user === FALSE || count($user)<=0 || count($target_user)<=0)  {
            $this->_apiData['message'] = 'Invalid User Request';
        }
        elseif($user->status == 0){
            // kick user
            $this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
        }
        elseif($user->status > 1){
            // kick user
            $this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
        }
        else if($target_user === FALSE) {
            $this->_apiData['message'] = 'Invalid Target User Request';
        }
        elseif($target_user->status != 1){
            $this->_apiData['message'] = 'Target user profile is not available';
        } else if($user->entity_id == $target_user->entity_id) {
            $this->_apiData['message'] = 'Cannot send message to yourself';
        }  else if ($message == "") {
            $this->_apiData['message'] = 'Please enter message';
        }
        else {
            // init models
            //$this->__models['user_history_model'] = new UserHistory;
            // success response
            $this->_apiData['response'] = "success";
            // init output data array
            $this->_apiData['data'] = $data = array();


            // save
            $save["sender_id"] = $user->entity_id;
            $save["receiver_id"] = $target_user->entity_id;
            $save["message"] = $message;
            $save["is_unread"] = 1;
            //$save["message_type"] = $request->input('message_type');
            //$save["data_packet"] = $request->input('data_packet');
            $save["created_at"] = date("Y-m-d H:i:s");
            $message_id = $this->__models['message_model']->put($save);

            // save history
            $reference_data = array(
                "reference_module" => "message",
                "reference_id" => $message_id,
                "against" => "user",
                "against_id" => $target_user->user_id,
                "navigation_type"    => "message",
                "navigation_item_id" => $message_id
            );
            //$this->__models['user_history_model']->putUserHistory($user->user_id,"messages",$reference_data);

            // set for history
            $activity_data = array(
                "navigation_type" => 'message',
                "navigation_item_id" => $message_id,
                "reference_module" => 'message',
                "reference_id" => $message_id,
                //"against" => "user",
                //"against_id" => $save["actor_id"]
            );
            // put history
//            $this->__models['entity_history_model']->putEntityHistory(
//                $target_user->user_id,
//                $request->user_id,
//                'user_message',
//                $activity_data,
//                "ef_social2"
//            );


            // output
            $save["sender_name"] = $user->name;
            $save["message_id"] = $message_id;
            $data["chat"] = $save;


            //  temp
//            $notification_model = new Notification;
//            $temp_data = array(
//                "sound" => "sound0",
//                "badge" => 1,
//                "title" => "test notification",
//                "body" => "just testing otification",
//                "key_code" => 105
//            );
//            $r = $notification_model->pn_ios("5712dafc45dd35dc43606afa1bf53c40769a3c517bcbe753a2b0fd36efd267a7",$temp_data);
//            $data['return'] = $r;

            // message
            $this->_apiData['message'] = "Message successfully sent";

            // assign to output
            $this->_apiData['data'] = $data;
        }


        return $this->__apiResponse($request,$this->_apiData);
    }


    /**
     * Message History
     *
     * @return Response
     */
    public function history(Request $request)
    {
        // init models

        // get params
        $user_id = intval(trim(strip_tags($request->input('entity_id', 0))));
        //$user_id = $user_id == "" ? 0 : $user_id; // set default value
        $target_user_id = intval(trim(strip_tags($request->input('target_entity_id', 0))));
        //$target_user_id = $target_user_id == "" ? 0 : $target_user_id; // set default value
        //$page_no  = (int)trim(strip_tags($request->input('page_no', "")));
        //$page_no  = $page_no == "" ? 1 : $page_no ;
        $limit    = (int)trim(strip_tags($request->input('limit', 0)));
        $limit    = $limit == "" ? PAGE_LIMIT_API : $limit;
        //$offset   = $limit * ($page_no - 1);
        $datetime = trim(strip_tags($request->input('datetime', "")));
        $datetime = $datetime == "" ? date("Y-m-d H:i:s") : $datetime; // set default value
		$allowed_sorting = "asc,desc";
		$sorting = ($request->input('sorting', "") == "") ? explode(",", $allowed_sorting)[0] :$request->input('sorting');
		
		$offset = (int)trim(strip_tags($request->input('offset', 0)));
		if (!is_numeric($offset)) $offset = 0;
		$offset = $offset < 0 ? 0 : $offset;
    	$next_offset = $offset; // - new pagination flow
		
		
		
        // get data

        $user = $this->entityToUserID($user_id);

        $target_user = $this->entityToUserID($target_user_id);

        if($user_id == 0) {
            $this->_apiData['message'] = 'Please enter User ID : '.$user_id.' Target userr ID : '.$target_user_id;
        }
        else if($user === FALSE || count($user)<=0 || count($target_user)<=0) {
            $this->_apiData['message'] = 'Invalid User Request';
        }
        elseif($user->status == 0){
            // kick user
            $this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
        }
        elseif($user->status > 1){
            // kick user
            $this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
        }
        else if($target_user_id == 0) {
            $this->_apiData['message'] = 'Please enter Target User ID';
        }
        else if($target_user === FALSE) {
            $this->_apiData['message'] = 'Invalid Target User Request';
        }
        elseif($target_user->status != 1){
            $this->_apiData['message'] = 'Target user profile is not available';
        }
        else {
            // success response
            $this->_apiData['response'] = "success";
            // init output data array
            $this->_apiData['data'] = $data = array();

            // set initial array for records
            $data["messages"] = array();

            $query = $this->__models['message_model']
                ->whereRaw("
				((m.sender_id = '".$user->user_id."' AND m.receiver_id = '".$target_user->entity_id."')
				OR (m.sender_id = '".$target_user->user_id."' AND m.receiver_id = '".$user->entity_id."'))
				AND m.message_id NOT IN (SELECT message_id FROM message_trash WHERE user_id = '".$user->entity_id."')
				");
            
			//$query->where("created_at", "<", $datetime);
            $query->orderBy("message_id", strtoupper($sorting));
			$query = $query->from("message AS m");
            $query->whereNull("m.deleted_at");
			if ($offset > 0) {
				$operator = strtolower($sorting) == "asc" ? ">" : "<";
				$query->where("message_id", $operator, $offset);
			}
            $total_records = $query->count();


            // query records
            $sql = "m.message_id, m.created_at";
            $query = $this->__models['message_model']->selectRaw($sql)
                ->whereRaw("
				((m.sender_id = '".$user->entity_id."' AND m.receiver_id = '".$target_user->entity_id."')
				OR (m.sender_id = '".$target_user->entity_id."' AND m.receiver_id = '".$user->entity_id."'))
				AND m.message_id NOT IN (SELECT message_id FROM message_trash WHERE user_id = '".$user->entity_id."')
				");
                //->where("m.created_at", "<", $datetime);

            //$query->orderBy("m.created_at", "DESC");
			$query->orderBy("message_id", strtoupper($sorting));
            $query = $query->from("message AS m");
            $query->whereNull("m.deleted_at");
			
			if ($offset > 0) {
				$operator = strtolower($sorting) == "asc" ? ">" : "<";
				$query->where("message_id", $operator, $offset);
			}
			
            $query->take($limit);
			
            /*if(!empty($offset)){
                $query->skip($offset);
            }*/
            $raw_records = $query->get();

            //exit($query->toSql());

            // set records
            if(isset($raw_records[0])) {
                // set last msg datetime
                //$datetime = $raw_records[0]->datetime;
                $user_image_path = url('/') . '/' . config("pl_user.DIR_IMG");

                foreach($raw_records as $raw_record) {

                    $message = $this->__models['message_model']->getData($raw_record->message_id);
                    //$sender = $this->__models['user_model']->get($message->sender_id);
                    //$receiver = $this->__models['user_model']->get($message->receiver_id);

                    $sender = $this->entityToUserID($message->sender_id);

                    $receiver = $this->entityToUserID($message->receiver_id);

                    // set remote img path
                    $sender->image = $sender->image == "" ? "" : $user_image_path . $sender->thumb;
                    //$message->sender_image = preg_match("@^http@",$sender->image) ? $sender->image : url("/")."/".DIR_USER_IMG.$sender->image;
                    $message->sender_image = $sender->image;

                    // set remote img path
                    $receiver->image = $receiver->image == "" ? "" : $user_image_path . $receiver->thumb;
                    //$message->receiver_image = preg_match("@^http@",$receiver->image) ? $receiver->image : url("/")."/".DIR_USER_IMG.$receiver->image;
                    $message->receiver_image = $receiver->image;

                    // sort keys ascendingly to find easily
                    $message = (array)$message;
                    ksort($message);
                    // back to original
                    $message = (object)$message;

                    $data["messages"][] = $message;
					$next_offset = $message->message_id; // new pagination flow
                    // set date
                    $datetime = $message->created_at;
                }
            }

            //$data["messages"] = array_reverse($data["messages"]);
            $total_pages = ceil($total_records / $limit);

            // set pagination response
            $data["page"] = array(
                //"current" => $page_no,
                //"total"   => $total_records > $limit ? ceil($total_records / $limit) : 1,
                //"next"    => $page_no >= $total_pages ? 0 : $page_no + 1,
                //"pre"	  => $page_no <= 1 ? 0 : $page_no - 1
				"limit" => $limit,
				"total_records" => $total_records,
				"next_offset" => $next_offset,
				"prev_offset" => $offset
            );



            // last msg date time
            //$data["datetime"] = $datetime;
            //echo $target_user->user_id; exit;
            // mark all msgs as read
            $this->__models['message_model']->markRead($user_id, $target_user->entity_id);

            // count unread message
            //$count_messages = $this->__models['message_model']->messageCount($user_id , $target_user->entity_id);

            $data["count_unread_messages"] = count($raw_records);

            // assign to output
            $this->_apiData['data'] = $data;
        }


        return $this->__apiResponse($request,$this->_apiData);
    }


    public function entityToUserID($user_id){
        $user = $this->__models['user_model']->where("entity_id" , "=" , $user_id)->get();
        if(count($user)>0){
            $user = $user[0];
        }
        return $user;
    }



}