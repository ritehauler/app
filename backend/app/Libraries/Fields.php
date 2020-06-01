<?php namespace App\Libraries;
use App\Http\Models\SYSAttribute;
use App\Http\Models\SYSAttributeOption;
use App\Http\Models\ApiMethodField;
use App\Http\Models\SYSCategory;
use App\Libraries\ApiCurl;

/**
 * Simple Fields Library
 *
 *
 * @category   Libraries
 * @package    Fields
 * @subpackage Libraries

 */
class Fields
{
	public $modalPath = "\App\Http\Models\\";
	private $_dropdownDisabled = false;
    /**
     * Constructor
     *
     * @param string $url URL
     */
    public function __construct(){
		
	}
 
    public function randerInput($field,$data=NULL,$entity_type_id=0,$el_attr=array()) {

		$div_class = (isset($el_attr['div_class']))?$el_attr['div_class']:"col-md-6";
		$lbla_class = (isset($el_attr['lbla_class']))?$el_attr['lbla_class']:"field-label cus-lbl";
		$lblb_class = (isset($el_attr['lblb_class']))?$el_attr['lblb_class']:"";
		$show_name = (isset($el_attr['show_name']))?true:false;
        $uri_method = (isset($el_attr['uri_method'])) ? $el_attr['uri_method'] : 'add';
		$select2_class = "select2-field";

		 $_value = $return = $field_class = '';
		$field_class =  $field->default_value;
	     $_type = $field->element_type;
		$validate_sign = ($field->type == 'required') ? '&nbsp;*' : '';

		$is_update = false;
		if(isset($data->attributes)){
			$is_update = true;
		}

		 if(\Session::has(ADMIN_SESS_KEY.'_POST_DATA')){

			 //if data is from user management then donot set session post data
			 //b/c it make blank to auth fields

			 if(isset($data->auth) && isset($data->entity_auth_id)) {
				 if ($data->entity_auth_id == 0) {
					 $data = (object)\Session::get(ADMIN_SESS_KEY . '_POST_DATA');
				 }
			 }
			 else{
				 $data = (object)\Session::get(ADMIN_SESS_KEY . '_POST_DATA');
			 }


			$data->attributes = (object)\Session::get(ADMIN_SESS_KEY . '_POST_DATA');
			//\Session::forget(ADMIN_SESS_KEY.'_POST_DATA');
			//echo '<pre>'; print_r($data); echo '</pre>'; exit;
		}
		  
		if($_type!='query'){
			$field_name = $field->name;
			$_value = (isset($data->{$field->name}))?$data->{$field->name}:'';
			$field_id = $field->name."_id";
			if($field->is_read_only=="0"){
				$name = "name=\"$field->name\" id=\"$field->name\"";
				if($uri_method == 'view')
                    $name .= 'readonly=""readonly"';
				
			}elseif($uri_method == 'view' || $field->is_read_only=="1"){
				$name = "readonly=\"readonly\" id=\"$field->name\"";
			}

			$div_id = "id=\"div_$field->name\"";
		}
		$field_class = $field->default_value." field_$_type";

		/*if entity is user management then set auth column values*/
		if(isset($data->auth) && $data->entity_auth_id > 0){

			if(isset($data->auth->{$field->name})){

				$_value = $data->auth->{$field->name};
				$readonly_auth_fields = array("email","mobile_no");

				if(in_array($field->name,$readonly_auth_fields )){
					$name = "readonly=\"readonly\" id=\"$field->name\"";
				}
			}
		}

		switch($_type){
		  case'radio':
		  case'option':
			 	if($field->data_type == "callback"){
					$return = "";
					break;
				}

		  	     $field_title = ($show_name)?$field_name:$field->description;
				 $field_desc  = $field->description;
		  		 $return = "<div $div_id class=\"$div_class field_$field->element_type\"><div class=\"section mb20\">";
		  		 $return .= "<label data-toggle=\"tooltip\" class=\"$lbla_class field-label cus-lbl\" title=\"$field_desc\">".$field_title.$validate_sign."</label>";
		  		 $options=false;
				if(empty($field->depend_table)){ 
					$SYSAttribute = new SYSAttribute();	
					$record = $SYSAttribute
					->where("attribute_code", "=",$field->name)
					->where("data_type_id", "=","6")
					->whereNull("deleted_at")
					->first();
					
					if($record){
						$SYSAttributeOption = new SYSAttributeOption();
						$options = $SYSAttributeOption
						->where("attribute_id", "=",$record->attribute_id)
						->whereNull("deleted_at")
						->get();	
					}
				}else{
					$title = (!empty($field->depend_table_title))?$field->depend_table_title:'title';
					$option_value = (!empty($field->depend_table_value))?$field->depend_table_value:$field->name;
					$depend_table_where = "deleted_at IS NULL";
					if($field->depend_table_where!="") $depend_table_where= $field->depend_table_where." AND deleted_at IS NULL";
					$options = $this->select_table($field->depend_table,"$option_value AS `value`,$title AS `option`","WHERE $depend_table_where");

				}
				$return .= "<label class=\"$lblb_class field select \"><select $name class=\"$field_class form-control\">";
				$return.='<option value="'.$field->default_value.'">-- Select '.$field_desc.' --</option>';
				if($options){

					foreach($options as $_option){
						$selected = ($_value==$_option->value)?"selected=\"selected\"":'';
						$return .= " <option value=\"$_option->value\" $selected >$_option->option </option>";
					}	

				}
			$return .= "</select><i class=\"arrow\"></i></label></div></div>";
		  break;
		  case'query':
		   		//Get entity attributes
				$ApiMethodField = new ApiMethodField();
				$listfields =  $ApiMethodField->getEntityAttributeList($entity_type_id);

				if($listfields){

          			foreach ($listfields as $listfield){

						$queryDivClass = $div_class;
						if($listfield->data_type_identifier=='retchtext') $queryDivClass = 'col-md-12';

						$field_id = "id=\"$listfield->attribute_code\"";
						$div_id = "id=\"div_$listfield->attribute_code\"";


						if($listfield->data_type_identifier != "hidden") {
							$return .= "<div $div_id class=\"$queryDivClass field_query\">
									<div class=\"section mb20\">";
						}

						$return .= $this->randerEntityFields($listfield,$data,$entity_type_id,$is_update,$el_attr);

						if($listfield->data_type_identifier != "hidden") {
							$return .= "</div><div style=\"clear:both\"></div></div>";
						}

			 	}
		 }
				 
		  break;
		  case 'text':
		  case 'retchtext':
		  case 'textarea':
		  	$field_title = ($show_name)?$field_name:$field->description;
			$field_desc  = $field->description;
		  	$return = "<div $div_id class=\"$div_class field_$field->element_type\"><div class=\"section mb20\">";
			$return .= "<label data-toggle=\"tooltip\" class=\"$lbla_class field-label cus-lbl\" title=\"$field_desc\">".$field_title.$validate_sign."</label>";
			$return .= "<label class=\"$lblb_class field\">";
			$return .= "<textarea $name class=\"$field_class gui-input form-control\" >$_value</textarea>"; 
			$return .= "</label></div><div style=\"clear:both\"></div></div>";
		  break;
		  default:
		  	$field_title = ($show_name)?$field_name:$field->description;
			$field_desc  = $field->description;
		  	$return  = "<div $div_id class=\"$div_class field_$field->element_type\"><div class=\"section mb20\">";
			$return .= "<label data-toggle=\"tooltip\" class=\"$lbla_class\" title=\"$field_desc\">".$field_title.$validate_sign."</label>";
			$return .= "<label class=\"$lblb_class field\">";
			$return .= "<input  type=\"$_type\" $name class='field_input gui-input form-control' value=\"$_value\">";
			$return .= "</label></div><div style=\"clear:both\"></div></div>";
		  break;
		}
		return  $return;        
    }
	
	private function select_table($table_name,$columns="*",$where=""){
		$data = \DB::select("SELECT $columns FROM $table_name $where");	
		return $data;
	}

	/**
	 * @param $attributes
	 * @return array
	 */
	public static function setEntityAttributes($attributes)
	{
		$entity_attribute = array();
		if(isset($attributes)){

			foreach($attributes as $key => $value){


				if(is_object($value)){ //echo "<pre>"; print_r($value);
					$entity_attribute[$key] = array(
						'option_id' =>  isset($value->attribute_option_id) ? $value->attribute_option_id : "",
						'option' => isset($value->option) ? $value->option : "",//$value->option,
						'value' =>  isset($value->value) ? $value->value : "",//$value->value,
					);
				}
				else{
					$entity_attribute[$key] = $value;
				}

			}
		}

		return $entity_attribute;
	}

	/**
	 * This render api fields in view files
	 * @param $field
	 * @param null $data
	 * @param int $entity_type_id
	 * @param bool $is_update
	 * @param array $el_attr
	 * @return string
	 */
	public function randerFields($field,$data=NULL,$entity_type_id=0,$is_update = false,$el_attr=array()) {

		$div_class = (isset($el_attr['div_class']))?$el_attr['div_class']:"col-md-6";
		$lbla_class = (isset($el_attr['lbla_class']))?$el_attr['lbla_class']:"field-label cus-lbl";
		$lblb_class = (isset($el_attr['lblb_class']))?$el_attr['lblb_class']:"";
		$show_name = (isset($el_attr['show_name']))?true:false;
        $uri_method = (isset($el_attr['uri_method'])) ? $el_attr['uri_method'] : 'add';

		$_value = $return = $field_class = '';
		$field_class =  $field->default_value;
		$_type = $field->element_type;

		$readonly = '';
		if(($is_update && $field->is_read_only == 1) || $uri_method == 'view'){
			$readonly = 'readonly="readonly"';
		}
		$is_read_only_dropdown = (($is_update && $field->is_read_only == 1) || $uri_method == 'view') ? 'disabled="disabled"': '';

		if(\Session::has(ADMIN_SESS_KEY.'_POST_DATA')){

			//if data is from user management then donot set session post data
			//b/c it make blank to auth fields

			if(isset($data->auth) && isset($data->entity_auth_id)) {
				if ($data->entity_auth_id == 0) {
					$data = (object)\Session::get(ADMIN_SESS_KEY . '_POST_DATA');
				}
			}
			else{
				$data = (object)\Session::get(ADMIN_SESS_KEY . '_POST_DATA');
			}


			$data->attributes = (object)\Session::get(ADMIN_SESS_KEY . '_POST_DATA');
			//\Session::forget(ADMIN_SESS_KEY.'_POST_DATA');

		}

		if($_type!='query'){
			$field_name = $field->name;
			$_value = (isset($data->{$field->name}))?$data->{$field->name}:'';
			$field_id = $field->name."_id";

			if(($field->is_read_only=="1" && $is_update)  || $uri_method == 'view'){
				$name = "id=\"$field->name\"";
			}else{
				$name = "name=\"$field->name\" id=\"$field->name\"";
			}

			$div_id = "id=\"div_$field->name\"";
		}
		$field_class = $field->default_value." field_$_type";

		/*if entity is user management then set auth column values*/
		if(isset($data->auth) && $data->entity_auth_id > 0){

			if(isset($data->auth->{$field->name})){

				$_value = $data->auth->{$field->name};
				$readonly_auth_fields = array("email","parent_role_id");

				if(in_array($field->name,$readonly_auth_fields )){
					$name = "id=\"$field->name\" readonly=\"readonly\"";
				}
			}
		}

		if(isset($el_attr['search_columns'])){
			$validate_sign = '';
		}else{
			$validate_sign = ($field->type == 'required') ? '&nbsp;*' : '';
		}

		$hidden_dropdown_value = "";
		if(!empty($is_read_only_dropdown)){
			$hidden_dropdown_value =  '<input type="hidden" '.$name.' value="'.$_value.'" />';
		}


		switch($_type){
			case'radio':
			case'option':
				if($field->data_type == "callback"){
					$return = "";
					break;
				}

				$field_title = ($show_name)?$field_name:$field->description;
				$field_desc  = $field->description;
				$return = "";
				// $return = "<div $div_id class=\"$div_class field_$field->element_type\"><div class=\"section mb20\">";
				$return .= "<label data-toggle=\"tooltip\" class=\"$lbla_class field-label cus-lbl\" title=\"$field_desc\">".$field_title.$validate_sign."</label>";
				$options=false;
				if(empty($field->depend_table)){
					$SYSAttribute = new SYSAttribute();
					 $recored = $SYSAttribute
						->where("attribute_code", "=",$field->name)
						->where(function ($query) {
							$query->where("data_type_id", "=","6");
								$query->orWhere("data_type_id", "=","19");
        				})
						->whereNull("deleted_at")
						->first();

					if($recored){
						$SYSAttributeOption = new SYSAttributeOption();
						$options = $SYSAttributeOption
							->where("attribute_id", "=",$recored->attribute_id)
							->whereNull("deleted_at")
							->get();
					}
				}else{
					$title = (!empty($field->depend_table_title))?$field->depend_table_title:'title';
					$option_value = (!empty($field->depend_table_value))?$field->depend_table_value:$field->name;
					$depend_table_where = "deleted_at IS NULL";
					if($field->depend_table_where!="") $depend_table_where= $field->depend_table_where." AND deleted_at IS NULL";
					$options = $this->select_table($field->depend_table,"$option_value AS `value`,$title AS `option`","WHERE $depend_table_where");

				}

					if($field->element_type == "radio"){

						$field_title = ($show_name)? $field_name : $field->description;
						$field_desc  = $field->description;

						if($options){
							foreach($options as $_option){

								$selected = ($_value==$_option->value) ? "checked='checked'" : (isset($field->default_value) && $field->default_value == $_option->value) ? "checked='checked'" : "";;

								$return .= " <input $name $readonly type='radio' value=\"$_option->value\" $selected  />";
								$return .= "<label>".$_option->option."</label>";
							}
						}


					}else{

						$return .= "<label class=\"$lblb_class field select \">";
						if(!empty($is_read_only_dropdown)){
							$return .= $hidden_dropdown_value;
						}

						$return .= "<select $name $is_read_only_dropdown class=\"$field_class form-control\">";
						$return.='<option value="'.$field->default_value.'">-- Select '.$field_desc.' --</option>';
						if($options) {
							foreach ($options as $_option) {
								$selected = ($_value == $_option->value) ? "selected=\"selected\"" : '';
								$return .= " <option value=\"$_option->value\" $selected >$_option->option </option>";
							}
						}
						//$return .= "</select></label></div></div>";
						$return .= "</select><i class=\"arrow\"></i></label>";
					}



				break;
			case'query':
				break;
			case 'retchtext':
			case 'textarea':
				$field_title = ($show_name)?$field_name:$field->description;
				$field_desc  = $field->description;
				//$return = "<div $div_id class=\"$div_class field_$field->element_type\"><div class=\"section mb20\">";
				$return .= "<label data-toggle=\"tooltip\" class=\"$lbla_class field-label cus-lbl\" title=\"$field_desc\">".$field_title.$validate_sign."</label>";
				$return .= "<label class=\"$lblb_class field\">";
				$return .= "<textarea $name class=\"gui-textarea\" >$_value</textarea></label>";
				//$return .= "</label></div><div style=\"clear:both\"></div></div>";
				break;
			case 'hidden':
				$return .= "<input  type=\"$_type\" $name class='field_input gui-input form-control' value=\"$_value\">";
				break;
            case 'text':
			default:
				$auto_complete = "";
				if(in_array($field->name,array('email','password','username'))){
					$auto_complete = " autocomplete='new-$field->name'";
				}

				$field_title = ($show_name)?$field_name:$field->description;
				$field_desc  = $field->description;
				//$return  = "<div $div_id class=\"$div_class field_$field->element_type\"><div class=\"section mb20\">";
				$return .= "<label data-toggle=\"tooltip\" class=\"$lbla_class\" title=\"$field_desc\">".$field_title.$validate_sign."</label>";
				$return .= "<label class=\"$lblb_class field\">";
				$return .= "<input  type=\"$_type\" $name class='field_input gui-input form-control' value=\"$_value\" $auto_complete></label>";
				//$return .= "</label></div><div style=\"clear:both\"></div></div>";
				break;
		}

		return  $return;
	}

	/**
	 * This render entity attributes in view files
	 * @param $listfield
	 * @param null $data
	 * @param int $entity_type_id
	 * @param bool $is_update
	 * @param array $el_attr
	 * @return string
	 */
	public function randerEntityFields($listfield,$data=NULL,$entity_type_id=0,$is_update = false,$el_attr=array()) {
		//echo "<pre>"; print_r( $listfield); exit;
		//echo 'is_read_only'.$listfield->is_read_only;
		$div_class = (isset($el_attr['div_class']))?$el_attr['div_class']:"col-md-6";
		$lbla_class = (isset($el_attr['lbla_class']))?$el_attr['lbla_class']:"field-label cus-lbl";
		$lblb_class = (isset($el_attr['lblb_class']))?$el_attr['lblb_class']:"";
		$show_name = (isset($el_attr['show_name']))?true:false;
		$show_label = (isset($el_attr['hide_label'])) ? false : true;
        $uri_method = (isset($el_attr['uri_method'])) ? $el_attr['uri_method'] : 'add';
        $search = (isset($el_attr['search_columns'])) ? $el_attr['search_columns'] : false;
		$this->_dropdownDisabled = false;

		$where_condition = $this->_setWhereCondition($listfield,$data);
		if($is_update){
            $listfield = $this->_updateEntityAttributes($listfield,$data);
        }

		$select2_class = "select2-field";
		$_value = $return = $field_class = '';
		//$field_class =  $field->default_value;
		//$_type = $field->element_type;

		//Do not display * sign if search columns are render
		if(isset($el_attr['search_columns'])){
			$validate_sign = '';
		}
		else{
			$validate_sign = (isset($listfield->entity_attr_is_required) && $listfield->entity_attr_is_required == 1) ? '&nbsp;*' : '';
		}

        $is_attachment = false; $attachment= array(); $attachment_class = '';
		if($listfield->backend_table != "" && $listfield->backend_table == 'pl_attachment'){
		    $is_attachment = true; $attachment_class = 'attachment-thumb';
        }

		   $default_value = (!$search && isset($listfield->entity_attr_default_value)) ? $listfield->entity_attr_default_value : "";

		if(\Session::has(ADMIN_SESS_KEY.'_POST_DATA')){

			//if data is from user management then donot set session post data
			//b/c it make blank to auth fields

			if(isset($data->auth) && isset($data->entity_auth_id)) {
				if ($data->entity_auth_id == 0) {
					$data = (object)\Session::get(ADMIN_SESS_KEY . '_POST_DATA');
				}
			}
			else{
				$data = (object)\Session::get(ADMIN_SESS_KEY . '_POST_DATA');
			}


			$data->attributes = (object)\Session::get(ADMIN_SESS_KEY . '_POST_DATA');
			//\Session::forget(ADMIN_SESS_KEY.'_POST_DATA');
		}
		if(isset($data->attributes)){
			/*if attribute code is dropdown option then so fetch value from object*/
			if(isset($data->attributes->{$listfield->attribute_code}) && is_object($data->attributes->{$listfield->attribute_code}))
			{
				/*check if attribute has category id which is separate from entity framework*/
				if(isset($data->attributes->{$listfield->attribute_code}->category_id))
				{
					$_value = $data->attributes->{$listfield->attribute_code}->category_id;
				}

				/*Then check if attribute is dropdown option and has value*/
				else if(isset($data->attributes->{$listfield->attribute_code}->id))
				{
					$_value = $data->attributes->{$listfield->attribute_code}->id;
				}
                else if($listfield->backend_table_value != ''){

                   // echo "<pre>"; print_r($data->attributes->{$listfield->attribute_code}); exit;
                    if(isset($data->attributes->{$listfield->attribute_code}->{$listfield->backend_table_value})){
                        $_value =  $data->attributes->{$listfield->attribute_code}->{$listfield->backend_table_value};
                    }
                }
				else{
					if(isset($data->attributes->{$listfield->attribute_code}->attachment_id)){
						$_value = $data->attributes->{$listfield->attribute_code};
						$attachment[] = $data->attributes->{$listfield->attribute_code};
						//print_r($_value); exit;
					}else{
						$_value = $data->attributes->{$listfield->attribute_code}->value;
					}

				}
			}else{
				/*if attribute code is multislected then fetch value from array*/
				if(isset($data->attributes->{$listfield->attribute_code}) && is_array($data->attributes->{$listfield->attribute_code})){

					$field_values = array();

					foreach($data->attributes->{$listfield->attribute_code} as $field_arr_value){

						if(isset($field_arr_value->category_id))
							$field_values[] = $field_arr_value->category_id;
						else if(isset($field_arr_value->id))
							$field_values[] = $field_arr_value->id;
						else if(isset($field_arr_value->option))
							$field_values[] = $field_arr_value->value;
                        else if($listfield->backend_table_value != '')
                            if(isset($field_arr_value->{$listfield->backend_table_value})){
                                $field_values[] =  $field_arr_value->{$listfield->backend_table_value};
                                $attachment[] = $field_arr_value;

                            }
						else
							$field_values = array();
					}
					/*set value to comma separted to set hidden value of multiselected*/
					(count($field_values) > 0) ? $_value = implode(',',$field_values) : $_value = "";

				}
				else{
					/*if attribue is simple text field then get value by only attribute code*/
					$_value = (isset($data->attributes->{$listfield->attribute_code}))?$data->attributes->{$listfield->attribute_code}:'';
				}

			}

		}else{
			$_value = (isset($data->{$listfield->attribute_code}))?$data->{$listfield->attribute_code}:'';
		}



		/*added if type is date and time then add classes*/
		$date_class = "";
		if($listfield->data_type_identifier == "date"){
			$date_class = "field_date";
		}
		 if($listfield->data_type_identifier == "time"){
		    $date_class = "field_time";
		    if($_value != "") $_value = date('H:i',strtotime($_value));
		}

		if($is_attachment && count($attachment) == 0){
            $show_label = false;
        }


		//$is_required = ($listfield->is_required == '1')?'required':'optional';
		$is_multiple = ($listfield->data_type_identifier == 'multiple_select')?'multiple="multiple"':'';
		$name = 'name="'.$listfield->attribute_code.((
				$listfield->data_type_identifier == 'multiple_select')?'[]':'').'"';

       /* check if enttity attribute has front label then display it as field title otherwise attribute field*/
		if($show_name){
			$field_title = ($listfield->attribute_code.(($listfield->data_type_identifier == 'multiple_select')?'[]':''));
		}
		else if(isset($listfield->entity_attr_frontend_label) && !empty($listfield->entity_attr_frontend_label)){
				$field_title = $listfield->entity_attr_frontend_label;
			}
		else{
			$field_title = $listfield->frontend_label;
		}


		$field_desc  = $listfield->frontend_label;

		$queryDivClass = $div_class;
		if($listfield->data_type_identifier=='retchtext') $queryDivClass = 'col-md-12';

		$field_id = "id=\"$listfield->attribute_code\"";
		$div_id = "id=\"div_$listfield->attribute_code\"";


		if($listfield->data_type_identifier != "hidden") {
			//$return .= "<div $div_id class=\"$queryDivClass field_query\"><div class=\"section mb20\">";
			if($show_label)
			$return .= "<label data-toggle=\"tooltip\" class=\"$lbla_class ".$attachment_class." field-label cus-lbl\" title=\"$field_desc\">".$field_title.$validate_sign."</label>";
		}
		//$field_class =  $field->default_value." field_".$listfield->data_type_identifier;
		$field_class =  " field_".$listfield->data_type_identifier;

		if(isset($listfield->frontend_class) && !empty($listfield->frontend_class)){
			$field_class .= " ".$listfield->frontend_class;
		}

		//Check readonly fields
		$is_read_only = '';
		if(($is_update && $listfield->is_read_only == 1) || $uri_method == 'view'){

			$disabled_fields = array('date','dropdown','entity_select','dropdown2','time');

			if(in_array($listfield->data_type_identifier,$disabled_fields) OR ($listfield->data_type_identifier == "textfield" AND $listfield->php_data_type == "comma_separated") ){
				$is_read_only = 'disabled="disabled"';
				$hidden_dropdown_value =  '<input type="hidden" '.$name.' value="'.$_value.'" />';
				$return .= $hidden_dropdown_value;
				$name = "";
			}
			else{
				$is_read_only = 'readonly="readonly"';
			}
		}


		if(
			$listfield->data_type_identifier == 'yes_no'
			|| $listfield->data_type_identifier == 'multiple_select'
			|| $listfield->data_type_identifier == 'entity_select'){
			$return .= "<label class=\"getchoosen $lblb_class field\">";


			if($listfield->attribute_entity_type_id!="0") {

				$return .= "<select $field_id class=\"field_dropdown form-control select2-field\"  $name data-type_id=\"$listfield->attribute_entity_type_id\" data-attribute_code=\"$listfield->linked_attribute_id\" $is_read_only>";
				$return .='<option value="">-- Select '.$field_title.' --</option>';

				/*Get entity listing for entity type dropdown*/
				$ex2Model = $this->modalPath . "SYSEntity";
				$ex2Model = new $ex2Model;
				$options = $ex2Model->getEntitiesListing($listfield->attribute_entity_type_id,$listfield->linked_attribute_id,$where_condition);

				if($options){
					foreach($options as $option){

						if($is_update)
						$selected = ($option->entity_id == $_value) ? 'selected="selected"' : "";
						else
							$selected = ($option->entity_id == $default_value) ? 'selected="selected"' : "";

						$return .='<option value="'.$option->entity_id.'" '.$selected.'>'.$option->value.'</option>';
					}
				}
				$return .= "</select>";
				$return.='<i class="arrow"></i>';

			}else{
                (!empty($is_multiple)) ? $disabled = 'disabled' : '';
				$return .='<select '.$field_id.'  class="'.$field_class.' form-control fields '.$select2_class.'" '.$is_multiple.' '.$name.' '.$is_read_only.'>';
				$_values_array = explode(',',$_value);
				if(!empty($listfield->attribute_options)){
					$options = explode(',',$listfield->attribute_options);
					$return.='<option value="'.$listfield->default_value.'" '.$disabled.'>-- Select '.$field_title.' --</option>';
					foreach ($options as $option ){
						$opt = explode(':',$option);
						$selected="";

						foreach($_values_array as $_op_value){
							if ($_op_value==$opt[0])$selected ="selected=\"selected\"";
						}
						$return.='<option '.$selected.' value="'.$opt[0].'">'.$opt[1].'</option>';
					}

				}
				$return.='</select>';
				$return.='<i class="arrow"></i>';
			}
			$return.='</label>';

		}else {
			switch($listfield->data_type_identifier){
				case 'dropdown':
				case 'dropdown2':

					$options=false;
					$SYSAttribute = new SYSAttribute();
					$recored = $SYSAttribute
						->where("attribute_code", "=",$listfield->attribute_code)
						->whereNull("deleted_at")
						->first();

					if($recored) {

						if(isset($recored->backend_table) && $recored->backend_table != ""){
							if($recored->linked_entity_type_id > 0)
								$has_entity_type = ' Where entity_type_id = '.$recored->linked_entity_type_id; else $has_entity_type = '';

                           /* if depend table is category then fetch category by level wise and show indenting in child options*/
							if($recored->backend_table == "sys_category"){

								$result = \DB::select("SELECT category_id From $recored->backend_table where `deleted_at` IS NULL AND `level` = 1 AND `status` = 1");
								$categories = (count($result)) ? $result : false;

								if(count($categories) > 0){
                                    (!empty($is_multiple)) ? $disabled = 'disabled' : '';
									$selected = '';
									$return .= "<label class=\"$lblb_class field select \">
													<select $name $field_id class=\"$field_class form-control $select2_class\" $is_read_only>";
									$return.='<option  value="" '.$is_multiple.'>-- Select '.$field_title.' --</option>';

									//Render category options by indenting
									$return .= self::getCategoryFieldByIndent($categories,$_value,$default_value,$is_update);

									$return .= "</select></label>";
								}

							}
							else{
                                $backend_where = 'WHERE 1=1 ';

                                if($recored->backend_table_where != ""){

                                    //for testing on Pakistan States and cities
                                    if($listfield->attribute_code == 'state_id' && config('constants.CITY_TEST') > 0){
                                        $backend_where .= ' AND country_id = '.config('constants.ALLOWED_COUNTRY_ID');
                                    }
                                    else{
                                        $backend_where .= $recored->backend_table_where;
                                    }

                                }



                                $fetch_backend_table = true;

                                if(!$is_update && $listfield->attribute_code == 'city_id'){
							        $fetch_backend_table = false;
                                }

                                    if($is_update && $listfield->attribute_code == 'city_id'){
                                        $backend_where .= " AND city_id = ".$_value;
                                    }

                                    if($fetch_backend_table) {

                                   // echo "SELECT $recored->backend_table_option as `option`, $recored->backend_table_value as `value` From $recored->backend_table $backend_where ;";
                                        $result = \DB::select("SELECT $recored->backend_table_option as `option`, $recored->backend_table_value as `value` From $recored->backend_table $backend_where ;");
                                    }

							        $options = (isset($result) && count($result)) ? $result : false;

								$selected = '';
								$return .= "<label class=\"$lblb_class field select \">
													<select $name $field_id class=\"$field_class form-control $select2_class\" $is_read_only>";
								$return.='<option  value="" >-- Select '.$field_title.' --</option>';
								if ($options) {

									//echo $_value;
									foreach ($options as $_option) {

										if($is_update)
											$selected = ($_option->value== $_value) ? 'selected="selected"' : "";
										else
											$selected = ($_option->value == $default_value) ? 'selected="selected"' : "";

										$return .= " <option value=\"$_option->value\" $selected >$_option->option</option>";
									}

								}
								$return .= "</select><i class=\"arrow\"></i></label>";
							}


						}else{

							$SYSAttributeOption = new SYSAttributeOption();
							$options = $SYSAttributeOption
								->where("attribute_id", "=", $recored->attribute_id)
								->whereNull("deleted_at")
								->get();

							$selected = '';
							$return .= "<label class=\"$lblb_class field select \">
													<select $name $field_id class=\"$field_class form-control\" $is_read_only>";
							$return.='<option  value="">-- Select '.$field_title.' --</option>';

							if ($options) {

								//echo $_value;
								foreach ($options as $_option) {

									if($is_update){
										$selected = ($_option->value== $_value) ? 'selected="selected"' : "";
									}else{
										$selected = ($_option->value == $default_value) ? 'selected="selected"' : "";
									}

									$return .= " <option value=\"$_option->value\" $selected >$_option->option </option>";
								}

							}

							$return .= "</select><i class=\"arrow\"></i></label>";
						}

					}
					break;
				case 'entity_multiple_select2':

					$options=false;

					$SYSAttribute = new SYSAttribute();
					$recored = $SYSAttribute
						->where("attribute_code", "=",$listfield->attribute_code)
						->whereNull("deleted_at")
						->first();

					if($recored) {

						if(isset($recored->backend_table) && $recored->backend_table != ""){

							$result = \DB::select("SELECT $recored->backend_table_option as `option`, $recored->backend_table_value as `value` From $recored->backend_table ;");
							$options = (count($result)) ? $result : false;

						}else{
							$SYSAttributeOption = new SYSAttributeOption();
							$options = $SYSAttributeOption
								->where("attribute_id", "=", $recored->attribute_id)
								->whereNull("deleted_at")
								->get();
						}

						$selected = '';
						$return .= "<label class=\"$lblb_class field select \">
													<select $field_id $name class=\"$field_class form-control\" $is_read_only>";
						$return.='<option  value="'.$listfield->default_value.'">-- Select '.$field_title.' --</option>';

						if ($options) {
							foreach ($options as $_option) {

								if($is_update)
									$selected = ($_option->value== $_value) ? 'selected="selected"' : "";
								else
									$selected = ($_option->value == $default_value) ? 'selected="selected"' : "";

								$return .= " <option value=\"$_option->value\" $selected >$_option->option </option>";
							}
						}
						$return .= "</select><i class=\"arrow\"></i></label>";
					}
					break;
				case 'text':
				case 'retchtext':
				$return .= "<label class=\"$lblb_class field\">";
				$return.='<textarea '.$field_id.' name="'.$listfield->attribute_code.'" class="gui-textarea '.$field_class.'" placeholder="'.$listfield->attribute_options.'" '.$is_read_only.'>'.$_value.'</textarea></label>';
				break;
				case 'textarea':
					$return .= "<label class=\"$lblb_class field\">";
					$return.='<textarea '.$field_id.' name="'.$listfield->attribute_code.'" class="gui-textarea" placeholder="'.$listfield->attribute_options.'" '.$is_read_only.'>'.$_value.'</textarea></label>';
					break;
				case 'media_image':
					$return .= '<div><a href="javascript:;" class="img-container" id="upload_image"><img name="'.$listfield->attribute_code.'" id="preview_image" width="100" src="'.\URL::to(DIR_FILES).'/'.$_value.'" alt="Upload Image" ></a>
										<input type="hidden" name="'.$listfield->attribute_code.'" value="'.$_value.'" /><div style="clear:both"></div></div>';
					break;
				case 'hidden':

					($is_update) ? $selected_value = $_value : $selected_value = $default_value;
					$return .= '<input '.$field_id.' type="hidden" name="'.$listfield->attribute_code.'" value="'.$selected_value.'" />';
					break;
				case 'radio':

					/*Get radio options from attribute options*/
					$SYSAttributeOption = new SYSAttributeOption();
					$options = $SYSAttributeOption
						->where("attribute_id", "=", $listfield->attribute_id)
						->whereNull("deleted_at")
						->get();

					if ($options) {
						$radio_count = 0;
						foreach ($options as $option_key => $_option) {

							$field_iid = $listfield->attribute_code.'_'.$radio_count;
							$field_id = 'id="'.$field_iid.'"';

							if($is_update)
								$checked = ($_option->value== $_value) ? 'checked="checked"' : "";
							else
								$checked = ($_option->value == $default_value) ? 'checked="checked"' : "";

							$return .= '<input class="'.$field_class.'" type="radio" '.$name.' '.$field_id.' value="'.$_option->value.'" '.$checked.' />';
							$return .= '<label for="'.$listfield->attribute_code.'">'.$_option->option.'</label>&nbsp;';
							$radio_count++;
						}
					}

					break;

                case 'checkbox':

                    /*Get radio options from attribute options*/
                    $SYSAttributeOption = new SYSAttributeOption();
                    $options = $SYSAttributeOption
                        ->where("attribute_id", "=", $listfield->attribute_id)
                        ->whereNull("deleted_at")
                        ->get();

                    if ($options) {
                        $radio_count = 0;
                        foreach ($options as $option_key => $_option) {

                            $field_iid = $listfield->attribute_code.'_'.$radio_count;
                            $field_id = 'id="'.$field_iid.'"';

                            if($is_update)
                                $checked = ($_option->value== $_value) ? 'checked="checked"' : "";
                            else
                                $checked = ($_option->value == $default_value) ? 'checked="checked"' : "";

                            $return .= '<input class="'.$field_class.'" type="checkbox" '.$name.' '.$field_id.' value="'.$_option->value.'" '.$checked.' />';

                            if(count($options) > 1){
                                $return .= '<label for="'.$listfield->attribute_code.'">'.$_option->option.'</label>&nbsp;';

                            }

                            $radio_count++;
                        }
                    }

                    break;
				default:
					$return .= "<label class=\"$lblb_class field\">";
					$field_type =	($listfield->attribute_code == "password") ? "password" : "text";

					/*if attribute is multi selected then add select2 dropdown and fetch entity type options*/

					if(isset($listfield->php_data_type) && $listfield->php_data_type == "comma_separated"){

						if($listfield->linked_entity_type_id!="0") {

						/*	set select2 html attributes*/
							$select_name = $listfield->attribute_code."_select2";
							$select2_name = "name=$select_name";
							$select2_id = "id=$select_name";
							$select2_classs = "class='$field_class select2-multiple form-control $select2_class'";

							$return .= "<select $select2_id  $select2_classs  data-type_id=\"$listfield->linked_entity_type_id\" data-attribute_code=\"$listfield->linked_attribute_id\" $is_read_only multiple='multiple'>";
							$return .='<option value="'.$listfield->default_value.'" disabled>-- Select '.$field_title.' --</option>';

							/*Get entity listing for entity type dropdown*/
							$ex2Model = $this->modalPath . "SYSEntity";
							$ex2Model = new $ex2Model;
							$options = $ex2Model->getEntitiesListing($listfield->linked_entity_type_id,$listfield->linked_attribute_id);

							if($options){

								foreach($options as $option){
									$selected = "";
									if(isset($field_values)){
										if((in_array($option->entity_id,$field_values))){
											$selected = 'selected="selected"';
										}
									}
									else{
										$selected = ($option->entity_id == $default_value) ? 'selected="selected"' : "";
									}

									$return .='<option value="'.$option->entity_id.'" '.$selected.'>'.$option->value.'</option>';
								}
							}
							$return .= "</select><i class=\"arrow\"></i>";
							$return .= '<input type="hidden" '.$field_id.' '.$name.' value="'.$_value.'" />';

						}
						/*if attribute is multi selected and data have to get from another table
						 then add select2 dropdown and fetch table values*/

						else if(isset($listfield->backend_table) && $listfield->backend_table != "") {

                            /*	set select2 html attributes*/
                            if ($is_attachment) {

                                if(count($attachment) > 0){

                                    foreach($attachment as $file)
                                    {
                                        if($file->attachment_type->attachment_type_id == 8)
                                        {
                                           $return .= $this->renderImage(\URL::to($file->thumb));
                                        }
                                    }

                                }

                               //echo "<pre>"; print_r($listfield); exit;
                            }
                            else{
                                $selected = '';
                            $select_name = $listfield->attribute_code . "_select2";
                            $select2_name = "name=$select_name";
                            $select2_id = "id=$select_name";
                            $select2_classs = "class='$field_class select2-multiple form-control $select2_class'";

                            $return .= "<label class=\"$lblb_class field select \">
														<select $select2_id $select2_classs multiple='multiple' $is_read_only>";
                            $return .= '<option  value="" disabled>-- Select ' . $field_title . ' --</option>';

                            /* if depend table is category then fetch category by level wise and show indenting in child options*/
                            if ($listfield->backend_table == "sys_category") {

                                $query_get_parent = 0;

                                //if no query is exist for category thn get all child categories
                                if ($query_get_parent == 0) {
                                    $result = \DB::select("SELECT category_id From $listfield->backend_table where deleted_at IS NULL AND `level` = 1  AND `status` = 1");
                                }

                                $categories = (count($result)) ? $result : FALSE;

                                if (count($categories) > 0) {

                                    //Render category options by indenting
                                    if ($query_get_parent == 0) {
                                        $return .= self::getCategoryFieldByIndent($categories, isset($field_values) ? $field_values : [], $default_value, $is_update, TRUE);

                                    } else {
                                        $return .= self::getCategoryParentOptions($categories, isset($field_values) ? $field_values : [], $default_value, $is_update, TRUE);

                                    }

                                    $return .= "</select><i class=\"arrow\"></i>";
                                    $return .= '<input type="hidden" ' . $field_id . ' ' . $name . ' value="' . $_value . '" />';
                                    $return .= "</label>";
                                }

                            } else {
                                $result = \DB::select("SELECT $listfield->backend_table_option as `option`, $listfield->backend_table_value as `value` From $listfield->backend_table;");
                                $options = (count($result)) ? $result : FALSE;

                                if ($options) {

                                    foreach ($options as $_option) {

                                        if (isset($field_values)) {
                                            $selected = (in_array($_option->value, $field_values)) ? "selected=\"selected\"" : '';
                                        } else {
                                            $selected = ($_option->value == $default_value) ? 'selected="selected"' : "";
                                        }

                                        $return .= " <option value=\"$_option->value\" $selected >$_option->option </option>";
                                    }
                                    $return .= "</select><i class=\"arrow\"></i>";
                                    $return .= '<input type="hidden" ' . $field_id . ' ' . $name . ' value="' . $_value . '" />';
                                    $return .= "</label>";
                                }

                            }
                        }

                    }
						else{
                            //Get dropdown options and make it multi select
							$SYSAttributeOption = new SYSAttributeOption();
							$options = $SYSAttributeOption
								->where("attribute_id", "=", $listfield->attribute_id)
								->whereNull("deleted_at")
								->get();

							/*	set select2 html attributes*/
							$selected = '';
							$select_name = $listfield->attribute_code . "_select2";
							$select2_name = "name=$select_name";
							$select2_id = "id=$select_name";
							$select2_classs = "class='$field_class select2-multiple form-control $select2_class'";

							$return .= "<label class=\"$lblb_class field select \">
														<select $select2_id $select2_classs multiple='multiple' $is_read_only>";
							$return .= '<option  value="" disabled>-- Select ' . $field_title . ' --</option>';


							if ($options) {

								foreach ($options as $_option) {

									if (isset($field_values)) {
										$selected = (in_array($_option->value, $field_values)) ? "selected=\"selected\"" : '';
									} else {
										$selected = ($_option->value == $default_value) ? 'selected="selected"' : "";
									}

									$return .= " <option value=\"$_option->value\" $selected >$_option->option </option>";
								}
								$return .= "</select><i class=\"arrow\"></i>";
								$return .= '<input type="hidden" ' . $field_id . ' ' . $name . ' value="' . $_value . '" />';
								$return .= "</label>";

							}


						}

					}
					else{

						if(($listfield->model == "sys_entity_gallery")){
							//($is_update) ? $selected_value = $_value : $selected_value = $default_value;
							$attachment_id = isset($_value->attachment_id) ? $_value->attachment_id : "";
							$return .= '
									 <div class="mb20">
											<div class="dropzone gallery" data-id="'.$listfield->attribute_code.'" id="gallery_'.$listfield->attribute_code.'">
												<div class="dz-default dz-message">
													<img data-src="holder.js/300x200/big/text:300x200" alt="holder">
												</div>
												<input type="hidden" '.$field_id.' name="'.$listfield->attribute_code.'" class="'.$date_class.' field_input gui-input form-control" placeholder="'.$listfield->attribute_options.'" value="'.$attachment_id.'">';

							//$return .='<br><label data-toggle="tooltip" class="field-label cus-lbl field-label cus-lbl" title="" data-original-title="Upload Image">Upload Image </label><div class="gallery" ></div>';

							if(isset($_value->attachment_id)){

								$size = "";
								if(isset($_value->data_packet)) {
									$data_packet = json_decode($_value->data_packet);
									$size = $data_packet->size;

								}
								$return .='<span id="'.$listfield->attribute_code.'-file-info" class="file-info">
								<input type="hidden" id="attachment_id" value="'.$_value->attachment_id.'">
								<input type="hidden" id="attachment_name" value="'.$_value->title.'" >
											<input type="hidden" id="attachment_size" value="'.$size.'">
                                        	<input type="hidden" id="attachment_thumb" value="'.url($_value->thumb).'">
                                        	 <input type="hidden" id="attachment_file" value="'.url($_value->file).'"></span> ';
							}

							$return .= '</div></div>';

						}
						else{
							$auto_complete = "";
							if(in_array($listfield->attribute_code,array('email','password','username'))){
								$auto_complete = " autocomplete='off'";
							}

							($is_update) ? $selected_value = $_value : $selected_value = $default_value;
							if(!empty($hidden_dropdown_value)) $return .= $hidden_dropdown_value;

							//check if field type is date then format it
							if($listfield->data_type_identifier == 'date'){
								if(!empty($selected_value)){
									if($selected_value == '0000-00-00 00:00:00'){
										$selected_value = '';
									}
								}
							}

							$return.='<input '.$field_id.' type="'.$field_type.'" name="'.$listfield->attribute_code.'" class=" field_input gui-input form-control '.$field_class.'" placeholder="'.$listfield->attribute_options.'" value="'.$selected_value.'" '.$is_read_only.$auto_complete.' /></label>';
							unset($selected_value);
						}


					}
					break;
			}

		}
		if($listfield->data_type_identifier != "hidden") {
			//$return .= "</div><div style=\"clear:both\"></div></div>";
		}

		unset($is_read_only);
		return $return;

	}

	/**
	 * This render hidden fields in view files
	 * @param $field
	 * @param null $data
	 * @return string
	 */
	public static function renderApiHiddenField($field,$data=NULL){
		$field_name = $field->name;
		$_value = (isset($data->{$field->name}))?$data->{$field->name}:'';
		$field_id = $field->name."_id";
		if($field->is_read_only=="0"){
			$name = "name=\"$field->name\" id=\"$field->name\"";

		}else{
			$name = "readonly=\"readonly\" id=\"$field->name\"";
		}

		$return = "<input  type='hidden' $name class='field_input gui-input form-control' value=\"$_value\">";
		return $return;
	}

	/**
	 * @param $path
	 * @param $title
	 * @return string
	 */
	public static function renderImage($path,$title = false)
	{
		$title =  ($title) ? "title='$title'" : "";
		return '<img src="'.$path.'" '.$title.' width="50" height="50" />';
	}
	/**
	 * Display Gallery image
	 * @param $gallery_arr
	 * @param bool $thumb
	 * @return string
	 */
	public static function getGalleryImageFile($gallery_arr,$identifier,$file_index = false,$source = false)
	{
		$image = self::getDefaultImagePath($identifier,$source);
		if(isset($gallery_arr)){

			if(isset($gallery_arr[0])){
				$gallery = $gallery_arr[0];
				if($file_index)
                    if(@getimagesize($gallery->{$file_index})) return $gallery->{$file_index};
				    else
				if(@getimagesize($gallery->file)) return $gallery->file;
			}
		}
		return $image;
	}

	/**
	 * @param $identifier
	 * @return mixed
	 */
	public static function getDefaultImagePath($identifier,$source = 'admin')
	{
	    if($source == 'admin'){
            Switch($identifier){
                case 'customer':
                case 'business_user':
                case 'driver':
                    return \URL::to("resources/assets/".config("panel.DIR")."assets/img/default-user-placeholder.jpg");
                    break;
                default:
                    return \URL::to("resources/assets/".config("panel.DIR")."assets/img/no-image.png");
                    break;
            }
        }else{
            return \URL::to("resources/assets/".config("panel.DIR")."assets/img/no-image.png");
        }

	}

	/**
	 * @param $gallery_arr
	 * @param bool $entity_type_identifier
	 * @param bool $thumb
	 * @return mixed
	 */
	public static function getGalleryImagePath($gallery_arr, $identifier = false,$thumb = false)
	{
		$image_path = self::getGalleryImageFile($gallery_arr,$identifier,$thumb);
		return self::renderImage($image_path);
	}

    /**
     * @param $gallery_arr
     * @param bool $identifier
     * @param bool $file_index
     * @return string
     */
    public static function getGalleryImage($gallery_arr, $identifier = false,$file_index = false)
    {
        return self::getGalleryImageFile($gallery_arr,$identifier,$file_index);
    }

	/**
	 * Render category options indent wise
	 * @param $categories
	 * @param $_value
	 * @param string $default_value
	 * @param bool $is_update
	 * @param bool $is_multiple
	 * @return string
	 */
	public static function getCategoryFieldByIndent($categories,$_value,$default_value = '',$is_update = false ,$is_multiple = false)
	{
		$category_options = "";

		if($categories){
			if(count($categories)>0){

				$category_model = new SYSCategory();

				foreach($categories as $category){

					$category_data = $category_model->getData($category->category_id);

					if($category_data){

						$category_parent = "";
						/*if category has child then get its options*/
						if(count($category_data->child) > 0){
							//check if update then auto select category ids else auto select if default value is exist
							if($is_update){
								if($is_multiple)
									$parent_selected = (in_array($category_data->category_id, $_value)) ? "selected=\"selected\"" : '';
								else
									$parent_selected = ($category_data->category_id == $_value) ? 'selected="selected"' : "";
							}
							else{
								$parent_selected = ($category_data->category_id == $default_value) ? 'selected="selected"' : "";
							}

							//display parent category as a label
							//$category_options .= " <option disabled value=\"$category_data->category_id\" $parent_selected ><strong>$category_data->title</strong></option>";
							$category_parent = " <option disabled value=\"$category_data->category_id\" $parent_selected ><strong>$category_data->title</strong></option>";

							//$child = $category_data->child[0];
							$category_child = "";
							foreach($category_data->child as $child){

								if($child->status == 2) continue;

								//Now display child categories
								$indent = ' ';
								if($is_update)
									if($is_multiple)
										$selected = (in_array($child->category_id, $_value)) ? "selected=\"selected\"" : '';
									else
										$selected = ($child->category_id == $_value) ? 'selected="selected"' : "";
								else
									$selected = ($child->category_id == $default_value) ? 'selected="selected"' : "";

								//$category_options .= " <option value=\"$child->category_id\" $selected >".$indent.$child->title."</option>";
								$category_child .= " <option value=\"$child->category_id\" $selected >".$indent.$child->title."</option>";

							}

							//check if parent category has child category then add options
							if($category_child != ""){
								$category_options .= $category_parent.$category_child;
							}

						}

					}
				}
			}
		}

		return $category_options;

	}


	/**
	 * @param $categories
	 * @param $_value
	 * @param string $default_value
	 * @param bool $is_update
	 * @param bool $is_multiple
	 * @return string
	 */
	public static function getCategoryParentOptions($categories,$_value,$default_value = '',$is_update = false ,$is_multiple = false)
	{
		$category_options = "";

		if($categories){
			if(count($categories)>0){

				$category_model = new SYSCategory();

				foreach($categories as $category){

					$category_data = $category_model->getData($category->category_id);

					if($category_data){

							//check if update then auto select category ids else auto select if default value is exist
							if($is_update){
								if($is_multiple)
									$parent_selected = (in_array($category_data->category_id, $_value)) ? "selected=\"selected\"" : '';
								else
									$parent_selected = ($category_data->category_id == $_value) ? 'selected="selected"' : "";
							}
							else{
								$parent_selected = ($category_data->category_id == $default_value) ? 'selected="selected"' : "";
							}
							//display parent category as a label
							$category_options .= " <option value=\"$category_data->category_id\" $parent_selected ><strong>$category_data->title</strong></option>";


					}
				}
			}
		}

		return $category_options;

	}

	/**
	 * @param $gallery
	 * @param bool $thumb
	 * @return string
	 */
	public static function getCategoryImagePath($gallery,$thumb = false)
	{
		$title = '';
		$image = self::getDefaultImagePath('category');
		if(isset($gallery->file)){
			if(@getimagesize($gallery->file)) $image = $gallery->file;
			$title = $gallery->title;
		}

		return self::renderImage($image,$title);

	}

    /**
     * @param $gallery
     * @param bool $gallery_index
     * @return mixed
     */
    public static function getCategoryImage($gallery,$gallery_index = false,$source)
    {
        $image = self::getDefaultImagePath('category',$source);
        if(isset($gallery->{$gallery_index})){
            if(@getimagesize($gallery->{$gallery_index})) return $gallery->{$gallery_index};
        }
        else{
            if(@getimagesize($gallery->file)) return $gallery->file;
        }

        return $image;
    }

	/**
	 * Check column visibility
	 * @param $view_at
	 * @param bool $is_update
	 * @return string
	 */
	public function showHideColumn($view_at,$is_update = false)
	{
		if($is_update){
			if($view_at == 1 || $view_at == 3)
				return "hide";
			else
				return "";
		}

		if($view_at == 2 || $view_at == 3)
			return "hide";
		else
			return "";


	}

	/**
	 * Set fields with conditions
	 * @param $field
	 * @param $data
	 * @return string
	 */
	private function _setWhereCondition($field,$data)
	{
		$where_condition = "";

		//if entity type is promotion then display products by promotion type
		if(isset($data->identifier)){

			if($data->identifier == 'promotion_item'){
				if($field->attribute_code == "promotion_product_id" && isset($data->attributes)){

					$product_type = EntityHelper::parseAttributeValue( $data->attributes->promotion_type);
					$where_condition = " product_type = ".$product_type;

					/*if(isset($data->attributes->start_date) && !empty($data->attributes->start_date)){
						$current_time = date('Y-m-d H:i:s');

						if(strttoime($current_time) >= strtotime($data->attributes->start_date)){
							$this->_dropdownDisabled = true;
						}
					}*/
				}

				return $where_condition;
			}

		}
	}

    /**
     * Update entity attribute value
     * @param $field
     * @param $data
     * @return mixed
     */
	private function _updateEntityAttributes($field,$data)
    {
        if(isset($data->identifier)){

            if($data->identifier == 'custom_notification'){

                if(isset($data->attributes)){

                    $field->linked_entity_type_id = $data->attributes->target_user_entity_type_id;
                    $field->linked_attribute_id = 234;

                }
            }
        }
        return $field;
    }
}
