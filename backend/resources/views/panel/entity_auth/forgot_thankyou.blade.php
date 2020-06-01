
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
<!-- END Reminder Footer -->
@include($p_dir.'footer')