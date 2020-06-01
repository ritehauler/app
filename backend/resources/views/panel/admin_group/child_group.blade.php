@foreach($childs as $child)
    @if(empty($child->deleted_at) && $child->parent_group_id != 0)
        <ul>
            <li class="dd-item" data-id="{!! $child->{$pk} !!}">
              <a href="{!! URL::to(DIR_ADMIN.$module.'/update/'.$child->{$pk} ) !!}"><div class="dd-handle">{!! $child->name !!}<span class="cell-tittle pull-right">{!! date(DATE_TIME_FORMAT_ADMIN,strtotime($child->created_at)) !!}</span> </div></a>
            </li>
            @if(count($child->adminGroups))
            	@include('administrator.admin_group.child_group',['childs' => $child->adminGroups])
            @endif
        </ul>
	@endif
@endforeach