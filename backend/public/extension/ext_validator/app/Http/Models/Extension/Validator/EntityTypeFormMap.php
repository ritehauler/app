<?php namespace App\Http\Models\Extension\Validator;

use App\Http\Models\Base;
use App\Libraries\CustomHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;
use Illuminate\Http\Request;

// init models
//use App\Http\Models\Conf;

class EntityTypeFormMap extends Base
{

    use SoftDeletes;
    public $table = 'fb_entity_type_form_map';
    public $timestamps = true;
    public $primaryKey = 'entity_type_form_map_id';
    protected $dates = ['deleted_at'];
    private $_pModelPath = "Extension\\Validator\\";
    public $objectIdentifier = "form";
    //public $actionIdentifier = "activity_type";
    //private $_pHook = "ExtActivityTypeModel"; // extension hook
    public $currentActorID = NULL;

    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table;
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set model path
        $this->_pModelPath = $this->__modelPath . $this->_pModelPath;

        // set fields
        $this->__fields = array($this->primaryKey, 'target_entity_type_id', 'actor_entity_type_id', 'actor_entity_id', 'form_id', 'title', 'description', 'created_at', 'updated_at', 'deleted_at');
    }


    /**
     * save
     *
     * @return Response
     */
    public function saveData($save_data, $timestamp = NULL)
    {
        // load models
        $eTypeModel = $this->__modelPath . 'SYSEntityType';
        $eTypeModel = new $eTypeModel;
        $formModel = $this->_pModelPath . 'Form';
        $formModel = new $formModel;

        // default vars
        $timestamp = $timestamp == "" ? date("Y-m-d H:i:s") : $timestamp;

        // save form
        $ref_data = array(
            'title' => ucfirst($formModel->objectIdentifier) . ' - ' . microtime(false),
            'description' => null,
            'created_at' => $timestamp
        );
        $ref_id = $formModel->put($ref_data);

        // save mapping data
        $save_data[$formModel->primaryKey] = $ref_id;
        $save_data['created_at'] = $timestamp;
        // save
        $id = $this->put($save_data);

        return $id;
    }


    /**
     * getData
     *
     * @return Response
     */
    public function getData($pk_id = 0)
    {
        // record
        $record = $this->get($pk_id);

        // init data
        $data = NULL;

        if ($record) {
            // load models
            /*$eTypeModel = $this->__modelPath . 'SYSEntityType';
            $eTypeModel = new $eTypeModel;*/

            // assign record
            $data = json_decode(json_encode($record));

            // extra keys
            $data->is_creator = ($this->currentActorID == $record->actor_entity_id) ? 1 : 0;

            // unset un-required;
            unset($data->deleted_at);

            // pass via hook
            $data = CustomHelper::hookData($this->_pHook, __FUNCTION__, new Request(), $data);
        }


        return $data;
    }

}