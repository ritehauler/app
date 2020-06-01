<?php 
require "../config.php";
require "Curl.php";

// api url
$api_url = API_ACCESS_URL;


function send($sender_id,$receiver_id,$msg,$imageName,$time, $is_unread = 0){
		// get variables
		global $api_url;
		// init curl
		$curl = new Curl;
		$curl->httpHeader("api_email",API_ACCESS_EMAIL);
		$curl->httpHeader("api_password", API_ACCESS_PASS);
		
		// api method
		$api_url .= "message/send_notification";
		// post params
		$post_params = array(
			"user_id" => $sender_id,
			"target_user_id" => $receiver_id,
			"message" => $msg,
			"time" => $time,
			"is_unread" => $is_unread
		);
		// call api
		$response = $curl->simple_post($api_url, $post_params);
		$fo = fopen("push.log","a+");
		fwrite($fo,date("Y:m:d H:i:s")." : ".json_encode($response)."\n");
		fclose($fo);
}