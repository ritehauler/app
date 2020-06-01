@include(config('panel.DIR').'header')
<?php
$fields = "App\Libraries\Fields";
$fields = new $fields();
?>
<!-- Begin: Content -->

<section id="content_wrapper" class="content">
    <section id="content" class="table-layout animated fadeIn">

        <!-- begin: .tray-center -->
        <div class="tray tray-center p25 va-t posr">

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
                            <div>

                                <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
                                <input type="hidden" name="do_post" value="1" />

                               <?php
                                /*add hidden fields*/
                                if(isset($hidden_records[0])){
                                    foreach($hidden_records as $hidden_record){
                                            echo $fields->randerFields($hidden_record,$update,0,true);

                                    }
                                }

                                if (isset($records[0])) {
                                 //  print_r($records); exit;
                                ?>

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
                                            <div class="dz-default dz-message" style="padding: 5px;">
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
                                                 echo $fields->randerFields($record,$update,0,true);
                                                ?> </div> {{--end of section--}}

                                            <?php
                                            //condition for first row having image close first div.row and div.col-md-8
                                            if(($key == 1) OR count($records)== 0){?> </div> </div>
                                    <?php }  else{
                                    //condition to close div.row which has been started after first row
                                    if($key ==(count($records)-1)){?> </div><?php }
                                }
                                ?>








                                <?php  } //end of foreach ?>


                                <?php }
                                ?>




                            </div>

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
<!-- End: Page Footer -->
<script type="text/javascript">
    $(function () {
        // default form submit/validate
        $('form[name="data_form"]').submit(function (e) {
            e.preventDefault();
            // hide all errors
            $("div[id^=error_msg_]").removeClass("show").addClass("hide");
            // validate form
            Common.jsonValidation('<?=Request::url()?>', this,'',"category");
        });


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

        @if(isset($update->image->attachment_id))
            <?php
            $gallery = $update->image;

           ?>
            var mockFile = { name: "{!! $gallery->title !!}", size: "{!! $gallery->size !!}" };
            myDropzone.emit("addedfile", mockFile);
            // And optionally show the thumbnail of the file:
            myDropzone.emit("thumbnail", mockFile, "{!! $gallery->file !!}");

            $('.dz-remove').attr('data-attachment-id',"{!! $gallery->attachment_id !!}");
        @endif

        <?php if(isset($update) && $update->is_parent == 1): ?>

            $(".parent_id_field").hide();
            $(".title_field").parent('div').removeClass('col-md-6');
            $(".title_field").parent('div').addClass('col-md-12');
            $(".is_featured_field").hide();
            $(".featured_type_field").hide();

           /* $('.gallery-wrap').show();
            $('.top-wrap').removeClass('col-md-12');
            $('.top-wrap').addClass('col-md-8');
            $('.top-wrap').each(function(){
                $(this).children('div').removeClass('col-md-6');
            });*/


        <?php else: ?>

            $(".parent_id_field").show();
            $(".is_featured_field").show();
            $(".featured_type_field").show();

           /* $('.gallery-wrap').hide();
           $('.top-wrap').removeClass('col-md-8');
           $('.top-wrap').addClass('col-md-12');
            $('.top-wrap').each(function(){
               $(this).children('div').addClass('col-md-6');
            });*/
        <?php endif; ?>


          $("input[name='is_featured']").on("change",function(){
            if($(this).val() == 1){
                $(".featured_type_field").show();
            }
            else{
                $(".featured_type_field").hide();
            }
        });


        if($('#is_featured').length > 0){


            <?php if(isset($update) && isset($update->is_featured)): ?>

                var is_featured = "{!! $update->is_featured !!}";
                    if(is_featured != 1){
                        $('.featured_type_field').addClass('hide');

                    }
                    else{
                        $('.featured_type_field').removeClass('hide');
                    }
             <?php endif; ?>

               // $('.featured_type_field').addClass('hide');

            $('#is_featured').on('change',function(){
                if($('#is_featured').val() == 1){
                    $('.featured_type_field').removeClass('hide');
                }
                else{
                    $('.featured_type_field').addClass('hide');
                }
            });
        }

    });
</script> 
@include(config('panel.DIR').'footer')