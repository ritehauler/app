@include(config('panel.DIR').'header')
<?php
$fields = "App\Libraries\Fields";
$fields = new $fields();
?>
        <!-- Begin: Content -->
<section id="content_wrapper" class="content">
    <iframe class="embed-responsive-item" src="{!! $kanban_call_url.$crypt_session_id !!}" height="660px" width="100%"></iframe>
    <section id="content" class="hidden">

        <!-- begin: .tray-center -->
        <div class="tray tray-center p25 va-t posr">

            <!-- create new order panel -->
            <div class="panel panel-theme panel-border top mb25">
                <div class="panel-heading">
                    <span class="panel-title">{!! $module !!}</span>
                </div>

                <div class="panel-body p20 pb10">
                    <form  name="data_form" method="post" id="data_form" class="panel-collapse collapse in">
                        <div class="tab-content pn br-n admin-form">
                            @include(config('panel.DIR').'flash_message')

                                    <!-- end section row section -->
                        </div>
                    </form>
                </div>
            </div>

        </div>  <!-- end tray -->

    </section>
    <!-- End: Content -->
    <!-- Begin: Page Footer
    @include(config('panel.DIR') . 'footer_bottom') -->
</section>
<!-- End: Page Footer -->

@include(config('panel.DIR').'footer')