@include(config('panel.DIR').'header')
<?php
$fields = "App\Libraries\Fields";
$fields = new $fields();
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
                        <div class="tab-content pn br-n admin-form">
                            @include(config('panel.DIR').'flash_message')
                            @if (Session::has('message'))
                                <div class="alert alert-info">{{ Session::get('message') }}</div>
                            @endif
                            <div class="alert-message"></div>
                            <div>

                                <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
                                <input type="hidden" name="do_post" value="1" />
                                <?php
                                if (isset($records[0])) {

                                    foreach ($records as $record) {
                                        if($record->element_type=='text') $record->element_type='input';
                                        echo $fields->randerInput($record, $update->data->role,true,array('uri_method' => $uri_method));
                                    }
                                }
                                ?>


                            </div>

                            <div class="pull-right p-relative">
                                @if($uri_method != 'view' )
                                    <button type="submit" class="btn ladda-button btn-theme btn-wide mt10" data-style="zoom-in"> <span class="ladda-label">Submit</span> </button>
                                    @include(config('panel.DIR').'entities.loader')
                                @else
                                    <a href="../update/{{ $update->data->role->role_id }}" type="submit" class="btn ladda-button btn-theme btn-wide mt10" data-style="zoom-in"> <span class="ladda-label">Edit Record</span> </a>
                                @endif
                            </div>
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
        // default form submit/validate
        $('form[name="data_form"]').submit(function (e) {
            e.preventDefault();
            // hide all errors
            $("div[id^=error_msg_]").removeClass("show").addClass("hide");
            // validate form
            Common.jsonValidation('<?=Request::url()?>', this,'',"group");
        });

        <?php if(isset($update->data->role)){ ?>
        //   $("#entity_type_id").attr("disabled");
        //$("#entity_type_id").prop("disabled", true);
       // $('#div_entity_type_id').addClass('hide');
        <?php } ?>

        if($('#div_parent_id').length > 0){
            $('#div_parent_id').hide();
        }
    });
</script> 
@include(config('panel.DIR').'footer')