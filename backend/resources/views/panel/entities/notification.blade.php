@include(config('panel.DIR').'header')
<!-- Start: Content-Wrapper -->
<section id="content_wrapper" class="content">

    @if(Session::has('message'))
        <p class="alert {{ Session::get('alert-class', 'alert-danger') }}">{{ Session::get('message') }}</p>
    @endif
    <div id="seaction-header"> @include(config('panel.DIR').'flash_message')

        <section id="content" class="pn">
            <div class="panel panel-theme panel-border top mb25">
                <div class="panel-heading">
                    <span class="panel-title">Notification Listing</span>
                </div>
                <div class="panel-body pn">
                    <form name="listing_form" method="post">
                        <!-- End: Topbar -->
                        <!-- Begin: Content -->
                        <section id="content" class="pn table-layout">
                            <div class="tray pn">


                                <div class="panel">
                                    <div class="table-responsive">
                                        <table class="table table-hover responsive" id="mydatatable" cellspacing="0" width="100%">
                                            <thead>
                                            <tr>
                                                <?php
                                                foreach ($columns as $key => $column_field) {
                                                ?>
                                                <th><?= $column_field ?></th>
                                                <?php }  ?>
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
        </section>

        <!-- End: Content -->
        <!-- Begin: Page Footer -->
    @include(config('panel.DIR') . 'footer_bottom')
    <!-- End: Page Footer -->
</section>
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
                url: "{!! URL::to($panel_path.$module.'/ajaxListing') !!}?_token=<?php echo csrf_token(); ?>", // ajax source
                type: "POST",
                data : function(d) {
                    /*for (var attrname in dg_ajax_params) { d[attrname] = dg_ajax_params[attrname]; }
                    d.search_columns = $('.search_columns').val();*/
                    d.search_columns = dg_ajax_params;
                   // if ("checked_ids" in d.search_columns) d.checked_ids = d.search_columns['checked_ids']; delete d.search_columns['checked_ids'];
                  //  if ("select_action" in d.search_columns) d.select_action = d.search_columns['select_action']; delete d.search_columns['select_action'];
                }
            },
            drawCallback: function (settings) {

            },
            pageLength: 10, // default record count per page
            columnDefs : [<?php
                $index = 0;
                foreach ($columns as $index_key => $column_field) { ?>
            {
                data: "<?= $index_key ?>",
                orderable: false,
                targets: <?= $index ?>
            },
                <?php $index++;
                } ?>
            ],
            "createdRow": function ( row, data, index ) {
                $('td:last', row).addClass('hv-btns');
            },
        });

        // add search to datatable
        dgSearch(dg);
        // add select actions to datatable
        dgSelectActions(dg);
    });
</script>
@include(config('panel.DIR').'footer')