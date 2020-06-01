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
	// Apply the filters (search/resets)
	dg.column().every( function () {
		var column = this;
		// every field change
		$( 'input, select', this.footer() ).on( 'change', function () {
			//column.search( this.value );
			dg_ajax_params[this.name] = this.value;
		});
		console.log('column',dg_ajax_params);
		// action button changes
		// - reset
		$( 'a#grid_reset', this.footer() ).on( 'click', function () {			
			$( 'input, select', $(this).parents("tr:first")).each(function(index) {
				$(this).val("");
				console.log('here',this.value);
				$(this).trigger("change");
			});
			dg.ajax.reload();
		});
		// -search
		$( 'a#grid_search', this.footer() ).on( 'click', function () {
			dg.ajax.reload();
		});
	});
}
// data grid select actions
function dgSelectActions(dg) {
	// on multi-select
	// select chechbox
	$("input[name=check_all]").on( "change", function() {
		$("input[name='check_ids[]']").prop("checked",$(this).prop("checked"));
	});
	// on select action
	$('select[name=select_action]').on('change', function () {
		var select_elem = $(this);
		var dg_action = $(select_elem).val();
		var elem_name = $(select_elem).prop("name");
		var elem_value = $(select_elem).val();
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
			$( 'select[name=select_action] option[value="delete"]' ).prop("selected",true).trigger("change");
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
    App.initHelpers('slick');
	// Init page helpers (Table Tools helper)
	App.initHelpers('table-tools');
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
	
	
		
});





// activate dashboard by-default
setAdminNav("dashboard");

