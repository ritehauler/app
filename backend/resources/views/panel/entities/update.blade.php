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
                            <div class="main admin-form ">
                                @include(config('panel.DIR').'flash_message')
                                @if (Session::has('message'))
                                    <div class="alert alert-info">{{ Session::get('message') }}</div>
                                @endif

                                <div class="alert-message"></div>

                                @if(isset($entity_data->show_gallery) && $entity_data->show_gallery == 1)
                                    @include(config('panel.DIR').'entities/'.$form_template_dir.'update_gallery')
                                @else
                                    @include(config('panel.DIR').'entities/'.$form_template_dir.'/update_basic')
                                @endif


                                <?php if($entity_data->depend_entity_type > 0){ ?>

                                <?php if($entity_data->identifier == "delivery_slot"){ ?>
                                     <input type="hidden" name="delete_depend_entity_id" id="delete_depend_entity_id" value="" />
                                <?php  } ?>

                            @if(isset($depend_entity_type_data->show_gallery) && $depend_entity_type_data->show_gallery == 1)
                                    @include(config('panel.DIR').'entities/'.$form_template_dir.'/depend_update_gallery')
                                @else
                                    @include(config('panel.DIR').'entities/'.$form_template_dir.'/depend_update_basic')
                                @endif

                                <?php  } ?>

                                <div class="pull-right p-relative">
                                    @if($uri_method != 'view' )
                                    <button type="submit" class="btn ladda-button btn-theme btn-wide mt10 submit-btn" data-style="zoom-in"> <span class="ladda-label">Update</span> </button>
                                        @include(config('panel.DIR').'entities.loader')
                                    @else
                                        @if(count($modulePermission) && $modulePermission->update_permission == 1 )
                                    <a href="../update/{{ $update->entity_id }}" class="btn ladda-button btn-theme btn-wide mt10 submit-btn" data-style="zoom-in"><span class="ladda-label">Edit Record</span>
                                    </a>
                                        @endif
                                    @endif
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
        $('#entity_type_id').val("<?php echo $entity_data->entity_type_id;?>");

        var entity_type_id = "{!! $entity_data->entity_type_id !!}";

        if ($('#role_id').length > 0 && entity_type_id != "") {

                <?php if($entity_data->identifier != "customer"){ ?>

            var selected_role = $('#role_id').val();
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

                //  $('#role_id').val(selected_role).prop('selected', true);
                $('select>option[value="' + selected_role + '"]').prop('selected', true);
            });
            <?php } ?>

        }

                @if(isset($entity_data->show_gallery) && $entity_data->show_gallery == 1)

        var baseUrl = "";
        var token = "{{ Session::getToken() }}";
        Dropzone.autoDiscover = false;
        var myDropzone = new Dropzone("div#dropzoneFileUpload", {

            url: baseUrl + "{!! URL::to('/api/system/attachment/save') !!}",
            addRemoveLinks: '{{ $uri_method != 'view' ? true : false }}',
            maxFiles: 1,
            thumbnailWidth: parseInt(minThumbWidth),
            thumbnailHeight: parseInt(minThumbHeight),
            dictRemoveFileConfirmation: "Are you sure you want to remove?",
            params: {
                _token: token,
                attachment_type_id: 8,
                entity_type_id: 0
            }
        });
        var numItems = 0;
        var attch_id = '';
        myDropzone.on("success", function (file, responseText) {

            $('.alert-message').html('');
            var image_response = $.parseJSON(responseText.jsonEditor);
            if (image_response.error == 1) {
                // $('.dz-preview').remove();
                showAlert(image_response.message);
                this.removeFile(file);
            }
            else {
                attch_id = image_response.data.attachment.attachment_id;
            }
            //console.log($.parseJSON(responseText.jsonEditor).data.attachment.attachment_id);
            //$('.dz-complete').eq(numItems).append('<input type="checkbox" onClick="isfeatured()">');
            //numItems++;
            myDropzone.processQueue();
        });

        myDropzone.on("complete", function () {

            console.log(attch_id);
            console.log('yesss');
            $('.dz-complete').append('<div class="pull-right"><input type="hidden" name="gallery_items[]" value="' + attch_id + '"></div>');
            $('.dz-remove').attr('data-attachment-id', attch_id);
            /* setTimeout(function(){
                 $('.dz-complete').eq(numItems).append('<input type="radio" name="gallery_featured_item" value="'+attch_id+'">');

                 numItems++;

             }, 3000);*/


        });


        myDropzone.on("removedfile", function (file) {
            var file_id = '';
            if ($('#dropzoneFileUpload').find('a.dz-remove').length > 0) {

                var attach_attr = file._removeLink.attributes['data-attachment-id'];
                var file_id = attach_attr.nodeValue;
            }
            if (file_id != "") {
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

        $('#removeGallery').click(function () {
            alert($('#removeGallery').val());
        });

                @if(isset($update->gallery[0]))
            <?php
            $image_name = ''; $image_size = '';
            if (isset($update->gallery[0]->data_packet)) {
                $data_packet = json_decode($update->gallery[0]->data_packet, true);
                $image_name = $data_packet['name'];
                $image_size = $data_packet['size'];
            }
            ?>
        var mockFile = {name: "{!! $image_name !!}", size: "{!! $image_size !!}"};
        myDropzone.emit("addedfile", mockFile);
        // And optionally show the thumbnail of the file:
        myDropzone.emit("thumbnail", mockFile, "{!! $update->gallery[0]->file !!}");

        $('.dz-remove').attr('data-attachment-id', "{!! $update->gallery[0]->attachment_id !!}");
        @endif



                @endif

        <?php if($entity_data->identifier == "coupon"){ ?>
        if ($('.coupon_code_field').length > 0) {
            $('#coupon_code').prop("readonly", true);
        }
        <?php } ?>

        @if($entity_data->depend_entity_type > 0)

        $(".submit-btn").attr("type", "button");

                @if((isset($depend_entity_type_data->show_gallery) && $depend_entity_type_data->show_gallery == 1))
            <?php
            if (isset($depend_entity_records) && isset($depend_update)) {

            $depend_count = 0;

            foreach ($depend_update as $depend_update_item) { ?>

        var depend_count = "{!! $depend_count !!}";
        var divID = "bulk_entity_raw_{!! $depend_count !!}";
        var gallery = {};
        //console.log("#dropzoneFileUpload_"+depend_count);

        <?php
            if(isset($depend_update_item->gallery[0])){

            $gallery = $depend_update_item->gallery[0];
            $image_name = ''; $image_size = '';

            if (isset($gallery->data_packet)) {
                $data_packet = json_decode($gallery->data_packet, true);
                $image_name = $data_packet['name'];
                $image_size = $data_packet['size'];
            }
            ?>

            gallery['id'] = "{!! $gallery->attachment_id !!}";
        gallery['name'] = "{!! $image_name !!}";
        gallery['size'] = "{!! $image_size !!}";
        gallery['file'] = "{!! $gallery->file !!}";


        <?php } ?>

        loadFileUpload('bulk_entity_raw_{!! $depend_count !!}', "{!! $depend_count !!}", gallery);

        <?php $depend_count++; } ?>
        <?php } ?>

        @endif

        $(".submit-btn").on("click", function (e) {

            var is_verify = 1;

            <?php if($entity_data->identifier == "delivery_slot"){ ?>
            hideAlert();
            var is_verify = verifyTimeSlots();

            if ($.isNumeric(is_verify) == false) {
                showAlert(is_verify);
                return false;
            }

            <?php } ?>

            if (is_verify == 1){

                var identifier = "{!! isset($entity_data->identifier) ? $entity_data->identifier : '' !!}";

            var form = {};
            $.each($('.entity_wrap').find('select, textarea, input').serializeArray(), function () {
                form[this.name] = this.value;
            });

            if ($('#delete_depend_entity_id').length > 0) {
                form["delete_depend_entity_id"] = $('#delete_depend_entity_id').val();
            }

            form["bulk_entity"] = 1;

            Common.jsonValidation('<?=Request::url()?>', this, form, identifier, true);
            console.log(do_call);
            if (do_call != 1) {

                if ($('#delete_depend_entity_id').length > 0) {
                    $('#delete_depend_entity_id').val('');
                }

                $.each($('.bulk_entity_raw'), function (k, v) {

                    var depend_form = {};
                    $.each($(this).find('select, textarea, input').serializeArray(), function () {
                        var ele_name = this.name;
                        if (ele_name == "gallery_items") {
                            var temp = {};
                            temp[0] = this.value;
                            depend_form[this.name] = temp;
                        }
                        else {
                            depend_form[this.name] = this.value;
                        }


                    });

                    depend_form["bulk_entity_raw"] = k;
                    depend_form["bulk_entity"] = 1;

                    console.log(depend_form);
                    var response;
                    e.preventDefault();

                    response = Common.jsonValidation('<?=Request::url()?>', this, depend_form, identifier, true);
                    console.log(do_call);
                    if (do_call == 1) {
                        $(".submit-btn").removeClass('disabled');
                        return false;
                    }

                    return true;
                });

            }
            }
        });

                @else
                    // default form submit/validate
                    $('form[name="data_form"]').submit(function (e) {
                        e.preventDefault();
                        // hide all errors
                        $("div[id^=error_msg_]").removeClass("show").addClass("hide");
                        // validate form
                        Common.jsonValidation('<?=Request::url()?>', this,'',"<?php echo $entity_data->identifier ?>");
                    });

             @endif

     <?php if($entity_data->identifier == "promotion_discount"){ ?>

             $( document ).on( "change", "#promotion_product_id", function() {
                 var item_id = $(this).val();
                 var entity_type_id = $(this).data("type_id");
                 // console.log($(this).val());
                 var selected_item =  $(this).parents('div.bulk_entity_raw').attr('id');
                 // console.log(selected_item);

                 if(item_id != ""){

                     $.ajax({
                         url: "<?php echo url('getProductData'); ?>",
                         dataType: "json",
                         data: {"entity_id": item_id, "entity_type_id": entity_type_id},
                         beforeSend: function () {
                         }
                     }).done(function (data) {
                         // console.log(data.length);
                         if(data.length >0){
                             var item = data[0];
                             $('#'+selected_item).find('#itemWrap').removeClass('hide');
                             $('#'+selected_item).find('#itemWrap #item_price').text(item.price);
                             $('#'+selected_item).find('#itemWrap #item_code').text(item.item_code);
                             $('#'+selected_item).find('#itemWrap #item_category').text(item.category);

                         }

                     });
                 }
                 else{
                     $('#'+selected_item).find('#itemWrap .item_value').text('');
                     $('#'+selected_item).find('#itemWrap').addClass('hide');

                 }


             });



        <?php } ?>

                <?php if(in_array($entity_data->identifier,array('product_tags','recipe_tags','bundle_tags'))){ ?>
                    if($("#price").val() == ""){
                        <?php if(isset($max_price)){ ?>
                        $('#price').val("<?php echo $max_price?>");
                        <?php } ?>
                    }
                  <?php } ?>

    });

</script>
@include(config('panel.DIR').'footer')