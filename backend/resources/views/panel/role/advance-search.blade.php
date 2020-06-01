<?php
$fields = "App\Libraries\Fields";
$fields = new $fields();
?>
<div id="adv-search" class="panel-collapse collapse" style="height: auto;">
    <div class="panel-heading">
        <label>Filter {!! isset($entity_data->title) ? $entity_data->title : "Columns" !!}</label>
    </div>
    <div class="panel-body bg-light lighter br-h-n br-t-n search-filters">

        <div class="main admin-form ">
            <div class="row">
                <div id="entity-search-fields">
                    <?php
                    if(isset($listing_columns)){

                    foreach($listing_columns as $listing_column){
                        if($listing_column->name == 'description') continue;
                    ?>
                    <div class="section mb10 col-md-6">
                        {{--<label>{!! $field_title !!}</label>--}}
                        <?php
                        if($listing_column->element_type=='text') $listing_column->element_type='input';
                        echo $fields->randerFields($listing_column,null,0,false,array('search_columns'=>true));
                        ?>

                    </div>

                    <?php	}
                    }

                    ?>
                </div>

                <div class="col-md-6">
                    <div class="mt25 clearfix">
                        <a type="button" id="grid_reset" class="btn btn-default btn-md col-sm-6 mr5 btn-clear">Clear</a>
                        <a type="button" id="grid_search" class="btn btn-theme btn-md col-sm-6 btn-search">Search</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>