/**
* Activate admin navidation
* 
* @param string nav_id
*/
function setAdminNav(nav_id) {
	var mainNavElem = $("ul[id=admin_nav_set]");
	var regex = new RegExp( '\\b-\\b' );
	// de-active list
	$("> li",mainNavElem).removeClass("open");
	// de-active list anchors
	$("> li a",mainNavElem).removeClass("active");
	// deactivate childs anchors
	$("> li ul li a",mainNavElem).removeClass("active");
	
	// if has child, active child
	if(regex.test(nav_id)) {
		var navElem = nav_id.split('-');
		var parentElem = $("> li[id='"+navElem[0]+"']",mainNavElem);
		// open master
		parentElem.addClass("open");
		// activate child
		$("> ul li[id='"+nav_id+"'] a", parentElem).addClass("active");
	} else {
		// acticate current
		$("> li[id='"+nav_id+"'] a",mainNavElem).addClass("active");
	}
}

// data grid var
var dg_ajax_params = {}; // data grid object for ajax params
// data grid search functionality extention
function dgSearch(dg) {
	console.log('dgSearch');
	// Apply the filters (search/resets)
	dg.column().every( function () {
		var column = this;
		// every field change
		/*$( 'input, select', this.footer() ).on( 'change', function () {
			//column.search( this.value );
			dg_ajax_params[this.name] = this.value;
			console.log(this.value);
		});
*/

		// action button changes
		// - reset
		$( 'a#grid_reset', this.footer() ).on( 'click', function () {
           /* $("select").multiselect({
                selectAll: false
            });*/
			$.each($('#entity-search-fields').find('select, textarea, input[type="text"],input[type="input"]'), function() {
				this.value = '';
			});
			dg_ajax_params = {};
			dg.ajax.reload();
		});
		// -search
		$( 'a#grid_search', this.footer() ).on( 'click', function () {

			var search_column = {};
			$.each($('#entity-search-fields').find('select, textarea, input').serializeArray(), function() {
				if(this.value != ""){
					search_column[this.name] = this.value;
				}

			});
			console.log(search_column);
			dg_ajax_params = search_column;
			//return false;
			dg.ajax.reload();
		});
	});
}
// data grid select actions
function dgSelectActions(dg) {
	// on multi-select
	// select chechbox
	$("input[name=check_all]").on( "change", function() {

		if($(".permission_checkbox").length > 0){
			$("input.permission_checkbox").prop("checked",$(this).prop("checked"));
		}else{
			$("input[name='check_ids[]']").prop("checked",$(this).prop("checked"));
		}

	});


	// on select action
	$('.select_action').on('click', function () {
		var select_elem = $(this);
        var dg_action = $(select_elem).attr('title');
        var elem_name = $(select_elem).prop("class");
        var elem_value = $(select_elem).attr('title');
		// if any action selected
		if (dg_action != "") {
			// init checked attribute
			dg_ajax_params.checked_ids = [];
			dg_ajax_params.sequence_arr = [];
			// assign current actions
			dg_ajax_params[elem_name] = elem_value;

			// assign checked ids
			$("input[name='check_ids[]']:checked").each(function (index, value) {
				dg_ajax_params.checked_ids.push(this.value);
				dg_ajax_params.sequence_arr.push($("input[id='sequence_arr_"+this.value+"']").val());
			});
			// has items
			if (dg_ajax_params.checked_ids.length > 0) {
				// if delete
				//if(dg_action == "delete") {
				// ask for confimation
				bootbox.confirm("Are you sure you want to " + ucFirst(dg_action) + " selected items?", function (result) {
					if (result === true) {
						dg_ajax_params[elem_name] = elem_value;
						dg.ajax.reload();
					}
				});
				/*} else {
				 dg.ajax.reload();
				 }*/
			} else {
				bootbox.alert("Please select items to " + ucFirst(dg_action));
			}
			select_elem.val("");
			select_elem.trigger("change");
			// reset master checkbox
			$("input[name=check_all]").prop("checked", false);
			// remove current action action for next request
			delete dg_ajax_params[elem_name];
		}

	});
	// on del action
	$(dg.context[0]["nTable"]).on( 'draw.dt', function () {
		$( 'a.grid_action_del', this ).click(function() {
			// uncheck others
			$("input[name='check_ids[]']").prop("checked",false);
			// mark current item's checkbox as checked
			$("input[name='check_ids[]']",$(this).parents("tr")).prop("checked",true);
			// select del action and trigger
			//$( 'select[name=select_action] option[value="delete"]' ).prop("selected",true).trigger("change");
            $('.select_action').trigger("click");
		});
		
		$( "input[name='check_ids[]']", this ).change(function() {
			// if all checked
			if($("input[name='check_ids[]']").length == $("input[name='check_ids[]']:checked").length) {
				$("input[name=check_all]").prop("checked",true);
			} else {
				$("input[name=check_all]").prop("checked",false);
			}
		});
	});
	
}
// data grid double scroll
function dgDoubleScroll(dg) {
	// preserver old style n apply overflow-x : hidden
	$(dg.context[0]["nTableWrapper"]).attr('style', function(i,s) { return (s ? s : "") + 'overflow-x: hidden !important;' });
	$("div.row:nth-child(2)",$(dg.context[0]["nTableWrapper"])).doubleScroll();
}
// data grid double scroll  for export buttons
function dgDoubleScroll2(dg) {
	$(dg.context[0]["nTable"]).on( 'draw.dt', function () {
		//$("div.b_scroller").doubleScroll();
		// preserver old style n apply overflow-x : hidden
		$(dg.context[0]["nTableWrapper"]).attr('style', function(i,s) { return (s ? s : "") + 'overflow-x: hidden !important;' });
		$("div.row:nth-child(2)",$(dg.context[0]["nTableWrapper"])).doubleScroll();
		$(".suwala-doubleScroll-scroll-wrapper").css("width","100%");
		$(".suwala-doubleScroll-scroll").html($(this).clone());
	});
}


function ucFirst(string) {
	return string.substring(0, 1).toUpperCase() + string.substring(1).toLowerCase();
}



$(function () {

    // Init page helpers (Slick Slider plugin)
    //App.initHelpers('slick');
    //// Init page helpers (Table Tools helper)
    //App.initHelpers('table-tools');
	// Init page helpers (Summernote + CKEditor plugins)
	//App.initHelpers(['summernote', 'ckeditor']);
	
	
	
	/**
	* Miscellenous Events
	* 
	*/
	// delete item action (show confirmation modal)
	$('.action_del_item').on("click",function(e) {
		e.preventDefault();
		// target modal
		var tar_modal = "#modal_delete_item";
		// get values
		var del_form = $(this).data("form") ? $(this).data("form") : "";
		var del_mod_url = $(this).data("module_url") ? $(this).data("module_url") : "";
		var del_item_id = $(this).data("item_id") ? $(this).data("item_id") : 0;
		// assign item id to modal fields
		$(tar_modal+" input#form").val(del_form);
		$(tar_modal+" input#item_id").val(del_item_id);
		$(tar_modal+" input#module_url").val(del_mod_url);
		// show delete modal
		$(tar_modal).modal("show");
	});
	/*// delete item btn (after confimation)
	$('#modal_delete_item #btn_close').on("click",function(e) {
		e.preventDefault();
		// target modal
		var tar_modal = "#modal_delete_item";
		// assign item id to modal fields (reset)
		$(tar_modal+" input#item_id").val(0);
		$(tar_modal+" input#module_url").val("");
		console.log("close");
		console.log("del_item_id",del_item_id);
		console.log("del_mod_url",del_mod_url);
	});*/
	// delete item btn (after confimation)
	$('#modal_delete_item #btn_ok').on("click",function(e) {
		e.preventDefault();
		// target modal
		var tar_modal = "#modal_delete_item";
		// get values
		var del_form = $(tar_modal+" input#form").val();
		del_form = "form[name="+del_form+"]";
		var del_mod_url = $(tar_modal+" input#module_url").val();
		var del_item_id = $(tar_modal+" input#item_id").val();
		// allot form action
		if(del_mod_url != "") {
			$(del_form).prop("action",del_mod_url);
		}
		console.log("del_item_id",del_item_id);
		// allot selected ids
		$(del_form+" input[name='delete_ids[]']").val(del_item_id);
		// assign delete action to target form
		$(del_form+" input[name=do_delete]").val(1);
		// submit form
		$(del_form).submit();
	});


	/*Text Richer editor*/
	$( '.field_retchtext' ).ckeditor();



});


$(document).ready(function(){

	$('.select2-field').select2();


	/*Calendar for data type is time*/
   /* $(".field_time").timepicker({
    });*/

	/*Calendar for data type is date*/
	$('.field_time').each(function(i) {

       $(this).removeClass('hasDatepicker');

		this.id = 'timepicker' + i;
	}).timepicker({
		//timeFormat: 'h:mm:ss p',
		//'showDatepicker': false
	});

	/*Calendar for data type is date*/
	$('.field_date').each(function(i) {
		this.id = 'datepicker' + i;
	}).datepicker({dateFormat: 'yy-mm-dd'});

	//added class for first heading column and last
	if($('#mydatatable').length > 0){

		if($("#check_all").length > 0){
			if($('.permissionTable').length > 0)
				$("#mydatatable th:first-child").addClass('list-checkbox-role');
			else
			$("#mydatatable th:first-child").addClass('list-checkbox');
		}

		$("#mydatatable th:last-child").addClass('list-option');
	}

	clearAlert();

	/*when cick on add more items this will copy first item html and add more*/
	if($(".add-more-entity").length > 0){


		$("#data_form .add-more-entity").on("click",function() {

			var div_size = $('div.bulk_entity_raw').size();
			var current_key = div_size++;
			var divID = 'bulk_entity_raw_' +current_key;

			/*if($("#entity_type_identifier").length > 0) {
				if ($("#entity_type_identifier").val() == "order") {
					var html_append = $("#temp_depend_item").html();
				}
			}
			else{*/
				var selector = $(".bulk_entity_wrap").children('div.bulk_entity_raw').first();
				var delete_anchor = '<a style="float:right" class="fa fa-times delete-depend-entity" id="delete-depend-entity-1" href="javascript:void(0);"></a>';

			if($('#entity_type_identifier').length > 0 && $('#entity_type_identifier').val() == 'order'){
				delete_anchor = "";
			}

			var html_append = "<div id='" + divID + "' class='" + selector.attr('class') + "'>" + delete_anchor + selector.html() + "</div>";
			/*}*/


			$(".bulk_entity_wrap").append(html_append);
			var currentDivID = 'bulk_entity_raw_' + current_key;

			//if entity type is recipe
			if ($('#itemWrap').length > 0) {
				//if($('#entity_type_identifier').val() == "recipe"){
				$('#' + divID).find('#itemWrap .item_value').text('');
				$('#' + divID).find('#itemWrap').addClass('hide');
				//}
			}

			if ($('#' + currentDivID).find(".dropzoneFileUpload").length > 0) {
				$('#' + currentDivID).find('.dropzoneFileUpload .dz-preview').remove();
			}

			if($('#entity_type_identifier').length > 0 && $('#entity_type_identifier').val() == 'order'){
				if($('#product_id').length >0){
					$('#' + currentDivID).find('input[name="product_id"]').remove();
					$('#' + currentDivID).find('#product_id').attr('name','product_id');
				}
			}

			//console.log(currentDivID);
			//order module then remove fields labels
			if($("#entity_type_identifier").length > 0){
				if($("#entity_type_identifier").val() == "order"){

					/*if($('#' + currentDivID).find('div.section').length > 0){
						$.each($('#' + currentDivID).find('div.section'),function(){
							$(this).find('.field-label').remove();
						});
					}*/

					$('#' + currentDivID).find('input#entity_id').val('');
                    $('#' + currentDivID).find('input#item_name').val('');

                    if($('#' + currentDivID).find('.attachment-thumb').length > 0)
                    {
                        $('#' + currentDivID).find('.attachment-thumb').parent('div.section').remove();
                    }

				}
			}


			//Reset the all div.bulk_entity_raw ids
			$.each($('.bulk_entity_raw'), function (k, v) {

				$(this).attr("id", "bulk_entity_raw_" + k);
				$(this).find('a.delete-depend-entity').attr('id', 'delete-depend-entity-' + k);

				$(this).find('input.is_new_package').attr('name', 'is_new_package_' + k);

				//Remove the datepicker selector class if exist
				if ($(this).find('.field_date').length > 0) {
					$(this).find('.field_date').removeClass('hasDatepicker');
				}

                if ($(this).find('.field_time').length > 0) {
                    $(this).find('.field_time').removeClass('hasDatepicker');
                }

				if ($(".dropzoneFileUpload").length > 0) {
					//$(this).find('.dropzoneFileUpload').attr('id','');
					$(this).find('.dropzoneFileUpload').attr('id', 'dropzoneFileUpload_' + k);
					//if(k != 0)

				}

			});


			/*Calendar for data type is date*/
			if ($('.field_date').length > 0) {
				$('.field_date').each(function (i) {
					this.id = 'datepicker' + i;
				}).datepicker({dateFormat: 'yy-mm-dd'});
			}

            /*Calendar for data type is time*/
            if ($('.field_time').length > 0) {
                $('.field_time').each(function (i) {
                    this.id = 'timepicker' + i;
                }).timepicker({ });
            }


			//console.log("#"+divID+".cb_bu_info");
			if ($("#" + currentDivID + " .cb_bu_info").length > 0){

					$("#" + currentDivID + " .cb_bu_info").next("div").remove();
					$("#" + currentDivID + " .cb_bu_info").removeAttr('disabled');

					$("#" + currentDivID + " .cb_bu_info").chosen({
						search_contains: true,
						width: "100%",
						no_results_text: "not found",
						display_disabled_options: false
					});

					//search dropdown for entity
					$("#" + currentDivID + " .chosen-search input").autocomplete({
						minLength: 1,
						source: function (request, response) {
							var entity_type_id = $($(this)[0].element).closest(".getchoosen").find("select").data("type_id");
							var attribute_code = $($(this)[0].element).closest(".getchoosen").find("select").data("attribute_code");
							var chosen_id = $($(this)[0].element).closest(".getchoosen").find("select").attr("id");

							chosen_id = divID + " #" + chosen_id;
							//if module is recipe then call updateSearchItem which will get item information too
							if ($('#entity_type_identifier').length > 0) {
								if ($('#entity_type_identifier').val() == "recipe") {
									updateSearchItem(entity_type_id, chosen_id, request.term, attribute_code);
								}
							}
							else {
								updateSearch(entity_type_id, chosen_id, request.term, attribute_code);
							}

						}
					});

		}

			/*if ($("#" + currentDivID + " .select2-field").length > 0){
				$("#" + currentDivID + ".select2-field").select2('destroy');
				$("#" + currentDivID + ".select2-field").select2();
			}*/

            $('.bulk_entity_wrap').each(function(index)
            {
                if ($(this).data('select2')) {
                    $(this).select2('destroy');
                }
            });

           // console.log( $("#"+currentDivID).find('.select2-field').next('.select2-container'));

            $("#"+currentDivID).find('.select2-field').next('.select2-container').remove();

           $('.select2-field').select2();


            if($('#'+currentDivID).find(".dropzoneFileUpload").length > 0){
				var gallery = {};
				loadFileUpload('bulk_entity_raw_'+current_key,current_key,gallery);
			}

		});

		/*when package add more is clicked*/
	/*	$("#package_form .add-more-entity").on("click",function(){
			var selector = $(".bulk_entity_wrap").children('div.bulk_entity_raw').first();
			var div_size = $('div.bulk_entity_raw').size();

			var delete_anchor = '<a style="float:right" class="fa fa-times delete-depend-entity" id="delete-depend-entity-1" href="javascript:void(0);"></a>';
			var divID = 'bulk_entity_raw_'+div_size++;

			var html_append = "<div id='"+divID+"' class='"+selector.attr('class')+"'>"+delete_anchor+selector.html()+"</div>";

			//make radio button name unique
			html_append = html_append.replace('is_new_package_0', 'is_new_package_'+div_size++);
			$(".bulk_entity_wrap").append(html_append);

			//get last div added
			var added_selector = $(".bulk_entity_wrap").children('div.bulk_entity_raw').last();
			var added_selector_id = added_selector.attr('id');

			//check radio button with new package is 1 and show title field and hide package dropdown
			$("#"+added_selector_id+" #package_options  input[type=radio][value=1]").prop('checked', true);
			$("#"+added_selector_id+" #title").parents("div.col-md-6").show();
			$("#"+added_selector_id+" #package_id").parents("div.col-md-6").hide();

			//reset div, radio and anchor ids
			$.each($('.bulk_entity_raw'),function(k,v){

				$(this).attr("id","");
				$(this).attr("id","bulk_entity_raw_"+k);
				$(this).find('a.delete-depend-entity').attr('id','delete-depend-entity-'+k);
				$(this).find('input.is_new_package').attr('name','is_new_package_'+k);
			});


		});*/

	}
	/*this is for verify package*/
/*	$(".verify_package").on("click",function(){

		var is_verify  = verifyPackage();

		if($.isNumeric(is_verify)){
			if($.trim(is_verify) == 1){
				$("#is_verify").val(1);
				showSuccessAlert('Successfully verified');
				hideAlert();
			}
			else{
				showAlert('Sorry Package cannot create because total inventory is exceed');
			}
		}
		else{
			showAlert(is_verify);
		}


	});*/


	/*this is for verify package*/
	/*$( document ).on( "click", ".is_new_package", function() {
		checkExistingPackage($(this));
	});
*/
	/*this is for verify Recipe now recipe is rename to package but on backend it is recipe*/
	$(".verify_recipe").on("click",function(){

		var is_verify  = verifyRecipe();

		if($.isNumeric(is_verify)){
			if($.trim(is_verify) == 1){
				$("#is_verify").val(1);
				showSuccessAlert('Successfully verified');
			}
			else{
				showAlert('Sorry Package cannot create because total inventory is exceed');
			}
		}
		else{
			showAlert(is_verify);
		}


	});

	/**
	 * Delete depend div raw
	 */
	$( document ).on( "click", ".delete-depend-entity", function(e) {
		e.preventDefault();
		if($(".bulk_entity_raw").length == 1){
			showAlert("Sorry item cannot remove, first add more item to remove this item.");
		}else{
			var select_id = $(this).attr('id');

			var select = $('#'+select_id).parent('div.bulk_entity_raw').remove();

			if($('#delete_depend_entity_id').length > 0){

                var depend_entity_id = $(this).data('depend_entity_id');

                if($('#delete_depend_entity_id').val() == ''){
                    $('#delete_depend_entity_id').val(depend_entity_id)
                }
                else{
                    $('#delete_depend_entity_id').val($('#delete_depend_entity_id').val()+','+depend_entity_id);
                }
			}

		}

	});

	$('.export_entity').on('click',function(){
		$('#searchEntity').submit();
	});

	$('.export_template').on('click',function(){
		$('#import_form').append('<input type="hidden" name="download_template" value="1" />');
		$('#import_form').submit();
	});

});

function verifyPackage()
{
	var order_inventory= 0;
	var total_inv = 0;
	var total_inventory = $("#total_inventory").val();
	var is_verify = 0;
	var is_check = 1;


	$.each($('.bulk_entity_raw'),function(k,v){
		if($(this).find('input#weight').val() != "" && $(this).find('input#quantity').val() != ""){

			order_inventory = parseFloat($(this).find('input#weight').val()*$(this).find('input#quantity').val());
			total_inv += order_inventory;
		}
		else{
			is_check = 0;
			is_verify = 'Please Enter Weight and Quantity';
			return false;
		}
	});

	//console.log(total_inventory);  console.log(total_inv);
	if(is_check == 1){
		if(total_inv > total_inventory){
			is_verify = 0;
		}
		else{
			is_verify = 1;
		}
	}

	return is_verify;
}

function showAlert(msg)
{
	hideAlert();
	addErrorMsg(msg);

}

function hideAlert(){

	if($("form .alert-success").length > 0){
		$("form .alert-success").remove();
	}
	if($("form .alert-danger").length > 0){
		$("form .alert-danger").remove();
	}
}

function showSuccessAlert(msg){
	hideAlert();
	addSuccessMsg(msg);
}


function clearAlert(){
	setTimeout(function() {

		hideAlert();
	}, 50000);
}

function addErrorMsg(message)
{
	$('form .alert-message').append(alertMsg(message));
	$("form .alert-danger").focus();
}


function addSuccessMsg(message)
{
	$('form .alert-message').append(alertSuccessMsg(message));
	$("form .alert-success").focus();
}

function alertMsg(message)
{
   return '<div class="alert alert-danger"> <a href="#" class="close" data-dismiss="alert">&times;</a>'+message+'</div>';
}

function alertSuccessMsg(message)
{
    return '<div class="alert alert-success"> <a href="#" class="close" data-dismiss="alert">&times;</a>'+message+'</div>';
}

function packageResponse(data_reponse)
{
	//  console.log( $('#bulk_entity_raw_'+data_reponse.data.bulk_entity_raw));
		if(data_reponse.data.total_inventory){
			$("#total_inventory").val(data_reponse.data.total_inventory);
			$('#total_inv').text(data_reponse.data.total_inventory);
		}
}

function checkExistingPackage(ele)
{	//console.log(ele);
	var div_id = ele.parents('div.bulk_entity_raw').attr('id');
	//console.log(div_id);
	if(ele.val() == 1){
		$("#"+div_id+" #package_id").parents("div.col-md-6").hide();
		$("#"+div_id+" #title").parents("div.col-md-6").show();
	}
	else{
		$("#"+div_id+" #title").parents("div.col-md-6").hide();
		$("#"+div_id+" #package_id").parents("div.col-md-6").show();
	}
}

function verifyRecipe()
{
	var is_verify = 1;

	var item_list = [];
	$.each($('.bulk_entity_raw'),function(k,v){

		if($('input#title').val() == ""){
			is_verify = 'Please Enter Package Title';
			return is_verify;
		}

		if($('input#quantity').val() == ""){
			is_verify = 'Please Enter Package Quantity';
			return is_verify;
		}


		//validate total inventory of item
		if($(this).find('#total_inventory').text() == ""){
			is_verify = 'Please choose Item Title';
			//is_verify = 'Please choose item '+parseInt(k+1);
			return is_verify;
		}

		if($(this).find('input#weight').val() == ""){
			is_verify = 'Please enter weight for Item';
			//is_verify = 'Please enter weight for item '+parseInt(k+1);
			return is_verify;
		}


		if($(this).find('input#weight').val() != "" && $(this).find('#total_inventory').text() != ""){

			//check item duplication
			//console.log(item_list);
			var item_id = $(this).find("#item_id").select2('val');
            console.log(item_id);
			if(k != 0){
				if ($.inArray(item_id, item_list) > -1)
				{
					is_verify = 'Please update items are duplicating';
					return is_verify;
				}
			}
			item_list.push(item_id);



			//check if weight is greater than available item inventory then alert
			if(parseFloat($(this).find('input#weight').val()) > parseFloat($(this).find('#total_inventory').text())){
				//is_verify = 0;
                var d =  $(this).find("#item_id").select2('data');
                var item_name = '';
                if(d[0]['text']){
                    item_name = d[0]['text'];
                }
				is_verify = 'Sorry inventory exceed, Please update '+item_name+" weight";
				return false;
			}
		}

	});
    console.log(item_list);
	//console.log(item_list);
	//console.log(is_verify);
	return is_verify;
}
/**
 * Data Table loader hmtl
 * @returns {string}
 */
function loaderHtml()
{
	return '<div style="width:100%;height:100%" class="icon-loader"><div></div><div></div><div></div></div>';
}

/**
 * Set Full Name
 */
function setFullName()
{
	if($('#first_name').length > 0){

		var name = "";
		if($('#last_name').length > 0){

			if($('#last_name').val() != ""){

				name += $('#last_name').val();
				name += ", ";
			}
		}

		 name +=  $('#first_name').val();

		$('#full_name').val(name);
	}
}

function verifyTimeSlots()
{
    var start_time = [];
    var end_time = [];
   var is_verify = 1;

    $.each($('.bulk_entity_raw'),function(k,v){

        //	console.log($(this).find('input[name="start_time"]').val());
            if ($.inArray($(this).find('input[name="start_time"]').val(), start_time) > -1)
            {
                is_verify = 'Please update start time is duplicating';
                return is_verify;
            }

        	start_time.push($(this).find('input[name="start_time"]').val());

        if ($.inArray($(this).find('input[name="end_time"]').val(), end_time) > -1)
        {
            is_verify = 'Please update end time is duplicating';
            return is_verify;
        }

        end_time.push($(this).find('input[name="end_time"]').val());


    });
    console.log(start_time);
    console.log(end_time);
    return is_verify;
}

// activate dashboard by-default
setAdminNav("dashboard");

