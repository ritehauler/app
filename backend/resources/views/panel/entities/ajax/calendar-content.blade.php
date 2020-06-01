<div class="tab-content row br-n admin-form">
                <table width="100%" class="table">
                    <thead>
                    <tr>
                        <td>Order #</td>
                        <td>Customer Name</td>
                        <td>Customer Phone</td>
                        <td>Driver Name</td>
                        <td>Driver Phone</td>
                        <td>Pickup Location</td>
                        <td>DropOff Location</td>
                        <td>Estimated Cost</td>
                        <td>Vehicle</td>
                    </tr>
                    </thead>
                    <tbody>
                    @if($orders)
                        @foreach($orders as $order)
                            <tr>
                                <td>{!! $order->order_number !!}</td>
                                <td>{!! $order->customer_name !!}</td>
                                <td>{!! $order->customer_mobile !!}</td>
                                <td>{!! $order->driver_name !!}</td>
                                <td>{!! $order->driver_mobile !!}</td>
                                <td>{!! $order->pickup !!}</td>
                                <td>{!! $order->dropoff !!}</td>
                                <td>${!! $order->pre_grand_total !!}</td>
                                <td>{!! $order->vehicle_name !!}</td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
