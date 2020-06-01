<div class="adv-search" style="top:60px;">
    <div class="topbar-left">
        <span class="panel-controls">
			<a class="export_rating" title="export">
                <button type="button" class="btn-default btn-sm link-unstyled ib"><span class="icon mdi mdi-export pr5 fs15"></span>Export to Excel</button>
            </a>
        </span>
    </div>
</div>
<aside id="sidebar_right" class="nano tray tray-right tray290 va-t pn affix has-scrollbar" data-tray-height="match">
    <div class="panel-heading rightSidebarHeading" unselectable="on" style="user-select: none;">
        <span class="panel-icon"><i class="fa fa-search fs20 text-primary"></i></span>
        <span class="panel-title"> Filter Rating</span>
    </div>
    <div class="p20 admin-form nano-content">
        <form name="searchEntity" id="searchEntity" method="post">
            <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
            <div id="entity-search-fields">
                <input type="hidden" name="do_export" value="0" />
                <div class="mb15">
                    <label data-toggle="tooltip" class="field-label cus-lbl  field-label cus-lbl" >Order ID</label>
                    <label class="field">
                        <input id="order_id" type="text" name="order_id" class="field_input gui-input form-control">
                    </label>
                </div>
                <div class="mb15">
                    <label data-toggle="tooltip" class="field-label cus-lbl  field-label cus-lbl" >Customer</label>
                    <label class="field">
                        <select id="customer_id" name="customer_id" class="field_input gui-input form-control">
                            <option value=""> -- Select Customer -- </option>
                            @if(count($getCustomers) && !empty($getCustomers))
                                @foreach($getCustomers as $customers)
                                    <option value="{{ $customers->entity_id }}">{{ $customers->attributes->full_name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </label>
                </div>
                <div class="mb15">
                    <label data-toggle="tooltip" class="field-label cus-lbl  field-label cus-lbl" >Driver</label>
                    <label class="field">
                        <select id="driver_id" name="driver_id" class="field_input gui-input form-control">
                            <option value=""> -- Select Driver</option>
                            @if(count($getDrivers) && !empty($getDrivers))
                                @foreach($getDrivers as $drivers)
                                    <option value="{{ $drivers->entity_id }}">{{ $drivers->attributes->full_name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </label>
                </div>
                <div class="mb15">
                    <label data-toggle="tooltip" class="field-label cus-lbl  field-label cus-lbl" >From Date</label>
                    <label class="field">
                        <input id="from_date" type="text" name="from_date" class="field_input gui-input form-control  field_date user_delivery_date js-datepicker" placeholder="" value="">
                    </label>
                </div>
                <div class="mb15">
                    <label data-toggle="tooltip" class="field-label cus-lbl  field-label cus-lbl" >To Date</label>
                    <label class="field">
                        <input id="to_date" type="text" name="to_date" class="field_input gui-input form-control  field_date user_delivery_date js-datepicker" placeholder="" value="">
                    </label>
                </div>
            </div>
            <div class="mt25 clearfix">
                <a type="button" id="grid_reset" class="btn btn-default btn-md col-sm-6 btn-clear">Clear</a>
                <a type="button" id="grid_search" class="btn btn-theme btn-md col-sm-6 btn-search">Search</a>
            </div>
        </form>
    </div>
</aside>
<!-- end: .tray-right -->
