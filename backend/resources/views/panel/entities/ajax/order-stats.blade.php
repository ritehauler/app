<?php


$data = json_decode(json_encode($data));
//echo "<pre>"; print_r($driver); exit;
?>
@if($data)
@if(isset($data->monthly))
    <div class="row d-flex">
        <div class="col-md-6 d-flex">
            <div class="panel panel-theme top orderStatsWrap fluid-width">
                <div class="panel-heading box-heading">
                    <span class="panel-title titleColorWhite pl15 pr15">Total Statistics</span>
                </div>
                <div class="panel-body pn">
                    <table width="100%" class="table">
                        <tbody>
                        <tr>
                            <td>Total Order</td>
                            <td><b>{!! $data->monthly->total_order !!}</b></td>
                        </tr>
                        <tr>
                            <td>Completed Order</td>
                            <td><b>{!! $data->monthly->completed_order !!}</b></td>
                        </tr>
                        <tr>
                            <td>Cancelled Order</td>
                            <td><b>{!! $data->monthly->cancelled_order !!}</b></td>
                        </tr>
                        <tr>
                            <td>Earned Order</td>
                            <td><b>${!! $data->monthly->total_earned !!}</b></td>
                        </tr>
                       {{-- <tr>
                            <td>Rating</td>
                            <td><b>{!! $data->monthly->rating !!}</b></td>
                        </tr>--}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6 d-flex">
            <div class="panel panel-theme top orderStatsWrap fluid-width">
                <div class="panel-heading box-heading">
                    <span class="panel-title titleColorWhite pl15 pr15">Total Statistics</span>
                </div>
                <div class="panel-body pn">
                    <table width="100%" class="table">
                        <tbody>
                        <tr>
                            <td>Average Rating</td>
                            <td><b>{!! $driver->ext_average_rating !!}</b></td>
                        </tr>
                        <tr>
                            <td>Total Raters</td>
                            <td><b>{!! $driver->ext_total_raters !!}</b></td>
                        </tr>
                        <tr>
                            <td>{!! $driver->joining_key !!}</td>
                            <td><b>{!! $driver->joining_value !!}</b></td>
                        </tr>

                        @if(count($driver->review_options))
                            @foreach($driver->review_options as $key => $review_option)
                                <tr>
                                    <td>{!! $review_option->option !!}</td>
                                    <td><b>{!! $review_option->count !!}</b></td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endif
@if($identifier == 'driver')
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-theme top orderStatsWrap fluid-width">
            <div class="panel-heading box-heading">
                <span class="panel-title titleColorWhite pl15 pr15">Last Week Statistics</span>
            </div>
            <div class="panel-body pn">
                <table width="100%" class="table">
                    <thead>
                    <tr>
                        <td>&nbsp;</td>
                        <td>Total</td>
                        <td>Completed</td>
                        <td>Cancelled</td>
                        <td>Earned</td>
                        <td>Rating</td>
                    </tr>
                    </thead>
                    <tbody>
                    @if(isset($data->weekly))
                        @foreach($data->weekly as $key => $day)
                            <tr>
                                <td>{!! ucwords($key) !!}</td>
                                <td>{!! $day->total_order !!}</td>
                                <td>{!! $day->completed_order !!}</td>
                                <td>{!! $day->cancelled_order !!}</td>
                                <td>${!! $day->total_earned !!}</td>
                                <td>{!! $day->rating !!}</td>
                            </tr>
                        @endforeach
                    @else
                        <td colspan="6"> No Record Found</td>
                    @endif
                    </tbody>
                </table>
                </table>
            </div>
        </div>
    </div>

</div>
@endif
    @else
    <div>No Order Yet</div>
    @endif

