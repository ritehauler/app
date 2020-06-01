'use strict';
var do_call = 0;
/*! main.js - v0.1.1
 * http://admindesigns.com/
 * Copyright (c) 2015 Admin Designs;*/

/* Core theme functions required for
 * most of the themes vital functionality */
var Core = function (options) {

    // Variables
    var Window = $(window);
    var Body = $('body');
    var Navbar = $('.navbar');
    var Topbar = $('#topbar');

    // Constant Heights
    var windowH = Window.height();
    var bodyH = Body.height();
    var navbarH = 0;
    var topbarH = 0;

    // Variable Heights
    if (Navbar.is(':visible')) {
        navbarH = Navbar.height();
    }
    if (Topbar.is(':visible')) {
        topbarH = Topbar.height();
    }

    // Calculate Height for inner content elements
    var contentHeight = windowH - (navbarH + topbarH);

    // SideMenu Functions
    var runSideMenu = function (options) {

        // If Nano scrollbar exist and element is fixed, init plugin
        if ($('.nano.affix').length) {
            $(".nano.affix").nanoScroller({
                preventPageScrolling: true
            });
        }

        // Sidebar state naming conventions:
        // "sb-l-o" - SideBar Left Open
        // "sb-l-c" - SideBar Left Closed
        // "sb-l-m" - SideBar Left Minified
        // Same naming convention applies to right sidebar

        // SideBar Left Toggle Function
        var sidebarLeftToggle = function () {

            // If sidebar is set to Horizontal we return
            if ($('body.sb-top').length) {
                return;
            }

            // We check to see if the the user has closed the entire
            // leftside menu. If true we reopen it, this will result
            // in the menu resetting itself back to a minified state.
            // A second click will fully expand the menu.
            if (Body.hasClass('sb-l-c') && options.collapse === "sb-l-m") {
                Body.removeClass('sb-l-c');
            }

            // Toggle sidebar state(open/close)
            Body.toggleClass(options.collapse).removeClass('sb-r-o').addClass('sb-r-c');
            triggerResize();
        };

        // SideBar Right Toggle Function
        var sidebarRightToggle = function () {

            // If sidebar is set to Horizontal we return
            if ($('body.sb-top').length) {
                return;
            }

            // toggle sidebar state(open/close)
            if (options.siblingRope === true && !Body.hasClass('mobile-view') && Body.hasClass('sb-r-o')) {
                Body.toggleClass('sb-r-o sb-r-c').toggleClass(options.collapse);
            } else {
                Body.addclas('sb-r-o sb-r-c').addClass(options.collapse);
            }
            triggerResize();
        };

        // SideBar Left Toggle Function
        var sidebarTopToggle = function () {

            // Toggle sidebar state(open/close)
            Body.toggleClass('sb-top-collapsed');

        };

        // Sidebar Left Collapse Entire Menu event
        $('.sidebar-toggle-mini').on('click', function (e) {
            e.preventDefault();

            // If sidebar is set to Horizontal we return
            if ($('body.sb-top').length) {
                return;
            }

            // Close Menu
            Body.addClass('sb-l-c');
            triggerResize();

            // After animation has occured we toggle the menu.
            // Upon the menu reopening the classes will be toggled
            // again, effectively restoring the menus state prior
            // to being hidden 
            if (!Body.hasClass('mobile-view')) {
                setTimeout(function () {
                    Body.toggleClass('sb-l-m sb-l-o');
                }, 250);
            }
        });

        // Check window size on load
        // Adds or removes "mobile-view" class based on window size
        var sbOnLoadCheck = function () {

            // If sidebar menu is set to Horizontal we add
            // unique custom mobile css classes
            if ($('body.sb-top').length) {
                // If window is < 1080px wide collapse both sidebars and add ".mobile-view" class
                if ($(window).width() < 900) {
                    Body.addClass('sb-top-mobile').removeClass('sb-top-collapsed');
                }
                return;
            }

            // Check Body for classes indicating the state of Left and Right Sidebar.
            // If not found add default sidebar settings(sidebar left open, sidebar right closed).
            if (!$('body.sb-l-o').length && !$('body.sb-l-m').length && !$('body.sb-l-c').length) {
                $('body').addClass(options.sbl);
            }
            if (!$('body.sb-r-o').length && !$('body.sb-r-c').length) {
                $('body').addClass(options.sbr);
            }

            if (Body.hasClass('sb-l-m')) {
                Body.addClass('sb-l-disable-animation');
            } else {
                Body.removeClass('sb-l-disable-animation');
            }

            // If window is < 1080px wide collapse both sidebars and add ".mobile-view" class
            if ($(window).width() < 1080) {
                Body.removeClass('sb-r-o').addClass('mobile-view sb-l-m sb-r-c');
            }

            resizeBody();
        };


        // Check window size on resize
        // Adds or removes "mobile-view" class based on window size
        var sbOnResize = function () {

            // If sidebar menu is set to Horizontal mode we return
            // as the menu operates using pure CSS
            if ($('body.sb-top').length) {
                // If window is < 1080px wide collapse both sidebars and add ".mobile-view" class
                if ($(window).width() < 900 && !Body.hasClass('sb-top-mobile')) {
                    Body.addClass('sb-top-mobile');
                } else if ($(window).width() > 900) {
                    Body.removeClass('sb-top-mobile');
                }
                return;
            }

            // If window is < 1080px wide collapse both sidebars and add ".mobile-view" class
            if ($(window).width() < 1080 && !Body.hasClass('mobile-view')) {
                Body.removeClass('sb-r-o').addClass('mobile-view sb-l-m sb-r-c');
            } else if ($(window).width() > 1080) {
                Body.removeClass('mobile-view');
            } else {
                return;
            }

            resizeBody();
        };

        // Function to set the min-height of content
        // to that of the body height. Ensures trays
        // and content bgs span to the bottom of the page
        var resizeBody = function () {

            var sidebarH = $('#sidebar_left').outerHeight();
            var cHeight = (topbarH + navbarH + sidebarH);

            Body.css('min-height', cHeight);
        };

        // Most CSS menu animations are set to 300ms. After this time
        // we trigger a single global window resize to help catch any 3rd 
        // party plugins which need the event to resize their given elements
        var triggerResize = function () {
            setTimeout(function () {
                $(window).trigger('resize');

                if (Body.hasClass('sb-l-m')) {
                    Body.addClass('sb-l-disable-animation');
                } else {
                    Body.removeClass('sb-l-disable-animation');
                }
            }, 300)
        };

        // Functions Calls
        sbOnLoadCheck();
        $("#toggle_sidemenu_t").on('click', sidebarTopToggle);
        $("#toggle_sidemenu_l").on('click', sidebarLeftToggle);
        $("#toggle_sidemenu_r").on('click', sidebarRightToggle);

        // Attach debounced resize handler
        var rescale = function () {
            sbOnResize();
        }
        var lazyLayout = _.debounce(rescale, 300);
        $(window).resize(lazyLayout);

        //
        // 2. LEFT USER MENU TOGGLE
        //

        // Author Widget selector 
        var authorWidget = $('#sidebar_left .author-widget');

        // Toggle open the user menu
        $('.sidebar-menu-toggle').on('click', function (e) {
            e.preventDefault();

            // Horizontal menu does not support sidebar widgets
            // so we return and prevent the menu from opening
            if ($('body.sb-top').length) {
                return;
            }

            // If an author widget is present we let
            // its sibling menu know it's open
            if (authorWidget.is(':visible')) {
                authorWidget.toggleClass('menu-widget-open');
            }

            // Toggle Class to signal state change
            $('.menu-widget').toggleClass('menu-widget-open').slideToggle('fast');

        });

        // 3. LEFT MENU LINKS TOGGLE
        $('.sidebar-menu li a.accordion-toggle').on('click', function (e) {
            e.preventDefault();

            // If the clicked menu item is minified and is a submenu (has sub-nav parent) we do nothing
            if ($('body').hasClass('sb-l-m') && !$(this).parents('ul.sub-nav').length) {
                return;
            }

            // If the clicked menu item is a dropdown we open its menu
            if (!$(this).parents('ul.sub-nav').length) {

                // If sidebar menu is set to Horizontal mode we return
                // as the menu operates using pure CSS
                if ($(window).width() > 900) {
                    if ($('body.sb-top').length) {
                        return;
                    }
                }

                $('a.accordion-toggle.menu-open').next('ul').slideUp('fast', 'swing', function () {
                    $(this).attr('style', '').prev().removeClass('menu-open');
                });
            }
            // If the clicked menu item is a dropdown inside of a dropdown (sublevel menu)
            // we only close menu items which are not a child of the uppermost top level menu
            else {
                var activeMenu = $(this).next('ul.sub-nav');
                var siblingMenu = $(this).parent().siblings('li').children('a.accordion-toggle.menu-open').next('ul.sub-nav')

                activeMenu.slideUp('fast', 'swing', function () {
                    $(this).attr('style', '').prev().removeClass('menu-open');
                });
                siblingMenu.slideUp('fast', 'swing', function () {
                    $(this).attr('style', '').prev().removeClass('menu-open');
                });
            }

            // Now we expand targeted menu item, add the ".open-menu" class
            // and remove any left over inline jQuery animation styles
            if (!$(this).hasClass('menu-open')) {
                $(this).next('ul').slideToggle('fast', 'swing', function () {
                    $(this).attr('style', '').prev().toggleClass('menu-open');
                });
            }

        });
    }

    // Footer Functions
    var runFooter = function () {

        // Init smoothscroll on page-footer "move-to-top" button if exist
        var pageFooterBtn = $('.footer-return-top');
        if (pageFooterBtn.length) {
            pageFooterBtn.smoothScroll({
                offset: -55
            });
        }

    }

    // jQuery Helper Functions
    var runHelpers = function () {

        // Disable element selection
        $.fn.disableSelection = function () {
            return this
                .attr('unselectable', 'on')
                .css('user-select', 'none')
                .on('selectstart', false);
        };

        // Find element scrollbar visibility
        $.fn.hasScrollBar = function () {
            return this.get(0).scrollHeight > this.height();
        }

        // Test for IE, Add body class if version 9
        function msieversion() {
            var ua = window.navigator.userAgent;
            var msie = ua.indexOf("MSIE ");
            if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) {
                var ieVersion = parseInt(ua.substring(msie + 5, ua.indexOf(".", msie)));
                if (ieVersion === 9) {
                    $('body').addClass('no-js ie' + ieVersion);
                }
                return ieVersion;
            } else {
                return false;
            }
        }

        msieversion();

        // Clean up helper that removes any leftover
        // animation classes on the primary content container
        // If left it can cause z-index and visibility problems
        setTimeout(function () {
            $('#content').removeClass('animated fadeIn');
        }, 800);

    }

    // Delayed Animations
    var runAnimations = function () {

        // Add a class after load to prevent css animations
        // from bluring pages that have load intensive resources
        if (!$('body.boxed-layout').length) {
            setTimeout(function () {
                $('body').addClass('onload-check');
            }, 100);
        }

        // Delayed Animations
        // data attribute accepts delay(in ms) and animation style
        // if only delay is provided fadeIn will be set as default
        // eg. data-animate='["500","fadeIn"]'
        $('.animated-delay[data-animate]').each(function () {
            var This = $(this)
            var delayTime = This.data('animate');
            var delayAnimation = 'fadeIn';

            // if the data attribute has more than 1 value
            // it's an array, reset defaults 
            if (delayTime.length > 1 && delayTime.length < 3) {
                delayTime = This.data('animate')[0];
                delayAnimation = This.data('animate')[1];
            }

            var delayAnimate = setTimeout(function () {
                This.removeClass('animated-delay').addClass('animated ' + delayAnimation)
                    .one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                        This.removeClass('animated ' + delayAnimation);
                    });
            }, delayTime);
        });

        // "In-View" Animations
        // data attribute accepts animation style and offset(in %)
        // eg. data-animate='["fadeIn","40%"]'
        $('.animated-waypoint').each(function (i, e) {
            var This = $(this);
            var Animation = This.data('animate');
            var offsetVal = '35%';

            // if the data attribute has more than 1 value
            // it's an array, reset defaults 
            if (Animation.length > 1 && Animation.length < 3) {
                Animation = This.data('animate')[0];
                offsetVal = This.data('animate')[1];
            }

            var waypoint = new Waypoint({
                element: This,
                handler: function (direction) {
                    console.log(offsetVal)
                    if (This.hasClass('animated-waypoint')) {
                        This.removeClass('animated-waypoint').addClass('animated ' + Animation)
                            .one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                                This.removeClass('animated ' + Animation);
                            });
                    }
                },
                offset: offsetVal
            });
        });

    }

    // Header Functions
    var runHeader = function () {

        // Searchbar - Mobile modifcations
        $('.navbar-search').on('click', function (e) {
            // alert('hi')
            var This = $(this);
            var searchForm = This.find('input');
            var searchRemove = This.find('.search-remove');

            // Don't do anything unless in mobile mode
            if ($('body.mobile-view').length || $('body.sb-top-mobile').length) {

                // Open search bar and add closing icon if one isn't found
                This.addClass('search-open');
                if (!searchRemove.length) {
                    This.append('<div class="search-remove"></div>');
                }

                // Fadein remove btn and focus search input on animation complete
                setTimeout(function () {
                    This.find('.search-remove').fadeIn();
                    searchForm.focus().one('keydown', function () {
                        $(this).val('');
                    });
                }, 250)

                // If remove icon clicked close search bar
                if ($(e.target).attr('class') == 'search-remove') {
                    This.removeClass('search-open').find('.search-remove').remove();
                }

            }

        });

        // Init jQuery Multi-Select for navbar user dropdowns
        if ($("#user-status").length) {
            $('#user-status').multiselect({
                buttonClass: 'btn btn-default btn-sm',
                buttonWidth: 100,
                dropRight: false
            });
        }
        if ($("#user-role").length) {
            $('#user-role').multiselect({
                buttonClass: 'btn btn-default btn-sm',
                buttonWidth: 100,
                dropRight: true
            });
        }

        // Dropdown Multiselect Persist. Prevents a menu dropdown
        // from closing when a child multiselect is clicked
        $('.dropdown-menu').on('click', function (e) {

            e.stopPropagation();
            var Target = $(e.target);
            var TargetGroup = Target.parents('.btn-group');
            var SiblingGroup = Target.parents('.dropdown-menu').find('.btn-group');

            // closes all open multiselect menus. Creates Toggle like functionality
            if (Target.hasClass('multiselect') || Target.parent().hasClass('multiselect')) {
                SiblingGroup.removeClass('open');
                TargetGroup.addClass('open');
            } else {
                SiblingGroup.removeClass('open');
            }

        });

        // Sliding Topbar Metro Menu
        var menu = $('#topbar-dropmenu');
        var items = menu.find('.metro-tile');
        var metroBG = $('.metro-modal');

        // Toggle menu and active class on icon click
        $('.topbar-menu-toggle').on('click', function () {

            // If dropmenu is using alternate style we don't show modal
            if (menu.hasClass('alt')) {
                // Toggle menu and active class on icon click
                menu.slideToggle(230).toggleClass('topbar-menu-open');
                metroBG.fadeIn();
            } else {
                menu.slideToggle(230).toggleClass('topbar-menu-open');
                $(items).addClass('animated animated-short fadeInDown').css('opacity', 1);

                // Create Modal for hover effect
                if (!metroBG.length) {
                    metroBG = $('<div class="metro-modal"></div>').appendTo('body');
                }
                setTimeout(function () {
                    metroBG.fadeIn();
                }, 380);
            }

        });

        // If modal is clicked close menu
        $('body').on('click', '.metro-modal', function () {
            metroBG.fadeOut('fast');
            setTimeout(function () {
                menu.slideToggle(150).toggleClass('topbar-menu-open');
            }, 250);
        });
    }

    // Tray related Functions
    var runTrays = function () {

        // Match height of tray with the height of body
        var trayFormat = $('#content .tray');
        if (trayFormat.length) {

            // Loop each tray and set height to match body
            trayFormat.each(function (i, e) {
                var This = $(e);
                var trayScroll = This.find('.tray-scroller');

                This.height(contentHeight);
                trayScroll.height(contentHeight);

                if (trayScroll.length) {
                    trayScroll.scroller();
                }
            });

            // Scroll lock all fixed content overflow
            $('#content').scrollLock('on', 'div');

        }
        ;

        // Debounced resize handler
        var rescale = function () {
            if ($(window).width() < 1000) {
                Body.addClass('tray-rescale');
            } else {
                Body.removeClass('tray-rescale tray-rescale-left tray-rescale-right');
            }
        }
        var lazyLayout = _.debounce(rescale, 300);

        if (!Body.hasClass('disable-tray-rescale')) {
            // Rescale on window resize
            $(window).resize(lazyLayout);

            // Rescale on load
            rescale();
        }

        // Perform a custom animation if tray-nav has data attribute
        var navAnimate = $('.tray-nav[data-nav-animate]');
        if (navAnimate.length) {
            var Animation = navAnimate.data('nav-animate');

            // Set default "fadeIn" animation if one has not been previously set
            if (Animation == null || Animation == true || Animation == "") {
                Animation = "fadeIn";
            }

            // Loop through each li item and add animation after set timeout
            setTimeout(function () {
                navAnimate.find('li').each(function (i, e) {
                    var Timer = setTimeout(function () {
                        $(e).addClass('animated animated-short ' + Animation);
                    }, 50 * i);
                });
            }, 500);
        }

        // Responsive Tray Javascript Data Helper. If browser window
        // is <575px wide (extreme mobile) we relocate the tray left/right
        // content into the element appointed by the user/data attr
        var dataTray = $('.tray[data-tray-mobile]');
        var dataAppend = dataTray.children();

        function fcRefresh() {
            if ($('body').width() < 585) {
                dataAppend.appendTo($(dataTray.data('tray-mobile')));
            } else {
                dataAppend.appendTo(dataTray);
            }
        };
        fcRefresh();

        // Attach debounced resize handler
        var fcResize = function () {
            fcRefresh();
        }
        var fcLayout = _.debounce(fcResize, 300);
        $(window).resize(fcLayout);

    }

    // Form related Functions
    var runFormElements = function () {

        // Init Bootstrap tooltips, if present 
        var Tooltips = $("[data-toggle=tooltip]");
        if (Tooltips.length) {
            if (Tooltips.parents('#sidebar_left')) {
                Tooltips.tooltip({
                    container: $('body'),
                    template: '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
                });
            } else {
                Tooltips.tooltip();
            }
        }

        // Init Bootstrap Popovers, if present 
        var Popovers = $("[data-toggle=popover]");
        if (Popovers.length) {
            Popovers.popover();
        }

        // Init Bootstrap persistent tooltips. This prevents a
        // popup from closing if a checkbox it contains is clicked
        $('.dropdown-menu.dropdown-persist').on('click', function (e) {
            e.stopPropagation();
        });

        // Prevents a dropdown menu from closing when
        // a nav-tabs menu it contains is clicked
        $('.dropdown-menu .nav-tabs li a').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).tab('show')
        });

        // Prevents a dropdown menu from closing when
        // a btn-group nav menu it contains is clicked
        $('.dropdown-menu .btn-group-nav a').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            // Remove active class from btn-group > btns and toggle tab content
            $(this).siblings('a').removeClass('active').end().addClass('active').tab('show');
        });

        // if btn has ".btn-states" class we monitor it for user clicks. On Click we remove
        // the active class from its siblings and give it to the button clicked.
        // This gives the button set a menu like feel or state
        if ($('.btn-states').length) {
            $('.btn-states').on('click', function () {
                $(this).addClass('active').siblings().removeClass('active');
            });
        }

        // If a panel element has the ".panel-scroller" class we init
        // custom fixed height content scroller. An optional delay data attr
        // may be set. This is useful when you expect the panels height to 
        // change due to a plugin or other dynamic modification.
        var panelScroller = $('.panel-scroller');
        if (panelScroller.length) {
            panelScroller.each(function (i, e) {
                var This = $(e);
                var Delay = This.data('scroller-delay');
                var Margin = 5;

                // Check if scroller bar margin is required
                if (This.hasClass('scroller-thick')) {
                    Margin = 0;
                }

                // Check if scroller bar is in a dropdown, if so 
                // we initilize scroller after dropdown is visible
                var DropMenuParent = This.parents('.dropdown-menu');
                if (DropMenuParent.length) {
                    DropMenuParent.prev('.dropdown-toggle').on('click', function () {
                        setTimeout(function () {
                            This.scroller();
                            $('.navbar').scrollLock('on', 'div');
                        }, 50);
                    });
                    return;
                }

                if (Delay) {
                    var Timer = setTimeout(function () {
                        This.scroller({
                            trackMargin: Margin,
                        });
                        $('#content').scrollLock('on', 'div');
                    }, Delay);
                } else {
                    This.scroller({
                        trackMargin: Margin,
                    });
                    $('#content').scrollLock('on', 'div');
                }

            });
        }

        // Init smoothscroll on elements with set data attr
        // data value determines smoothscroll offset
        var SmoothScroll = $('[data-smoothscroll]');
        if (SmoothScroll.length) {
            SmoothScroll.each(function (i, e) {
                var This = $(e);
                var Offset = This.data('smoothscroll');
                var Links = This.find('a');

                // Init Smoothscroll with data stored offset
                Links.smoothScroll({
                    offset: Offset
                });

            });
        }

    }
    return {
        init: function (options) {

            // Set Default Options
            var defaults = {
                sbl: "sb-l-o", // sidebar left open onload 
                sbr: "sb-r-c", // sidebar right closed onload
                sbState: "save", //Enable localstorage for sidebar states

                collapse: "sb-l-m", // sidebar left collapse style
                siblingRope: true
                // Setting this true will reopen the left sidebar
                // when the right sidebar is closed
            };

            // Extend Default Options.
            var options = $.extend({}, defaults, options);

            // Call Core Functions
            runHelpers();
            runAnimations();
            runHeader();
            runSideMenu(options);
            runFooter();
            runTrays();
            runFormElements();
        }

    }

}();


/* Common theme functions required throughout executing various tasks
 *  Author : Salman Khimani */
var Common = function (options) {

    // Variables
    var Window = $(window);
    var Body = $('body');

    return {
        // init
        init: function (options) {

            // Set Default Options
            var defaults = {
                app_rel_url: "", // app relative url
                ajax_call_timeout: 10000, // default timeout
                ajax_response_silent: 0, // silent response
                // Setting this true will reopen the left sidebar
                // when the right sidebar is closed
            };

            // Extend Default Options.
            var options = $.extend({}, defaults, options);

            // Call Core Functions
            //console.log("getBaseUrl()", getBaseUrl());
        },
        // base url
        getBaseUrl : function () {
            var re = new RegExp(/^.*\//);
            return re.exec(window.location.href);
        },
        // redirect
        redirect : function (url) {
            document.location = url;
            return;
        },
        // redirect top
        redirectTop : function (url) {
            window.top.location = url;
            return;
        },
        // ajax multipurpose function

        jsonValidate : function (params, formElem, spinnerElem, method_type,post_param) {
            var form = null;
           var form_name = $(formElem).attr("name");

            setSelect2MultiValue(form_name);

            if (formElem && formElem != '') {
                var form = $(formElem).serialize();
            }

           /* if entity type has depend entity type*/
            if($("#depend_entity_exist").length > 0
               // ||  $("#is_bulk_entity").length > 0
            ){
               form =  setDependForm();
                //console.log(form); return false;

            }

            if(post_param && post_param != ""){
                form = post_param;
            }
            //console.log(form); return false;

            // set spinner active if given
            if (spinnerElem && spinnerElem != '') {
                $(spinnerElem).show();
            }
            // default request method
            if (method_type && method_type != '') {
                method_type = method_type;
            } else {
                method_type = "post";
            }
            method_type = method_type.toUpperCase();
            //console.log("method_type",method_type);

            $.ajax({
                type: method_type, //defaults to GET
                url: params, //defaults to window.location
                //contentType:'application/json', //defaults to application/x-www-form-urlencoded
                cache: false,
                //headers:{},
                timeout: this.ajax_call_timeout,
                dataType: 'application/json', //defaults to text/html
                data: form, //Can be a Key/Value pair string or object. If it's an object, $.serialize is called to turn it into a Key/Value pair string
                complete: function (resp) {
                    // set spinner inactive if given
                    if (spinnerElem && spinnerElem != '') {
                        $(spinnerElem).hide();
                    }
                    //console.log('HTTP RESP : ',resp);
                    if (resp.status != 200) {
                        if (this.ajax_response_silent == 0) {
                            if (resp.status == 0) {
                                if (typeof jAlert !== 'undefined')
                                    jAlert('Internet Timeout. Please try again');
                                else
                                    alert('Internet Timeout. Please try again');
                            }
                            else if (resp.status == 404 || resp.status == 500) {
                                if (typeof jAlert !== 'undefined')
                                    jAlert('Error : ' + resp.statusText + '. We are trying to fix it soon. Thanks for your patience...');
                                else
                                    alert('Error : ' + resp.statusText + '. We are trying to fix it soon. Thanks for your patience...');
                            }
                            else {
                                if (typeof jAlert !== 'undefined')
                                    jAlert(resp.statusText);
                                else
                                    alert(resp.statusText);
                            }
                        } else {
                            console.log("response status : " + resp.status + " (" + resp.statusText + ")");
                        }
                    }
                    else {
                        var data = $.parseJSON(resp.responseText);

                        if ($("#is_bulk_entity").length > 0) {
                            if (data.is_error == 1) {
                                showAlert(data.error);
                                return false;
                            }
                            else{
                               return true;
                            }
                        }
                        else {
                            // put html/text in targetElement
                            if (data.targetElem || data.error) {
                                // html
                                if (data.html) {
                                    $(data.targetElem).html(data.html);
                                }
                                // append HTML
                                if (data.prependHTML) {
                                    $(data.targetElem).prepend(data.prependHTML);
                                }
                                // append HTML
                                if (data.appendHTML) {
                                    $(data.targetElem).append(data.appendHTML);
                                }
                                // before HTML
                                if (data.beforeHTML) {
                                    $(data.targetElem).before(data.beforeHTML);
                                }
                                // after HTML
                                if (data.afterHTML) {
                                    $(data.targetElem).after(data.afterHTML);
                                }
                                // text
                                var hideClass = data.removeClass;
                                var showClass = data.addClass;

                                var errorMsgID = data.errorFieldID;
                                if (!errorMsgID) {
                                    errorMsgID = "error_msg_";
                                }

                                $("div[id^=" + errorMsgID + "]").text('');
                                $("div[id^=" + errorMsgID + "]").addClass(hideClass);

                                //Display error messages
                                if (data.message) {
                                    $.each(data.message, function (k, v) {
                                        //console.log(data.fields[k]);  console.log(v);
                                        var form_element = "";
                                        if (form_name != "") {
                                            var form_element = 'form[name=' + form_name + '] ';
                                        }

                                        $(form_element + '#' + errorMsgID + data.fields[k]).addClass(showClass);
                                        $(form_element + '#' + errorMsgID + data.fields[k]).removeClass(hideClass);
                                        $(form_element + '#' + errorMsgID + data.fields[k]).text(v);
                                    });

                                }

                                if (data.text) {
                                    $(data.targetElem).addClass(showClass);
                                    $(data.targetElem).removeClass(hideClass);
                                    $(data.targetElem).text(data.text);
                                }


                                // prettyPrint
                                if (data.prettyPrint) {
                                    $(data.targetElem).html(library.json.prettyPrint(JSON.parse(data.prettyPrint)));
                                }
                                // jsonEditor
                                if (data.jsonEditor) {
                                    // empty first
                                    $(data.targetElem).empty();
                                    // create the editor
                                    var container = $(data.targetElem).get(0);
                                    var options = {
                                        mode: 'code',
                                        //modes: ['code', 'text', 'tree'], // allowed modes
                                        onError: function (err) {
                                            alert(err.toString());
                                        },
                                        onModeChange: function (newMode, oldMode) {
                                            //console.log('Mode switched from', oldMode, 'to', newMode);
                                        }
                                    };
                                    var editor = new JSONEditor(container, options);
                                    editor.set($.parseJSON($.trim(data.jsonEditor)));
                                }

                            }

                            // field text
                            if (data.fldText) {
                                // remove previous errors
                                $('div[class*="' + data.fldClass + '"]').remove();
                                var errHtml = '<div class="' + data.fldClass + '">' + data.fldText + '</div>';
                                $(data.focusElem).parent('div').filter(":first").append(errHtml);
                            }

                            // focus Element
                            if (data.focusElem) {
                                $(data.focusElem).focus();
                            }

                            // scroll focus Element
                            if (data.scrollFocus) {
                                doScroll(data.scrollFocus);
                            }

                            // set redirection
                            if (data.redirect) {
                                Common.redirect(data.redirect);
                            }
                            // set top redirection
                            if (data.redirectTop) {
                                redirectTop(data.redirectTop);
                            }
                            // set refresh
                            if (data.refresh) {
                                window.location.reload();
                            }
                            // set top refresh
                            if (data.refreshTop) {
                                window.top.location.reload();
                            }
                            // jsAlert
                            if (data.jsAlert) {
                                jAlert(data.jsAlert, (data.jsAlertTitle ? data.jsAlertTitle : 'Error'));
                            }
                            // callback
                            if (data.callback) {
                                eval(data.callback);
                            }
                            // trigger
                            if (data.trigger) {
                                if (data.trigger.elem != "" && data.trigger.event != "") {
                                    $(data.trigger.elem).trigger(data.trigger.event);
                                }
                            }
                        }

                    }
                    ///

                }
            });

            return false;
        },
       /* this is for depend entities submit form it will submit request individually*/
        jsonValidation : function (params, formElem, post_param,identifier,is_depend_update) {
            var form = null;
            var form_name = $(formElem).attr("name");
            var form_id = $(formElem).attr("id");
            $("#"+form_id+" button[type='submit']").attr('disabled',true);
            $("#"+form_id+" #loader-submit").removeClass('hide');
            var bulk_ajax_proceed = 1;
            setSelect2MultiValue(form_name);

            if (formElem && formElem != '') {
                var form = $(formElem).serialize();
            }

            if(identifier != '' || identifier == "order"){
                if($('.bulk_entity_raw').length>0){
                    form =  setDependForm();
                }
            }

            if(post_param && post_param != ""){
                form = post_param;
            }

            if(!is_depend_update){
                is_depend_update = false;
            }
            //console.log(form); return false;

            // set spinner active if given
            /*if (spinnerElem && spinnerElem != '') {
                $(spinnerElem).show();
            }*/
            // default request method

            var  method_type = "post";
            method_type = method_type.toUpperCase();
            $.ajaxSetup({async: false});
            $.ajax({
                type: method_type, //defaults to GET
              //  async: "false",
                url: params, //defaults to window.location
                //contentType:'application/json', //defaults to application/x-www-form-urlencoded
                cache: false,
                //headers:{},
                timeout: this.ajax_call_timeout,
                dataType: 'application/json', //defaults to text/html
                data: form, //Can be a Key/Value pair string or object. If it's an object, $.serialize is called to turn it into a Key/Value pair string
                complete: function (resp) {
                    // set spinner inactive if given
                  /*  if (spinnerElem && spinnerElem != '') {
                        $(spinnerElem).hide();
                    }*/
                    //console.log('HTTP RESP : ',resp);

                    $("#"+form_id+" #loader-submit").addClass('hide');
                    $("#"+form_id+" button[type='submit']").removeAttr('disabled');
                    if (resp.status != 200) {
                        if (this.ajax_response_silent == 0) {
                            if (resp.status == 0) {
                                if (typeof jAlert !== 'undefined')
                                    jAlert('Internet Timeout. Please try again');
                                else
                                    alert('Internet Timeout. Please try again');
                            }
                            else if (resp.status == 404 || resp.status == 500) {
                                if (typeof jAlert !== 'undefined')
                                    jAlert('Error : ' + resp.statusText + '. We are trying to fix it soon. Thanks for your patience...');
                                else
                                    alert('Error : ' + resp.statusText + '. We are trying to fix it soon. Thanks for your patience...');
                            }
                            else {
                                if (typeof jAlert !== 'undefined')
                                    jAlert(resp.statusText);
                                else
                                    alert(resp.statusText);
                            }
                        } else {
                            console.log("response status : " + resp.status + " (" + resp.statusText + ")");
                        }
                    }
                    else {
                        var data_reponse = $.parseJSON(resp.responseText);

                        if(identifier == "package"){
                            packageResponse(data_reponse);
                        }

                            if(identifier != '' && is_depend_update == false){
                                if (data_reponse.error == 1) {

                                    var error_message = data_reponse.message;
                                    showAlert(error_message);
                                    /*if( $("#"+form_id+" button[type='submit']").length > 0){
                                        $("#"+form_id+" button[type='submit']").removeAttr('disabled');
                                    }
                                    else{
                                        $(".submit-btn").attr("disabled", false);
                                    }*/

                                    return false;
                                }
                                else{
                                    if(data_reponse.redirect){
                                        showSuccessAlert(data_reponse.message);
                                        window.location.href =  data_reponse.redirect;
                                    }
                                    showSuccessAlert(data_reponse.message);
                                    /*if( $("#"+form_id+" button[type='submit']").length > 0){
                                        $("#"+form_id+" button[type='submit']").removeAttr('disabled');
                                    }*/
                                }
                            }
                        else{
                                if (data_reponse.error == 1) {

                                    var error_message = data_reponse.message;

                                    if(data_reponse.bulk_entity_raw){
                                        var item_raw = parseInt(parseInt(1) + parseInt(data_reponse.bulk_entity_raw));
                                        var error_message = "Item "+item_raw+" - "+data_reponse.message;
                                    }

                                    showAlert(error_message);
                                   // $("#"+form_id+" button[type='submit']").removeAttr('disabled');
                                    do_call = 1;
                                    return false;
                                }
                                else{
                                  //  showSuccessAlert(data_reponse.message);
                                    do_call = 0;
                                    // if ($("#is_bulk_entity").length > 0) {
                                    if(data_reponse.bulk_entity_raw){

                                        bulk_ajax_proceed = parseInt(parseInt(bulk_ajax_proceed) + parseInt(data_reponse.bulk_entity_raw))

                                        // console.log("bulk_ajax_proceed"+bulk_ajax_proceed);
                                        // console.log($('.bulk_entity_raw').length);

                                        if($('.bulk_entity_raw').length == bulk_ajax_proceed){
                                            window.location.href =  data_reponse.redirect;
                                        }

                                        // $('#bulk_entity_raw_'+data_reponse.bulk_entity_raw).remove();
                                    }
                                    // }

                                    return true;
                                }
                            }


                    }

                }
            });
            $.ajaxSetup({async: false});
            return false;
        }
    }

}();

'use strict';

var Demo = function () {

    // Demo AdminForm Functions
    var runDemoForms = function () {

        // Prevents directory response when submitting a demo form
        $('.admin-form').on('submit', function (e) {

            if ($('body.timeline-page').length || $('body.admin-validation-page').length) {
                return;
            }
            e.preventDefault;
            alert('Your form has submitted!');
            return false;
        });

        // give file-upload preview onclick functionality
        var fileUpload = $('.fileupload-preview');
        if (fileUpload.length) {

            fileUpload.each(function (i, e) {
                var fileForm = $(e).parents('.fileupload').find('.btn-file > input');
                $(e).on('click', function () {
                    fileForm.click();
                });
            });
        }

    }

    // Demo Header Functions
    var runDemoTopbar = function () {

        // Init jQuery Multi-Select
        if ($("#topbar-multiple").length) {
            $('#topbar-multiple').multiselect({
                buttonClass: 'btn btn-default btn-sm ph15',
                dropRight: true
            });
        }

    }

    // Demo AdminForm Functions
    var runDemoSourceCode = function () {

        var bsElement = $(".bs-component");

        if (bsElement.length) {

            // allow caching of demo resources
            $.ajaxSetup({
                cache: true
            });

            // Define Source code modal
            var modalSource = '<div class="modal fade" id="source-modal" tabindex="-1" role="dialog">  ' +
                '<div class="modal-dialog modal-lg"> ' +
                '<div class="modal-content"> ' +
                '<div class="modal-header"> ' +
                '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> ' +
                '<h4 class="modal-title" id="myModalLabel">Source Code HTML</h4> ' +
                '</div> ' +
                '<div class="modal-body"> ' +
                '<div class="highlight"> ' +
                '<pre> ' +
                '<code class="language-html" data-lang="html"></code> ' +
                '</pre> ' +
                '</div> </div> ' +
                '<div class="modal-footer"> ' +
                '<button type="button" class="btn btn-primary btn-clipboard">Highlight Source</button> ' +
                '</div> </div> </div> </div> </div>';


            // Append modal to body
            $(modalSource).appendTo('body');

            // Code btn definition
            var codeBtn = $("<div id='source-button' class='btn btn-primary btn-xs'>&lt; &gt;</div>")
            codeBtn.click(function () {
                var html = $(this).parent().html();
                html = cleanSource(html);
                $("#source-modal pre").text(html);
                $("#source-modal").modal();

                // Init Highlight.js plugin after delay
                var source = $("#source-modal").find('pre');
                setTimeout(function () {
                    source.each(function (i, block) {
                        hljs.highlightBlock(block);
                    });
                }, 250);

                // Highlight code text on click
                $('.btn-clipboard').on('click', function () {
                    var selection = $(this).parents('.modal-dialog').find('pre');
                    selection.selectText();
                });

                $(document).keypress(function (e) {
                    if (e.which == 99) {
                        console.log('go')
                        // highlight source code if user preses "c" key
                        $('.btn-clipboard').click();
                    }
                });

            });

            // Show code btn on hover
            bsElement.hover(function () {
                $(this).append(codeBtn);
                codeBtn.show();
            }, function () {
                codeBtn.hide();
            });

            // Show code modal on click
            var cleanSource = function (html) {
                var lines = html.split(/\n/);

                lines.shift();
                lines.splice(-1, 1);

                var indentSize = lines[0].length - lines[0].trim().length,
                    re = new RegExp(" {" + indentSize + "}");

                lines = lines.map(function (line) {
                    if (line.match(re)) {
                        line = line.substring(indentSize);
                    }
                    return line;
                });

                lines = lines.join("\n");
                return lines;
            }

            // Helper function to highlight code text
            jQuery.fn.selectText = function () {
                var doc = document,
                    element = this[0],
                    range, selection;
                if (doc.body.createTextRange) {
                    range = document.body.createTextRange();
                    range.moveToElementText(element);
                    range.select();
                } else if (window.getSelection) {
                    selection = window.getSelection();
                    range = document.createRange();
                    range.selectNodeContents(element);
                    selection.removeAllRanges();
                    selection.addRange(range);
                }
            };

        }

    }

    // DEMO FUNCTIONS - primarily trash
    var runDemoSettings = function () {

        if ($('#skin-toolbox').length) {

            // Toggles Theme Settings Tray
            $('#skin-toolbox .panel-heading').on('click', function () {
                $('#skin-toolbox').toggleClass('toolbox-open');
            });
            // Disable text selection
            $('#skin-toolbox .panel-heading').disableSelection();

            // Cache component elements
            var Body = $('body');
            var Breadcrumbs = $('#topbar');
            var Sidebar = $('#sidebar_left');
            var Header = $('.navbar');
            var Branding = Header.children('.navbar-branding');

            // Possible Component Skins
            var headerSkins = "bg-primary bg-success bg-info bg-warning bg-danger bg-alert bg-system bg-dark";
            var sidebarSkins = "sidebar-light light dark";

            // Theme Settings
            var settingsObj = {
                // 'headerTone': true,
                'headerSkin': '',
                'sidebarSkin': 'sidebar-default',
                'headerState': 'navbar-fixed-top',
                'sidebarState': 'affix',
                'sidebarAlign': '',
                'breadcrumbState': 'relative',
                'breadcrumbHidden': 'visible',
            };

            // Local Storage Theme Key
            var themeKey = 'admin-settings1';

            // Local Storage Theme Get
            var themeGet = localStorage.getItem(themeKey);

            // Set new key if one doesn't exist
            if (themeGet === null) {
                localStorage.setItem(themeKey, JSON.stringify(settingsObj));
                themeGet = localStorage.getItem(themeKey);
            }

            // Restore Theme Settings onload from Local Storage Key
            (function () {

                var settingsParse = JSON.parse(themeGet);
                settingsObj = settingsParse;

                $.each(settingsParse, function (i, e) {
                    switch (i) {
                        case 'headerSkin':
                            Header.removeClass(headerSkins).addClass(e);
                            Branding.removeClass(headerSkins).addClass(e + ' dark');
                            if (e === "bg-light") {
                                Branding.removeClass(headerSkins);
                            } else {
                                Branding.removeClass(headerSkins).addClass(e);
                            }
                            $('#toolbox-header-skin input[value="bg-light"]').prop('checked', false);
                            $('#toolbox-header-skin input[value="' + e + '"]').prop('checked', true);
                            break;
                        case 'sidebarSkin':
                            Sidebar.removeClass(sidebarSkins).addClass(e);
                            $('#toolbox-sidebar-skin input[value="bg-light"]').prop('checked', false);
                            $('#toolbox-sidebar-skin input[value="' + e + '"]').prop('checked', true);
                            break;
                        case 'headerState':
                            if (e === "navbar-fixed-top") {
                                Header.addClass('navbar-fixed-top');
                                $('#header-option').prop('checked', true);
                            } else {
                                Header.removeClass('navbar-fixed-top');
                                $('#header-option').prop('checked', false);

                                // Remove left over inline styles from nanoscroller plugin
                                Sidebar.nanoScroller({
                                    destroy: true
                                });
                                Sidebar.find('.nano-content').attr('style', '');
                                Sidebar.removeClass('affix');
                                $('#sidebar-option').prop('checked', false);
                            }
                            break;
                        case 'sidebarState':
                            if (e === "affix") {
                                Sidebar.addClass('affix');
                                $('#sidebar-option').prop('checked', true);
                            } else {
                                // Remove left over inline styles from nanoscroller plugin
                                Sidebar.nanoScroller({
                                    destroy: true
                                });
                                Sidebar.find('.nano-content').attr('style', '');
                                Sidebar.removeClass('affix');
                                $('#sidebar-option').prop('checked', false);
                            }
                            break;
                        case 'sidebarAlign':
                            if (e === "sb-top") {
                                Body.addClass('sb-top');
                                $('#sidebar-align').prop('checked', true);
                            } else {
                                Body.removeClass('sb-top');
                                $('#sidebar-align').prop('checked', false);
                            }
                            break;
                        case 'breadcrumbState':
                            if (e === "affix") {
                                Breadcrumbs.addClass('affix');
                                $('#breadcrumb-option').prop('checked', true);
                            } else {
                                Breadcrumbs.removeClass('affix');
                                $('#breadcrumb-option').prop('checked', false);
                            }
                            break;
                        case 'breadcrumbHidden':
                            if (Breadcrumbs.hasClass('hidden')) {
                                $('#breadcrumb-hidden').prop('checked', true);
                            }
                            else {
                                if (e === "hidden") {
                                    Breadcrumbs.addClass('hidden');
                                    $('#breadcrumb-hidden').prop('checked', true);
                                } else {
                                    Breadcrumbs.removeClass('hidden');
                                    $('#breadcrumb-hidden').prop('checked', false);
                                }
                            }
                            break;
                    }
                });

            })();

            // Header Skin Switcher
            $('#toolbox-header-skin input').on('click', function () {
                var This = $(this);
                var Val = This.val();
                var ID = This.attr('id');

                // Swap Header Skin
                Header.removeClass(headerSkins).addClass(Val);
                Branding.removeClass(headerSkins).addClass(Val + ' dark');

                // Save new Skin to Settings Key
                settingsObj['headerSkin'] = Val;
                localStorage.setItem(themeKey, JSON.stringify(settingsObj));

            });

            // Sidebar Skin Switcher
            $('#toolbox-sidebar-skin input').on('click', function () {
                var Val = $(this).val();

                // Swap Sidebar Skin
                Sidebar.removeClass(sidebarSkins).addClass(Val);

                // Save new Skin to Settings Key
                settingsObj['sidebarSkin'] = Val;
                localStorage.setItem(themeKey, JSON.stringify(settingsObj));
            });

            // Fixed Header Switcher
            $('#header-option').on('click', function () {
                var headerState = "navbar-fixed-top";

                if (Header.hasClass('navbar-fixed-top')) {
                    Header.removeClass('navbar-fixed-top');
                    headerState = "relative";

                    // Remove Fixed Sidebar option if navbar isnt fixed
                    Sidebar.removeClass('affix');

                    // Remove left over inline styles from nanoscroller plugin
                    Sidebar.nanoScroller({
                        destroy: true
                    });
                    Sidebar.find('.nano-content').attr('style', '');
                    Sidebar.removeClass('affix');
                    $('#sidebar-option').prop('checked', false);

                    $('#sidebar-option').parent('.checkbox-custom').addClass('checkbox-disabled').end().prop('checked', false).attr('disabled', true);
                    settingsObj['sidebarState'] = "";
                    localStorage.setItem(themeKey, JSON.stringify(settingsObj));

                    // Remove Fixed Breadcrumb option if navbar isnt fixed
                    Breadcrumbs.removeClass('affix');
                    $('#breadcrumb-option').parent('.checkbox-custom').addClass('checkbox-disabled').end().prop('checked', false).attr('disabled', true);
                    settingsObj['breadcrumbState'] = "";
                    localStorage.setItem(themeKey, JSON.stringify(settingsObj));

                } else {
                    Header.addClass('navbar-fixed-top');
                    headerState = "navbar-fixed-top";
                    // Enable fixed sidebar and breadcrumb options
                    $('#sidebar-option').parent('.checkbox-custom').removeClass('checkbox-disabled').end().attr('disabled', false);
                    $('#breadcrumb-option').parent('.checkbox-custom').removeClass('checkbox-disabled').end().attr('disabled', false);
                }

                // Save new setting to Settings Key
                settingsObj['headerState'] = headerState;
                localStorage.setItem(themeKey, JSON.stringify(settingsObj));
            });

            // Fixed Sidebar Switcher
            $('#sidebar-option').on('click', function () {
                var sidebarState = "";

                if (Sidebar.hasClass('affix')) {

                    // Remove left over inline styles from nanoscroller plugin
                    Sidebar.nanoScroller({
                        destroy: true
                    });
                    Sidebar.find('.nano-content').attr('style', '');
                    Sidebar.removeClass('affix');

                    sidebarState = "";
                } else {
                    Sidebar.addClass('affix');
                    // If sidebar is fixed init nano scrollbar plugin

                    if ($('.nano.affix').length) {
                        $(".nano.affix").nanoScroller({
                            preventPageScrolling: true
                        });
                    }
                    sidebarState = "affix";

                }

                $(window).trigger('resize');

                // Save new setting to Settings Key
                settingsObj['sidebarState'] = sidebarState;
                localStorage.setItem(themeKey, JSON.stringify(settingsObj));
            });

            // Sidebar Horizontal Setting Switcher
            $('#sidebar-align').on('click', function () {

                var sidebarAlign = "";

                if (Body.hasClass('sb-top')) {
                    Body.removeClass('sb-top');
                    sidebarAlign = "";
                } else {
                    Body.removeClass('sb-top');
                    sidebarAlign = "sb-top";
                }

                // Save new setting to Settings Key
                settingsObj['sidebarAlign'] = sidebarAlign;
                localStorage.setItem(themeKey, JSON.stringify(settingsObj));
            });

            // Fixed Breadcrumb Switcher
            $('#breadcrumb-option').on('click', function () {

                var breadcrumbState = "";

                if (Breadcrumbs.hasClass('affix')) {
                    Breadcrumbs.removeClass('affix');
                    breadcrumbState = "";
                } else {
                    Breadcrumbs.addClass('affix');
                    breadcrumbState = "affix";
                }

                // Save new setting to Settings Key
                settingsObj['breadcrumbState'] = breadcrumbState;
                localStorage.setItem(themeKey, JSON.stringify(settingsObj));
            });

            // Hidden Breadcrumb Switcher
            $('#breadcrumb-hidden').on('click', function () {
                var breadcrumbState = "";

                if (Breadcrumbs.hasClass('hidden')) {
                    Breadcrumbs.removeClass('hidden');
                    breadcrumbState = "";
                } else {
                    Breadcrumbs.addClass('hidden');
                    breadcrumbState = "hidden";
                }

                // Save new setting to Settings Key
                settingsObj['breadcrumbHidden'] = breadcrumbState;
                localStorage.setItem(themeKey, JSON.stringify(settingsObj));
            });

            // Clear local storage button and confirm dialog
            $("#clearLocalStorage").on('click', function () {

                // check for Bootbox plugin - should be in core
                if (bootbox.confirm) {
                    bootbox.confirm("Are You Sure?!", function (e) {

                        // e returns true if user clicks "accept"
                        // false if "cancel" or dismiss icon are clicked
                        if (e) {
                            // Timeout simply gives the user a second for the modal to
                            // fade away so they can visibly see the options reset
                            setTimeout(function () {
                                localStorage.clear();
                                location.reload();
                            }, 200);
                        } else {
                            return;
                        }
                    });

                }

            });

        }
    }

    // Runfull Screen Demo
    var runFullscreenDemo = function () {

        // If browser is IE we need to pass the fullsreen plugin the 'html' selector
        // rather than the 'body' selector. Fixes a fullscreen overflow bug
        var selector = $('html');

        var ua = window.navigator.userAgent;
        var old_ie = ua.indexOf('MSIE ');
        var new_ie = ua.indexOf('Trident/');
        if ((old_ie > -1) || (new_ie > -1)) {
            selector = $('body');
        }

        // Fullscreen Functionality
        var screenCheck = $.fullscreen.isNativelySupported();

        // Attach handler to navbar fullscreen button
        $('.request-fullscreen').on('click', function () {

            // Check for fullscreen browser support
            if (screenCheck) {
                if ($.fullscreen.isFullScreen()) {
                    $.fullscreen.exit();
                }
                else {
                    selector.fullscreen({
                        overflow: 'auto'
                    });
                }
            } else {
                alert('Your browser does not support fullscreen mode.')
            }
        });

    }

    // SelectAll
    var selectAll = function () {

        // Basic Table Check All
        var $selectAll = $('#selectAll'); // main checkbox inside table thead
        var $table = $('.table'); // table selector 
        var $tdCheckbox = $table.find('tbody input:checkbox'); // checboxes inside table body
        var $tdCheckboxChecked = []; // checked checbox arr

        //Select or deselect all checkboxes on main checkbox change
        $selectAll.on('click', function () {
            $tdCheckbox.prop('checked', this.checked);
        });

        //Switch main checkbox state to checked when all checkboxes inside tbody tag is checked
        $tdCheckbox.on('change', function () {
            $tdCheckboxChecked = $table.find('tbody input:checkbox:checked');//Collect all checked checkboxes from tbody tag
            //if length of already checked checkboxes inside tbody tag is the same as all tbody checkboxes length, then set property of main checkbox to "true", else set to "false"
            $selectAll.prop('checked', ($tdCheckboxChecked.length == $tdCheckbox.length));
        })

        $('.table input[type="checkbox"]').change(function () {
            if ($('.table input[type="checkbox"]:checked').length) {
                $('.sec-title').addClass('is_hidden');
                $('.table-tools').removeClass('is_hidden');
            } else {
                $('.sec-title').removeClass('is_hidden');
                $('.table-tools').addClass('is_hidden');
            }
        });

    }

    // WidgetBox
    var widgetBox = function () {

        // Widget Box
        $(".panel .panel-tools input").change(function () {
            $(this).parent().parent().parent().toggleClass('selected');
        });
        $('.panel .panel-tools input:checked').parent().parent().parent().addClass('selected');
    }


    //============================ Custom Jquery  ==========================//

    return {
        init: function () {
            //runDemoForms();
            runDemoTopbar();
            runDemoSourceCode();
            //runDemoSettings();
            runFullscreenDemo();
            selectAll();
            widgetBox();

            /* Custom Initalize */
            //  trayRightOpen();
        }
    }
}();

function getParameters(form_param)
{
    var raw = {};

    var sURLVariables = form_param.split('&');
    for (var i = 0; i < sURLVariables.length; i++)
    {
        var sParameterName = sURLVariables[i].split('=');
        console.log(sParameterName);
        raw[sParameterName[0]] = sParameterName[1];
    }

    return raw;
}

// Global Library of Theme colors for Javascript plug and play use  
var bgPrimary = '#4a89dc',
    bgPrimaryL = '#5d9cec',
    bgPrimaryLr = '#83aee7',
    bgPrimaryD = '#2e76d6',
    bgPrimaryDr = '#2567bd',
    bgSuccess = '#70ca63',
    bgSuccessL = '#87d37c',
    bgSuccessLr = '#9edc95',
    bgSuccessD = '#58c249',
    bgSuccessDr = '#49ae3b',
    bgInfo = '#3bafda',
    bgInfoL = '#4fc1e9',
    bgInfoLr = '#74c6e5',
    bgInfoD = '#27a0cc',
    bgInfoDr = '#2189b0',
    bgWarning = '#f6bb42',
    bgWarningL = '#ffce54',
    bgWarningLr = '#f9d283',
    bgWarningD = '#f4af22',
    bgWarningDr = '#d9950a',
    bgDanger = '#e9573f',
    bgDangerL = '#fc6e51',
    bgDangerLr = '#f08c7c',
    bgDangerD = '#e63c21',
    bgDangerDr = '#cd3117',
    bgAlert = '#967adc',
    bgAlertL = '#ac92ec',
    bgAlertLr = '#c0b0ea',
    bgAlertD = '#815fd5',
    bgAlertDr = '#6c44ce',
    bgSystem = '#37bc9b',
    bgSystemL = '#48cfad',
    bgSystemLr = '#65d2b7',
    bgSystemD = '#2fa285',
    bgSystemDr = '#288770',
    bgLight = '#f3f6f7',
    bgLightL = '#fdfefe',
    bgLightLr = '#ffffff',
    bgLightD = '#e9eef0',
    bgLightDr = '#dfe6e9',
    bgDark = '#3b3f4f',
    bgDarkL = '#424759',
    bgDarkLr = '#51566c',
    bgDarkD = '#2c2f3c',
    bgDarkDr = '#1e2028',
    bgBlack = '#283946',
    bgBlackL = '#2e4251',
    bgBlackLr = '#354a5b',
    bgBlackD = '#1c2730',
    bgBlackDr = '#0f161b';

/* ============== Theme Color Gradient ============== */

$('.gradient-overlay').each(function () {

    // First random color
    var rand1 = '#ff7f5d';
    // Second random color
    var rand2 = '#d0674b';

    var grad = $(this);

    // Convert Hex color to RGB
    function convertHex(hex, opacity) {
        var hex = hex.replace('#', '');
        var r = parseInt(hex.substring(0, 2), 16);
        var g = parseInt(hex.substring(2, 4), 16);
        var b = parseInt(hex.substring(4, 6), 16);

        // Add Opacity to RGB to obtain RGBA
        var result = 'rgba(' + r + ',' + g + ',' + b + ',' + opacity / 100 + ')';
        return result;
    }

    // Gradient rules
    grad.css('background-color', convertHex(rand1, 60));
    grad.css("background-image", "-webkit-gradient(linear, left top, left bottom, color-stop(0%," + convertHex(rand1, 60) + "), color-stop(100%," + convertHex(rand2, 60) + "))");
    grad.css("background-image", "-webkit-linear-gradient(top,  " + convertHex(rand1, 60) + " 0%," + convertHex(rand2, 60) + " 100%)");
    grad.css("background-image", "-o-linear-gradient(top, " + convertHex(rand1, 60) + " 0%," + convertHex(rand2, 60) + " 100%)");
    grad.css("background-image", "-ms-linear-gradient(top, " + convertHex(rand1, 60) + " 0%," + convertHex(rand2, 60) + " 100%)");
    grad.css("background-image", "linear-gradient(to bottom, " + convertHex(rand1, 60) + " 0%," + convertHex(rand2, 60) + " 100%)");
    grad.css("filter", "progid:DXImageTransform.Microsoft.gradient( startColorstr='" + convertHex(rand1, 60) + "', endColorstr='" + convertHex(rand2, 60) + "',GradientType=0 )");

});

/**
 * set select2 values comma separated in hidden field
 * @param form_id
 */
function setSelect2MultiValue(form_id)
{
    if($('form[name="'+form_id+'"] .select2-multiple').length > 0 ){

        $('form[name="'+form_id+'"] .select2-multiple').each(function() {

            var select2_value = $(this).select2('val');
          //  console.log(select2_value);
            if(select2_value != null){
                var selected_ids = select2_value.join(',');
            }
            //console.log(selected_ids);
            var ele_id = $(this).attr('id');
            var ele_hidden_id =ele_id.replace("_select2", "");
            $("#"+ele_hidden_id).val(selected_ids);

        });
    }

}

function setDependForm()
{
    //  var form_data = $('.entity_wrap').find('select, textarea, input').serialize();
    var depend_entity = [];
    $.each($('.bulk_entity_raw'),function(k,v){
        /*var raw = {};
         var depend_ent = $(this).find('select, textarea, input').serialize();
         raw = getParameters(depend_ent);
         depend_entity[k] = raw;*/
        var raw = {};
        $.each($(this).find('select, textarea, input').serializeArray(), function() {
            var ele_name = this.name;
            if(ele_name == "gallery_items"){
                var temp = {};
                temp[0] = this.value;
                raw[this.name] = temp;
            }
            else{
                raw[this.name] = this.value;
            }

        });

        depend_entity[k] = raw;

    });


    var form = { };
    $.each($('.entity_wrap').find('select, textarea, input').serializeArray(), function() {
        form[this.name] = this.value;
    });


    if($("#depend_entity_exist").length > 0){
        form['depend_entity'] = depend_entity;
    }

    return form;
}