@include(config('panel.DIR').'header')
        <!-- Start: Content-Wrapper -->


<!-- New Design Start --->
<section id="content_wrapper" class="content dubble_side">
    <section id="content" class="table-layout">

        <!-- begin: .tray-center -->
        <div class="tray tray-center p25 va-t posr">
            <!-- create new order panel -->
            @include(config('panel.DIR').'category.add')

            <!-- recent orders table -->
            <div class="panel panel-theme panel-border top mb25">
                <div class="panel-heading">
                    <span class="panel-title">Listing</span>
					<span class="panel-controls">
                        <a class="select_action" title="delete">
						<button type="button" class="btn-default btn-sm link-unstyled ib" ><span class="icon mdi mdi-delete pr5 fs15"></span> Delete</button>
					    </a>
                    </span>
                </div>
                <div class="panel-body pn">
                    <form name="listing_form" method="post">
                        <section id="content" class="pn table-layout">
                            <div class="tray pn">
                                <div class="panel">
                                    <div class="table-responsive">

                                        <table class="table table-hover responsive fs13 smallTable" id="mydatatable" cellspacing="0" width="100%">
                                            <thead>
                                            <tr>
                                                <?php  foreach ($columns as $column_field) { ?>
                                                <th><?=  $column_field ?></th>
                                                <?php } ?>
                                            </tr>
                                            </thead>
                                        </table>

                                    </div>
                                </div>
                            </div>
                        </section>
                        <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
                    </form>
                </div>
            </div>


        </div>
        <!-- end: tray-center -->


    </section>

    @include(config('panel.DIR') . 'category/advance-search')
    <!-- Begin: Page Footer -->
    @include(config('panel.DIR') . 'footer_bottom')
            <!-- End: Page Footer -->

</section>


<!-- begin: .tray-right -->


<!-- end: .tray-right -->


<!-- Detailed View Popup -->
<!-- End: Content-Wrapper -->
<!-- Required Plugin CSS -->
<link rel="stylesheet" type="text/css" href="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/datepicker/css/bootstrap-datetimepicker.css' ) !!}">
<link rel="stylesheet" type="text/css" href="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/daterange/daterangepicker.css' ) !!}">
<!-- Page Plugins via CDN -->
<script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/moment/moment.min.js' ) !!}"></script>
<!-- Datatables -->
<script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/datatables/media/js/datatables.min.js' ) !!}"></script>
<!-- ckeditor -->
<script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/ckeditor/ckeditor.js' ) !!}"></script>
<!-- Page Plugins -->
<script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/xeditable/js/bootstrap-editable.js' ) !!}"></script>
<script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/daterange/daterangepicker.js' ) !!}"></script>
<script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/datepicker/js/bootstrap-datetimepicker.js' ) !!}"></script>

<script type="text/javascript" src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/holder/holder.min.js' ) !!}"></script>

<!-- Page Plugins -->
<script type="text/javascript" src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/magnific/jquery.magnific-popup.js' ) !!}"></script>


<script type="text/javascript">
    $(document).ready(function() {

        // Init Boostrap Multiselect
        $('#multiselect2').multiselect({
            includeSelectAllOption: true
        });
        // Init Boostrap Multiselect
        $('#multiselect3').multiselect({
            includeSelectAllOption: true
        });
        // Init daterange plugin
        $('#daterangepicker1').daterangepicker();
        // Init datetimepicker - fields
        $('#datetimepicker1').datetimepicker();
        // Select Status
        if ($('a').hasClass('admin-status')){
            $('.admin-status').editable({
                showbuttons: false,
                source: [
                    {value: 1, text: 'Active'},
                    {value: 2, text: 'Inactive'},
                ]
            });
        }



        // data grid generation
        $('#mydatatable').DataTable().destroy();
        var dg = $("#mydatatable").DataTable({
            processing: true,
            serverSide: true,
            //paging: false,
            searching: false,
            "aaSorting": [],
            "oLanguage":{
                "sProcessing": loaderHtml(),
            },
            //bStateSave: true, // save datatable state(pagination, sort, etc) in cookie.
            ajax: {
                url: "{!! $module !!}/ajax/listing?_token=<?php echo csrf_token(); ?>", // ajax source
                type: "POST",
                data : function(d) {
                   /* for (var attrname in dg_ajax_params) { d[attrname] = dg_ajax_params[attrname]; }
                    d.search_columns = $('.search_columns').val();*/
                    d.search_columns = dg_ajax_params;
                    if ("checked_ids" in d.search_columns) d.checked_ids = d.search_columns['checked_ids']; delete d.search_columns['checked_ids'];
                    if ("select_action" in d.search_columns) d.select_action = d.search_columns['select_action']; delete d.search_columns['select_action'];

                }
            },
            drawCallback: function (settings) {

            },
            pageLength: 10, // default record count per page
            columnDefs : [

                    <?php $index = 0;
                    foreach ($columns as $index_key => $column_field) {
                    ?>
                {
                    data: "<?= $index_key ?>",
                    orderable: false,
                    targets: <?= $index ?>
                },
                <?php $index++;
                }
                ?>
            ],
            "createdRow": function ( row, data, index ) {
                $('td:last', row).addClass('hv-btns');
            }
        });
        // add search to datatable
        dgSearch(dg);
        // add select actions to datatable
        dgSelectActions(dg);
    });
</script>
<script type="text/javascript">
    $(function () {
        // default form submit/validate


        $('form[name="data_form"]').submit(function(e) { //alert('sam'); return false;
            e.preventDefault();
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


            $('.dz-complete').append('<div class="pull-right"><input type="hidden" name="gallery_items[]" value="'+attch_id+'"></div>');
            $('.dz-remove').attr('data-attachment-id',attch_id);
            /*setTimeout(function(){
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

        if($("#is_parent:checked").val() == 1){
            $(".parent_id_field").hide();
            $(".is_featured_field").hide();
            $(".category_type_field").show();
            $(".featured_type_field").hide();

     /*       $('.gallery-wrap').show();
            $('.top-wrap').removeClass('col-md-12');
            $('.top-wrap').addClass('col-md-8');
            $('.top-wrap').each(function(){
                $(this).children('div').removeClass('col-md-6');
            });*/

        }else{
            $(".parent_id_field").show();
            $(".is_featured_field").show();
            $(".category_type_field").hide();

          /*  $('.gallery-wrap').hide();
            $('.top-wrap').removeClass('col-md-8');
            $('.top-wrap').addClass('col-md-12');
            $('.top-wrap').each(function(){
               $(this).children('div').addClass('col-md-6');
            });*/
        }


        $('select#category_type>option[value="1"]').prop('selected', true);
        $(".category_type_field").hide();

        $("input[name='is_parent']").on("click",function(){

            //alert($("#is_parent:checked").val());
            if($("#is_parent:checked").val() == 1){
                $(".parent_id_field").hide();
                $(".is_featured_field").hide();
                $(".featured_type_field").hide();
               // $(".category_type_field").show();

               /* $('.gallery-wrap').show();
                $('.top-wrap').removeClass('col-md-12');
                $('.top-wrap').addClass('col-md-8');
                $('.top-wrap').each(function(){
                    $(this).children('div').removeClass('col-md-6');
                });*/

            }else{
                $(".parent_id_field").show();
                $(".is_featured_field").show();
               // $(".featured_type_field").show();
               // $(".category_type_field").hide();

               /* $('.gallery-wrap').hide();
                $('.top-wrap').removeClass('col-md-8');
                $('.top-wrap').addClass('col-md-12');
                $('.top-wrap').each(function(){
                    $(this).children('div').addClass('col-md-6');
                });*/
            }
        });


        $("input[name='is_featured']").on("change",function(){
            if($(this).val() == 1){
                $(".featured_type_field").show();
            }
            else{
                $(".featured_type_field").hide();
            }
        });

        if($('#is_featured').length > 0){

            $('.featured_type_field').addClass('hide');

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