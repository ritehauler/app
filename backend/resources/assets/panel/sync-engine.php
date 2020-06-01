<?php
    ob_start();
    include("header.php");
    headerDefault();
    $buffer=ob_get_contents();
    ob_end_clean();

    $buffer=str_replace("%TITLE%","Sync Engine",$buffer);
    echo $buffer;
?>
	
<!-- Start: Content-Wrapper -->
    <section id="content_wrapper" class="content">
      <!-- Start: Topbar-Dropdown -->
      <div id="topbar-dropmenu">
        <div class="topbar-menu row">
          <div class="col-xs-4 col-sm-2">
            <a href="#" class="metro-tile">
              <span class="glyphicon glyphicon-inbox"></span>
              <span class="metro-title">Messages</span>
            </a>
          </div>
          <div class="col-xs-4 col-sm-2">
            <a href="#" class="metro-tile">
              <span class="glyphicon glyphicon-user"></span>
              <span class="metro-title">Users</span>
            </a>
          </div>
          <div class="col-xs-4 col-sm-2">
            <a href="#" class="metro-tile">
              <span class="glyphicon glyphicon-headphones"></span>
              <span class="metro-title">Support</span>
            </a>
          </div>
          <div class="col-xs-4 col-sm-2">
            <a href="#" class="metro-tile">
              <span class="fa fa-gears"></span>
              <span class="metro-title">Settings</span>
            </a>
          </div>
          <div class="col-xs-4 col-sm-2">
            <a href="#" class="metro-tile">
              <span class="glyphicon glyphicon-facetime-video"></span>
              <span class="metro-title">Videos</span>
            </a>
          </div>
          <div class="col-xs-4 col-sm-2">
            <a href="#" class="metro-tile">
              <span class="glyphicon glyphicon-picture"></span>
              <span class="metro-title">Pictures</span>
            </a>
          </div>
        </div>
      </div>
      <!-- End: Topbar-Dropdown -->
    
        <!-- Start: Topbar -->
        <div id="seaction-header">
            <div id="topbar" >
                <div class="topbar-left">
                    <div class="sec-title">
                        <span>Showing</span>
                        <span class="p-list-count op6">(12)</span>
                    </div>
                </div>
                <div class="topbar-right">
                    <div class="pull-right">
                        <form class="admin-form ib va-m mr5 search">
                            <div class="smart-widget sm-right">
                                <label class="field mbn">
                                  <input type="text" name="search" id="jquery-search-sample" class="input-sm mnw200" placeholder="Search">
                                </label>
                                <button type="submit" class="button btn btn-sm h-30 ph10 lh20 ">
                                  <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </form>   
                        <button type="button" class="btn-default btn-sm add-new-btn link-unstyled ib va-m" href="add-sync-engine.php"><span class="icon mdi mdi-plus pr5 fs15"></span> Add Sync Engine</button>
                    </div>
                </div>
            </div>
        </div>    
        <!-- End: Topbar -->
	  
        <!-- Begin: Content -->
        <section id="content" class="table-layout pn tab-block"> 
            <aside class="tray tray-left tray240 pn">
                <ul class="nav tray-nav-border side-nav-tab">
                    <li><a id="tabAll" href="#" data-toggle="tab" aria-expanded="false">All</a></li>
                    <li class="active"><a href="#general_tab" data-toggle="tab" aria-expanded="false">General</a></li>
                    <li class=""><a href="#text_tab" data-toggle="tab" aria-expanded="false">Text</a></li>
                    <li class=""><a href="#color_tab" data-toggle="tab" aria-expanded="false">Color</a></li>
                    <li class=""><a href="#typography_tab" data-toggle="tab" aria-expanded="false">Typography</a></li>
                    <li class=""><a href="#constant_tab" data-toggle="tab" aria-expanded="false">Constant</a></li>
                    <li class=""><a href="#custom_tab" data-toggle="tab" aria-expanded="false">Custom</a></li>
                    <li class=""><a href="#custom_tab" data-toggle="tab" aria-expanded="false">Import</a></li>
                </ul>
            </aside>
            <div class="tray tray-center">
                <div class="tab-header">
                    <h3 class="mtn">General</h3>
                    <p>Setting up MD Iconic Font can be as simple as adding one line of code to your website - it's like Font Awesome but with Material Design by Google.</p>
                </div>
                <div class="tab-content admin-form">
                    <div id="general_tab" class="tab-pane active">
                        <div class="panel-group mb10" id="accordion">
                            <div class="panel panel-default mb10 jsearch-row">
                               <div class="panel-heading">
                                    <a class="panel-title" data-toggle="collapse" data-parent="#accordion" href="#general-01">
                                        <table>
                                            <thead></thead>
                                            <tbody>
                                                <tr>
                                                    <td width="50%" class="jsearch-field">
                                                        <strong>Key <span class="text-system">(application_id)</span></strong> 
                                                    </td>
                                                    <td width="30%">
                                                        <strong>Data Type <span class="text-system">String</span></strong> 
                                                    </td>
                                                    <td width="15%">
                                                        <strong>Value <span class="text-system">34</span></strong> 
                                                    </td>
                                                    <td width="5%" class="text-right">
                                                        <span class="panel-controls">
                                                            <a href="#" class="icon mdi mdi-delete fs18"></a>
                                                        </span>    
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>  
                                    </a>
                               </div><!--/.panel-heading -->
                               <div id="general-01" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <div class="row mb15" id="spy1">
                                            <div class="col-md-6">
                                                <label class="field-label cus-lbl">Language</label>
                                                <label class="field select">
                                                    <select id="language" name="language">
                                                        <option value="">Choose One</option>
                                                        <option value="en" selected="selected">English (en)</option>
                                                        <option value="fr">French (fr)</option>
                                                        <option value="ar">Arabic (ar)</option>
                                                        <option value="es">Spanish (es)</option>
                                                    </select>
                                                    <i class="arrow"></i>
                                                </label>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="field-label cus-lbl">Data Type</label>
                                                <label for="lastname" class="field select">
                                                    <select id="language" name="language">
                                                        <option value="">Choose One</option>
                                                        <option value="integer" selected="selected">Integer</option>
                                                        <option value="string">String</option>
                                                        <option value="float">Float</option>
                                                        <option value="bool">Bool</option>
                                                    </select>
                                                    <i class="arrow"></i>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="row" id="spy1">
                                            <div class="col-md-6">
                                                <label class="field-label cus-lbl">Key</label>
                                                <label for="lastname" class="field">
                                                    <input type="text" name="from" id="from" class="gui-input" placeholder="">
                                                </label>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="field-label cus-lbl">Value</label>
                                                <label for="lastname" class="field">
                                                    <input type="text" name="from" id="from" class="gui-input" placeholder="">
                                                </label>
                                            </div>
                                            <div class="col-md-4">
                                            </div>
                                        </div>
                                    </div><!--/.panel-body -->
                               </div><!--/.panel-collapse -->
                            </div><!-- /.panel -->
                            <div class="panel panel-default mb10 jsearch-row">
                               <div class="panel-heading">
                                    <a class="panel-title" data-toggle="collapse" data-parent="#accordion" href="#general-02">
                                        <table>
                                            <thead></thead>
                                            <tbody>
                                                <tr>
                                                    <td width="50%" class="key-search">
                                                        <strong>Key <span class="text-system">(app_name)</span></strong> 
                                                    </td>
                                                    <td width="30%">
                                                        <strong>Data Type <span class="text-system">String</span></strong> 
                                                    </td>
                                                    <td width="15%">
                                                        <strong>Value <span class="text-system">34</span></strong> 
                                                    </td>
                                                    <td width="5%" class="text-right">
                                                        <span class="panel-controls">
                                                            <a href="#" class="icon mdi mdi-delete fs18"></a>
                                                        </span>    
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </a>
                               </div><!--/.panel-heading -->
                               <div id="general-02" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <div class="row mb15" id="spy1">
                                            <div class="col-md-6">
                                                <label class="field-label cus-lbl">Language</label>
                                                <label class="field select">
                                                    <select id="language" name="language">
                                                        <option value="">Choose One</option>
                                                        <option value="en" selected="selected">English (en)</option>
                                                        <option value="fr">French (fr)</option>
                                                        <option value="ar">Arabic (ar)</option>
                                                        <option value="es">Spanish (es)</option>
                                                    </select>
                                                    <i class="arrow"></i>
                                                </label>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="field-label cus-lbl">Data Type</label>
                                                <label for="lastname" class="field select">
                                                    <select id="language" name="language">
                                                        <option value="">Choose One</option>
                                                        <option value="integer" selected="selected">Integer</option>
                                                        <option value="string">String</option>
                                                        <option value="float">Float</option>
                                                        <option value="bool">Bool</option>
                                                    </select>
                                                    <i class="arrow"></i>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="row" id="spy1">
                                            <div class="col-md-6">
                                                <label class="field-label cus-lbl">Key</label>
                                                <label for="lastname" class="field">
                                                    <input type="text" name="from" id="from" class="gui-input" placeholder="">
                                                </label>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="field-label cus-lbl">Value</label>
                                                <label for="lastname" class="field">
                                                    <input type="text" name="from" id="from" class="gui-input" placeholder="">
                                                </label>
                                            </div>
                                            <div class="col-md-4">
                                            </div>
                                        </div>
                                    </div><!--/.panel-body -->
                               </div><!--/.panel-collapse -->
                            </div><!-- /.panel -->
                            <div class="panel panel-default mb10 jsearch-row">
                               <div class="panel-heading">
                                    <a class="panel-title" data-toggle="collapse" data-parent="#accordion" href="#general-03">
                                        <table>
                                            <thead></thead>
                                            <tbody>
                                                <tr>
                                                    <td width="50%" class="jsearch-field">
                                                        <strong>Key <span class="text-system">(Black)</span></strong> 
                                                    </td>
                                                    <td width="30%">
                                                        <strong>Data Type <span class="text-system">String</span></strong> 
                                                    </td>
                                                    <td width="15%">
                                                        <strong>Value <span class="text-system">34</span></strong> 
                                                    </td>
                                                    <td width="5%" class="text-right">
                                                        <span class="panel-controls">
                                                            <a href="#" class="icon mdi mdi-delete fs18"></a>
                                                        </span>    
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </a>
                               </div><!--/.panel-heading -->
                               <div id="general-03" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <div class="row mb15" id="spy1">
                                            <div class="col-md-6">
                                                <label class="field-label cus-lbl">Language</label>
                                                <label class="field select">
                                                    <select id="language" name="language">
                                                        <option value="">Choose One</option>
                                                        <option value="en" selected="selected">English (en)</option>
                                                        <option value="fr">French (fr)</option>
                                                        <option value="ar">Arabic (ar)</option>
                                                        <option value="es">Spanish (es)</option>
                                                    </select>
                                                    <i class="arrow"></i>
                                                </label>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="field-label cus-lbl">Data Type</label>
                                                <label for="lastname" class="field select">
                                                    <select id="language" name="language">
                                                        <option value="">Choose One</option>
                                                        <option value="integer" selected="selected">Integer</option>
                                                        <option value="string">String</option>
                                                        <option value="float">Float</option>
                                                        <option value="bool">Bool</option>
                                                    </select>
                                                    <i class="arrow"></i>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="row" id="spy1">
                                            <div class="col-md-6">
                                                <label class="field-label cus-lbl">Key</label>
                                                <label for="lastname" class="field">
                                                    <input type="text" name="from" id="from" class="gui-input" placeholder="">
                                                </label>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="field-label cus-lbl">Value</label>
                                                <label for="lastname" class="field">
                                                    <input type="text" name="from" id="from" class="gui-input" placeholder="">
                                                </label>
                                            </div>
                                            <div class="col-md-4">
                                            </div>
                                        </div>
                                    </div><!--/.panel-body -->
                               </div><!--/.panel-collapse -->
                            </div><!-- /.panel -->
                        </div><!-- /.panel-group -->
                    </div>   
                    <div id="text_tab" class="tab-pane">
                        <div class="panel-group mb10" id="accordion-2">
                            <div class="panel panel-default mb10 jsearch-row">
                               <div class="panel-heading">
                                    <a class="panel-title" data-toggle="collapse" data-parent="#accordion-2" href="#text-01">
                                        <table>
                                            <thead></thead>
                                            <tbody>
                                                <tr>
                                                    <td width="50%" class="jsearch-field">
                                                        <strong>Key <span class="text-system">(application_id)</span></strong> 
                                                    </td>
                                                    <td width="30%">
                                                        <strong>Data Type <span class="text-system">String</span></strong> 
                                                    </td>
                                                    <td width="15%">
                                                        <strong>Value <span class="text-system">34</span></strong> 
                                                    </td>
                                                    <td width="5%" class="text-right">
                                                        <span class="panel-controls">
                                                            <a href="#" class="icon mdi mdi-delete fs18"></a>
                                                        </span>    
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>  
                                    </a>
                               </div><!--/.panel-heading -->
                               <div id="text-01" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <div class="row mb15" id="spy1">
                                            <div class="col-md-6">
                                                <label class="field-label cus-lbl">Language</label>
                                                <label class="field select">
                                                    <select id="language" name="language">
                                                        <option value="">Choose One</option>
                                                        <option value="en" selected="selected">English (en)</option>
                                                        <option value="fr">French (fr)</option>
                                                        <option value="ar">Arabic (ar)</option>
                                                        <option value="es">Spanish (es)</option>
                                                    </select>
                                                    <i class="arrow"></i>
                                                </label>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="field-label cus-lbl">Data Type</label>
                                                <label for="lastname" class="field select">
                                                    <select id="language" name="language">
                                                        <option value="">Choose One</option>
                                                        <option value="integer" selected="selected">Integer</option>
                                                        <option value="string">String</option>
                                                        <option value="float">Float</option>
                                                        <option value="bool">Bool</option>
                                                    </select>
                                                    <i class="arrow"></i>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="row" id="spy1">
                                            <div class="col-md-6">
                                                <label class="field-label cus-lbl">Key</label>
                                                <label for="lastname" class="field">
                                                    <input type="text" name="from" id="from" class="gui-input" placeholder="">
                                                </label>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="field-label cus-lbl">Value</label>
                                                <label for="lastname" class="field">
                                                    <input type="text" name="from" id="from" class="gui-input" placeholder="">
                                                </label>
                                            </div>
                                            <div class="col-md-4">
                                            </div>
                                        </div>
                                    </div><!--/.panel-body -->
                               </div><!--/.panel-collapse -->
                            </div><!-- /.panel -->
                            <div class="panel panel-default mb10 jsearch-row">
                               <div class="panel-heading">
                                    <a class="panel-title" data-toggle="collapse" data-parent="#accordion-2" href="#text-02">
                                        <table>
                                            <thead></thead>
                                            <tbody>
                                                <tr>
                                                    <td width="50%" class="jsearch-field">
                                                        <strong>Key <span class="text-system">(app_name)</span></strong> 
                                                    </td>
                                                    <td width="30%">
                                                        <strong>Data Type <span class="text-system">String</span></strong> 
                                                    </td>
                                                    <td width="15%">
                                                        <strong>Value <span class="text-system">34</span></strong> 
                                                    </td>
                                                    <td width="5%" class="text-right">
                                                        <span class="panel-controls">
                                                            <a href="#" class="icon mdi mdi-delete fs18"></a>
                                                        </span>    
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </a>
                               </div><!--/.panel-heading -->
                               <div id="text-02" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <div class="row mb15" id="spy1">
                                            <div class="col-md-6">
                                                <label class="field-label cus-lbl">Language</label>
                                                <label class="field select">
                                                    <select id="language" name="language">
                                                        <option value="">Choose One</option>
                                                        <option value="en" selected="selected">English (en)</option>
                                                        <option value="fr">French (fr)</option>
                                                        <option value="ar">Arabic (ar)</option>
                                                        <option value="es">Spanish (es)</option>
                                                    </select>
                                                    <i class="arrow"></i>
                                                </label>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="field-label cus-lbl">Data Type</label>
                                                <label for="lastname" class="field select">
                                                    <select id="language" name="language">
                                                        <option value="">Choose One</option>
                                                        <option value="integer" selected="selected">Integer</option>
                                                        <option value="string">String</option>
                                                        <option value="float">Float</option>
                                                        <option value="bool">Bool</option>
                                                    </select>
                                                    <i class="arrow"></i>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="row" id="spy1">
                                            <div class="col-md-6">
                                                <label class="field-label cus-lbl">Key</label>
                                                <label for="lastname" class="field">
                                                    <input type="text" name="from" id="from" class="gui-input" placeholder="">
                                                </label>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="field-label cus-lbl">Value</label>
                                                <label for="lastname" class="field">
                                                    <input type="text" name="from" id="from" class="gui-input" placeholder="">
                                                </label>
                                            </div>
                                            <div class="col-md-4">
                                            </div>
                                        </div>
                                    </div><!--/.panel-body -->
                               </div><!--/.panel-collapse -->
                            </div><!-- /.panel -->
                            <div class="panel panel-default mb10 jsearch-row">
                               <div class="panel-heading">
                                    <a class="panel-title" data-toggle="collapse" data-parent="#accordion-2" href="#text-03">
                                        <table>
                                            <thead></thead>
                                            <tbody>
                                                <tr>
                                                    <td width="50%" class="jsearch-field">
                                                        <strong>Key <span class="text-system">(Black)</span></strong> 
                                                    </td>
                                                    <td width="30%">
                                                        <strong>Data Type <span class="text-system">String</span></strong> 
                                                    </td>
                                                    <td width="15%">
                                                        <strong>Value <span class="text-system">34</span></strong> 
                                                    </td>
                                                    <td width="5%" class="text-right">
                                                        <span class="panel-controls">
                                                            <a href="#" class="icon mdi mdi-delete fs18"></a>
                                                        </span>    
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </a>
                               </div><!--/.panel-heading -->
                               <div id="text-03" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <div class="row mb15" id="spy1">
                                            <div class="col-md-6">
                                                <label class="field-label cus-lbl">Language</label>
                                                <label class="field select">
                                                    <select id="language" name="language">
                                                        <option value="">Choose One</option>
                                                        <option value="en" selected="selected">English (en)</option>
                                                        <option value="fr">French (fr)</option>
                                                        <option value="ar">Arabic (ar)</option>
                                                        <option value="es">Spanish (es)</option>
                                                    </select>
                                                    <i class="arrow"></i>
                                                </label>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="field-label cus-lbl">Data Type</label>
                                                <label for="lastname" class="field select">
                                                    <select id="language" name="language">
                                                        <option value="">Choose One</option>
                                                        <option value="integer" selected="selected">Integer</option>
                                                        <option value="string">String</option>
                                                        <option value="float">Float</option>
                                                        <option value="bool">Bool</option>
                                                    </select>
                                                    <i class="arrow"></i>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="row" id="spy1">
                                            <div class="col-md-6">
                                                <label class="field-label cus-lbl">Key</label>
                                                <label for="lastname" class="field">
                                                    <input type="text" name="from" id="from" class="gui-input" placeholder="">
                                                </label>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="field-label cus-lbl">Value</label>
                                                <label for="lastname" class="field">
                                                    <input type="text" name="from" id="from" class="gui-input" placeholder="">
                                                </label>
                                            </div>
                                            <div class="col-md-4">
                                            </div>
                                        </div>
                                    </div><!--/.panel-body -->
                               </div><!--/.panel-collapse -->
                            </div><!-- /.panel -->
                        </div><!-- /.panel-group -->
                    </div>  
                </div>
            </div>   
        </section>
        <!-- End: Content -->

      <!-- Begin: Page Footer -->
      <footer id="content-footer">
        <div class="row">
          <div class="col-md-12 text-center">
            <span class="footer-legal">Cubix Panel 2.0.1</span>
          </div>
        </div>
      </footer>
      <!-- End: Page Footer -->

    </section>
    <!-- End: Content-Wrapper -->
<?php include_once("footer.php"); ?>
<!-- Style Css -->

<!-- Admin Forms CSS -->
<link rel="stylesheet" type="text/css" href="assets/admin-tools/admin-forms/css/admin-forms.css">

<!-- Jquery Search -->
<script src="vendor/plugins/search/jquery.search.min.js"></script>

<script type="text/javascript">
	$(document).ready(function() {
		
		// Page Load Ajax
		$('button.add-new-btn').on('click', function(e){
			e.preventDefault();
			var pageRef = $(this).attr('href');
			callPage(pageRef)

		});
		function callPage(pageRefInput){
			// Using the core $.ajax() method
			$.ajax({
				url: pageRefInput,
				type: "GET",
				dataType : 'text',
				success: function( response ) {
				  console.log('the page was loaded', response);
				  $('.content').html(response);
				},
			 
				error: function( error ) {
				  console.log('the page was NOT loaded', error);
				},
			 
				complete: function( xhr, status ) {
				  console.log("The request is complete!");
				}
			});    
		}
        
        /* Tab All Active */  
        $('#tabAll').on('click',function(){
          $('#tabAll').parent().addClass('active');  
          $('.tab-pane').addClass('active in');  
          $('[data-toggle="tab"]').parent().removeClass('active');
        });
        
        /* Search Jquery */  
		$("#jquery-search-sample").jsearch({minLength: 2});

	});
	
</script>