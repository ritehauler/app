
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Calendar</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{!! URL::to(config('panel.DIR_PANEL_RESOURCE').'calendar/lib/font-awesome-4.7.0/css/font-awesome.min.css') !!}">
    <!-- Bootstrap core CSS -->
    <link href="{!! URL::to(config('panel.DIR_PANEL_RESOURCE').'calendar/lib/bootstrap-md/css/bootstrap.min.css') !!}" rel="stylesheet">
    <!-- Material Design Bootstrap -->
    <link href="{!! URL::to(config('panel.DIR_PANEL_RESOURCE').'calendar/lib/bootstrap-md/css/mdb.min.css') !!}" rel="stylesheet">
    <!--Air Datepicker JS-->
    <link href="{!! URL::to(config('panel.DIR_PANEL_RESOURCE').'calendar/lib/air-datepicker/css/datepicker.css') !!}" rel="stylesheet" type="text/css"/>
    <link href="{!! URL::to(config('panel.DIR_PANEL_RESOURCE').'calendar/css/scroll.css') !!}" rel="stylesheet">
    <link href="{!! URL::to(config('panel.DIR_PANEL_RESOURCE').'calendar/css/material-input.css') !!}" rel="stylesheet">
    <link href="{!! URL::to(config('panel.DIR_PANEL_RESOURCE').'calendar/css/datepicker-custom.css') !!}" rel="stylesheet">
    <!-- Custom styles -->
    <link href="{!! URL::to(config('panel.DIR_PANEL_RESOURCE').'calendar/css/styles.css') !!}" rel="stylesheet">
</head>
<body class="body">

<header class="">
    <div class="container-fluid">
        <div class="row align-items-center">
        <div class="col-md-2">
            <!-- <button type="button" class="btn btn-light-blue btn-round"><i class="fa fa-bars f-16"></i></button> -->
            <img src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE').'calendar/img/calendar.png') !!}" class="image-fluid" width="35px"/>
            <b class="f-18 c-gray-c d-inline-block">Calender</b>
        </div><!--End col-md-2-->
        <div class="col-md-5">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
           <a href="{!! URL::to($panel_path.'dashboard') !!}" class="btn btn-primary btn-orange btn-sm">Back To Panel</a>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <a href="#" class="dayNext" onClick="nextPreviousTableDate('previous')"><i class="fa fa-chevron-left"></i></a>
            <a href="#" class="dayPrevious" onClick="nextPreviousTableDate('next')"><i class="fa fa-chevron-right"></i></a>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <b class="f-22 c-gray-c d-inline-block" id="month">October 2018</b>
        </div><!--End col-md-5-->
        <div class="col-md-5 text-right">

            <div class="d-inline-block">
                <select class="form-control" name="driver_id" id="driver_id">
                    <option value="all">All Driver</option>
                    @foreach($drivers as $driver)
                        <option value="{!! $driver->entity_id !!}">{!! $driver->full_name !!}</option>
                    @endforeach
                </select>
            </div>

         {{--   <div class="dropdown d-inline">
                <button class="btn btn-primary btn-orange btn-round" type="button" id="dropdownMenu3" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-cog f-16"></i>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenu3">
                    <h6 class="dropdown-header">Setting</h6>
                    <a class="dropdown-item" href="#">Trash</a>
                    <hr/>
                    <a class="dropdown-item" href="#">Density and color</a>
                    <a class="dropdown-item" href="#">print</a>
                </div>
            </div>--}}

        </div><!--End col-md-5-->
    </div><!--End Row-->
    </div>
</header>

<section>
    <table border="0" class="table">
        <tr>
            <td width="235px">
                <nav class="sideLeftNav">
                    <div class="datepicker-here" id="fixed-dp" data-language='en'></div>

                </nav>
            </td>
            <td class="heightStyle">
                <table class="table table-bordered table-week-header m-0">
                    <tr class="table-calender-day" id="dateTableHeading">

                    </tr>
                </table>
                <div class="scrollbar table-day-container" id="style-4">
                    <input type="hidden" name="start_date" id="start_date" value="" />
                    <input type="hidden" name="end_date" id="end_date" value="" />
                    <table class="table table-bordered table-calender m-0" id="calendarWrap">
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


                                <td data-date="{!! $dates[0] !!}" width="13.714%"><a href="#" class="setEventModalDay modal-current-position"></a></td>
                                <td data-date="{!! $dates[1] !!}" width="13.714%"><a href="#" class="setEventModalDay modal-current-position"></a></td>
                                <td data-date="{!! $dates[2] !!}" width="13.714%"><a href="#" class="setEventModalDay modal-current-position"></a></td>
                                <td data-date="{!! $dates[3] !!}" width="13.714%"><a href="#" class="setEventModalDay modal-current-position"></a></td>
                                <td data-date="{!! $dates[4] !!}" width="13.714%"><a href="#" class="setEventModalDay modal-current-position"></a></td>
                                <td data-date="{!! $dates[5] !!}" width="13.714%"><a href="#" class="setEventModalDay modal-current-position"></a></td>
                                <td data-date="{!! $dates[6] !!}" width="13.714%"><a href="#" class="setEventModalDay modal-current-position"></a></td>
                            </tr>
                        @endfor

                        </tbody>
                    </table>
                </div>
            </td>
        </tr>
    </table>
</section>


<div class="setEventCustomModalContainer">
    <div class="setEventCustomModal">
        <div class="setEventCustomModalHeader">
           <b class="modal-heading">Order Details</b>
            <a href="#" class="closeModal float-right"><i class="fa fa-times"></i></a>
        </div>
        <div id="calendar-content"></div>

    </div>
</div>

<!-- SCRIPTS -->
<!-- JQuery -->
<script type="text/javascript" src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE').'calendar/lib/bootstrap-md/js/jquery-3.3.1.min.js') !!}"></script>
<!-- Bootstrap tooltips -->
<script type="text/javascript" src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE').'calendar/lib/bootstrap-md/js/popper.min.js') !!}"></script>
<!-- Bootstrap core JavaScript -->
<script type="text/javascript" src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE').'calendar/lib/bootstrap-md/js/bootstrap.min.js') !!}"></script>
<!-- MDB core JavaScript -->
<script type="text/javascript" src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE').'calendar/lib/bootstrap-md/js/mdb.min.js') !!}"></script>
<script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE').'calendar/lib/jquery-ui.js') !!}"></script>
<!--Air Datepicker JS-->
<script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE').'calendar/lib/air-datepicker/js/datepicker.js') !!}" type="text/javascript"></script>
<!-- Include English language -->
<script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE').'calendar/lib/air-datepicker/js/i18n/datepicker.en.js') !!}"></script>

<script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE').'calendar/js/custom.js') !!}"></script>
<script>



    function getBusyOrderTime(driver_id) {

        $.ajax({
            url: "<?php echo url('order_calendar'); ?>",
            dataType: "json",
            data: {"driver_id": driver_id,"start_date": '',"end_date": $('#end_date').val() },
            beforeSend: function () {
                // $('#' + chosen_id).empty();
            }
        }).done(function (data) {
            if(data.error == 0){

                $('#calendarWrap').html('');
                $('#calendarWrap').html(data.data.html);
            }
        });

    }


    $(document).on('click','.setEventModalDay',function(){
       // console.log($(this).data('date'));
      //  console.log($(this).data('start-time'));
       // console.log($(this).data('end-time'));
       // console.log($(this).text());

        if($(this).text() != '') {
            $('.setEventCustomModalContainer').css({'display': 'block'});
            $('#calendar-content').html('');
            $('#calendar-content').html('Loading...');
            $.ajax({
                url: "<?php echo url('order_calendar_content'); ?>",
                dataType: "json",
                data: {
                    "driver_id": $('#driver_id').val(),
                    "start_time": $(this).data('start-time'),
                    "end_time": $(this).data('end-time'),
                    "pickup_date": $(this).data('date')
                },
                beforeSend: function () {
                    // $('#' + chosen_id).empty();
                }
            }).done(function (data) {
                if (data.error == 0) {
                    $('#calendar-content').html('');
                    $('#calendar-content').html(data.data.html);
                }
            });

        }

    });

</script>
</body>
</html>
