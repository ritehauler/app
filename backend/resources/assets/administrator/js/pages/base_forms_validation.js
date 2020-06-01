/*
 *  Document   : base_forms_validation.js
 *  Author     : pixelcave
 *  Description: Custom JS code used in Form Validation Page
 */

var BaseFormValidation = function () {
// Init Bootstrap Forms Validation, for more examples you can check out https://github.com/jzaefferer/jquery-validation
    var initValidationBootstrap = function () {
        jQuery('.js-validation-bootstrap').validate({
            errorClass: 'help-block animated fadeInDown',
            errorElement: 'div',
            errorPlacement: function (error, e) {
                jQuery(e).parents('.form-group > div').append(error);
            },
            highlight: function (e) {
                jQuery(e).closest('.form-group').removeClass('has-error').addClass('has-error');
                jQuery(e).closest('.help-block').remove();
            },
            success: function (e) {
                jQuery(e).closest('.form-group').removeClass('has-error');
                jQuery(e).closest('.help-block').remove();
            },
            rules: {
                'username': {
                    required: true,
                    minlength: 3
                },
                'email': {
                    required: true,
                    email: true
                }
                ,
                'country_id': {
                    required: true
                },
                'state_id': {
                    required: true
                },
                'city_id': {
                    required: true
                },
                'is_active': {
                    required: true
                },
                'game_name': {
                    required: true
                },
                'image': {
                    required: true
                },
                'game_donut': {
                    required: true
                },
                'donut_highlight': {
                    required: true
                },
                'status': {
                    required: true
                },
                'territory': {
                    required: true
                },
                'country_ids': {
                    required: true
                },
                'sortname': {
                    required: true
                },
                'country_name': {
                    required: true
                },
                'territory_id': {
                    required: true
                },
                'val-password': {
                    required: true,
                    minlength: 5
                },
                'val-confirm-password': {
                    required: true,
                    equalTo: '#val-password'
                },
                'state': {
                    required: true
                },
                'city': {
                    required: true
                },
                'retailer_name': {
                    required: true
                },
                'retailer_id': {
                    required: true
                },
                'reward_coins': {
                    required: true,
                    number: true
                },
                'change_password': {
                    required: true,
                },
                'change_passcode': {
                    required: true,
                },
                'store': {
                    required: true
                },
                'message': {
                    required: true
                },
                'val-range': {
                    required: true,
                    range: [1, 5]
                },
                'val-terms': {
                    required: true
                },
                'level_number': {
                    required: true
                },
                'level_title': {
                    required: true
                },
                'game_mode': {
                    required: true
                },
                'score': {
                    required: true
                },
                'time': {
                    required: true
                },
                'speed': {
                    required: true
                },
                'game': {
                    required: true
                },
                'mode_title': {
                    required: true
                }
            },
            messages: {
                'username': {
                    required: 'Please enter a username',
                    minlength: 'Your username must consist of at least 3 characters'
                },
                'email': {
                    required: 'Please enter an email'
                },
                'game_name': {
                    required: 'Please enter an game name'
                },
                'image': {
                    required: 'Please select a game image'
                },
                'game_donut': {
                    required: 'Please select a donut color'
                },
                'donut_highlight': {
                    required: 'Please select a donut on hover color'
                },
                'status': {
                    required: 'Please select status'
                },
                'territory': {
                    required: 'Please enter a terrtory name'
                },
                'territory_id': {
                    required: 'Please select a territory'
                },
                'country_ids[]': {
                    required: 'Please select atleast one country'
                },
                'country_name': {
                    required: 'Please enter a country'
                },
                'sortname': {
                    required: 'Please enter a sort name'
                },
                'state': {
                    required: 'Please enter a state name'
                },
                'city': {
                    required: 'Please enter a city name'
                },
                'retailer_name': {
                    required: 'Please enter a retailer name'
                },
                'retailer_id': {
                    required: 'Please select a retailer'
                },
                'store': {
                    required: 'Please enter a store name'
                },
                'reward_coins': {
                    required: 'Please enter a reward points',
                    number: 'Please enter a number'
                },
                'message': {
                    required: 'Please enter a message'
                },
                'change_password': {
                    required: 'Please enter a password',
                },
                'change_passcode': {
                    required: 'Please enter a passcode',
                },
                'level_number': {
                    required: 'Please enter a level number',
                },
                'level_title': {
                    required: 'Please enter a level title',
                },
                'game_mode': {
                    required: 'Please select a game mode',
                },
                'score': {
                    required: 'Please enter score',
                },
                'time': {
                    required: 'Please enter time',
                },
                'speed': {
                    required: 'Please enter speed',
                },
                'game': {
                    required: 'Please select a game',
                },
                'mode_title': {
                    required: 'Please enter a mode title',
                },
//                'val-username': {
//                    required: 'Please enter a username',
//                    minlength: 'Your username must consist of at least 3 characters'
//                },
//                'val-email': 'Please enter a valid email address',
//                'val-password': {
//                    required: 'Please provide a password',
//                    minlength: 'Your password must be at least 5 characters long'
//                },
//                'val-confirm-password': {
//                    required: 'Please provide a password',
//                    minlength: 'Your password must be at least 5 characters long',
//                    equalTo: 'Please enter the same password as above'
//                },
//                'val-suggestions': 'What can we do to become better?',
//                'val-skill': 'Please select a skill!',
                'country_id': 'Please select a country!',
                'state_id': 'Please select a state!',
                'city_id': 'Please select a city!',
                'is_active': 'Please select is active status',
//                'val-website': 'Please enter your website!',
//                'val-digits': 'Please enter only digits!',
//                'val-number': 'Please enter a number!',
//                'val-range': 'Please enter a number between 1 and 5!',
//                'val-terms': 'You must agree to the service terms!'
            }
        });
    };
    // Init Material Forms Validation, for more examples you can check out https://github.com/jzaefferer/jquery-validation
    var initValidationMaterial = function () {
        jQuery('.js-validation-material').validate({
            errorClass: 'help-block text-right animated fadeInDown',
            errorElement: 'div',
            errorPlacement: function (error, e) {
                jQuery(e).parents('.form-group .form-material').append(error);
            },
            highlight: function (e) {
                jQuery(e).closest('.form-group').removeClass('has-error').addClass('has-error');
                jQuery(e).closest('.help-block').remove();
            },
            success: function (e) {
                jQuery(e).closest('.form-group').removeClass('has-error');
                jQuery(e).closest('.help-block').remove();
            },
            rules: {
                'val-username2': {
                    required: true,
                    minlength: 3
                },
                'val-email2': {
                    required: true,
                    email: true
                },
                'val-password2': {
                    required: true,
                    minlength: 5
                },
                'val-confirm-password2': {
                    required: true,
                    equalTo: '#val-password2'
                },
                'val-suggestions2': {
                    required: true,
                    minlength: 5
                },
                'val-skill2': {
                    required: true
                },
                'val-website2': {
                    required: true,
                    url: true
                },
                'val-digits2': {
                    required: true,
                    digits: true
                },
                'val-number2': {
                    required: true,
                    number: true
                },
                'val-range2': {
                    required: true,
                    range: [1, 5]
                },
                'val-terms2': {
                    required: true
                }
            },
            messages: {
                'val-username2': {
                    required: 'Please enter a username',
                    minlength: 'Your username must consist of at least 3 characters'
                },
                'val-email2': 'Please enter a valid email address',
                'val-password2': {
                    required: 'Please provide a password',
                    minlength: 'Your password must be at least 5 characters long'
                },
                'val-confirm-password2': {
                    required: 'Please provide a password',
                    minlength: 'Your password must be at least 5 characters long',
                    equalTo: 'Please enter the same password as above'
                },
                'val-suggestions2': 'What can we do to become better?',
                'val-skill2': 'Please select a skill!',
                'val-website2': 'Please enter your website!',
                'val-digits2': 'Please enter only digits!',
                'val-number2': 'Please enter a number!',
                'val-range2': 'Please enter a number between 1 and 5!',
                'val-terms2': 'You must agree to the service terms!'
            }
        });
    };
    return {
        init: function () {
            // Init Bootstrap Forms Validation
            initValidationBootstrap();
            // Init Meterial Forms Validation
            initValidationMaterial();
        }
    };
}();
// Initialize when page loads
jQuery(function () {
    BaseFormValidation.init();
});