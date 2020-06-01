<?php
namespace App\Libraries\Wfs;

use App\Http\Models\SYSRole;
use App\Http\Models\WFSTaskInstance;
use App\Http\Models\WFSTaskTemplate;
use App\Http\Models\WFSWFTTTRelation;
use App\Libraries\Wfs\Dictionary;
class WFSTaskExecution
{
	public $data,$decisonConstraints;
	public $successors;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct($data)
	{
        $this->decisonConstraints['next_node'] = '#';
        $this->decisonConstraints['is_admin'] = 0;
        $this->decisonConstraints['is_redirection'] = 0;
        $this->decisonConstraints['is_expired'] = 0;
        $this->decisonConstraints['next_node_id'] = '#';
        $this->decisonConstraints['redirect_url'] = '';
        $this->successors = array();
        $this->data = $data;
        $this->objDic = new WFSDictionary();
        $search_items = array();
        $replace_items = array();

        foreach($data[0] as $key => $value){

            if(in_array($key,array('pre_conditions','post_conditions','is_expired')))
                $this->objDic->$key = new WFSVariableRights($key, $value, WFSVariableRights::CHANGEABLE);
            else
                $this->objDic->$key = new WFSVariableRights($key, $value, WFSVariableRights::READABLE);

            $search_items[] = '$'.$key;
            $replace_items[] = '$this->objDic->'.$key.'->getValue()';

            $this->data[0]->$key =  '';
        }

        $this->objDic->pre_conditions->resetValue(str_replace($search_items,$replace_items,$this->objDic->pre_conditions->getValue()));
        $this->objDic->post_conditions->resetValue(str_replace($search_items,$replace_items,$this->objDic->post_conditions->getValue()));
	}

    public function execute()
    {
        $func_array = array('_preConditions','_evaluateExpiry','_postConditions');
        foreach($func_array as $func){
            $this->$func();
            if($this->decisonConstraints['next_node'] != '#'){
                break;
            }
        }
        $this->evaluateSuccessors();
        $this->data[0]->next_node = '';
        $this->data[0]->next_node_id = '';
        $this->data[0]->is_admin = '';
        $this->data[0]->is_redirection = '';

        $this->objDic->next_node = new WFSVariableRights('next_node',$this->decisonConstraints['next_node'], WFSVariableRights::READABLE);
        $this->objDic->next_node_id = new WFSVariableRights('next_node_id',$this->decisonConstraints['next_node_id'], WFSVariableRights::READABLE);
        $this->objDic->is_admin = new WFSVariableRights('is_admin',$this->decisonConstraints['is_admin'], WFSVariableRights::READABLE);
        $this->objDic->is_redirection = new WFSVariableRights('is_redirection',$this->decisonConstraints['is_redirection'], WFSVariableRights::READABLE);

        $this->_prepareData();
        return;
    }

	private function _preConditions()
    {
        $pre_conditions = json_decode($this->objDic->pre_conditions->getValue(),true);

        eval($pre_conditions['php']);
        foreach($this->decisonConstraints as $key => $value){
            if(isset(${$key}))
                $this->decisonConstraints[$key] = ${$key};
        }
        return;
    }

    private function _postConditions()
    {
        $post_conditions = json_decode($this->objDic->post_conditions->getValue(),true);

        eval($post_conditions['php']);
        foreach($this->decisonConstraints as $key => $value){
            if(isset(${$key}))
                $this->decisonConstraints[$key] = ${$key};
        }
        return;
    }

	public function params(){}

	public function actions(){}

	private function _evaluateExpiry(){
        $now = date("Y-m-d H:m:s");
        $expiry_time = date("Y-m-d H:i:s", strtotime($this->objDic->start_date->getValue() . ' +'.$this->objDic->expiry_duration->getValue() .' '. $this->objDic->expiry_type->getValue()));

        if(($expiry_time < $now || $this->objDic->end_date->getValue() <= $now) && !empty($this->objDic->expiry_duration->getValue()) && !empty($this->objDic->start_date->getValue()) && !empty($this->objDic->expiry_type->getValue()) && !empty($this->objDic->end_date->getValue())) {
            $this->decisonConstraints['is_expired'] = 1;
            $this->objDic->is_expired->resetValue(1);
            $this->is_expired = 1;
        }
        if(($this->objDic->retry_limit->getValue() < $this->objDic->retry_attemps->getValue()) && !empty($this->objDic->retry_limit->getValue())){
            $this->decisonConstraints['is_expired'] = 1;
            //$this->data[0]->is_expired = 1;
            $this->objDic->is_expired->resetValue(1);
            $this->is_expired = 1;
        }
    }

	public function evaluateSuccessors()
    {
        $successor_titles = array();
        if(count($this->successors) == 1){
            $this->decisonConstraints['next_node_id'] = $this->successors[0]->ti_id ;
            if ($this->decisonConstraints['is_redirection'])
                $this->decisonConstraints['redirect_url'] = "workflow/task/instance/" . $this->successors[0]->ti_id ;
        }else {
            foreach ($this->successors as $successor) {
                if ($successor->tt_title == $this->decisonConstraints['next_node']) {
                    $this->decisonConstraints['next_node_id'] = $successor->ti_id;
                    if ($this->decisonConstraints['is_redirection'])
                        $this->decisonConstraints['redirect_url'] = "workflow/task/instance/" . $successor->ti_id;
                }
                $successor_titles[] = $successor->tt_title;
            }
        }
        $this->objDic->successor_titles = new WFSVariableRights('successor_titles', implode(',',$successor_titles), WFSVariableRights::READABLE);
        return;
    }

    private function _prepareData()
    {
        foreach($this->data[0] as $key => $value){
            $this->data[0]->$key = $this->objDic->$key->getValue();
        }
    }

    public function generateNextNode()
    {
        $start_date = date('Y-m-d H:m:s');
        $end_date = date('Y-m-d 23:59:59');
        $assign_entity_id = $this->data[0]->assign_entity_id;
        $assign_entity_type_id = $this->data[0]->assign_entity_type_id;
        $assign_by_id = 1;
        $activation_state = 1;
        $next_node_instance_id = 0;
        $next_node = $this->data[0]->next_node;
        $next_node_id = $this->data[0]->next_node_id;

        /*print "if(!empty($next_node_id) || $next_node_id != '#') {";
        exit;*/
        if(!empty($next_node_id) && $next_node_id != '#') {
            $data = WFSTaskTemplate::getTT($next_node_id);
            $assign_to = $this->_getDepartmentGroup($data);
            $this->data[0]->next_node_external_url = $data->external_url;
            $this->data[0]->next_node_is_external = $data->is_external;

            $wfi_id = $this->data[0]->wfi_id;
            $next_node_instance_id = WFSTaskInstance::insertWFSInstance($next_node_id, $start_date, $end_date, $assign_entity_id, $assign_entity_type_id, $assign_by_id, $activation_state, $assign_by_id, $assign_to);
            WFSWFTTTRelation::insertWFSInstance($wfi_id,array($next_node_instance_id));
            $this->data[0]->next_node_instance_id = $next_node_instance_id;
        }
        return $next_node_instance_id;
    }


    private function _getDepartmentGroup($wft)
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
        return implode('.',$response);
    }

}
