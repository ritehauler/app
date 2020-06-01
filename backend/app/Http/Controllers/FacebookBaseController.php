<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FacebookBaseController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Facebook Controller
    |--------------------------------------------------------------------------
    |
    | This controller renders your application's "dashboard" for users that
    | are authenticated. Of course, you are free to change or remove the
    | controller as you wish. It is just here to get your app started!
    |
    */

    /**
     * Private vars
     */
    protected $_fb;
    protected $_fbConf;
    protected $_fbHelper;
    protected $_debug = APP_DEBUG;
    protected $_accessToken = null;
    /**
     * include routes that does not need to have authentication required
     */
    private $_excludeAuth = array(
        "facebook/login_redirect",
        "facebook/set_token"
    );
    /**
     * include routes that does not need to have authentication required
     */
    protected $_extraExcludeAuth = array();
    /**
     * data to be share in all views/method
     */
    protected $_assignData = array(
        "dir" => "facebook/"
    );


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        // init facebook configurations
        $this->_init();
        $check_auth = FALSE;
        // check exclude auth routes
        if (!in_array($request->path(), $this->_excludeAuth)) {
            $check_auth = TRUE;
        }


        if ($check_auth === TRUE) {
            $this->_checkFbAuth($request);
        }

        // assign fb objects/vars for usage in views
        $this->_assignData["fbConf"] = $this->_fbConf;
        $this->_assignData["fb"] = $this->_fb;
        $this->_assignData["fbHelper"] = $this->_fbHelper;
        $this->_assignData["accessToken"] = $this->_accessToken;
    }

    /**
     * initialize facebook configurations
     *
     * @return Response
     */
    private function _init($helper_type = "canvas")
    {
        // conf data
        $this->_fbConf = $this->__models['conf_model']->getBy('key', 'facebook');
        $this->_fbConf = json_decode($this->_fbConf->value);
        // init facebook object
        $this->_fb = new \Facebook\Facebook([
            'app_id' => $this->_fbConf->app_id,
            'app_secret' => $this->_fbConf->secret,
            'default_graph_version' => $this->_fbConf->api_version,
            //'default_access_token' => '{access-token}', // optional
        ]);
        // init helper
        if ($helper_type == "canvas") {
            $this->_fbHelper = $this->_fb->getCanvasHelper();
        }
    }

    /**
     * redirect to referrer
     *
     * @return Response
     */
    protected function _redirectToReferrer(Request $request)
    {
        //if ($request->path() == "facebook") {
        // redirect to referred route
        if (isset($_SESSION["redirect_to"])) {
            $url = $_SESSION["redirect_to"];
            unset($_SESSION["redirect_to"]);
            return \Redirect::to($url);
        }
        //}

    }

    /**
     * get access token
     *
     * @return Response
     */
    protected function _getAccessToken(Request $request)
    {
        // get access token from session
        $this->_accessToken = \Session::get("facebook_access_token", null);
        // if still null, get from fb helper
        if (!$this->_accessToken) {
            return $this->_accessToken = $this->_fbHelper->getAccessToken();
        } else {
            // if token is valid by retrieving graph user object
            try {
                // Returns a `Facebook\FacebookResponse` object
                $response = $this->_fb->get('/me?fields=id,name', $this->_accessToken);
            } catch (\Facebook\Exceptions\FacebookResponseException $e) {
                if ($this->_debug === TRUE) {
                    echo 'Graph returned an error: ' . $e->getMessage();
                }
                return null;
            } catch (\Facebook\Exceptions\FacebookSDKException $e) {
                if ($this->_debug === TRUE) {
                    echo 'Facebook SDK returned an error: ' . $e->getMessage();
                }
                return null;
            }
            //$user = $response->getGraphUser();
            // call hook on successful token reecived
            $this->_onSuccessfulToken($request);
        }
        return $this->_accessToken;

    }


    /**
     * check facebook authentication
     *
     * @return void
     */
    private function _checkFbAuth(Request $request)
    {
        $this->_accessToken = $this->_getAccessToken($request);

        if (!isset($this->_accessToken)) {
            //echo 'No OAuth data could be obtained from the signed request. User has not authorized your app yet.';

            // put requests in session
            \Session::put("stored_request", $request->all());
            // put requests in session
            \Session::put("redirect_to", $request->path());
            /*
             * Fix : Laravel doesn't exchange session via \Session after redirect
             */
            $_SESSION["redirect_to"] = $request->path();
            // User denied the request
            \Redirect::to("facebook/login_redirect")->send();
            exit;
        }
    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        return view($this->_assignData["dir"] . __FUNCTION__, $this->_assignData);
    }

    /**
     * redirect user for facebook login
     *
     * @return Response
     */
    public function loginRedirect(Request $request)
    {
        $view_file = strtolower(preg_replace('/(.)([A-Z])/', '$1_$2', __FUNCTION__));
        $this->_assignData["login_url"] = $this->_getLoginUrl();
        return view($this->_assignData["dir"] . $view_file, $this->_assignData);
    }

    /**
     * get login url for authentication
     *
     * @return Response
     */
    protected function _getLoginUrl()
    {
        // override fbHelper to get redirecthelpers
        $this->_fbHelper = $this->_fb->getRedirectLoginHelper();
        $permissions = $this->_fbConf->login_permissions != "" ?
            explode(",", $this->_fbConf->login_permissions) : "public_profile"; // optional
        $loginUrl = $this->_fbHelper->getLoginUrl(\URL::to("facebook/set_token"), $permissions);
        return $loginUrl;
    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function setToken(Request $request)
    {
        if ($request->input("error", "") == "access_denied") {
            $this->_assignData["login_url"] = $this->_getLoginUrl();
            return view($this->_assignData["dir"] . "please_allow", $this->_assignData);
        }

        // for redirect Login
        // override helper
        $this->_fbHelper = $this->_fb->getRedirectLoginHelper();
        try {
            $this->_accessToken = (string)$this->_fbHelper->getAccessToken();
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            if ($this->_debug === TRUE) {
                echo 'Graph returned an error: ' . $e->getMessage();
            }
            return view($this->_assignData["dir"] . "other_error");
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            if ($this->_debug === TRUE) {
                echo 'Facebook SDK returned an error: ' . $e->getMessage();
            }
            return view($this->_assignData["dir"] . "other_error");
        }


        //  we got access token
        if ($this->_accessToken) {
            // call aut success event
            $this->_onAuthSuccess($request);
            // Logged in!
            \Session::put("facebook_access_token", $this->_accessToken);
            // Now you can redirect to another page and use the
            // access token from $_SESSION['facebook_access_token']
            \Redirect::to($this->_fbConf->app_url)->send();
        }

    }


    /**
     * check permissions
     *
     * @return Response
     */
    protected function _checkPermissions(Request $request)
    {
        try {
            $response = $this->_fb->get('/me/permissions', $this->_accessToken);
            $permissions = $response->getGraphEdge();
            foreach ($permissions as $item) {
                if ($item['permission'] == 'publish_actions') {
                    if ($item['status'] == 'declined') {
                        // save current route in session
                        \Session::put("redirect_to", $request->path());
                        /*
                         * Fix : Laravel doesn't exchange session via \Session after redirect
                         */
                        $_SESSION["redirect_to"] = $request->path();
                        // return
                        return false;
                    }
                }
            }
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            if ($this->_debug === TRUE) {
                echo 'Facebook SDK returned an error: ' . $e->getMessage();
            }
        }
        return true;
    }


    /**
     * hook function after auth success
     *
     * @return Response
     */
    protected function _onAuthSuccess(Request $request)
    {
        // extend me
    }

    /**
     * on successful token
     *
     * @return Response
     */
    protected function _onSuccessfulToken(Request $request)
    {
        // add user detail
    }


}
