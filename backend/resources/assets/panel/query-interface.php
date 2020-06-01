<?php
    ob_start();
    include("header.php");
    headerDefault();
    $buffer=ob_get_contents();
    ob_end_clean();

    $buffer=str_replace("%TITLE%","Query Interface",$buffer);
    echo $buffer;
?>


<!-- Start: Content-Wrapper -->
    <section id="content_wrapper">
        <div class="code-editor">
            <div class="bs-component">
                <div class="alert alert-danger alert-dismissable mn">
                     <span class="icon mdi mdi-alert-circle fs22 mr10"></span>#1064 - You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near 'wp_user' at line 1
                </div>
            </div>
            <textarea id="code2" name="code" style="display: none;">
-- SQL Mode for CodeMirror
SELECT SQL_NO_CACHE DISTINCT
        @var1 AS `val1`, @'val2', @global.'sql_mode',
        1.1 AS `float_val`, .14 AS `another_float`, 0.09e3 AS `int_with_esp`,
        0xFA5 AS `hex`, x'fa5' AS `hex2`, 0b101 AS `bin`, b'101' AS `bin2`,
        DATE '1994-01-01' AS `sql_date`, { T "1994-01-01" } AS `odbc_date`,
        'my string', _utf8'your string', N'her string',
        TRUE, FALSE, UNKNOWN
    FROM DUAL
    -- space needed after '--'
    # 1 line comment
    /* multiline
    comment! */
    LIMIT 1 OFFSET 0;
            </textarea>
            <div class="code-footer">
                <div class="code-left">
                    <button type="button" class="btn btn-default btn-wide">Clear</button>
                </div>
                <div class="code-right">
                    <button type="button" class="btn ladda-button btn-theme btn-wide" data-style="zoom-in">
                        <span class="ladda-label">Submit</span>
                    </button>
                </div>
				
            </div>
        </div>
        <section id="content" class="pn">
            <div class="code-result">
                <div class="panel panel-visible" id="spy3">
                    <div class="panel-body pn br-v-n br-h-n">
                      <table class="table table-striped table-hover" id="datatable3" cellspacing="0" width="100%">
                        <thead>
                          <tr>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Office</th>
                            <th>Age</th>
                            <th>Start date</th>
                            <th>Salary</th>
                          </tr>
                        </thead>
                        <tfoot>
                          <tr>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Office</th>
                            <th>Age</th>
                            <th>Start date</th>
                            <th>Salary</th>
                          </tr>
                        </tfoot>
                        <tbody>
                          <tr>
                            <td>Tiger Nixon</td>
                            <td>System Architect</td>
                            <td>Edinburgh</td>
                            <td>61</td>
                            <td>2011/04/25</td>
                            <td>$320,800</td>
                          </tr>
                          <tr>
                            <td>Cedric Kelly</td>
                            <td>Senior Javascript Developer</td>
                            <td>Edinburgh</td>
                            <td>22</td>
                            <td>2012/03/29</td>
                            <td>$433,060</td>
                          </tr>
                          <tr>
                            <td>Brielle Williamson</td>
                            <td>Integration Specialist</td>
                            <td>New York</td>
                            <td>61</td>
                            <td>2012/12/02</td>
                            <td>$372,000</td>
                          </tr>
                          <tr>
                            <td>Sonya Frost</td>
                            <td>Software Engineer</td>
                            <td>Edinburgh</td>
                            <td>23</td>
                            <td>2008/12/13</td>
                            <td>$103,600</td>
                          </tr>
                          <tr>
                            <td>Quinn Flynn</td>
                            <td>Support Lead</td>
                            <td>Edinburgh</td>
                            <td>22</td>
                            <td>2013/03/03</td>
                            <td>$342,000</td>
                          </tr>
                          <tr>
                            <td>Haley Kennedy</td>
                            <td>Senior Marketing Designer</td>
                            <td>London</td>
                            <td>43</td>
                            <td>2012/12/18</td>
                            <td>$313,500</td>
                          </tr>
                          <tr>
                            <td>Tatyana Fitzpatrick</td>
                            <td>Regional Director</td>
                            <td>London</td>
                            <td>19</td>
                            <td>2010/03/17</td>
                            <td>$385,750</td>
                          </tr>
                          <tr>
                            <td>Michael Silva</td>
                            <td>Marketing Designer</td>
                            <td>London</td>
                            <td>66</td>
                            <td>2012/11/27</td>
                            <td>$198,500</td>
                          </tr>
                          <tr>
                            <td>Paul Byrd</td>
                            <td>Chief Financial Officer (CFO)</td>
                            <td>New York</td>
                            <td>64</td>
                            <td>2010/06/09</td>
                            <td>$725,000</td>
                          </tr>
                          <tr>
                            <td>Gloria Little</td>
                            <td>Systems Administrator</td>
                            <td>New York</td>
                            <td>59</td>
                            <td>2009/04/10</td>
                            <td>$237,500</td>
                          </tr>
                          <tr>
                            <td>Bradley Greer</td>
                            <td>Software Engineer</td>
                            <td>London</td>
                            <td>41</td>
                            <td>2012/10/13</td>
                            <td>$132,000</td>
                          </tr>
                          <tr>
                            <td>Dai Rios</td>
                            <td>Personnel Lead</td>
                            <td>Edinburgh</td>
                            <td>35</td>
                            <td>2012/09/26</td>
                            <td>$217,500</td>
                          </tr>
                          <tr>
                            <td>Jenette Caldwell</td>
                            <td>Development Lead</td>
                            <td>New York</td>
                            <td>30</td>
                            <td>2011/09/03</td>
                            <td>$345,000</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                </div>
            </div>    
        </section>
    </section>
    <!-- End: Content-Wrapper -->

<!-- Start: Right Sidebar -->
    <aside id="sidebar_right" class="nano affix">

      <!-- Start: Sidebar Right Content -->
      <div class="sidebar-right-content nano-content p15">
          <h5 class="title-divider text-muted mb20"> Server Statistics
            <span class="pull-right"> 2013
              <i class="fa fa-caret-down ml5"></i>
            </span>
          </h5>
          <div class="progress mh5">
            <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 44%">
              <span class="fs11">DB Request</span>
            </div>
          </div>
          <div class="progress mh5">
            <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 84%">
              <span class="fs11 text-left">Server Load</span>
            </div>
          </div>
          <div class="progress mh5">
            <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 61%">
              <span class="fs11 text-left">Server Connections</span>
            </div>
          </div>
          <h5 class="title-divider text-muted mt30 mb10">Traffic Margins</h5>
          <div class="row">
            <div class="col-xs-5">
              <h3 class="text-primary mn pl5">132</h3>
            </div>
            <div class="col-xs-7 text-right">
              <h3 class="text-success-dark mn">
                <i class="fa fa-caret-up"></i> 13.2% </h3>
            </div>
          </div>
          <h5 class="title-divider text-muted mt25 mb10">Database Request</h5>
          <div class="row">
            <div class="col-xs-5">
              <h3 class="text-primary mn pl5">212</h3>
            </div>
            <div class="col-xs-7 text-right">
              <h3 class="text-success-dark mn">
                <i class="fa fa-caret-up"></i> 25.6% </h3>
            </div>
          </div>
          <h5 class="title-divider text-muted mt25 mb10">Server Response</h5>
          <div class="row">
            <div class="col-xs-5">
              <h3 class="text-primary mn pl5">82.5</h3>
            </div>
            <div class="col-xs-7 text-right">
              <h3 class="text-danger mn">
                <i class="fa fa-caret-down"></i> 17.9% </h3>
            </div>
          </div>
          <h5 class="title-divider text-muted mt40 mb20"> Server Statistics
            <span class="pull-right text-primary fw600">USA</span>
          </h5>
        </div>
        
    </aside>
<!-- End: Right Sidebar -->

<?php include_once("footer.php"); ?>
<!-- Style Css -->

<!-- Datatables CSS -->
<link rel="stylesheet" type="text/css" href="vendor/plugins/datatables/media/css/dataTables.bootstrap.css">


<!-- Datatables -->
<script src="vendor/plugins/datatables/media/js/jquery.dataTables.js"></script>

<!-- Datatables Tabletools addon -->
<script src="vendor/plugins/datatables/extensions/TableTools/js/dataTables.tableTools.min.js"></script>

<!-- Datatables ColReorder addon -->
<script src="vendor/plugins/datatables/extensions/ColReorder/js/dataTables.colReorder.min.js"></script>

<!-- Datatables Bootstrap Modifications  -->
<script src="vendor/plugins/datatables/media/js/dataTables.bootstrap.js"></script>


<!-- Ladda Loading Button JS -->
<script src="vendor/plugins/ladda/ladda.min.js"></script>

<!-- Codemirror css -->
<link rel="stylesheet" href="vendor/plugins/codemirror/codemirror.css" />
<link rel="stylesheet" href="vendor/plugins/codemirror/ambiance.css" />
<link rel="stylesheet" href="vendor/plugins/codemirror/show-hint.css" />

<!-- Code mirror js -->
<script src="vendor/plugins/codemirror/codemirror.js"></script>
<script src="vendor/plugins/codemirror/sql.js"></script>
<script src="vendor/plugins/codemirror/show-hint.js"></script>
<script src="vendor/plugins/codemirror/sql-hint.js"></script>

<script type="text/javascript">
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

window.onload = function() {
  var mime = 'text/x-mariadb';
  // get mime type
  if (window.location.href.indexOf('mime=') > -1) {
    mime = window.location.href.substr(window.location.href.indexOf('mime=') + 5);
  }
  window.editor = CodeMirror.fromTextArea(document.getElementById('code2'), {
    mode: mime,
    indentWithTabs: true,
    smartIndent: true,
    lineNumbers: true,
    theme: 'ambiance',  
    matchBrackets : true,
    autofocus: true,
    extraKeys: {"Ctrl-Space": "autocomplete"},
    hintOptions: {tables: {
      users: {name: null, score: null, birthDate: null},
      countries: {name: null, population: null, size: null}
    }}
  });
};
$('#datatable3').dataTable({
  "aoColumnDefs": [{
    'bSortable': false,
    'aTargets': [-1]
  }],
  "oLanguage": {
    "oPaginate": {
      "sPrevious": "",
      "sNext": ""
    }
  },
  "iDisplayLength": 5,
  "aLengthMenu": [
    [5, 10, 25, 50, -1],
    [5, 10, 25, 50, "All"]
  ],
  "sDom": '<"dt-panelmenu clearfix"Tfr>t<"dt-panelfooter clearfix"ip>',
  "oTableTools": {
    "sSwfPath": "vendor/plugins/datatables/extensions/TableTools/swf/copy_csv_xls_pdf.swf"
  }
});

</script>