
<?php
        if(isset($is_update_order)){
            if(isset($data['order'])){
              //  print_r($data['order']); exit;
                $order = (object)$data['order'];
            }
        }

    $estimated_distance = isset($order->estimated_distance) ? $order->estimated_distance : "";
    $estimated_minutes = isset($order->estimated_minutes) ? $order->estimated_minutes : "";

    $min_estimated_charges = isset($order->min_estimated_charges) ? $order->min_estimated_charges : 0;
    $max_estimated_charges = isset($order->max_estimated_charges) ? $order->max_estimated_charges : 0;

    $total_weight = isset($order->weight) ? $order->weight : 0;
    $total_volume = isset($order->volume) ? $order->volume : 0;

    $base_fee = isset($order->base_fee) ? $order->base_fee : 0;
    $charge_per_minute = isset($order->charge_per_minute) ? $order->charge_per_minute : 0;

    $loading_price = (isset($order->loading_price) && $order->loading_price > 0) ? $order->loading_price : 0;
    $extra_item_charges = (isset($order->extra_item_charges) && $order->extra_item_charges > 0) ? $order->extra_item_charges : 0;

    $total_minutes = (isset($order->total_minutes) && $order->total_minutes > 0) ? $order->total_minutes : 0;
    $total_distance = (isset($order->total_distance) && $order->total_distance > 0) ? $order->total_distance : 0;
    $actual_charges = (isset($order->actual_charges) && $order->actual_charges > 0) ? $order->actual_charges : 0;

    $pre_grand_total = isset($order->pre_grand_total) ? $order->pre_grand_total : 0;
    $grand_total = (isset($order->grand_total) && $order->grand_total > 0) ? $order->grand_total : 0;
    $payment_method_fee = (isset($order->payment_method_fee) && $order->payment_method_fee > 0) ? $order->payment_method_fee : 0;
        ?>

<?php

        if(isset($data['warning']) && $data['warning'] == 1){
            ?>
            <div class="alert alert-warning">{!! $data['warning_message'] !!}</div>
        <?php
              }
        ?>

            <!--<table width="100%" class="orderPrevDetail">
                <tr>
                    <td>
                        <table width="">

                            <tr>
                                <td>Total Weight</td>
                                <td><b><span>{!! $total_weight !!}</span>&nbsp;kg</b></td>
                            </tr>
                            <tr>
                                <td>Total Volume </td>
                                <td><b><span>{!! $total_volume !!}</span>&nbsp;ft</b></td>
                            </tr>

                            <tr>
                                <td>Estimated Minutes</td>
                                <td><b><span>{!! $estimated_minutes !!}</span>&nbsp;min</b></td>
                            </tr>
                            <tr>
                                <td>Estimated Distance </td>
                                <td><b><span>{!! $estimated_distance !!}</span>&nbsp;mi</b></td>
                            </tr>

                        </table>
                    </td>
                    <td>
                        <table width="">
                            <tr>
                                <td>Base Fee</td>
                                <td><b><span>$</span><span>{!! $base_fee !!}</span></b></td>
                            </tr>
                            <tr>
                                <td>Charge Per Minute</td>
                                <td><b><span>$</span><span>{!! $charge_per_minute !!}</span></b></td>
                            </tr>
                            <tr>
                                <td>Estimated Charges</td>
                                <td>&nbsp;<b><span>$</span><span>{!! $min_estimated_charges.' - $'.$max_estimated_charges !!}</span></b></td>
                            </tr>
                            <tr>
                                <td>Loading Charges</td>
                                <td><b><span>$</span><span>{!! $loading_price !!}</span></b></td>
                            </tr>
                            <tr>
                                <td>Pre Grand Total</td>
                                <td><b><span>$</span><span>{!! $pre_grand_total !!}</span></b></td>
                            </tr>

                        </table>
                    </td>
                    <td>
                        <table width="">
                            <tr>
                                <td>Total Minutes</td>
                                <td><b><span>&nbsp;</span><span>{!! $total_minutes !!}</span>&nbsp;min</b></td>
                            </tr>
                            <tr>
                                <td>Total Distance</td>
                                <td><b><span>&nbsp;</span><span>{!! $total_distance !!}</span>&nbsp;mi</b></td>
                            </tr>
                            <tr>
                                <td>Total Charges</td>
                                <td><b><span>$</span><span>{!! $actual_charges !!}</span></b></td>
                            </tr>
                            <tr>
                                <td>Extra Item Charges</td>
                                <td><b><span>$</span><span>{!! $extra_item_charges !!}</span></b></td>
                            </tr>
                            <tr>
                                <td>Grand Total</td>
                                <td><b><span>$</span><span>{!! $grand_total !!}</span></b></td>
                            </tr>
                        </table>
                    </td>
                </tr>

            </table>-->


        <table width="100%" class="table orderPrevDetail mb30">
            <tr>
                <td>Total Weight</td>
                <td align="right"><b><span>{!! $total_weight !!}</span>&nbsp;{!! config("constants.WEIGHT_UNIT") !!}</b></td>
            </tr>
            <tr>
                <td>Total Volume </td>
                <td align="right"><b><span>{!! $total_volume !!}</span>&nbsp;{!! config("constants.VOLUME_UNIT") !!}</b></td>
            </tr>

            <tr>
                <td>Estimated Minutes</td>
                <td align="right"><b><span>{!! $estimated_minutes !!}</span>&nbsp;min</b></td>
            </tr>
            <tr>
                <td>Estimated Distance </td>
                <td align="right"><b><span>{!! $estimated_distance !!}</span>&nbsp;mi</b></td>
            </tr>
        </table>

        <table width="100%" class="table orderPrevDetail mb30">
            <tr>
                <td>Base Fee</td>
                <td align="right"><b><span>$</span><span>{!! $base_fee !!}</span></b></td>
            </tr>
            <tr>
                <td>Charge Per Minute</td>
                <td align="right"><b><span>$</span><span>{!! $charge_per_minute !!}</span></b></td>
            </tr>
            <tr>
                <td>Estimated Charges</td>
                <td align="right">&nbsp;<b><span>$</span><span>{!! $min_estimated_charges.' - $'.$max_estimated_charges !!}</span></b></td>
            </tr>
            <tr>
                <td>Loading Charges</td>
                <td align="right"><b><span>$</span><span>{!! $loading_price !!}</span></b></td>
            </tr>
            <tr>
                <td>Pre Grand Total</td>
                <td align="right"><b><span>$</span><span>{!! $pre_grand_total !!}</span></b></td>
            </tr>
        </table>

        <table width="100%" class="table orderPrevDetail">
            <tr>
                <td>Total Minutes</td>
                <td align="right"><b><span>&nbsp;</span><span>{!! $total_minutes !!}</span>&nbsp;min</b></td>
            </tr>
            <tr>
                <td>Total Distance</td>
                <td align="right"><b><span>&nbsp;</span><span>{!! $total_distance !!}</span>&nbsp;mi</b></td>
            </tr>
            <tr>
                <td>Total Charges</td>
                <td align="right"><b><span>$</span><span>{!! $actual_charges !!}</span></b></td>
            </tr>
            <tr>
                <td>Extra Item Charges</td>
                <td align="right"><b><span>$</span><span>{!! $extra_item_charges !!}</span></b></td>
            </tr>
            <tr>
                <td>Stripe Fees</td>
                <td align="right"><b><span>$</span><span>{!! $payment_method_fee !!}</span></b></td>
            </tr>
            <tr>
                <td>Grand Total</td>
                <td align="right"><b><span>$</span><span>{!! $grand_total !!}</span></b></td>
            </tr>
        </table>
