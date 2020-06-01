<div class="row entity_wrap mb20" id="entity_data">

    <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
    <input type="hidden" name="do_post" value="1" />
    <input type="hidden" name="entity_type_id" id="entity_type_id" value="{!! $entity_data->entity_type_id !!}" />
    <?php
    /*add hidden fields*/
    if(isset($hidden_records) && count($hidden_records)>0){
        foreach($hidden_records as $hidden_record){
            if($hidden_record->is_entity_column == 1){
                echo $fields->randerEntityFields($hidden_record,$update,$entity_data->entity_type_id,true,array('uri_method'=>$uri_method));
            }else{
                echo $fields->randerFields($hidden_record,$update,$entity_data->entity_type_id,true,array('uri_method'=>$uri_method));
            }
        }
    }


    if (isset($records) && count($records) > 0) {

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

        <?php  } //end of foreach ?>


    <?php }
    ?>


</div>
