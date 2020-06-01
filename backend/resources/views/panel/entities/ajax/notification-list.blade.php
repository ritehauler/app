<?php
//$data = array();
if(count($data) > 0){

$separate_identifier = array('general_setting','product_tags','recipe_tags');
$out_of_entity_module = array('role','category','group');

    foreach($data as $list){

        if(in_array($list->target_entity_type_identifier,$separate_identifier)){
            $link = \URL::to($panel_path .'entities/'.$list->target_entity_type_identifier).'?entity_notification_id='.$list->entity_notification_id;
        }
        elseif(in_array($list->target_entity_type_identifier,$out_of_entity_module)){
            $link = \URL::to($panel_path.$list->target_entity_type_identifier).'/view/'.$list->entity_id.'?entity_notification_id='.$list->entity_notification_id;
        }
        else{
            $link = \URL::to($panel_path .'entities/'.$list->target_entity_type_identifier).'/view/'.$list->entity_id.'?entity_notification_id='.$list->entity_notification_id;

        }
        if(trim(strtolower($list->permission)) == 'add')  $permission_title = strtolower($list->permission).'ed';
        else $permission_title = strtolower($list->permission).'d';

        $message = $list->actor_entity_type_title.' has '.$permission_title.' '.$list->target_entity_type_title.' # ';
        $message .= '<a class="text-system" href="'.$link .'">'.$list->entity_id.'</a>';

         $created_at =   \App\Libraries\CustomHelper::getElapsedTime($list->created_at);

    ?>

<li class="media"> <a class="media-left" href="{!! $link !!}"> <span class="mw40 w40 h-40 br64 ib text-center bg-theme"><span class="icon mdi mdi-notifications fs20 ib lh40 fa-inverse"></span></span> </a>
    <div class="media-body">
        <h5 class="media-heading">{!! $list->target_entity_type_title !!} </h5>
        {!! $message !!} <small class="text-muted">{!! $created_at !!}</small> </div>
</li>

<?php } ?>
<li class="media"><a class="media-left" href="{!! \URL::to($panel_path .'notification') !!}">See All</a></li>
<?php } else{
    ?>
<li class="media">No Notification Found</li>
<?php }
?>
