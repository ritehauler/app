<?php namespace App\Http\Controllers;

use App\Http\Controllers\FacebookBaseController;
use Illuminate\Http\Request;

class FacebookController extends FacebookBaseController
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
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        // construct parent
        parent::__construct($request);
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
     * hook will be called everytime on successful token from fb
     *
     * @return Response
     */
    protected function _onSuccessfulToken(Request $request)
    {
        // extend me
        $response = $this->_fb->get('/me?fields=id,name,email', $this->_accessToken);
        $user = $response->getGraphUser();
        // save in db

    }


}
