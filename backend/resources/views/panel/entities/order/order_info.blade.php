<div class="row">
    <div class="col-md-6">
        <div class="panel panel-theme top mb25">
            <div class="panel-body p20 pb10">
                <table width="100%" class="orderPrevDetail">
                    <tr>
                        <td>Order #</td>
                        <td><b>{!! $order->order_number !!}</b></td>
                    </tr>
                    <tr>
                        <td>Order Date</td>
                        <td><b>{!! $order_date !!}</b></td>
                    </tr>
                    <tr>
                        <td>Order Status</td>
                        <td><b>{!! $order_status !!}</b></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-theme  top mb25">

            <div class="panel-body p20 pb10">
                <table width="100%" class="orderPrevDetail">
                    <tr>
                        <td>Customer Name</td>
                        <td><b>{!! $customer_name !!}</b></td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td><b>{!! $email !!}</b></td>
                    </tr>
                    <tr>
                        <td>Contact</td>
                        <td><b>{!! $phone !!}</b></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="panel panel-theme top mb25">
            <div class="panel-body p20 pb10">
                <table width="100%" class="orderPrevDetail">
                    <tr>
                        <td>Pickup Location</td>
                        <td><b>{!! isset($update->order_pickup[0]->attributes->address) ? $update->order_pickup[0]->attributes->address : 'No Address' !!}</b></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-theme  top mb25">
            <div class="panel-body p20 pb10">
                <table width="100%" class="orderPrevDetail">
                    <tr>
                        <td>Drop Off Location</td>
                        <td><b>{!! isset($update->order_dropoff[0]->attributes->address) ? $update->order_dropoff[0]->attributes->address : 'No Address' !!}</b></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>