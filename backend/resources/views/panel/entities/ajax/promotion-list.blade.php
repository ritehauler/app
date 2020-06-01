<?php
    if($data){
        if(count($data) > 0){
            foreach($data as $list){
                $expiry_date  = date(DATE_FORMAT_ADMIN, strtotime($list->end_date));
        ?>
<tr>
    <td>
        <span class="fa fa-circle text-warning fs14 mr10"></span>{!! $list->title !!}</td>
    <td>{!! $expiry_date !!}</td>
</tr>
<?php
         }
        }  else{?>
<tr>
    <td>
        <span class="fa fa-circle text-warning fs14 mr10"></span>No Record Found</td>
    <td>&nbsp;</td>
</tr>
<?php  }
    }
        else{?>
<tr>
    <td>
        <span class="fa fa-circle text-warning fs14 mr10"></span>No Record Found</td>
    <td>&nbsp;</td>
</tr>
      <?php  }
?>
