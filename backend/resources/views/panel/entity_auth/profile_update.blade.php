@include(config('panel.DIR').'header')
<?php
$fields = "App\Libraries\Fields";
$fields = new $fields();

$heading =  isset($entity_data->title) ? $entity_data->title : $module;
?>
        <!-- Begin: Content -->


<section id="content_wrapper" class="content">
    <section id="content" class="pn">

        <!-- begin: .tray-center -->
        <div class="tray tray-center p25 va-t posr">

            <div class="panel panel-theme panel-border top mb25">
                <div class="panel-heading">
                    <span class="panel-title">{!! $page_action !!}</span>
                </div>
                <div class="panel-body p20 pb10">
                    <form  name="data_form" method="post" id="data_form" class="panel-collapse collapse in">
                        <div class="tab-content pn br-n admin-form">
                            @include(config('panel.DIR').'flash_message')
                            @if (Session::has('message'))
                                <div class="alert alert-info">{{ Session::get('message') }}</div>
                            @endif
                            <div class="alert-message"></div>
                            <div class="entity_wrap" id="entity_data">

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


                                if (count($records)>0) {
                                $i = 0;
                                $record_keys = array_keys($records);

                                foreach ($records as $key =>$record) {

                                $class = "";
                                if($record->is_entity_column == 1){
                                    $field_class = $record->attribute_code.'_field';

                                }else{
                                    $field_class = $record->name.'_field';
                                }

                                //Check if column has to show / hide
                                $hide = $fields->showHideColumn($record->view_at,true);

                                if($i == 0){ //div start for first row which has image
                                ?>
                                <div class="row mbn">
                                    <div class="col-md-4 mb20">
                                        <div class="dropzone" id="dropzoneFileUpload">
                                            <div class="dz-default dz-message" style="padding: 5px;">
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
                                                    echo $fields->randerEntityFields($record,$update,$entity_data->entity_type_id,true);
                                                }else{
                                                    echo $fields->randerFields($record,$update,$entity_data->entity_type_id,true);
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
                                <?php $i++; } //end of foreach ?>


                                <?php }
                                ?>

                            </div> {{--end of entity_wrap--}}

                            <div class="pull-right">
                                <button type="submit" class="btn ladda-button btn-theme btn-wide mt10" data-style="zoom-in"> <span class="ladda-label">Submit</span> </button>
                            </div>
                            <!-- end section row section -->
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </section>

    <!-- End: Content -->
    <!-- Begin: Page Footer -->
    @include(config('panel.DIR') . 'footer_bottom')
</section>

<script type="text/javascript" src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/holder/holder.min.js' ) !!}"></script>


<!-- End: Page Footer -->
<script type="text/javascript">
    $(function () {
        $('#entity_type_id').val("<?php echo $entity_data->entity_type_id;?>");
        // default form submit/validate
        $('form[name="data_form"]').submit(function (e) {
            e.preventDefault();
            // hide all errors
            $("div[id^=error_msg_]").removeClass("show").addClass("hide");

            setFullName();
            // validate form
            Common.jsonValidation('<?=Request::url()?>', this,'',"<?php echo $entity_data->identifier ?>");
        });


        var entity_type_id = "{!! $entity_data->entity_type_id !!}";

        if( $('#role_id').length > 0 && entity_type_id != ""){

                    <?php if($entity_data->identifier != "customer"){ ?>

            var selected_role = $('#role_id').val();
            var parent_role_id = $('#parent_role_id').val();
            console.log(selected_role);
            $('#role_id').empty();
            $('#role_id').append('<option value="">Select Role</option>');
            $.ajax({
                url: "<?php echo url('getRoleOptions'); ?>",
                dataType: "json",
                data: {"entity_type_id": entity_type_id,"parent_id": parent_role_id},
                beforeSend: function () {
                    // $('#' + chosen_id).empty();
                }
            }).done(function (data) {
                $(data).each(function (index, ele) {
                    $('#role_id').append('<option value="' + ele.role_id + '">' + ele.title + '</option>');
                });

                // $('#role_id').val(selected_role).prop('selected', true);
                $('select#role_id>option[value="' + selected_role + '"]').prop('selected', true);
            });

            <?php } ?>

        }

                @if(isset($entity_data->show_gallery) && $entity_data->show_gallery == 1)

        var baseUrl = "";
        var token = "{{ Session::getToken() }}";
        Dropzone.autoDiscover = false;
        var myDropzone = new Dropzone("div#dropzoneFileUpload", {

            url: baseUrl + "{!! URL::to('/api/system/attachment/save') !!}",
            addRemoveLinks: true,
            maxFiles:1,
            thumbnailWidth: parseInt(minThumbWidth),
            thumbnailHeight: parseInt(minThumbHeight),
            dictRemoveFileConfirmation:  "Are you sure you want to remove?",
            params: {
                _token: token,
                attachment_type_id: 8,
                entity_type_id:0
            }
        });
        var numItems = 0;
        var attch_id = '';
        myDropzone.on("success", function(file,responseText) {


            //$.parseJSON(responseText.jsonEditor);
            attch_id = $.parseJSON(responseText.jsonEditor).data.attachment.attachment_id;
            //console.log($.parseJSON(responseText.jsonEditor).data.attachment.attachment_id);
            //$('.dz-complete').eq(numItems).append('<input type="checkbox" onClick="isfeatured()">');
            //numItems++;
            myDropzone.processQueue();
        });

        myDropzone.on("complete", function() {

            console.log(attch_id);
            $('.dz-complete').append('<div class="pull-right"><input type="hidden" name="gallery_items[]" value="'+attch_id+'"></div>');
            $('.dz-remove').attr('data-attachment-id',attch_id);
            /* setTimeout(function(){
             $('.dz-complete').eq(numItems).append('<input type="radio" name="gallery_featured_item" value="'+attch_id+'">');

             numItems++;

             }, 3000);*/


        });


        myDropzone.on("removedfile", function(file) {
            // console.log(file);
            //console.log(file._removeLink.attributes['data-attachment-id']);
            var attach_attr = file._removeLink.attributes['data-attachment-id'];
            var file_id = attach_attr.nodeValue;
            if(file_id != ""){
                $.ajax({
                    url: "<?php echo url('/api/system/attachment/delete'); ?>",
                    dataType: "json",
                    data: {"attachment_id": file_id},
                    beforeSend: function () {
                    }
                }).done(function (data) {

                });
            }

        });

        $('#removeGallery').click(function(){
            alert($('#removeGallery').val());
        });

                @if(isset($update->gallery[0]))
                <?php
                $image_name = ''; $image_size = '';
                if(isset($update->gallery[0]->data_packet)){
                    $data_packet = json_decode($update->gallery[0]->data_packet,true);
                    $image_name = $data_packet['name'];
                    $image_size = $data_packet['size'];
                }
                ?>
        var mockFile = { name: "{!! $image_name !!}", size: "{!! $image_size !!}" };
        myDropzone.emit("addedfile", mockFile);
        // And optionally show the thumbnail of the file:
        myDropzone.emit("thumbnail", mockFile, "{!! $update->gallery[0]->file !!}");

        $('.dz-remove').attr('data-attachment-id',"{!! $update->gallery[0]->attachment_id !!}");
        @endif



        @endif

            $('.user_status_field').addClass('hide');
       {{-- $('#role_id').attr('disabled','disabled');
        $('#parent_role_id').attr('disabled','disabled');--}}
    });

</script>
@include(config('panel.DIR').'footer')