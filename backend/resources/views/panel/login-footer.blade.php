<!-- New Login Design End -->

<!-- jQuery -->
<script src="{!! \URL::to(config('panel.DIR_PANEL_RESOURCE')) !!}/vendor/jquery/jquery-1.11.1.min.js"></script>
<script src="{!! \URL::to(config('panel.DIR_PANEL_RESOURCE')) !!}/vendor/jquery/jquery_ui/jquery-ui.min.js"></script>
<script src="{!! \URL::to(config('panel.DIR_PANEL_RESOURCE')) !!}/vendor/plugins/slick/slick.js"></script>
<!-- Theme Javascript -->
<script src="{!! \URL::to(config('panel.DIR_PANEL_RESOURCE')) !!}/assets/js/utility/utility.js"></script>
<script src="{!! \URL::to(config('panel.DIR_PANEL_RESOURCE')) !!}/assets/js/demo/demo.js"></script>
<script src="{!! \URL::to(config('panel.DIR_PANEL_RESOURCE')) !!}/assets/js/main.js"></script>
<!-- END: PAGE SCRIPTS -->
<script type="text/javascript">
    jQuery(document).ready(function () {
        "use strict";
        // Init Theme Core
        Core.init();
        // Init Demo JS
        Demo.init();

        // Init Common JS
        Common.init();

        $('.footer-links a').click(function (e) {
            e.preventDefault();
            $('a[href="' + $(this).attr('href') + '"]').tab('show');
        });

        $("form[name=data_form]").submit(function(e) {
            e.preventDefault();
            Common.jsonValidate("", this);
        });

        /* Slick Slider */
        if($('div').hasClass('signup-carousel')){
            $(".signup-carousel").slick({
                dots: true,
                arrows: false,
                infinite: true,
                slidesToShow: 1,
                slidesToScroll: 1,
                fade: true,
                autoplay: true,
                autoplaySpeed:4500,
            });
        }

    });
</script>
</body>
</html>