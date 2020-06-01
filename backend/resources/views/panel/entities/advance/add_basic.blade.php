<?php
$fields = "App\Libraries\Fields";
$fields = new $fields();
?>
        <!-- create new order panel -->
<div class="panel panel-theme panel-border top mb25">
    <div class="panel-heading">
        <span class="panel-title">{!! $page_action !!} {!! isset($entity_data->title) ? $entity_data->title : $module !!}</span>
    </div>
    <div class="panel-body p20 pb10">
        <form  name="data_form" method="post" id="data_form" class="panel-collapse collapse in">
            <div class="tab-content pn br-n admin-form">
                @include(config('panel.DIR').'flash_message')
                @if (Session::has('message'))
                    <div class="alert alert-info">{{ Session::get('message') }}</div>
                @endif
                <div class="alert-message"></div>
                <div class="row entity_wrap mb20" id="entity_data">
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
                    if (isset($records) && count($records)>0) {

                        foreach ($records as $key =>$record) {

                            if($record->is_entity_column == 1){
                                $field_class = $record->attribute_code.'_field';

                            }else{
                                $field_class = $record->name.'_field';
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
                                    echo $fields->randerEntityFields($record, $entity_data, $entity_data->entity_type_id);
                                } else {
                                    echo $fields->randerFields($record, $entity_data, $entity_data->entity_type_id);
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


                </div>
                <div class="pull-right p-relative">
                    <button type="submit" class="btn ladda-button btn-theme btn-wide" data-style="zoom-in"> <span class="ladda-label">Submit</span> </button>
                    @include(config('panel.DIR').'entities.loader')
                </div>
                <!-- end section row section -->
            </div>
        </form>
    </div>
</div>
