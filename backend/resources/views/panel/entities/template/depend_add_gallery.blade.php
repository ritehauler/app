<?php

if (isset($depend_entity_records) && count($depend_entity_records) > 0) {

    $record_keys = array_keys($records);
    $i = 0;

    foreach ($depend_entity_records as $key =>$record) {

            $class = "";
            if ($record->is_entity_column == 1) {
                $field_class = $record->attribute_code . '_field';

            } else {
                $field_class = $record->name . '_field';
            }

            //Check if column has to show / hide
            $hide = $fields->showHideColumn($record->view_at);

            if($i == 0){ //div start for first row which has image

                    if(!isset($di))
                        $di = 0;
            ?>
            <div class="row mbn">
                <div class="col-md-4 mb20">
                    <div class="dropzone dropzoneFileUpload" id="dropzoneFileUpload_{!! $di !!}">
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
                                echo $fields->randerEntityFields($record, $depend_entity_type_data, $depend_entity_type_data->entity_type_id);
                            } else {
                                echo $fields->randerFields($record, $depend_entity_type_data, $depend_entity_type_data->entity_type_id);
                            }

                            ?> </div> {{--end of section--}}

                        <?php
                        //condition for first row having image close first div.row and div.col-md-8
                        if(($i == 1) OR count($records) == 0){?> </div>
                </div>
                <?php }  else{
                //condition to close div.row which has been started after first row
                if($i == end($record_keys)){?> </div><?php }
            }
            ?>
    <?php  $i++; } //end of foreach ?>


<?php }
?>
