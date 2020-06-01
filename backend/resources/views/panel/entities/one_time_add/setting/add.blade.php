@include(config('panel.DIR').'header')
<?php
$fields = "App\Libraries\Fields";
$fields = new $fields();

?>
        <!-- Begin: Content -->
<section id="content_wrapper" class="content">
    <section id="content" >
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-theme panel-border top mb25">
                    <div class="panel-heading">
                        <span class="panel-title">{!! isset($entity_data->title) ? $entity_data->title : $module !!}</span>
                    </div>
                    <form  name="data_form" method="post" id="data_form">
                        <div class="panel-body dark">
                            <div class="main admin-form ">
                                @include(config('panel.DIR').'flash_message')
                                @if (Session::has('message'))
                                    <div class="alert alert-info">{{ Session::get('message') }}</div>
                                @endif
                                <div class="alert-message"></div>
                                <input name="entity_action" type="hidden" value="add" />

                                @if(isset($entity_data->show_gallery) && $entity_data->show_gallery == 1)
                                    @include(config('panel.DIR').'entities/template/add_gallery')
                                    @else
                                    @include(config('panel.DIR').'entities/template/add_basic')
                                @endif

                        {{--        <div class="row mb20">
                                    <div class="section-divider mb30 mt15" id="spy1">
                                        <span>Warehouse Setting</span>
                                    </div>

                                    <div class="col-md-12 cuspad">
                                        <div class="map_canvas" style="width: 100%; height: 400px; margin: 10px 20px 10px 0; " ></div>
                                        <input class="map_textbox" id="geocomplete" type="text" placeholder="Type in an address" value="" />
                                        <input class="map_search" id="find" type="button" value="Find" />
                                        <input class="map_reset" id="reset" type="button" style="display:none;" value="Reset Marker" />
                                        <div class="map_text">Drag the map to your exact location</div>
                                    </div>
                                </div>--}}

                                <?php if($entity_data->depend_entity_type > 0){ ?>

                                <input type="hidden" id="depend_entity_exist" name="depend_entity_exist" value="1" />

                                <div class="bulk_entity_wrap">
                                    <div class="row bulk_entity_raw border-wrap" id="bulk_entity_raw_0">
                                        <div class="section-divider mb30 mt15" id="spy1">
                                            <span>Add Item</span>
                                        </div>

                                        <input type="hidden" name="entity_type_id" id="entity_type_id" value="{!! $depend_entity_type_data->entity_type_id !!}" />
                                        <?php

                                        /*add hidden fields*/
                                        if(isset($depend_entity_hidden_records[0])){
                                            foreach($depend_entity_hidden_records as $hidden_record){
                                                if($hidden_record->is_entity_column == 1){
                                                    echo $fields->randerEntityFields($hidden_record,$depend_entity_type_data,$depend_entity_type_data->entity_type_id);
                                                }else{
                                                    echo $fields->randerFields($hidden_record,$depend_entity_type_data,$depend_entity_type_data->entity_type_id);
                                                }
                                            }
                                        }?>


                                            @if(isset($depend_entity_type_data->show_gallery) && $depend_entity_type_data->show_gallery == 1)
                                                @include(config('panel.DIR').'entities/template/depend_add_gallery')
                                            @else
                                                @include(config('panel.DIR').'entities/template/depend_add_basic')
                                            @endif


                                    </div>

                                </div>

                                <?php  } ?>

                                <div class="pull-right p-relative">
                                    <button type="submit" class="btn ladda-button btn-theme btn-wide" data-style="zoom-in"> <span class="ladda-label">Submit</span> </button>
                                    @include(config('panel.DIR').'entities.loader')
                                </div>
                            </div>
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
{{--<script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'assets/js/map-script.js' ) !!}"></script>--}}
<script type="text/javascript">

    $(function () {

        $('form[name="data_form"]').submit(function(e) {
            e.preventDefault();
            setFullName();
            Common.jsonValidation('<?=Request::url()?>', this,'',"<?php echo $entity_data->identifier ?>");
        });

        // upload logo action

        if($('.gallery').length){
            //console.log( $('.gallery').length);

            $('.gallery').each(function(k,v){
                //  console.log( k);
                var galleryDivID =  $(this).attr('id');
                var galleryFieldID =  $(this).data('id');

                loadDropZoneUpload(galleryDivID,galleryFieldID);
            });
        }



        var entity_type_id = "{!! $entity_data->entity_type_id !!}";

                 <?php if(in_array($entity_data->identifier,array('product_tags','recipe_tags','bundle_tags'))){ ?>
                     if($("#price").val() == ""){
                         <?php if(isset($max_price)){ ?>
                         $('#price').val("<?php echo $max_price?>");
                         <?php } ?>
                     }
                 <?php } ?>
    });

    function loadDropZoneUpload(galleryDivID, galleryFieldID)
    {
        var baseUrl = "";
        var token = "{{ Session::getToken() }}";
        Dropzone.autoDiscover = false;
        var myDropzone = new Dropzone("div#"+galleryDivID, {

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

            $('.alert-message').html('');
            var image_response = $.parseJSON(responseText.jsonEditor);
            if(image_response.error == 1){
                // $('.dz-preview').remove();
                showAlert(image_response.message);
                this.removeFile(file);
            }
            else{
                attch_id = image_response.data.attachment.attachment_id;
            }
            //console.log($.parseJSON(responseText.jsonEditor).data.attachment.attachment_id);
            //$('.dz-complete').eq(numItems).append('<input type="checkbox" onClick="isfeatured()">');
            //numItems++;
            myDropzone.processQueue();
        });

        myDropzone.on("complete", function() {

            $('#'+galleryDivID).find('#'+galleryFieldID).val(attch_id);
            // $('#'+divID).find('.dz-complete').append('<div class="pull-right"><input type="hidden" name="gallery_items" value="'+attch_id+'"></div>');
            $('#'+galleryDivID).find('.dz-remove').attr('data-attachment-id',attch_id);
            /*setTimeout(function(){
             $('.dz-complete').eq(numItems).append('<input type="radio" name="gallery_featured_item" value="'+attch_id+'">');

             numItems++;

             }, 3000);*/


        });


        myDropzone.on("removedfile", function(file) {
            // console.log(file);
            //console.log(file._removeLink.attributes['data-attachment-id']);
            var file_id = '';
            if(  $('#'+galleryDivID).find('a.dz-remove').length > 0){

                var attach_attr = file._removeLink.attributes['data-attachment-id'];
                var file_id = attach_attr.nodeValue;
            }
            if(file_id != ""){
                $.ajax({
                    url: "<?php echo url('/api/system/attachment/delete'); ?>",
                    dataType: "json",
                    data: {"attachment_id": file_id},
                    beforeSend: function () {
                    }
                }).done(function (data) {
                    // console.log(galleryFieldID);
                    $('#'+galleryFieldID).val('');
                    if($('#'+galleryFieldID+'-file-info').length > 0){
                        $('#'+galleryFieldID+'-file-info').remove();
                    }

                });
            }

        });


    }


</script>
@include(config('panel.DIR').'footer')
