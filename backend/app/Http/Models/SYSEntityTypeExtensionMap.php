<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// init models
//use App\Http\Models\Conf;

class SYSEntityTypeExtensionMap extends Base
{

    use SoftDeletes;
    public $table = 'sys_entity_type_extension_map';
    public $timestamps = true;
    public $primaryKey;
    protected $dates = ['deleted_at'];

    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table;
        $this->primaryKey = 'entity_type_extension_map_id';
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields = array($this->primaryKey, 'extension_id', 'actor_entity_type_id', 'target_entity_type_id', 'data_entity_type_id', 'aggregate_field', 'listing_attributes', 'search_attributes', 'create_attributes', 'update_attributes', 'configuration', 'created_at', 'updated_at', 'deleted_at');
    }

    /**
     * Check Access
     * @param int $extension_id
     * @param int $entity_type_id
     * @param int $actor_entity_type_id
     * @return Query
     */
    public function checkAPIAccess($extension_id, $request)
    {
        // init models
        $exModel = $this->__modelPath . "SYSEntityType";
        $exModel = new $exModel;

        $check = $this->_checkAccess(
            $extension_id,
            $request->input('target_' . $exModel->primaryKey, 0),
            $request->input("actor_" . $exModel->primaryKey, 0)
        //$request->input('data_'.$exModel->primaryKey,0)
        );

        //if($check < 1) {
        if (!$check) {
            // set response header
            header('Cache-Control: no-cache, must-revalidate');
            header('Content-Type: application/json; charset=utf8');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

            $api_data['response'] = "error";
            $api_data['kick_user'] = 1; // kick user
            $api_data['message'] = trans('api_errors.access_not_allowed');

            // parse for devices
            if (\Session::token() != $request->input('_token')) {
                header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
                header("Cache-Control: post-check=0, pre-check=0", false);
                header("Pragma: no-cache");
                echo json_encode($api_data);
                exit;
            }

            // target element
            $data['targetElem'] = 'pre[id=response]';
            // view page
            //$data['prettyPrint'] = json_encode($api_data);
            $data['jsonEditor'] = trim(json_encode($api_data));

            echo json_encode($data);
            exit;
        }
        return $check;
    }

    /**
     * Check Access (private)
     * @param int $extension_id
     * @param int $entity_type_id
     * @param int $actor_entity_type_id
     * @return Query
     */
    public function _checkAccess($extension_id, $entity_type_id, $actor_entity_type_id, $data_entity_type_id = NULL)
    {
        // init models
        $exModel = $this->__modelPath . "SYSExtension";
        $exModel = new $exModel;
        $exModel2 = $this->__modelPath . "SYSEntityType";
        $exModel2 = new $exModel2;

        // filter
        $extension_id = trim($extension_id);
        $entity_type_id = trim($entity_type_id);
        $actor_entity_type_id = trim($actor_entity_type_id);

        // init query
        $query = $this
            //->select("b.".$exModel->primaryKey)
            ->select("a." . $this->primaryKey)
            ->from($this->table . " AS a")
            ->join($exModel->table . " AS b", "b." . $exModel->primaryKey, "=", "a." . $exModel->primaryKey)
            ->join($exModel2->table . " AS c", "c." . $exModel2->primaryKey, "=", "a.target_" . $exModel2->primaryKey)
            ->join($exModel2->table . " AS d", "d." . $exModel2->primaryKey, "=", "a.actor_" . $exModel2->primaryKey)
            ->whereNull("a.deleted_at")
            ->whereNull("b.deleted_at")
            ->whereNull("c.deleted_at")
            ->whereNull("d.deleted_at");
        /*// data entity id
        if($data_entity_type_id) {
            $query->join($exModel2->table." AS x","x.".$exModel2->primaryKey, "=", "a.data_".$exModel2->primaryKey);
            $query->whereNull("x.deleted_at");

        } else {
            $query->whereNull('a.data_entity_type_id');
        }*/
        // extension
        if (is_numeric($extension_id)) {
            $query->where("b." . $exModel->primaryKey, "=", $extension_id);
        } else {
            $query->where("b.identifier", "=", $extension_id);
        }
        // entity_type
        if (is_numeric($entity_type_id)) {
            $query->where("c." . $exModel2->primaryKey, "=", $entity_type_id);
        } else {
            $query->where("c.identifier", "=", $entity_type_id);
        }
        // target_entity_type
        if (is_numeric($actor_entity_type_id)) {
            $query->where("d." . $exModel2->primaryKey, "=", $actor_entity_type_id);
        } else {
            $query->where("d.identifier", "=", $actor_entity_type_id);
        }
        $exists = $query->get();

        // return
        //return isset($exists[0]->{$exModel->primaryKey}) ? $exists[0]->{$exModel->primaryKey} : 0;
        return isset($exists[0]) ? $this->get($exists[0]->{$this->primaryKey}) : NULL;
    }

    /**
     * Generate Extension Field
     *
     * @return Response
     */
    public function generateField($request, $field, $entity_type_id = NULL, $show = 0, $field_data_type = 'intfield')
    {
        // load model
        $extModel = $this->__modelPath . 'SYSExtension';
        $extModel = new $extModel;
        // call from extension model
        return $extModel->generateField($request, $field, $entity_type_id, $show, $field_data_type);
    }

}