<?php
  include "header.php";
  headerDefault();
?>
  <!-- Ladda Loading Btns CSS -->
  <link rel="stylesheet" type="text/css" href="vendor/plugins/ladda/ladda.min.css">

  <!-- Social Icon Btns CSS(font) -->
  <link rel="stylesheet" type="text/css" href="assets/fonts/zocial/zocial.css">

    <!-- Start: Content-Wrapper -->
    <section id="content_wrapper">

      <!-- Start: Topbar-Dropdown -->
      <div id="topbar-dropmenu">
        <div class="topbar-menu row">
          <div class="col-xs-4 col-sm-2">
            <a href="#" class="metro-tile">
              <span class="metro-icon glyphicon glyphicon-inbox"></span>
              <p class="metro-title">Messages</p>
            </a>
          </div>
          <div class="col-xs-4 col-sm-2">
            <a href="#" class="metro-tile">
              <span class="metro-icon glyphicon glyphicon-user"></span>
              <p class="metro-title">Users</p>
            </a>
          </div>
          <div class="col-xs-4 col-sm-2">
            <a href="#" class="metro-tile">
              <span class="metro-icon glyphicon glyphicon-headphones"></span>
              <p class="metro-title">Support</p>
            </a>
          </div>
          <div class="col-xs-4 col-sm-2">
            <a href="#" class="metro-tile">
              <span class="metro-icon fa fa-gears"></span>
              <p class="metro-title">Settings</p>
            </a>
          </div>
          <div class="col-xs-4 col-sm-2">
            <a href="#" class="metro-tile">
              <span class="metro-icon glyphicon glyphicon-facetime-video"></span>
              <p class="metro-title">Videos</p>
            </a>
          </div>
          <div class="col-xs-4 col-sm-2">
            <a href="#" class="metro-tile">
              <span class="metro-icon glyphicon glyphicon-picture"></span>
              <p class="metro-title">Pictures</p>
            </a>
          </div>
        </div>
      </div>
      <!-- End: Topbar-Dropdown -->

      <!-- Start: Topbar -->
      <header id="topbar">
        <div class="topbar-left">
          <ol class="breadcrumb">
            <li class="crumb-active">
              <a href="dashboard.html">Dashboard</a>
            </li>
            <li class="crumb-icon">
              <a href="dashboard.html">
                <span class="glyphicon glyphicon-home"></span>
              </a>
            </li>
            <li class="crumb-link">
              <a href="dashboard.html">Home</a>
            </li>
            <li class="crumb-trail">Dashboard</li>
          </ol>
        </div>
        <div class="topbar-right">
          <div class="ib topbar-dropdown">
            <label for="topbar-multiple" class="control-label pr10 fs11 text-muted">Reporting Period</label>
            <select id="topbar-multiple" class="hidden">
              <optgroup label="Filter By:">
                <option value="1-1">Last 30 Days</option>
                <option value="1-2" selected="selected">Last 60 Days</option>
                <option value="1-3">Last Year</option>
              </optgroup>
            </select>
          </div>
          <div class="ml15 ib va-m" id="toggle_sidemenu_r">
            <a href="#" class="pl5">
              <i class="fa fa-sign-in fs22 text-primary"></i>
              <span class="badge badge-hero badge-danger">3</span>
            </a>
          </div>
        </div>
      </header>
      <!-- End: Topbar -->

      <div id="content" class="pn pb30 animated fadeIn">

        <!-- Start: Color Variations -->
        <div class="hero-content bg-light dark pv30 br-b br-grey">
          <div class="col-adjust-8">
            <div class="row mb10 text-center">

              <h3 class="mtn mb30 fw400">Color Variations</h3>
              <div class="col-xs-2">
                <code>.btn-default</code>
              </div>
              <div class="col-xs-2">
                <code>.btn-dark</code>
              </div>
              <div class="col-xs-2">
                <code>.btn-primary</code>
              </div>
              <div class="col-xs-2">
                <code>.btn-success</code>
              </div>
              <div class="col-xs-2">
                <code>.btn-info</code>
              </div>
              <div class="col-xs-2">
                <code>.btn-warning</code>
              </div>
              <div class="col-xs-2">
                <code>.btn-danger</code>
              </div>
              <div class="col-xs-2">
                <code>.btn-alert</code>
              </div>
              <div class="col-xs-2">
                <code>.btn-system</code>
              </div>

              <div class="clearfix"></div>
            </div>
            <div class="row">
              <div class="col-xs-2">
                <div class="bg-light light br-b br-lighter pv20 fw600 text-center">#FEFEFE</div>
                <div class="bg-light br-b br-light pv20 fw600 text-center">#FAFAFA</div>
                <div class="bg-light dark pv20 fw600 text-center">#F2F2F2</div>

              </div>
              <div class="col-xs-2">
                <div class="bg-dark light pv20 text-white fw600 text-center">#484D61</div>
                <div class="bg-dark pv20 text-white fw600 text-center">#3B3F4F</div>
                <div class="bg-dark dark pv20 text-white fw600 text-center">#2E313D</div>
              </div>
              <div class="col-xs-2">
                <div class="bg-primary light pv20 text-white fw600 text-center">#649AE1</div>
                <div class="bg-primary pv20 text-white fw600 text-center">#4A89DC</div>
                <div class="bg-primary dark pv20 text-white fw600 text-center">#3078D7</div>
              </div>
              <div class="col-xs-2">
                <div class="bg-success light pv20 text-white fw600 text-center">#85D27A</div>
                <div class="bg-success pv20 text-white fw600 text-center">#70CA63</div>
                <div class="bg-success dark pv20 text-white fw600 text-center">#5BC24C</div>
              </div>
              <div class="col-xs-2">
                <div class="bg-info light pv20 text-white fw600 text-center">#55BADF</div>
                <div class="bg-info pv20 text-white fw600 text-center">#3BAFDA</div>
                <div class="bg-info dark pv20 text-white fw600 text-center">#27A2CF</div>
              </div>
              <div class="col-xs-2">
                <div class="bg-warning light pv20 text-white fw600 text-center">#F7C65F</div>
                <div class="bg-warning pv20 text-white fw600 text-center">#F6BB42</div>
                <div class="bg-warning dark pv20 text-white fw600 text-center">#F5B025</div>
              </div>
              <div class="col-xs-2">
                <div class="bg-danger light pv20 text-white fw600 text-center">#EC6F5A</div>
                <div class="bg-danger pv20 text-white fw600 text-center">#E9573F</div>
                <div class="bg-danger dark pv20 text-white fw600 text-center">#E63F24</div>
              </div>
              <div class="col-xs-2">
                <div class="bg-alert light pv20 text-white fw600 text-center">#A992E2</div>
                <div class="bg-alert pv20 text-white fw600 text-center">#967ADC</div>
                <div class="bg-alert dark pv20 text-white fw600 text-center">#8362D6</div>
              </div>
              <div class="col-xs-2">
                <div class="bg-system light pv20 text-white fw600 text-center">#48C9A9</div>
                <div class="bg-system pv20 text-white fw600 text-center">#37BC9B</div>
                <div class="bg-system dark pv20 text-white fw600 text-center">#30A487</div>
              </div>
            </div>
          </div>
        </div>
        <!-- End: Color Variations -->


        <!-- Start: Button States -->
        <div class="col-adjust-8 mb30">
          <div class="row mt20 text-center">
            <h3 class="mt20 mb10 fw400">Button States</h3>
            <p class="mb25">
              <code>.disabled .active</code>
            </p>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn disabled btn-default btn-block">Disabled</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn disabled btn-dark btn-block">Disabled</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn disabled btn-primary btn-block">Disabled</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn disabled btn-success btn-block">Disabled</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn disabled btn-info btn-block">Disabled</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn disabled btn-warning btn-block">Disabled</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn disabled btn-danger btn-block">Disabled</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn disabled btn-alert btn-block">Disabled</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn disabled btn-system btn-block">Disabled</button>
              </div>
            </div>
          </div>
          <div class="row mt20 text-center">
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-hover btn-default btn-block">Hover</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-hover btn-dark btn-block">Hover</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-hover btn-primary btn-block">Hover</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-hover btn-success btn-block">Hover</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-hover btn-info btn-block">Hover</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-hover btn-warning btn-block">Hover</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-hover btn-danger btn-block">Hover</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-hover btn-alert btn-block">Hover</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-hover btn-system btn-block">Hover</button>
              </div>
            </div>
          </div>
          <div class="row mt20 text-center">
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn active btn-default btn-block">Active</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn active btn-dark btn-block">Active</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn active btn-primary btn-block">Active</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn active btn-success btn-block">Active</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn active btn-info btn-block">Active</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn active btn-warning btn-block">Active</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn active btn-danger btn-block">Active</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn active btn-alert btn-block">Active</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn active btn-system btn-block">Active</button>
              </div>
            </div>
          </div>
        </div>

        <!-- button sizes -->
        <div class="col-adjust-8 mb30">
          <div class="row mt20 text-center">
            <h3 class="mt20 mb10 fw400">Button Sizes</h3>
            <p class="mb25">
              <code>.btn-xs .btn-sm .btn-lg</code>
            </p>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-xs btn-default btn-block">Default</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-xs btn-dark btn-block">Dark</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-xs btn-primary btn-block">Primary</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-xs btn-success btn-block">Success</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-xs btn-info btn-block">Info</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-xs btn-warning btn-block">Warning</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-xs btn-danger btn-block">Danger</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-xs btn-alert btn-block">Alert</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-xs btn-system btn-block">System</button>
              </div>
            </div>
          </div>
          <div class="row mt20 text-center">
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-sm btn-default btn-block">Default</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-sm btn-dark btn-block">Dark</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-sm btn-primary btn-block">Primary</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-sm btn-success btn-block">Success</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-sm btn-info btn-block">Info</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-sm btn-warning btn-block">Warning</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-sm btn-danger btn-block">Danger</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-sm btn-alert btn-block">Alert</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-sm btn-system btn-block">System</button>
              </div>
            </div>
          </div>
          <div class="row mt20 text-center">
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-sm btn-default btn-block">Default</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-dark btn-block">Dark</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-primary btn-block">Primary</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-success btn-block">Success</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-info btn-block">Info</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-warning btn-block">Warning</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-danger btn-block">Danger</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-alert btn-block">Alert</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-system btn-block">System</button>
              </div>
            </div>
          </div>
          <div class="row mt20 text-center">
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-lg btn-default btn-block">Default</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-lg btn-dark btn-block">Dark</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-lg btn-primary btn-block">Primary</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-lg btn-success btn-block">Success</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-lg btn-info btn-block">Info</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-lg btn-warning btn-block">Warning</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-lg btn-danger btn-block">Danger</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-lg btn-alert btn-block">Alert</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-lg btn-system btn-block">System</button>
              </div>
            </div>
          </div>
        </div>

        <!-- button options -->
        <div class="col-adjust-8 mb30">
          <div class="row mt20 text-center">
            <h3 class="mt20 mb10 fw400">Button Options</h3>
            <p class="mb25">
              <code>.btn-rounded .btn-gradient</code>
            </p>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-rounded btn-default btn-block">Default</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-rounded btn-dark btn-block">Dark</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-rounded btn-primary btn-block">Primary</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-rounded btn-success btn-block">Success</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-rounded btn-info btn-block">Info</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-rounded btn-warning btn-block">Warning</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-rounded btn-danger btn-block">Danger</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-rounded btn-alert btn-block">Alert</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-rounded btn-system btn-block">System</button>
              </div>
            </div>
          </div>
          <div class="row mt20 text-center">
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-default btn-gradient dark btn-block">Default</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-dark btn-gradient dark btn-block">Dark</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-primary btn-gradient dark btn-block">Primary</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-success btn-gradient dark btn-block">Success</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-info btn-gradient dark btn-block">Info</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-warning btn-gradient dark btn-block">Warning</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-danger btn-gradient dark btn-block">Danger</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-alert btn-gradient dark btn-block">Alert</button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn btn-system btn-gradient dark btn-block">System</button>
              </div>
            </div>
          </div>
        </div>

        <!-- loading buttons -->
        <div class="col-adjust-8 mb30 demo-btn-ladda">
          <div class="row mt20 text-center">
            <h3 class="mt20 mb10 fw400">Loading Buttons</h3>
            <p class="mb25">
              <code>.ladda-button</code>
            </p>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn ladda-button btn-default" data-style="expand-up">
                  <span class="ladda-label">Load Up</span>
                </button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn ladda-button btn-dark" data-style="expand-right">
                  <span class="ladda-label">Load Right</span>
                </button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn ladda-button btn-primary" data-style="expand-down">
                  <span class="ladda-label">Load Down</span>
                </button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn ladda-button btn-success" data-style="expand-left">
                  <span class="ladda-label">Load Left</span>
                </button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn ladda-button btn-info" data-style="contract">
                  <span class="ladda-label">Contract</span>
                </button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn ladda-button btn-warning" data-style="zoom-in">
                  <span class="ladda-label">Zoom In</span>
                </button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn ladda-button btn-danger" data-style="zoom-out">
                  <span class="ladda-label">Zoom Out</span>
                </button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn ladda-button btn-alert" data-style="zoom-out">
                  <span class="ladda-label">Progress</span>
                </button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn ladda-button btn-system" data-style="expand-right">
                  <span class="ladda-label">Progress 2</span>
                </button>
              </div>
            </div>
          </div>
          <div class="row mt20 text-center">
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn ladda-button btn-default progress-button" data-style="expand-up">
                  <span class="ladda-label">Up</span>
                </button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn ladda-button btn-dark progress-button" data-style="expand-right">
                  <span class="ladda-label">Right</span>
                </button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn ladda-button btn-primary progress-button" data-style="expand-down">
                  <span class="ladda-label">Down</span>
                </button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn ladda-button btn-success progress-button" data-style="expand-left">
                  <span class="ladda-label">Left</span>
                </button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn ladda-button btn-info progress-button" data-style="contract">
                  <span class="ladda-label">Progress</span>
                </button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn ladda-button btn-warning progress-button" data-style="zoom-in">
                  <span class="ladda-label">In</span>
                </button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn ladda-button btn-danger progress-button" data-style="zoom-out">
                  <span class="ladda-label">Out</span>
                </button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn ladda-button btn-alert progress-button progress-button" data-style="zoom-out">
                  <span class="ladda-label">Progress</span>
                </button>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <button type="button" class="btn ladda-button btn-system progress-button progress-button" data-style="expand-right">
                  <span class="ladda-label">Progress 2</span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- icon/button groups -->
        <div class="col-adjust-8 mb30 demo-btn-groups">
          <div class="row mt20 text-center">
            <h3 class="mt20 mb10 fw400">Icon/Button Groups</h3>
            <p class="mb25">
              <code>Multiple .btns wrapped in .btn-group</code>
            </p>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-default">
                    <i class="fa fa-home"></i>
                  </button>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-dark">
                    <i class="fa fa-home"></i>
                  </button>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-primary">
                    <i class="fa fa-home"></i>
                  </button>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-success">
                    <i class="fa fa-home"></i>
                  </button>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-info">
                    <i class="fa fa-home"></i>
                  </button>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-warning">
                    <i class="fa fa-home"></i>
                  </button>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-danger">
                    <i class="fa fa-home"></i>
                  </button>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-alert">
                    <i class="fa fa-home"></i>
                  </button>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-system">
                    <i class="fa fa-home"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div class="row mt20 text-center">
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-default">
                    <i class="fa fa-home"></i>
                  </button>
                  <button type="button" class="btn btn-default dark">
                    <i class="fa fa-coffee"></i>
                  </button>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-dark">
                    <i class="fa fa-home"></i>
                  </button>
                  <button type="button" class="btn btn-dark dark">
                    <i class="fa fa-coffee"></i>
                  </button>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-primary">
                    <i class="fa fa-home"></i>
                  </button>
                  <button type="button" class="btn btn-primary dark">
                    <i class="fa fa-coffee"></i>
                  </button>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-success">
                    <i class="fa fa-home"></i>
                  </button>
                  <button type="button" class="btn btn-success dark">
                    <i class="fa fa-coffee"></i>
                  </button>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-info">
                    <i class="fa fa-home"></i>
                  </button>
                  <button type="button" class="btn btn-info dark">
                    <i class="fa fa-coffee"></i>
                  </button>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-warning">
                    <i class="fa fa-home"></i>
                  </button>
                  <button type="button" class="btn btn-warning dark">
                    <i class="fa fa-coffee"></i>
                  </button>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-danger">
                    <i class="fa fa-home"></i>
                  </button>
                  <button type="button" class="btn btn-danger dark">
                    <i class="fa fa-coffee"></i>
                  </button>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-alert">
                    <i class="fa fa-home"></i>
                  </button>
                  <button type="button" class="btn btn-alert dark">
                    <i class="fa fa-coffee"></i>
                  </button>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-system">
                    <i class="fa fa-home"></i>
                  </button>
                  <button type="button" class="btn btn-system dark">
                    <i class="fa fa-coffee"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div class="row mt20 text-center">
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-default light">
                    <i class="fa fa-envelope"></i>
                  </button>
                  <button type="button" class="btn btn-default">
                    <i class="fa fa-home"></i>
                  </button>
                  <button type="button" class="btn btn-default dark">
                    <i class="fa fa-coffee"></i>
                  </button>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-dark light">
                    <i class="fa fa-envelope"></i>
                  </button>
                  <button type="button" class="btn btn-dark">
                    <i class="fa fa-home"></i>
                  </button>
                  <button type="button" class="btn btn-dark dark">
                    <i class="fa fa-coffee"></i>
                  </button>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-primary light">
                    <i class="fa fa-envelope"></i>
                  </button>
                  <button type="button" class="btn btn-primary">
                    <i class="fa fa-home"></i>
                  </button>
                  <button type="button" class="btn btn-primary dark">
                    <i class="fa fa-coffee"></i>
                  </button>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-success light">
                    <i class="fa fa-envelope"></i>
                  </button>
                  <button type="button" class="btn btn-success">
                    <i class="fa fa-home"></i>
                  </button>
                  <button type="button" class="btn btn-success dark">
                    <i class="fa fa-coffee"></i>
                  </button>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-info light">
                    <i class="fa fa-envelope"></i>
                  </button>
                  <button type="button" class="btn btn-info">
                    <i class="fa fa-home"></i>
                  </button>
                  <button type="button" class="btn btn-info dark">
                    <i class="fa fa-coffee"></i>
                  </button>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-warning light">
                    <i class="fa fa-envelope"></i>
                  </button>
                  <button type="button" class="btn btn-warning">
                    <i class="fa fa-home"></i>
                  </button>
                  <button type="button" class="btn btn-warning dark">
                    <i class="fa fa-coffee"></i>
                  </button>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-danger light">
                    <i class="fa fa-envelope"></i>
                  </button>
                  <button type="button" class="btn btn-danger">
                    <i class="fa fa-home"></i>
                  </button>
                  <button type="button" class="btn btn-danger dark">
                    <i class="fa fa-coffee"></i>
                  </button>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-alert light">
                    <i class="fa fa-envelope"></i>
                  </button>
                  <button type="button" class="btn btn-alert">
                    <i class="fa fa-home"></i>
                  </button>
                  <button type="button" class="btn btn-alert dark">
                    <i class="fa fa-coffee"></i>
                  </button>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-system light">
                    <i class="fa fa-envelope"></i>
                  </button>
                  <button type="button" class="btn btn-system">
                    <i class="fa fa-home"></i>
                  </button>
                  <button type="button" class="btn btn-system dark">
                    <i class="fa fa-coffee"></i>
                  </button>

                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-adjust-8 mb30 demo-btn-dropdowns">
          <div class="row mt20 text-center">
            <h3 class="mt20 mb10 fw400">Button Dropdowns</h3>
            <p class="mb25">
              <code>.dropdown-toggle</code>
            </p>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Default
                    <span class="caret ml5"></span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li>
                      <a href="#">Action</a>
                    </li>
                    <li>
                      <a href="#">Another action</a>
                    </li>
                    <li>
                      <a href="#">Something else here</a>
                    </li>
                    <li class="divider"></li>
                    <li>
                      <a href="#">Separated link</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown">Dark
                    <span class="caret ml5"></span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li>
                      <a href="#">Action</a>
                    </li>
                    <li>
                      <a href="#">Another action</a>
                    </li>
                    <li>
                      <a href="#">Something else here</a>
                    </li>
                    <li class="divider"></li>
                    <li>
                      <a href="#">Separated link</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Primary
                    <span class="caret ml5"></span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li>
                      <a href="#">Action</a>
                    </li>
                    <li>
                      <a href="#">Another action</a>
                    </li>
                    <li>
                      <a href="#">Something else here</a>
                    </li>
                    <li class="divider"></li>
                    <li>
                      <a href="#">Separated link</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">Success
                    <span class="caret ml5"></span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li>
                      <a href="#">Action</a>
                    </li>
                    <li>
                      <a href="#">Another action</a>
                    </li>
                    <li>
                      <a href="#">Something else here</a>
                    </li>
                    <li class="divider"></li>
                    <li>
                      <a href="#">Separated link</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">Info
                    <span class="caret ml5"></span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li>
                      <a href="#">Action</a>
                    </li>
                    <li>
                      <a href="#">Another action</a>
                    </li>
                    <li>
                      <a href="#">Something else here</a>
                    </li>
                    <li class="divider"></li>
                    <li>
                      <a href="#">Separated link</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown">Warning
                    <span class="caret ml5"></span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li>
                      <a href="#">Action</a>
                    </li>
                    <li>
                      <a href="#">Another action</a>
                    </li>
                    <li>
                      <a href="#">Something else here</a>
                    </li>
                    <li class="divider"></li>
                    <li>
                      <a href="#">Separated link</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown">Danger
                    <span class="caret ml5"></span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li>
                      <a href="#">Action</a>
                    </li>
                    <li>
                      <a href="#">Another action</a>
                    </li>
                    <li>
                      <a href="#">Something else here</a>
                    </li>
                    <li class="divider"></li>
                    <li>
                      <a href="#">Separated link</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-alert dropdown-toggle" data-toggle="dropdown">Alert
                    <span class="caret ml5"></span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li>
                      <a href="#">Action</a>
                    </li>
                    <li>
                      <a href="#">Another action</a>
                    </li>
                    <li>
                      <a href="#">Something else here</a>
                    </li>
                    <li class="divider"></li>
                    <li>
                      <a href="#">Separated link</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-system dropdown-toggle" data-toggle="dropdown">System
                    <span class="caret ml5"></span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li>
                      <a href="#">Action</a>
                    </li>
                    <li>
                      <a href="#">Another action</a>
                    </li>
                    <li>
                      <a href="#">Something else here</a>
                    </li>
                    <li class="divider"></li>
                    <li>
                      <a href="#">Separated link</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
          <div class="row mt20 text-center">
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-default">Default</button>
                  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li>
                      <a href="#">Action</a>
                    </li>
                    <li>
                      <a href="#">Another action</a>
                    </li>
                    <li>
                      <a href="#">Something else here</a>
                    </li>
                    <li class="divider"></li>
                    <li>
                      <a href="#">Separated link</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-dark">Dark</button>
                  <button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li>
                      <a href="#">Action</a>
                    </li>
                    <li>
                      <a href="#">Another action</a>
                    </li>
                    <li>
                      <a href="#">Something else here</a>
                    </li>
                    <li class="divider"></li>
                    <li>
                      <a href="#">Separated link</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-primary">Primary</button>
                  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li>
                      <a href="#">Action</a>
                    </li>
                    <li>
                      <a href="#">Another action</a>
                    </li>
                    <li>
                      <a href="#">Something else here</a>
                    </li>
                    <li class="divider"></li>
                    <li>
                      <a href="#">Separated link</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-success">Success</button>
                  <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li>
                      <a href="#">Action</a>
                    </li>
                    <li>
                      <a href="#">Another action</a>
                    </li>
                    <li>
                      <a href="#">Something else here</a>
                    </li>
                    <li class="divider"></li>
                    <li>
                      <a href="#">Separated link</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-info">Info</button>
                  <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li>
                      <a href="#">Action</a>
                    </li>
                    <li>
                      <a href="#">Another action</a>
                    </li>
                    <li>
                      <a href="#">Something else here</a>
                    </li>
                    <li class="divider"></li>
                    <li>
                      <a href="#">Separated link</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-warning">Warning</button>
                  <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li>
                      <a href="#">Action</a>
                    </li>
                    <li>
                      <a href="#">Another action</a>
                    </li>
                    <li>
                      <a href="#">Something else here</a>
                    </li>
                    <li class="divider"></li>
                    <li>
                      <a href="#">Separated link</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-danger">Danger</button>
                  <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li>
                      <a href="#">Action</a>
                    </li>
                    <li>
                      <a href="#">Another action</a>
                    </li>
                    <li>
                      <a href="#">Something else here</a>
                    </li>
                    <li class="divider"></li>
                    <li>
                      <a href="#">Separated link</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-alert">Alert</button>
                  <button type="button" class="btn btn-alert dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li>
                      <a href="#">Action</a>
                    </li>
                    <li>
                      <a href="#">Another action</a>
                    </li>
                    <li>
                      <a href="#">Something else here</a>
                    </li>
                    <li class="divider"></li>
                    <li>
                      <a href="#">Separated link</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-system">System</button>
                  <button type="button" class="btn btn-system dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li>
                      <a href="#">Action</a>
                    </li>
                    <li>
                      <a href="#">Another action</a>
                    </li>
                    <li>
                      <a href="#">Something else here</a>
                    </li>
                    <li class="divider"></li>
                    <li>
                      <a href="#">Separated link</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
          <div class="row mt20 text-center">
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-default">Default</button>
                  <button type="button" class="btn btn-default dark dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li>
                      <a href="#">Action</a>
                    </li>
                    <li>
                      <a href="#">Another action</a>
                    </li>
                    <li>
                      <a href="#">Something else here</a>
                    </li>
                    <li class="divider"></li>
                    <li>
                      <a href="#">Separated link</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-dark">Dark</button>
                  <button type="button" class="btn btn-dark dark dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li>
                      <a href="#">Action</a>
                    </li>
                    <li>
                      <a href="#">Another action</a>
                    </li>
                    <li>
                      <a href="#">Something else here</a>
                    </li>
                    <li class="divider"></li>
                    <li>
                      <a href="#">Separated link</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-primary">Primary</button>
                  <button type="button" class="btn btn-primary dark dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li>
                      <a href="#">Action</a>
                    </li>
                    <li>
                      <a href="#">Another action</a>
                    </li>
                    <li>
                      <a href="#">Something else here</a>
                    </li>
                    <li class="divider"></li>
                    <li>
                      <a href="#">Separated link</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-success">Success</button>
                  <button type="button" class="btn btn-success dark dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li>
                      <a href="#">Action</a>
                    </li>
                    <li>
                      <a href="#">Another action</a>
                    </li>
                    <li>
                      <a href="#">Something else here</a>
                    </li>
                    <li class="divider"></li>
                    <li>
                      <a href="#">Separated link</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-info">Info</button>
                  <button type="button" class="btn btn-info dark dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li>
                      <a href="#">Action</a>
                    </li>
                    <li>
                      <a href="#">Another action</a>
                    </li>
                    <li>
                      <a href="#">Something else here</a>
                    </li>
                    <li class="divider"></li>
                    <li>
                      <a href="#">Separated link</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-warning">Warning</button>
                  <button type="button" class="btn btn-warning dark dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li>
                      <a href="#">Action</a>
                    </li>
                    <li>
                      <a href="#">Another action</a>
                    </li>
                    <li>
                      <a href="#">Something else here</a>
                    </li>
                    <li class="divider"></li>
                    <li>
                      <a href="#">Separated link</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-danger">Danger</button>
                  <button type="button" class="btn btn-danger dark dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li>
                      <a href="#">Action</a>
                    </li>
                    <li>
                      <a href="#">Another action</a>
                    </li>
                    <li>
                      <a href="#">Something else here</a>
                    </li>
                    <li class="divider"></li>
                    <li>
                      <a href="#">Separated link</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-alert">Alert</button>
                  <button type="button" class="btn btn-alert dark dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li>
                      <a href="#">Action</a>
                    </li>
                    <li>
                      <a href="#">Another action</a>
                    </li>
                    <li>
                      <a href="#">Something else here</a>
                    </li>
                    <li class="divider"></li>
                    <li>
                      <a href="#">Separated link</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="bs-component">
                <div class="btn-group">
                  <button type="button" class="btn btn-system">Systemss</button>
                  <button type="button" class="btn btn-system dark dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li>
                      <a href="#">Action</a>
                    </li>
                    <li class="active">
                      <a href="#">Another action</a>
                    </li>
                    <li>
                      <a href="#">Something else here</a>
                    </li>
                    <li class="divider"></li>
                    <li>
                      <a href="#">Separated link</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- social buttons -->
        <div class="mt50 text-center">
          <h3 class="mt20 mb10 fw400">Social Buttons</h3>
          <p class="mb25">
            <code>.zocial .name(eg facebook)</code>
          </p>
        </div>
        <div class="mh30 p20 bg-dark lighter text-center">

          <div class="bs-component ib">
            <button class="zocial icon facebook">Sign in with Facebook</button>

          </div>
          <div class="bs-component ib">
            <button class="zocial icon googleplus">Sign in with Google+</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon twitter">Sign in with Twitter</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon google">Sign in with Google</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon linkedin">Sign in with LinkedIn</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon paypal">Pay with Paypal</button>
          </div>
          <div class="bs-component ib ib">
            <button class="zocial icon amazon">Sign in with Amazon</button>
          </div>
          <div class="bs-component ib ib">
            <button class="zocial icon dropbox">Sync with Dropbox</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon evernote">Clip to Evernote</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon skype">Call me on Skype</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon guest">Sign in as guest</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon spotify">Play on Spotify</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon lastfm">Sign in with Last.fm</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon songkick">Sign in with Songkick</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon forrst">Follow me on Forrst</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon dribbble">Sign in with Dribbble</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon cloudapp">Sign in to CloudApp</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon github">Fork me on Github</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial pinterest icon">Follow me on Pinterest</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial quora icon">Follow me on Quora</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial pinboard icon">Bookmark with Pinboard</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial lanyrd icon">Attend on Lanyrd</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon itunes">Download on iTunes</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon android">Download on Android</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon disqus">Sign in with Disqus</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon yahoo">Sign in with Yahoo</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon vimeo">Upload to Vimeo</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon chrome">Add to Chrome</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon ie">Get a new browser</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon html5">Made from HTML5</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon instapaper">Read It Later</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon scribd">Read more on Scribd</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon wikipedia">View on Wikipedia</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon flattr">Tip with Flattr</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon tumblr">Follow me on Tumblr</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon posterous">Subscribe to my Posterous</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon gowalla">Check in with Gowalla</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon foursquare">Check in with foursquare</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon yelp">Write a review on Yelp</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon soundcloud">Follow me on Soundcloud</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon smashing">Read on Smashing Magazine</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon wordpress">Sign in with WordPress</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon intensedebate">Sign in with IntenseDebate</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon openid">Sign in with OpenID</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon gmail">Sign in with Gmail</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon eventbrite">Sign in with Eventbrite</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon eventasaurus">Sign in with Eventasaurus</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon meetup">Sign in with Meetup.com</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon aol">Sign in with AIM</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon plancast">Follow me on Plancast</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon youtube">Subscribe on YouTube</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon appstore">Available on the Mac App Store</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon creativecommons">View Creative Commons Licence</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial icon rss">Subscribe to RSS</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial weibo icon">Follow me on Weibo</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial plurk icon">Follow me on Plurk</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial grooveshark icon">Follow me on Grooveshark</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial blogger icon">Post on Blogger</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial viadeo icon">Sign in with Viadeo</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial podcast icon">Subscribe to this Podcast</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial fivehundredpx icon">View Portfolio on 500px</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial bitcoin icon">Bitcoin accepted here</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial ninetyninedesigns icon">View Portfolio on 99Designs</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial stumbleupon icon">Stumble!</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial itunes icon">Download on iTunes</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial myspace icon">Find me on Myspace</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial windows icon">Sign in with Windows Live</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial eventful icon">Find Events with Eventful</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial klout icon">Influence with Klout</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial xing icon">Sign in with Xing</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial flickr icon">Upload to Flickr</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial delicious icon">Sign in with Del.icio.us</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial googleplay icon">Download from Google Play</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial opentable icon">Reserve with OpenTable</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial digg icon">Digg this</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial reddit icon">Share on Reddit</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial angellist icon">Fund us on AngelList</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial instagram icon">Sign in with Instagram</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial steam icon">Sign in with Steam</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial vk icon">Sign in with VKontakte</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial appnet icon">Sign in with App.net</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial stripe icon">Sign in with Stripe</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial dwolla icon">Sign in with Dwolla</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial drupal icon">Built with Drupal</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial statusnet icon">Share with Status.net/Indenti.ca</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial pocket icon">Save for later</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial acrobat icon">Download Adobe Acrobat</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial bitbucket icon">Fork from Bitbucket</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial buffer icon">Buffer</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial lego icon">Make me out of Lego</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial logmein icon">Log in</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial ycombinator icon">Hacker News</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial stackoverflow icon">Sign in with Stackoverflow</button>
          </div>
          <br>
        </div>
        <div class="mh30 p20 bg-dark light text-center">

          <div class="bs-component ib">
            <button class="zocial facebook">Sign in with Facebook</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial tumblr">Follow me on Tumblr</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial twitter">Sign in with Twitter</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial linkedin">Sign in with LinkedIn</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial google">Sign in with Google</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial googleplus">Sign in with Google+</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial pinterest">Follow me on Pinterest</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial github">Fork me on Github</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial dribbble">Sign in with Dribbble</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial gmail">Sign in with Gmail</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial soundcloud">Follow me on Soundcloud</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial itunes">Available on iTunes</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial paypal">Pay with Paypal</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial amazon">Sign in with Amazon</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial skype">Call me on Skype</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial cloudapp">Sign in to CloudApp</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial forrst">Follow me on Forrst</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial spotify">Play on Spotify</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial instapaper">Read It Later</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial smashing">Read on Smashing Magazine</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial dropbox">Sync with Dropbox</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial appstore">Available on the App Store</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial macstore">Available on the Mac App Store</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial android">Available on Android Market</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial evernote">Clip to Evernote</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial quora">Follow me on Quora</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial lanyrd">Attend on Lanyrd</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial lastfm">Sign in with Last.fm</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial yelp">Write a review on Yelp</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial foursquare">Check in with foursquare</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial klout">Influence with Klout</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial wikipedia">View on Wikipedia</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial disqus">Sign in with Disqus</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial intensedebate">Sign in with IntenseDebate</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial vimeo">Upload to Vimeo</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial scribd">Read more on Scribd</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial wordpress">Sign in with WordPress</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial songkick">Sign in with Songkick</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial posterous">Sign in with Posterous</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial eventbrite">Sign in with Eventbrite</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial flattr">Tip with Flattr</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial plancast">Follow me on Plancast</button>
          </div>
          <div class="bs-component ib">
            <button class="zocial youtube">Subscribe on YouTube</button>
          </div>
        </div>

      </div>
      <!-- content -->

    </section>
    <!-- End: Content -->

<?php
  include "footer.php";
  footerDefault();
?>
  <!-- Ladda Loading Button JS -->
  <script src="vendor/plugins/ladda/ladda.min.js"></script>
  <script type="text/javascript">
      jQuery(document).ready(function() {

        // Init Ladda Plugin on buttons
        Ladda.bind('.ladda-button', {
          timeout: 2000
        });

        // Bind progress buttons and simulate loading progress. Note: Button still requires ".ladda-button" class.
        Ladda.bind('.progress-button', {
          callback: function(instance) {
            var progress = 0;
            var interval = setInterval(function() {
              progress = Math.min(progress + Math.random() * 0.1, 1);
              instance.setProgress(progress);

              if (progress === 1) {
                instance.stop();
                clearInterval(interval);
              }
            }, 200);
          }
        });

      });
  </script>

    </body>
</html>