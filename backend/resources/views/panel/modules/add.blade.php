@include(config('panel.DIR').'header')
<?php
$fields = "App\Libraries\Fields";
$fields = new $fields();
?>
<!-- Begin: Content -->
<section id="content_wrapper" class="content">
    <section id="content" class="pn">
        <div class="col-md-8 col-md-offset-2 p30">
            <div class="row">
                <div class="col-md-12 mt0 mb25">
                    <h3>{!! $page_action !!} {!! $module !!}</h3>
                </div>
            </div>
 
            <form  name="data_form" method="post">
                <div class="main admin-form ">                 
                    @include(config('panel.DIR').'flash_message')
                    <div class="row">
                        <?php
                        if (isset($records[0])) {
                            foreach ($records as $record) { 
							 echo $fields->randerInput($record); 
                            }
                        }
                        ?>
                    </div>	

                    <div class="pull-right">
                        <button type="submit" class="btn ladda-button btn-theme btn-wide" data-style="zoom-in"> <span class="ladda-label">Submit</span> </button>
                    </div>
                </div>
                <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
                <input type="hidden" name="do_post" value="1" />
            </form>
        </div>
    </section>
    <!-- End: Content -->
    <!-- Begin: Page Footer -->
    @include(config('panel.DIR') . 'footer_bottom')
</section>
<!-- End: Page Footer -->
<script type="text/javascript">
    $(function () {
        // default form submit/validate
        $('form[name="data_form"]').submit(function (e) {
            e.preventDefault();
            // hide all errors
            $("div[id^=error_msg_]").removeClass("show").addClass("hide");
            // validate form
            return jsonValidate('<?=Request::url()?>', $(this));
        });

    });

</script> 
@include(config('panel.DIR').'footer')