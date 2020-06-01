<?php
namespace App\Libraries\Wfs;


class UIBTileGenerator
{
	public $data,$decisonConstraints;
    private $_tile_view_path;
    private $_processed_data = array();

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct($data_in)
	{
        //print_r($data_in);
        $this->_tile_view_path = app_path('..\resources\views\uib_tiles');
        $this->_organizeData($data_in);
	}

    // make total tiles in incoming data
    private function _organizeData($data_in)
    {
        $processed_data = array();
        $counter = 0;
        foreach($data_in['screen_data'] as $row)
        {
            $row = array_merge((array)$data_in['user_data'],(array)$row,(array)$data_in['task_data']);
            $processed_data['screen_id'] = $row['screen_id'];
            $processed_data['screen_title'] = $row['screen_title'];
            $processed_data['screen_caption'] = $row['screen_caption'];
            $tile_path = $this->_tile_view_path . '\\' .$row['tile_template'];
            $content = file_get_contents($tile_path);
            $processed_data['tile'][$counter] = $row;
            foreach($row as $key => $value) {
                $content = str_replace("<!--$key-->",$value,$content);
            }
            $processed_data['tile'][$counter]['content'] = $content;
            $counter++;
        }
        $this->_processed_data = $processed_data;
    }

    public function getProcessedData()
    {
        return $this->_processed_data;
    }

}
