<div class="row entity_wrap" id="entity_data">

    <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
    <input type="hidden" name="do_post" value="1" />
    <input type="hidden" name="entity_type_id" id="entity_type_id" value="{!! $entity_data->entity_type_id !!}" />
    <?php
    /*add hidden fields*/
    if(isset($hidden_records[0])){
        foreach($hidden_records as $hidden_record){
            if($hidden_record->is_entity_column == 1){
                echo $fields->randerEntityFields($hidden_record,$update,$entity_data->entity_type_id,true);
            }else{
                echo $fields->randerFields($hidden_record,$update,$entity_data->entity_type_id,true);
            }
        }
    }

    if (isset($records[0])) { ?>


<?php
    foreach ($records as $key =>$record) {

    if($record->is_entity_column == 1){
        $field_class = $record->attribute_code.'_field';

    }else{
        $field_class = $record->name.'_field';
    }

    //Check if column has to show / hide
    $hide = $fields->showHideColumn($record->view_at,true);
    //when column is odd
    /*if($key % 2 == 0){ */?><!--
                                         <div class="row mbn">--><?php /*} */ ?>


    <div class="section mb10 col-md-6 {!! $field_class.' '.$hide !!}">
        <?php
        if (isset($record->element_type) && $record->element_type == 'text') {
            $record->element_type = 'input';
        }
        if ($record->is_entity_column == 1) {
            echo $fields->randerEntityFields($record, $update, $entity_data->entity_type_id,true,array('uri_method'=>$uri_method));
        } else {
            echo $fields->randerFields($record, $update, $entity_data->entity_type_id,true,array('uri_method'=>$uri_method));
        }


        ?> </div>

    <?php
    //when column is even
    /* if($key%2 > 0){*/?><!-- </div>--><?php /*} */
    ?>
    <?php
    ?>
    <?php  } //end of foreach ?>


    <?php }
    ?>


    <?php if(isset($update->attributes->truck_id->id)){
    $truck_detail = $update->attributes->truck_id->detail->attributes;


    //echo "<pre>"; print_r($truck_detail); exit;
    ?>
    <div id="truckWrap" class="col-md-12">
{{--        <p><label class="field-label">Truck License: <span class="item_value" id="truck_code">{!! $truck_detail->vehicle_code; !!}</span></label></p>--}}
        <p><label class="field-label">Truck Class: <span class="item_value" id="truck_class">{!!$truck_detail->truck_class_id->detail->attributes->truck_class->option !!}</span></label></p>
        <p><label class="field-label">Weight Range: <span class="item_value" id="truck_weight">{!! $truck_detail->truck_class_id->detail->attributes->min_weight.' '.config("constants.WEIGHT_UNIT").' - '.$truck_detail->truck_class_id->detail->attributes->max_weight.' '.config("constants.WEIGHT_UNIT") !!}</span></label></p>
        <p><label class="field-label">Truck Volume: <span class="item_value" id="truck_volume">{!! $truck_detail->volume.' '.config("constants.VOLUME_UNIT") !!}</span></label></p>

        @if($uri_method != 'view')
        <div id="assignVehicleWrap">

            <div class="section mb10 col-md-4">
                <label title="" class="field-label cus-lbl  field-label cus-lbl" data-toggle="tooltip">Available Vehicle *</label>
                <label class=" field select ">
                    <select class=" field_dropdown2 form-control" id="truck_vehicle" name="truck_vehicle">
                        <option value="">-- Select Vehicle --</option>

                    </select><i class="arrow"></i></label>
            </div>

          {{--  <div class="col-md-12"> <button type="button" class="btn ladda-button btn-theme btn-wide assign-vehicle" data-style="zoom-in"> <span class="ladda-label">Assign Vehicle</span> </button></div>
--}}
        </div>
            @endif

    </div>

<?php  } ?>
</div>

