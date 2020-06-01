<?php  namespace App\Libraries;
use App\Http\Models\SYSEntity;
use App\Http\Models\SYSRole;
use Illuminate\Http\Request;

use App\Http\Models\WFSWorkflowTemplate;
use App\Http\Models\WFSTaskTemplate;
use App\Http\Models\WFSWFTTTRelation;

use App\Libraries\Wfs\WorkflowGenerator;
use App\Libraries\Wfs\WFSTaskExecution;
use App\Libraries\Wfs\UIBTileGenerator;

/**
 * Class CustomHelper
 */
Class WfsEntityWrapper
{
    /**
     * Constructor
     *
     * @param string $url URL
     */
    private static $_mapping_entity_wfs = [
        15 => 'orderDepartmentalStatus'
    ];

    public function __construct(){}

    public static function generateWfTask($request, $entity, $wft_id)
    {

        $start_date = date('Y-m-d H:m:s');
        $end_date = date('Y-m-d 23:59:59');
        //$order =  (isset($post_response->data->entity)) ? $post_response->data->entity : $post_response->data->order;
        $assign_entity_id =  $entity->entity_id;
        $assign_entity_type_id = $entity->entity_type_id;
        $assign_by_id = 1;
        $activation_state = 1;

        $wft = WFSWorkflowTemplate::getFirstTemplateTaskById($wft_id);
        $departmentGroup = self::getDepartmentGroup($wft);

        if(empty($departmentGroup['dept_id'])){
            // Insert into error log and maintain this order is not initiated with work flow.
            // after insertion return it from here
        }


        $task_assign_to = implode('.',$departmentGroup);

        $wfi_id = WFSWorkflowTemplate::insertInstance($wft_id,$start_date,$end_date,$activation_state);
        $ti_ids = WFSTaskTemplate::insertWFSInstance($wft_id,$start_date,$end_date,$assign_entity_id,$assign_entity_type_id,$assign_by_id,$activation_state,1,$task_assign_to);
        WFSWFTTTRelation::insertWFSInstance($wfi_id,$ti_ids);
        if(count($ti_ids) > 0) {
            $ti_id = reset($ti_ids);
            $ti_detail = WFSTaskTemplate::getTIDetail($ti_id);
           // print_r($ti_detail);exit;
            //
            //$this->_assignWFSTask($request, $ti_detail);

            $response['wft_id'] = $wft_id;
            $response['wfi_id'] = $wfi_id;
            $response['ti_ids'] = $ti_ids;
        }
        return $response;

    }

    private static function getDepartmentGroup($wft)
    {
        $assign_to = $wft->assign_to;
        $dept = explode('.',$assign_to)[0];
        $group = explode('.',$assign_to)[1];
        $user = explode('.',$assign_to)[2];
        $response['dept_id'] = 0;
        $response['group_id'] = 0;
        $response['user_id'] = 0;

        $response = array();
        $result = SYSRole::getDepartmentGroup($dept);
        foreach($result as $row){
            if($group == $row->title) {
                $response['dept_id'] = $row->parent_id;
                $response['group_id'] = $row->role_id;
                $response['user_id'] = $user;
            }elseif($group == '*'){
                $response['group_id'] = '*';
                $response['user_id'] = $user;
            }
            if($dept == '*'){
                $response['dept_id'] = '*';
            }
        }
        return $response;
    }

    /**
     * assign workflow task instance to entity or entity type
     * @param string $url
     * @param string $method
     * @param string $params
     * @return count
     */
    private function _assignWFSTask(Request $request, $ti_detail)
    {
        $ti_id = $ti_detail[0]->ti_id;
        $assignee_data = explode('.',$ti_detail[0]->assign_to);
        $assignee_to = $assignee_data[1];
        $assignee_type = 'user';
        $assignee_type_id = 0;
        if($assignee_data[1] == '*') {
            $assignee_to = $assignee_data[0];
            $assignee_type = 'department';
        }
        if($assignee_type == 'department') {
            $url = 'system/entity_type/listing';
            $method = 'get';
            $params['title'] = $assignee_to;
            $params['mobile_json'] = 1;
            $res = $this->__internalCall($request, $url, $method, $params, false);
            //print_r($res['data']['sys_entity_type_listing']);exit;
            $assignee_type_id = $res['data']['sys_entity_type_listing'][0]['entity_type_id'];
        }else{
            $url = 'system/entities/listing';
            $method = 'get';
            $params['entity_type_id'] = 5;
            $params['name'] = $assignee_to;
            $params['mobile_json'] = 1;
            $res = $this->__internalCall($request, $url, $method, $params, false);
            $assignee_type_id = $res['data'][$assignee_type][0]['entity_id'];

        }
        $url = 'system/entities';
        $method = 'post';
        $nx_params['entity_type_id'] = 28;
        $nx_params['wf_ti_id'] = $ti_id;
        $nx_params['assign_id'] = $assignee_type_id;
        $nx_params['assign_type'] = $assignee_type;
        $nx_params['mobile_json'] = 1;
        $res = $this->__internalCall($request, $url, $method, $nx_params,false);

        return;
    }

    public static function entityTrigger($data)
    {
        if(isset(self::$_mapping_entity_wfs[$data->assign_entity_type_id])) {
            $fn = self::$_mapping_entity_wfs[$data->assign_entity_type_id];
            $response_params = self::$fn($data);
            $request = Request();
            CustomHelper::internalCall($request, $data->next_node_external_url, 'post', $response_params, false);
        }
    }

    private static function orderDepartmentalStatus($data)
    {
        $params['entity_type_id']       = $data->assign_entity_type_id;
        $params['entity_id']            = $data->assign_entity_id;
        $params['departmental_status']  = $data->next_node;
        $params['wfs_ti_id']            = $data->next_node_instance_id;
        $params['user_id']              = $data->user_id;
        $params['department_id']        = $data->department_id;
        $params['role_id']              = $data->role_id;
        $params['order_status']         = $data->order_status;

        return $params;
    }
}