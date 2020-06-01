@include(config('panel.DIR').'header')
<!-- Start: Content-Wrapper -->
<!-- New Design Start --->
<section id="content_wrapper" class="content dubble_side">

    @if($order->attributes->order_status->detail->attributes->keyword != 'completed')
    <section id="content" class="table-layout animated fadeIn">
        <div class="tray tray-center p25 va-t posr">
            @include(config('panel.DIR').'flash_message')
            <div class="panel panel-theme panel-border top">
                <div class="panel-heading">
                    <span class="panel-title">Update Order Status</span>
                </div>
                <div id="orderModal" class="panel-body p15">
                    <div class="alert-message"></div>
                    <form  name="data_form" method="post" id="data_form">
                        <div id="orderContent">
                            Loading...
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-right">
                                 <button type="button" class="btn ladda-button btn-theme btn-wide order-update-btn" data-style="zoom-in" >Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </section>
    @endif
    <section id="content" class="table-layout animated fadeIn">
        <!-- begin: .tray-center -->
        <div class="tray tray-center p25 va-t posr">
        <!-- recent orders table -->
            <div class="panel panel-theme panel-border top mb25">
                <div class="panel-heading">
                    <span class="panel-title">Order History ( Order ID - {{ $order_id }})</span>
                </div>
                <div class="panel-body pn">
                    <form name="listing_form" method="post">
                        <section id="content" class="pn table-layout">
                            <div class="tray pn">
                                <div class="panel">
                                    <div class="table-responsive">
                                        <table class="table table-hover responsive fs13 smallTable smallImgTable" id="mydatatable" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Status</th>
                                                    <th>Driver</th>
                                                    <th>Vehicle</th>
                                                    <th>Comments</th>
                                                    <th>Created At</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @if(count($orderHistory->data->order_history))
                                                @foreach($orderHistory->data->order_history as $key => $order_history)
                                                <tr>
                                                    <td>{{ ($key + 1) }}</td>
                                                    <td>{{ $order_history->order_status->value }}</td>
                                                    <td>{{ count($order_history->driver_id) ? $order_history->driver_id->value : '' }}</td>
                                                    <td>{{ count($order_history->vehicle_id) ? $order_history->vehicle_id->value : '' }}</td>
                                                    <td>{{ isset($order_history->comment) ? $order_history->comment : '' }}</td>
                                                    <td>{{ date('d-m-Y h:i A',strtotime($order_history->created_at)) }}</td>
                                                </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="4">No Record Found</td>
                                                </tr>
                                            @endif
                                            </tbody>
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
    <!-- Begin: Page Footer -->
<!-- End: Page Footer -->
</section>
@include(config('panel.DIR') . 'footer_bottom')
<script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/datatables/media/js/datatables.min.js' ) !!}"></script>
<!-- ckeditor -->
<script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/ckeditor/ckeditor.js' ) !!}"></script>
<script>
    $(document).ready(function() {

    @if($order->attributes->order_status->detail->attributes->keyword != 'completed')
        var order_id = '{{ $order_id }}';
        $("#orderContent").text('Loading...');
        $(".order-update-btn").attr("disabled","disabled");

        $.ajax({
            type: "GET",
            url: "<?php echo url('getOrderStatus'); ?>",
            dataType: "json",
            data: {"order_id": order_id, "driver_id": ''},
            success: function (data) {
                //if(data.html){
                //onsole.log(data.data.html);
                $('#orderModal .alert-message').html('');
                $("#orderContent").html(data.data.html);
                $(".order-update-btn").removeAttr("disabled");
                //}

                if(data.data.vehicle_id != ""){
                    driverVehicleInfo(data.data.vehicle_id);
                }
            }
        });

        @endif
    });
</script>
@include(config('panel.DIR').'footer')