{{--*/
// extra models
$admin_module_model = $model_path."AdminModule";
$admin_module_model = new $admin_module_model;

$admin_module_permission_model = $model_path."AdminModulePermission";
$admin_module_permission_model = new $admin_module_permission_model;
/*--}}
@include(DIR_ADMIN.'header')
<!-- Begin: Content -->
<form name="data_form" class="form" method="post">
<input type="hidden" name="_token" value="{!! csrf_token() !!}"/>
<input type="hidden" name="do_post" value="1"/>
<section id="content_wrapper" class="content">
  <section id="content" class="pn">
    @include(DIR_ADMIN.'flash_message')
    <div class="p20 bg-light br-b">
        <div class="main admin-form ">
          <div class="row">
            <div class="col-md-12 ">
              <h3 class="mt10">{!! $p_title !!}</h3>
              <div class="section mb10">
                <label for="" class="field">
                <input type="text" name="name" id="name" class="gui-input" placeholder="Enter Name">
                </label>
                <div id="error_msg_name" class="help-block text-right animated fadeInDown hide" style="color:red">Please enter Role Name</div>
              </div>
            </div>
          </div>
        </div>
    </div>
    <div class="col-md-12 pn">
      <section id="content" class="pn permission-table">
      					<div class="panel">
      						<table class="table">
      							<thead>
      								<tr>
      									<th style="width:8%;">#</th>
      									<th style="width:45%;">Module</th>
      									<th style="width:10%;">
      									<div class="checkbox-t">
      										  <input type="checkbox" name="view_master" id="check01">
      										  <label for="check01"  class="mr5"></label> View
                                        </div>
      									</th>
      									<th style="width:10%;">
      									<div class="checkbox-t">
      										  <input type="checkbox" name="add_master" id="check02">
      										  <label for="check02" class="mr5"></label> Add
      									</div>
      									</th>
      									<th style="width:10%;">
      									<div class="checkbox-t">
      										  <input type="checkbox" name="update_master" id="check03">
      										  <label for="check03" class="mr5"></label> Update
      							        </div>
      									</th>
      									<th style="width: 10%;">
      									<div class="checkbox-t">
      										  <input type="checkbox" name="delete_master" id="check04">
      										  <label for="check04" class="mr5"></label> Delete
      							        </div>
      									</th>
      								</tr>
      							</thead>
      							<tbody>
      							{{--*/
                                        $admin_group_id = \Session::get(ADMIN_SESS_KEY."auth")->admin_group_id;
                                        $raw_ids = $admin_module_model->select("admin_module.*")
                                                ->where("admin_module.is_active","=",1)
                                                ->whereNull("admin_module.deleted_at")
                                                ->where("admin_module.parent_id","=", 0)
                                                ->where("admin_module.admin_module_id",">", 0)
                                                ->whereRaw("admin_module.`admin_module_id` IN (SELECT admin_module_id FROM admin_module_permission WHERE admin_group_id = {$admin_group_id}  AND deleted_at IS NULL)")
                                                ->orderBy("name","ASC")
                                                ->groupBy("admin_module.admin_module_id")
                                                ->get();

                                /*--}}

                                @if (isset($raw_ids[0])) {{--*/ $i=0 /*--}}
                                @foreach($raw_ids as $raw_id) {{--*/ $i++ /*--}}
                                        <!-- get record -->
                                {{--*/ $record = DB::table("admin_module")->select("admin_module.*")
                                                ->join("admin_module_permission","admin_module_permission.admin_module_id","=","admin_module.admin_module_id")
                                                ->where("admin_module_permission.admin_group_id",\Session::get(ADMIN_SESS_KEY."auth")->admin_group_id)
                                                ->where("admin_module.admin_module_id",$raw_id->admin_module_id)
                                                ->first()
                                /*--}}
                                        <!-- check if has childs -->
                                {{--*/ $raw_child_ids = $admin_module_model->select("admin_module_id")
                                ->where("is_active","=",1)
                                ->whereNull("deleted_at")
                                ->where("parent_id","=", $record->admin_module_id)
                                ->orderBy("name","ASC")
                                ->get() /*--}}
                                        <!-- check child -->
                                @if(isset($raw_child_ids[0])) {{--*/ $j=0 /*--}}
                                        <!-- has child-->
      								<tr>
      									<td>{!! $i !!}</td>
      									<td class="cell-cont-wrap" colspan="4">{!! $record->name !!}</td>
      								</tr>
      								<!-- childs loop -->
                                    @foreach($raw_child_ids as $raw_child_id) {{--*/ $j++ /*--}}
                                            <!-- get child record -->
                                    {{--*/ $c_record = $admin_module_model->get($raw_child_id->admin_module_id);  /*--}}
                                    <?php
                                        $getAdminModulePermission = DB::table('admin_module_permission')->where('admin_group_id',\Session::get(ADMIN_SESS_KEY."auth")->admin_group_id)->where('admin_module_id',$c_record->admin_module_id)->whereNull('deleted_at')->first();
                                    ?>
      								<tr>
      								    <td>
      								        {!! $i !!}. {!! $j !!}
      								        <input type="hidden" name="modules[]" value="{!! $c_record->admin_module_id !!}"/>
      								        <input type="hidden" name="parents[]" value="{!! $c_record->parent_id !!}"/>
      								    </td>
      								    <td class="cell-cont-wrap">{!! $c_record->name !!}</td>
      									<td>
      									    @if(count($getAdminModulePermission) != 0 && $getAdminModulePermission->view == 1)
      										  <div class="checkbox-t">
                                                  <input type="checkbox" id="view_{!! $c_record->admin_module_id !!}" name="view_{!! $c_record->admin_module_id !!}">
                                                  <label for="view_{!! $c_record->admin_module_id !!}"></label>
      										  </div>
      										@endif
      									</td>
      									<td>
      									    @if(count($getAdminModulePermission) != 0 && $getAdminModulePermission->add == 1)
      										  <div class="checkbox-t">
                                                  <input type="checkbox" id="add_{!! $c_record->admin_module_id !!}" name="add_{!! $c_record->admin_module_id !!}">
                                                  <label for="add_{!! $c_record->admin_module_id !!}"></label>
                                              </div>
      										@endif
      									</td>
      									<td>
      									    @if(count($getAdminModulePermission) != 0 && $getAdminModulePermission->update == 1)
      										<div class="checkbox-t">
      										  <input type="checkbox" id="update_{!! $c_record->admin_module_id !!}" name="update_{!! $c_record->admin_module_id !!}">
      										  <label for="update_{!! $c_record->admin_module_id !!}"></label>
      										</div>
      										@endif
      									</td>
      									<td>
      									@if( count($getAdminModulePermission) != 0 && $getAdminModulePermission->delete == 1)
      									    <div class="checkbox-t">
      										  <input type="checkbox" id="delete_{!! $c_record->admin_module_id !!}" name="delete_{!! $c_record->admin_module_id !!}">
      										  <label for="delete_{!! $c_record->admin_module_id !!}"></label>
      										</div>
      									@endif
      									</td>
      								</tr>

      								@endforeach
                                    @else
                                    <!-- no child -->
                                    <?php
                                        $getAdminModulePermission = DB::table('admin_module_permission')->where('admin_group_id',\Session::get(ADMIN_SESS_KEY."auth")->admin_group_id)->where('admin_module_id',$record->admin_module_id)->whereNull('deleted_at')->first();
                                    ?>
      								<tr>
      									<td>
      									    {!! $i !!}
      									     <input type="hidden" name="modules[]" value="{!! $record->admin_module_id !!}"/>
      									     <input type="hidden" name="parents[]" value="{!! $record->parent_id !!}"/>
      									</td>
      									<td class="cell-cont-wrap">{!! $record->name !!}</td>
      									<td>
      									    @if(count($getAdminModulePermission) != 0 && $getAdminModulePermission->view == 1)
      									    <div class="checkbox-t">
      										  <input type="checkbox" id="view_{!! $record->admin_module_id !!}"  name="view_{!! $record->admin_module_id !!}">
      										  <label for="view_{!! $record->admin_module_id !!}"></label>
      										</div>
      										@endif
      									</td>
      									<td>
      									    @if( count($getAdminModulePermission) != 0 && $getAdminModulePermission->add == 1)
      									    <div class="checkbox-t">
      										  <input type="checkbox" id="add_{!! $record->admin_module_id !!}" name="add_{!! $record->admin_module_id !!}">
      										  <label for="add_{!! $record->admin_module_id !!}"></label>
      										 </div>
      										@endif
      									</td>
      									<td>
      									    @if(count($getAdminModulePermission) != 0 && $getAdminModulePermission->update == 1)
      									    <div class="checkbox-t">
      										  <input type="checkbox" id="update_{!! $record->admin_module_id !!}" name="update_{!! $record->admin_module_id !!}">
      										  <label for="update_{!! $record->admin_module_id !!}"></label>
      										</div>
      										@endif
      									</td>
      									<td>
      									    @if(count($getAdminModulePermission) != 0 && $getAdminModulePermission->delete == 1)
      									    <div class="checkbox-t">
      										  <input type="checkbox" id="delete_{!! $record->admin_module_id !!}" name="delete_{!! $record->admin_module_id !!}">
      										  <label for="delete_{!! $record->admin_module_id !!}"></label>
      										</div>
      										@endif
      									</td>
      								</tr>
                                    @endif
                                    @endforeach
                                    @else
                                    <!-- no records -->
                                    <tr>
                                        <td style="width: 100%;" colspan="5" align="center">No records found
                                        </td>
                                    </tr>
                                    @endif
      							</tbody>
      						</table>
      					</div>
      					<div class="pull-right">
      						<button type="submit" class="btn ladda-button btn-theme btn-wide" data-style="zoom-in">
      							<span class="ladda-label">Submit</span>
      						</button>
      					</div>
      				</section>
    </div>
  </section>
  <!-- End: Content -->
  <!-- Begin: Page Footer -->
  @include(DIR_ADMIN . 'footer_bottom') </section>
</form>
<script type="text/javascript">
    $(function () {
        // default form submit/validate
        $('form[name="data_form"]').submit(function (e) {
            e.preventDefault();
            // hide all errors
            $("div[id^=error_msg_]").removeClass("show").addClass("hide");
            // validate form
            return jsonValidate('<?=Request::url()?>', $(this), 'i[id=loading]');
        });

        // master checkbox controls
        $('input[name$="_master"]').change(function () {
            var sel = $(this).attr('name').replace('_master', '');
            if ($(this).prop('checked')) {
                $('input[name^="' + sel + '_"]').prop('checked', true);
                // mark views checked on other master control checked
                if ($(this).attr('name') != 'view_master') {
                    $('input[name^="view_"]').prop('checked', true);
                }
            }
            else {
                $('input[name^="' + sel + '_"]').prop('checked', false);
            }
        });

        // all controls
        $('input[id^="add_"], input[id^="update_"], input[id^="delete_"]').change(function () {
            if ($(this).prop('checked')) {
                elemData = $(this).attr('id').split('_');
                $('input[id="view_' + elemData[1] + '"]').prop('checked', true);
            }
        });

    });

</script>
@include(DIR_ADMIN.'footer') 