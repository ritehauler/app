
@include($p_dir.'header')

        <!-- Reminder Content -->
<div class="content overflow-hidden">
    <div class="row">
        <div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">

            <!-- Reminder Block -->
            <div class="block block-themed animated fadeIn">
                <div class="block-header bg-success">
                    <h3 class="block-title">{!! $page_action !!}</h3>
                    <h4 class="block-content text-center">{!! $data["content"] !!}</h4>
                </div>
            </div>
            <!-- END Reminder Block -->
        </div>
    </div>
</div>
<script type="application/javascript">
    // disable alerts
    //window.alert = null;
    //var alert = null;



    {{--$(document).ready(function () {--}}
        {{--@if($detect->isiOS())--}}
        {{--$.ajax({--}}
            {{--type: "GET",--}}
            {{--url: "{!! $schema !!}",--}}
            {{--data: null,--}}
            {{--complete: function (e, xhr, settings) {--}}
                {{--if (e.status === 200) {--}}
                    {{--alert('{!! $schema !!}');--}}
                    {{--window.location = '{!! $schema !!}';--}}
                {{--} else {--}}
                    {{--//alert("error : " + e.statusText);--}}
                    {{--//window.location = '{!! $appstore_url2 !!}';--}}
                {{--}--}}
            {{--}--}}
        {{--});--}}
        {{--@endif--}}

        {{--@if($detect->isAndroidOS())--}}
        {{--$.ajax({--}}
            {{--type: "GET",--}}
            {{--url: "{!! $schema !!}",--}}
            {{--data: null,--}}
            {{--complete: function (e, xhr, settings) {--}}
                {{--if (e.status === 200) {--}}
                    {{--alert('{!! $schema !!}');--}}
                    {{--window.location = '{!! $schema !!}';--}}
                {{--} else {--}}
                    {{--//alert("error : " + e.statusText);--}}
                    {{--//window.location = '{!! $playstore_url2 !!}';--}}
                {{--}--}}
            {{--}--}}
        {{--});--}}
        {{--@endif--}}
    {{--});--}}



</script>
<!-- END Reminder Footer -->
@include($p_dir.'footer')