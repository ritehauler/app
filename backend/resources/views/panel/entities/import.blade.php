@include(config('panel.DIR').'header')
<?php
$fields = "App\Libraries\Fields";
use App\Http\Models\PLAttachment;
$fields = new $fields();

$heading =  isset($entity_data->title) ? $entity_data->title : $module;
$import_file = "";
if(\Session::has(ADMIN_SESS_KEY.'_POST_DATA')){
   $data = (object)\Session::get(ADMIN_SESS_KEY . '_POST_DATA');
   // echo "<pre>"; print_r($data); exit;
    $import_file = $data->import_file;

    if($import_file > 0){
        $pl_attachment_model = new PLAttachment();
        $attachment = $pl_attachment_model->get($import_file);

    }
}
?>
<!-- Begin: Content -->


    <section id="content_wrapper" class="content">
        <section id="content" class="pn">

            <!-- begin: .tray-center -->
            <div class="tray tray-center p25 va-t posr">

                <div class="panel panel-theme panel-border top mb25">
                    <div class="panel-heading">
                        <span class="panel-title">{!! $page_action !!} {!! $heading !!}</span>
                    </div>
                    <div class="panel-body p20 pb10">
                        <form  name="data_form" method="post" id="import_form" class="panel-collapse collapse in">
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
                                   <input type="hidden" name="import_file" id="import_file" value="{!! $import_file !!}">
                                   <div class="section mb10 col-md-6">
                                       <div class="dropzone" id="dropzoneFileUpload">
                                           <div class="dz-default dz-message">
                                               <img data-src="holder.js/300x200/big/text:300x200" alt="holder">
                                           </div>

                                       </div>
                                   </div>

                                   <div class="section mb10 col-md-6">
                                       Download Template
                                       <a class="export_template" title="export-template">
                                           <button type="button" class="btn-default btn-sm link-unstyled ib" ><span class="icon mdi mdi-export pr5 fs15"></span>Export Template</button>
                                       </a>
                                   </div>

                               </div>

                               <div class="pull-right">
                                    <button type="button" id="submit-btn" class="btn ladda-button btn-theme btn-wide mt10 hide" data-style="zoom-in"> <span class="ladda-label">Submit</span> </button>
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

        if($('#import_file').val() != ""){
            $('#submit-btn').removeClass('hide');
        }


		$('#entity_type_id').val("<?php echo $entity_data->entity_type_id;?>");
        // default form submit/validate
       /* $('form[name="data_form"]').submit(function (e) {
            e.preventDefault();
            // hide all errors
            $("div[id^=error_msg_]").removeClass("show").addClass("hide");

           // setFullName();
           // Common.jsonValidation('<?=Request::url()?>', this,'',"<?php echo $entity_data->identifier ?>");
        });
*/

        var baseUrl = "";
        var token = "{{ Session::getToken() }}";
        Dropzone.autoDiscover = false;
        var myDropzone = new Dropzone("div#dropzoneFileUpload", {

            url: baseUrl + "{!! URL::to('/api/system/attachment/save') !!}",
            addRemoveLinks: true,
            maxFiles:1,
            dictRemoveFileConfirmation:  "Are you sure you want to remove?",
            params: {
                _token: token,
                attachment_type_id: 9,
                entity_type_id:"<?php echo $entity_data->entity_type_id ?>"
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
           // $('.dz-complete').append('<div class="pull-right"><input type="hidden" name="import_file" value="'+attch_id+'"></div>');
           $('#import_file').val(attch_id);
            $('.dz-remove').attr('data-attachment-id',attch_id);
            $('#submit-btn').removeClass('hide');
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
                   $('#import_file').val('');
                   $('#submit-btn').addClass('hide');
                });
            }

        });

        $('#removeGallery').click(function(){
            alert($('#removeGallery').val());
        });


    @if(isset($attachment))
            <?php
                $image_name = ''; $image_size = '';
                if(isset($attachment->data_packet)){
                    $data_packet = json_decode($attachment->data_packet,true);
                    $image_name = $data_packet['name'];
                    $image_size = $data_packet['size'];
                }
                ?>
        var mockFile = { name: "{!! $image_name !!}", size: "{!! $image_size !!}" };
        myDropzone.emit("addedfile", mockFile);
        // And optionally show the thumbnail of the file:
        myDropzone.emit("thumbnail", mockFile, "{!! $attachment->file !!}");

        $('.dz-remove').attr('data-attachment-id',"{!! $attachment->attachment_id !!}");
        @endif


        $('#submit-btn').on('click',function(){

           if($('input[name="download_template"]').length > 0 ){
               $('input[name="download_template"]').remove();
           }
            $('#import_form').submit();
        });

    });

</script>
@include(config('panel.DIR').'footer')