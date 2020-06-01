@include(config('panel.DIR').'header')
<?php
$fields = "App\Libraries\Fields";
$fields = new $fields();

 $entity_helper = "App\Libraries\EntityHelper";
        $entity_helper = new $entity_helper();
?>
        <!-- Begin: Content -->
<section id="content_wrapper" class="content">
    <section id="content" >

        <!-- begin: .tray-center -->
        <div class="tray tray-center va-t posr">

            <!-- create new order panel -->
            <div class="panel panel-theme panel-border top mb25">
                <div class="panel-heading">
                    <span class="panel-title">{!! $page_action !!} {!! isset($entity_data->title) ? $entity_data->title : $module !!}</span>
                </div>
                <div class="panel-body p20 pb10">
                    <form  name="data_form" method="post" id="data_form" class="panel-collapse collapse in">
                        <div class="tab-content pn br-n admin-form">
                            @include(config('panel.DIR').'flash_message')
                            <table class="view_popup_table">
                                <?php
                                if (isset($update)) {

                                    $i = 0;

                                foreach ($records as $key => $record) {


                                if(isset($update->{$record->name}))
                                {
                                    if($record->name == 'role_id') continue;
                                    $value = $update->{$record->name};
                                     $column_label = $record->description;

                                    ?>

                                 <tr>

                                        <td><h4><?php echo $column_label ?></h4></td>
                                        <td><span><?php echo $value ?></span></td>

                                       </tr>
                                    <?php
                                    $i++; }
                                        }
                                }
                                ?>
                            </table>

                                    <!-- end section row section -->
                        </div>
                    </form>
                </div>
            </div>

        </div>  <!-- end tray -->

    </section>
    <!-- End: Content -->
    <!-- Begin: Page Footer -->
    @include(config('panel.DIR') . 'footer_bottom')
</section>
<!-- End: Page Footer -->

@include(config('panel.DIR').'footer')