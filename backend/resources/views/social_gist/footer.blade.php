<!-- Footer -->
<footer id="page-footer" class="content-mini content-mini-full font-s12 bg-gray-lighter clearfix">
    @if(config("constants.ALLOWED_CUSTOM_BRANDING"))
        <div class="text-center"><a class="font-w600" href="<?php echo url("/"); ?>"
                                   target="_blank">{!! $_meta->site_name !!}</a> &copy; <span
                    class="js-year-copy"></span>
        </div>
    @else
        <div class="text-center"><a class="font-w600" href="<?php echo url("/"); ?>"
                                   target="_blank">{!! $_meta->site_name !!}</a> &copy; <span
                    class="js-year-copy"></span> Powered by <a target="_blank"
                                                               href="{!! config("constants.POWERED_BY_LINK") !!}">{!! config("constants.POWERED_BY_CO")!!}</a>
        </div>
    @endif
</footer>
<!-- END Footer -->
</div>
<div id="raw_div"></div>
<!-- END Page Container -->

<!-- OneUI Core JS: jQuery, Bootstrap, slimScroll, scrollLock, Appear, CountTo, Placeholder, Cookie and App.js -->
<script src="{!! URL::to(config('constants.ADMIN_JS_URL').'bootbox.js') !!}"></script>
<script src="{!! URL::to(config('constants.ADMIN_JS_URL').'custom.js') !!}"></script>
<script src="{!! URL::to(config('constants.JS_URL').'core.js') !!}"></script>
<script src="{!! URL::to(config('constants.ADMIN_JS_URL').'core/bootstrap.min.js') !!}"></script>
<script src="{!! URL::to(config('constants.ADMIN_JS_URL').'core/jquery.slimscroll.min.js') !!}"></script>
<script src="{!! URL::to(config('constants.ADMIN_JS_URL').'core/jquery.scrollLock.min.js') !!}"></script>
<script src="{!! URL::to(config('constants.ADMIN_JS_URL').'core/jquery.appear.min.js') !!}"></script>
<script src="{!! URL::to(config('constants.ADMIN_JS_URL').'core/jquery.countTo.min.js') !!}"></script>
<script src="{!! URL::to(config('constants.ADMIN_JS_URL').'core/jquery.placeholder.min.js') !!}"></script>
<script src="{!! URL::to(config('constants.ADMIN_JS_URL').'core/js.cookie.min.js') !!}"></script>
<script src="{!! URL::to(config('constants.ADMIN_JS_URL').'plugins/masked-inputs/jquery.maskedinput.min.js') !!}"></script>
<script src="{!! URL::to(config('constants.ADMIN_JS_URL').'plugins/jquery-mousewheel/jquery.mousewheel.min.js') !!}"></script>
<script src="{!! URL::to(config('constants.ADMIN_JS_URL').'plugins/jquery-ui/globalize.js') !!}"></script>
<script src="{!! URL::to(config('constants.ADMIN_JS_URL').'plugins/jquery-ui/jquery-ui.js') !!}"></script>
<script src="{!! URL::to(config('constants.JS_URL').'jquery.doubleScroll.js') !!}"></script>
<script src="{!! URL::to(config('constants.ADMIN_JS_URL').'app.js') !!}"></script>

</body></html>