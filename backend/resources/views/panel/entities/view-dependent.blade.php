@include(config('panel.DIR').'header')
<?php
$fields = "App\Libraries\Fields";
$fields = new $fields();

$entity_helper = "App\Libraries\EntityHelper";
$entity_helper = new $entity_helper();

//echo "<pre>"; print_r( $update);exit;
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
                                $value = $entity_helper->parseAttributeToDisplay($value);

                                //check if date type is date then format
                                if(isset($data_type["$key"]) && $data_type["$key"] == "date"){
                                    $value = date(DATE_FORMAT_ADMIN, strtotime($value));
                                }

                                $column_label = isset($columns["$key"]) ? $columns["$key"] : $key;

                                ?>

                                <?php if($i == 0 || $i%2 == 0){ ?><tr><?php } ?>

                                    <td><h4>{!! $column_label !!}</h4></td>
                                    <td><span>{!! $value !!}</span></td>

                                    <?php if($i%2 != 0){ ?></tr><?php } ?>
                                <?php
                                $i++; }
                                }

                                }
                                ?>
                            </table>


                            <?php if($entity_data->depend_entity_type > 0){

                            if(isset($update->{$dependent_entity_type}) && count($update->{$dependent_entity_type})){
                            // echo "<pre>"; print_r( $update->{$dependent_entity_type});exit;

                            ?>
                            <table width="100%" class="table table-striped mt20">
                                <tbody>
                                <tr>
                                    <?php
                                    $i = 0;
                                    // print_r($dependent_columns);
                                    foreach ($dependent_columns as $key => $column_label) {

                                    if(isset($update->{$dependent_entity_type}[0]->attributes->{$key}))
                                    ?>
                                    <td><h4>{!! $column_label !!}</h4></td>

                                    <?php
                                    $i++;
                                    }
                                    ?></tr>

                                <?php
                                for($j = 0; $j <= count($update->{$dependent_entity_type}); $j++){
                                $item_unit = '';
                                ?>

                                <tr>
                                    <?php
                                    foreach ($dependent_columns as $key => $column_label) {

                                    if(isset($update->{$dependent_entity_type}[$j]->attributes->{$key}))
                                    {

                                    $value = $update->{$dependent_entity_type}[$j]->attributes->{$key};
                                    $value = $entity_helper->parseAttributeToDisplay($value);

                                    //check if date type is date then format
                                    if(isset($data_type["$key"]) && $data_type["$key"] == "date"){
                                        $value = date(DATE_FORMAT_ADMIN, strtotime($value));
                                    }

                                    //Display item unit with weight
                                    if($key == 'item_id'){
                                        if(isset($update->{$dependent_entity_type}[$j]->attributes->item_id->detail->attributes->item_unit)){
                                            $item_unit = $entity_helper->parseAttributeToDisplay($update->{$dependent_entity_type}[$j]->attributes->item_id->detail->attributes->item_unit);
                                        }
                                    }

                                    if($key == 'weight' && !empty($item_unit))
                                        $value .=  ' '.$item_unit;


                                    ?>
                                    <td>{!! $value !!}</td>
                                    <?php  }
                                    } ?> </tr>
                                <?php }
                                ?>

                                </tbody>
                            </table>

                        <?php
                        }
                        }
                        ?>

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