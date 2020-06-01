<?php
$fields = "App\Libraries\Fields";
$fields = new $fields();
?>
<!-- Begin: Content -->

        <div class="panel panel-theme panel-border top mb25">
			<div class="panel-heading">
				<span class="panel-title">{!! $page_action !!} {!! $module !!}</span>
			</div>

            <div class="panel-body p20 pb10">
                <form  name="data_form" method="post" id="data_form" >
                    <div class="main admin-form ">
                        @include(config('panel.DIR').'flash_message')
                        <div class="alert-message"></div>
                            <div class="row entity_wrap" id="entity_data">

                                <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
                                <input type="hidden" name="do_post" value="1" />
                                <?php
                                if (isset($records[0])) {
                                    foreach ($records as $record) {
                                        if($record->element_type=='text') $record->element_type='input';
                                        echo $fields->randerInput($record);
                                    }
                                }
                                ?>

                            </div>

                        <div class="pull-right p-relative">
                            <button type="submit" class="btn ladda-button btn-theme btn-wide" data-style="zoom-in"> <span class="ladda-label">Submit</span> </button>
                            @include(config('panel.DIR').'entities.loader')
                    </div>
                    </div>
                </form>
                </div>
		</div>


    <!-- End: Content -->
    <!-- Begin: Page Footer -->

<!-- End: Page Footer -->

