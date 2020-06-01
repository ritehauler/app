<?php
$fields = "App\Libraries\Fields";
$fields = new $fields();
$el_attr = array("div_class" => "", "lbla_class" => "col-lg-4 control-label", "lblb_class" => "col-md-8", 'show_name' => true);
?>
<h4>Request URL </h4>
<div class="panel-body-title"><strong>[<?php echo strtoupper($api_method->type); ?>
        ]</strong> <?php echo url('/') . "/" . DIR_API . $api_method->uri; ?></div>
<h4>Description</h4>
<div class="panel-body-title"><?php echo $api_method->description; ?></div>
<h4 class="panel-body-title">Parameters</h4>

<form name="get_response" id="get_response">
    <?php if (isset($records[0])): ?>
        <!--<form name="get_response" id="get_response">-->
        <?php foreach ($records as $record): ?>
            <?php $field = $api_method_field_model->get($record->api_method_field_id);
            if ($field->data_type == 'callback')://echo  $field->name;
                ?>
                <div class="form-group ">
                    <label for="secretKey" class="col-lg-4 control-label" data-toggle="tooltip"
                           title="<?php echo $field->description; ?>"><?php echo $field->name; ?>
                        (<?php echo $field->type; ?>)</label>
                    <div class="col-md-8">
                        <div class="input-group">
                            <select class="form-control fields <?php echo $field->default_value; ?>"
                                    name="<?php echo $field->name; ?>"
                                    data-type_id="<?php echo $api_method->type_id; ?>"
                                    onchange="load_api_url('get_response')">
                                <?php
                                //->where("allow_backend_auth", "=",$record->is_entity_auth)
                                $options = \DB::table('sys_entity_type')->select('*')->get();

                                foreach ($options as $option):
                                    $selected = ($option->entity_type_id == $api_method->type_id) ? 'selected="selected"' : '' ?>
                                    <option <?= $selected ?>
                                        value="<?php echo $option->entity_type_id; ?>"><?php echo $option->identifier; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            <?php elseif ($field->data_type == 'json'): ?>
                <div class="form-group">
                    <label for="secretKey" class="col-lg-4 control-label" data-toggle="tooltip"
                           title="<?php echo $field->description; ?>"><?php echo $field->name; ?>
                        (<?php echo $field->type; ?>)</label>
                    <div class="col-md-8">
                        <div id="json_<?php echo $field->name; ?>" class="json_editor"></div>
                        <input type="hidden" name="<?php echo $field->name; ?>" value=""/>
                        <script type="text/javascript">
                            // create the editor
                            var cont_<?php echo $field->name; ?> = document.getElementById('json_<?php echo $field->name; ?>');
                            var opt_cont_<?php echo $field->name; ?> = {
                                mode: 'code',
                                onError: function (err) {
                                    alert(err.toString());
                                },
                                onChange: function () {
                                    try {
                                        $('input[name="<?php echo $field->name; ?>"]').val(JSON.stringify(ed_<?php echo $field->name; ?>.get()));
                                    }
                                    catch (err) {
                                        // silent
                                    }
                                }
                            };

                            var ed_<?php echo $field->name; ?> = new JSONEditor(cont_<?php echo $field->name; ?>, opt_cont_<?php echo $field->name; ?>);
                            ed_<?php echo $field->name; ?>.set({});
                        </script>
                    </div>
                </div>
                <?php
            elseif ($field->data_type != 'query' && $field->data_type != 'callback'): ?>
                <div class="form-group">
                    <?php
                    if ($field->element_type == "text" || $field->element_type == "hidden") $field->element_type = "input";
                    echo $fields->randerInput($field, NULL, $api_method->type_id, $el_attr); ?>
                </div>
            <?php elseif ($field->data_type == 'query'): ?>
                <div class="form-group">
                    <?php
                    //$entity_type_id = (isset($_POST['entity_type_id']))?$_POST['entity_type_id']:$api_method->type_id;
                    echo $fields->randerInput($field, NULL, $api_method->type_id, $el_attr);
                    ?>

                </div>
            <?php endif; ?>


        <?php endforeach; ?>

        <?php if (isset($show_gallery)) { ?>
            <div id="div_gallery_items" class="gallery_items">
                <div class="section mb20">
                    <label data-toggle="tooltip" class="col-lg-4 control-label field-label cus-lbl"
                           data-original-title="Comma separated gallery items IDs">gallery_items </label>
                    <label class="col-md-8 field">
                        <input id="gallery_items" name="gallery_items" class="field_intfield form-control fields"
                               placeholder="1,2,3" value="" type="text">
                    </label>
                </div>
                <div style="clear:both">
                </div>
            </div>

            <div id="div_gallery_featured_item" class="gallery_featured_item">
                <div class="section mb20">
                    <label data-toggle="tooltip" class="col-lg-4 control-label field-label cus-lbl"
                           data-original-title="featured gallery item ID">gallery_featured_item </label>
                    <label class="col-md-8 field">
                        <input id="gallery_featured_item" name="gallery_featured_item"
                               class="field_intfield form-control fields" placeholder="2" value="" type="text">
                    </label>
                </div>
                <div style="clear:both">
                </div>
            </div>
        <?php } ?>

        <!--<div id="div_actor_user_id" class="div_actor_user_id">
            <div class="section mb20">
                <label data-toggle="tooltip" class="col-lg-4 control-label field-label cus-lbl"
                       data-original-title="Actor user ID">actor_user_id </label>
                <label class="col-md-8 field">
                    <input id="actor_user_id" name="actor_user_id" class="field_intfield form-control fields" value=""
                           type="text">
                </label>
            </div>
            <div style="clear:both">
            </div>
        </div>-->

        <!--<div class="form-group pad-lft">
          <input class="submit btn btn-blue pull-left" id="submit_btn" value="Submit" type="submit">
        </div>
        <input type="hidden" name="inner_response" value="1" />
      </form>-->
      <div class="form-group">
      <div class="col-md-12">
        <label class="col-lg-4 control-label field-label cus-lbl">mobile_json </label>
        <label class="col-md-8 field">
            <input id="mobile_json" name="mobile_json" class="field_time form-control fields" value="1"
                   checked="checked" type="checkbox">
        </label>
    </div>
    </div>
    <?php else: ?>
        <div class="form-group">
            <div class="col-md-10"> No Parameters found</div>
        </div>
    <?php endif; ?>
    

    <div class="col-md-12">
        <input class="submit btn btn-blue pull-right" id="submit_btn" value="Submit" type="submit">
    </div>

    <input type="hidden" name="uri" value="<?php echo $api_method->type; ?>|<?= $api_method->uri ?>"/>
    <input type="hidden" name="inner_response" value="1"/>
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
</form>
<script>
    $("form[name=get_response]").submit(function (e) {
        e.preventDefault();
        jsonValidate("<?php echo DIR_API; ?><?php echo $api_method->uri; ?>?v=<?php echo time(); ?>", $("form[name=get_response]"), null, "<?php echo $api_method->type; ?>");
    });


    $(function () {

        $('[data-toggle="tooltip"]').tooltip({placement: 'top'});

        $('#is_auth_exists').change(function () {
            var exists = $(this).val();
            console.log("check", exists);
            if (exists > 0) {
                console.log("exists");
                $("._not_exists").parents("div.form-group").hide();
                $("._exists").parents("div.form-group").show();
            } else {
                console.log("not exists");
                $("._not_exists").parents("div.form-group").show();
                $("._exists").parents("div.form-group").hide();
            }
        });

        $('#is_auth_exists').change();
    });
</script>