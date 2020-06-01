<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

// models
//use App\Http\Models\History;
//use App\Http\Models\HistoryNotification;

class SocSubscription extends Base {
	
	private $_model_path = "\App\Http\Models\\";
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table = 'soc_subscription';
		$this->primaryKey = $this->__table . '_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
		
        // set fields
        $this->__fields   = array($this->primaryKey, 'plugin_identifier', 'history_identifier', "reference_module", 'reference_id', 'reference_originator_id', 'target_entity', 'target_entity_id', 'created_at', 'updated_at', 'deleted_at');
	}
	
	
	/**
     * Subscribe user for social notification (ie: like,comment)
     * @param string $target_entity
	 * @param int $target_entity_id
	 * @param array $save_data
     * @return NULL
     */
    public function xsubscribe($target_entity, $target_entity_id, $save_data) {
		$save_data["target_entity"] = $target_entity;
		$save_data["target_entity_id"] = $target_entity_id;
		$this->put($save_data);
        return;
    }
	public function subscribe($plugin_identifier, $history_identifier, $reference_module, $reference_id, $target_entity, $target_entity_id, $originator_id = NULL) {
		// check existance
		$check = $this->where("plugin_identifier","=",$plugin_identifier)
			->where("history_identifier","=",$history_identifier)
			->where("reference_module","=",$reference_module)
			->where("reference_id","=",$reference_id)
			->where("target_entity","=",$target_entity)
			->where("target_entity_id","=",$target_entity_id)
			->count();
		// insert if not exists already
		if($check === 0) {
			$save = array(
				"plugin_identifier" => $plugin_identifier,
				"history_identifier" => $history_identifier,
				"reference_module" => $reference_module,
				"reference_id" => $reference_id,
				"target_entity" => $target_entity,
				"target_entity_id" => $target_entity_id,
				"reference_originator_id" => $originator_id
			);
			$this->put($save);
		}
        return;
    }
	
	
	/**
     * Unsubscribe user from social notification (ie: like,comment)
     * @param string $plugin_identifier
	 * @param string $history_identifier
	 * @param string $reference_module
	 * @param int $reference_id
	 * @param string $target_entity
	 * @param int $target_entity_id
     * @return NULL
     */
    public function unsubscribe($plugin_identifier, $history_identifier, $reference_module, $reference_id, $target_entity, $target_entity_id) {
		// load models
		$entity_history_model = $this->__modelPath."EntityHistory";
		$entity_history_model = new $entity_history_model;
		
		$this->where("plugin_identifier","=",$plugin_identifier)
			->where("history_identifier","=",$history_identifier)
			->where("reference_module","=",$reference_module)
			->where("reference_id","=",$reference_id)
			->where("target_entity","=",$target_entity)
			->where("target_entity_id","=",$target_entity_id)
			->delete();
		// delete from entity history
		$entity_history_model->whereRaw("(history_id = (SELECT history_id FROM history WHERE identifier = '".$history_identifier."' AND plugin_identifier = '".$plugin_identifier."' AND deleted_at IS NULL))")
			->where("reference_module","=",$reference_module)
			->where("reference_id","=",$reference_id)
			->where("actor_entity","=",$target_entity)
			->where("actor_id","=",$target_entity_id)
			->delete();
    }
	
	
}