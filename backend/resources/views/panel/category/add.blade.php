<?php
$fields = "App\Libraries\Fields";
$fields = new $fields();
?>
<!-- Begin: Content -->

        <div class="panel panel-theme panel-border top mb25">
			<div class="panel-heading">
				<span class="panel-title">{!! $page_action !!} {!! $module !!}</span>
			</div>

            <div class="panel-body p20 pb10">
                <form  name="data_form" method="post">
                    <div class="main admin-form ">
                        @include(config('panel.DIR').'flash_message')

                        <div class="alert-message"></div>

                            <div class=" entity_wrap" id="entity_data">

                                <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
                                <input type="hidden" name="do_post" value="1" />
                               <?php
                                /*add hidden fields*/
                                /*add hidden fields*/
                                if(isset($hidden_records[0])){
                                    foreach($hidden_records as $hidden_record){
                                        echo $fields->randerFields($hidden_record);

                                    }
                                }

                                if (isset($records[0])) { ?>

                                <?php
                                foreach ($records as $key =>$record) {

                                $class = "";
                                if($record->is_entity_column == 1){
                                    $field_class = $record->attribute_code.'_field';

                                }else{
                                    $field_class = $record->name.'_field';
                                }

                                if($key == 0){ //div start for first row which has image
                                ?>
                                <div class="row mbn">
                                    <div class="col-md-4 mb20 gallery-wrap">
                                        <div class="dropzone" id="dropzoneFileUpload">
                                            <div class="dz-default dz-message">
                                                <img data-src="holder.js/300x200/big/text:300x200" alt="holder">
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-md-8 pl15 top-wrap">
                                        <?php }else{ // if column is 3rd then this condition will run section will be col-md-6
                                        if($key > 1){
                                        $class = " col-md-6";
                                        ?>

                                        <?php if($key == 2){?><div class="row mbn"><?php } //when column is 3rd ?>
                                            <?php  }
                                            } ?>

                                            <div class="section mb10 {!! $class.' '.$field_class !!}">
                                                <?php
                                                if(isset($record->element_type) && $record->element_type=='text'){
                                                    $record->element_type='input';
                                                }
                                                echo $fields->randerFields($record);

                                                ?> </div> {{--end of section--}}

                                            <?php
                                            //condition for first row having image close first div.row and div.col-md-8
                                            if(($key == 1) OR count($records)== 0){?> </div> </div>
                                    <?php }  else{
                                    //condition to close div.row which has been started after first row
                                    if($key == (count($records)-1)){?> </div><?php }
                                }
                                ?>
                                <?php  } //end of foreach ?>


                                <?php }
                                ?>

                            </div> {{--end of entity_wrap--}}



                            <div class="pull-right">
                            <button type="submit" class="btn ladda-button btn-theme btn-wide" data-style="zoom-in"> <span class="ladda-label">Submit</span> </button>
                        </div>
                    </div>

                </form>
                </div>
		</div>


    <!-- End: Content -->
    <!-- Begin: Page Footer -->

<!-- End: Page Footer -->

