<?php namespace App\Http\Controllers;


use App\Http\Models\HistoryNotification;
use App\Http\Models\Notification;
use App\Http\Models\User as AppUser;
use App\Libraries\System\Entity;
use Cache;
use Illuminate\Http\Request;

// load models

class IndexController extends Controller
{

    private $_assignData = array(
        'pDir' => '',
        'dir' => ''
    );
    private $_headerData = array();
    private $_footerData = array();
    private $_layout = "";

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('guest');
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
     * Confirm user Signup
     *
     * @return HTML
     */
    public function confirm_signup()
    {
        // init models
        $this->__models['user_model'] = new AppUser;

        // init blank msg
        $msg = "";

        // get params
        $email = trim(strip_tags(Input::get('email', '')));
        $hash = trim(strip_tags(Input::get('hash', '')));

        // validations
        $valid_email = Validator::make(array('email' => $email), array('email' => 'email'));

        // fetch data
        $row_exists = $this->__models['user_model']
            ->where('signup_hash', '=', $hash)
            ->where('email', '=', $email)
            ->get(array("user_id")
            );
        $user_id = isset($row_exists[0]) ? $row_exists[0]->user_id : 0;

        // validations
        if ($valid_email->fails() || $user_id === 0) {
            $msg = "Invalid confirmation Link";
        } /*if($email == "") {
			$msg = "Please Enter Email";
		}
		else if($valid_email->fails()) {
			$this->_apiData['message'] = 'Please enter valid Email';
		}
		else if($user_id === 0) {
			$msg = "Invalid confirmation Link";
		}*/
        else {
            // get user data
            $user = $this->__models['user_model']->get($user_id);

            // send forgot password email
            $this->__models['user_model']->welcome_user($user);

            $msg = "Your account is successfuly activated";
        }

        if ($msg !== "") {
            echo "<div align='center'><h2>$msg</h2></div>";
        }

    }

    /**
     * Confirm Forgot Password
     *
     * @return HTML
     */
    public function confirm_forgot(Request $request)
    {
        // init models
        $this->__models['user_model'] = new AppUser;

        // get params
        $email = trim(strip_tags(Input::get('email', '')));
        $hash = trim(strip_tags(Input::get('hash', '')));

        // validations
        $valid_email = Validator::make(array('email' => $email), array('email' => 'email'));

        // fetch data
        $row_exists = $this->__models['user_model']
            ->where('forgot_hash', '=', $hash)
            ->where('email', '=', $email)
            ->get(array("user_id")
            );
        $user_id = isset($row_exists[0]) ? $row_exists[0]->user_id : 0;

        // get user data
        $user = $this->__models['user_model']->get($user_id);

        // process form
        if ($request->ajax()) {

            // params
            $password = trim(strip_tags(Input::get('password', '')));
            $c_password = trim(strip_tags(Input::get('c_password', '')));

            if ($user === FALSE) {
                $this->_jsonData['focusElem'] = 'input[name="password"]';
                $this->_jsonData['callback'] = 'alert("Invalid Confirmation Link");';
            } else if ($password == "") {
                $this->_jsonData['focusElem'] = 'input[name="password"]';
                $this->_jsonData['callback'] = 'alert("Please enter New Password");';
            } else if (strlen($password) < 8) {
                $this->_jsonData['focusElem'] = 'input[name="password"]';
                $this->_jsonData['callback'] = 'alert("Password must contain alteast 8 characters");';
            } else if ($c_password == "") {
                $this->_jsonData['focusElem'] = 'input[name="c_password"]';
                $this->_jsonData['callback'] = 'alert("Please Confirm Password");';
            } else if ($password != $c_password) {
                $this->_jsonData['focusElem'] = 'input[name="c_password"]';
                $this->_jsonData['callback'] = 'alert("Passwords did not match");';
            } else {

                // set data
                $user->forgot_hash = "";
                $user->password = $this->__models['user_model']->salt_password($password);
                $this->__models['user_model']->set($user->user_id, (array)$user);

                // target element
                $this->_jsonData['targetElem'] = 'div[id=main_body]';
                $this->_jsonData['html'] = "<br /><h4 align=\"center\">Password successfully changed. Thank you</h4>";
            }

            $this->_assignData['jsonData'] = $this->_jsonData;
            $this->_layout .= view(DIR_ADMIN . "jsonResponse", $this->_assignData)->with($this->__models);

            return $this->_layout;
        }

        // set action title
        $this->_assignData["action_title"] = "Reset Password";

        // assign data to view
        $this->_assignData["email"] = $email;
        $this->_assignData["hash"] = $hash;
        $this->_assignData["user"] = $user;

        $this->_layout .= view($this->_assignData["dir"] . "/" . __FUNCTION__, $this->_assignData)->with($this->__models);

        return $this->_layout;
    }


    /**
     * Reset Password
     *
     * @return HTML
     */
    /*public function reset_password()
    {
        // init blank msg
        $msg = "";

        // get params
        $password = trim(strip_tags($this->input->get_post("password")));
        //$hash = trim(strip_tags($this->input->get_post("hash")));

        // conditions
        //$opt["conditions"]["signup_hash"] = $hash;
        $opt["conditions"]["email"] = $email;
        $raw_user = $this->user_model->findAllIDs($opt);
        unset($opt);

        $user_id = isset($raw_user[0]->user_id) ? $raw_user[0]->user_id : 0;
        // get user data
        $user = $this->user_model->findByID($user_id);

        // validations
        if($password == "") {
            $msg = "Please Enter Email";
        }
        else if($user === FALSE) {
            $msg = "No user Found";
        }
        else {

            //$this->load->view('reset_password');
                //echo "you can change your password here"; die;

            // send forgot password email forgot_password_change
            //$this->user_model->welcome_user_signup($user->user_id);
            $this->user_model->forgot_password_change($user->user_id);

            $msg = "Password is Re-Set Successfully";

        }

        if($msg !== "") {
            echo "<div align='center'><h2>$msg</h2></div>";
        }

    }*/

    /**
     * multi uploader
     *
     * @param $request
     * @return
     */
    public function multiUploader(Request $request)
    {
        // params
        $reserve_name = (int)$request->reserve_name > 0 ? 1 : 0;

        // filesize
        $file_size = isset($_FILES["file"]["size"]) ? $_FILES["file"]["size"] : 0;

        if ($file_size > 0) {
            //file type
            $file_type = $_FILES['file']['type'];
            //$file_type = $request->file->getClientMimeType();
            $file_type = explode('/', $file_type);
            // content type
            $content_type = $file_type[0];

            if ($request->reserve_name > 0) {
                //file name
                $file_name = $_FILES['file']['name'];
                /*
                // filetype
                 $content_type = "image"; // default
                 if(preg_match("@^(image_)@i")) {
                     $content_type = "image";
                 } else if(preg_match("@^(audio_)@i")) {
                     $content_type = "audio";
                 } else if(preg_match("@^(video_)@i")) {
                     $content_type = "video";
                 } else {
                   // nothing
                 }*/

            } else {
                //file name
                //$file_name = $file_type[0].'_'.Auth::user()->admin_id.'_'.uniqid().'.'.$request->file->getClientOriginalExtension();
                $file_name = $file_type[0] . '_' . uniqid() . '.' . $request->file->getClientOriginalExtension();
            }
            //move file
            $destination_path = base_path() . '/' . config("constants.RAW_PATH"); // upload path
            move_uploaded_file($_FILES["file"]["tmp_name"], $destination_path . $file_name);

            // set record
            $save['title'] = $file_name;
            $save['type'] = $content_type;
            $save['queue_id'] = $request->queue_id;
            $save['params'] = json_encode($_FILES['file']);
            $save["created_at"] = date("Y-m-d H:i:s");

            // insert
            $raw_model = new \App\Http\Models\RawFile;
            $record_id = $raw_model->put($save);
        }

    }


    /**
     * clear cache
     *
     * @return
     */
    public function clearCache()
    {
        // init models
        Cache::flush();

        return CACHE_ON ? "Cleared" : "Cache is not enabled";
    }

    /**
     * Declare Winners
     *
     * @return HTML
     */
    public function declare_winners()
    {
        //exit($_SERVER['DOCUMENT_ROOT']."/".ADD_PATH.APP_ALIAS);
        // init other configs
        set_time_limit(0);
        //exit($_SERVER['DOCUMENT_ROOT']);

        // init models
        $this->__models['user_model'] = new AppUser;
        $this->__models['winner_model'] = new Winner;
        $this->__models['media_vote_model'] = new MediaVote;

        // configs
        $limit_winners = 20;

        // schedule date
        //$schedule_date = "2015-11-05"; // -temp
        $schedule_date = date("Y-m-d", strtotime("today"));
        var_dump($schedule_date);
        var_dump(strtotime($schedule_date));
        //exit;


        $last_record = $this->__models['winner_model']->where("date_stamp", $schedule_date)->first();
        if (isset($last_record->winner_id)) {
            // do nothing already have winners today
        } else {

            // find total pages
            $query = $this->__models['media_vote_model']
                ->selectRaw("mv.media_id, m.user_id, count(mv.user_id) as count_votes")
                ->where("mv.status", "=", 1)// current date votes
                ->where("m.status", "=", 1)// only active media
                //->where("mv.vote_date", "=", strtotime($schedule_date));
                ->where("mv.vote_date", "=", $schedule_date);
            $query->join('media AS m', 'm.media_id', '=', 'mv.media_id');
            $query->groupBy("mv.media_id");
            $query->take($limit_winners);
            $query->from("media_vote AS mv");
            $query->orderBy("count_votes", "DESC");
            $raw_records = $query->get();
            //print_r($raw_records[0]);exit;
            if (isset($raw_records[0])) {
                // init model
                $this->__models['notification_model'] = new Notification;

                $i = 0;

                $datetime = strtotime($schedule_date);
                $date_stamp = $schedule_date;

                // open ios socket for multiple notifications
                $this->__models['notification_model']->open_ios();

                // collect winner ids
                $winner_user_ids = array(0);

                foreach ($raw_records as $raw_record) {
                    $i++;
                    //echo "<p>".$raw_record->media_id." \t ".$raw_record->count_votes."</p>";
                    $user = $this->__models['user_model']->get($raw_record->user_id);

                    // save winner data
                    $winner = array(
                        "media_id" => $raw_record->media_id,
                        "user_id" => $user->user_id,
                        "position" => $i,
                        "count_votes" => $raw_record->count_votes,
                        "date_stamp" => $date_stamp,
                        "datetime" => $datetime
                    );
                    $this->__models['winner_model']->put($winner);


                    // update badge count of target user
                    //$user->count_notification = $user->count_notification + 1; // donot increament count
                    $this->__models['user_model']->set($user->user_id, (array)$user);

                    // send push notification to user about winning
                    if ($user->device_token != "" && $user->is_notify_request == 1) {
                        // get updates count
                        $updates_data = $this->__models["notification_model"]->updates($user->user_id);

                        // prepare notification data
                        $noti_data = array(
                            "alert" => "Congratulations, your media is amongst the top 20 voted media.",
                            "target_user_id" => $user->user_id,
                            //"type" => "user_win",
                            "type" => 106,
                            "sound" => $user->sound,
                            //"badge" => (int)$user->count_notification
                            "badge" => $updates_data["update"]["badge_count"]
                        );

                        if ($user->device_type == "ios") {
                            $r = $this->__models['notification_model']->pn_ios($user->device_token, $noti_data, FALSE);
                        } else {
                            $r = $this->__models['notification_model']->pn_android($user->device_token, $noti_data);
                        }
                    }

                    // collect winner id
                    $winner_user_ids[] = $user->user_id;
                }

                // mark all votes for this media as inactive
                $this->__models['media_vote_model']->inactivate_votes(strtotime($schedule_date), $datetime);

                // send push notification to all user about winnings
                // get other users
                $raw_user_ids = $this->__models['user_model']
                    ->whereNotIn("user_id", $winner_user_ids)
                    ->get(array("user_id"));

                if (isset($raw_user_ids[0])) {
                    foreach ($raw_user_ids as $raw_user_id) {
                        $user = $this->__models['user_model']->get($raw_user_id->user_id);

                        // update badge count of target user
                        //$user->count_notification = $user->count_notification + 1; // donot increament count
                        $this->__models['user_model']->set($user->user_id, (array)$user);

                        // send push notification to user about winning
                        if ($user->device_token != "" && $user->is_notify_request == 1) {
                            // get updates count
                            $updates_data = $this->__models["notification_model"]->updates($user->user_id);

                            // prepare notification data
                            $noti_data = array(
                                "alert" => "Today's winners are announced, come and see who make it to Top 20 for today.",
                                "target_user_id" => $user->user_id,
                                //"type" => "declare_winner",
                                "type" => 105,
                                "sound" => $user->sound,
                                //"badge" => (int)$user->count_notification
                                "badge" => $updates_data["update"]["badge_count"]
                            );

                            if ($user->device_type == "ios") {
                                $r = $this->__models['notification_model']->pn_ios($user->device_token, $noti_data, FALSE);
                            } else {
                                $r = $this->__models['notification_model']->pn_android($user->device_token, $noti_data);
                            }
                        }

                    }
                }

                if (MASTER_DB_HOST == "74.208.8.145") {
                    $this->__models['winner_model']->cron_winners_email($schedule_date, $winner_user_ids);
                }

                // close ios socket for multiple notifications
                $this->__models['notification_model']->close_ios();

            }

        }

        // write log
        $fo = fopen($_SERVER['DOCUMENT_ROOT'] . "/" . ADD_PATH . APP_ALIAS . "test.log", "a+");
        @fwrite($fo, date("Y-m-d H:i:s") . " : Cron for : " . $schedule_date . " \n");
        fclose($fo);

        exit("done");
    }


    /**
     * inner_testing
     *
     * @return HTML
     */
    public function inner_testing()
    {
        // write log
        $fo = fopen($_SERVER['DOCUMENT_ROOT'] . "/test.log", "a+");
        @fwrite($fo, date("Y-m-d H:i:s") . " : Inner Log : " . $schedule_date . " \n");
        fclose($fo);

        exit("done");
    }


    /**
     * Notify Followers
     *
     * @return HTML
     */
    public function notify_followers()
    {
        // init other configs
        set_time_limit(0);
        ignore_user_abort(TRUE);

        // init models
        $this->__models['user_model'] = new AppUser;
        $this->__models['dish_model'] = new Dish;
        $this->__models['notification_model'] = new Notification;
        $this->__models['category_model'] = new Category;

        // get unsent notifications
        $raw_records = $this->__models['notification_model']
            ->select("notification_id")
            ->where("is_sent", 1)// -temp
            ->where("entity", "=", "dish_create")
            ->get();

        //<Azfar> has uploaded a new video in <category>
        //<Azfar> has uploaded a new image in <category>

        // if has records
        if (isset($raw_records[0])) {
            // open ios socket for multiple notifications
            $this->__models['notification_model']->open_ios();

            foreach ($raw_records as $record) {
                $notification = $this->__models['notification_model']->get($record->notification_id);
                //$notification = $record;
                $user = $this->__models['user_model']->get($notification->user_id);
                $target_user = $this->__models['user_model']->get($notification->target_user_id);
                //$target_user = $this->__models['user_model']->get(315); // - temp
                $dish = $this->__models['dish_model']->get($notification->entity_id);

                $r = '';
                // if valid media
                if ($dish !== FALSE) {
                    // get category
                    //$category = $this->__models['category_model']->get($dish->category_id);

                    // send notification to receiver
                    if ($target_user->device_token != "" && $target_user->is_notify == 1) {

                        // sound for notification
                        $sound = $target_user->sound;

                        // get updates count
                        $updates_data = $this->__models["notification_model"]->updates($target_user->user_id);

                        // prepare notification data
                        $noti_data = array(
                            //"alert" => $user->first_name." ".$user->last_name." has cooked ".$dish->title. " in ".$category->name,
                            "alert" => $user->name . " has cooked " . $dish->title,
                            "dish" => $this->__models['dish_model']->get_data($dish->dish_id, $target_user->user_id),
                            "target_user_id" => $target_user->user_id,
                            "type" => 101,
                            "sound" => $sound,
                            "badge" => $updates_data["update"]["badge_count"]
                        );

                        if ($target_user->device_type == "ios") {
                            $r = $this->__models['notification_model']->pn_ios($target_user->device_token, $noti_data, FALSE);
                        } else {
                            $r = $this->__models['notification_model']->pn_android($target_user->device_token, $noti_data);
                        }

                        // update target user notification counter
                        $target_user->count_notification = $updates_data["update"]["badge_count"];
                        $this->__models['user_model']->set($target_user->user_id, (array)$target_user);
                    }
                }

                $notification->is_sent = 1;
                //$this->__models['notification_model']->set($notification->notification_id,(array)$notification); // -temp commented
            }

            // close ios socket for multiple notifications
            $this->__models['notification_model']->close_ios();
        }

        exit("done");
    }


    /**
     * Open Graph
     *
     * @return Response
     */
    public function og_media()
    {
        // init models
        $this->__models['media_model'] = new Media;

        // get params
        $media_id = trim(strip_tags(Input::get('media_id', 0)));
        $media_id = $media_id == "" ? 0 : $media_id; // set default value

        $media = $this->__models['media_model']->get($media_id);

        if ($media_id == 0) {
            $this->_assignData['message'] = 'Please enter User ID';
        } else if ($media === FALSE) {
            $this->_assignData['message'] = 'Invalid User Request';
        } else {

            $this->_assignData['media'] = $this->__models['media_model']->get_data($media->media_id);

            return view("og/media", $this->_assignData)->with($this->__models);
        }
    }


    /**
     * Set web username
     *
     * @return Response
     */
    public function removeWebUsername()
    {
        // init models
        $this->__models["user_model"] = new AppUser;

        // get params
        $user_id = intval(trim(strip_tags(Input::get('user_id', 0))));

        // get data
        $user = $this->__models['user_model']->get($user_id);

        if ($user === FALSE) {
            $apiData['message'] = "Invalid user request";
        } elseif ($user !== FALSE && $user->status == 0) {
            // kick user
            $apiData['kick_user'] = 1;
            // message
            $apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
        } elseif ($user !== FALSE && $user->status > 1) {
            // kick user
            $apiData['kick_user'] = 1;
            // message
            $apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
        } else {
            // init models
            //$this->__models["preference_model"] = new Preference;

            // update
            $user->web_username = "";
            $user->web_url = "";
            // update data
            $this->__models['user_model']->set($user->user_id, (array)$user);

            // output data
            $data["user"] = $this->__models['user_model']->getData($user->user_id);

            // set message
            $apiData['message'] = "Username successfully removed";
            // assign to output
            $apiData['data'] = $data;
        }


        return $apiData;
    }


    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function testNotification()
    {
        // init models
        $history_notification_model = new HistoryNotification;
        $user_model = new AppUser;

        // defaults
        $history_notification = $history_notification_model->get(3);
        $user = $user_model->get(1);
        $target_user = $user_model->get(1);


        if ($history_notification->wildcards != "") {
            $wildcards = explode(",", $history_notification->wildcards);
            $replacers = explode(",", $history_notification->replacers);
            $history_notification->body = str_replace($wildcards, $replacers, $history_notification->body);
            eval("\$history_notification->body = \"$history_notification->body\";");
        }

        echo $history_notification->body;
        //var_dump($body);
        exit;

    }


    /**
     * test
     *
     * @return
     */
    public function test()
    {
        $url = url('test/background_task');
        var_dump('go write');
        background_call($url, 80, 'POST', ['test' => 123]);
        var_dump('end method');

    }


    /**
     * Test background task
     *
     * @return
     */
    public function testBackgroundTask()
    {
        set_time_limit(0);

        $started_at = microtime(TRUE);

        try {
            //for ($i = 0; $i <= 1000000; $i++) {
            for ($i = 0; $i <= 1000; $i++) {
                echo "run $i \n";
            }
        } catch (\Exception $e) {
            $i = $e->getMessage();
        }


        $i .= "\r\n\r\n" . load_time($started_at);

        $fo = fopen(getcwd() . '/test_background.log', 'w+');
        fwrite($fo, $i);
        fclose($fo);
    }

    

}
