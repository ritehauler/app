@include(config('panel.DIR').'header')
<?php
$fields = "App\Libraries\Fields";
$fields = new $fields();
?>
<!-- Begin: Content -->
<section id="content_wrapper" class="content">
    <section id="content">
        <div class="row">
            <form  name="data_form" method="post" id="data_form">
                <div class="col-md-12">
                    <!-- Form Design Change -->
                    <div class="panel panel-theme panel-border top mb25">
                        <div class="panel-heading">
                            <span class="panel-title">{!! $page_action !!} {!! $module !!}</span>
                        </div>

                        <div class="panel-body dark">
                            <div class="main admin-form ">
                                @include(config('panel.DIR').'flash_message')

                                <div class="alert-message"></div>
                                <div class="row">
                                        <?php
                                        if (isset($records[0])) {

                                            foreach ($records as $record) {
                                                if($record->element_type=='text') $record->element_type='input';
                                                echo $fields->randerInput($record, $update->data->role,true,array('uri_method' => $uri_method));
                                            }
                                        }
                                        ?>
                                    </div>

                            </div>
                            {{--<div class="pull-right">
                                <button type="submit" class="btn ladda-button btn-theme btn-wide" data-style="zoom-in"> <span class="ladda-label">Submit</span> </button>
                            </div>--}}
                        </div>
                        <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
                        <input type="hidden" name="do_post" value="1" />

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
							<div class="col-md-12 text-right p-relative">
                                @if($uri_method != 'view' )
								<button type="submit" class="btn ladda-button btn-theme btn-wide" data-style="zoom-in"> <span class="ladda-label">Submit</span> </button>
                                @include(config('panel.DIR').'entities.loader')
                                @else
                                    <a href="../update/{{ $update->data->role->role_id }}" type="submit" class="btn ladda-button btn-theme btn-wide mt10" data-style="zoom-in"> <span class="ladda-label">Edit Record</span> </a>
                                @endif
                            </div>
						</div>
					</div>
				</div>
            </form>
        </div>
    </section>



    <!-- End: Content -->
    <!-- Begin: Page Footer -->
    @include(config('panel.DIR') . 'footer_bottom')
</section>
<!-- Datatables -->
<script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/datatables/media/js/datatables.min.js' ) !!}" ></script>
<!-- End: Page Footer -->
<script type="text/javascript">
    $(function () {
        // default form submit/validate

    });
</script>
<script type="text/javascript">
    $(document).ready(function () {

        $('form[name="data_form"]').submit(function (e) {
            e.preventDefault();
            // hide all errors
            $("div[id^=error_msg_]").removeClass("show").addClass("hide");
            // validate form
            Common.jsonValidation('{!! $route_action !!}', $(this),'',"role");
        });

        <?php if(isset($update->data->role)
        //&& $update->data->role->parent_id == 0
        ){ ?>
        //   $("#entity_type_id").attr("disabled");
        $("#entity_type_id").prop("disabled", true);
        //$("#parent_id").prop("disabled", true);
        <?php } ?>

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
                    d.role_id =  "<?= $update->data->role->role_id; ?>";
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