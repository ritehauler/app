@include(config('panel.DIR').'header')
<?php
$fields = "App\Libraries\Fields";
$fields = new $fields();

 $entity_helper = "App\Libraries\EntityHelper";
        $entity_helper = new $entity_helper();
?>
        <!-- Begin: Content -->
<section id="content_wrapper" class="content">
    <section id="content" >

        <!-- begin: .tray-center -->
        <div class="tray tray-center p25 va-t posr">

            <div class="col-md-12">
                <!-- create new order panel -->
                <div class="panel panel-theme panel-border top mb25">
                    <div class="panel-heading">
                        <span class="panel-title">{!! $page_action !!} {!! isset($entity_data->title) ? $entity_data->title : $module !!}</span>
                    </div>
                    <div class="panel-body p20 pb10">
                        <form  name="data_form" method="post" id="data_form" class="panel-collapse collapse in">
                            <div class="tab-content pn br-n admin-form">
                                @include(config('panel.DIR').'flash_message')
                                <table class="view_popup_table">
                                    <?php
                                    if (isset($update)) {

                                        $i = 0;

                                    foreach ($records as $key => $record) {


                                    if(isset($update->{$record->name}))
                                    {
                                        if($record->name == 'role_id') continue;
                                        $value = $update->{$record->name};
                                         $column_label = $record->description;

                                        ?>

                                      <tr>

                                            <td><h4><?php echo $column_label ?></h4></td>
                                            <td><span><?php echo $value ?></span></td>

                                           </tr>
                                        <?php
                                        $i++; }
                                            }
                                    }
                                    ?>
                                </table>

                                        <!-- end section row section -->
                            </div>
                        </form>


                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel panel-theme panel-border top mb25">
                    <div class="panel-heading">
                        <span class="panel-title">Permission</span>
                    </div>
                    <div class="panel-body dark pn pb15">
                        <div class="panel">
                            <div class="table-responsive permissionTable">
                                <table class="table table-hover responsive " id="mydatatable" cellspacing="0" width="100%">
                                    <thead>
                                    <tr>
                                        <?php foreach ($columns as $column_field) { ?>
                                        <th><?= $column_field ?></th>
                                        <?php } ?>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>  <!-- end tray -->

    </section>
    <!-- End: Content -->
    <!-- Begin: Page Footer -->
    @include(config('panel.DIR') . 'footer_bottom')
</section>

<!-- Datatables -->
<script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/datatables/media/js/datatables.min.js' ) !!}" ></script>
<!-- End: Page Footer -->
<script type="text/javascript">
    $(document).ready(function () {


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
        if ($('a').hasClass('admin-status')) {
            $('.admin-status').editable({
                showbuttons: false,
                source: [
                    {value: 1, text: 'Active'},
                    {value: 2, text: 'Inactive'},
                ]
            });
        }

        <?php if(isset($update->is_group) && $update->is_group != 1)
        { ?>
        // data grid generation
        $('#mydatatable').DataTable().destroy();
        var dg = $("#mydatatable").DataTable({
            processing: true,
            serverSide: true,
            paging: false,
            searching: false,
            "aaSorting": [],
            //bStateSave: true, // save datatable state(pagination, sort, etc) in cookie.
            ajax: {

                url: " {!! URL::to($panel_path.$module.'/moduleslisting') !!}?_token=<?php echo  csrf_token(); ?>", // ajax source
                type: "POST",
                data: function (d) {
                    for (var attrname in dg_ajax_params) {
                        d[attrname] = dg_ajax_params[attrname];
                    }
                    d.search_columns = $('.search_columns').val();
                    d.role_id =  "<?= $update->role_id; ?>";
                }
            },
            drawCallback: function (settings) {

            },
            pageLength: 10, // default record count per page
            columnDefs: [
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
            ]
        });
        // add search to datatable
        dgSearch(dg);
        // add select actions to datatable
        dgSelectActions(dg);
    });
    <?php } ?>
    /* var myurl;
     // check_ids_view
     var checked = [''];
     var Unchecked = [''];
     $(".save_check").click(function () {
         $.ajax({
             type: "POST",
             url: '{!! URL::to($panel_path.$module.'/modulesupdate') !!}?_token={!! csrf_token() !!}',
            data:  $("#roles").serialize(),
            success: function (data) {
                window.location.reload(true);
            }
        });
    })*/


    //    function ChangeCheck(id,role){
    //       var arr['allChange'][id] = role;
    //        console.log(arr);
    //    }
</script>
@include(config('panel.DIR').'footer')