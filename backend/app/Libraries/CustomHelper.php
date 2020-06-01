<?php namespace App\Libraries;

use Illuminate\Http\Request;
use Carbon\Carbon;
/**
 * Class CustomHelper
 */
Class CustomHelper
{
    public static $mobileJson = 0;
    private static $_hookPath = "\\App\\Http\\Hooks\\";
    private static $_collector = [];

    /**
     * Constructor
     *
     * @param string $url URL
     */
    public function __construct()
    {

    }

    /**
     * @param bool $panel
     * @return string
     */
    public static function getPanelPath($panel = false)
    {
        if (!$panel) {
            $entity_session_identifier = config("panel.SESS_KEY");
            if (\Session::has($entity_session_identifier . "auth")) {
                $auth = \Session::get($entity_session_identifier . "auth");
                $panel = \Session::get($entity_session_identifier . "department");
                if (isset($auth->entity)) {
                    //$panel = $auth->entity->panel;
                    return config("panel.DIR") . $panel . '/';
                }
            } else {
                $panel = \Route::current()->getParameter('department');
            }
        }

        return config("panel.DIR") . $panel . '/';
    }

    /**
     * @param bool $panel
     * @return string
     */
    public static function getSegments(Request $request)
    {
        //Checking module Authentication
        $prefix = trim($request->route()->getPrefix(), '/');
        $parameters = $request->route()->parameters();

        foreach ($parameters as $key => $parameter) {
            $prefix = str_replace('{' . $key . '}', $parameter, $prefix);
        }
        return explode('/', trim(substr(strstr($request->path(), $prefix), strlen($prefix)), '/'));
    }

    /**
     * @param Request $request
     * @param $url
     * @param $method
     * @param array $params
     * @param bool $is_param_merge
     * @return bool|mixed
     */

    public static function internalCall(Request $request, $url, $method, $params = array(), $is_param_merge = true)
    {
        if ($is_param_merge)
            $request->merge($params);
        else
            $request->replace($params);

        Self::_extraParamOperation($request);
        $url = str_replace(\URL::to("/"), "", $url);

        $_SERVER["PHP_AUTH_USER"] = API_ACCESS_USER;
        $_SERVER["PHP_AUTH_PW"] = API_ACCESS_PASS;
        $_SERVER["REQUEST_URI"] = $url;
        $method = strtolower($method);


        switch (strtolower($method)) {
            case  'get':
                $request = \Request::create($url, 'GET');
                break;
            case  'post':
                $request = \Request::create($url, 'POST', $params);
                break;
            case  'file':
                $request = \Request::create($url, 'POST', $params, null, \Request::file('file'));
                break;
            default:
                print 'Method is not defined';
                return false;
        }

        $content = trim(\Route::dispatch($request)->getContent());
        $ret = json_decode($content);

        if (isset($ret->jsonEditor) && $ret) {
            return json_decode($ret->jsonEditor);
        } else {
            // if is valid JSON
            if (is_array(json_decode(json_encode($ret), true))) {
                return $ret;
            } else {
                exit($content);
            }
            //return json_decode(json_encode($ret));
        }
    }

    /**
     * @param Request $request
     * @param $url
     * @param $method
     * @param array $params
     * @param bool $is_param_merge
     * @return bool|mixed
     */
    public static function appCall(Request $request, $url, $method, $params = array(), $is_param_merge = true)
    {
        /*if ($is_param_merge)
            $request->merge($params);
        else
            $request->replace($params);*/
        // combine mobile json param
        if ($request->input('mobile_json', null) !== null && self::$mobileJson == 0) {
            self::$mobileJson = $request->input('mobile_json', 0);
        }
        // combine if not given in param
        if (isset($params['mobile_json'])) {
            // nothing
        } else {
            $params['mobile_json'] = self::$mobileJson;
        }
        // add csrf token so it does not ask for authentication
        $params['_token'] = csrf_token();

        self::_extraParamOperation($request);
        $url = str_replace(\URL::to("/"), "", $url);

        /*$_SERVER["PHP_AUTH_USER"] = config("service_provider.AUTH_USERNAME");
        $_SERVER["PHP_AUTH_PW"] = config("service_provider.AUTH_PASSWORD");
        $_SERVER["REQUEST_URI"] = $url;*/
        $method = strtolower($method);


        /*switch (strtolower($method))
        {
            case  'get':
                $request = \Request::create($url, 'GET');
                break;
            case  'post':
                $request = \Request::create($url, 'POST', $params);
                break;
            case  'file':
                $request = \Request::create($url, 'POST', $params, null, \Request::file('file'));
                break;
            default:
                print 'Method is not defined';
                return false;
        }*/

        $base_url = url('/'); // url messup fix
        $req = \Request::create($url, $method, $params);
        $content = app()->handle($req)->getContent();
        //$content = trim(\Route::dispatch($request)->getContent()); // -test
        $ret = json_decode($content);
        \URL::forceRootUrl($base_url); //  url messup fix

        if (isset($ret->jsonEditor) && $ret) {
            return json_decode($ret->jsonEditor);
        } else {
            // if is valid JSON
            if (is_array(json_decode(json_encode($ret), true))) {
                return $ret;
            } else {
                //return json_decode(json_encode($content));
                // prepare error
                $resp_err = array('error' => 1, 'message' => $content);
                return $resp_err;
            }
            //return json_decode(json_encode($ret));
        }
    }


    private static function _extraParamOperation(Request $request)
    {
        if (isset($request->start) && isset($request->length)) {
            $extra_param['offset'] = $request->start;
            $request->merge($extra_param);
        }
    }


    /**
     * call Hook
     *
     * @return Response
     */
    public static function hookData($class, $method, $request, $data)
    {
        $class_path = self::$_hookPath . $class;
        // if class exists
        if (class_exists($class_path)) {
            $class = new $class_path;
            $method_exists = method_exists($class, $method);
            if ($method_exists) {
                $data = $class->{$method}($request, $data);
            }
        }
        return $data;
    }

    public static function getIdPositionKanban($decision_container, $row_data)
    {
        $ids = explode(',',$decision_container->ids);
        $target_id = explode('.',$row_data->assign_to)[0];
        $counter = 0;
        foreach($ids as $id){
            if($target_id == $id)
                return $counter;
            $counter++;
        }
        return count($ids) - 1;
        return 'not found';
    }

    public static function getUserAsteriskPositionKanban($disission_container, $row_data)
    {
        $y_axis = 1;
        self::$_collector['callback_kanban_uri'] = 'updateUser/state';
        $y_axis_cont = explode('.',$row_data->assign_to);
        if(isset($y_axis_cont[2])) {
            if ($y_axis_cont[2] == '*') {
                self::$_collector['callback_kanban_uri'] = 'assignUser';
                $y_axis = 0;
            }
        }
        return $y_axis;
    }

    public static function getKanbanCallBackUri($row, $data)
    {
        $uri = '';
        $assign_to_exploded = explode('.',$row->assign_to);
        if($data['is_admin'])
            $uri = self::$_collector['callback_kanban_uri'];

        if(isset($assign_to_exploded[0]) && isset($assign_to_exploded[1]))
        if($assign_to_exploded[0] == $data['department_id'] && $assign_to_exploded[1] == $data['role_id'])
            $uri = self::$_collector['callback_kanban_uri'];

        return $uri;
    }

    public static function getKanbanCommentCallBackUri($row, $data)
    {
        $uri = '';
        $assign_to_exploded = explode('.',$row->assign_to);
        if($data['is_admin'])
            $uri = 'comment/add';

        if(isset($assign_to_exploded[0]) && isset($assign_to_exploded[1]))
        if($assign_to_exploded[0] == $data['department_id'] && $assign_to_exploded[1] == $data['role_id'])
            $uri = 'comment/add';

        return $uri;
    }

    public static function getEntityCallBackUri($department = 'super_admin')
    {
        return "$department/entities/order/view/";
    }

    public static function guzzleHttpRequest($url, $params = [], $headers = [], $type = 'POST')
    {
        try {
            $client = new \GuzzleHttp\Client();

            if(count($headers) > 0)
                $client->setDefaultOption('headers', $headers);

            return $client->send(
                $client->createRequest(
                    $type, $url,
                    [
                        'body' => $params
                    ]
                )
            )->getBody();
        }catch(Exception $e) {
            return ['error' => 1,
                'message' => $e->getMessage()
            ];
        }
    }

    public static function getSessionDataById($session_id)
    {
        $file_path = storage_path('framework/sessions/'.$session_id);
        if(file_exists($file_path))
         return unserialize(file_get_contents($file_path));
        return false;
    }

    public static function timeElapsedString($datetime, $full = false) {
        $now = new \DateTime();
        $ago = new \DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    /**
     * Genrate Unique title
     * @param $title
     * @return string
     */
    public static function generateUniqueTitle($title)
    {
        $hash_code = hash('md5',$title.'-'.time());
        $hash_code = substr(strtoupper($hash_code),-7);
        $title = $title.'-'.$hash_code;
        return $title;
    }

    /**
     * @param $string
     * @param string $replace
     * @return mixed
     */
    public function replaceSpaceWithString($string,$replace = '_')
    {
        $string = preg_replace('/\s+/', $replace, strtolower($string));
        return $string;
    }

    /**
     * @param array $array_set
     * @return array
     */
    public static function unsetNulls($array_set = array())
    {
        if (count($array_set) > 0) {
            foreach ($array_set as $key => $value) {
                if ($value === null) {
                    unset($array_set[$key]);
                }
            }

        }
        return $array_set;
    }


    /**
     * Get Panel Path
     *
     * @param bool $panel
     * @return string
     */
    public static function convertToCamel($convet_str)
    {
        return lcfirst(str_replace('_', '', ucwords($convet_str, '_')));
    }

    /**
     * @param $filter_type
     * @return object
     */
    public static function getDatesByFilterType($filter_type, $start_date = false, $end_date = false)
    {
        if($filter_type == "today"){
            $start_date = Carbon::now()->startOfDay()->format('Y-m-d H:i:s');
            $end_date = Carbon::now()->endOfDay()->format('Y-m-d H:i:s');
        }
        elseif($filter_type == "week"){
            $start_date = Carbon::now()->subDays(6)->startOfDay()->format('Y-m-d H:i:s');
            $end_date =  Carbon::now()->endOfDay()->format('Y-m-d H:i:s');
            //$start_date = Carbon::now()->startOfWeek()->format('Y-m-d H:i:s');
            //$end_date = Carbon::now()->endOfWeek()->format('Y-m-d H:i:s');
        }
        elseif($filter_type == "month"){

            $cal_end_date =  Carbon::now()->endOfDay()->format('Y-m-d');
            $start_date = Carbon::createFromFormat('Y-m-d', $cal_end_date)->subDays(29)->startOfDay()->format('Y-m-d H:i:s');

            $end_date =  Carbon::now()->endOfDay()->format('Y-m-d H:i:s');
           // $start_date = Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
           // $end_date = Carbon::now()->endOfMonth()->format('Y-m-d H:i:s');
        }
        else{
            $start_date = Carbon::createFromFormat('Y-m-d', $start_date)->startOfDay()->format('Y-m-d H:i:s');
            $end_date = Carbon::createFromFormat('Y-m-d', $end_date)->endOfDay()->format('Y-m-d H:i:s');
           // $start_date  = $start_date.' 00:00:00';
           // $end_date  = $end_date.' 23:59:59';
        }

        $return = (object)array('start_date'=>$start_date,'end_date'=>$end_date);
        return $return;
    //  print_r($return); exit;
    }

    /**
     * Create Time SLots considering 24 hours
     * @param $time_start
     * @param $time_slots
     * @return array
     */
    public static function createTimeSlots($time_start,$time_slots)
    {
        // $time_start = 12;
        //  $time_slot = 3;
        //$time_slot_range = $time_start/$time_slot;

        $time_start = 0;
        $time_slot_range = 4;
        $time_slots = 6;

        $time = array();
        for($count = 1; $count <= $time_slots; $count++){

            $end_time = $time_start+$time_slot_range;
            $time[] = $time_start.",".$end_time;
            $time_start = $end_time;
        }

        return $time;
    }

    /**
     * @param $time_slots
     * @param $hour
     * @return bool|string
     */
    public static function checkHourInTimeSlot($time_slots,$hour)
    {
        foreach($time_slots as $key=>$time_slot){

            //echo $time_slot;
            $time_slot_arr = explode(',',$time_slot);
            if (in_array($hour, range($time_slot_arr[0],$time_slot_arr[1])) ) {
                return $key."|".$time_slot;
            }
        }

        return false;
    }

    /**
     * Set Full Name format
     * @param $request_params
     * @return string
     */
    public static function setFullName($request_params)
    {
        $full_name = '';

        if(isset($request_params->first_name)){
            $full_name .=  $request_params->first_name;
        }

        if(isset($request_params->last_name) && !empty($request_params->last_name)){
            $full_name .= ' '.$request_params->last_name;
        }

        return $full_name;
    }

    /**
     * Display date time format
     * @param $date_time
     * @return false|string
     */
    public static function displayDateTime($date_time)
    {
        return date(DATE_TIME_FORMAT_ADMIN, strtotime($date_time));
    }

    /**
     * Limit words
     * @param $text
     * @param $limit
     * @return string
     */
   public static function limit_text($text, $limit) {
        $strings = $text;
        if (strlen($text) > $limit) {
            $words = str_word_count($text, 2);
            $pos = array_keys($words);
            if(sizeof($pos) >$limit)
            {
                $text = substr($text, 0, $pos[$limit]) . '...';
            }
            return $text;
        }
        return $text;
    }

    /**
     * @param $amount
     * @return float
     */
    public static function roundOffPrice($amount)
    {
        if($amount > 0)
        return round($amount,2);
        else
            return $amount;
    }


    /**
     * @param $eventTime
     * @return string
     */
    public static function getElapsedTime($eventTime)
    {
        $totaldelay = time() - strtotime($eventTime);
        if($totaldelay <= 0)
        {
            return 'just now';
        }
        else
        {
            if($days=floor($totaldelay/86400))
            {
                $totaldelay = $totaldelay % 86400;
                return $days.' days ago';
            }
            if($hours=floor($totaldelay/3600))
            {
                $totaldelay = $totaldelay % 3600;
                return $hours.' hours ago';
            }
            if($minutes=floor($totaldelay/60))
            {
                $totaldelay = $totaldelay % 60;
                return $minutes.' minutes ago';
            }
            if($seconds=floor($totaldelay/1))
            {
                $totaldelay = $totaldelay % 1;
                return $seconds.' seconds ago';
            }
        }
    }

    /**
     * @param $request
     * @return float|int|string
     */
    public static function calculateVolume($request)
    {
        $request = is_array($request) ? (object)$request : $request;
        $volume = '';
        if(isset($request->width) && !empty($request->width) ||
            isset($request->height) && !empty($request->height) ||
            isset($request->length) && !empty($request->length)
        ){

            if(isset($request->width) && !empty($request->width))
                $volume =  ($volume != '') ? $volume * $request->width :  $request->width;

            if(isset($request->height) && !empty($request->height))
                $volume =  ($volume != '') ? $volume * $request->height :  $request->height;

            if(isset($request->length) && !empty($request->length))
                $volume =  ($volume != '') ? $volume * $request->length :  $request->length;

        }

        return $volume;
    }

    /**
     * @param $amount
     * @return float|int
     */
    public static function convertKgToPound($amount)
    {
        if($amount > 0)  return $amount*config('constants.AMOUNT_KG_TO_POUND');
        return $amount;
    }

    /**
     * @param $amount
     * @return float
     */
    public static function convertPoundToKg($amount)
    {
        if($amount != '')  return number_format((float)$amount/2.205, 2, '.', '');
        return $amount;
    }

        public static function metersToMiles($meters){
        return round($meters * 0.000621371,2);
    }

    public static function getJoiningDays($date)
    {
        $created = new Carbon($date);
        $now = Carbon::now();
        $difference = $created->diff($now)->days;

        if($difference < 29){
            $key = 'Day';
            $value = $difference;
        }

        elseif($difference >= 29 && $difference < 365){
            $key = 'Month';
            $value = ceil($difference/30);
        }

        elseif($difference >= 365){

            $key = 'Year';
            $reminder = $difference % 365;

            if($reminder > 0){
                $value = floor($difference/365). "+";
            }else{
                $value = $difference/365;
            }

            if($value > 1) $key .= 's';
        }

        return array('key' => $key, 'value' => $value);
    }

    function check_date_is_within_range($start_timestamp, $end_timestamp, $today_timestamp)
    {

        return (($today_timestamp > $start_timestamp) && ($today_timestamp < $end_timestamp));

    }

    function lessThanTimeSlot($pickup,$dropof,$slots)
    {
        $return = 0;
        $total_count = count($slots);
        $slot = $slots[0];

        $pickup = strtotime($pickup);
        $dropof =  strtotime($dropof);

        $start_time = strtotime($slot->start_time);
        $end_time =  strtotime($slot->end_time);

        if($dropof < $start_time){
            $return = 1;
        }

        if($return == 0){
            if($this->check_date_is_within_range($start_time,$end_time,$dropof)){
                $return = -1;
            }
        }

        //echo "<pre>"; var_dump($return); exit;

        return $return;
    }

    function greaterThanTimeSlot($pickup,$dropof,$slots)
    {
        $return = 0;
        $total_count = count($slots);
        $last_index = $total_count-1;
        $slot = $slots[$last_index];

        $pickup = strtotime($pickup);
        $dropof =  strtotime($dropof);

        $start_time = strtotime($slot->start_time);
        $end_time =  strtotime($slot->end_time);

        if($pickup > $end_time){
            $return = 1;
        }
        //echo "<pre>"; var_dump($return); exit;

        return $return;
    }

    function sameTimeSlot($pickup,$dropof,$slots)
    {
        $return = 1;

        $pickup = strtotime($pickup);
        $dropof =  strtotime($dropof);

        foreach($slots as $slot){

            $start_time = strtotime($slot->start_time);
            $end_time =  strtotime($slot->end_time);

            if($pickup == $start_time && $dropof == $end_time){
                $return = 0;
                break;
            }
        }

        return $return;
    }

    function betweenTimeSlot($pickup,$dropof,$slots)
    {
        $return = 0;
        $pickup = strtotime($pickup);
        $dropof =  strtotime($dropof);
        $prevous_end_time = "";

        foreach($slots as $slot){

            $start_time = strtotime($slot->start_time);
            $end_time =  strtotime($slot->end_time);


            if($prevous_end_time != ""){
                if(($pickup > $prevous_end_time && $dropof > $prevous_end_time) && ($pickup < $start_time && $dropof < $start_time)){
                    //if($dropof > $prevous_end_time && $dropof < $start_time){
                    $return = 1;
                    break;
                }

            }

            $prevous_end_time = $end_time;
        }

        return $return;
    }

    /**
     * @param $pickup
     * @param $dropof
     * @param $slots
     * @return int
     */
    public function isSlotAvailable($pickup,$dropof,$slots)
    {
        $slot_available = 0;
        $return = $this->lessThanTimeSlot($pickup,$dropof,$slots);
        if($return == 0){ //Not exist

            //Now check second condition
            $return = $this->greaterThanTimeSlot($pickup,$dropof,$slots);
            if($return == 0){

                if($this->sameTimeSlot($pickup,$dropof,$slots) == 1){
                    $slot_available = $return = $this->betweenTimeSlot($pickup,$dropof,$slots);
                }
                else{
                    $slot_available = 0;
                }
            }
            $slot_available = $return;

        }elseif($return == -1){ //slot not exist lie in first range
            $slot_available = 0;
        }
        else{
            //slot exist
            $slot_available = 1;
        }

        return $slot_available;
    }

    public static function getSettings()
    {
        $data = \DB::table('setting')->get(['key', 'value']);

        foreach ($data as $item) {
            $settings[trim($item->key)] = trim($item->value);
        }

        if (!empty($settings)) {
            \Config::set("services.stripe.secret", ($settings['stripe_mode'] == 'live') ? $settings['stripe_secret_live'] : $settings['stripe_secret_sandbox']);
        }

        return (!empty($settings)) ? $settings : false;
    }
}