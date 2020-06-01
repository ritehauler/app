<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

class SYSEntityNotification extends Base
{

    use SoftDeletes;
    public $table = 'sys_entity_notification';
    public $timestamps = true;
    public $primaryKey;
    protected $dates = ['deleted_at'];

    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table;
        $this->primaryKey = 'entity_notification_id';
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields = array($this->primaryKey, 'entity_history_id', 'against_entity_type_id', 'against_entity_id', "is_read","module_id","permission_id", 'created_at', 'updated_at', 'deleted_at');
    }

    /**
     * @param $against_entity_type_id
     * @param $against_entity_id
     * @return bool
     */
    public function getTotalCount($against_entity_type_id,$against_entity_id)
    {
        if($against_entity_type_id == 1){

            // AND n.against_entity_id = $against_entity_id
            $row = \DB::select("SELECT count(n.entity_notification_id) as total_count
                            FROM sys_entity_notification n
                            LEFT JOIN sys_entity_role r ON n.against_entity_id = r.entity_id
                            WHERE n.against_entity_type_id = $against_entity_type_id  
                            AND n.is_read = 0");
        }
        else{
            $row = \DB::select("SELECT count(n.entity_notification_id) as total_count
                            FROM sys_entity_notification n
                            LEFT JOIN sys_entity_role r ON n.against_entity_id = r.entity_id
                            LEFT JOIN sys_role_permission_map p ON (p.role_id = r.role_id) 
                            AND (n.module_id = p.module_id AND n.permission_id = p.permission_id)
                            WHERE p.do_allow = 1 
                            AND n.against_entity_type_id = $against_entity_type_id  
                            AND n.against_entity_id = $against_entity_id
                            AND n.is_read = 0");
        }

        return isset($row[0]->total_count) ? $row[0]->total_count : 0;
    }

    /**
     * @param $against_entity_type_id
     * @param $against_entity_id
     * @return bool
     */
    public function getList($against_entity_type_id,$against_entity_id,$request_params)
    {
        if($against_entity_type_id == 1){
// AND n.against_entity_id = $against_entity_id
            $query ="SELECT h.*,n.entity_notification_id,n.module_id,n.permission_id,m.slug as module,perm.title as permission 
                            FROM sys_entity_notification n
                            LEFT JOIN sys_entity_role r ON n.against_entity_id = r.entity_id
                            LEFT JOIN sys_entity_history h ON h.entity_history_id = n.entity_history_id
                            LEFT JOIN sys_module m ON n.module_id = m.module_id
                            LEFT JOIN sys_permission perm ON perm.permission_id = n.permission_id
                            WHERE n.against_entity_type_id = $against_entity_type_id  
                            AND n.is_read = 0
                            ORDER by entity_notification_id DESC";
        }
        else{
            $query ="SELECT h.*,n.entity_notification_id,n.module_id,n.permission_id,m.slug as module,perm.title as permission 
                            FROM sys_entity_notification n
                            LEFT JOIN sys_entity_role r ON n.against_entity_id = r.entity_id
                            LEFT JOIN sys_role_permission_map p ON (p.role_id = r.role_id) 
                            AND (n.module_id = p.module_id AND n.permission_id = p.permission_id)
                            LEFT JOIN sys_entity_history h ON h.entity_history_id = n.entity_history_id
                            LEFT JOIN sys_module m ON n.module_id = m.module_id
                            LEFT JOIN sys_permission perm ON perm.permission_id = n.permission_id
                            WHERE p.do_allow = 1 
                            AND n.against_entity_type_id = $against_entity_type_id  
                            AND n.against_entity_id = $against_entity_id
                            AND n.is_read = 0
                            ORDER by entity_notification_id DESC";
        }


        if(isset($request_params->order_by) && !empty($request_params->order_by)){
            //$query .= 'ORDER by ';
        }

        if((isset($request_params->limit))
        && (isset($request_params->offset))){
            $query .= ' LIMIT '.$request_params->offset.' ,'.$request_params->limit;
        }
        else{
            $query .= ' LIMIT 0,10';
        }
       // echo $query; exit;
        $row = \DB::select($query);

        return isset($row[0]) ? $row : false;
    }

}