<input type="hidden" id="depend_entity_exist" name="depend_entity_exist" value="1"/>

<div class="bulk_entity_wrap">

    <?php

    if ((isset($depend_entity_records) && count($depend_entity_records)>0) &&
    (isset($depend_update) && count($depend_update)>0)) {

    $ii = 0;
    foreach ($depend_update as $depend_update_item) {

      $depend_update_item->identifier = $depend_entity_type_data->identifier;
    ?>

    <div class="row bulk_entity_raw border-wrap mb20" id="bulk_entity_raw_{!! $ii !!}">

        <?php if($entity_data->identifier == "delivery_slot"){ ?>
         <a style="float:right" class="fa fa-times delete-depend-entity delete-depend-element" data-depend_entity_id="{!! $depend_update_item->entity_id !!}" id="delete-depend-entity-<?php echo $ii; ?>" href="javascript:void(0);"></a>
       <?php } ?>
        <!--<div class="section-divider mb30 mt15" id="spy1">
            <span>Add Item</span>
        </div>-->
        <input type="hidden" name="do_post" value="1"/>
        <input type="hidden" name="_token" value="{!! csrf_token() !!}"/>
        <input type="hidden" name="entity_type_id" id="entity_type_id"
               value="{!! $depend_entity_type_data->entity_type_id !!}"/>
        <input type="hidden" name="entity_id" id="entity_id" value="{!! $depend_update_item->entity_id !!}" />
        <?php

        /*add hidden fields*/
        if (isset($depend_entity_hidden_records) && count($depend_entity_hidden_records)>0) {
            foreach ($depend_entity_hidden_records as $hidden_record) {
                if ($hidden_record->is_entity_column == 1) {
                    echo $fields->randerEntityFields($hidden_record, $depend_update_item, $depend_entity_type_data->entity_type_id,true);
                } else {
                    echo $fields->randerFields($hidden_record, $depend_update_item, $depend_entity_type_data->entity_type_id,true);
                }
            }
        }

        //  print_r($depend_entity_records);
        foreach ($depend_entity_records as $key =>$record) {

        if ($record->is_entity_column == 1) {
            $field_class = $record->attribute_code . '_field';

        } else {
            $field_class = $record->name . '_field';
        }

        //Check if column has to show / hide
        $hide = $fields->showHideColumn($record->view_at,true);

        //when column is odd
        /*if($key % 2 == 0){ */?><!--
                                                         <div class="row mbn">--><?php /*} */ ?>


        <div class="section mb10 col-md-6 {!! $field_class.' '.$hide !!}">
            <?php
            if (isset($record->element_type) && $record->element_type == 'text') {
                $record->element_type = 'input';
            }
            if ($record->is_entity_column == 1) {
                echo $fields->randerEntityFields($record, $depend_update_item, $depend_entity_type_data->entity_type_id,true);
            } else {
                echo $fields->randerFields($record, $depend_update_item, $depend_entity_type_data->entity_type_id,true);
            }


            ?> </div>

        <?php
        //when column is even
        /* if($key%2 > 0){*/?><!-- </div>--><?php /*} */
        ?>
        <?php
        ?>
        <?php  } ?>

        <?php if($entity_data->identifier == "promotion_discount"){

        if(isset( $depend_update_item->attributes->promotion_product_id->detail->attributes)){

        $product_attributes = $depend_update_item->attributes->promotion_product_id->detail->attributes;

        $sale_price = isset($product_attributes->price) ? $product_attributes->price : "";
        $product_code = isset($product_attributes->product_code) ? $product_attributes->product_code : "";

        $categories = array();
        if(isset($product_attributes->category_id)){
            if(isset($product_attributes->category_id[0])){

                foreach($product_attributes->category_id as $category){
                    if(isset($category->title)){
                        $categories[] = $category->title;
                    }

                }
            }
        }

        $item_category = '';
        if(count($categories) > 0){
            $item_category = implode(', ',$categories);
        }
        ?>
        <div id="itemWrap" class="col-md-12">
            <p><label class="field-label">Category: <span class="item_value" id="item_category">{!! $item_category !!}</span></label></p>
            <p><label class="field-label">Retail Price: <span class="item_value" id="item_price">{!! $sale_price !!}</span></label></p>
            <p><label class="field-label">Item Code: <span class="item_value" id="item_code">{!! $product_code !!}</span></label></p>
        </div>

        <?php  }
            else{
                ?>
            <div id="itemWrap" class="col-md-12 hide">
                <p><label class="field-label">Category: <span class="item_value" id="item_category">{!! $item_category !!}</span></label></p>
                <p><label class="field-label">Retail Price: <span class="item_value" id="item_price">{!! $sale_price !!}</span></label></p>
                <p><label class="field-label">Item Code: <span class="item_value" id="item_code">{!! $product_code !!}</span></label></p>
            </div>
         <?php
            }
        }
        ?>



    </div>

    <?php  $ii++; }
    //end of foreach ?>

    <?php    }
    ?>

</div>