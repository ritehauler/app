<?php 
include "header.php"; 
headerVisualEditor();
?>
<!-- Select2 Plugin CSS  -->
<link rel="stylesheet" type="text/css" href="vendor/plugins/select2/css/core.css">

<!-- Grid editor CSS -->
<link rel="stylesheet" type="text/css" href="vendor/plugins/grideditor/dist/grideditor.css" />

    <!-- Start: Content-Wrapper -->
    <section id="content_wrapper" class="vc-container">
        <div id="myGrid">
            <div class="row">
                <div class="col-md-12">
                    <h1>Lorem ipsum dolor sit amet, consectetur</h1>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Velit exercitationem eaque aperiam rem quia quibusdam dolor ducimus quo similique eos pariatur nostrum aliquam nam eius, doloremque quis voluptatum unde. Porro voluptates aspernatur voluptate ipsam, magni vero. Accusamus, iusto tempore id!</p>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quae laboriosam, excepturi quas.</p>
                </div>

            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <h2>Lorem ipsum dolor</h2>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ea facilis vel aliquam aspernatur dolor placeat totam saepe perferendis. Odio quia vel sed eveniet cupiditate, illum doloremque sint veniam eum? Corporis?</p>
                </div>
                <div class="col-md-4">
                    <h2>Pariatur reprehenderit</h2>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Illo adipisci ipsa, consequuntur cum, sunt dolores veniam. Enim inventore in dolore deserunt vitae sequi nemo!</p>
                </div>
                <div class="col-md-4">
                    <h2>Pariatur reprehenderit</h2>
                    <p>Lorem ipsumdolor sit amet, cons dolor sit amet, consectetur adipisicing elit. Ea excepturi ducimus numquam aut error corporis aspernatur ipsum quos eius culpa!</p>
                </div>dolor sit amet, consdolor sit amet, consdolor sit amet, consdolor sit amet, consdolor sit amet, cons
            </div>
            
            <div class="row">
                <div class="col-md-8">
                    <h2>Lorem ipsum dolor.</h2>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Porro distinctio atque molestiae optio, consequuntur? Iusto ratione cumque dolor aut dolore!</p>
                    </br>
                    <h2>Lorem ipsum dolor.</h2>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Porro distinctio atque molestiae optio, consequuntur? Iusto ratione cumque dolor aut dolore!</p>
                </div>
                <div class="col-md-4">
                    <h2>Lusto ratione</h2>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quis fugit quasi officiis id laudantium error aut ut aperiam dicta saepe non vel, cupiditate illum ipsam velit deleniti natus incidunt impedit molestias dolore quos dolores enim. Aliquid ipsam eaque consequuntur quaerat, suscipit a in. Praesentium repudiandae quibusdam recusandae sequi eligendi quos, dignissimos, officiis officia minima necessitatibus eaque consequatur in id adipisci qui minus voluptatum quae debitis, quas maxime iure. Tempore vero unde quia reiciendis ad beatae voluptate omnis, ipsa expedita ab, quasi, neque. Doloribus, pariatur. Aut hic voluptate.</p>
                </div>
            </div>
        </div>
    </section>
    <!-- End: Content-Wrapper -->

<?php include "footer.php"; ?>
<!-- Grid Editor Plugin Plugin -->
<script src="vendor/plugins/grideditor/dist/jquery.grideditor.js"></script>
<!-- Tinymce Plugin Plugin -->
<script src="vendor/plugins/tinymce/tinymce.min.js"></script>
<script src="vendor/plugins/tinymce/jquery.tinymce.min.js"></script>
<script>
    $(function() {
        // Initialize grid editor
        $('#myGrid').gridEditor({
            new_row_layouts: [[12], [6, 6], [9, 3]],
            content_types: ['tinymce'],
            source_textarea: $('.myTextarea'),
        });

        // Get resulting html
        var html = $('#myGrid').gridEditor('getHtml');
        console.log(html);
    });
</script>
<script type="text/javascript">
    function formatDesign (item) { 
        var $state = $('<span>' + item.text.replace(" - ","<br />")+'</span>');
        return $state;
    };
    jQuery(document).ready(function() {
        $('#select2-dark-two').select2({
          templateResult: formatDesign
        });
    });
</script>
<script src="vendor/plugins/select2/select2.min.js"></script>