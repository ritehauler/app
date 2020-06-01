<input type="hidden" id="depend_entity_exist" name="depend_entity_exist" value="1"/>

<div class="bulk_entity_wrap">
        <div class="section-divider mb30 mt15" id="spy1">
            <span>Order Item</span>
        </div>
    <?php
    $di = 0;
    if (isset($depend_entity_records[0]) && isset($depend_update[0])) {

    $ii = 0;
    foreach ($depend_update as $depend_update_item) {
    ?>

    <div class="row bulk_entity_raw mb20" id="bulk_entity_raw_{!! $ii !!}">
        <?php if($uri_method != "view"){ ?>
        <a style="float:right" class="fa fa-times delete-depend-entity delete-depend-element" id="delete-depend-entity-<?php echo $ii; ?>" href="javascript:void(0);"></a>
            <?php } ?>

        <input type="hidden" name="do_post" value="1"/>
        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
        <input type="hidden" name="entity_type_id" id="entity_type_id"
               value="<?php echo $depend_update_item->entity_type_id; ?>"/>
        <input type="hidden" name="entity_id" id="entity_id" value="<?php echo $depend_update_item->entity_id; ?>" />
        <?php

        /*add hidden fields*/
        if (isset($depend_entity_hidden_records[0])) {
            foreach ($depend_entity_hidden_records as $hidden_record) {
                if ($hidden_record->is_entity_column == 1) {
                    echo $fields->randerEntityFields($hidden_record, $depend_update_item, $depend_entity_type_data->entity_type_id,true,array('uri_method'=>$uri_method));
                } else {
                    echo $fields->randerFields($hidden_record, $depend_update_item, $depend_entity_type_data->entity_type_id,true,array('uri_method'=>$uri_method));
                }
            }
        }


        foreach ($depend_entity_records as $key =>$record) {

        if ($record->is_entity_column == 1) {
            $field_class = $record->attribute_code . '_field';

        } else {
            $field_class = $record->name . '_field';
        }

        //Check if column has to show / hide
        $depend_hide = $fields->showHideColumn($record->view_at,true);

        //when column is odd
        /*if($key % 2 == 0){ */?><!--
                                                         <div class="row mbn">--><?php /*} */ ?>


        <div class="section mb10 col-md-4 {!! $field_class.' '.$depend_hide !!}">
            <?php
            if (isset($record->element_type) && $record->element_type == 'text') {
                $record->element_type = 'input';
            }
            if ($record->is_entity_column == 1) {

                $more_option = array();
                if($di > 0){
                    $more_option = array(
                            'hide_label' => false,
                    );
                }

                echo $fields->randerEntityFields($record, $depend_update_item, $depend_entity_type_data->entity_type_id,true,array('uri_method'=>$uri_method));
            } else {
                echo $fields->randerFields($record, $depend_update_item, $depend_entity_type_data->entity_type_id,true,array('uri_method'=>$uri_method));
            }


            ?> </div>

        <?php
        //when column is even
        /* if($key%2 > 0){*/?><!-- </div>--><?php /*} */
        ?>
        <?php  unset($hide);  } ?>

        <?php

 /*       if(isset( $depend_update_item->attributes->product_id->detail->attributes)){

        $product_attributes = $depend_update_item->attributes->product_id->detail->attributes;

        $sale_price = $product_attributes->price;
        $product_code = $product_attributes->product_code;

        $categories = array();
        if(isset($product_attributes->category_id)){
            if(isset($product_attributes->category_id[0])){

                foreach($product_attributes->category_id as $category){
                    $categories[] = $category->title;
                }
            }
        }

        if(count($categories) > 0){
            $item_category = implode(', ',$categories);
        }
        */?><!--
        <div id="itemWrap" class="col-md-12">
            <p><label class="field-label">Category: <span class="item_value" id="item_category">{{--{!! $item_category !!}--}}</span></label></p>
            <p><label class="field-label">Retail Price: <span class="item_value" id="item_price">{{--{!! $sale_price !!}--}}</span></label></p>
            <p><label class="field-label">Item Code: <span class="item_value" id="item_code">{{--{!! $product_code !!}--}}</span></label></p>
        </div>

        <?php /* }
            else{
                */?>
            <div id="itemWrap" class="col-md-12 hide">
                <p><label class="field-label">Category: <span class="item_value" id="item_category">{{--{!! $item_category !!}--}}</span></label></p>
                <p><label class="field-label">Retail Price: <span class="item_value" id="item_price">{{--{!! $sale_price !!}--}}</span></label></p>
                <p><label class="field-label">Item Code: <span class="item_value" id="item_code">{{--{!! $product_code !!}--}}</span></label></p>
            </div>
         --><?php
/*            }

        */?>



    </div>

    <?php  $ii++;  $di++; }
    //end of foreach ?>

    <?php    }
    ?>

</div>
@if(isset($is_inactive) && $uri_method != 'view')
    @if($is_inactive == 0)
    <button type="button" class="btn ladda-button btn-theme btn-wide add-more-entity" data-style="zoom-in"> <span class="ladda-label">Add More Items</span> </button>

    <button type="button" class="btn ladda-button btn-theme btn-wide update-cart-btn" data-style="zoom-in"> <span class="ladda-label">Validate Cart</span> </button>

    @endif
@endif