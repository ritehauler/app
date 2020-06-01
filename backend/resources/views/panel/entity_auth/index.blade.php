@include(config('panel.DIR').'header')
<div id="page-container" class="sidebar-l sidebar-o side-scroll header-navbar-fixed">


    <!-- Main Container -->
    <main id="main-container">
        <!-- Page Header -->
        <div class="content overflow-hidden bg-gray-lighter" style="">
            <div class="push-15">
                <h1 class="h2 animated zoomIn">Dashboard</h1>
                <h2 class="h5 animated zoomIn">Welcome </h2>
            </div>
        </div>
        <!-- END Page Header -->



        <section id="content_wrapper" class="content">
            <section id="content" class="text-center" style="display: flex;align-items: center;justify-content: center;height: 90vh;">

                <!-- Session Messages -->
                @include(config('panel.DIR').'flash_message')
                <table cellspacing="0" cellpadding="0" width="100%">
                    <tbody>
                    <tr>
                        <td class="mb15 text-center">
                            <img src="{!! \URL::to("resources/assets/".config("panel.DIR")."assets/img/logos/admin-logo.png") !!}" class="img-responsive " style="margin: 0 auto; width:25%;">
                        </td>
                    </tr>
                    <tr>
                        <td class="header-lg mb15">
                            Welcome To <br> Rite Hauler AdminPanel
                        </td>
                    </tr>
                    <tr>
                        <td class="free-text">
                            Widgets/Analytics coming soon
                        </td>
                    </tr>
                    </tbody>
                </table>






            </section>
        </section>

        <!-- Page Content -->
        <div class="content">

            <div class="row" id="tiles_wrapper">
                <!-- Widget tiles -->
                @if(isset($widget["tiles"][0]))
                    {{--*/ //parse if widgets assign  /*--}}
                    @foreach($widget["tiles"] as $raw_widget)
                        <div class="col-lg-4" id="widget_{!! $raw_widget->admin_widget_id !!}">
                            <i class="fa fa-2x fa-asterisk fa-spin text-success pull-right"></i>
                        </div>
                        <!--<div class="col-lg-4" id="widget_{!! $raw_widget->admin_widget_id !!}"></div>-->
                        @endforeach
                        @endif

                                <!-- line charts -->
                        @if(isset($widget["line_charts"][0]))
                            {{--*/ //parse if widgets assign  /*--}}
                            @foreach($widget["line_charts"] as $raw_widget)
                                <div class="col-lg-6" id="widget_{!! $raw_widget->admin_widget_id !!}"><i
                                            class="fa fa-2x fa-asterisk fa-spin text-success pull-right"></i></div>
                                <!--<div class="col-lg-4" id="widget_{!! $raw_widget->admin_widget_id !!}"></div>-->
                                @endforeach
                                @endif

                                        <!-- line charts -->
                                @if(isset($widget["bar_charts"][0]))
                                    {{--*/ //parse if widgets assign  /*--}}
                                    @foreach($widget["bar_charts"] as $raw_widget)
                                        <div class="col-lg-6" id="widget_{!! $raw_widget->admin_widget_id !!}"><i
                                                    class="fa fa-2x fa-asterisk fa-spin text-success pull-right"></i>
                                        </div>
                                        <!--<div class="col-lg-4" id="widget_{!! $raw_widget->admin_widget_id !!}"></div>-->
                                        @endforeach
                                        @endif

                                                <!-- Widget Pie charts -->
                                        @if(isset($widget["pie_charts"][0]))
                                            {{--*/ //parse if widgets assign  /*--}}
                                            @foreach($widget["pie_charts"] as $raw_widget)
                                                <div class="col-lg-6" id="widget_{!! $raw_widget->admin_widget_id !!}">
                                                    <i class="fa fa-2x fa-asterisk fa-spin text-success pull-right"></i>
                                                </div>
                                                <!--<div class="col-lg-4" id="widget_{!! $raw_widget->admin_widget_id !!}"></div>-->
                                                @endforeach
                                                @endif

                                                        <!-- Widget donut charts -->
                                                @if(isset($widget["donut_charts"][0]))
                                                    {{--*/ //parse if widgets assign  /*--}}
                                                    @foreach($widget["donut_charts"] as $raw_widget)
                                                        <div class="col-lg-6" id="widget_{!! $raw_widget->admin_widget_id !!}"><i
                                                                    class="fa fa-2x fa-asterisk fa-spin text-success pull-right"></i>
                                                        </div>
                                                        <!--<div class="col-lg-4" id="widget_{!! $raw_widget->admin_widget_id !!}"></div>-->
                                                        @endforeach
                                                        @endif

                                                                <!-- Widget flot charts -->
                                                        @if(isset($widget["flot_charts"][0]))
                                                            {{--*/ //parse if widgets assign  /*--}}
                                                            @foreach($widget["flot_charts"] as $raw_widget)
                                                                <div class="col-lg-6" id="widget_{!! $raw_widget->admin_widget_id !!}"><i
                                                                            class="fa fa-2x fa-asterisk fa-spin text-success pull-right"></i>
                                                                </div>
                                                                <!--<div class="col-lg-4" id="widget_{!! $raw_widget->admin_widget_id !!}"></div>-->
                                                                @endforeach
                                                                @endif

                                                                        <!-- Widget map charts -->
                                                                @if(isset($widget["map_charts"][0]))
                                                                    {{--*/ //parse if widgets assign  /*--}}
                                                                    @foreach($widget["map_charts"] as $raw_widget)
                                                                        <div class="col-lg-6"  id="widget_{!! $raw_widget->admin_widget_id !!}">
                                                                            <i class="fa fa-2x fa-asterisk fa-spin text-success pull-right"></i>
                                                                        </div>
                                                                        <!--<div class="col-lg-4" id="widget_{!! $raw_widget->admin_widget_id !!}"></div>-->
                                                                    @endforeach
                                                                @endif

                                                                <div class="col-lg-8">
                                                                </div>
                                                                <div class="col-lg-4">
                                                                    <!-- Latest Sales Widget -->
                                                                    <!-- END Latest Sales Widget -->
                                                                </div>
            </div>
        </div>
        <!-- END Page Content -->
    </main>
    <!-- END Main Container -->

    <?php /*
<script type='text/javascript' >

        var $chartBarsCon = jQuery('.js-chartjs-bars')[0].getContext('2d');
        var $chartDonutCon = jQuery('.js-chartjs-donut')[0].getContext('2d');
        // Set global chart options
        var $globalOptions = {
        scaleFontFamily: "'Open Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif",
                scaleFontColor: '#999',
                scaleFontStyle: '600',
                tooltipTitleFontFamily: "'Open Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif",
                tooltipCornerRadius: 3,
                maintainAspectRatio: false,
                showTooltips: false,
                tooltipEvents: ["mousemove", "touchstart", "touchmove"],
                responsive: true
        };
        // Polar/Pie/Donut Data
        var $chartPolarPieDonutData = {!! $top_games !!}

        var $chartBarsData = {
        labels: [<?php
                            $day = '';
                            foreach ($days_lables as $lab) {
                                $day .="'$lab',";
                            } echo $day;
                            ?>],
                datasets: [{
                label: 'This Week',
                        fillColor: 'rgba(171, 227, 125, .3)',
                        strokeColor: 'rgba(171, 227, 125, 1)',
                        pointColor: 'rgba(171, 227, 125, 1)',
                        pointStrokeColor: '#fff',
                        pointHighlightFill: '#fff',
                        pointHighlightStroke: 'rgba(171, 227, 125, 1)',
                        data: [<?php
                            $total = implode(',', $user_counter);
                            echo $total;
                            ?>]
                }
                ]
        };
        // Init Charts
        $chartDonut = new Chart($chartDonutCon).Doughnut($chartPolarPieDonutData, $globalOptions);
        $chartBars = new Chart($chartBarsCon).Bar($chartBarsData, $globalOptions);
    </script>
	*/
    ?>

    <style type="text/css">
        #chartjs-tooltip-line #chartjs-tooltip-line {
            opacity: 0;
            position: absolute;
            background: rgba(0, 0, 0, .7);
            color: white;
            padding: 3px;
            border-radius: 3px;
            -webkit-transition: all .1s ease;
            transition: all .1s ease;
            pointer-events: none;
            -webkit-transform: translate(-50%, 0);
            transform: translate(-50%, 0);
        }
    </style>


    <div id="container" style="min-width: 310px; max-width: 600px"></div>
    <!-- Page JS Plugins -->
    <script src="{!! URL::to(config('constants.ADMIN_JS_URL')) !!}/plugins/sparkline/jquery.sparkline.min.js"></script>
    <script src="{!! URL::to(config('constants.ADMIN_JS_URL')) !!}/plugins/easy-pie-chart/jquery.easypiechart.min.js"></script>
    <script src="{!! URL::to(config('constants.ADMIN_JS_URL')) !!}/plugins/chartjs/Chart.min.js"></script>
    <script src="{!! URL::to(config('constants.ADMIN_JS_URL')) !!}/plugins/flot/jquery.flot.min.js"></script>
    <script src="{!! URL::to(config('constants.ADMIN_JS_URL')) !!}/plugins/flot/jquery.flot.pie.min.js"></script>
    <script src="{!! URL::to(config('constants.ADMIN_JS_URL')) !!}/plugins/flot/jquery.flot.stack.min.js"></script>
    <script src="{!! URL::to(config('constants.ADMIN_JS_URL')) !!}/plugins/flot/jquery.flot.resize.min.js"></script>
    <script src="{!! URL::to(config('constants.ADMIN_JS_URL')) !!}/plugins/highmap/highmaps.js"></script>
    <script src="{!! URL::to(config('constants.ADMIN_JS_URL')) !!}/plugins/highmap/data.js"></script>
    <script src="{!! URL::to(config('constants.ADMIN_JS_URL')) !!}/plugins/highmap/exporting.js"></script>
    <script src="{!! URL::to(config('constants.ADMIN_JS_URL')) !!}/plugins/highmap/world.js"></script>
    <script src="{!! URL::to(config('constants.ADMIN_JS_URL')) !!}/plugins/highmap/world.js"></script>

    <script type="text/javascript">


        $(function () {
            <?php if(isset($widget["all"][0])):  ?>
                <?php foreach($widget["all"] as $raw_widget): ?>
                    <?php $widget = $admin_widget_model->get($raw_widget->admin_widget_id); ?>
                    jsonValidate('<?php echo url(DIR_BACKEND); ?>/parse_widget/<?php echo $widget->type; ?>/<?php echo $widget->identifier; ?>?_token=<?php echo csrf_token(); ?>');
            <?php endforeach; ?>
            <?php endif; ?>
        });
    </script>
@include(config('panel.DIR') . 'footer_bottom')
@include(config('panel.DIR').'footer')