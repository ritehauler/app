<input type="hidden" id="depend_entity_exist" name="depend_entity_exist" value="1"/>

<div class="bulk_entity_wrap">

    <?php

   //   echo "<pre>"; print_r( $depend_update); exit;


    if ((isset($depend_entity_records) && count($depend_entity_records)>0) &&
    (isset($depend_update) && count($depend_update)>0)) {

    $ii = 0;
    foreach ($depend_update as $depend_update_item) {
    ?>

    <div class="row bulk_entity_raw mb20" id="bulk_entity_raw_{!! $ii !!}">
        <div class="section-divider mb30 mt15" id="spy1">
            <span>Add Item</span>
        </div>
        <input type="hidden" name="do_post" value="1"/>
        <input type="hidden" name="_token" value="{!! csrf_token() !!}"/>
        <input type="hidden" name="entity_type_id" id="entity_type_id"
               value="{!! $depend_entity_type_data->entity_type_id !!}"/>
        <input type="hidden" name="entity_id" id="entity_id" value="{!! $depend_update_item->entity_id !!}" />
        <?php

        /*add hidden fields*/
        if (isset($depend_entity_hidden_records) && count($depend_entity_hidden_records)>0) {
            foreach ($depend_entity_hidden_records as $hidden_record) {
                if ($hidden_record->is_entity_column == 1) {
                    echo $fields->randerEntityFields($hidden_record, $depend_update_item, $depend_entity_type_data->entity_type_id,true);
                } else {
                    echo $fields->randerFields($hidden_record, $depend_update_item, $depend_entity_type_data->entity_type_id,true);
                }
            }
        }

        //  print_r($depend_entity_records);
        $record_keys = array_keys($depend_entity_records);

        $i = 0;
        foreach ($depend_entity_records as $key =>$record) {
        $class = '';
        if ($record->is_entity_column == 1) {
            $field_class = $record->attribute_code . '_field';

        } else {
            $field_class = $record->name . '_field';
        }

        //Check if column has to show / hide
        $hide = $fields->showHideColumn($record->view_at,true);

        if($i == 0){ //div start for first row which has image

        ?>
        <div class="row mbn">
            <div class="col-md-4 mb20">
                <div class="dropzone dropzoneFileUpload" id="dropzoneFileUpload_{!! $ii !!}">
                    <div class="dz-default dz-message">
                        <img data-src="holder.js/300x200/big/text:300x200" alt="holder">
                    </div>

                </div>
            </div>
            <div class="col-md-8 pl15">
                <?php }else{ // if column is 3rd then this condition will run section will be col-md-6
                if($i > 1){
                $class = " col-md-6";
                ?>

                <?php if($i == 2){?>
                <div class="row mbn"><?php } //when column is 3rd ?>
                    <?php  }
                    } ?>

                    <div class="section mb10 {!! $class.' '.$field_class.' '.$hide !!}">
                        <?php
                        if (isset($record->element_type) && $record->element_type == 'text') {
                            $record->element_type = 'input';
                        }
                        if ($record->is_entity_column == 1) {
                            echo $fields->randerEntityFields($record, $depend_update_item, $depend_entity_type_data->entity_type_id,true);
                        } else {
                            echo $fields->randerFields($record, $depend_update_item, $depend_entity_type_data->entity_type_id,true);
                        }

                        ?> </div> {{--end of section--}}

                    <?php
                    //condition for first row having image close first div.row and div.col-md-8
                    if(($i == 1) OR count($depend_entity_records) == 0){?> </div>
            </div>
            <?php }  else{
            //condition to close div.row which has been started after first row
            if($i == end($record_keys)){?> </div><?php }
        }
        ?>


    <?php  $i++; }
    //end of foreach ?>
    </div>
    <?php   $ii++;   } ?>



<?php   } ?>



</div>