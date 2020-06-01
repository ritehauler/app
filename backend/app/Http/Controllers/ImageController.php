<?php namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use View;
use Input;
use Log;
use Session;
use Validator;
use Carbon\Carbon;
use Request;
// load models
use App\Http\Models\User as UserModel;
use App\Http\Models\ApiMethod;
use App\Http\Models\ApiMethodField;
use App\Http\Models\ApiUser;
use App\Http\Models\Dish as DishModel;
use App\Http\Models\Category;
use App\Http\Models\Specifics; 
use App\Http\Models\DishSpecifics;
use App\Http\Models\DishImages;
use App\Http\Models\DishLike;
use App\Http\Models\DishVote;
use App\Http\Models\DishComment;
use App\Http\Models\DishReport;
use App\Http\Models\DishFollow;
use App\Http\Models\Message;
use App\Http\Models\Winner;
use App\Http\Models\Notification;

class ImageController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Welcome Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders the "marketing page" for the application and
	| is configured to only allow guests. Like most of the other sample
	| controllers, you are free to modify or remove it as you desire.
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('guest');
	}

	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
		
		$this->__models['dishimages_model'] = new DishImages;
		
				  
				  $host = $_SERVER['HTTP_HOST'];
				  try{
				  echo '<br>socket is open</br>';	  
				  $socketcon = fsockopen($host, 80, $errno, $errstr, 10);
				  $r_url = url()."/notify_followed_user";
				  
				  if($socketcon) {
					  echo '<br>socket is open</br>';					 
				  $socketdata = "GET $r_url HTTP 1.1\r\nHost: $host\r\nConnection: Close\r\n\r\n";
				  fwrite($socketcon, $socketdata);
				  fclose($socketcon);
				  }else{
					  echo '<br>socket is not open</br>';
				  }
				  }catch(Exception $e)
				  {
				    print_r($e);
				  }
				  exit;
		?>
        <html>
        <head>
        <meta name="csrf_token" content="<?php echo csrf_token(); ?>" />
        </head>
        <body>
        <form enctype="multipart/form-data" method="post">
         <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
        <input type="file" name="image_1">
        <input type="file" name="image_2">
        <input type="file" name="image_3">
        <input type="submit">
        
        </form>
        
        <?php
		
		 $dish_id = 50;
		 $user_id = 287; 
		for($i=1; $i<=3; $i++) 
			{
				$images = 'image_'.$i;				   
				if(Input::file($images) and Request::hasFile($images))
				{
					$image1 = Input::file($images);
					$imageName = "image$i-".$user_id."-".$dish_id.'.' .$extension = $image1->getClientOriginalExtension();;
					//@file_put_contents(getcwd()."/".DIR_DISH.$dish_image,base64_decode($image,true));
					$path =getcwd()."/".DIR_DISH;
					$image1->move($path, $imageName);
					//$image1->move($path);
					//$image1->save();
					
					Log::emergency($image1);
					Log::alert($image1);
					Log::critical($image1);
					Log::error($image1);
					Log::warning($image1);
					Log::notice($image1);
					Log::info($image1);
					Log::debug($image1);
					
					$dish_image['dish_id'] = $dish_id;
					$dish_image['image'] = $imageName;
					//$dish_image['status'] = 1;
					$dish_image['datetime'] = date('Y-m-d H:i:s');
					$dish_image['updated_datetime'] = date('Y-m-d H:i:s');
					
					$this->__models['dishimages_model']->put($dish_image);
					unset($dish_image);					
				}
			}
		?>
        </body>
        </html>
        <?php	
		
		//return view('index');
	}
	
	
	public function privacy()
	{
		return view('privacy');
	}
	
	
	public function terms()
	{
		return view('terms');
	}

}
