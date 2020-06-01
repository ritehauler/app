<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Cache;
use App\Libraries\CustomHelper;
use Illuminate\Support\Facades\Mail;

class Base extends Model
{

    protected $__keyParam;
    protected $__table;
    protected $__fields;
    protected $__useCache = CACHE_ON;
    protected $__modelPath = "\App\Http\Models\\";


    /**
     * Method    :    get
     * Reason    :    finding Data according to id
     **/
    function get($id = 0, $exclude_deleted = false)
    {
        $key = MEM_KEY . $this->__keyParam . (int)$id;
        $data = FALSE;
        if ($this->__useCache === TRUE) {
            $data = Cache::get($key, FALSE);
        }

        if ($data === FALSE) {
            //$query = $this->_class_name->where($this->primaryKey, '=', $id);
            $query = DB::table($this->__table)->where($this->primaryKey, '=', $id);
            if($exclude_deleted) { $query->whereNull("deleted_at"); }
            $result = $query->get($this->__fields);
            $data = isset($result[0]->{$this->__fields[0]}) && $result[0]->{$this->__fields[0]} > 0 ? $result[0] : FALSE;
            // cache
            if ($this->__useCache === TRUE && $data !== FALSE) {
                Cache::put($key, $data, CACHE_LIFE);
            }
        } else {
            $data = (object)$data;
        }
        return $data;
    }


    /**
     * Method    :    getBy
     * Reason    :    finding Data according to provided column
     **/
    function getBy($column = "", $value = "", $exclude_deleted = false)
    {
        $query = DB::table($this->__table)->where($column, '=', $value);
        if($exclude_deleted) { $query->whereNull("deleted_at"); }
        $result = $query->get(array($this->__fields[0]));
        $data = isset($result[0]) ? $this->get($result[0]->{$this->__fields[0]}) : FALSE;
        return $data;
    }

    /**
     * Get Data
     *
     * @return Object
     */
    public function getData($pk_id = 0)
    {
        // init data
        $data = $this->get($pk_id);

        if ($data !== FALSE) {
            // unset unrequired
			//$data->updated_at, 
            unset($data->deleted_at);
        }

        return $data;
    }


    /**
     * Method    :    update
     * Reason    :    update specific id data
     **/
    function set($id = 0, $data = array())
    {
        //unset($data[$this->__fields[0]]);
        $result = DB::table($this->__table)->where($this->__fields[0], $id)->update($data);
        if ($this->__useCache === TRUE && (int)@$id > 0) {
            //$data[$this->__fields[0]] = $id;
            $key = is_array($id) ? MEM_KEY . $this->__keyParam . implode('-', $id) : MEM_KEY . $this->__keyParam . $id;
            Cache::put($key, $data, CACHE_LIFE);
        }
        return $result;
    }

    /**
     * Method    :    setFieldCount
     * Reason    :    update count in a field
     **/
    function setFieldCount($id = 0, $field = "", $increament_value = 0)
    {
        $result = NULL;
        try {
            $result = DB::unprepared("UPDATE `" . $this->__table . "` SET `$field` = `$field` + " . $increament_value . "
			WHERE `" . $this->__fields[0] . "` = '" . $id . "';");
            // remove cache if active
            if ($this->__useCache === TRUE && (int)@$id > 0) {
                $key = is_array($id) ? MEM_KEY . $this->__keyParam . implode('-', $id) : MEM_KEY . $this->__keyParam . $id;
                Cache::forget($key);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            //var_dump($e->getMessage()); exit;
        } catch (Exception $e) {
            //var_dump($e->getMessage()); exit;
        }
        return $result;
    }

    /**
     * Method    :    put
     * Reason    :    insert data
     **/
    function put($data = array())
    {
        $id = DB::table($this->__table)->insertGetId($data);
        return $id;
    }


    /**
     * Method    :    delete
     * Reason    :    delete data
     **/
    function remove($id = 0)
    {
        $affected_rows = NULL;
        // get record
        $record = $this->get($id);

        if ($record !== FALSE) {
            if (SOFT_DELETE === TRUE) {
                //$affected_rows = $this->where($this->__fields[0], '=', $id)->delete();
                $record->deleted_at = date("Y-m-d H:i:s");
                $this->set($record->{$this->__fields[0]}, (array)$record);
            } else {
                $affected_rows = DB::table($this->__table)->where($this->__fields[0], '=', $id)->delete();
                if ($this->__useCache === TRUE) {
                    $key = is_array($id) ? MEM_KEY . $this->__keyParam . implode('-', $id) : MEM_KEY . $this->__keyParam . $id;
                    Cache::forget($key);
                }
            }
        }

        return $affected_rows;
    }


    /**
     * Method    :    delete
     * Reason    :    delete data
     **/
    function hardRemove($id = 0)
    {
        $affected_rows = NULL;
        $affected_rows = DB::table($this->__table)->where($this->__fields[0], '=', $id)->delete();
        if ($this->__useCache === TRUE) {
            $key = is_array($id) ? MEM_KEY . $this->__keyParam . implode('-', $id) : MEM_KEY . $this->__keyParam . $id;
            Cache::forget($key);
        }

        return $affected_rows;
    }



    /**
     * Method    :    slugify
     * Reason    :    convert string to permalink
     *
     * ??? find url_helper
     **/
    /*function slugify($string) {
        // load helper
        $this->load->helper('url_helper');

        $str = url_title($string,'dash',TRUE);
        //check if exists
        $options['count'] = TRUE;
        $options['conditions']["slug"] = trim($str);
        $record = $this->findAllIDs($options);
        if($record > 0) {
            if(preg_match("#-[0-9]{0,}$#",$str)) {
                //get count
                $count = preg_match("#[0-9]{0,}$#",$str,$strArr);
                //increment count
                $count = (int)$strArr[0]++;
                $count = sprintf("%02d",$strArr[0]++);
                //new string
                $newStr = preg_replace("#[0-9]{0,}$#","$1",$str).$count;
                return $this->slugify($newStr);
            }
            $newStr = $str."-".sprintf("%02d",1);
            return $this->slugify($newStr);
        }
        else {
            return $str;
        }
    }*/

    /**
     * Prepare Attachment
     * @param string $path
     * @param string $random_hash
     * @param : $data (array of data)
     * @return bool
     */
    private function _prepareAttachment($path, $random_hash)
    {
        $rn = "\r\n";
        $message = "";
        //echo "Path : ".$path;
        if (file_exists($path)) {
            //echo "Exists";
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $ftype = finfo_file($finfo, $path);
            $file = fopen($path, "r");
            $attachment = fread($file, filesize($path));
            $attachment = chunk_split(base64_encode($attachment));
            fclose($file);

            //include attachment
            $message .= "--PHP-mixed-$random_hash\r\n";
            $message .= "Content-Type: " . $ftype . ";
            name=" . basename($path) . "\r\n" . "Content-Transfer-Encoding:
            base64\r\n" . "Content-Disposition: attachment\r\n\r\n";

            $message .= $attachment;
        }
        return $message;
    }


    /**
     * send Mail
     * @param : $to (To email)
     * @param : $message (Message)
     * @param : $data (array of data)
     * @return bool
     */
    function sendMail($to, $msg, $data = array(), $files = array(), $ex_headers = array())
    {
        // init vars
        $charset = "utf-8";
        $headers = $ex_headers;
        $headers[] = "MIME-Version: 1.0";

        // - from
        if (isset($data['from'])) {
            if (is_array($data['from'])) {
                $headers[] = "From: " . $data['from'][1] . " <" . $data['from'][0] . ">";
            } else {
                $headers[] = "From: <" . $data['from'] . ">";
            }
        }

        // cc
        if (isset($data['cc'])) {
            if (is_array($data['cc'])) {
                $headers[] = "Cc: " . $data['cc'][1] . " <" . $data['cc'][0] . ">";
            } else {
                $headers[] = "Cc: <" . $data['cc'] . ">";
            }
        }

        // bcc
        if (isset($data['bcc'])) {
            if (is_array($data['bcc'])) {
                $headers[] = "Bcc: " . $data['bcc'][1] . " <" . $data['bcc'][0] . ">";
            } else {
                $headers[] = "Bcc: <" . $data['bcc'] . ">";
            }
        }
        // reply-to
        if (isset($data['reply-to'])) {
            if (is_array($data['reply-to'])) {
                $headers[] = "Reply-To: " . $data['reply-to'][1] . " <" . $data['reply-to'][0] . ">";
            } else {
                $headers[] = "Reply-To: <" . $data['reply-to'] . ">";
            }
        }

        // to_email
        if (is_array($to)) {
            $to_email = $to[0];
        } else {
            $to_email = $to;
        }

        $headers[] = "Subject: {" . $data["subject"] . "}";
        $headers[] = "X-Mailer: PHP/" . phpversion();

        if (count($files) > 0) {
            $random_hash = md5(uniqid(time()));
            $headers[] = "Content-Type: multipart/mixed; boundary=PHP-mixed-" . $random_hash;

            $message = '';
            //define the body of the message.
            $htmlbody = $message;

            $message = "--PHP-mixed-$random_hash\r\n" . "Content-Type: multipart/alternative; boundary=PHP-alt-$random_hash\r\n\r\n";
            $message .= "--PHP-alt-$random_hash\r\n" . "Content-Type: text/html;
          charset=\"" . $charset . "\"\r\n" . "Content-Transfer-Encoding: 7bit\r\n\r\n";


            //Insert the html message.
            $message .= $htmlbody;
            $message .= "\r\n\r\n--PHP-alt-$random_hash--\r\n\r\n";

            for ($f = 0; $f < count($files); $f++) {
                $message .= $this->_prepareAttachment($files[$f], $random_hash);
            }
            $message .= "/r/n--PHP-mixed-$random_hash--";


        } else {
            $headers[] = "Content-type: text/html; charset=" . $charset;
        }

        //die($msg);
       // return @mail($to_email, $data["subject"], $message, implode("\r\n", $headers));
        $data['send_to_mail'] = $to_email;
        \Mail::send('emails.mail', ['data' => $msg], function ($mail) use ($data) {
            $mail->to($data['send_to_mail'])
                ->subject($data['subject'])
                ->from($data['from']);
        });
        return;
    }


    /**
     * chmodR
     * @param string $path
     * @param number $dir_perm
     * @param number $file_perm
     * @return bool
     */
    function chmodR($path, $dir_perm = 0750, $file_perm = 0644)
    {
        $dp = opendir($path);
        while ($file = readdir($dp)) {
            if ($file != "." && $file != "..") {
                if (is_dir($file)) {
                    chmod($file, $dir_perm);
                    chmod_r($path . "/" . $file);
                } else {
                    chmod($path . "/" . $file, $file_perm);
                }
            }
        }
        closedir($dp);
    }

    /**
     * removeDir
     * @param string $dirPath
     * @return bool
     */
    public function removeDir($dirPath)
    {
        if (!is_dir($dirPath)) {
            //throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        //$files = scandir($dirPath);
        foreach ($files as $file) {
            /*if($file == "." || $file == "..") {
                continue;
            }*/
            if (is_dir($file)) {
                $this->removeDir($file);
            } else {
                @unlink($file);
            }
        }
        @rmdir($dirPath);
    }


    /**
     * Convert an array to XML
     * @param array $array
     * @param string $root_tag
     * @param bool $xml
     * @param array $array
     * @param string $encoding
     * @param string $standalone
     * @param SimpleXMLElement $xml
     */
    function arrayToXML($array, $root_tag = "", $xml = false, $encoding = "UTF-8", $standalone = "yes")
    {
        $root_tag = $root_tag == "" ? '<result/>' : $root_tag;
        $root_tag = '<?xml version="1.0" encoding="' . $encoding . '" '
            . 'standalone="' . $standalone . '"?>' . $root_tag;

        if ($xml === false) {
            $xml = new \SimpleXMLElement($root_tag);
        }

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $this->arrayToXML($value, $root_tag, $xml->addChild($key));
            } else {
                $xml->addChild($key, $value);
            }
        }

        return $xml->asXML();
    }

    /**
     * Get panel path
     * @param bool $panel
     * @return string
     */
    function __getPanelPath($panel = false)
    {
        return CustomHelper::getPanelPath($panel);
    }


    /**
     * @param $sql
     * @param int $timeout
     * @return mixed
     */
    public function cacheQuery($sql, $timeout = 60) {
        return \Cache::remember(md5($sql), $timeout, function() use ($sql) {
            return \DB::select($sql);
        });
    }
}
