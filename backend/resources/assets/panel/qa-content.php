<?php
    ob_start();
    include("header.php");
    headerDefault();
    $buffer=ob_get_contents();
    ob_end_clean();

    $buffer=str_replace("%TITLE%","Content",$buffer);
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
            <div class="adv-search">
                <div class="topbar-left">
                    <div class="sec-title">
                        <span>Showing</span>
                        <span class="p-list-count op6">(12)</span>
                    </div>
                    <div class="table-tools is_hidden">
                        <button type="button" class="btn-default btn-sm link-unstyled ib" href="#"><span class="icon mdi mdi-delete pr5 fs15"></span> Delete</button>
                        <button type="button" class="btn-default btn-sm link-unstyled ib" href="#" data-toggle="dropdown"><span class="icon mdi mdi-chevron-down fs15"></span></button>
                        <ul class="dropdown-menu list-group dropdown-persist w200" role="menu">
                            <li class="list-group-item">
                                <a href="#" class="animated animated-short fadeInUp"><i class="fa fa-circle text-success fs12 pr5"></i>Active</a>
                            </li>
                            <li class="list-group-item">
                                <a href="#" class="animated animated-short fadeInUp"><i class="fa fa-circle text-danger fs12 pr5"></i>Inactive</a>
                            </li>
                            <li class="list-group-item">
                                <a href="#" class="animated animated-short fadeInUp"><span class="icon mdi mdi-block-alt pr5 fs15"></span>Ban</a>
                            </li>
                        </ul>
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
                        <button type="button" class="accordion-toggle mr5 btn-default btn-sm accordion-icon link-unstyled collapsed ib" data-toggle="collapse" data-parent="#accordion" href="#adv-search"><span class="icon mdi mdi-menu"></span></button>
                        <button type="button" class="btn-default btn-sm add-new-btn link-unstyled ib" href="add-new-content.php"><span class="icon mdi mdi-plus pr5 fs15"></span> Add Content</button>
                    </div>
                </div>
            </div>    
            <div id="adv-search" class="panel-collapse collapse" style="height: auto;">
                <div class="panel-body bg-light lighter br-h-n br-t-n search-filters"> 
					<div class="row">
						<div class="col-md-8">
							<div class="row">
								<div class="col-md-6 multiSelect">
									<label>Organization Name</label>
									<select id="multiselect2" multiple="multiple" >
										<option value="cheese">Cheese</option>
										<option value="tomatoes">Tomatoes</option>
										<option value="mozarella">Mozzarella</option>
										<option value="mushrooms">Mushrooms</option>
										<option value="pepperoni">Pepperoni</option>
										<option value="onions">Onions</option>
									</select>
								</div>
								<div class="col-md-6">
									<label>Status</label>
									<input type="text" id="" class="form-control" placeholder="Name">
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="mt25 clearfix">
								<button type="button" class="btn btn-default btn-md col-sm-6 mr5 btn-clear">Clear</button>
								<button type="button" class="btn btn-theme btn-md col-sm-6 btn-search">Search</button>
							</div>	
						</div>
					</div>
				</div>
            </div>
        </div>
        <!-- End: Topbar -->

        <!-- Begin: Content -->
        <section id="content" class="pn">
        <div class="panel">
			<table class="table table-hover" id="datatable2" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th style="width:5%;" class="nosort">
							<div class="checkbox-t">
							  <input type="checkbox" id="selectAll">
							  <label for="selectAll"></label>
							</div>
						</th>
						<th style="width:30%;">Content Title</th>
						<th style="width:15%;">Content</th>
						<th style="width:15%;">Content Type</th>
						<th style="width:15%;">Language</th>
						<th style="width:12%;" class="nosort"></th>
						<th class="text-right nosort"><button type="button" class="btn-default btn-sm accordion-icon link-unstyled" href="#"><span class="icon mdi mdi-refresh-alt fs15"></span></button></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<div class="checkbox-t">
							  <input type="checkbox" id="check01">
							  <label for="check01"></label>
							</div>
						</td>
						<td>
							Question : 1 Question : 1 Question : 1 Question : 1 
						</td>
						<td>Hello</td>
						<td>Text Based</td>
						<td>English</td>
						<td class="hv-btns text-right">
							<button type="button" class="btn btn-default btn-sm" href="#" title="View" data-toggle="modal" data-target=".view-popup"><span class="icon mdi mdi-eye fs13"></span></button>
							<button type="button" class="btn btn-default btn-sm" href="#" title="Edit"><span class="icon mdi mdi-edit fs13"></span></button>
							<button type="button" class="btn btn-default btn-sm" href="#" title="Delete"><span class="icon mdi mdi-delete fs13"></span></button>
						</td>
						<td class="text-right">
							<span class="mdi mdi-chevron-right fs20"></span>
						</td>
					</tr>
					<tr>
						<td>
							<div class="checkbox-t">
							  <input type="checkbox" id="check01">
							  <label for="check01"></label>
							</div>
						</td>
						<td>
							Answer : 1
						</td>
						<td>Answer</td>
						<td>Text Based</td>
						<td>English</td>
						<td class="hv-btns text-right">
							<button type="button" class="btn btn-default btn-sm" href="#" title="Edit" data-toggle="modal" data-target="#myModal"><span class="icon mdi mdi-eye fs13"></span></button>
							<button type="button" class="btn btn-default btn-sm" href="#" title="Edit"><span class="icon mdi mdi-edit fs13"></span></button>
							<button type="button" class="btn btn-default btn-sm" href="#" title="Delete"><span class="icon mdi mdi-delete fs13"></span></button>
						</td>
						<td class="text-right">
							<span class="mdi mdi-chevron-right fs20"></span>
						</td>
					</tr>
					<tr>
						<td>
							<div class="checkbox-t">
							  <input type="checkbox" id="check01">
							  <label for="check01"></label>
							</div>
						</td>
						<td>
							Answer : 1
						</td>
						<td>Answer</td>
						<td>Text Based</td>
						<td>English</td>
						<td class="hv-btns text-right">
							<button type="button" class="btn btn-default btn-sm" href="#" title="Edit" data-toggle="modal" data-target="#myModal"><span class="icon mdi mdi-eye fs13"></span></button>
							<button type="button" class="btn btn-default btn-sm" href="#" title="Edit"><span class="icon mdi mdi-edit fs13"></span></button>
							<button type="button" class="btn btn-default btn-sm" href="#" title="Delete"><span class="icon mdi mdi-delete fs13"></span></button>
						</td>
						<td class="text-right">
							<span class="mdi mdi-chevron-right fs20"></span>
						</td>
					</tr>
					<tr>
						<td>
							<div class="checkbox-t">
							  <input type="checkbox" id="check01">
							  <label for="check01"></label>
							</div>
						</td>
						<td>
							Answer : 1
						</td>
						<td>Answer</td>
						<td>Text Based</td>
						<td>English</td>
						<td class="hv-btns text-right">
							<button type="button" class="btn btn-default btn-sm" href="#" title="Edit" data-toggle="modal" data-target="#myModal"><span class="icon mdi mdi-eye fs13"></span></button>
							<button type="button" class="btn btn-default btn-sm" href="#" title="Edit"><span class="icon mdi mdi-edit fs13"></span></button>
							<button type="button" class="btn btn-default btn-sm" href="#" title="Delete"><span class="icon mdi mdi-delete fs13"></span></button>
						</td>
						<td class="text-right">
							<span class="mdi mdi-chevron-right fs20"></span>
						</td>
					</tr>
					<tr>
						<td>
							<div class="checkbox-t">
							  <input type="checkbox" id="check01">
							  <label for="check01"></label>
							</div>
						</td>
						<td>
							Answer : 1
						</td>
						<td>Answer</td>
						<td>Text Based</td>
						<td>English</td>
						<td class="hv-btns text-right">
							<button type="button" class="btn btn-default btn-sm" href="#" title="Edit" data-toggle="modal" data-target="#myModal"><span class="icon mdi mdi-eye fs13"></span></button>
							<button type="button" class="btn btn-default btn-sm" href="#" title="Edit"><span class="icon mdi mdi-edit fs13"></span></button>
							<button type="button" class="btn btn-default btn-sm" href="#" title="Delete"><span class="icon mdi mdi-delete fs13"></span></button>
						</td>
						<td class="text-right">
							<span class="mdi mdi-chevron-right fs20"></span>
						</td>
					</tr>
					<tr>
						<td>
							<div class="checkbox-t">
							  <input type="checkbox" id="check01">
							  <label for="check01"></label>
							</div>
						</td>
						<td>
							Answer : 1
						</td>
						<td>Answer</td>
						<td>Text Based</td>
						<td>English</td>
						<td class="hv-btns text-right">
							<button type="button" class="btn btn-default btn-sm" href="#" title="Edit" data-toggle="modal" data-target="#myModal"><span class="icon mdi mdi-eye fs13"></span></button>
							<button type="button" class="btn btn-default btn-sm" href="#" title="Edit"><span class="icon mdi mdi-edit fs13"></span></button>
							<button type="button" class="btn btn-default btn-sm" href="#" title="Delete"><span class="icon mdi mdi-delete fs13"></span></button>
						</td>
						<td class="text-right">
							<span class="mdi mdi-chevron-right fs20"></span>
						</td>
					</tr>
				</tbody>
			</table>
        </div>
     </section>
        <!-- End: Content -->

        <!-- Begin: Page Footer -->
        <footer id="content-footer">
            <div class="row">
              <div class="col-md-6">
                <span class="footer-legal">? 2017 Cubix Panel</span>
              </div>
              <div class="col-md-6 text-right">
                <span class="footer-meta">10GB of <b>250GB</b> Free</span>
                <a href="#content" class="footer-return-top">
                  <span class="fa fa-arrow-up"></span>
                </a>
              </div>
            </div>
        </footer>
        <!-- End: Page Footer -->

    </section>
    
	<!-- Detailed View Popup -->
		<div class="modal fade view-popup">
		  <div class="modal-dialog" role="document">
			<div class="modal-content">
			  <div class="modal-header">
				<h4 class="modal-title">Detailed View</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				  <span aria-hidden="true">&times;</span>
				</button>
			  </div>
			  <div class="modal-body detail-view">
				<table width="100%" >
					<tr>
						<th>Content Title</th>
						<td>
							<span class="cell-tittle">Question : 1</span>
						</td>
					</tr>
					<tr>
						<th width="30%">Content</th>
						<td width="70%">Hello</td>
					</tr>
					<tr>
						<th>Content Type</th>
						<td>Text Based</td>
					</tr>
					<tr>
						<th>Language</th>
						<td>English</td>
					</tr>
				</table>
			  </div>
			</div>
		  </div>
		</div>
	
	<!-- End: Content-Wrapper -->
<?php include_once("footer.php"); ?>
<!-- Style Css -->

<!-- Required Plugin CSS -->
<link rel="stylesheet" type="text/css" href="vendor/plugins/datepicker/css/bootstrap-datetimepicker.css">
<link rel="stylesheet" type="text/css" href="vendor/plugins/daterange/daterangepicker.css">

<!-- Page Plugins via CDN -->
<script src="vendor/plugins/moment/moment.min.js"></script>

<!-- Datatables -->
<script src="./vendor/plugins/datatables/media/js/datatables.min.js"></script>
<script src="./vendor/plugins/datatables/ellipsis.js"></script>

<!-- ckeditor -->
<script src="vendor/plugins/ckeditor/ckeditor.js"></script>

<!-- Page Plugins -->
<script src="vendor/plugins/xeditable/js/bootstrap-editable.js"></script>
<script src="vendor/plugins/daterange/daterangepicker.js"></script>
<script src="vendor/plugins/datepicker/js/bootstrap-datetimepicker.js"></script>


<script type="text/javascript">
	$(document).ready(function() {	

		// Init Boostrap Multiselect
		$('#multiselect2').multiselect({
			includeSelectAllOption: true
		});
		
		// Init Boostrap Multiselect
		$('#multiselect3').multiselect({
			includeSelectAllOption: true
		});
		
        // Init daterange plugin
        $('#daterangepicker1').daterangepicker();

        // Init datetimepicker - fields
        $('#datetimepicker1').datetimepicker();
        		
		
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
		
		// No Sorting
		$('#datatable2').DataTable({
			"bLengthChange":   false,
			"bInfo": false,
			"searching": false,
			"bPaginate": false,
			"responsive": true,			
			"order": [],	
			"columnDefs": [{
				"targets": [ 1, 2 ],
                "render": $.fn.dataTable.render.ellipsis( 30 )
				
			},
			{
				"targets"  : 'nosort',
				  "orderable": false,
			}]
			
		});
        
        $('.ellipsis').tooltip()
		
	});
	
</script>