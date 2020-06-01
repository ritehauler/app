<?php
/**
 * Custom helper for common functions if exists
 * Long description for file (if any)...
 * PHP version 5
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   helper
 * @package    helper
 * @author     Salman Khimani <salman.khimani@cubixlabs.com>
 * @author     Another Author <php@cubixlabs.com>
 * @copyright  Cubixlabs
 */


/**
 * Function to convert string, object, array into utf-8 encoding
 *
 * @param $input
 * @return void
 */

function utf8_encode_deep(&$input)
{
    if (is_string($input)) {
        $input = utf8_encode($input);
    } else if (is_array($input)) {
        foreach ($input as &$value) {
            utf8_encode_deep($value);
        }

        unset($value);
    } else if (is_object($input)) {
        $vars = array_keys(get_object_vars($input));

        foreach ($vars as $var) {
            utf8_encode_deep($input->$var);
        }
    }
}

function utf8_encode_str($string)
{
    return trim(
        iconv(
            mb_detect_encoding($string, mb_detect_order(), TRUE),
            "UTF-8", $string
        )
    );
}

/**
 * To calculate load time
 *
 * @param $started_at
 */
function load_time($started_at = NULL)
{
    return $started_at ?
        'Cool, that only took ' . (microtime(TRUE) - $started_at) . ' seconds!' :
        'Start time invalid';
}

/**
 * Background call
 *
 * @param $url
 * @param int $port
 * @param string $method
 * @param array $params
 * @throws Exception
 */
function background_call($url, $port = 80, $method = 'GET', $params = [])
{
    // get/set params
    $parts = parse_url($url);
    $method = strtoupper($method);

    $url = str_replace(url('/'), '', $url);
    $url = url('/') . ':' . $port . $url;

    try {

        $fp = fsockopen(
            $parts['host'],
            isset($parts['port']) ? $parts['port'] : $port,
            $errno,
            $errstr,
            NULL
        );

        if ($fp) {
            //$user_agent = $_SERVER['HTTP_USER_AGENT'];
            // Fake user agent
            $user_agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.140 Safari/537.36';

            // prepare data to write to socket
            $out = $method . " " . $parts['path'] . " HTTP/1.1\r\n";
            $out .= "Host: " . $parts['host'] . "\r\n";
            $out .= "User-Agent: " . $user_agent . "\r\n";


            if ($method == 'POST') {
                $params = is_array($params) ? $params : [];

                // build query param
                $content = http_build_query($params);

                // push csrf token
                // push other headers
                $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
                $out .= "Content-Length: " . strlen($content) . "\r\n";
            }

            $out .= "Accept: */*\r\n";
            $out .= "\r\n";

            fwrite($fp, $out);

            // write post params if exists
            if ($method == 'POST' && isset($content)) {
                fwrite($fp, $content);
            }

            fclose($fp);
        }
    } catch
    (\Exception $e) {
        //echo "$errstr ($errno)<br />\n";
        throw new \Exception('bagroundCall : ' . $e->getMessage());
    }

}

/**
 * @param $array
 * @return mixed
 */
function last_key($array){
    $keys = array_keys($array);
    return end($keys);
}