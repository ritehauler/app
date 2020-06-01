<?php namespace App\Http\Models;

use App\Http\Models\Base;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// init models
//use App\Http\Models\Conf;

class SYSExtensionStat extends Base
{

    use SoftDeletes;
    public $table = 'sys_extension_stat';
    public $timestamps = true;
    public $primaryKey;
    protected $dates = ['deleted_at'];

    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table;
        $this->primaryKey = 'extension_stat_id';
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields = array($this->primaryKey, 'extension_id', 'target_entity_type_id', 'target_entity_id', 'json_value', 'aggregate_value', 'created_at', 'updated_at', 'deleted_at');
    }


    /**
     * set aggregate
     * @param int $extension_id
     * @param int $target_entity_id
     * @param int $value
     * @return bool
     */
    function setAggregate($ext_map_id, $target_entity_id, $value)
    {
        // load models
        $eTypeExtMapModel = $this->__modelPath . 'SYSEntityTypeExtensionMap';
        $eTypeExtMapModel = new $eTypeExtMapModel;

        // defaults
        $timestamp = date("Y-m-d H:i:s");
        $ext_map = $eTypeExtMapModel->get($ext_map_id);
        // exists ?
        $exists_id = $this->select($this->primaryKey)
            ->where($eTypeExtMapModel->primaryKey, $ext_map->{$eTypeExtMapModel->primaryKey})
            ->where("extension_id", $ext_map->extension_id)
            ->where("target_entity_id", $target_entity_id)
            ->whereNull("deleted_at")
            ->get();

        // get if exists
        if (isset($exists_id[0])) {
            $record = $this->get($exists_id[0]->{$this->primaryKey});
        } else {
            // init models
            $exModel = $this->__modelPath . "SYSEntity";
            $exModel = new $exModel;

            $record = array(
                $eTypeExtMapModel->primaryKey => $ext_map->{$eTypeExtMapModel->primaryKey},
                "extension_id" => $ext_map->extension_id,
                "target_entity_id" => $target_entity_id,
                "target_entity_type_id" => $ext_map->target_entity_type_id,
                "created_at" => $timestamp
            );
            // insert
            $id = $this->put($record);
            // get
            $record = $this->get($id);
        }

        // set record
        $record->aggregate_value = ($record->aggregate_value + $value);
        $record->updated_at = $timestamp;
        // update
        $this->set($record->{$this->primaryKey}, (array)$record);

        //return $record;
        return $this->getData($record->{$this->primaryKey});

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
            // decode json value
            //$data->json_value = trim($data->json_value) != '' ? json_decode(trim($data->json_value)) : '{}';
            $data->json_value = json_decode(trim($data->json_value));
            //$data->updated_at,
            unset($data->deleted_at);
        }

        return $data;
    }

    /**
     * set aggregate
     * @param int $extension_id
     * @param int $target_entity_id
     * @param string $json_key
     * @param string $value
     * @return bool
     */
    function setJSON($ext_map_id, $target_entity_id, $json_key, $value)
    {
        // load models
        $eTypeExtMapModel = $this->__modelPath . 'SYSEntityTypeExtensionMap';
        $eTypeExtMapModel = new $eTypeExtMapModel;

        // defaults
        $timestamp = date("Y-m-d H:i:s");
        $ext_map = $eTypeExtMapModel->get($ext_map_id);

        // exists ?
        $exists_id = $this->select($this->primaryKey)
            ->where($eTypeExtMapModel->primaryKey, $ext_map->{$eTypeExtMapModel->primaryKey})
            ->where("extension_id", $ext_map->extension_id)
            ->where("target_entity_id", $target_entity_id)
            ->whereNull("deleted_at")
            ->get();
        $exists_id = json_decode(json_encode($exists_id));

        // get if exists
        if (isset($exists_id[0])) {
            $record = $this->get($exists_id[0]->{$this->primaryKey});
        } else {
            // init models
            $exModel = $this->__modelPath . "SYSEntity";
            $exModel = new $exModel;

            $record = array(
                $eTypeExtMapModel->primaryKey => $ext_map->{$eTypeExtMapModel->primaryKey},
                "extension_id" => $ext_map->extension_id,
                "target_entity_id" => $target_entity_id,
                "target_entity_type_id" => $ext_map->target_entity_type_id,
                "created_at" => $timestamp
            );
            // insert
            $id = $this->put($record);
            // get
            $record = $this->get($id);
        }

        $json = trim($record->json_value) != "" ? json_decode($record->json_value) : (object)array($json_key => 0);

        // if integer, process plus/minus | else set string
        if ($json_key != "") {
            if (isset($json->{$json_key}) && is_int($value)) {
                $json->{$json_key} = ($json->{$json_key} + $value);
            } else {
                $json->{$json_key} = $value;
            }
        }


        // set record
        $record->json_value = json_encode($json);
        $record->updated_at = $timestamp;

        // update
        $this->set($record->{$this->primaryKey}, (array)$record);

        //return $record;
        return $this->getData($record->{$this->primaryKey});
    }
}