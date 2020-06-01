<?php



namespace App\Http\Middleware;
use Session;
use Cookie; 
use Closure;

use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use App\Libraries\CustomHelper;

class FbAuth
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
	
		 $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		if (Session::has('users')  )
		{
				
		}
		
		if(isset($_SESSION['fbUserProfile']))
		{
			$data = array(
								'entity_type_id'	=> "11",
								'name'				=> $_SESSION['fbUserProfile']['name'],
								'first_name'		=> $_SESSION['fbUserProfile']['first_name'],
								'last_name'			=> $_SESSION['fbUserProfile']['last_name'],
								'platform_type'		=> 'facebook',
								'device_type'		=> 'none',
								'platform_id'		=> $_SESSION['fbUserProfile']['id'],
								'email'				=> $_SESSION['fbUserProfile']['email'],
								'status'			=> 1,
						  ); 

			foreach ( $_REQUEST as $key=>$value)
				if($request->input($key)!==null)
						$data[$key] = $value;
						
			\URL::forceRootUrl(url('/')); //  url messup fix
			//$response = json_encode(CustomHelper::internalCall($request,"api/entity_auth/social_login", 'POST',$data,false));
			//$json = json_decode($response,true); 
			//print_r($json); 
			return $next($request);
		}	
		else
		{

				$appId = '902201553268257'; //Facebook App ID
				$appSecret = '45dfb4827383399ad2c9d9f417b9352e'; //Facebook App Secret
				
				//$appId = '547070199013117'; //Mehran Facebook App ID
				//$appSecret = '85a38a289dd71be1d1213b5c7774848d'; //Mehran Facebook App Secret

				$redirectURL = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']; //Callback URL
				$fbPermissions = array('email');  //Optional permissions

				$fb = new Facebook(array(
					'app_id' => $appId,
					'app_secret' => $appSecret,
					'default_graph_version' => 'v2.2'
				));

				/* Get redirect login helper */

				$helper = $fb->getRedirectLoginHelper();
				// Try to get access token


				try {
					if (isset($_SESSION['facebook_access_token'])) {
						$accessToken = $_SESSION['facebook_access_token'];
					} else {
						$accessToken = $helper->getAccessToken();
					}
				} catch (FacebookResponseException $e) {
					//echo 'Graph returned an error: ' . $e->getMessage();

				} catch (FacebookSDKException $e) {
					//echo 'Facebook SDK returned an error: ' . $e->getMessage();

				}


				$loginURL = $helper->getLoginUrl($redirectURL, $fbPermissions);

				if(isset($accessToken))
				{
					$_SESSION['fbAccessToken'] = $accessToken;

				}
				$_SESSION['loginurl'] = $loginURL;
				if (isset($_SESSION['fbAccessToken']) && isset($_GET['code']))
				{



					if (isset($_SESSION['facebook_access_token'])) {
						$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
					} else {
						// Put short-lived access token in session
						$_SESSION['facebook_access_token'] = (string)$accessToken;

						// OAuth 2.0 client handler helps to manage access tokens
						$oAuth2Client = $fb->getOAuth2Client();

						// Exchanges a short-lived access token for a long-lived one
						$longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);
						$_SESSION['facebook_access_token'] = (string)$longLivedAccessToken;

						// Set default access token to be used in script
						$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
					}


					// Getting user facebook profile info
					try {
						$profileRequest = $fb->get('/me?fields=id,name,first_name,last_name,email,link,gender,locale,picture');
						$fbUserProfile = $profileRequest->getGraphNode()->asArray();

					} catch (FacebookResponseException $e) {
						echo 'Graph returned an error: ' . $e->getMessage();
						session_destroy();
						// Redirect user back to app login page
						header("Location: ./");
						exit;
					} catch (FacebookSDKException $e) {
						echo 'Facebook SDK returned an error: ' . $e->getMessage();
						exit;
					}

					session_unset();
					//session_destroy();
					

					$_SESSION['fbUserProfile'] = $fbUserProfile;


                    $json = json_decode(
                        json_encode(
                            CustomHelper::internalCall(
                                $request,
                                'api/entity_auth/social_login',
                                'POST',
                                [
                                    'entity_type_id' => 11,
                                    'name' => $_SESSION['fbUserProfile']['name'],
                                    'first_name' => $_SESSION['fbUserProfile']['first_name'],
                                    'last_name' => $_SESSION['fbUserProfile']['last_name'],
                                    'platform_type' => 'facebook',
                                    'device_type' => 'none',
                                    'platform_id' => $_SESSION['fbUserProfile']['id'],
                                    'email' => $_SESSION['fbUserProfile']['email'],
                                    'status' => 1,
                                    //'mobile_json' => 1,
                                ],
                                FALSE
                            )
                        ),
                        TRUE
                    );

                    if(isset($json['data']['entity_auth']))
                    {
                        session_unset();
                        $json = $json['data']['entity_auth'];
                        $data['entity_auth'] = $json;


                        if ($request->session()->has('users'))
                        {
                            $request->session()->forget('users');
                            $request->session()->push('users',$json);
                        }
                        else
                        {
                            $request->session()->push('users',$json);
                        }

                    }

					$_SESSION['logoutURL'] = $helper->getLogoutUrl($accessToken, url('/').'/fbSignout');
					
					return $next($request);
				} 
				else
				{
					return $next($request);
				}


			}

	}
}	
