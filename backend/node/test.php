<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);
/*
echo "<p>1</p>";
require "../config.php";
echo "<p>2</p>";
require '../vendor/autoload.php';
echo "<p>3</p>";
//require '../config/database.php';
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Cache\CacheManager as CacheManager;

use App\Http\Models\User;
use App\Http\Models\Notification;
//use Cache;
$capsule = new Capsule; 

$capsule->addConnection(array(
    'driver'    => 'mysql',
    'host'      => MASTER_DB_HOST,
    'database'  => MASTER_DB_NAME,
    'username'  => MASTER_DB_USER,
    'password'  => MASTER_DB_PASS,
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => ''
));

$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "<p>4</p>";
//use Illuminate\Database\Capsule\Manager as Capsule;

$notification_model = new Notification;
$notification_model->pn_ios("test",array(
	'sender_id'   => 1,
	'sender_name' => "test ad",
	'alert' => "asd sd asd asd",
	'type' => 'message')
);
echo "<p>5</p>";

$user_model = new User;
$data = User::find(1);
var_dump($data);
echo "<p>6</p>";

*/


require "Curl.php";
/*
//set POST variables
$url = 'http://cubixcube.com/mobile/outdoors/api/';
$post_array = array(
	"user_id" => 1,
	"message" => "test message here"
);

$url .= "app/categories";

// make curl call
$curl = new Curl;
$curl->httpHeader("api_email","salman.khimani@cubixlabs.com");
$curl->httpHeader("api_password", "apipass123");
$response = $curl->simple_post($url, $post_array);
var_dump($response);
*/

$url = "http://upgradebay.com/Products/ProductInfo.aspx?Product=5048954";

$ch = curl_init();

$header=array(
  'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.12',
  'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
  'Accept-Language: en-us,en;q=0.5',
  //'Accept-Encoding: gzip,deflate',
  'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7',
  'Keep-Alive: 115',
  'Connection: keep-alive',
);

curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
//curl_setopt($ch,CURLOPT_COOKIEFILE,'cookies.txt');
//curl_setopt($ch,CURLOPT_COOKIEJAR,'cookies.txt');
curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
$result=curl_exec($ch);

var_dump($result);

curl_close($ch);