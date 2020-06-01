@include(config('panel.DIR').'header')
<?php
use App\Libraries\CustomHelper;
$fields = "App\Libraries\Fields";
$fields = new $fields();
if($update){
    //order information
    $order_id = isset($update->entity_id) ? $update->entity_id : "";
    $order_date = isset($update->created_at) ? CustomHelper::displayDateTime($update->created_at) : "";
    $order = $update->attributes;

    //customer information
    $is_inactive = 0; $user_status = ''; $email = ''; $customer_name = ''; $phone = '';
    $customer_deleted = 0;
    $customer = $update->attributes->customer_id;
    if(isset($customer->detail->auth->email)){

        $email = isset($customer->detail->auth->email) ? $customer->detail->auth->email : "";
        $phone = isset($customer->detail->auth->mobile_no) ? $customer->detail->auth->mobile_no : "";
        // echo '<pre>'; print_r($update); exit;
        if(isset($customer->detail->attributes->full_name) && !empty($customer->detail->attributes->full_name)){
            $customer_name = $customer->detail->attributes->full_name;
        }
        else{
            $customer_name = CustomHelper::setFullName($customer->detail);
        }

        if($customer->detail->attributes->user_status->value != 1){
            $is_inactive = 1;
            $user_status = strtolower($customer->detail->attributes->user_status->option);
        }

    }
    else{
        $customer_deleted = 1;
    }


    $order_status = isset($order->order_status->value) ? $order->order_status->value : "";
    $order_status_keyword = isset($order->order_status->detail->attributes->keyword) ? $order->order_status->detail->attributes->keyword : "";
    //$star_rating = EntityHelper::parseAttributeToDisplay(isset($customer->detail->attributes->star_rating) ?  $customer->detail->attributes->star_rating : "");
   // $reviews = isset($customer->detail->attributes->reviews) ?  $customer->detail->attributes->reviews : "No Reviews";
}
?>
<!-- Begin: Content -->
    <section id="content_wrapper" class="content">
        <section id="content" class="table-layout animated fadeIn">
            <!-- begin: .tray-center -->
            <div class="tray tray-center p25 va-t posr">

                <div class="panel panel-theme panel-border top mb25">
                    <div class="panel-heading">
                        <span class="panel-title">{!! $page_action !!} {!! isset($entity_data->title) ? $entity_data->title : $module !!}</span>
                    </div>
                    <div class="panel-body p20 pb10">
                        <form  name="data_form" method="post" id="data_form" class="panel-collapse collapse in">
                            <div class="main admin-form ">
                                @include(config('panel.DIR').'flash_message')
                                @if (Session::has('message'))
                                    <div class="alert alert-info">{{ Session::get('message') }}</div>
                                @endif

                                @if($is_inactive == 1)
                                <div class="alert alert-warning"> <a data-dismiss="alert" class="close" href="#">×</a>
                                    The order cannot update beacause of customer is {!! $user_status !!}</div>
                                @endif

                                @if($customer_deleted == 1)
                                    <div class="alert alert-warning"> <a data-dismiss="alert" class="close" href="#">×</a>
                                        The order cannot update beacause of customer is deleted</div>
                                @endif

                                <div class="alert-message"></div>

                                @include(config('panel.DIR').'entities/'.$form_template_dir.'/order_info')


                                <input type="hidden" id="entity_type_identifier" name="entity_type_identifier" value="{!! $entity_data->identifier !!}"/>

                                @if(isset($entity_data->show_gallery) && $entity_data->show_gallery == 1)
                                    @include(config('panel.DIR').'entities/'.$form_template_dir.'update_gallery')
                                @else
                                    @include(config('panel.DIR').'entities/'.$form_template_dir.'/update_basic')
                                @endif


                                <?php if($entity_data->depend_entity_type > 0){ ?>

                                @if(isset($depend_entity_type_data->show_gallery) && $depend_entity_type_data->show_gallery == 1)
                                    @include(config('panel.DIR').'entities/'.$form_template_dir.'/depend_update_gallery')
                                @else
                                    @include(config('panel.DIR').'entities/'.$form_template_dir.'/depend_update_basic')
                                @endif


                                <div id="temp_depend_item" class="hide"></div>

                                <?php  } ?>

                                <br><br>
                                <div class="col-md-12">
                                    <div class="panel panel-theme top mb25">
                                        <div class="panel-body p20 pb10" >
                                            <div class="row">
                                                <div class="col-md-7">
                                                    <div id="map" class="map_canvas" style="width:100%; height:730px;border:0"></div>
                                                    {{-- <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2996.0339928370745!2d69.31817291507974!3d41.32987417926943!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x38aef4ec32242aa1%3A0x1d7ab8fa6515bea9!2sSayram+Blind+Alley%2C+Tashkent%2C+Uzbekistan!5e0!3m2!1sen!2s!4v1535459950308" width="100%" height="730px" frameborder="0" style="border:0" allowfullscreen></iframe>--}}
                                                </div>
                                                <div class="col-md-5" id="orderTotal">
                                                    @include(config('panel.DIR').'entities/'.$form_template_dir.'/order_total')
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            @if($uri_method != 'view')
                                <div class="pull-right p-relative">
                                    <button  type="button" class="btn ladda-button btn-theme btn-wide mt10 cancel-btn" data-style="zoom-in"> <span class="ladda-label">Cancel</span> </button>
                                <?php $disabled  = ($is_inactive == 1 || $customer_deleted == 1) ? 'disabled' : ""; ?>
                                    <button <?php echo $disabled; ?> type="submit" class="btn ladda-button btn-theme btn-wide mt10 submit-btn" data-style="zoom-in"> <span class="ladda-label">Update</span> </button>
                                    @include(config('panel.DIR').'entities.loader')
                                </div>
                            @else
                                    @if(count($modulePermission) && $modulePermission->update_permission == 1 )
                                <div class="pull-right">
                                    <a href="../update/{{ $update->entity_id  }}" class="btn ladda-button btn-theme btn-wide mt10 cancel-btn" data-style="zoom-in"> <span class="ladda-label">Edit Order</span> </a>
                                </div>
                                    @endif
                                       {{-- <div class="col-md-12 cuspad">
                                            <div id="map" class="map_canvas" style="width: 100%; height: 400px; margin: 10px 20px 10px 0; " ></div>
                                        </div>--}}
                            </div>
                            @endif
                                <!-- end section row section -->
                            </div>
                        </form>

                    </div>
                </div>

                </div>
            </section>


    <!-- End: Content -->
    <!-- Begin: Page Footer -->
    @include(config('panel.DIR') . 'footer_bottom')
</section>
<!-- End: Page Footer -->
<script type="text/javascript">
    $(function () {
		$('#entity_type_id').val("<?php echo $entity_data->entity_type_id;?>");


        var entity_type_id = "{!! $entity_data->entity_type_id !!}";

                  $('form[name="data_form"]').submit(function(e) {
                      e.preventDefault();
                       setFullName();
                       $(".submit-btn").attr("disabled", true);
                           Common.jsonValidation('<?=Request::url()?>', this,'',"<?php echo $entity_data->identifier ?>");
                        });


         $( document ).on( "change", "#product_id", function() {
                 var item_id = $(this).val();
                 var entity_type_id = $(this).data("type_id");
                 // console.log($(this).val());
                 var selected_item =  $(this).parents('div.bulk_entity_raw').attr('id');
                    $(".submit-btn").attr("disabled","disabled");
                 if(item_id != ""){

                     $.ajax({
                         url: "<?php  echo url('getProductData'); ?>",
                         dataType: "json",
                         data: {"entity_id": item_id, "entity_type_id": entity_type_id},
                         beforeSend: function () {
                         }
                     }).done(function (data) {
                         // console.log(data.length);
                         if(data.length >0){
                             var item = data[0];
                            // $('#'+selected_item).find('#itemWrap').removeClass('hide');
                             if(item.discount_price != "")
                             var price = item.discount_price;
                             else
                             var price = item.retail_price
                             $('#'+selected_item).find('#price').val(price);

                         }

                     });
                 }
                 else{
                     $('#'+selected_item).find('#price').val('');

                 }

             });

        //calculate subtotal and show changes
        $(".update-cart-btn").on("click",function(){

            var form =  setDependForm();
            form['order_id'] = "<?php echo $order_id; ?>"

            $.ajax({
                type: "POST",
                url: "<?php  echo url('getOrderCart'); ?>",
                dataType: "json",
                data: form,
                beforeSend: function () {
                }
            }).done(function (data) {
                 console.log(data.length);
                console.log(data);
               // if(data.length >0){

                   if(data.error == 1){
                       console.log(data.message);
                       showAlert(data.message);
                   } else{

                       showSuccessAlert(data.message);
                       $("#orderTotal").html('');
                       $("#orderTotal").html(data.view);


                       if(data.suggested_truck){

                           truck_selected = data.selected_truck_id;
                           if(data.suggested_truck.length > 0){

                               $('#truck_id').empty();

                               $.each(data.suggested_truck,function(k,v){
                                   // console.log(v.entity_id);
                                   var selected = '';
                                   if(truck_selected == v.entity_id) selected = 'selected="selected"';
                                   $('#truck_id').append("<option value='"+v.entity_id+"'  "+selected+">"+v.title+"</option>");
                                   // $('.blah').val(key); // if you want it to be automatically selected
                                   $('#truck_id').trigger("chosen:updated");
                               });

                               $("#truck_id").select2("val", truck_selected);
                           }
                       }


                        if(data.selected_truck){
                            if(data.selected_truck.length > 0){

                                $("#base_fee").val(data.selected_truck.base_fee);
                                $("#charge_per_minute").val(data.selected_truck.charge_per_minute);
                            }
                        }


                       $(".submit-btn").removeAttr("disabled");
                      // $('#assignVehicleWrap .alert-info').addClass('hide');
                       //$('#assignVehicleWrap').addClass('hide');
                   }

              //  }

            });

        });

        $( document ).on( "blur", "#quantity", function(e) {

            $(".submit-btn").attr("disabled","disabled");
        });

        $( document ).on( "click", ".delete-depend-entity", function(e) {
           // e.preventDefault();
            if($(".bulk_entity_raw").length == 1){
                $(".submit-btn").removeAttr("disabled");
            }
            else{
                $(".submit-btn").attr("disabled","disabled");
            }

        });

        $("#data_form .add-more-entity").on("click",function() {
            $(".submit-btn").attr("disabled","disabled");
        });

        var truck_selected = $('#truck_id').val();

        <?php if(isset($update->attributes->truck_id->id)){ ?>
            truck_selected = parseInt("{!! $update->attributes->truck_id->id !!}");
        <?php } ?>



        console.log(truck_selected);
        if($('#weight').val() != '' && $('#volume').val()){

          $.ajax({
                type : "GET",
                url: "<?php echo url('getTruckList'); ?>",
                dataType: "json",
                data: {"total_weight": $('#weight').val(),"total_volume":$('#volume').val()},
                success: function(data){
                    // alert(data.view);

                    var trucks = data.data;
                    if(trucks.length >0){

                        $('#truck_id').empty();
                        $('#truck_id').append("<option value=''>-- Select Truck --</option>");

                        $.each(trucks,function(k,v){
                           // console.log(v.entity_id);   console.log(truck_selected);
                            var selected = '';
                            if(truck_selected == v.entity_id){
                                selected = 'selected="selected"';
                            }
                            $('#truck_id').append("<option value='"+v.entity_id+"'  "+selected+">"+v.title+"</option>");
                            // $('.blah').val(key); // if you want it to be automatically selected
                            $('#truck_id').trigger("chosen:updated");

                        });

                        $("#truck_id").select2("val", truck_selected);


                    }

                },
            });
        }


        $(document).on("change","#truck_id",function(){

            //if(truck_selected != $(this).val()){
                $('#assignVehicleWrap').removeClass('hide');
              getVehicles($('#truck_id').val(),"{!! $order_id !!}");
          //  }
           // else{
             //   $('#assignVehicleWrap').addClass('hide');
            //}


            console.log($(this).val());
            $.ajax({
                type : "GET",
                url: "<?php echo url('getTruckInfo'); ?>",
                dataType: "json",
                data: {"entity_id": $(this).val()},
                success: function(data){
                    // alert(data.view);

                    var truck = data.data;
                    if(data.data.entity_id){
                        $("#base_fee").val(data.data.base_fee);
                        $("#charge_per_minute").val(data.data.charge_per_minute);

                        //  $('#truckWrap #truck_code').text(truck.vehicle_code);
                        $('#truckWrap #truck_weight').text(truck.min_weight+' '+truck.weight_unit+' - '+truck.max_weight+' '+truck.weight_unit);
                        $('#truckWrap #truck_class').text(truck.truck_class);
                        $('#truckWrap #truck_volume').text(truck.volume+' '+truck.vol_unit);
                    }



                },
            });


        });


        $('.assign-vehicle').on('click',function(){
            getVehicles($('#truck_id').val(),"{!! $order_id !!}","{!! $order_status_keyword !!}")
        });


        $("#professional_id").on("change",function(){

            console.log($(this).val());
            $.ajax({
                type : "GET",
                url: "<?php echo url('getDeliveryProfessional'); ?>",
                dataType: "json",
                data: {"entity_id": $(this).val()},
                success: function(data){
                  // alert(data.view);

                    $("#number_of_labour").val(data.data.number_of_labour);
                    $("#loading_price").val(data.data.price);


                },
            });


        });

       // $(document).on('change', '#truck_id');


        $('.cancel-btn').on("click",function(){
            window.location.reload();
        });

        $('input[name="charge_extra_item"]').on("click",function()
        {
            if ($(this).prop('checked')) {
                $(this).val(1);
            }
            else{
                $(this).val(0);
            }
        });

        $( document ).on( "blur", "input", function(e) {
            $(".submit-btn").attr("disabled","disabled");
        });

    });


    function getVehicles(truck_id,order_id)
    {


        var vehicle_selected = '';
            <?php if(isset($update->attributes->vehicle_id->id)){ ?>
        var vehicle_selected = parseInt("{!! $update->attributes->vehicle_id->id !!}");
        <?php } ?>

        $.ajax({
            type : "GET",
            url: "<?php echo url('available_vehicles'); ?>",
            dataType: "json",
            data: {"truck_id":truck_id, 'order_id': order_id,},
            success: function(data){
                // alert(data.view);

                if(data.data.drivers){

                    if(data.data.drivers.length >0){

                       // $('.vehicle_id_field').removeClass('hide');

                        $('#truck_vehicle').empty();
                        $('#truck_vehicle').append("<option value=''>-- Select Vehicle --</option>");

                        $.each(data.data.drivers,function(k,v){
                            // console.log(v.entity_id);   console.log(truck_selected);
                            var selected = '';
                            if(vehicle_selected == v.entity_id) selected = 'selected="selected"';
                            $('#truck_vehicle').append("<option value='"+v.entity_id+"'  "+selected+">"+v.title+"</option>");
                            // $('.blah').val(key); // if you want it to be automatically selected
                            $('#truck_vehicle').trigger("chosen:updated");

                        });
                       // $("#truck_vehicle").select2("val", vehicle_selected);
                    }
                }

                $('#assignVehicleWrap .alert-info').remove();
                if(data.data.warning != ""){
                    console.log(data.data.warning);
                   var message = ' <div class="alert alert-info col-md-12">'+data.data.warning+'</div>';
                    $('#assignVehicleWrap').prepend(message);
                }


         /*       if(data.data.truck){

                    console.log(data.data.truck);
                    var truck = data.data.truck;
                    $("#base_fee").val(truck.base_fee);
                    $("#charge_per_minute").val(truck.charge_per_minute);

                    //  $('#truckWrap #truck_code').text(truck.vehicle_code);
                    $('#truckWrap #truck_weight').text(truck.min_weight+' '+truck.weight_unit+' - '+truck.max_weight+' '+truck.weight_unit);
                    $('#truckWrap #truck_class').text(truck.truck_class);
                    $('#truckWrap #truck_volume').text(truck.volume+' '+truck.vol_unit);
                }*/


            },
        });
    }

</script>
@if(isset($location) && !empty($location))
<script>
    var lcoation = '{!! $location !!}';

    locationTrack('map',lcoation);

    function locationTrack(div_id,locations) {

        var markers = JSON.parse(locations);

        var orderID = '{!! isset($update->entity_id) ? $update->entity_id : "" !!}';
        var orderNum = '{!! isset($update->attributes->order_number) ? $update->attributes->order_number : "" !!}';

        var orderLatitude = '{!! isset($update->order_pickup[0]->attributes->latitude) ? $update->order_pickup[0]->attributes->latitude : '' !!}';

        var orderLongitude = '{!! isset($update->order_dropoff[0]->attributes->longitude) ? $update->order_dropoff[0]->attributes->longitude : '' !!}';

        var driver = '{{ isset($update->attributes->driver_id->value) ? $update->attributes->driver_id->value : '' }}';

        var mapOptions = {
            center: new google.maps.LatLng(orderLatitude, orderLongitude),
            zoom:10,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        var map = new google.maps.Map(document.getElementById(div_id), mapOptions);
        var infoWindow = new google.maps.InfoWindow();
        var lat_lng = new Array();
        var latlngbounds = new google.maps.LatLngBounds();
        for (i = 0; i < markers.length; i++) {


                var data = markers[i]
                var desc;
                 var total_points = parseInt(markers.length -1);
                if(i == 0)
                    desc = ' (Starting Point)';
                else if(i == total_points)
                    desc = ' (Ending Point)';
                else
                    desc  = ' (Track - ' + i + ')';

                data.description = '<h5>' + driver + '</h5><br>Order #: ' + orderNum + desc;
                var myLatlng = new google.maps.LatLng(data.latitude, data.longitude);
                lat_lng.push(myLatlng);
                var marker = new google.maps.Marker({
                    position: myLatlng,
                    map: map,
                    title: '',
                });
                latlngbounds.extend(marker.position);
                (function (marker, data) {
                    google.maps.event.addListener(marker, "click", function (e) {
                        infoWindow.setContent(data.description);
                        infoWindow.open(map, marker);
                    });
                })(marker, data);


                if(i > 0 && i < total_points){
                    marker.setVisible(false);
                }

            }

        map.setCenter(latlngbounds.getCenter());
        map.fitBounds(latlngbounds);

        //***********ROUTING****************//

        //Intialize the Path Array
        var path = new google.maps.MVCArray();

        //Intialize the Direction Service
        var service = new google.maps.DirectionsService();

        //Set the Path Stroke Color
        var poly = new google.maps.Polyline({map: map, strokeColor: '#4986E7'});

        //Loop and Draw Path Route between the Points on MAP
        for (var i = 0; i < lat_lng.length; i++) {
            if ((i + 1) < lat_lng.length) {
                var src = lat_lng[i];
                var des = lat_lng[i + 1];
                path.push(src);
                poly.setPath(path);
                service.route({
                    origin: src,
                    destination: des,
                    travelMode: google.maps.DirectionsTravelMode.DRIVING
                }, function (result, status) {
                    if (status == google.maps.DirectionsStatus.OK) {
                        for (var i = 0, len = result.routes[0].overview_path.length; i < len; i++) {
                            path.push(result.routes[0].overview_path[i]);
                        }
                    }
                });
            }
        }
    }
</script>
@else
<script>
//Display pickup and droop off lcoation if driver routes is not available
    var pickup_lat = '{!! isset($update->order_pickup[0]->attributes->latitude) ? $update->order_pickup[0]->attributes->latitude : '' !!}';
    var pickup_lng = '{!! isset($update->order_pickup[0]->attributes->longitude) ? $update->order_pickup[0]->attributes->longitude : '' !!}';

    var drop_lat = '{!! isset($update->order_dropoff[0]->attributes->latitude) ? $update->order_dropoff[0]->attributes->latitude : '' !!}';
    var drop_lng = '{!! isset($update->order_dropoff[0]->attributes->longitude) ? $update->order_dropoff[0]->attributes->longitude : '' !!}';

    console.log(pickup_lat); console.log(pickup_lng);
    console.log(drop_lat); console.log(drop_lng);



    function initMap(pickup_lat,pickup_lng,drop_lat,drop_lng){
       /* var pointA = new google.maps.LatLng(51.7519, -1.2578),
            pointB = new google.maps.LatLng(50.8429, -0.1313),*/
        var pointA = new google.maps.LatLng(pickup_lat, pickup_lng),
            pointB = new google.maps.LatLng(drop_lat,drop_lng),
            myOptions = {
                zoom: 7,
                center: pointA
            },
            map = new google.maps.Map(document.getElementById('map'), myOptions),
            // Instantiate a directions service.
            directionsService = new google.maps.DirectionsService,
            directionsDisplay = new google.maps.DirectionsRenderer({
                map: map
            }),
            markerA = new google.maps.Marker({
                position: pointA,
                title: "A",
                label: "A",
                map: map
            }),
            markerB = new google.maps.Marker({
                position: pointB,
                title: "B",
                label: "B",
                map: map
            });

        // get route from A to B
        calculateAndDisplayRoute(directionsService, directionsDisplay, pointA, pointB);

    }



    function calculateAndDisplayRoute(directionsService, directionsDisplay, pointA, pointB) {
        directionsService.route({
            origin: pointA,
            destination: pointB,
            travelMode: 'DRIVING',
        }, function(response, status) {
            if (status == google.maps.DirectionsStatus.OK) {
                directionsDisplay.setDirections(response);
            } else {
                window.alert('Directions request failed due to ' + status);
            }
        });
    }

    initMap(pickup_lat,pickup_lng,drop_lat,drop_lng);

</script>
@endif
@include(config('panel.DIR').'footer')