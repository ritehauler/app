<?php

namespace App\Http\Controllers;

use App\Http\Models\SYSEntity;
use App\Http\Models\SYSEntityType;
use App\Libraries\ApiCurl;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;

// extra libs
use Illuminate\Http\Request;

use Session;

// load models
use App\Http\Models\Conf;
use App\Libraries\CustomHelper;


use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;


class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

    protected $__meta;
    protected $__models = array();
    protected $_modelPath = "\App\Http\Models\\";
    protected $_lang;
    protected $_in_detail;
    protected $_request_parameter;

    /**
     * Primary Library
     * @var $_pLib
     */
    protected $_pLib;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        // init models
        $this->__models['conf_model'] = new Conf;

        $detail = $this->__models['conf_model']->getBy('key', 'site');
        $detail = json_decode($detail->value);
        $this->__meta = $detail;

        // default lang
        $this->_setLang($request);

        // if session has indetail, apply that
        if (\Session::has("_in_detail")) {
            $this->_in_detail = \Session::get("_in_detail");
        } else {
            $this->_in_detail = isset($request['in_detail']) ? $request['in_detail'] : 1;
        }
        $request->merge(array('_in_detail' => $this->_in_detail));
        // set locale
        app()->setLocale($this->_in_detail);

        // if session has _request_parameter, apply that
        if (\Session::has("_request_parameter")) {
            $this->_request_parameter = \Session::get("_request_parameter");
        } else {
            $this->_request_parameter = isset($request['request_parameter']) ? $request['request_parameter'] : '';
        }
        $request->merge(array('_request_parameter' => $this->_request_parameter));
        // set locale
        app()->setLocale($this->_request_parameter);


        //\URL::forceRootUrl(url('/')); //  url messup fix
    }

    /**
     * Set default language
     * @param Request $request
     */
    private function _setLang(Request $request)
    {
        // request headers
        $headers = apache_request_headers();
        // get language
        $lang_i = "language"; // index
        // set from API
        $this->_lang = isset($headers[strtolower($lang_i)]) ? $headers[strtolower($lang_i)] : "en";

        // if session has language, apply that language
        if (\Session::has($lang_i)) {
            $this->_lang = \Session::get($lang_i);
        } else {
            $this->_lang = isset($headers[$lang_i]) ? $headers[$lang_i] : "en";
        }
        $request->merge(array('_lang' => $this->_lang));

        // set locale
        app()->setLocale($this->_lang);
    }


    /**
     * Parse API response
     *
     * @return Response
     */
    protected function __ApiResponse(Request $request, $api_data)
    {
        // we need message params in all requests
        if (!isset($api_data["message"])) {
            $api_data["message"] = "";
        }
        //$api_data["t_message"] = trans('system.invalid_user_request');

        // we need to have bool (0/1) for response
        $api_data["response"] = $api_data["response"] == "success" ? 0 : 1;
        if (!isset($api_data["error"])) {
            $api_data["error"] = $api_data["response"];
        }
        unset($api_data["response"]);

        // kick user if not valid - or removed
        /*if($api_data['message'] == trans('system.invalid_user_request')) {
            $api_data['kick_user'] = 1;
        }*/

        // parse for devices
        if (\Session::token() != $request->input('_token')) {
            header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            header('Content-Type: application/json');
            return $api_data;
        }

        // target element
        $data['targetElem'] = 'pre[id=response]';
        // view page
        //$data['prettyPrint'] = json_encode($api_data);
        $data['jsonEditor'] = trim(json_encode($api_data));

        return $data;
    }


    /**
     * check Param
     * @param string pk
     * @param string $model
     * @param string $param
     * @param string $param_def_value
     * @return count
     */
    protected function __checkParamCount($request, $pk, $model, $param, $param_def_value = "")
    {
        return $this->__models[$model]
            ->where($pk, "=", $request->input($param, $param_def_value))
            ->whereNull("deleted_at")
            ->count();
    }


    /**
     * validate input params
     * @param string $request
     * @param string $validate_map
     * @return array
     */
    protected function __validateInputParams($request, $validate_map)
    {
        $validator = \Validator::make($request, $validate_map);
        $errors = $validator->errors();
        $validate_data = array();
        $validate_data['error'] = false;
        foreach ($validate_map as $field => $value) {
            if (!empty($errors->first("$field"))) {
                $field_name = "$field";
                $validate_data['message'][] = $errors->first("$field");
                $validate_data['fields'][] = $field;
                $validate_data['error'] = true;
            }
        }
        return $validate_data;
    }


    /**
     * internal call
     * @param string $url
     * @param string $method
     * @param string $params
     * @return count
     */
    protected
    function __internalCall(Request $request, $url, $method, $params = array(), $is_param_merge = true)
    {
        return CustomHelper::internalCall($request, $url, $method, $params, $is_param_merge);
    }

    /**
     * verify reCaptcha response
     * @param bool $post_param
     * @return array
     */
    protected function __validateRecaptcha($post_param = false)
    {
        $url = CAPTCHA_VERIFY_URL;
        if (!$post_param) {
            $post_param = trim($_POST[CAPTCHA_RESPONSE_FIELD]);
        }
        // set post fields
        $post = array(
            'secret' => CAPTCHA_SECRET_KEY,
            'response' => $post_param,
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        // execute!
        $response_captcha = curl_exec($ch);

        if (!$response_captcha) {
            $err = curl_errno($ch);
            $errmsg = curl_error($ch);

            return array(
                'error' => 1,
                'message' => $err . " - " . $errmsg
            );
        }
        // close the connection, release resources used
        curl_close($ch);

        // do anything you want with your response
        //  echo "<pre>"; print_r($response_captcha); exit;
        $response_captcha = json_decode($response_captcha);
        if (!$response_captcha->success) {
            return array(
                'error' => 1,
                'message' => trans("backend.invalid_reCaptcha")
            );

        } else {
            return array(
                'error' => 0,
                'message' => trans("backend.success")
            );
        }
    }

    /**
     * Get Panel Path
     * @param bool $panel
     * @return string
     */
    protected function __getPanelPath($panel = false)
    {
        return CustomHelper::getPanelPath($panel);
    }

    /**
     * Get Panel Path
     * @param bool $panel
     * @return string
     */
    protected function __convertToCamel($convet_str)
    {
        return lcfirst(str_replace('_', '', ucwords($convet_str, '_')));
    }


    /**
     * internal call
     * @param string $url
     * @param string $method
     * @param string $params
     * @return count
     */
    public function apiPostRequest($url, $type, $parameter = array(), $is_external = false)
    {
        $ApiCurl = new ApiCurl();
        return $ApiCurl->apiPostRequest($url, $type, $parameter, $is_external);
        exit();
    }


    /**
     * Set entity type params (provided in string or int) and return data
     *
     * @param Request $request
     */
    protected function _setEntityTypeParams(Request $request)
    {
        $etype_model = new SYSEntityType();
        // get data from id
        $et_data = $etype_model->get(trim(request($etype_model->primaryKey, 0)));

        // get from identifier
        if (isset($request->{$etype_model->primaryKey}) && !is_numeric(trim($request->{$etype_model->primaryKey}))) {
            $et_data = $etype_model->getBy(
                'identifier',
                trim($request->{$etype_model->primaryKey})
            );
            // assign to request
            $et_id = isset($et_data->{$etype_model->primaryKey}) ?
                $et_data->{$etype_model->primaryKey} :
                0;
            $request->merge([$etype_model->primaryKey => $et_id]);
        }

        return $et_data;
    }


}
