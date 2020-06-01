<?php if(isset($order_revisions) && count($order_revisions) > 0){

?>

<div class="col-md-12">
    <div class="panel panel-theme panel-border top mb25">
        <div class="panel-heading">
            <span class="panel-title">Order History</span>
        </div>

        <?php
        $i= 1;
        foreach($order_revisions as $order_revision){
        $order_history = json_decode($order_revision->order_detail);
        $order_history_item = $order_history->order_item;

        ?>
        <span class="panel-title"> Revision - {!! $i !!}</span>

        <div class="panel-body pn">
            <table width="100%" class="table table-striped" >
                <tr>
                    <th width="10%">Product</th>
                    <th>Name</th>
                    <th>Qty</th>
                    <th>Price</th>
                </tr>

                <?php
                $no_item = 0;
                if(isset($order_history_item[0])){?>

                <?php foreach($order_history_item as $order_item){

                if(isset($order_item->product_id)){

                $gallery = $pl_atatchment->getAttachmentByEntityID($order_item->product_id->id);
                $file = $fields::getGalleryImageFile($gallery,'product');
                ?>
                <tr>
                    <td><img src="{!!$file !!}" class="mw30 mr15 border bw2 border-alert"></td>
                    <td>{!! $order_item->product_id->value !!}</td>
                    <td>{!! isset($order_item->quantity) ? $order_item->quantity : "" !!}</td>
                    <td>{!! $general_setting->getPrettyPrice($order_item->price) !!}</td>
                </tr>
                <?php
                $no_item++;
                }
                }
                }


                if($no_item == 0){
                ?>
                <tr><td colspan="4">No Item Found</td></tr>
                <?php }
                ?>

            </table>
        </div>

        <div class="panel-body p20 pb10">
            <table width="100%" class="orderPrevDetail">
                <tr>
                    <td>Sub Total</td>
                    <td><b>{!! $general_setting->getPrettyPrice((!empty($order_history->subtotal)) ? $order_history->subtotal : 0) !!}</b></td>
                </tr>
                <tr>
                    <td>Discount</td>
                    <td><b>{!!  $general_setting->getPrettyPrice((!empty($order_history->discount_amount)) ? $order_history->discount_amount : 0) !!}</b></td>
                </tr>
                <tr>
                    <td>Tax</td>
                    <td><b>{!! $general_setting->getPrettyPrice(isset($order_history->tax_amount) ? $order_history->tax_amount : 0) !!}</b></td>
                </tr>
                <tr>
                    <td>Delivery Charges</td>
                    <td><b>{!!  $general_setting->getPrettyPrice((!empty($order_history->delivery_charge)) ? $order_history->delivery_charge : 0) !!}</b></td>
                </tr>
                <tr>
                    <td>Grand Total</td>
                    <td><b>{!! $general_setting->getPrettyPrice(isset($order_history->grand_total) ? $order_history->grand_total : 0) !!}</b></td>
                </tr>
                <tr>
                    <td>Wallet</td>
                    <td><b>{!!  $general_setting->getPrettyPrice((!empty($order_history->wallet)) ? $order_history->wallet : 0) !!}</b></td>
                </tr>
                <tr>
                    <td>Total Paid</td>
                    <td><b>{!! $general_setting->getPrettyPrice(isset($order_history->grand_total) ? $order_history->grand_total : 0) !!}</b></td>
                </tr>

                <tr>
                    <td>Notes</td>
                    <td><b>{!! isset($order_history->order_notes) ? $order_history->order_notes : '' !!}</b></td>
                </tr>
                {{--<tr>
                    <td>Total Refunded</td>
                    <td><b>$0.00</b></td>
                </tr>
                <tr>
                    <td>Total Due</td>
                    <td><b>$0.00</b></td>
                </tr>--}}
            </table>
        </div>

        <?php $i++; } ?>
    </div>
</div>
<?php  } ?>