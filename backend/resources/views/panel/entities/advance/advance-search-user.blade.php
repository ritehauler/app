<?php
$fields = "App\Libraries\Fields";
$fields = new $fields();
?>
        <!-- begin: .tray-right -->
<aside id="sidebar_right" class="nano tray tray-right tray290 va-t pn affix has-scrollbar" data-tray-height="match">
	<div class="panel-heading rightSidebarHeading" unselectable="on" style="user-select: none;">
		<span class="panel-icon"><i class="fa fa-search fs20 text-primary"></i></span>
		<span class="panel-title"> Filter {!! $entity_data->title !!}</span>
	{{--<span class="panel-controls">
			<a href="javascript:window.print()" class="btn btn-xs mr5 text-right"> <i class="fa fa-print fs17"></i> </a>
		</span>--}}
	</div>
    <!-- menu quick links -->
    <div class="p20 admin-form nano-content">
       <!-- <h4 class="mt5 text-muted fw500"> Filter {!! isset($entity_data->title) ? $entity_data->title : "Columns" !!}</h4>
        <hr class="short">-->

        <form name="searchEntity" id="searchEntity" method="post">

            <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
            <input type="hidden" name="do_export" value="1" />
            <input type="hidden" name="entity_type_id" id="entity_type_id" value="{!! $entity_data->entity_type_id !!}" />

            <div id="entity-search-fields">

                <div class="mb15">
                    <label data-toggle="tooltip" class="field-label cus-lbl  field-label cus-lbl" >From Date</label>
                    <label class="field">
                        <input id="from_date" type="text" name="from_date" class="field_input gui-input form-control  field_date user_delivery_date js-datepicker" placeholder="" value="">
                    </label>
                </div>
                <div class="mb15">
                    <label data-toggle="tooltip" class="field-label cus-lbl  field-label cus-lbl" >To Date</label>
                    <label class="field">
                        <input id="to_date" type="text" name="to_date" class="field_input gui-input form-control  field_date user_delivery_date js-datepicker" placeholder="" value="">
                    </label>
                </div>

                <?php
                if(isset($records)){

                  ?>
                    <div class="mb15">
                        <label title="" class="field-label cus-lbl" data-toggle="tooltip" data-original-title="Name">Name</label>
                        <label class=" field"><input type="text" value="" class="field_input gui-input form-control" id="full_name" name="full_name">
                        </label></div>
                <?php
                foreach($records as $listing_column){

               // echo "<pre>"; print_r( $listing_column);exit;


                if(isset($listing_column->entity_attr_frontend_label) && !empty($listing_column->entity_attr_frontend_label)){
                    $field_title = $listing_column->entity_attr_frontend_label;
                }
                else{
                    $field_title = $listing_column->frontend_label;
                }
                ?>

                    {{--<label>{!! $field_title !!}</label>--}}
                    <?php

                    if ($listing_column->is_entity_column == 1 && (isset($listing_column->entity_attr_show_in_list) && $listing_column->entity_attr_show_in_list == 1 )) {

                        echo '<div class="mb15">';
                        echo $fields->randerEntityFields($listing_column, $entity_data, $entity_data->entity_type_id,false,array('search_columns'=>true));
                        echo '</div>';
                    }
                    elseif($listing_column->is_entity_column == 0){

                        $check_fields = array('is_auth_exists', 'entity_auth_id', 'password');

                        if ($entity_data->identifier == "customer") { //if customer update then donot show role
                            $check_fields = array_merge($check_fields, array('role_id','parent_role_id'));
                        }

                        if (in_array($listing_column->name, $check_fields)) {
                            continue;
                        }


                        echo '<div class="mb15">';
                        echo $fields->randerFields($listing_column, $entity_data, $entity_data->entity_type_id,false,array('search_columns'=>true));
                        echo '</div>';
                    }
                    else {
                       continue;
                    }

                   // echo $fields->randerEntityFields($listing_column,$entity_data,$entity_data->entity_type_id,false,array('search_columns'=>true)); ?>

                <?php	}
                }

                ?>
            </div>

        <div class="mt25 clearfix">
            <a type="button" id="grid_reset" class="btn btn-default btn-md col-sm-6 btn-clear">Clear</a>
            <a type="button" id="grid_search" class="btn btn-theme btn-md col-sm-6 btn-search">Search</a>
        </div>
        </form>
    </div>

</aside>
<!-- end: .tray-right -->
