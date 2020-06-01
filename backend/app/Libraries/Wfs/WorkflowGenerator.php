<?php
namespace App\Libraries\Wfs;


class WorkflowGenerator
{
	private $data;
	private $start_node;
	private $end_node = array();
	private $node = array();
	private $total_nodes = 0;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
	}

    public function generateJSON($data)
    {
        $this->data = $data;
        $this->_makeNode();
        $this->_generateJSON();
    }

    public function parseJSON($data)
    {
        $task_container = array();
        $task_key_container = array();
        $decision_key_container = array();
        $successor_container = array();
        $this->data = $data;
        $counter = 0;
        //print_r($data['nodeDataArray']);exit;
        //print_r($data['linkDataArray']);
        $wf_row['wf_title']				= '';
        $wf_row['wf_type']				= 'State Machine';
        $wf_row['wf_wfs_state_id']		= '1';
        $wf_row['wf_created_by_id']		= '1';
        $wf_row['wf_status_id'] 		= '1';
        $wf_row['wf_description']		= '';
        $wf_row['wf_pre_condition']		= '';
        $wf_row['wf_post_condition'] 	= '';
        $wf_row['wf_successor_wft_id']	= '';
        $wf_row['wf_start_tt_id']		= '';
        $wf_row['wf_end_tt_id'] 		= '';
        $wf_row['wf_access_arr'] 		= '';
        $wf_row['wf_access_type']		= '';
        $wf_row['wf_can_copy']			= '';
        $wf_row['wf_can_delete'] 		= '';
        $wf_row['wf_can_start'] 		= '';
        $min_key = 0;
        $max_key = 0;
        $task_container[0] = $wf_row;

        foreach($data['nodeDataArray'] as $task){
            if(isset($task['category'])) {
                if($task['category'] == 'comment')
                    $wf_row['wf_title'] = $task['text'];
            }else{
                if(isset($task['figure'])){
                    //$task['figure'] == 'Diamond';
                    $task_container[$counter-1]['tt_post_conditions'] = json_encode(array("php" =>$task['text'], "sql" => "", "js" => ""));
                    $task_container[$counter-1]['key'] = $task['key'];
                    $decision_key_container[] = $task['key'];
                }else {
                    if(($task['key'] < $min_key && $task['key'] > 0) || $min_key == 0){
                        $min_key = $task['key'];
                        $wf_row['wf_start_tt_id'] = $task['text'];
                    }
                    if($task['key'] > $max_key && $task['key'] > 0){
                        $max_key = $task['key'];
                        $wf_row['wf_end_tt_id'] = $task['text'];
                    }
                    $task_container[$counter] = $this->_taskMap($task);
                    $task_key_container[$task['key']] = $task['text'];
                    $counter++;
                }
            }
        }
        //print_r($wf_row);
        foreach($data['linkDataArray'] as $link){
            if($link['to'] > 0 && $link['from'] > 0 && !in_array($link['to'],$decision_key_container)) {
                $successor_container[$link['from']][] = $task_key_container[$link['to']];
            }
        }

        foreach($task_container as $index => $task){
            if(isset($successor_container[$task['key']]))
                $task['tt_successor_tt_id'] = implode(',',$successor_container[$task['key']]);
            unset($task['key']);
            $tmp = array_merge($wf_row,$task);
            $task_container[$index + 1] = array_values($tmp);
        }
        //print_r($task_container);exit;
        return $task_container;
    }

    private function _taskMap($task_node)
    {


        $row['tt_title'] 			= $task_node['text'];
        $row['tt_task_type_id'] 	= '1';
        $row['tt_wfs_state_id'] 	= '1';
        $row['tt_successor_tt_id'] 	= '';
        $row['tt_pre_conditions']	= '';
        $row['tt_post_conditions'] 	= '';
        $row['tt_actions'] 			= '';
        $row['tt_params'] 			= '';
        $row['tt_retry_limit'] 		= '';
        $row['tt_expiry_duration'] 	= '';
        $row['tt_expiry_type'] 		= '';
        $row['tt_created_by_id'] 	= '1';
        $row['tt_status_id'] 		= '1';
        $row['tt_description'] 		= '';
        $row['tt_access']	 		= '';
        $row['tt_access_type'] 		= '';
        $row['tt_can_copy'] 		= '';
        $row['tt_can_delete'] 		= '';
        $row['tt_can_start'] 		= '';
        $row['tt_assign_to'] 		= '';

        $row['key']         		= $task_node['key'];


        //print_r(explode(',',implode(',',$row)));
        //print_r($task_node);
        //exit;
        return $row;
    }

	private function _makeNode()
	{
		$node = array();
		$counter = 0;
		foreach($this->data as $row){
			$this->start_node = $row->start_tt_id;
			$this->end_node = $row->end_tt_id;
			$this->total_nodes++;
			$node[$counter]['node_id'] = $row->tt_id;
			$node[$counter]['wft_title'] = $row->wft_title;
			$node[$counter]['title'] = $row->tt_title;
			$node[$counter]['successor'] = $row->successor;
			$node[$counter]['from'] = $row->tt_id;
			$node[$counter]['to'] = explode(',',$row->successor);
			$node[$counter]['features']['actions'] = $row->actions;
			$node[$counter]['features']['expiry_duration'] = $row->expiry_duration;
			$node[$counter]['features']['expiry_type'] = $row->expiry_type;
			$node[$counter]['features']['params'] = $row->params;
			$node[$counter]['features']['pre_conditions'] = $row->pre_conditions;
			$node[$counter]['features']['post_conditions'] = $row->post_conditions;
			$node[$counter]['features']['retry_limit'] = $row->retry_limit;
			$counter++;
		}
		$this->node = $node;
		return;
	}

	private function _generateJSON($json_type = 'gojs')
	{
		$this->$json_type();
	}

	private function gojs($chart_type = 'FlowChart')
	{
		switch($chart_type){
			case 'StateChart':
				$this->gojsStateChart();
				break;
			case 'FlowChart':
				$this->gojsFlowChart();
				break;
		}
	}

	private function gojsStateChart()
	{

		//print_r($this->node);
		$response_json = array();
		$response_json['nodeKeyProperty'] = 'id';
		$response_json['nodeDataArray'] = array();
		$response_json['linkDataArray'] = array();
		$counter = 0;
		$counter2 = 0;
		$flag = 0;
		$x_axis = 100;
		$y_axis = 100;
		foreach($this->node as $node) {
			//print_r($node);exit;
			$response_json['nodeDataArray'][$counter]['id'] = $node['node_id'];
			$response_json['nodeDataArray'][$counter]['loc'] = "$x_axis $y_axis";
			$response_json['nodeDataArray'][$counter]['text'] = $node['title'];
			//print_r($node['to']);exit;
			foreach ($node['to'] as $to) {
				$response_json['linkDataArray'][$counter2]['from'] = $node['from'];
				$response_json['linkDataArray'][$counter2]['to'] = $to;
				$response_json['linkDataArray'][$counter2]['text'] = '';
				$counter2++;
			}
			$counter++;
			if ($flag == 0){
				$x_axis += 200;
				$flag = 1;
			}else {
				$y_axis += 100;
				$flag = 0;
			}
		}
		//print_r($response_json);
		print_r(json_encode($response_json));
		exit;
	}

	private function gojsFlowChart()
	{
		//print_r($this->node);exit;
		$response_json = array();
		$response_json['class'] = 'go.GraphLinksModel';
		$response_json['linkFromPortIdProperty'] = 'fromPort';
		$response_json['linkToPortIdProperty'] = 'toPort';
		$response_json['nodeDataArray'] = array();
		$response_json['linkDataArray'] = array();
		$counter = 0;
		$counter2 = 0;
		$flag = 0;
		$x_axis = 0;
		$y_axis = 70;
		$last_to = array();
		$response_json['nodeDataArray'][$counter]['category'] = 'comment';
		$response_json['nodeDataArray'][$counter]['loc'] = "360 -10";
		$response_json['nodeDataArray'][$counter]['text'] = $this->node[0]['wft_title'];
		$response_json['nodeDataArray'][$counter]['key'] = '-13';

        $counter = 1;
		$response_json['nodeDataArray'][$counter]['category'] = 'Start';
		$response_json['nodeDataArray'][$counter]['loc'] = "175 0";
		$response_json['nodeDataArray'][$counter]['text'] = 'Start';
		$response_json['nodeDataArray'][$counter]['key'] = '-1';

        $counter = 2;
		foreach($this->node as $node) {
			//print_r($node);
			$response_json['nodeDataArray'][$counter]['key'] = $node['node_id'];
			$response_json['nodeDataArray'][$counter]['loc'] = "$x_axis $y_axis";
			$response_json['nodeDataArray'][$counter]['text'] = $node['title'];
			//print_r($node['to']);exit;
			foreach ($node['to'] as $to) {
                if($counter2 == 0) {
                    $response_json['linkDataArray'][$counter2]['from'] = '-1';
                    $response_json['linkDataArray'][$counter2]['to'] = $node['from'];
                    $response_json['linkDataArray'][$counter2]['fromPort'] = 'B';
                    $response_json['linkDataArray'][$counter2]['toPort'] = 'T';
                    $counter2++;
                }
				$response_json['linkDataArray'][$counter2]['from'] = $node['from'];
				$response_json['linkDataArray'][$counter2]['to'] = $to;
				$response_json['linkDataArray'][$counter2]['fromPort'] = 'B';
				$response_json['linkDataArray'][$counter2]['toPort'] = 'T';
				$counter2++;
                if(empty($to)) {
                    $last_to[] = $node['from'];
                    $flag = 1;
                    $x_axis += 50;
                }
			}

			$counter++;
			if ($flag == 0){
				$x_axis = 175;
                $y_axis += 50;
				$flag = 0;
			}else {
				$y_axis += 50;
				$flag = 0;
			}
		}
        $response_json['nodeDataArray'][$counter]['category'] = 'End';
        $response_json['nodeDataArray'][$counter]['loc'] = "175 $y_axis";
        $response_json['nodeDataArray'][$counter]['text'] = 'End';
        $response_json['nodeDataArray'][$counter]['key'] = '-2';

        foreach($last_to as $last) {
            $response_json['linkDataArray'][$counter2]['from'] = $last;
            $response_json['linkDataArray'][$counter2]['to'] = '-2';
            $response_json['linkDataArray'][$counter2]['fromPort'] = 'B';
            $response_json['linkDataArray'][$counter2]['toPort'] = 'T';
            $counter2++;
        }

        //print 'Yes ... im here';
		//print_r($response_json);
		print_r(json_encode($response_json));
		exit;
	}
}
