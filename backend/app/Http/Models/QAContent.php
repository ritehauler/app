<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// models
#use App\Http\Models\Setting;

class QAContent extends Base {
	
	use SoftDeletes;
    public $table = 'qa_content';
    public $timestamps = true;
	public $primaryKey;
    protected $dates = ['deleted_at'];
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table;
		$this->primaryKey = 'content_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
        $this->__fields   = array($this->primaryKey, 'content_type_id', 'language_id', 'title', 'content', 'created_at', 'updated_at', 'deleted_at');
	}
    
    public function insertContent($content_type_id, $language_id, $title, $content){
        $qa_content = QAContent::create();
        $qa_content->content_type_id = $content_type_id;
        $qa_content->language_id = $language_id;
        $qa_content->title = $title;
        $qa_content->content = $content;
        $qa_content->save();
        
        if($qa_content->save()) {
            return $qa_content->content_id;
        }
        return false;
    }

    /**
     * remove
     * @param integer $user_id
     * @return NULL
     */
    function remove($content_id = 0)
    {
        //identifing package type and getting container id
        $query = $this->selectRaw("qac.".$this->primaryKey.", qapcm.package_id, qapcrm.container_id, qapcrm.package_container_map_id, qapcm.package_content_map_id, qapt.identifier");
        $query->join("qa_package_content_map AS qapcm", "qac.content_id", "=", "qapcm.content_id");
        $query->join("qa_package_container_map AS qapcrm", "qapcrm.package_id", "=", "qapcm.package_id");
        $query->join("qa_package_type AS qapt", "qapcrm.package_type_id", "=", "qapt.package_type_id");
        $query->from("qa_content AS qac");
        $query->where("qac.content_id", '=', $content_id);
        $record = $query->get();

        // init Models
        $qa_package_container_map_model = new \App\Http\Models\QAPackageContainerMap;
        $qa_package_content_map_model = new \App\Http\Models\QAPackageContentMap;
        $qa_container_model = new \App\Http\Models\QAContainer;
        $qa_bag_container_map_model = new \App\Http\Models\QABagContainerMap;
        $qa_package_model = new \App\Http\Models\QAPackage;
        $qa_user_input_model = new \App\Http\Models\QAUserInput;

        if($record[0]->identifier == 'question'){ 	// if question
            //get all package container ids
            $package_container_map_ids = $qa_package_container_map_model->select("*")
                ->where('container_id', '=', $record[0]->container_id)
                ->whereNull('deleted_at')->get();

            foreach($package_container_map_ids as $package_container_map_id){
                $query = $this->selectRaw("qac.".$this->primaryKey.", qapcm.package_id, qapcrm.container_id, qapcrm.package_container_map_id, qapcm.package_content_map_id, qapt.identifier");
                $query->join("qa_package_content_map AS qapcm", "qac.content_id", "=", "qapcm.content_id");
                $query->join("qa_package_container_map AS qapcrm", "qapcrm.package_id", "=", "qapcm.package_id");
                $query->join("qa_package_type AS qapt", "qapcrm.package_type_id", "=", "qapt.package_type_id");
                $query->from("qa_content AS qac");
                $query->where("qapcrm.package_id", '=', $package_container_map_id->package_id);
                $query->where("qapcrm.container_id", '=', $package_container_map_id->container_id);
                $record = $query->get();

                // remove current model data
                $this->hardRemove($record[0]->content_id);

                //remove package container map
                $qa_package_container_map_model->hardRemove($record[0]->package_container_map_id);

                //remove package content map
                $qa_package_content_map_model->hardRemove($record[0]->package_content_map_id);

                //remove package data
                $qa_package_model->hardRemove($record[0]->package_id);

                //User Input
                $user_input = $qa_user_input_model->select("user_input_id")
                    ->where("package_id", "=", $record[0]->package_id)
                    ->where("container_id", "=", $record[0]->container_id)
                    ->whereNull("deleted_at")
                    ->get();
                if(isset($user_input[0])){
                    //remove bag container map
                    $qa_user_input_model->hardRemove($user_input[0]->user_input_id);
                }

            }

            //remove container data
            $qa_container_model->hardRemove($record[0]->container_id);

            //bag container map
            $bag_container_data = $qa_bag_container_map_model->select("bag_container_map_id")
                ->where("container_id", "=", $record[0]->container_id)
                ->whereNull("deleted_at")
                ->get();
            if(isset($bag_container_data[0])){
                //remove bag container map
                $qa_bag_container_map_model->hardRemove($bag_container_data[0]->bag_container_map_id);
            }

        }else{      //if answer
            // remove current model data
            $this->hardRemove($record[0]->{$this->primaryKey});

            //remove package container map
            $qa_package_container_map_model->hardRemove($record[0]->package_container_map_id);

            //remove package content map
            $qa_package_content_map_model->hardRemove($record[0]->package_content_map_id);

            //remove package data
            $qa_package_model->hardRemove($record[0]->package_id);

            //User Input
            $user_input = $qa_user_input_model->select("user_input_id")
                ->where("package_id", "=", $record[0]->package_id)
                ->where("container_id", "=", $record[0]->container_id)
                ->whereNull("deleted_at")
                ->get();
            if(isset($user_input[0])){
                //remove bag container map
                $qa_user_input_model->hardRemove($user_input[0]->user_input_id);
            }
        }
    }
}