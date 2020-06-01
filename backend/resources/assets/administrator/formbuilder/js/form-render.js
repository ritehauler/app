/*
formBuilder - http://kevinchappell.github.io/formBuilder/
Version: 1.7.6
Author: Kevin Chappell <kevin.b.chappell@gmail.com>
*/
'use strict';

//Star Rating Start

(function ( $ ) {
 
    $.fn.rating = function( method, options ) {
    method = method || 'create';
        // This is the easiest way to have default options.
        var settings = $.extend({
            // These are the defaults.
      limit: 5,
      value: 0,
      glyph: "glyphicon-star",
            coloroff: "gray",
      coloron: "gold",
      size: "2.0em",
      cursor: "default",
      onClick: function () {},
            endofarray: "idontmatter"
        }, options );
    var style = "";
    style = style + "font-size:" + settings.size + "; ";
    style = style + "color:" + settings.coloroff + "; ";
    style = style + "cursor:" + settings.cursor + "; ";
  

    
    if (method == 'create')
    {
      //this.html('');  //junk whatever was there
      
      //initialize the data-rating property
      this.each(function(){
        var attr = $(this).attr('data-rating');
        if (attr === undefined || attr === false) { $(this).attr('data-rating',settings.value); }
      })
      
      //bolt in the glyphs
      for (var i = 0; i < settings.limit; i++)
      {
        this.append('<span data-value="' + (i+1) + '" class="ratingicon glyphicon ' + settings.glyph + '" style="' + style + '" aria-hidden="true"></span>');
      }
      
      //paint
      this.each(function() { paint($(this)); });

    }
    if (method == 'set')
    {
      this.attr('data-rating',options);
      this.each(function() { paint($(this)); });
    }
    if (method == 'get')
    {
      return this.attr('data-rating');
    }
    //register the click events
    this.find("span.ratingicon").click(function() {
      var rating = $(this).attr('data-value')
      $(this).parent().attr('data-rating',rating);
      paint($(this).parent());
      settings.onClick.call( $(this).parent() );
    })
    function paint(div)
    {
      var rating = parseInt(div.attr('data-rating'));
      div.find("input").val(rating);  //if there is an input in the div lets set it's value
      div.find("span.ratingicon").each(function(){  //now paint the stars
        
        var rating = parseInt($(this).parent().attr('data-rating'));
        var value = parseInt($(this).attr('data-value'));
        if (value > rating) { $(this).css('color',settings.coloroff); }
        else { $(this).css('color',settings.coloron); }
      })
    }

    };
 
}( jQuery ));


//Star Rating End

(function ($) {
  'use strict';

  var Toggle = function Toggle(element, options) {

    var defaults = {
      theme: 'fresh',
      labels: {
        off: 'Off',
        on: 'On'
      }
    };

    var opts = $.extend(defaults, options),
        $kcToggle = $('<div class="kc-toggle"/>').insertAfter(element).append(element);

    $kcToggle.toggleClass('on', element.is(':checked'));

    var kctOn = '<div class="kct-on">' + opts.labels.on + '</div>',
        kctOff = '<div class="kct-off">' + opts.labels.off + '</div>',
        kctHandle = '<div class="kct-handle"></div>',
        kctInner = '<div class="kct-inner">' + kctOn + kctHandle + kctOff + '</div>';

    $kcToggle.append(kctInner);

    $kcToggle.click(function () {
      element.attr('checked', !element.attr('checked'));
      $(this).toggleClass('on');
    });
  };

  $.fn.kcToggle = function (options) {
    var toggle = this;
    return toggle.each(function () {
      var element = $(this);
      if (element.data('kcToggle')) {
        return;
      }
      var kcToggle = new Toggle(element, options);
      element.data('kcToggle', kcToggle);
    });
  };
})(jQuery);
// render the formBuilder XML into html
'use strict';

(function ($) {
  'use strict';
  $.fn.formRender = function (options) {
    var $template = $(this),
        defaults = {
      destroyTemplate: true, // @todo
      container: false,
      label: {
        selectColor: 'Select Color'
      }
    },
        _helpers = {};

    var opts = $.extend(defaults, options);

    /**
     * Generate markup wrapper where needed
     * @param  {string} type
     * @param  {object} attrs
     * @param  {string} content we wrap this
     * @return {string}
     */
    _helpers.markup = function (type) {
      var attrs = arguments.length <= 1 || arguments[1] === undefined ? {} : arguments[1];
      var content = arguments.length <= 2 || arguments[2] === undefined ? '' : arguments[2];

      attrs = _helpers.attrString(attrs);
      content = Array.isArray(content) ? content.join('') : content;
      var inlineElems = ['input'],
          template = inlineElems.indexOf(type) === -1 ? '<' + type + ' ' + attrs + '>' + content + '</' + type + '>' : '<' + type + ' ' + attrs + '/>';
      return template;
    };

    /**
     * Generate preview markup
     * @param  {object} field
     * @return {string}       preview markup for field
     * @todo
     */
    _helpers.fieldRender = function (field) {
      var fieldMarkup = '',
          fieldLabel = '',
          optionsMarkup = '';
      var fieldAttrs = _helpers.parseAttrs(field.attributes),
          fieldDesc = fieldAttrs.description || '',
          fieldRequired = '',
          fieldOptions = $('option', field);
      fieldAttrs.id = fieldAttrs.name;
      if (fieldAttrs.type !== 'checkbox') {
        fieldAttrs.className = 'form-control';
      }

      if (fieldAttrs.required) {
        fieldAttrs.required = null;
        fieldAttrs['aria-required'] = 'true';
        fieldRequired = '<span class="required">*</span>';
      }

      if (fieldAttrs.type !== 'hidden') {
        if (fieldDesc) {
          fieldDesc = '<span class="tooltip-element" tooltip="' + fieldDesc + '">?</span>';
        }
        fieldLabel = '<label for="' + fieldAttrs.id + '">' + fieldAttrs.label + ' ' + fieldRequired + ' ' + fieldDesc + '</label>';
      }

      delete fieldAttrs.label;
      delete fieldAttrs.description;

      var fieldAttrsString = _helpers.attrString(fieldAttrs);

      switch (fieldAttrs.type) {
        case 'textarea':
        case 'rich-text':
          delete fieldAttrs.type;
          delete fieldAttrs.value;
          fieldMarkup = fieldLabel + '<textarea ' + fieldAttrsString + '></textarea>';
          break;
        case 'select':
          fieldAttrs.type = fieldAttrs.type.replace('-group', '');

          if (fieldOptions.length) {
            fieldOptions.each(function (index, el) {
              index = index;
              var optionAttrs = _helpers.parseAttrs(el.attributes),
                  optionAttrsString = _helpers.attrString(optionAttrs),
                  optionText = el.innerHTML || el.innerContent || el.innerText || el.childNodes[0].nodeValue || el.value;
              optionsMarkup += '<option ' + optionAttrsString + '>' + optionText + '</option>';
            });
          }
          fieldMarkup = fieldLabel + '<select ' + fieldAttrsString + '>' + optionsMarkup + '</select>';
          break;
        case 'checkbox-group':
        case 'radio-group':
          fieldAttrs.type = fieldAttrs.type.replace('-group', '');

          delete fieldAttrs.className;

          if (fieldOptions.length) {
            (function () {
              var optionName = fieldAttrs.type === 'checkbox' ? fieldAttrs.name + '[]' : fieldAttrs.name;
              fieldOptions.each(function (index, el) {
                var optionAttrs = $.extend({}, fieldAttrs, _helpers.parseAttrs(el.attributes)),
                    optionAttrsString = undefined,
                    optionText = undefined;

                if (optionAttrs.selected) {
                  delete optionAttrs.selected;
                  optionAttrs.checked = null;
                }

                optionAttrs.name = optionName;
                optionAttrs.id = fieldAttrs.id + '-' + index;
                optionAttrsString = _helpers.attrString(optionAttrs);
                optionText = el.innerHTML || el.innerContent || el.innerText || el.value || '';

                optionsMarkup += '<input ' + optionAttrsString + ' /> <label for="' + optionAttrs.id + '">' + optionText + '</label><br>';
              });
            })();
          }
          fieldMarkup = fieldLabel + '<div class="' + fieldAttrs.type + '-group">' + optionsMarkup + '</div>';
          break;
        case 'text':
        case 'password':
        case 'email':
        case 'hidden':
        case 'date':
        case 'time':
        case 'file':
        case 'autocomplete':
          fieldMarkup = fieldLabel + ' <input ' + fieldAttrsString + '>';
          break;
        case 'rating':
          fieldMarkup = fieldLabel + '<div class="starRating" data-rating="0"><input ' + fieldAttrsString + '><span data-value="1" class="ratingicon glyphicon glyphicon-star" style="font-size: 2em; color: gold; cursor: default;" aria-hidden="true"></span><span data-value="2" class="ratingicon glyphicon glyphicon-star" style="font-size: 2em; color: gold; cursor: default;" aria-hidden="true"></span><span data-value="3" class="ratingicon glyphicon glyphicon-star" style="font-size: 2em; color: gold; cursor: default;" aria-hidden="true"></span><span data-value="4" class="ratingicon glyphicon glyphicon-star" style="font-size: 2em; color: gold; cursor: default;" aria-hidden="true"></span><span data-value="5" class="ratingicon glyphicon glyphicon-star" style="font-size: 2em; color: gold; cursor: default;" aria-hidden="true"></span></div>';
          break;  
        case 'color':
          fieldMarkup = fieldLabel + ' <input ' + fieldAttrsString + '> ' + opts.label.selectColor;
          break;
        case 'checkbox':
          fieldMarkup = '<input ' + fieldAttrsString + '> ' + fieldLabel;

          if (fieldAttrs.toggle) {
            setTimeout(function () {
              $(document.getElementById(fieldAttrs.id)).kcToggle();
            }, 100);
          }
          break;
        default:
          fieldMarkup = '<' + fieldAttrs.type + '></' + fieldAttrs.type + '>';
      }

      if (fieldAttrs.type !== 'hidden') {
        fieldMarkup = _helpers.markup('div', {
          className: 'form-group field-' + fieldAttrs.id
        }, fieldMarkup);
      }

      return fieldMarkup;
    };

    _helpers.attrString = function (attrs) {
      var attributes = [];

      for (var attr in attrs) {
        if (attrs.hasOwnProperty(attr)) {
          attr = _helpers.safeAttr(attr, attrs[attr]);
          attributes.push(attr.name + attr.value);
        }
      }
      console.log(attributes);
      return attributes.join(' ');
    };

    _helpers.safeAttr = function (name, value) {
      var safeAttr = {
        className: 'class'
      };

      name = safeAttr[name] || name;
      value = window.JSON.stringify(value);
      value = value ? '=' + value : '';

      return {
        name: name,
        value: value
      };
    };

    _helpers.parseAttrs = function (attrNodes) {
      var fieldAttrs = {};
      for (var attr in attrNodes) {
        if (attrNodes.hasOwnProperty(attr)) {
          fieldAttrs[attrNodes[attr].nodeName] = attrNodes[attr].nodeValue;
        }
      }
      return fieldAttrs;
    };

    // Begin the core plugin
    this.each(function () {
      var rendered = [];

      var formData = $.parseXML($template.val()),
          fields = $('field', formData);
      // @todo - form configuration settings (control position, creatorId, theme etc)
      // settings = $('settings', formData);

      if (!formData) {
        alert('No formData. Add some fields and try again');
        return false;
      }

      // generate field markup if we have fields
      if (fields.length) {
        fields.each(function (index, field) {
          index = index;
          rendered.push(_helpers.fieldRender(field));
        });
      }

      var output = rendered.join('');

      if (opts.container && opts.container.length) {
        opts.container.html(output);
      } else {
        $template.replaceWith(output);
      }
    });
  };
})(jQuery);