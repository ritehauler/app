<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::get('/', function () {
    return view('welcome');
});

Route::get('/backend', function () {
    return redirect('backend/login');
});

//Route::any('/', 'IndexController@index');


Route::get('home', 'HomeController@index');
// - under development
//Route::match(array('get', 'post'),DIR_API.'user/reportdp', rtrim(ucfirst(DIR_API), '/').'\Index@ud');

// Index routes
Route::get('confirm_signup', 'IndexController@confirm_signup');
Route::any('confirm_forgot', 'IndexController@confirm_forgot');
// page
Route::any('page/terms', 'PageController@terms');
Route::any('page/privacy', 'PageController@privacy');
Route::any('page/faq', 'PageController@faq');
Route::any('page/about', 'PageController@about');
// user
Route::any('/@{username}', 'UserController@webProfile');
Route::get('user/removeWebUsername', 'IndexController@removeWebUsername');
// Check (CSRF protected)
Route::get('check/admin_auth', 'CheckController@adminAuth');


// Test
Route::get('test/', 'IndexController@test');
Route::post('test/background_task', 'IndexController@testBackgroundTask');

// Cache
Route::get('cache/clear', 'IndexController@clearCache');
// multi uploader
Route::any('ajax/multi_uploader', 'IndexController@multiUploader');

// Open Graph
Route::any('og/share', 'OpenGraphController@share');

// Flurry
Route::any('flurry/pull_analytics', 'FlurryController@pullAnalyticsData');

// Administrator : Q&A : Content
Route::any(DIR_ADMIN . 'qa_content/', rtrim(ucfirst(DIR_ADMIN), '/') . '\QAContentController@index');
Route::any(DIR_ADMIN . 'qa_content/ajax/listing', rtrim(ucfirst(DIR_ADMIN), '/') . '\QAContentController@ajaxListing');
Route::any(DIR_ADMIN . 'qa_content/add', rtrim(ucfirst(DIR_ADMIN), '/') . '\QAContentController@add');
Route::any(DIR_ADMIN . 'qa_content/update/{id}', rtrim(ucfirst(DIR_ADMIN), '/') . '\QAContentController@update');
Route::any(DIR_ADMIN . 'qa_content/image_browser', rtrim(ucfirst(DIR_ADMIN), '/') . '\QAContentController@imageBrowser');
// Administrator : Queue Upload
Route::any(DIR_ADMIN . 'qa_content/queue_upload', rtrim(ucfirst(DIR_ADMIN), '/') . '\QAContentController@queueUpload');

// Administrator : Q&A : Tag
Route::any(DIR_ADMIN . 'tag/', rtrim(ucfirst(DIR_ADMIN), '/') . '\TagController@index');
Route::any(DIR_ADMIN . 'tag/ajax/listing', rtrim(ucfirst(DIR_ADMIN), '/') . '\TagController@ajaxListing');
Route::any(DIR_ADMIN . 'tag/add', rtrim(ucfirst(DIR_ADMIN), '/') . '\TagController@add');
Route::any(DIR_ADMIN . 'tag/update/{id}', rtrim(ucfirst(DIR_ADMIN), '/') . '\TagController@update');
Route::any(DIR_ADMIN . 'tag/image_browser', rtrim(ucfirst(DIR_ADMIN), '/') . '\TagController@imageBrowser');
// Administrator : Queue UploadTagController@imageBrowser
Route::any(DIR_ADMIN . 'tag/queue_upload', rtrim(ucfirst(DIR_ADMIN), '/') . '\TagController@queueUpload');

// Administrator : Q&A : Bag
Route::any(DIR_ADMIN . 'bag/', rtrim(ucfirst(DIR_ADMIN), '/') . '\BagController@index');
Route::any(DIR_ADMIN . 'bag/ajax/listing', rtrim(ucfirst(DIR_ADMIN), '/') . '\BagController@ajaxListing');
Route::any(DIR_ADMIN . 'bag/add', rtrim(ucfirst(DIR_ADMIN), '/') . '\BagController@add');
Route::any(DIR_ADMIN . 'bag/update/{id}', rtrim(ucfirst(DIR_ADMIN), '/') . '\BagController@update');
Route::any(DIR_ADMIN . 'bag/image_browser', rtrim(ucfirst(DIR_ADMIN), '/') . '\BagController@imageBrowser');
// Administrator : Queue Upload
Route::any(DIR_ADMIN . 'bag/queue_upload', rtrim(ucfirst(DIR_ADMIN), '/') . '\BagController@queueUpload');

//General Routes
$general_panel_dir = str_replace("/", '\\',ucwords(config("panel.DIR")));
Route::any('getoptions', $general_panel_dir . 'EntityAjaxController@getoptions');
Route::any('getItemData', $general_panel_dir . 'EntityAjaxController@getItemData');
Route::any('getRoleOptions', $general_panel_dir . 'EntityAjaxController@getRoleOptions');

Route::any('getOrderCart', $general_panel_dir . 'EntityAjaxController@getOrderCart');

//Dashboard Widgets routes
Route::any('totalCountStats', $general_panel_dir . 'EntityAjaxController@totalCountStats');
Route::any('totalSalesChart', $general_panel_dir . 'EntityAjaxController@totalSalesChart');
Route::any('topProductChart', $general_panel_dir . 'EntityAjaxController@topProductChart');
Route::any('orderByProductType', $general_panel_dir . 'EntityAjaxController@totalOrderByProductType');
Route::any('getListWidgets', $general_panel_dir . 'EntityAjaxController@getListWidgets');
Route::any('topDriver', $general_panel_dir . 'EntityAjaxController@topDriverChart');
Route::any('topCustomerChart', $general_panel_dir . 'EntityAjaxController@topCustomerChart');
Route::any('topCustomerList', $general_panel_dir . 'EntityAjaxController@topCustomerList');
Route::any('topDriverList', $general_panel_dir . 'EntityAjaxController@topDriverList');
Route::any('activePromotion', $general_panel_dir . 'EntityAjaxController@activePromotion');
Route::any('activeCoupon', $general_panel_dir . 'EntityAjaxController@activeCoupon');
Route::any('topDeliverySlot', $general_panel_dir . 'EntityAjaxController@topDeliverySlots');
Route::any('topCity', $general_panel_dir . 'EntityAjaxController@topCity');
Route::any('peakOrderTime', $general_panel_dir . 'EntityAjaxController@peakOrderTime');
Route::any('getProductsByType', $general_panel_dir . 'EntityAjaxController@getProductsByType');
Route::any('getTopVehicles', $general_panel_dir . 'EntityAjaxController@getTopVehicles');

Route::get('getDeliverySlot', $general_panel_dir . 'EntityAjaxController@getDeliverySlot');

//Syetem Notification routes
Route::any('countNotification', $general_panel_dir . 'EntityNotificationController@countNotification');
Route::any('listNotification', $general_panel_dir . 'EntityNotificationController@listNotification');

//cron services routes
$cron_dir = str_replace("/", '\\',ucwords(DIR_CRON));
Route::any('applyPromotion', $cron_dir . 'PromotionController@apply');
Route::any('clearCoupon', $cron_dir . 'CouponController@clearCoupon');

$data_cron_dir = str_replace("/", '\\',ucwords('DataScript/'));
Route::any('clearData', $data_cron_dir . 'DataScriptController@clearData');
Route::any('updateProductCount', $data_cron_dir . 'DataScriptController@updateProductCount');
Route::any('compressImages', $data_cron_dir . 'DataScriptController@compressImages');
Route::any('update_data', $data_cron_dir . 'DataScriptController@updateDate');
Route::any('clearSystemEntity', $data_cron_dir . 'DataScriptController@clearSystemEntity');
Route::any('assign_order', $cron_dir . 'OrderController@assignOrder');
Route::any('auto_decline', $cron_dir . 'OrderController@autoDecline');

//Rite Hauler Routes
Route::get('getCityByState', $general_panel_dir . 'EntityAjaxController@getCityByState');
Route::any('getDeliveryProfessional', $general_panel_dir . 'EntityAjaxController@getDeliveryProfessional');
Route::any('getTruckInfo', $general_panel_dir . 'EntityAjaxController@getTrucksById');
Route::any('getTruckList', $general_panel_dir . 'EntityAjaxController@getTruckListByVol');
Route::any('getOrderStatus', $general_panel_dir . 'EntityAjaxController@getOrderStatus');
Route::any('updateOrderStatus', $general_panel_dir . 'EntityAjaxController@updateOrderStatus');
Route::get('getDriverVehicle', $general_panel_dir . 'EntityAjaxController@getVehicleInfo');
Route::get('getDriverLocation', $general_panel_dir . 'EntityAjaxController@getDriverLocation');
Route::get('order_calendar', $general_panel_dir . 'EntityAjaxController@getOrderCalendar');
Route::get('order_calendar_content', $general_panel_dir . 'EntityAjaxController@getOrderCalendarContent');

//Share url
Route::get('share/{slug}/{id}','OrderController@shareView');

// - thumbnail
Route::get('/thumb/{path}/{size}/{name}/{thumb}', function ($path = NULL, $size = NULL, $name = NULL, $thumb = null) {
    if (!is_null($path) && !is_null($size) && !is_null($name)) {
        $path = base64_decode($path);
        $size = explode('x', $size);
        $cache_image = Image::cache(function ($image) use ($path, $size, $name, $thumb) {
            $thumb = !$thumb ? $name : $thumb;
            //return $image->make(url("/".$path.$name))->resize($size[0], $size[1]);
            return $image->make(\URL::to($path .$name))->fit($size[0])->save($path.$thumb);
        }, 10); // cache for 10 minutes
        return Response::make($cache_image, 200, array('Content-Type' => 'image'));
    } else {
        abort(404);
    }
});

// - mask
Route::get('/mask/{entity}/{type}/{name}', function ($entity = NULL, $type = NULL, $name = NULL) {
    if (!is_null($entity) && !is_null($type) && !is_null($name)) {

        /*
        * PHP GD
        * adding watermark to an image with GD library
        */
        //$path = $entity == "user" ? getcwd()."/".DIR_USER_IMG : $_SERVER['DOCUMENT_ROOT']."/".DIR_DISH;
        $path = getcwd() . "/" . DIR_RAW_FILES;
        //$path = $entity == "user" ? $_SERVER['DOCUMENT_ROOT']."/".DIR_USER_IMG : $_SERVER['DOCUMENT_ROOT']."/".DIR_DISH;
        if (file_exists($path . "wm-" . $name)) {
            header('Content-type: image/png');
            $img_data = file_get_contents($path . "wm-" . $name);
            echo $img_data;
            return;
        } else {
            $watermark_img = $type . ".png";

            // Load the watermark and the photo to apply the watermark to
            $stamp = imagecreatefrompng(getcwd() . "/" . DIR_WATERMARK . $watermark_img);
            //$stamp = imagecreatefrompng($_SERVER['DOCUMENT_ROOT']."/".DIR_WATERMARK.$watermark_img);
            $im = imagecreatefromjpeg($path . $name);

            // Set the margins for the stamp and get the height/width of the stamp image
            $wm_img = getimagesize(getcwd() . "/" . DIR_WATERMARK . $watermark_img);
            //$wm_img = getimagesize($_SERVER['DOCUMENT_ROOT']."/".DIR_WATERMARK.$watermark_img);
            $main_img = getimagesize($path . $name);
            //var_dump($wm_img); exit;
            //$marge_right = 10;
            //$marge_bottom = 10;
            $marge_right = ($main_img[0] / 2) - ($wm_img[0] / 2);
            $marge_bottom = ($main_img[1] / 2) - ($wm_img[1] / 2);
            $sx = imagesx($stamp);
            $sy = imagesy($stamp);

            // Copy the stamp image onto our photo using the margin offsets and the photo
            // width to calculate positioning of the stamp.
            imagecopy($im, $stamp, imagesx($im) - $sx - $marge_right, imagesy($im) - $sy - $marge_bottom, 0, 0, imagesx($stamp), imagesy($stamp));

            // Output and free memory
            ob_start();
            header('Content-type: image/png');
            imagejpeg($im);
            $img_data = ob_get_clean();
            imagedestroy($im);
            // save into file
            echo $img_data;
            @file_put_contents($path . "wm-" . $name, $img_data);
            return;
        }
    } else {
        abort(404);
    }
});


/*
|--------------------------------------------------------------------------
| Include all Routes
|--------------------------------------------------------------------------
|
| Here we include all php files that exists in Routes directory
|
*/
$dir = __DIR__ . "/Routes/";
$files = scandir($dir);
if ($files) {
    foreach ($files as $file) {
        if (preg_match("@(\.php)$@", $file)) {
            include_once($dir . $file);
        }
    }
}

Route::any('entities/other_item/updateOtherItem', $ctrl_dir . 'EntityBackController@updateOtherItemStatus');
