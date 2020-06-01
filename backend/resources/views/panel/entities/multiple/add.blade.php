@include(config('panel.DIR').'header')
<?php
$fields = "App\Libraries\Fields";
$fields = new $fields();

?>
<!-- Begin: Content -->
<section id="content_wrapper" class="content">
    <section id="content" class="pn">
        <div class="col-md-8 col-md-offset-2 p30">
            <div class="row">
                <div class="col-md-12 mt0 mb25">
                    <h3>{!! $page_action !!} {!! isset($entity_data->identifier) ? ucwords($entity_data->identifier) : $module !!}</h3>
                </div>
            </div>

            <form  name="data_form"  method="post" id="data_form">
                <div class="main admin-form ">
                    @include(config('panel.DIR').'flash_message')
                    @if (Session::has('message'))
                           <div class="alert alert-info">{{ Session::get('message') }}</div>
                        @endif

                    <div class="alert alert-danger hide"></div>
                    <div class="alert alert-success hide"></div>

                    <div class="entity_wrap">
                        <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
                        <input type="hidden" name="do_post" value="1" />
                    </div>

                    <div class="bulk_entity_wrap">
                        <input type="hidden" id="is_bulk_entity" name="is_bulk_entity" value="1" />

                        <div class="row bulk_entity_raw border-wrap">
                        <?php
                        if (isset($records[0])) {
                            foreach ($records as $record) {
								if($record->element_type=='text') $record->element_type='input';
								echo $fields->randerInput($record,$entity_data,$entity_data->entity_type_id);
                            }
                        }
                        if(isset($img) ){
                          echo $img;
                        }


                        ?>
                        </div>
                    </div>

                      <button type="button" class="btn ladda-button btn-theme btn-wide add-more-entity" data-style="zoom-in"> <span class="ladda-label">Add More Items</span> </button>
                    <div class="pull-right">
                        <button type="submit" class="btn ladda-button btn-theme btn-wide" data-style="zoom-in"> <span class="ladda-label">Submit</span> </button>
                    </div>
                </div>

            </form>
        </div>
    </section>
    <!-- End: Content -->
    <!-- Begin: Page Footer -->
    @include(config('panel.DIR') . 'footer_bottom')
</section>
<!-- End: Page Footer -->

<script type="text/javascript">

    $(function () {

        "use strict";
        // Init Theme Core
        Core.init();
        // Init Demo JS
        Demo.init();

        // Init Common JS
        Common.init();

        $('form[name="data_form"]').submit(function(e) {
            e.preventDefault();
            Common.jsonValidate('<?=Request::url()?>', this);
        });


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

       @if(isset($entity_data->show_gallery) && $entity_data->show_gallery == 1)

       var baseUrl = "";
       var token = "{{ Session::getToken() }}";
       Dropzone.autoDiscover = false;
       var myDropzone = new Dropzone("div#dropzoneFileUpload", {

           url: baseUrl + "{!! URL::to('/api/system/attachment/save') !!}",
           addRemoveLinks: true,
           thumbnailWidth: parseInt(minThumbWidth),
           thumbnailHeight: parseInt(minThumbHeight),
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


           $('.dz-complete').append('<div class="pull-right"><input type="hidden" name="gallery_items[]" value="'+attch_id+'"></div>');

           setTimeout(function(){
               $('.dz-complete').eq(numItems).append('<input type="radio" name="gallery_featured_item" value="'+attch_id+'">');

               numItems++;

           }, 3000);


       });


       $('#removeGallery').click(function(){
           alert($('#removeGallery').val());
       });

       @endif

   });



</script>
@include(config('panel.DIR').'footer')
