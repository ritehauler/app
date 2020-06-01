<tbody>
@for($i = 1; $i<=25; $i++)
    <?php
    if($i <= 9) $i = '0'.$i;

    $ii = $i-1;
    $start_time =  $ii.":00:00";
    $end_time =  $i.":00:00";

    ?>
    <tr data-start-time="{!!  $i-1 !!}:00:00" data-end-time="{!!  $i !!}:00:00">
        <td width="4%" class="no-bottom-border"><span class="time">{!!  $i !!}:00</span></td>

        <?php
            for($d=0; $d <=6; $d++){
                 $count =  \App\Libraries\OrderHelper::checkOrderTimSlot($dates[$d],$start_time,$end_time,$orders);
                    $selected_class = '';
                 if($count > 0)
                     $selected_class = 'boxHighlight'

        ?>
        <td data-date="{!! $dates[$d] !!}" width="13.714%"><a data-date="{!! $dates[$d] !!}" data-start-time="{!! $start_time !!}" data-end-time="{!! $end_time !!}"  href="#" class="setEventModalDay modal-current-position {!! $selected_class !!}">{!! $count !!}</a></td>

         <?php unset($count); } ?>

    </tr>
@endfor

</tbody>