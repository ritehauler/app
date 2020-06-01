<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);


require "Curl.php";
// get credentials
/*$account_sid = "ACe26fe3c1b0ee775112a1b717b84508da"; 
$auth_token = "255fefdcd4f9fc6e140a952290ef08bd";
$account_no = "+17862506694";*/

$account_sid = "AC4c1a0aabf8651a6fe76777216b9bf52c"; 
$auth_token = "e011ea5b79e0acf34b94b4c7a21a6efa";
$account_no = "+12027590146";

$partners = array(
	/*"shafiq" => "03452703022",
	"anas" => "03333408669",
	"ameena" => "03453309237",
	"adeel" => "03328790580",*/
	"salman" => "3222517499"
);

// set api call url
$url = "https://api.twilio.com/2010-04-01";
$url .= "/Accounts/$account_sid/Messages";

/*$body = "Rate 5 and Promote Ameena's app. Category : Fashion. Link : https://play.google.com/store/apps/details?id=amee.fashion.mehndi";*/
$body = "Test Message";
// make curl call
$curl = new Curl;

foreach($partners as $key=>$val) {
	$curl->httpLogin($account_sid, $auth_token);
	$response = $curl->simple_post($url, array( 
		//'To' => "+92".trim($receiver->user_phone_no),
		'To' => "+92".$val,
		'From' => $account_no, 
		'Body' => $body,   
	));
	var_dump($response);
}

echo "Done";