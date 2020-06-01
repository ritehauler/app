<?php
    ob_start();
    include("header.php");
    headerDefault();
    $buffer=ob_get_contents();
    ob_end_clean();

    $buffer=str_replace("%TITLE%","Assets Manager",$buffer);
    echo $buffer;
?>
	
	
    <!-- Start: Content-Wrapper -->
    <section id="content_wrapper">
      
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


        <!-- Begin: Content -->
        <section id="content">
			<div class="col-xs-12 mb20">
				<div class="tray-bin  pn mn" style="min-height: 250px;">
					<form action="/file-upload" class="dropzone dropzone-sm" id="dropZone" />
						<div class="fallback">
							<input name="file" type="file" multiple="" />
						</div>
					</form>
				</div>
			</div>
			<div id="content" class="table-layout animated fadeIn col-XS-12 gallery-sorting ">
				<div class="tray tray-center pn va-t">
					<div class="mh15 pt15">
						<div class="row r-vm">
							<div class="col-xs-7">
								<div class="sec-title">
									<span>Showing</span>
									<span class="p-list-count op6">(12)</span>
								</div>
							</div>
							<div class="col-xs-5 text-right">
								<div class="mix-controls ib pull-right">
									<form class="controls" id="select-filters">
										<!-- We can add an unlimited number of "filter groups" using the following format: -->
										<div class="btn-group ib">
											<div class="btn-group pull-right">
												<fieldset>
													<select id="filter2">
														<option value="">All Labels</option>
														<option value=".image">Image</option>
														<option value=".video">Video</option>
														<option value=".audio">Audio</option>
													</select>
												</fieldset>
											</div>
										</div>
									</form>
								</div>
								<div class="pull-right mr5" style="width: 70%;">
									<input type="text" class="form-control" id="filter" placeholder="Search..." />
								</div>
							</div>
						</div>
						<div class="mix-controls hidden">
							<form class="controls admin-form" id="checkbox-filters">
								<!-- We can add an unlimited number of "filter groups" using the following format: -->
								<fieldset class="">
									<h4>Cars</h4>
									<label class="option block mt10">
										<input type="checkbox" value=".circle">
										<span class="checkbox"></span>Circle
									</label>
								</fieldset>
								<button id="mix-reset2">Clear All</button>
							</form>
						</div>
					</div>
					<div id="mix-container">
						<div class="fail-message">
							<span>No items were found matching the selected filters</span>
						</div>
						<div class="mix video folder1">
							<div class="panel p6 pbn">
								<div class="of-h">
									<div class="type-icon-wrap">
										<a class="holder-style p10 text-center olb asset-thumb" href="#">
                                            <div class="d-v-table">
                                                <div class="d-v-cell">
								                    <span class="icon mdi mdi-videocam fs60"></span>
                                                </div>    
                                            </div>    
										</a>
									</div>
									<div class="row table-layout">
										<div class="col-xs-12 va-m pn">
											<h6 class="pull-left">video.mp4</h6>
											<button type="button" class="btn btn-none btn-sm pull-right" href="#" title="Delete"><span class="icon mdi mdi-delete fs18"></span></button>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="mix audio folder2">
							<div class="panel p6 pbn">
								<div class="of-h">
									<div class="type-icon-wrap">
										<a class="holder-style p10 text-center olb asset-thumb" href="#">
											<div class="d-v-table">
                                                <div class="d-v-cell">
                                                    <span class="icon mdi mdi-volume-up fs60"></span>
                                                </div>    
                                            </div>
										</a>
									</div>
									<div class="row table-layout">
										<div class="col-xs-12 va-m pn">
											<h6 class="pull-left">audio</h6>
											<button type="button" class="btn btn-none btn-sm pull-right" href="#" title="Delete"><span class="icon mdi mdi-delete fs18"></span></button>
										
										</div>
										
									</div>
								</div>
							</div>
						</div>
						<div class="mix image folder3">
							<div class="panel p6 pbn">
								<div class="of-h">
									<img src="assets/img/stock/3.jpg" class="img-responsive asset-thumb" title="why_are_locks.jpg">
									<div class="row table-layout">
										<div class="col-xs-12 va-m pn">
											<h6 class="pull-left">why_are_locks.jpg</h6>
											<button type="button" class="btn btn-none btn-sm pull-right" href="#" title="Delete"><span class="icon mdi mdi-delete fs18"></span></button>
										
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="mix video folder1">
							<div class="panel p6 pbn">
								<div class="of-h">
									<div class="type-icon-wrap">
										<a class="holder-style p10 text-center olb asset-thumb" href="#">
											<div class="d-v-table">
                                                <div class="d-v-cell">
								                    <span class="icon mdi mdi-videocam fs60"></span>
                                                </div>    
                                            </div>
										</a>
									</div>
									<div class="row table-layout">
										<div class="col-xs-12 va-m pn">
											<h6 class="pull-left">video.mp4</h6>
											<button type="button" class="btn btn-none btn-sm pull-right" href="#" title="Delete"><span class="icon mdi mdi-delete fs18"></span></button>
										
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="mix audio folder2">
							<div class="panel p6 pbn">
								<div class="of-h">
									<div class="type-icon-wrap">
										<a class="holder-style p10 text-center olb asset-thumb" href="#">
											<div class="d-v-table">
                                                <div class="d-v-cell">
                                                    <span class="icon mdi mdi-volume-up fs60"></span>
                                                </div>    
                                            </div>
										</a>
									</div>
									<div class="row table-layout">
										<div class="col-xs-12 va-m pn">
											<h6 class="pull-left">audio</h6>
											<button type="button" class="btn btn-none btn-sm pull-right" href="#" title="Delete"><span class="icon mdi mdi-delete fs18"></span></button>
										
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="mix video folder3">
							<div class="panel p6 pbn">
								<div class="of-h">
									<div class="type-icon-wrap">
										<a class="holder-style p10 text-center olb asset-thumb" href="#">
											<div class="d-v-table">
                                                <div class="d-v-cell">
								                    <span class="icon mdi mdi-videocam fs60"></span>
                                                </div>    
                                            </div>
										</a>
									</div>
									<div class="row table-layout">
										<div class="col-xs-12 va-m pn">
											<h6 class="pull-left">video.mp4</h6>
											<button type="button" class="btn btn-none btn-sm pull-right" href="#" title="Delete"><span class="icon mdi mdi-delete fs18"></span></button>
										
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="mix image folder1">
							<div class="panel p6 pbn">
								<div class="of-h">
									<img src="assets/img/stock/2.jpg" class="img-responsive asset-thumb" title="yosemite_sun.jpg">
									<div class="row table-layout">
										<div class="col-xs-12 va-m pn">
											<h6 class="pull-left">yosemite_sun.jpg</h6>
											<button type="button" class="btn btn-none btn-sm pull-right" href="#" title="Delete"><span class="icon mdi mdi-delete fs18"></span></button>
										
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="mix audio folder2">
							<div class="panel p6 pbn">
								<div class="of-h">
									<div class="type-icon-wrap">
										<a class="holder-style p10 text-center olb asset-thumb" href="#">
											<div class="d-v-table">
                                                <div class="d-v-cell">
                                                    <span class="icon mdi mdi-volume-up fs60"></span>
                                                </div>    
                                            </div>
										</a>
									</div>
									<div class="row table-layout">
										<div class="col-xs-12 va-m pn">
											<h6 class="pull-left">audio</h6>
											<button type="button" class="btn btn-none btn-sm pull-right" href="#" title="Delete"><span class="icon mdi mdi-delete fs18"></span></button>
										
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="mix image folder1">
							<div class="panel p6 pbn">
								<div class="of-h">
									<img src="assets/img/stock/1.jpg" class="img-responsive asset-thumb" title="lost_typewritter.jpg">
								</div>
								<div class="row table-layout">
									<div class="col-xs-12 va-m pn">
										<h6 class="pull-left">lost_typewritter.jpg</h6>
										<button type="button" class="btn btn-none btn-sm pull-right" href="#" title="Delete"><span class="icon mdi mdi-delete fs18"></span></button>
										
									</div>
								</div>
							</div>
						</div>
						<div class="mix video folder2">
							<div class="panel p6 pbn">
								<div class="of-h">
									<div class="type-icon-wrap">
										<a class="holder-style p10 text-center olb asset-thumb" href="#">
											<div class="d-v-table">
                                                <div class="d-v-cell">
								                    <span class="icon mdi mdi-videocam fs60"></span>
                                                </div>    
                                            </div>
										</a>
									</div>
									<div class="row table-layout">
										<div class="col-xs-12 va-m pn">
											<h6 class="pull-left">video.mp4</h6>
											<button type="button" class="btn btn-none btn-sm pull-right" href="#" title="Delete"><span class="icon mdi mdi-delete fs18"></span></button>
										
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="mix image folder3">
							<div class="panel p6 pbn">
								<div class="of-h">
									<img src="assets/img/stock/3.jpg" class="img-responsive asset-thumb" title="why_are_locks.jpg">
									<div class="row table-layout">
										<div class="col-xs-12 va-m pn">
											<h6 class="pull-left">why_are_locks.jpg</h6>
											<button type="button" class="btn btn-none btn-sm pull-right" href="#" title="Delete"><span class="icon mdi mdi-delete fs18"></span></button>
										
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="mix video folder1">
							<div class="panel p6 pbn">
								<div class="of-h">
									<div class="type-icon-wrap">
										<a class="holder-style p10 text-center olb asset-thumb" href="#">
											<div class="d-v-table">
                                                <div class="d-v-cell">
								                    <span class="icon mdi mdi-videocam fs60"></span>
                                                </div>    
                                            </div>
										</a>
									</div>
									<div class="row table-layout">
										<div class="col-xs-12 va-m pn">
											<h6 class="pull-left">video.mp4</h6>
											<button type="button" class="btn btn-none btn-sm pull-right" href="#" title="Delete"><span class="icon mdi mdi-delete fs18"></span></button>
										
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="gap"></div>
						<div class="gap"></div>
						<div class="gap"></div>
						<div class="gap"></div>
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

<!-- Required Plugin CSS -->
<link rel="stylesheet" type="text/css" href="vendor/plugins/datepicker/css/bootstrap-datetimepicker.css">
<link rel="stylesheet" type="text/css" href="vendor/plugins/daterange/daterangepicker.css">

<!-- Page Plugins via CDN -->
<script src="vendor/plugins/moment/moment.min.js"></script>

<!-- ckeditor -->
<script src="vendor/plugins/ckeditor/ckeditor.js"></script>

<!-- Dropzone JS -->
<script type="text/javascript" src="vendor/plugins/dropzone/dropzone.min.js"></script>

<!-- Page Plugins -->
<script type="text/javascript" src="vendor/plugins/magnific/jquery.magnific-popup.js"></script>

<!-- Mixitup Plugin (CDN) -->
<script src="vendor/plugins/mixitup/jquery.mixitup.min.js"></script>


<!-- Page Plugins -->
<script src="vendor/plugins/xeditable/js/bootstrap-editable.js"></script>
<script src="vendor/plugins/daterange/daterangepicker.js"></script>
<script src="vendor/plugins/datepicker/js/bootstrap-datetimepicker.js"></script>


<script type="text/javascript">
    
    // Assets Thumb
    $('.asset-thumb').matchHeight();
    
	$(document).ready(function() {	

		// Dropzone autoattaches to "dropzone" class.
		// Configure Dropzone options
		Dropzone.options.dropZone = {
			paramName: "file", // The name that will be used to transfer the file
			maxFilesize: 0, // MB

			addRemoveLinks: true,
			dictDefaultMessage: '<i class="fa fa-cloud-upload"></i> \
	 <span class="main-text">Drop Files or Click Here to upload</span> <br /> \
	 <span class="sub-text">(PNG, JPEG, BMP, AVI, MP4 or MOV Files)</span> \
	',
			dictResponseError: 'Server not Configured'
		};
		
		    // To keep our code clean and modular, all custom functionality will be contained inside a single object literal called "dropdownFilter".
            var dropdownFilter = {

                // Declare any variables we will need as properties of the object
                $filters: null,
                $reset: null,
                groups: [],
                outputArray: [],
                outputString: '',

                // The "init" method will run on document ready and cache any jQuery objects we will need.
                init: function() {
                    var self = this; // As a best practice, in each method we will asign "this" to the variable "self" so that it remains scope-agnostic. We will use it to refer to the parent "dropdownFilter" object so that we can share methods and properties between all parts of the object.

                    self.$filters = $('#select-filters');
                    self.$reset = $('#mix-reset');
                    self.$container = $('#mix-container');

                    self.$filters.find('fieldset').each(function() {
                        self.groups.push({
                            $dropdown: $(this).find('select'),
                            active: ''
                        });
                    });

                    self.bindHandlers();
                },

                // The "bindHandlers" method will listen for whenever a select is changed. 
                bindHandlers: function() {
                    var self = this;

                    // Handle select change    
                    self.$filters.on('change', 'select', function(e) {
                        e.preventDefault();

                        self.parseFilters();
                    });

                    // Handle reset click
                    self.$reset.on('click', function(e) {
                        e.preventDefault();

                        self.$filters.find('select').val('');

                        self.parseFilters();
                    });
                },

                // The parseFilters method pulls the value of each active select option
                parseFilters: function() {
                    var self = this;

                    // loop through each filter group and grap the value from each one.
                    for (var i = 0, group; group = self.groups[i]; i++) {
                        group.active = group.$dropdown.val();
                    }

                    self.concatenate();
                },

                // The "concatenate" method will crawl through each group, concatenating filters as desired:
                concatenate: function() {
                    var self = this;

                    self.outputString = ''; // Reset output string

                    for (var i = 0, group; group = self.groups[i]; i++) {
                        self.outputString += group.active;
                    }

                    // If the output string is empty, show all rather than none:
                    !self.outputString.length && (self.outputString = 'all');

                    //console.log(self.outputString); 
                    // ^ we can check the console here to take a look at the filter string that is produced

                    // Send the output string to MixItUp via the 'filter' method:
                    if (self.$container.mixItUp('isLoaded')) {
                        self.$container.mixItUp('filter', self.outputString);
                    }
                }
            };

            // To keep our code clean and modular, all custom functionality will be contained inside a single object literal called "checkboxFilter".
            var checkboxFilter = {

                // Declare any variables we will need as properties of the object
                $filters: null,
                $reset: null,
                groups: [],
                outputArray: [],
                outputString: '',

                // The "init" method will run on document ready and cache any jQuery objects we will need.
                init: function() {
                    var self = this; // As a best practice, in each method we will asign "this" to the variable "self" so that it remains scope-agnostic. We will use it to refer to the parent "checkboxFilter" object so that we can share methods and properties between all parts of the object.

                    self.$filters = $('#checkbox-filters');
                    self.$reset = $('#mix-reset2');
                    self.$container = $('#mix-container');

                    self.$filters.find('fieldset').each(function() {
                        self.groups.push({
                            $inputs: $(this).find('input'),
                            active: [],
                            tracker: false
                        });
                    });

                    self.bindHandlers();
                },

                // The "bindHandlers" method will listen for whenever a form value changes. 
                bindHandlers: function() {
                    var self = this;

                    self.$filters.on('change', function() {
                        self.parseFilters();
                    });

                    self.$reset.on('click', function(e) {
                        e.preventDefault();
                        self.$filters[0].reset();
                        self.parseFilters();
                    });
                },

                // The parseFilters method checks which filters are active in each group:
                parseFilters: function() {
                    var self = this;

                    // loop through each filter group and add active filters to arrays
                    for (var i = 0, group; group = self.groups[i]; i++) {
                        group.active = []; // reset arrays
                        group.$inputs.each(function() {
                            $(this).is(':checked') && group.active.push(this.value);
                        });
                        group.active.length && (group.tracker = 0);
                    }

                    self.concatenate();
                },

                // The "concatenate" method will crawl through each group, concatenating filters as desired:
                concatenate: function() {
                    var self = this,
                        cache = '',
                        crawled = false,
                        checkTrackers = function() {
                            var done = 0;

                            for (var i = 0, group; group = self.groups[i]; i++) {
                                (group.tracker === false) && done++;
                            }

                            return (done < self.groups.length);
                        },
                        crawl = function() {
                            for (var i = 0, group; group = self.groups[i]; i++) {
                                group.active[group.tracker] && (cache += group.active[group.tracker]);

                                if (i === self.groups.length - 1) {
                                    self.outputArray.push(cache);
                                    cache = '';
                                    updateTrackers();
                                }
                            }
                        },
                        updateTrackers = function() {
                            for (var i = self.groups.length - 1; i > -1; i--) {
                                var group = self.groups[i];

                                if (group.active[group.tracker + 1]) {
                                    group.tracker++;
                                    break;
                                } else if (i > 0) {
                                    group.tracker && (group.tracker = 0);
                                } else {
                                    crawled = true;
                                }
                            }
                        };

                    self.outputArray = []; // reset output array

                    do {
                        crawl();
                    }
                    while (!crawled && checkTrackers());

                    self.outputString = self.outputArray.join();

                    // If the output string is empty, show all rather than none:
                    !self.outputString.length && (self.outputString = 'all');

                    //console.log(self.outputString); 
                    // ^ we can check the console here to take a look at the filter string that is produced

                    // Send the output string to MixItUp via the 'filter' method:
                    if (self.$container.mixItUp('isLoaded')) {
                        self.$container.mixItUp('filter', self.outputString);
                    }
                }
            };

            // Init multiselect plugin on filter dropdowns   
            $('#filter2').multiselect({
                buttonClass: 'btn btn-default',
            });
			
			// Instantiate MixItUp:
			$container = $('#content')
			$('#filter').keyup(function(){
			  var val = $(this).val();
			  var state = $container.mixItUp('getState');
			  var $filtered = state.$targets.filter(function(index, element){
				return $(this).text().toString().indexOf( val.trim() ) >= 0;
			  }); 
				
			  $container.mixItUp('filter', $filtered);
			});

            // Init checkboxFilter code
            checkboxFilter.init();

            // Init dropdownFilter code
            dropdownFilter.init();

            var $container = $('#mix-container'), // mixitup container
                $toGrid = $('.to-grid'); // list view button

            // Instantiate MixItUp
            $container.mixItUp({
                controls: {
                    enable: false // we won't be needing these
                },
                animation: {
                    enable: false
                },
                callbacks: {
                    onMixFail: function() {}
                }
            });

            // Add Gallery Item to Lightbox
            $('.mix img').magnificPopup({
              type: 'image',
              callbacks: {
                beforeOpen: function(e) {
                    // we add a class to body to indicate overlay is active
                    // We can use this to alter any elements such as form popups
                    // that need a higher z-index to properly display in overlays
                    $('body').addClass('mfp-bg-open');

                    // Set Magnific Animation
                    this.st.mainClass = 'mfp-fade';

                    // Inform content container there is an animation
                    this.contentContainer.addClass('mfp-with-anim');
                },
                afterClose: function(e) {
                    $('body').removeClass('mfp-bg-open');
                },
                elementParse: function(item) {
                  // Function will fire for each target element
                  // "item.el" is a target DOM element (if present)
                  // "item.src" is a source that you may modify
                  item.src = item.el.attr('src');
                },
              },
              removalDelay: 200, //delay removal by X to allow out-animation
            });



	});
	
</script>