<h4>Request URL </h4>
<div class="panel-body-title"><strong>[<?php echo strtoupper($record->request_type); ?>]
    </strong> <?php echo url('/') . "/" . DIR_API . DIR_STUB_API . $record->endpoint_uri; ?></div>
<h4>Description</h4>
<div class="panel-body-title"><?php echo $record->description; ?></div>
<h4 class="panel-body-title">Parameters</h4>
<form name="get_response" id="get_response">
    <?php $fields = json_decode($record->request); ?>
    <?php if (isset($fields->fields) && isset($fields->fields[0])): ?>
        <!--<form name="get_response" id="get_response">-->
        <?php foreach ($fields->fields as $field): ?>
            <?php $draw_elment = isset($field->name) && isset($field->type); ?>
            <?php if ($draw_elment) : ?>
                <div class="form-group">
                    <label for="secretKey" class="col-lg-4 control-label" data-toggle="tooltip"
                           title="<?php echo isset($field->hint) ? $field->hint : ''; ?>"><?php echo $field->name; ?>
                        (<?php echo $field->type; ?>)</label>
                    <div class="col-md-8">
                        <div class="input-group">
                            <input type="text" name="<?php echo $field->name; ?>" class="form-control fields"
                                   placeholder="<?php echo isset($field->hint) ? $field->hint : ''; ?>"
                                   value="<?php echo isset($field->value) ? $field->value : ''; ?>"/>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
        <!--<div class="form-group pad-lft">
          <input class="submit btn btn-blue pull-left" id="submit_btn" value="Submit" type="submit">
        </div>
        <input type="hidden" name="inner_response" value="1" />
      </form>-->

    <?php else: ?>
        <div class="form-group">
            <div class="col-md-10"> No Parameters found</div>
        </div>
    <?php endif; ?>
    <!--<div class="col-md-12">
        <input class="submit btn btn-blue pull-right" id="submit_btn" value="Submit" type="submit">
    </div>-->
    <input type="hidden" name="inner_response" value="1"/>
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
</form>

<script type="text/javascript">
    // create the editor
    var cont = document.getElementById('response');
    cont.innerHTML = '';
    var opt_cont = {
        mode: 'code',
        onError: function (err) {
            alert(err.toString());
        },
        onChange: function () {

        }
    };

    var ed_json = new JSONEditor(cont, opt_cont);
    ed_json.set(<?php echo $record->response; ?>);
</script>