<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

// models
//use App\Http\Models\History;
//use App\Http\Models\HistoryNotification;

class SocNotificationTemplate extends Base {
	
	private $_model_path = "\App\Http\Models\\";
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table = 'pl_soc_notification_template';
		$this->primaryKey = $this->__table . '_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
		
        // set fields
        $this->__fields   = array($this->primaryKey, 'plugin_identifier', 'type', "history_identifier", 'key_code', 'target_entity_count', 'title', 'body', 'hint', 'wildcards', 'replacers' , 'created_at', 'updated_at', 'deleted_at');
	}
	
	
	/**
     * Notify entities as per activity performed (like,comment,etc)
     * @param string $plugin_identifier
	 * @param string $history_identifier
	 * @param string $history_identifier
     * @return NULL
     */
    public function notify($plugin_identifier = NULL, $history_identifier = NULL, $reference_module = NULL, $reference_id = NULL) {
		
        return;
    }
	
	
}