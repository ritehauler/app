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
        <div class="tray tray-center p25 va-t posr">
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
                                if (isset($update->attributes)) {

                                    $i = 0;

                                    foreach ($columns as $key => $column_label) {

                                if(isset($update->attributes->{$key}))
                                {
                                    $value = $update->attributes->{$key};
                                    $value = $entity_helper->parseAttributeToDisplay($value,$entity_fields[$key]);

                                    //check if date type is date then format
                                    if(isset($data_type["$key"]) && $data_type["$key"] == "date"){
                                        $value = date(DATE_FORMAT_ADMIN, strtotime($value));
                                    }


                                    $column_label = isset($columns["$key"]) ? $columns["$key"] : $key;

                                    ?>

                                    <?php
                                        if($i == 3){
                                        if(isset($update->auth->email)){ ?>
                                    <tr>
                                        <td><h4>Email</h4></td>
                                        <td><span><?php echo $update->auth->email ?></span></td>
                                    </tr>
                                    <tr>
                                        <td><h4>Contact #</h4></td>
                                        <td><span><?php echo $update->auth->mobile_no ?></span></td>
                                    </tr>
                                        <?php }
                                        } ?>


                                    <?php // if($i == 0 || $i%2 == 0){ ?><tr><?php // } ?>

                                        <td><h4><?php echo $column_label ?></h4></td>
                                        <td><span><?php echo $value ?></span></td>

                                        <?php //if($i%2 != 0){ ?></tr><?php //} ?>
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