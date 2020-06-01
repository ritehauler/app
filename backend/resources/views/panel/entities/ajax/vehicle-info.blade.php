<?php

//echo "<pre>"; print_r($data); exit;
?>
<p><label class="field-label">Truck: <span class="item_value" id="truck_name">{!! $data->truck_id->value !!}</span></label></p>
<p><label class="field-label">Driver: <span class="item_value" id="vehicle_name">{!! $data->driver_id->detail->full_name !!}</span></label></p>
<p><label class="field-label">Driver Shift: <span class="item_value" id="vehicle_code">{!! $data->driver_id->detail->shift->value !!}</span></label></p>
