<?php
    ob_start();
    include("header.php");
    headerDefault();
    $buffer=ob_get_contents();
    ob_end_clean();

    $buffer=str_replace("%TITLE%","App Setting",$buffer);
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
				   </div>
                </div>
            </div>
        </div>    
        <!-- End: Topbar -->
	  
        <!-- Begin: Content -->
        <section id="content" class="table-layout pn tab-block">
            <div class="tray tray-center">
                <div class="tab-header">
                    <h3 class="mtn">General</h3>
                    <p>Setting up MD Iconic Font can be as simple as adding one line of code to your website - it's like Font Awesome but with Material Design by Google.</p>
                </div>
                <div class="admin-form">
                        <div class="panel-group mb10" id="accordion">
                            <div class="panel panel-default mb10 jsearch-row">
                               <div class="panel-heading">
                                    <a class="panel-title" data-toggle="collapse" data-parent="#accordion" href="#app-setting-01">
                                        <table>
                                            <thead></thead>
                                            <tbody>
                                                <tr>
                                                    <td width="40%" class="jsearch-field">
                                                        <strong>Key <span class="text-system">(qa_question_auto_refresh)</span></strong> 
                                                    </td>
													<td width="40%">
                                                        <strong>Value <span class="text-system">34</span></strong> 
                                                    </td>
                                                    <td width="10%">
                                                        <span data-toggle="tooltip" title="" data-original-title="Template for Admin Password Recovery email confirmation"><i class="icon mdi mdi-help mt2 fs18 text-system"></i></span>
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
                               <div id="app-setting-01" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <div class="row mb15" id="spy1">
                                            <div class="col-md-4">
                                                <label class="field-label cus-lbl">Key</label>
                                                <label for="lastname" class="field">
                                                    <input type="text" name="from" id="from" class="gui-input" placeholder="">
                                                </label>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="field-label cus-lbl">Value</label>
                                                <label for="lastname" class="field">
                                                    <input type="text" name="from" id="from" class="gui-input" placeholder="">
                                                </label>
                                            </div>
											<div class="col-md-4">
                                                <label class="field-label cus-lbl">Hint</label>
                                                <label for="lastname" class="field">
                                                    <input type="text" name="from" id="from" class="gui-input" placeholder="">
                                                </label>
                                            </div>
                                        </div>
                                    </div><!--/.panel-body -->
                               </div><!--/.panel-collapse -->
                            </div><!-- /.panel -->
                            <div class="panel panel-default mb10 jsearch-row">
                               <div class="panel-heading">
                                    <a class="panel-title" data-toggle="collapse" data-parent="#accordion" href="#app-setting-02">
                                        <table>
                                            <thead></thead>
                                            <tbody>
                                                <tr>
                                                    <td width="40%" class="key-search">
                                                        <strong>Key <span class="text-system">(admin_email_name)</span></strong> 
                                                    </td>
													
                                                    <td width="40%">
														<strong>Value <span class="text-system">Team GeoTrivia</span></strong>  
                                                    </td>
													<td width="10%">
                                                       <span data-toggle="tooltip" title="" data-original-title="Template for Admin Password Recovery email confirmation"><i class="icon mdi mdi-help mt2 fs18 text-system"></i></span>
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
                               <div id="app-setting-02" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <div class="row mb15" id="spy1">
                                            <div class="col-md-4">
                                                <label class="field-label cus-lbl">Key</label>
                                                <label for="lastname" class="field">
                                                    <input type="text" name="from" id="from" class="gui-input" placeholder="">
                                                </label>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="field-label cus-lbl">Value</label>
                                                <label for="lastname" class="field">
                                                    <input type="text" name="from" id="from" class="gui-input" placeholder="">
                                                </label>
                                            </div>
											<div class="col-md-4">
                                                <label class="field-label cus-lbl">Hint</label>
                                                <label for="lastname" class="field">
                                                    <input type="text" name="from" id="from" class="gui-input" placeholder="">
                                                </label>
                                            </div>
                                        </div>
                                    </div><!--/.panel-body -->
                               </div><!--/.panel-collapse -->
                            </div><!-- /.panel -->
                            <div class="panel panel-default mb10 jsearch-row">
                               <div class="panel-heading">
                                    <a class="panel-title" data-toggle="collapse" data-parent="#accordion" href="#app-setting-03">
                                        <table>
                                            <thead></thead>
                                            <tbody>
                                                <tr>
                                                    <td width="40%" class="jsearch-field">
                                                        <strong>Key <span class="text-system">(og_schema_share)</span></strong> 
                                                    </td>
                                                    <td width="40%">
                                                        <strong>Value <span class="text-system">glimmer://share</span></strong> 
                                                    </td>
													<td width="10%">
                                                       <span data-toggle="tooltip" title="" data-original-title="Template for Admin Password Recovery email confirmation"><i class="icon mdi mdi-help mt2 fs18 text-system"></i></span>
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
                               <div id="app-setting-03" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <div class="row mb15" id="spy1">
                                            <div class="col-md-4">
                                                <label class="field-label cus-lbl">Key</label>
                                                <label for="lastname" class="field">
                                                    <input type="text" name="from" id="from" class="gui-input" placeholder="">
                                                </label>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="field-label cus-lbl">Value</label>
                                                <label for="lastname" class="field">
                                                    <input type="text" name="from" id="from" class="gui-input" placeholder="">
                                                </label>
                                            </div>
											<div class="col-md-4">
                                                <label class="field-label cus-lbl">Hint</label>
                                                <label for="lastname" class="field">
                                                    <input type="text" name="from" id="from" class="gui-input" placeholder="">
                                                </label>
                                            </div>
                                        </div>
                                    </div><!--/.panel-body -->
                               </div><!--/.panel-collapse -->
                            </div><!-- /.panel -->
                        </div><!-- /.panel-group -->
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