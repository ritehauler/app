<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Auth;
use View;
use DB;
use Validator;
use Illuminate\Http\Request;
use Redirect;
use Mail;
use Excel;
use File;
use URL;
// models
use App\Http\Models\Admin;
use App\Http\Models\AdminModule;
use App\Http\Models\AdminModulePermission;


class AssetController extends Controller {

	private $_assign_data = array(
		's_title' => 'Asset', // singular title
		'p_title' => 'Assets', // plural title
		'p_dir' => DIR_ADMIN, // parent directory
		'page_action' => 'Listing', // default page action
		'parent_nav' => 'qa-', // parent navigation id
		'err_msg' => '',
		'succ_msg' => '',

	);
	private $_module = "asset"; // (db module name, directory name, active nav id, routing name)
	private $_pk; // (primary key of module table : extracted from module i.e: {module_name}_id)
	private $_model = "Asset"; // name of primary model
	private $_json_data = array();
	private $_layout = "";

    /**
     * Prevent Unauthorized User
     */
    public function __construct() {
        dd("herere");
        $this->middleware('auth');
		// construct parent
		parent::__construct();

		// init models
		$this->_assign_data["admin_model"] = new Admin;
		$this->_assign_data["admin_module_permission_model"] = new AdminModulePermission;
		// set model path for views
		$this->_assign_data["model_path"] = "App\Http\Models\\";
		// init current module model
		$this->_model = $this->_assign_data["model_path"].$this->_model;
		$this->_model = $this->_assign_data["model"] = new $this->_model;
		// default nav id
		$this->_assign_data["active_nav"] = $this->_assign_data["parent_nav"].$this->_module;
		// set dir path
		$this->_assign_data["dir"] = $this->_assign_data["p_dir"].$this->_module."/";
		// set module name
		$this->_assign_data["module"] = $this->_module;
		// set primary key
		$this->_pk = $this->_assign_data["pk"] = $this->_module."_id";
		// assign meta from parent constructor
		$this->_assign_data["_meta"] = $this->__meta;
    }

    /**
     * Return data to admin listing page
     *
     * @return type Array()
     */
    public function index(Request $request) {
		//Checking module Authentication
        $this->_assign_data["admin_module_permission_model"]->checkModuleAuth($this->_module, "view", \Auth::user()->admin_group_id);

		// Module permissions
		$this->_assign_data["perm_add"] = $this->_assign_data["admin_module_permission_model"]->checkAccess($this->_module, "add", \Auth::user()->admin_group_id);
		$this->_assign_data["perm_update"] = $this->_assign_data["admin_module_permission_model"]->checkAccess($this->_module, "update", \Auth::user()->admin_group_id);
		$this->_assign_data["perm_del"] = $this->_assign_data["admin_module_permission_model"]->checkAccess($this->_module, "delete", \Auth::user()->admin_group_id);

        $view = View::make($this->_assign_data["dir"].__FUNCTION__, $this->_assign_data);
        return $view;
    }

	/**
     * Ajax Listing
     *
     * @return json
     */
    public function ajaxListing(Request $request) {
        // datagrid params : sorting/order
        $search_value = isset($_REQUEST['search']['value']) ? $_REQUEST['search']['value'] : '';
        $dg_order = isset($_REQUEST['order'][0]['column']) ? $_REQUEST['order'][0]['column'] : '';
        $dg_sort = isset($_REQUEST['order'][0]['dir']) ? $_REQUEST['order'][0]['dir'] : '';
        $dg_columns = isset($_REQUEST['columns']) ? $_REQUEST['columns'] : '';
        // default ordering
        if ($dg_order == "" && $dg_sort == "") {
            $dg_order = "created_at";
            $dg_sort = "ASC";
        } else {
            // fix invalid column
            $dg_order = $dg_order == 0 ? 1 : $dg_order;
            // get column field name
            $dg_order = $dg_columns[$dg_order]["data"];
			// fix joined column name
			$dg_order = str_replace("|",".",$dg_order);
        }

		// perform select actions
		$this->_selectActions($request);

        // init output
        $records = array();
        $records["data"] = array();

        // init query
        $query = $this->_model->select($this->_pk);
		$query->whereNull("deleted_at");

		// apply search
        $query = $this->_searchParams($request, $query);


        // get total records count
        $total_records = $query->count(); // total records
        //$total_records = count($query->get()); // total records
        // datagrid settings
        $dg_limit = intval($_REQUEST['length']);
        $dg_limit = $dg_limit < 0 ? $total_records : $dg_limit;
        $dg_start = intval($_REQUEST['start']);
        $dg_draw = intval($_REQUEST['draw']);
        $dg_end = $dg_start + $dg_limit;
        $dg_end = $dg_end > $total_records ? $total_records : $dg_end;



        // get records
        $query = $this->_model->select($this->_pk);
        $query->whereNull("deleted_at");

        // apply search
        $query = $this->_searchParams($request, $query);
		$query->take($dg_limit); // limit
        $query->skip($dg_start); // offset
        $query->orderBy($dg_order, $dg_sort); // ordering
        $paginated_ids = $query->get();

        // if records
        if (isset($paginated_ids[0])) {
			// Check Permissions
			$perm_update = $this->_assign_data["admin_module_permission_model"]->checkAccess($this->_module, "update", \Auth::user()->admin_group_id);
			$perm_del = $this->_assign_data["admin_module_permission_model"]->checkAccess($this->_module, "delete", \Auth::user()->admin_group_id);

            // collect records
			$i=0;
            foreach ($paginated_ids as $paginated_id) {
				$id_record = $this->_model->get($paginated_id->{$this->_pk});

				// status html
				$status = "";
				// options html
				$options = '<div class="btn-group">';
				// selectbox html
				$checkbox = '<label class="css-input css-checkbox css-checkbox-rounded css-checkbox-sm css-checkbox-primary">';
				$checkbox .= '<input type="checkbox" id="check_id_'.$id_record->{$this->_pk}.'" name="check_ids[]" value="'.$id_record->{$this->_pk}.'" />';
				$checkbox .= '<span></span> </label>';
				// manage options
				// - update
				if($perm_update) {
					//$options .= '<a class="btn btn-xs btn-default" type="button" href="'.\URL::to(DIR_ADMIN.$this->_module.'/update/'.$id_record->{$this->_pk}).'" data-toggle="tooltip" title="Update" data-original-title="Update"><i class="fa fa-pencil"></i></a>';
				}
				// - delete - given in checkbox
				if($perm_del) {
					$options .= '<a class="btn btn-xs btn-default grid_action_del" type="button" data-toggle="tooltip" title="" data-original-title="Delete"><i class="fa fa-times"></i></a>';
				}
				$options .= '</div>';

                //image OR icon
                if($id_record->type === 'image'){
                    // image html
    				$image = '<a target="_blank" class="img-link img-thumb" href="'.\URL::to(config("constants.ASSET_MANAGEMENT_PATH").$id_record->source).'"><img class="img-responsive" src="'.\URL::to(config("constants.ASSET_MANAGEMENT_PATH").$id_record->source).'" alt=""></a>';
                }elseif($id_record->type === 'audio'){
                    // image html
    				$image = '<a target="_blank" href="'.\URL::to(config("constants.ASSET_MANAGEMENT_PATH").$id_record->source).'"><span class="fa fa-file-audio-o fa-4x"></span></a>';
                }else{
                    $image = '<a target="_blank" href="'.\URL::to(config("constants.ASSET_MANAGEMENT_PATH").$id_record->source).'"><span class="fa fa-file-video-o fa-4x"></span></a>';
                }


				// collect data
                $records["data"][] = array(
                    "ids" => $checkbox,
                    "image" => $id_record->type === 'image' ? $image : $id_record->type == 'audio' ? $image : $image,
					"type" => ucfirst($id_record->type),
					"source" => wordwrap($id_record->source),
                    "created_at" => date(DATE_FORMAT_ADMIN,strtotime($id_record->created_at)),
                    "options" => $options
                );

				// increament
				$i++;

            }
        }


        $records["draw"] = $dg_draw;
        $records["recordsTotal"] = $total_records;
        $records["recordsFiltered"] = $total_records;

        echo json_encode($records);
    }

	/**
     * Search Params
     * @param $query query
     * @return query
     */
    private function _searchParams($request, $query) {
		// search
        // - source
        if($request->source != "") {
			$q = trim(strtolower($request->source));
			$query->where('source', 'like', "%$q%");
		}
		// - type
        if($request->type != "") {
			$q = trim(strtolower($request->type));
			$query->where('type', '=', "$q");
		}

		// - created_at
		if($request->created_at != "") {
			$q = trim($request->created_at);
			$query->where('created_at', 'like', date("Y-m-d", strtotime($q))." %");
		}
		return $query;
    }


    /**
     * Add
     *
     * @return view
     */
    public function add(Request $request) {
        //Checking module Authentication
        $this->_assign_data["admin_module_permission_model"]->checkModuleAuth($this->_module, __FUNCTION__, \Auth::user()->admin_group_id);

		// page action
		$this->_assign_data["page_action"] = ucfirst(__FUNCTION__);
		$this->_assign_data["route_action"] = strtolower(__FUNCTION__);

		// validate post form
		if($request->do_post == 1) {
			return $this->_add($request);
		}
		// validate post form
		if($request->do_import == 1) {
			return $this->_import($request);
		}

        $view = View::make($this->_assign_data["dir"].__FUNCTION__, $this->_assign_data);
        return $view;
    }



	/**
     * Add (private)
     *
     * @return view
     */
    private function _add(Request $request) {
		// init models
		$raw_model = new \App\Http\Models\RawFile;

		// filter params
		// trim/escape all
		$request->merge(@array_map('strip_tags', $request->all()));
		$request->merge(@array_map('trim', $request->all()));

		// default errors class
		$this->_json_data['removeClass'] = "hide";
		$this->_json_data['addClass'] = "show";

		// vars
		$raw_file_count = $raw_model->select('raw_id')
                    ->where('queue_id', '=', $request->queue_id)
                    ->whereNull('deleted_at')
                    ->count();

		// get all modules
		if ($request->asset_type == "") {
			$field_name = "asset_type";
			$this->_json_data['focusElem'] = "input[name=" . $field_name . "]";
			$this->_json_data['targetElem'] = "div[id=error_msg_" . $field_name . "]";
			$this->_json_data['text'] = "Select Asset Type";
		} else if ($raw_file_count == 0) {
			$field_name = "upload_files";
			//$this->_json_data['focusElem'] = "input[name=" . $field_name . "]";
			$this->_json_data['targetElem'] = "div[id=error_msg_" . $field_name . "]";
			$this->_json_data['text'] = "Please upload files";
		} else {

            //get raw data
            $query = $raw_model->select('*')
                    ->where('queue_id', '=', $request->queue_id)
                    ->whereNull('deleted_at')
                    ->get();

            if(isset($query[0])){
                //move file
    			foreach($query as $q){
                    $file_path_from = base_path() .'/'.config("constants.RAW_PATH").$q->title; // get file path
                    $file_path_to = base_path() .'/'.config("constants.ASSET_MANAGEMENT_PATH").$q->title; // upload file path
                    //copy($file_path_from, $file_path_to);

                    //Unlink files
                    File::delete($file_path_from);

                    //set record
                    $save['source'] = $q->title;
            		$save['type'] = $request->asset_type;
            		$save["created_at"] = date("Y-m-d H:i:s");

            		// insert
            		$record_id = $this->_model->put($save);

                    //Delete from raw file
                    $raw_model->remove($q->raw_file_id);
    			}



    			// set session msg
    			\Session::put(ADMIN_SESS_KEY.'success_msg', 'record has been added');
            }
			//redirect
			$this->_json_data['redirect'] = \URL::to(DIR_ADMIN.$this->_module);

		}
		// return json
		return $this->_json_data;
    }

    /**
     * Raw (public)
     * @param $request
     * @return
     */
    public function raw(Request $request){
		// params
        $reserve_name = (int)$request->reserve_name > 0 ? 1 : 0;

		// filesize
		$file_size = isset($_FILES["file"]["size"]) ? $_FILES["file"]["size"] : 0;

        if ($file_size > 0) {
            //file type
            $file_type = $_FILES['file']['type'];
            //$file_type = $request->file->getClientMimeType();
            $file_type = explode('/', $file_type);
			// content type
			$content_type = $file_type[0];

            if($request->reserve_name > 0) {
                //file name
                $file_name = $_FILES['file']['name'];
               /*
			   // filetype
                $content_type = "image"; // default
                if(preg_match("@^(image_)@i")) {
                    $content_type = "image";
                } else if(preg_match("@^(audio_)@i")) {
                    $content_type = "audio";
                } else if(preg_match("@^(video_)@i")) {
                    $content_type = "video";
                } else {
                  // nothing
                }*/

            } else {
                //file name
				$file_name = $file_type[0].'_'.Auth::user()->admin_id.'_'.uniqid().'.'.$request->file->getClientOriginalExtension();
            }
            //move file
            $destination_path = base_path() .'/'.config("constants.RAW_PATH"); // upload path
			move_uploaded_file($_FILES["file"]["tmp_name"],$destination_path.$file_name);

            // set record
    		$save['title'] = $file_name;
    		$save['type'] = $content_type;
    		$save['queue_id'] = $request->queue_id;
            $save['params'] = json_encode($_FILES['file']);
    		$save["created_at"] = date("Y-m-d H:i:s");

    		// insert
            $raw_model = new \App\Http\Models\RawFile;
    		$record_id = $raw_model->put($save);
        }

    }

	/**
     * Import (private)
     *
     * @return view
     */
    private function _import(Request $request) {
		// init models
		$raw_model = new \App\Http\Models\RawFile;

		// trim/escape all
		$request->merge(array_map('strip_tags', $request->all()));
		$request->merge(array_map('trim', $request->all()));

        // vars
		$raw_file_count = $raw_model->select('raw_id')
			->where('queue_id', '=', $request->queue_id)
            ->whereNull('deleted_at')
			->count();

		// default errors class
		$this->_json_data['removeClass'] = "hide";
		$this->_json_data['addClass'] = "show";

		// get all modules
		if ($raw_file_count == 0) {
			$field_name = "upload_files2";
			//$this->_json_data['focusElem'] = "input[name=" . $field_name . "]";
			$this->_json_data['targetElem'] = "div[id=error_msg_" . $field_name . "]";
			$this->_json_data['text'] = "Please upload files";
		} else {
			// init var
			$count_copied = 0; // copied files count
			$raw_file_path = base_path() .'/'.config("constants.RAW_PATH"); // get file path
			$destination_file_path = base_path() .'/'.config("constants.ASSET_MANAGEMENT_PATH"); // upload file path

			//get raw data
            $raw_files = $raw_model->select('*')
				->where('queue_id', '=', $request->queue_id)
				->whereNull('deleted_at')
				->get();

            if(isset($raw_files[0])){
                //move file
    			foreach($raw_files as $q){

					$copied = @copy($raw_file_path.$q->title, $destination_file_path.$q->title);

					if($copied) {
						$count_copied++;

						// remove file
						@unlink($raw_file_path.$q->title);
						// remove record
						$raw_model->remove($q->raw_file_id);

						// filetype
						$content_type = "image"; // default

						// set record
						$save['source'] = $q->title;
						$save['type'] = $q->type;
						$save["created_at"] = date("Y-m-d H:i:s");
						// insert
						$record_id = $this->_model->put($save);

					}
    			}

            }

			// set session msg
			\Session::put(ADMIN_SESS_KEY.'success_msg', 'Successfully saved '.$copied.' out of '.$raw_file_count. ' assets');

			//redirect
			$this->_json_data['redirect'] = \URL::to(DIR_ADMIN.$this->_module);
		}
		// return json
		return $this->_json_data;
    }


	/**
     * Update
     *
     * @return view
     */
    public function update(Request $request, $id) {
        //Checking module Authentication
        $this->_assign_data["admin_module_permission_model"]->checkModuleAuth($this->_module, __FUNCTION__, \Auth::user()->admin_group_id);

		// page action
		$this->_assign_data["page_action"] = ucfirst(__FUNCTION__);
		$this->_assign_data["route_action"] = strtolower(__FUNCTION__);

		// get record
		$this->_assign_data["data"] = $this->_model->get($id);

		// redirect on invalid record
		if($this->_assign_data["data"] == FALSE) {
			// set session msg
			\Session::put(ADMIN_SESS_KEY.'error_msg', 'Invalid record selection');
			// redirect
			return redirect(\URL::to(DIR_ADMIN.$this->_module));
		}

		// validate post form
		if($request->do_post == 1) {
			return $this->_update($request, $this->_assign_data["data"]);
		}

        $view = View::make($this->_assign_data["dir"].__FUNCTION__, $this->_assign_data);
        return $view;
    }

	/**
     * Update (private)
     *
     * @return view
     */
    private function _update(Request $request, $data) {
        // filter params
		// trim/escape all
		$request->merge(array_map('strip_tags', $request->all()));
		$request->merge(array_map('trim', $request->all()));
		// type casting
		$request->from_xp = intval(trim($request->from_xp));
		$request->to_xp = intval(trim($request->to_xp));

		// default errors class
		$this->_json_data['removeClass'] = "hide";
		$this->_json_data['addClass'] = "show";


		// validator
		$valid_email = Validator::make(array('slug' => $request->slug), array('slug' => 'required|unique:page,slug,'.$data->{$this->_pk}.','.$this->_pk));

		// get all modules
		if ($request->title == "") {
			$field_name = "title";
			$this->_json_data['focusElem'] = "input[name=" . $field_name . "]";
			$this->_json_data['targetElem'] = "div[id=error_msg_" . $field_name . "]";
			$this->_json_data['text'] = "Please enter Title";
		} else if ($request->level_type == "") {
			$field_name = "level_type";
			$this->_json_data['focusElem'] = "select[name=" . $field_name . "]";
			$this->_json_data['targetElem'] = "div[id=error_msg_" . $field_name . "]";
			$this->_json_data['text'] = "Select level type";
		} else if ($request->from_xp === "") {
			$field_name = "from_xp";
			$this->_json_data['focusElem'] = "input[name=" . $field_name . "]";
			$this->_json_data['targetElem'] = "div[id=error_msg_" . $field_name . "]";
			$this->_json_data['text'] = "Please enter XP Ranges (From)";
		} else if ($request->to_xp == 0) {
			$field_name = "to_xp";
			$this->_json_data['focusElem'] = "input[name=" . $field_name . "]";
			$this->_json_data['targetElem'] = "div[id=error_msg_" . $field_name . "]";
			$this->_json_data['text'] = "Please enter valid XP Ranges (to)";
		 } else if ($request->to_xp <= $request->from_xp) {
			$field_name = "to_xp";
			$this->_json_data['focusElem'] = "input[name=" . $field_name . "]";
			$this->_json_data['targetElem'] = "div[id=error_msg_" . $field_name . "]";
			$this->_json_data['text'] = "Please enter valid XP Ranges (To should be greater then From)";
		} else if ($request->schema == "") {
			$field_name = "schema";
			$this->_json_data['focusElem'] = "input[name=" . $field_name . "]";
			$this->_json_data['targetElem'] = "div[id=error_msg_" . $field_name . "]";
			$this->_json_data['text'] = "Please design Schema";
		} else if ($request->schema == "") {
			$field_name = "schema";
			$this->_json_data['focusElem'] = "input[name=" . $field_name . "]";
			$this->_json_data['targetElem'] = "div[id=error_msg_" . $field_name . "]";
			$this->_json_data['text'] = "Please design Schema";
		} else {
			$save = (array)$data;
			// set record
			$save['title'] = $request->title;
			$save['level_type'] = $request->level_type;
			$save['from_xp'] = $request->from_xp;
			$save['to_xp'] = $request->to_xp;
			$save['schema'] = $request->schema;
			$save["updated_at"] = date("Y-m-d H:i:s");


			// update
			$this->_model->set($save[$this->_pk], $save);
			// set pk
			$record_id = $save[$this->_pk];

			// set conf update
			$conf_model = new \App\Http\Models\Conf;
			$conf_model->setUpdated("game_config");

			// set session msg
			\Session::put(ADMIN_SESS_KEY.'success_msg', 'record has been updated');

			//redirect
			$this->_json_data['redirect'] = \URL::to(DIR_ADMIN.$this->_module);
		}
		// return json
		return $this->_json_data;
    }



    /**
     * Select Action
     *
     * @return query
     */
    private function _selectActions($request) {
		$request->select_action = trim($request->select_action);
		$request->checked_ids = is_array($request->checked_ids) ? $request->checked_ids : array();

        if($request->select_action != "" && isset($request->checked_ids[0])) {
			$i_affected = 0;
			foreach($request->checked_ids as $checked_id) {
				$record = $this->_model->get($checked_id);
				// if valid record
				if($record !== FALSE) {
					// if delete
					if($request->select_action == "delete") {
						// remove current
						$this->_model->remove($record->{$this->_pk});
					}

					/*// active/inactive
					if($request->select_action == "active" || $request->select_action == "ban") {
						$record->status = $request->select_action == "active" ? 1 : 2;
						$record->updated_at = date("Y-m-d H:i:s");
						$this->_model->set($record->{$this->_pk},(array)$record);
					}*/

					$i_affected++;

				}
			}

			// if affected
			if($i_affected > 0) {
				// set conf update
				$conf_model = new \App\Http\Models\Conf;
				$conf_model->setUpdated("game_config");
			}

		}

    }

	/**
     * image browser
     *
     * @return view
     */
    public function imageBrowser(Request $request) {
		//Checking module Authentication
        $this->_assign_data["admin_module_permission_model"]->checkModuleAuth($this->_module, $request->referrer_action, \Auth::user()->admin_group_id);
		// page action
		$this->_assign_data["page_action"] = ucfirst(__FUNCTION__);
		$this->_assign_data["route_action"] = strtolower(__FUNCTION__);
		// view file
		$view_file = strtolower(preg_replace('/(.)([A-Z])/', '$1_$2', __FUNCTION__));

        $view = View::make($this->_assign_data["dir"].$view_file, $this->_assign_data);
        return $view;
    }


	/**
     * Fetch Items
     *
     * @return json
     */
    public function fetchItems(Request $request) {
		// filter params
		// trim/escape all
		$params = $request->q;
		// trim/escape all
		$q = isset($params["term"]) ? $params["term"] : "";
		$q = trim(strip_tags($q));
		// type casting
		$request->page = intval(trim($request->page));


		// init output
        $records = array();
        $records["results"] = array();

        // init query
        $query = $this->_model->select($this->_pk);
		$query->whereNull("deleted_at");
		$query->where("title", "like", "%".$q."%");

        // get total records count
        $total_records = $query->count(); // total records
        //$total_records = count($query->get()); // total records
        // offfset / limits / valid pages
		$page_limit = 10;
		$page_no = $request->page;
		$total_pages = ceil($total_records / $page_limit);
		$page_no = $page_no >= $total_pages ? $total_pages : $page_no;
		$page_no = $page_no <= 1 ? 1 : $page_no;
		$offset = $page_limit * ($page_no - 1);

        // get records
        $query = $this->_model->select($this->_pk);
        $query->whereNull("deleted_at");
		$query->where("title", "like", "%".$q."%");
		$query->take($page_limit);
		$query->skip($offset);
		$query->orderBy("title", "ASC"); // ordering
		$paginated_ids = $query->get();

        // if records
        if (isset($paginated_ids[0])) {
            // collect records
			$i=0;
            foreach ($paginated_ids as $paginated_id) {
				$id_record = $this->_model->get($paginated_id->{$this->_pk});

				// collect data
                $records["results"][] = array(
                    "id" => $paginated_id->{$this->_pk},
					"title" => $id_record->title
                );

            }
        }


        $records["total_count"] = $total_records;
        echo json_encode($records);
	}


}
