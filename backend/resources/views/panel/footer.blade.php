<?php
if(\Session::has(ADMIN_SESS_KEY.'_POST_DATA')){
	\Session::forget(ADMIN_SESS_KEY.'_POST_DATA');
}
?>
<!-- BEGIN: PAGE SCRIPTS -->
    <!-- jQuery -->

<script src="{{\URL::to('/')}}/resources/assets/panel/vendor/plugins/holder/holder.min.js"></script><!-- Holder Image -->
<script type="text/javascript" charset="utf-8">
    jQuery(document).ready(function() {

    if($('#notify_icon').length > 0){


        var resultsSelected = false;
        $("#notify_icon").hover( function () {
            resultsSelected = true;
        } , function () {
            resultsSelected = false;
        });

        $('#notify_icon').focusout(function() { if (!resultsSelected) {
			//if you click on anything other than the results
            $(this).removeClass('open');
        } });

        var searchSelected = false;
        $("#sidebar_right").hover( function () {
            searchSelected = true;
        } , function () {
            searchSelected = false;
        });

       // console.log('selected'+searchSelected)


       $(document).click(function(e){
            if(!searchSelected){
             //   console.log(e.target.id);  console.log($(e.target).parents('aside#sidebar_right'));
                if(e.target.id != 'sidebar_right' && $(e.target).parents('aside#sidebar_right').length == 0 ) {
                    $('#sidebar_right').removeClass('sidebar_right-open');
                }
			}

        });


        countNotification();

        //Count Notification
       setInterval(function(){
            countNotification();
        },7000);


        $('#notify_icon').on('click',function(){
            $(this).toggleClass('open');

            if($(this).hasClass('open')){
                //list Notification

                $.ajax({
                    url: "<?php echo url('listNotification'); ?>",
                    dataType: "json",
                    //data: {"attachment_id": file_id},
                    beforeSend: function () {
                    }
                }).done(function (data) {
                    if(data.data.html){
                        $('.notification-list').html('');
                       $('.notification-list').html(data.data.html);
                    }
                });

			}

        });

    }


        $(document).on('click',".order-update-btn ",function(){
            var order_id = $('#orderContent #order_id').val();
            var order_status = $('#orderContent #order_status').val();
            var vehicle_id = $('#orderContent #vehicle_id').val();
            var driver_id = $('#orderContent #driver_id').val();
            var comment = $('#orderContent #comment').val();
            $(".order-update-btn").attr("disabled","disabled");

            $.ajax({
                type: "POST",
                url: "<?php echo url('updateOrderStatus'); ?>",
                dataType: "json",
                data: {"order_id": order_id,
					"order_status": order_status,
                    "vehicle_id" : vehicle_id,
                    "driver_id" : driver_id,
					"comment" : comment,
                    "_token": "{{ csrf_token() }}"},
                success: function (data) {

                   // console.log(data.error);
                    $(".order-update-btn").removeAttr("disabled");

                    if(data.error == 1){
                        $('#orderModal .alert-message').html(alertMsg(data.message));
                    }
                    else{
                        $('#orderModal .alert-message').html(alertSuccessMsg(data.message));
                        $("#orderModal").modal('hide');
                        window.location.reload(true);
                    }
                }
            });

        });

        $(document).on('change',"#orderContent #vehicle_id",function(){
        //$('#driver_id').on('change',function(){
			driverVehicleInfo($(this).val());
        });



    });





    $(".cb_bu_info").chosen({
        search_contains: true,
        width: "100%",
        no_results_text: "not found",
        display_disabled_options: false
    });

    $('.chosen-search input').autocomplete({
        minLength: 1,
        source: function (request, response) {
            var entity_type_id = $($(this)[0].element).closest(".getchoosen").find("select").data("type_id");
            var attribute_code = $($(this)[0].element).closest(".getchoosen").find("select").data("attribute_code");
            var chosen_id = $($(this)[0].element).closest(".getchoosen").find("select").attr("id");

            if($('#entity_type_identifier').length > 0){
                if($('#entity_type_identifier').val() == "recipe"){
                    updateSearchItem(entity_type_id, chosen_id, request.term, attribute_code);
                }
            }
            else{
                updateSearch(entity_type_id, chosen_id, request.term, attribute_code);
            }

        }
    });

    function updateSearch(entity_type_id, chosen_id, term,attribute_code) {
        $.ajax({
            url: "<?php echo url('getoptions'); ?>",
            dataType: "json",
            data: {"term": term, "entity_type_id": entity_type_id, "attribute_code": attribute_code},
            beforeSend: function () {
                $('#' + chosen_id).parent().find('ul.chosen-results').empty();
                $('#' + chosen_id).empty();
            }
        }).done(function (data) {
            $('#' + chosen_id).parent().find('ul.chosen-results').empty();
            $('#' + chosen_id).empty();
            $(data).each(function (index, ele) {
                $('#' + chosen_id).append('<option value="' + ele.entity_id + '">' + ele.title + '</option>');
            });
            $("#" + chosen_id).trigger("chosen:updated");
			$('#' + chosen_id).parent().find('.chosen-search input').val(term);
        });
    }

    function updateSearchItem(entity_type_id, chosen_id, term , attribute_code) {
       // console.log(chosen_id); console.log('sam');

        $("#" + chosen_id).parents('')
        $.ajax({
            url: "<?php echo url('getItemData'); ?>",
            dataType: "json",
            data: {"term": term, "entity_type_id": entity_type_id , "attribute_code": attribute_code},
            beforeSend: function () {
                $('#' + chosen_id).parent().find('ul.chosen-results').empty();
                $('#' + chosen_id).empty();
            }
        }).done(function (data) {
            //console.log(data);
            $(data).each(function (index, ele) {
                $('#' + chosen_id).append('<option value="' + ele.entity_id + '">' + ele.title + '</option>');

                var selected_element = $("#" + chosen_id).parents('div.bulk_entity_raw').children('#itemWrap');
                $(selected_element).find('#total_inventory').text(ele.total_inventory);
                $(selected_element).find('#item_unit').text(ele.item_unit);
            });
            $("#" + chosen_id).trigger("chosen:updated");
            $('#' + chosen_id).parent().find('.chosen-search input').val(term);

        });
    }

    function loadFileUpload(divID,k,gallery)
    {
        var baseUrl = "";
        var token = "{{ Session::getToken() }}";
        Dropzone.autoDiscover = false;
        var myDropzone = new Dropzone("div#dropzoneFileUpload_"+k, {

            url: baseUrl + "{!! URL::to('/api/system/attachment/save') !!}",
            addRemoveLinks: true,
            maxFiles:1,
            thumbnailWidth: parseInt(minThumbWidth),
            thumbnailHeight: parseInt(minThumbHeight),
            dictRemoveFileConfirmation:  "Are you sure you want to remove?",
            params: {
                _token: token,
                attachment_type_id: 8,
                entity_type_id:0
            }
        });
        var numItems = 0;
        var attch_id = '';
        myDropzone.on("success", function(file,responseText) {

            $('.alert-message').html('');
            var image_response = $.parseJSON(responseText.jsonEditor);
            if(image_response.error == 1){
                // $('.dz-preview').remove();
                showAlert(image_response.message);
                this.removeFile(file);
            }
            else{
                attch_id = image_response.data.attachment.attachment_id;
            }

            //console.log($.parseJSON(responseText.jsonEditor).data.attachment.attachment_id);
            //$('.dz-complete').eq(numItems).append('<input type="checkbox" onClick="isfeatured()">');
            //numItems++;
            myDropzone.processQueue();
        });

        myDropzone.on("complete", function() {

            // console.log(divID);
            $('#'+divID).find('.dz-complete').append('<div class="pull-right"><input type="hidden" name="gallery_items" value="'+attch_id+'"></div>');
            $('#'+divID).find('.dz-remove').attr('data-attachment-id',attch_id);
            /*setTimeout(function(){
             $('.dz-complete').eq(numItems).append('<input type="radio" name="gallery_featured_item" value="'+attch_id+'">');

             numItems++;

             }, 3000);*/


        });


        myDropzone.on("removedfile", function(file) {
            var file_id = '';
            if($('#'+divID).find('a.dz-remove').length > 0){

                var attach_attr = file._removeLink.attributes['data-attachment-id'];
                var file_id = attach_attr.nodeValue;
            }
            if(file_id != ""){
                $.ajax({
                    url: "<?php echo url('/api/system/attachment/delete'); ?>",
                    dataType: "json",
                    data: {"attachment_id": file_id},
                    beforeSend: function () {
                    }
                }).done(function (data) {

                });
            }

        });

        if(gallery){
          //  console.log(gallery);
            if(gallery.id){
                var mockFile = { name: gallery.name, size: gallery.size };
                myDropzone.emit("addedfile", mockFile);
                // And optionally show the thumbnail of the file:
                myDropzone.emit("thumbnail", mockFile, gallery.file);
                $('#'+divID).find('.dz-remove').attr('data-attachment-id',gallery.id);
            }


        }

    }

    function countNotification()
	{ //console.log($('#notify_count').text());
        $.ajax({
            url: "<?php echo url('countNotification'); ?>",
            dataType: "json",
            //data: {"attachment_id": file_id},
            beforeSend: function () {
            }
        }).done(function (data) {
            if(data.data.total_count){
                if(data.data.total_count > 0){

                    if($.trim($('#notify_count').text()) != ""){

						if(parseInt($.trim(data.data.total_count)) > parseInt($.trim($('#notify_count').text()))){
						    //bell sound
                            var audio = new Audio("<?php echo  url(config('panel.DIR_PANEL_RESOURCE').'assets/audio/notify.mp3'); ?>");
                            audio.play();
						}
					}
                    $('#notify_count').text(data.data.total_count);
                }

            }
        });
	}

	function driverVehicleInfo(vehicle_id)
	{
        $('#orderModal #vehicleInfo').html('');
        $('#orderModal #driver_id').val('');
//console.log(vehicle_id);
        if(vehicle_id != ''){

            $.ajax({
                type: "GET",
                url: "<?php echo url('getDriverVehicle'); ?>",
                dataType: "json",
                data: {"vehicle_id": vehicle_id },
                success: function (data) {

                    if(data.error == 1){
                        $('#orderModal .alert-message').html(alertMsg(data.message));
                    }
                    else{

                        $('#orderModal #vehicleInfo').html(data.data.html);
                        $('#orderModal #driver_id').val(data.data.driver_id);

                    }
                }
            });
		}

	}

</script>

</body>

</html>