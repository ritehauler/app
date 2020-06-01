<div class=" entity_wrap" id="entity_data">

    <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
    <input type="hidden" name="do_post" value="1" />
    <input type="hidden" name="entity_type_id" id="entity_type_id" value="{!! $entity_data->entity_type_id !!}" />
    <?php
    /*add hidden fields*/
    if(isset($hidden_records[0])){
        foreach($hidden_records as $hidden_record){
            if($hidden_record->is_entity_column == 1){
                echo $fields->randerEntityFields($hidden_record,$entity_data,$entity_data->entity_type_id);
            }else{
                echo $fields->randerFields($hidden_record,$entity_data,$entity_data->entity_type_id);
            }
        }
    }

    if (isset($records) && count($records) > 0) {

        $record_keys = array_keys($records);
        $i = 0;
        foreach ($records as $key =>$record) {

                $class = "";
                if($record->is_entity_column == 1){
                    $field_class = $record->attribute_code.'_field';

                }else{
                    $field_class = $record->name.'_field';
                }

                //Check if column has to show / hide
                $hide = $fields->showHideColumn($record->view_at);

                if($i == 0){ //div start for first row which has image
                ?>
                <div class="row mbn">
                    <div class="col-md-4 mb20">
                        <div class="dropzone " id="dropzoneFileUpload">
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

                        <?php if($i == 2){?><div class="row mbn"><?php } //when column is 3rd ?>
                            <?php  }
                            } ?>

                            <div class="section mb10 {!! $class.' '.$field_class.' '.$hide !!}">
                                <?php
                                if(isset($record->element_type) && $record->element_type=='text'){
                                    $record->element_type='input';
                                }
                                if($record->is_entity_column == 1){
                                    echo $fields->randerEntityFields($record,$entity_data,$entity_data->entity_type_id);
                                }else{
                                    echo $fields->randerFields($record,$entity_data,$entity_data->entity_type_id);
                                }

                                ?> </div> {{--end of section--}}

                            <?php
                            //condition for first row having image close first div.row and div.col-md-8
                            if(($i == 1) OR count($records)== 0){?> </div> </div>
                    <?php }  else{
                    //condition to close div.row which has been started after first row
                    if($i == end($record_keys)){?> </div><?php }
                }
                ?>
         <?php  $i++; } //end of foreach ?>


    <?php }
    ?>

</div> {{--end of entity_wrap--}}