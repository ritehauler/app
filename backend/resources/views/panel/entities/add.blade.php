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
                        <span class="panel-title">{!! $page_action !!} {!! isset($entity_data->title) ? $entity_data->title : $module !!}</span>
                    </div>
                    <form  name="data_form" method="post" id="data_form">
                        <div class="panel-body dark p20">
                            <div class="main admin-form ">
                                @include(config('panel.DIR').'flash_message')
                                @if (Session::has('message'))
                                    <div class="alert alert-info">{{ Session::get('message') }}</div>
                                @endif

                                <div class="alert-message"></div>

                                @if(isset($entity_data->show_gallery) && $entity_data->show_gallery == 1)
                                    @include(config('panel.DIR').'entities/template/add_gallery')
                                    @else
                                    @include(config('panel.DIR').'entities/template/add_basic')
                                @endif

                                <?php if($entity_data->depend_entity_type > 0){ ?>

                                <input type="hidden" id="depend_entity_exist" name="depend_entity_exist" value="1" />

                                <div class="bulk_entity_wrap">
                                    <div class="clearfix bulk_entity_raw border-wrap mb20" id="bulk_entity_raw_0">
                                        {{--<div class="section-divider mb30 mt15" id="spy1">--}}
                                            {{--<span>Add Item</span>--}}
                                        {{--</div>--}}

                                        <input type="hidden" name="entity_type_id" id="entity_type_id" value="{!! $depend_entity_type_data->entity_type_id !!}" />
                                        <?php


                                        /*add hidden fields*/
                                        if(isset($depend_entity_hidden_records) && count($depend_entity_hidden_records)>0){
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


                                        <?php if($entity_data->identifier == "inventory"){ ?>

                                        <div id="itemWrap" class="col-md-12 hide">
                                            <p><label class="field-label">Category: <span class="item_value" id="item_category"></span></label></p>
                                            <p><label class="field-label">Item Code: <span class="item_value" id="item_code"></span></label></p>
                                        </div>

                                        <?php } ?>

                                        <?php if($entity_data->identifier == "promotion_discount"){ ?>

                                        <div id="itemWrap" class="col-md-12 hide">
                                            <p><label class="field-label">Category: <span class="item_value" id="item_category"></span></label></p>
                                            <p><label class="field-label">Retail Price: <span class="item_value" id="item_price"></span></label></p>
                                            <p><label class="field-label">Item Code: <span class="item_value" id="item_code"></span></label></p>
                                        </div>

                                        <?php } ?>

                                    </div>

                                </div>


                                <?php if(isset($entity_data->identifier) && (in_array($entity_data->identifier,array('inventory','recipe','promotion_discount','about_business','delivery_slot')))){ ?>

                                <button type="button" class="btn ladda-button btn-theme btn-wide add-more-entity" data-style="zoom-in"> <span class="ladda-label">Add More Items</span> </button>
                                <?php  } ?>

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

<script type="text/javascript">

    $(function () {

        $('form[name="data_form"]').submit(function(e) {
            e.preventDefault();
            setFullName();
            <?php if($entity_data->identifier == "inventory"){ ?>

            var vendor_name = $("#vendor_id option:selected").text();
            vendor_name = vendor_name.replace(/\s+/g, '-');
            $('#title').val(vendor_name);
            <?php } ?>

            <?php if($entity_data->identifier == "delivery_slot"){ ?>
            hideAlert();
            var is_verify  = verifyTimeSlots();

            if($.isNumeric(is_verify)){
                if(is_verify == 1){

                    e.preventDefault();
                    Common.jsonValidation('<?=Request::url()?>', this,'',"<?php echo $entity_data->identifier ?>");
                }
                else{
                    showAlert('Sorry delivery slot can not create');
                }
            }
            else{
                showAlert(is_verify);
            }
            <?php }else{  ?>

              Common.jsonValidation('<?=Request::url()?>', this,'',"<?php echo $entity_data->identifier ?>");
            <?php }  ?>
        });



        //$('[data-toggle="tooltip"]').tooltip({placement: 'top'});

        if($('#is_auth_exists').length > 0){
            $('#is_auth_exists').change(function () {
                var exists = $(this).val();
                console.log("check",exists);
                if (exists > 0) {
                    console.log("exists");
                    $("._not_exists").parents("div.col-md-6").hide();
                    $("._exists").parents("div.col-md-6").show();
                } else {
                    console.log("not exists");
                    $("._not_exists").parents("div.col-md-6").show();
                    $("._exists").parents("div.col-md-6").hide();
                }
            });

            var exists =  $('#is_auth_exists').val();
            console.log("check",exists);
            if (exists > 0) {
                console.log("exists");
                $("._not_exists").parents("div.col-md-6").hide();
                $("._exists").parents("div.col-md-6").show();
            } else {
                console.log("not exists");
                $("._not_exists").parents("div.col-md-6").show();
                $("._exists").parents("div.col-md-6").hide();
            }


        }



        $("input:file.media_image").on("change",function (){
            var fileName = $(this).val();
            alert(fileName);
            // $(".filename").html(fileName);
        });

        var el_commands = [
            'open', 'reload', 'getfile', 'quicklook',
            'download', 'mkdir', 'mkfile', 'upload','extract', 'search', 'info', 'view', 'help',
            'resize', 'sort',  'view',
        ];

        // upload logo action
        $("#gallery").click(function (e) {
            e.preventDefault();
            var field_name = $(this).children('img').attr('name');
            var fm = $('<div/>').dialogelfinder({
                url: 'imageBrowser?_token={!! csrf_token() !!}&referrer_action={!! $route_action !!}',
                lang: 'en',
                width: 840,
                commands: el_commands,
                destroyOnClose: true,
                getFileCallback: function (files, fm) {
                    console.log(files);
                    $("img[name="+field_name+"]").prop("src", "{!! URL::to(config('constants.IMAGES_UPLOAD_PATH'))."/".$entity_data->identifier !!}/" + files.name);
                    var image_path = "{!! config('constants.IMAGES_UPLOADS').$entity_data->identifier !!}/" + files.name;
                    $("input[name="+field_name+"]").val(image_path);
                },
                commandsOptions: {
                    getfile: {
                        oncomplete: 'close',
                        folders: false
                    }
                }
            }).dialogelfinder('instance');

        });

    @if((isset($entity_data->show_gallery) && $entity_data->show_gallery == 1))

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


            $('.dz-complete').append('<div class="pull-right"><input type="hidden" name="gallery_items[]" value="'+attch_id+'"></div>');
            $('.dz-remove').attr('data-attachment-id',attch_id);
            /*setTimeout(function(){
             $('.dz-complete').eq(numItems).append('<input type="radio" name="gallery_featured_item" value="'+attch_id+'">');

             numItems++;

             }, 3000);*/


        });


        myDropzone.on("removedfile", function(file) {
            var file_id = '';
            if($('#dropzoneFileUpload').find('a.dz-remove').length > 0){

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

                });
            }

        });


      /*  $('#removeGallery').click(function(){
            alert($('#removeGallery').val());
        });*/


        @endif

         @if((isset($depend_entity_type_data->show_gallery) && $depend_entity_type_data->show_gallery == 1))
             if($('#bulk_entity_raw_0').find(".dropzoneFileUpload").length > 0){
            var gallery = {};
                loadFileUpload('bulk_entity_raw_0',0,gallery);

            }
                @endif

        var entity_type_id = "{!! $entity_data->entity_type_id !!}";

        <?php if($entity_data->identifier != "customer"){ ?>

        if( $('#role_id').length > 0 && entity_type_id != ""){
            $('#role_id').empty();
            $('#role_id').append('<option value="">Select Role</option>');
            $.ajax({
                url: "<?php echo url('getRoleOptions'); ?>",
                dataType: "json",
                data: {"entity_type_id": entity_type_id},
                beforeSend: function () {
                    // $('#' + chosen_id).empty();
                }
            }).done(function (data) {
                $(data).each(function (index, ele) {
                    $('#role_id').append('<option value="' + ele.role_id + '">' + ele.title + '</option>');
                });
            });
        }

        <?php } ?>



    });



</script>
@include(config('panel.DIR').'footer')
