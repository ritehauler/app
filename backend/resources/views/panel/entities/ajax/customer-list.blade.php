<?php
if($data){
if(count($data) > 0){
foreach($data as $list){
  if(isset($list->login_status)){
      if($list->login_status == 0){
          $class = "text-success";
      }else{
          $class = "text-warning";
      }
  }else{
     $class = "text-warning";
  }
?>
<tr>
    <td>
        <span class="fa fa-circle {{ $class }} fs14 mr10"></span>{!! $list->first_name.' '.$list->last_name !!}</td>
    <td>{!! $list->total_count !!} Order</td>
</tr>
<?php
}
}  else{ ?>
<tr>
    <td>
        <span class="fa fa-circle text-warning fs14 mr10"></span>No Record Found</td>
    <td>&nbsp;</td>
</tr>
<?php  }
}
else{ ?>
<tr>
    <td>
        <span class="fa fa-circle text-warning fs14 mr10"></span>No Record Found</td>
    <td>&nbsp;</td>
</tr>
<?php  } ?>
