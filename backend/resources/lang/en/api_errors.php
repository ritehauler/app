<?php

return [
    'success' => 'Success',
    'invalid_user_request' => 'Invalid user Request',
    'email_already_exists' => 'Email already exists',
    'check_email_for_confirmation' => 'Please check your email for confirmation',
	'check_phone_for_confirmation' => 'Please check your Phone for confirmation code',
    "not_authorized_for_webservice" => "You are not authorized to access this service.",
    "cannot_follow_self" => "Cannot follow self",
    "cannot_friend_self" => "Cannot friend self",
    "invalid_record_request" => "Invalid record request",
    "attribute_in_use" => "Attribute in use",
    "entity_type_in_use" => "Entity type in use",
    "role_permission_in_use" => "Role permission in use",
    "attribute_set_in_use" => "Attribute set in use",
    "attribute_delete_success" => "Attribute deleteted successfully",
    "entity_type_delete_success" => "Entity type deleteted successfully",
    "record_requested_is_already_exist" => "Record already exist.",
    "your_account_is_baned_removed" => "Your account is either inactive or ban",
    "access_not_allowed" => "Access not allowed",

    // entity errors
    'invalid_entity_request' => 'Invalid :entity Request',
    "pls_enter_entity_id" => "Please enter :entity ID",
    'entity_is_invalid' => ':entity is invalid',
    'entity_is_required' => ':entity is required',
    "already_following" => "You are already following this :entity",
    "entity_already_exists" => ":entity already exists",
    "already_liked" => "You have already liked this :entity",
    "cannot_add_friend" => "Cannot add selected :entity as friend",
    "entity_does_not_exists" => ":entity does not exist",
    'entity_updated_successfully' => ':entity updated successfully',
    // entity crud
    'entity_successfully_saved' => ':entity successfully saved',
    'entity_successfully_updated' => ':entity successfully updated',
    'entity_successfully_removed' => ':entity successfully removed',
    'email_already_exist_other_department' => 'You are already signed up with this email as a :department',

    // other
    "forgot_password_reset_successfully" => "Your password reset successfully",
    "your_account_is_inactive" => "Your account is inactive",
    'backend_user_created' => 'User created successfully and confirmation email is sent to user',
    'user_field_missing' => 'Any of field :entity is missing',
    'account_already_verified' => 'Account already verified',
    'auth_department_already_exist' => 'User already exist with this department',
    'user_created_successfully' => 'User created successfully',
    'entity_is_incorrect' => ':entity is incorrect',
    'account_verification_success' => 'Your account is verified successfully',
    'refer_friend_code_not_found' => 'Please update :entity, There is no friend refer code',
    'invalid_refer_code' => 'Invalid refer code applied',
	'change_password_success' => 'You have successfully changed your password',

    //Inventory
    'package_quantity_exceeds' => 'Package quantity exceed',
    'wastage_greater_than_quantity' => 'Wastage can not be greater than quantity',

	//coupon
	'coupon_expired' => 'Coupon is expired',
	'coupon_valid_for_special' => 'Coupon is valid for special customer',
	'coupon_minimum_order' => 'Coupon is valid for minimum order :min_order',

	//recipe
	'new_quantity_numeric' => 'Add Quantity must be numeric',
	'expiry_must_greater_than_stock' => 'Expiry date must be greater than stock-in date',
	'expiry_must_greater_start_date' => 'Expiry date must be greater than start date',
	'end_must_greater_start_date' => 'End date must be greater than start date',

    //image max size validation
    'max_size_image' => 'Please use the correct file size of max 1 MB',

    'quantity_greater_than_stock' => 'The :entity quantity cannot be :action greater than stock',
    'no_box_found' => 'No item box found for these dimensions',
    'invalid_dimension' => 'The width, height and length should be valid integers',
    'truck_selected_required' => 'The truck selected id is required',
    'field_required' => 'The :field field is required',
    'truck_not_capable' => 'The truck is not capable, Please update the items width, height, length and weight',

    'shift_to_greater_shift_from' => 'Sift To must be greater than Shift From',
    'data_not_found' => 'Data not found',
    'order_cannot_complete' => 'Order can not completed before driver reached'
];
