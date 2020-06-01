<?php
if($data){
    if(count($data) > 0){
        foreach($data as $list){
        ?>
        <tr>
            <td>
                <span class="fa fa-circle text-warning fs14 mr10"></span>{!! $list->day !!}</td>
            <td>{!! $list->start_time.'-'.$list->end_time !!}</td>
        </tr>
        <?php
        }
    }  else{ ?>
<tr>
    <td>
        <span class="fa fa-circle text-warning fs14 mr10"></span>No Delivery Slot Found</td>
    <td>&nbsp;</td>
</tr>
<?php  }
}
else{ ?>
<tr>
    <td>
        <span class="fa fa-circle text-warning fs14 mr10"></span>No Delivery Slot Found</td>
    <td>&nbsp;</td>
</tr>
<?php  } ?>
