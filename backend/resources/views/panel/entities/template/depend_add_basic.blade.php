<?php

if (isset($depend_entity_records) && count($depend_entity_records) > 0) {

    foreach ($depend_entity_records as $key =>$record) {

            if ($record->is_entity_column == 1) {
                $field_class = $record->attribute_code . '_field';

            } else {
                $field_class = $record->name . '_field';
            }

            //Check if column has to show / hide
            $hide = $fields->showHideColumn($record->view_at);
            //when column is odd
            /*if($key % 2 == 0){ */?><!--
                                                     <div class="row mbn">--><?php /*} */ ?>


            <div class="section mb10 col-md-6 {!! $field_class.' '.$hide !!}">
                <?php
                if (isset($record->element_type) && $record->element_type == 'text') {
                    $record->element_type = 'input';
                }
                if ($record->is_entity_column == 1) {
                    echo $fields->randerEntityFields($record, $depend_entity_type_data, $depend_entity_type_data->entity_type_id);
                } else {
                    echo $fields->randerFields($record, $depend_entity_type_data, $depend_entity_type_data->entity_type_id);
                }


                ?> </div>

            <?php
            //when column is even
            /* if($key%2 > 0){*/?><!-- </div>--><?php /*} */
            ?>

    <?php  } //end of foreach ?>


<?php }
?>
