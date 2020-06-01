<?php
$fields = "App\Libraries\Fields";
$fields = new $fields();
?>
        <!-- begin: .tray-right -->
<aside id="sidebar_right" class="nano tray tray-right tray290 va-t pn affix has-scrollbar" data-tray-height="match">
    <div class="panel-heading rightSidebarHeading" unselectable="on" style="user-select: none;">
        <span class="panel-icon"><i class="fa fa-search fs20 text-primary"></i></span>
        <span class="panel-title"> Filter Columns</span>
		{{--<span class="panel-controls">
			<a href="javascript:window.print()" class="btn btn-xs mr5 text-right"> <i class="fa fa-print fs17"></i> </a>
		</span>--}}
    </div>
    <!-- menu quick links -->
    <div class="p20 admin-form nano-content">
        <!-- <h4 class="mt5 text-muted fw500"> Filter </h4>
        <hr class="short">-->
                <div id="entity-search-fields">
                    <?php
                    if(isset($listing_columns)){

                    foreach($listing_columns as $listing_column){

                    ?>
                    <div class="mb15">
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

        <div class="mt25 clearfix">
            <a type="button" id="grid_reset" class="btn btn-default btn-md col-sm-6 btn-clear">Clear</a>
            <a type="button" id="grid_search" class="btn btn-theme btn-md col-sm-6 btn-search">Search</a>
        </div>

    </div>

</aside>