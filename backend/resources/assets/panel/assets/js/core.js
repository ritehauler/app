$(document).ready(function(){
	// Toggles Theme Settings Tray
	$('#sidebar_right .panel-heading').on('click', function() {
		$('#sidebar_right').toggleClass('sidebar_right-open');
	});
});
// default constants
if (typeof app_rel_url == undefined) {
    var app_rel_url = '';
}
if (typeof ajax_call_timeout == undefined) {
    var ajax_call_timeout = 10000;
}
if (typeof ajax_response_silent == undefined) {
    var ajax_response_silent = 0;
}

function limitText(limitField, limitCount, limitNum) {
    if (limitField.value.length > limitNum) {
        limitField.value = limitField.value.substring(0, limitNum);
    } else {
        limitCount.value = limitNum - limitField.value.length;
    }
}

function changeNav(id, total) {
    for (i = 1; i <= total; i++) {
        if (i == id) {
            var clName = document.getElementById(id).className;
            var d = clName.substr((clName.length - 4), clName.length);
            if (d != '-act') {
                document.getElementById(id).className = clName + '-act';
            }
        } else {
            var clNamee = document.getElementById(i).className;
            var dd = clNamee.substr((clNamee.length - 4), clNamee.length);
            if (dd == '-act') {
                var n = clNamee.substr(0, (clNamee.length - 4));
                document.getElementById(i).className = n;
            }
        }
    }
}
/** Salman Function Starts */
function showSpinner(spinnerId) {
    $('#' + spinnerId).css('display', 'block');
    $('#' + spinnerId).css('visibility', 'visible');
    $('#' + spinnerId).show()
}
function hideSpinner(spinnerId) {
    $('#' + spinnerId).css('display', 'none');
    $('#' + spinnerId).css('visibility', 'hidden');
    $('#' + spinnerId).hide();
}
function simpleAjax(pageUrl, FormId, divId, spinnerId) {
    var dataVar = '';
    var d = new Date();
    if (FormId != null) {
        dataVar = $('#' + FormId).serialize();
    }
    $.ajax({
        type: "POST",
        //url: pageUrl+'&ct='+d.getTime(),
        url: pageUrl,
        cache: false,
        data: dataVar,
        success: function (msg) {
            if ($.isArray(divId)) {
                for (var i in divId) {
                    $('#' + divId[i]).fadeIn('slow', function () { /*Animation complete.*/
                    });
                    $('#' + divId[i]).html(msg);
                    //alert(divId[i]);
                }
            }
            else {
                $('#' + divId).fadeIn('slow', function () { /*Animation complete.*/
                });
                $('#' + divId).html(msg);
            }
            //$('#'+divId).html(msg);
            if (spinnerId != null) {
                hideSpinner(spinnerId);
            }
        },
        beforeSend: function () {
            //alert('slideUp');
            if ($.isArray(divId)) {
                for (var i in divId) {
                    $('#' + divId[i]).fadeOut('slow', function () { /*Animation complete.*/
                    });
                }
            }
            else {
                $('#' + divId).fadeOut('slow', function () { /*Animation complete.*/
                });
            }

            if (spinnerId != null) {
                showSpinner(spinnerId);
            }
        },
        error: function (m) {
            if (spinnerId != null) {
                hideSpinner(spinnerId);
            }
            //alert(m);
        },
        complete: function () {

        }
    });
}

function appendAjax(pageUrl, FormId, divId, spinnerId) {
    var dataVar = '';
    var d = new Date();
    if (FormId != null) {
        dataVar = $('#' + FormId).serialize();
    }
    $.ajax({
        type: "POST",
        //url: pageUrl+'&ct='+d.getTime(),
        url: pageUrl,
        cache: false,
        data: dataVar,
        success: function (msg) {
            if ($.isArray(divId)) {
                for (var i in divId) {
                    $('#' + divId[i]).fadeIn('slow', function () { /*Animation complete.*/
                    });
                    $('#' + divId[i]).append(msg);
                    //alert(divId[i]);
                }
            }
            else {
                $('#' + divId).fadeIn('slow', function () { /*Animation complete.*/
                });
                $('#' + divId).append(msg);
            }
            //$('#'+divId).html(msg);
            if (spinnerId != null) {
                hideSpinner(spinnerId);
            }
        },
        beforeSend: function () {
            //alert('slideUp');
            if (spinnerId != null) {
                showSpinner(spinnerId);
            }
        },
        error: function (m) {
            if (spinnerId != null) {
                hideSpinner(spinnerId);
            }
            //alert(m);
        },
        complete: function () {

        }
    });
}

function prependAjax(pageUrl, FormId, divId, spinnerId) {
    var dataVar = '';
    var d = new Date();
    if (FormId != null) {
        dataVar = $('#' + FormId).serialize();
    }
    $.ajax({
        type: "POST",
        //url: pageUrl+'&ct='+d.getTime(),
        url: pageUrl,
        cache: false,
        data: dataVar,
        success: function (msg) {
            if ($.isArray(divId)) {
                for (var i in divId) {
                    $('#' + divId[i]).fadeIn('slow', function () { /*Animation complete.*/
                    });
                    $('#' + divId[i]).prepend(msg);
                    //alert(divId[i]);
                }
            }
            else {
                $('#' + divId).fadeIn('slow', function () { /*Animation complete.*/
                });
                $('#' + divId).prepend(msg);
            }
            //$('#'+divId).html(msg);
            if (spinnerId != null) {
                hideSpinner(spinnerId);
            }
        },
        beforeSend: function () {
            //alert('slideUp');
            if (spinnerId != null) {
                showSpinner(spinnerId);
            }
        },
        error: function (m) {
            if (spinnerId != null) {
                hideSpinner(spinnerId);
            }
            //alert(m);
        },
        complete: function () {

        }
    });
}


function justAjax(pageUrl, FormId, divId, spinnerId) {
    var dataVar = '';
    var d = new Date();
    if (FormId != null) {
        dataVar = $('#' + FormId).serialize();
    }
    $.ajax({
        type: "POST",
        //url: pageUrl+'&ct='+d.getTime(),
        url: pageUrl,
        cache: false,
        data: dataVar,
        success: function (msg) {
            if ($.isArray(divId)) {
                for (var i in divId) {
                    $('#' + divId[i]).html(msg);
                    //alert(divId[i]);
                }
            }
            else {
                $('#' + divId).html(msg);
            }
            if (spinnerId != null) {
                hideSpinner(spinnerId);
            }
        },
        beforeSend: function () {
            if (spinnerId != null) {
                showSpinner(spinnerId);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            if (spinnerId != null) {
                hideSpinner(spinnerId);
            }
            //alert(m);
            //alert(xhr.status);
            //alert(thrownError);
        },
        complete: function () {

        }
    });
}

/**
 Function : setNavigation
 Required :
 prefix            = prefix used in navigation eg:"prefix_"
 totalnav        = total navigations
 idtoactive        = id of navigation to active eg, for "prefix_3" enter 3
 actClass        = active class name
 */
function setNavigation(prefix, totalnav, idtoactive, actClass) {
    if (prefix == "nav_") {
        for (var i = 0; i <= totalnav; i++) {
            $('#' + prefix + i).removeClass("link" + i + "-act");
        }
        $('#' + prefix + idtoactive).addClass("link" + idtoactive + "-act");
    }
    else {
        for (var i = 0; i <= totalnav; i++) {
            $('#' + prefix + i).removeClass(actClass);
        }
        $('#' + prefix + idtoactive).addClass(actClass);
    }
}

/**
 Popups html
 */
function htmlPopup(html, titleMsg) {
    new Boxy(html, {title: titleMsg});
}

/**
 myAlert
 */

function boxyAlert(msg, titleMsg) {
    Boxy.alert(msg, null, {title: titleMsg});
}


// Limit the text field to only numbers (with decimals)
function format(input) {
    var num = input.value.replace(/\,/g, '');
    if (!isNaN(num)) {
        if (num.indexOf('.') > -1) {
            num = num.split('.');
            num[0] = num[0].toString().split('').reverse().join('').replace(/(?=\d*\.?)(\d{3})/g, '$1,').split('').reverse().join('').replace(/^[\,]/, '');
            if (num[1].length > 2) {
                //alert('You may only enter two decimals!');
                num[1] = num[1].substring(0, num[1].length - 1);
            }
            input.value = num[0] + '.' + num[1];
        } else {
            input.value = num.toString().split('').reverse().join('').replace(/(?=\d*\.?)(\d{3})/g, '$1,').split('').reverse().join('').replace(/^[\,]/, '')
        }
        ;
    } else {
        //alert('You may enter only numbers in this field!');
        input.value = input.value.substring(0, input.value.length - 1);
    }
}

// Limit the text field to only numbers (no decimals)

function formatInt(input) {
    var num = input.value.replace(/\,/g, '');
    if (!isNaN(num)) {
        if (num.indexOf('.') > -1) {
            //alert("You may not enter any decimals.");
            input.value = input.value.substring(0, input.value.length - 1);
        }
    } else {
        //alert('You may enter only numbers in this field!');
        input.value = input.value.substring(0, input.value.length - 1);
    }
}

//Purpose : Email Validation

function isEmail(em_address) {
    var email = /^[A-Za-z0-9]+([_\.-][A-Za-z0-9]+)*@[A-Za-z0-9]+([_\.-][A-Za-z0-9][_\.-]+)*\.([A-Za-z]){2,4}$/i;
    return (email.test(em_address))
}

function isValidUrl(url) {
    var urlRegxp = /^(http:\/\/www.|https:\/\/www.|www.|http:\/\/|https:\/\/){1}([\w]+)(.[\w]+){1,2}$/;
    if (urlRegxp.test(url) != true) {
        return false;
    } else {
        return true;
    }
}

/*
 * PHP Serialize
 * Morten Amundsen
 * mor10am@gmail.com
 */
function php_serialize(obj) {
    var string = '';

    if (typeof(obj) == 'object') {
        if (obj instanceof Array) {
            string = 'a:';
            tmpstring = '';
            count = 0;
            for (var key in obj) {
                tmpstring += php_serialize(key);
                tmpstring += php_serialize(obj[key]);
                count++;
            }
            string += count + ':{';
            string += tmpstring;
            string += '}';
        } else if (obj instanceof Object) {
            classname = obj.toString();

            if (classname == '[object Object]') {
                classname = 'StdClass';
            }

            string = 'O:' + classname.length + ':"' + classname + '":';
            tmpstring = '';
            count = 0;
            for (var key in obj) {
                tmpstring += php_serialize(key);
                if (obj[key]) {
                    tmpstring += php_serialize(obj[key]);
                } else {
                    tmpstring += php_serialize('');
                }
                count++;
            }
            string += count + ':{' + tmpstring + '}';
        }
    } else {
        switch (typeof(obj)) {
            case 'number':
                if (obj - Math.floor(obj) != 0) {
                    string += 'd:' + obj + ';';
                } else {
                    string += 'i:' + obj + ';';
                }
                break;
            case 'string':
                string += 's:' + obj.length + ':"' + obj + '";';
                break;
            case 'boolean':
                if (obj) {
                    string += 'b:1;';
                } else {
                    string += 'b:0;';
                }
                break;
        }
    }

    return string;
}

function getFileExtension(filename) {
    var ext = /^.+\.([^.]+)$/.exec(filename);
    return ext == null ? "" : ext[1];
}

function doScroll(target, newSpeed, moreMargin) {
    var newSpeed = (!newSpeed || newSpeed == null) ? 1000 : newSpeed;
    var moreMargin = (!moreMargin || moreMargin == null) ? 0 : moreMargin;
    var targetOffset = $('#' + target).offset().top;
    $('html,body').animate({scrollTop: (targetOffset + moreMargin)}, newSpeed);
}

function clearForm(form) {
    // iterate over all of the inputs for the form
    // element that was passed in
    $(':input', form).each(function () {
        var type = this.type;
        var tag = this.tagName.toLowerCase(); // normalize case
        // it's ok to reset the value attr of text inputs,
        // password inputs, and textareas
        if (type == 'text' || type == 'password' || tag == 'textarea')
            this.value = "";
        // checkboxes and radios need to have their checked state cleared
        // but should *not* have their 'value' changed
        else if (type == 'checkbox' || type == 'radio')
            this.checked = false;
        // select elements need to have their 'selectedIndex' property set to -1
        // (this works for both single and multiple select elements)
        else if (tag == 'select')
            this.selectedIndex = -1;
    });

}

function flashColor(id, fadeColor, fadeSpeed) {
    var fadeSpeed = (!fadeSpeed || fadeSpeed == '' || fadeSpeed == null) ? 'slow' : fadeSpeed;
    var fadeSpeed = (!fadeColor || fadeColor == '' || fadeColor == null) ? 'yellow' : fadeColor;
    var container = $('#' + id);
    if (container.length) {
        var originalColor = container.css('backgroundColor');
        container.animate({backgroundColor: 'yellow'},
            fadeSpeed, 'linear', function () {
                $(this).animate({backgroundColor: originalColor});
            });
    }
}

function getBaseURL() {
    var url = location.href;  // entire url including querystring - also: window.location.href;
    var baseURL = url.substring(0, url.indexOf('/', 14));

    if (baseURL.indexOf('http://localhost') != -1) {
        // Base Url for localhost
        var url = location.href;  // window.location.href;
        var pathname = location.pathname;  // window.location.pathname;
        var index1 = url.indexOf(pathname);
        var index2 = url.indexOf("/", index1 + 1);
        var baseLocalUrl = url.substr(0, index2);

        return baseLocalUrl + "/";
    }
    else {
        // Root Url for domain name
        return baseURL + "/";
    }

}

function showDate(targetID) {
    // Create two variable with the names of the months and days in an array
    var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    var dayNames = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"]

    // Create a newDate() object
    var newDate = new Date();
    // Extract the current date from Date object
    newDate.setDate(newDate.getDate());
    // Output the day, date, month and year
    //$('#'+targetID).html(dayNames[newDate.getDay()] + " " + newDate.getDate() + ' ' + monthNames[newDate.getMonth()] + ' ' + newDate.getFullYear());
    $('#' + targetID).html(dayNames[newDate.getDay()] + " " + newDate.getDate() + ' ' + monthNames[newDate.getMonth()]);
}

function showTime(hourID, muniteID, secID) {
    setInterval(function () {
        // Create a newDate() object and extract the seconds of the current time on the visitor's
        var seconds = new Date().getSeconds();
        // Add a leading zero to seconds value
        $("#" + secID).html(( seconds < 10 ? "0" : "" ) + seconds);
    }, 1000);

    setInterval(function () {
        // Create a newDate() object and extract the minutes of the current time on the visitor's
        var minutes = new Date().getMinutes();
        // Add a leading zero to the minutes value
        $("#" + muniteID).html(( minutes < 10 ? "0" : "" ) + minutes);
    }, 1000);

    setInterval(function () {
        // Create a newDate() object and extract the hours of the current time on the visitor's
        var hours = new Date().getHours();
        // Add a leading zero to the hours value
        hours = hours > 12 ? hours - 12 : hours;
        $("#" + hourID).html(( hours < 10 ? "0" : "" ) + hours);
    }, 1000);
}

function redirect(url) {
    document.location = url;
    return;
}

function redirectTop(url) {
    window.top.location = url;
    return;
}

function jsonValidate(params, formElem, spinnerElem, method_type) {
    var form = null;

    if (formElem && formElem != '') {

        var form_name = $(formElem).attr("name");
        setSelect2MultiValue(form_name);

        var form = $(formElem).serialize();
    }

    // set spinner active if given
    if (spinnerElem && spinnerElem != '') {
        $(spinnerElem).show();
    }
    // set spinner active if given
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
        timeout: ajax_call_timeout,
        dataType: 'application/json', //defaults to text/html
        data: form, //Can be a Key/Value pair string or object. If it's an object, $.serialize is called to turn it into a Key/Value pair string
        complete: function (resp) {
            // set spinner inactive if given
            if (spinnerElem && spinnerElem != '') {
                $(spinnerElem).hide();
            }
            //console.log('HTTP RESP : ',resp);
            if (resp.status != 200) {
                if (ajax_response_silent == 0) {
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
                data = $.parseJSON(resp.responseText);

                // put html/text in targetElement
                if (data.targetElem) {
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
                    if (data.text) {
                        $(data.targetElem).text(data.text);
                    }
                    // add Class
                    if (data.addClass) {
                        $(data.targetElem).addClass(data.addClass);
                    }
                    // remove Class
                    if (data.removeClass) {
                        $(data.targetElem).removeClass(data.removeClass);
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
                    redirect(data.redirect);
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
    });

    return false;
}


function setTitle(newTitle) {
    //$('html head').find('title').text(newTitle);
    document.title = newTitle;
}


function numOnly(e, decimal) {
    var key;
    var keychar;

    if (window.event) {
        key = window.event.keyCode;
    }
    else if (e) {
        key = e.which;
    }
    else {
        return true;
    }
    keychar = String.fromCharCode(key);

    if ((key == null) || (key == 0) || (key == 8) || (key == 9) || (key == 13) || (key == 27)) {
        return true;
    }
    else if ((("0123456789").indexOf(keychar) > -1)) {
        return true;
    }
    else if (decimal && (keychar == ".")) {
        return true;
    }
    else
        return false;
}

function microtime(get_as_float) {
    // Returns either a string or a float containing the current time in seconds and microseconds  
    // 
    // version: 1109.2015
    // discuss at: http://phpjs.org/functions/microtime    // +   original by: Paulo Freitas
    // *     example 1: timeStamp = microtime(true);
    // *     results 1: timeStamp > 1000000000 && timeStamp < 2000000000
    var now = new Date().getTime() / 1000;
    var s = parseInt(now, 10);
    var t = (get_as_float) ? now : (Math.round((now - s) * 1000) / 1000) + ' ' + s;
    return t.replace(' ', '').replace('.', '');
}


// show preview after upload
function uploadPreview(params, uploaderDiv, removeUploader) {
    $.post(app_rel_url + params, null, function (data) {
        // get preview div
        var previewElem = $(uploaderDiv).prev('.update-thumb');
        // empty preview div
        previewElem.empty();

        // make thumbs
        if ($.isArray(data.images)) {
            for (var i in data.images) {
                if (data.images[i]) {
                    var img = '<a href="' + data.images[i] + '" title="" rel="lightbox"><img src="' + data.images[i] + '" alt="" /></a>';
                    previewElem.append(img);
                }
            }
        }
        else if ($.isArray(data.others)) {
            for (var i in data.others) {
                if (data.others[i]) {
                    var img = '<a target="_blank" href="' + data.others[i] + '">Click Here</a>';
                    previewElem.append(img);
                }
            }
        }
        else {
            if (data.images) {
                var img = '<a href="' + data.images + '" title="" rel="lightbox"><img src="' + data.images + '" alt="" /></a>';
                previewElem.append(img);
            }
            if (data.others) {
                var img = '<a target="_blank" href="' + data.others + '">Click Here</a>';
                previewElem.append(img);
            }

        }

        // remove uploader ?
        if (removeUploader) {
            $uploaderParent = $(uploaderDiv).parent(); // download parent
            $('div.uploadify-queue', $uploaderParent).empty().remove(); // remove uploader queues
            $('div.label_hint', $uploaderParent).empty().remove();  // remove hint
            $(uploaderDiv).remove(); // remove uploader
        }


    }, "json");
}

$.fn.center = function () {
    this.css({
        'position': 'fixed',
        'left': '50%',
        'top': '50%'
    });
    this.css({
        //'margin-left': -this.width() / 2 + 'px',
        'margin-top': -this.height() / 2 + 'px'
    });
    return this;
}

$.fn.centerEvent = function (eventData) {
    this.css({'top': (eventData.pageY - 250)});
    return this;
}


// get url parameters
function getUrlVars(url) {
    var vars = {};
    //var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
    var parts = url.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (m, key, value) {
        vars[key] = value;
    });
    return vars;
}

if (!library)
    var library = {};

library.json = {
    replacer: function (match, pIndent, pKey, pVal, pEnd) {
        var key = '<span class=json-key>';
        var val = '<span class=json-value>';
        var str = '<span class=json-string>';
        var r = pIndent || '';
        if (pKey)
            r = r + key + pKey.replace(/[": ]/g, '') + '</span>: ';
        if (pVal)
            r = r + (pVal[0] == '"' ? str : val) + pVal + '</span>';
        return r + (pEnd || '');
    },
    prettyPrint: function (obj) {
        var jsonLine = /^( *)("[\w]+": )?("[^"]*"|[\w.+-]*)?([,[{])?$/mg;
        return JSON.stringify(obj, null, 3)
            .replace(/&/g, '&amp;').replace(/\\"/g, '&quot;')
            .replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(jsonLine, library.json.replacer);
    }
};
/** Salman Function Ends */


