@include(DIR_ADMIN.'header')
<!-- Start: Content-Wrapper -->
<link rel="stylesheet" type="text/css" href="{!! URL::to(config('constants.ADMIN_ASSET_URL') . 'vendor/plugins/nestable/nestable.css' ) !!}" />
<section id="content_wrapper" class="content">
  <div id="seaction-header"> @include(DIR_ADMIN.'flash_message')
    <div class="adv-search">
      <div class="topbar-left">

      </div>
      <div class="topbar-right text-right">
        <div class="pull-right adv-search-bar">
          {{--<button type="button" class="accordion-toggle mr5 btn-default btn-sm accordion-icon link-unstyled collapsed ib" data-toggle="collapse" data-parent="#accordion" href="#adv-search"><span class="icon mdi mdi-menu"></span></button>--}}
          @if($perm_add) <a href="{!! URL::to(DIR_ADMIN.$module.'/add') !!}" class="btn-default btn-sm add-new-btn link-unstyled ib"><span class="icon mdi mdi-plus pr5 fs15"></span> Add {!! $p_title !!}</a> @endif </div>
      </div>
    </div>
    {{--<div id="adv-search" class="panel-collapse collapse" style="height: auto;">--}}
      {{--<div class="panel-body bg-light lighter br-h-n br-t-n search-filters">--}}
        {{--<div class="row">--}}
          {{--<div class="col-md-8">--}}
            {{--<div class="row">--}}
              {{--<div class="col-md-6 multiSelect mb20">--}}
                {{--<label>Organization Name</label>--}}
                {{--<select class="search_columns" id="multiselect2" multiple="multiple" >--}}
                  {{--<option value="admin_group">Admin Group</option>--}}
                  {{--<option value="username">User</option>--}}
                  {{--<option value="email">Email</option>--}}
                  {{--<option value="status">Status</option>--}}
                {{--</select>--}}
              {{--</div>--}}
              {{--<div class="col-md-6 mb5">--}}
                {{--<label>Status</label>--}}
                {{--<input type="text" name="keyword" class="form-control" placeholder="Name">--}}
              {{--</div>--}}
            {{--</div>--}}
          {{--</div>--}}
          {{--<div class="col-md-4">--}}
            {{--<div class="mt25 clearfix"> <a type="button" id="grid_reset" class="btn btn-default btn-md col-sm-6 mr5 btn-clear">Clear</a> <a type="button" id="grid_search" class="btn btn-theme btn-md col-sm-6 btn-search">Search</a> </div>--}}
          {{--</div>--}}
        {{--</div>--}}
      {{--</div>--}}
    {{--</div>--}}
  </div>
  <!-- End: Topbar -->
  <!-- Begin: Content -->
  <section id="content">
    <div class=" panel-visible">
      <div class="table-responsive">
       @if (isset($raw_ids[0])) {{--*/ $i = 0 /*--}}
        <div class="dd" id="nestable">
          <ol class="dd-list">
            <!-- found records -->
            @foreach($raw_ids as $raw_id)
            {{--*/ $record = $model->get($raw_id->{$pk}) /*--}}
            <li class="dd-item" data-id="{!! $raw_id->{$pk} !!}">
              <a href="{!! URL::to(DIR_ADMIN.$module.'/update/'.$record->{$pk}) !!}"><div class="dd-handle">{!! $record->name !!} <span class="cell-tittle pull-right">{!! date(DATE_TIME_FORMAT_ADMIN,strtotime($record->created_at)) !!}</span> </div></a>
              @if(count($raw_id->adminGroups))
                  @include('administrator.admin_group.child_group',['childs' => $raw_id->adminGroups])
              @endif
            </li>
            @endforeach
          </ol>
        </div>
        @endif
      </div>
    </div>
  </section>
  <!-- End: Content -->
  <!-- Begin: Page Footer -->
  @include(DIR_ADMIN . 'footer_bottom')
  <!-- End: Page Footer -->
</section>
<!-- Detailed View Popup -->
<!-- End: Content-Wrapper -->
<!-- Required Plugin CSS -->
<link rel="stylesheet" type="text/css" href="{!! URL::to(config('constants.ADMIN_ASSET_URL') . 'vendor/plugins/datepicker/css/bootstrap-datetimepicker.css' ) !!}">
<link rel="stylesheet" type="text/css" href="{!! URL::to(config('constants.ADMIN_ASSET_URL') . 'vendor/plugins/daterange/daterangepicker.css' ) !!}">
<!-- Page Plugins via CDN -->
<script src="{!! URL::to(config('constants.ADMIN_ASSET_URL') . 'vendor/plugins/moment/moment.min.js' ) !!}"></script>

<script src="{!! URL::to(config('constants.ADMIN_ASSET_URL').'vendor/plugins/nestable/jquery.nestable.js') !!}"></script>
<script>
// Init Nestable on list 1
$('#nestable').nestable({
	group: 1
});
</script>
@include(DIR_ADMIN.'footer')