<!DOCTYPE html>
<html>
<head>
    <base href="<?php echo url('/') . "/"; ?>"/>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <title>
        <?php APP_NAME; ?>
        Stubs</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Font CSS  -->
    <link rel="shortcut icon" href="<?php echo config("panel.DIR_PANEL_RESOURCE") ?>assets/img/favicon.ico">
    <link rel="stylesheet" type="text/css" href="<?php echo config("constants.ASSETS_PATH") . DIR_API; ?>asset/css.css">

    <!-- Core CSS  -->
    <link rel="stylesheet" type="text/css"
          href="<?php echo config("constants.ASSETS_PATH") . DIR_API; ?>asset/bootstrap.css">
    <link rel="stylesheet" type="text/css"
          href="<?php echo config("constants.ASSETS_PATH") . DIR_API; ?>asset/font-awesome.css">
    <link rel="stylesheet" type="text/css"
          href="<?php echo config("constants.ASSETS_PATH") . DIR_API; ?>asset/glyphicons.css">

    <!-- Plugin CSS -->
    <link rel="stylesheet" type="text/css"
          href="<?php echo config("constants.ASSETS_PATH") . DIR_API; ?>asset/chosen.css">

    <!-- Theme CSS -->
    <link rel="stylesheet" type="text/css"
          href="<?php echo config("constants.ASSETS_PATH") . DIR_API; ?>asset/theme.css">
    <link rel="stylesheet" type="text/css"
          href="<?php echo config("constants.ASSETS_PATH") . DIR_API; ?>asset/pages.css">
    <link rel="stylesheet" type="text/css"
          href="<?php echo config("constants.ASSETS_PATH") . DIR_API; ?>asset/plugins.css">
    <link rel="stylesheet" type="text/css"
          href="<?php echo config("constants.ASSETS_PATH") . DIR_API; ?>asset/responsive.css">
    <link href="<?php echo config('constants.ADMIN_JS_URL'); ?>plugins/json_editor/jsoneditor.css" rel="stylesheet"
          type="text/css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.5.1/chosen.min.css">
    <style>
        div.jsoneditor-tree, textarea.jsoneditor-text, div.ace-jsoneditor {
            min-height: 300px;
        }

        pre {
            background-color: ghostwhite;
            border: 1px solid silver;
            padding: 10px 20px;
            margin: 20px;
        }

        .json_editor {
            background-color: ghostwhite;
            margin: 0;
        }

        .json-key {
            color: brown;
        }

        .json-value {
            color: navy;
        }

        .json-string {
            color: olive;
        }

        .pad-lft {
            padding-left: 2%;
        }

        div.jsoneditor-menu a.jsoneditor-poweredBy {
            display: none;
        }

        div.jsoneditor {
            border: 1px solid #dddddd !important;
        }

        div.jsoneditor-menu {
            background-color: #999999;
            border-bottom: 1px solid #666666;
        }
    </style>
</head>
<body class="forms-page">
<!-- Start: Main -->
<div id="main">
    <!-- Start: Content -->
    <section>
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel">
                                <form name="api" class="form-horizontal" role="form" method="post">
                                    <div class="panel-heading">
                                        <div class="panel-title"><i class="fa fa-pencil"></i> Fields</div>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label for="services-list" class="col-md-3 control-label">Select
                                                Method</label>
                                            <div class="col-md-9">
                                                <select class="form-control" name="<?php echo $_model->primaryKey; ?>">
                                                    <option value="">Select Any</option>
                                                    <?php if (isset($raw_methods[0])): ?>
                                                        <?php foreach ($raw_methods as $raw_method): ?>
                                                            <?php $record = $_model->get($raw_method->{$_model->primaryKey}); ?>
                                                            <option
                                                                value="<?php echo $record->{$_model->primaryKey}; ?>"><?php echo $record->title; ?></option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="panel-body-title"></div>
                                        <div id="parameters"></div>
                                    </div>
                                    <!--<div class="panel-footer">
                                      <div class="form-group pad-rgt">
                                        <input class="submit btn btn-blue pull-right" value="Submit" type="submit">
                                      </div>
                                    </div>-->
                                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel">
                                <div class="panel-heading">
                                    <div class="panel-title"><i class="fa fa-pencil"></i> Response</div>
                                </div>
                                <div class="panel-body">
                                    <pre id="response" style="overflow-y:auto; max-height:50em;"></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End: Content -->
</div>
<!-- End: Main -->

<!-- Core Javascript - via CDN -->
<script type="text/javascript" src="<?php echo config("constants.ASSETS_PATH") . DIR_API; ?>asset/jquery.js"></script>
<script type="text/javascript"
        src="<?php echo config("constants.ASSETS_PATH") . DIR_API; ?>asset/jquery-ui.js"></script>
<script type="text/javascript"
        src="<?php echo config("constants.ASSETS_PATH") . DIR_API; ?>asset/bootstrap.js"></script>
<!--<script type="text/javascript" src="<?php /*echo config("constants.ASSETS_PATH") . DIR_API; */ ?>asset/chosen.js"></script>-->
<script src="//cdnjs.cloudflare.com/ajax/libs/chosen/1.5.1/chosen.jquery.min.js"></script>
<script src="<?php echo config('constants.ADMIN_JS_URL'); ?>plugins/json_editor/jsoneditor.js"></script>
<script type="text/javascript">
    var app_rel_url = "/<?php echo ADD_PATH . APP_ALIAS . (DO_URL_REWRITE === TRUE ? "" : "index.php/");?>";
    var app_admin_rel_url = "/<?php echo ADD_PATH . APP_ALIAS . (DO_URL_REWRITE === TRUE ? "" : "index.php/");?><?php echo DIR_ADMIN;?>";
    var app_rel_path = "/<?php echo ADD_PATH . APP_ALIAS;?>";
    var base_admin_url = "<?php echo url('/') . "/" . DIR_ADMIN;?>";
    var pls_wait_txt = "Please wait...";
</script>
<script type="text/javascript"
        src="<?php echo config("constants.ASSETS_PATH"); ?>js/core.js?v=<?php echo VERSION; ?>"></script>
<script type="text/javascript">
    $(function () {

        $("select[name=<?php echo $_model->primaryKey; ?>]").change(function () {
            if ($.trim($(this).val()) != "") {
                load_api_url('api');
            }
        });

    });

    function load_api_url(name) {
        jsonValidate("stub_console/load_params", $("form[name=" + name + "]"));
        setTimeout(load_conf, 3000);
    }

    function load_conf() {
        $(".cb_bu_info").chosen({
            search_contains: true,
            width: "100%",
            no_results_text: "not found",
            display_disabled_options: false
        });

        $('.chosen-search input').autocomplete({
            minLength: 1,
            source: function (request, response) {

                var entity_type_id = $($(this)[0].element).closest(".getchoosen").find("select").data("type_id");
                var chosen_id = $($(this)[0].element).closest(".getchoosen").find("select").attr("id");
                updateSearch(entity_type_id, chosen_id, request.term);
            }
        });
    }

    function updateSearch(entity_type_id, chosen_id, term) {
        $.ajax({
            url: "<?php echo url('/backend/getoptions'); ?>",
            dataType: "json",
            data: {"term": term, "entity_type_id": entity_type_id},
            beforeSend: function () {
                $('#' + chosen_id).parent().find('ul.chosen-results').empty();
                $('#' + chosen_id).empty();
            }
        }).done(function (data) {
            //console.log(data);
            $(data).each(function (index, ele) {
                $('#' + chosen_id).append('<option value="' + ele.entity_id + '">' + ele.title + '</option>');
            });
            $("#" + chosen_id).trigger("chosen:updated");
            $('#' + chosen_id).parent().find('.chosen-search input').val(term);
        });
    }

</script>
</body>
</html>
