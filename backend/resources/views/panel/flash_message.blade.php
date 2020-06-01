@if(Session::has(ADMIN_SESS_KEY.'success_msg'))
<div class="alert alert-success alert-dismissable">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  <p><strong>Success : </strong>{!!  Session::get(ADMIN_SESS_KEY.'success_msg') !!}</p>
</div>
{{--*/ Session::forget(ADMIN_SESS_KEY.'success_msg') /*--}} 
@endif
@if(Session::has(ADMIN_SESS_KEY.'error_msg'))
<div class="alert alert-danger alert-dismissable">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  <p><strong>Error : </strong>{!!  Session::get(ADMIN_SESS_KEY.'error_msg') !!}</p>
</div>
{{--*/ Session::forget(ADMIN_SESS_KEY.'error_msg') /*--}} 
@endif
@if(Session::has(ADMIN_SESS_KEY.'info_msg'))
<div class="alert alert-info alert-dismissable">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  <p><strong>Info : </strong>{!!  Session::get(ADMIN_SESS_KEY.'info_msg') !!}</p>
</div>
{{--*/ Session::forget(ADMIN_SESS_KEY.'info_msg') /*--}} 
@endif