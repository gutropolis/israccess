// First clear the local storage

var IONRangeSlider={init:function(){
		$(".range-slider").ionRangeSlider({
			type:"double",
			grid:!1,
			min:0,
			max:200,
			from:1,
			to:200,
			prefix:""
			})
			}};jQuery(document).ready(function(){IONRangeSlider.init()});
jQuery(document).ready(function() {
  $(".range-slider").ionRangeSlider();
});

var counterGlobal;

function getUserValue(counter) {
	counterGlobal = counter;
	var IONRangeSlider={init:function(){
		$(".m_slider_3_slider").ionRangeSlider({
			type:"double",
			grid:!1,
			min:0,
			max:200,
			from:1,
			to:200,
			prefix:""
			})
			}};jQuery(document).ready(function(){IONRangeSlider.init()});
    var user_value_from = $("#seat_row_from_"+counter).val();
	var user_value_to = $("#seat_row_to_"+counter).val();
	string_value = user_value_to.length;
	
	$("#seat_row_from_"+counter).val(($("#seat_row_from_"+counter).val()).toUpperCase());
	$("#seat_row_to_"+counter).val(($("#seat_row_to_"+counter).val()).toUpperCase());
	// Hence we have now range from and to use a loop
	var i;
	var rows_cols = '';
	var is_number = 'N';	
    var alphabetic_array = ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o',
	                       'p','q','r','s','t','u','v','w','x','y','z'];
	
	if( $.inArray(user_value_to, alphabetic_array) != -1 ){
		start_value  = user_value_from.charCodeAt(0);
		end_value    = user_value_to.charCodeAt(0);
		is_number = 'Y';
		//sleep();
	}else{
	    start_value  = user_value_from;
		end_value    = user_value_to;
		is_number = 'N';
		//alert('Numbers');
	}
    
	
	var j;
	if(user_value_to  !=''){
	for(i = start_value; i <= end_value; i++){
		if(is_number == 'Y'){
			//alert('Chars inside');
			 j = String.fromCharCode(i).toUpperCase();
		}else{
			j = i; 
			//alert('Numbers inside');
		}
		rows_cols += '<div class="row" style="margin-bottom:20px !important">';
		rows_cols += '<div class="col-md-2">&nbsp;</div>';
		rows_cols += '<div class="col-md-2" style="margin-top:14px;">Row &nbsp&nbsp '+ j +'</div>';
		rows_cols += '<div class="col-md-7">';
		rows_cols += '<div class="m-ion-range-slider"><input type="hidden" name="slider_range_'+counterGlobal+'[]" class="m_slider_3_slider slider_range" /></div>';
		rows_cols += '</div>'; // Div wit class col-md-7 End
		rows_cols += '</div>';
	}
	rows_cols += '<div class="row" >';
	rows_cols += '<div class="col-md-2">&nbsp</div>';
	rows_cols += '<div class="col-md-2" style="margin-top:14px;">Category Price</div>';
	rows_cols += '<div class="col-md-3"><input type="text" class="form-control" name="category_price[]"   placeholder="Category Price" /></div>';
	rows_cols += '</div>';
    $(".row_new_"+counter).append(rows_cols).slideDown("slow");
	is_number = 'N';
	}
	
}

$(document).ready(function () {
	
	
	// For Event Groups Comments
    var counter_comnt = 0;
    $("#addrowComments").on("click", function () {
		
        var cols = "<div class='main'>";
		cols +='<div class="row" style="padding-top:10px">';
		cols +='<div class="col-md-2">Category Name</div>';
		cols +='<div class="col-md-3"><input type="text" class="form-control" name="seat_category[]" id="seat_category_'+ counter_comnt +'"  placeholder="Enter Category" /></div>';
		cols +='<div class="col-md-2"><input type="text" style="width: 128px;" maxlength="1" class="form-control" name="seat_row_from[]" id="seat_row_from_'+ counter_comnt +'"  placeholder="Row From" /></div>';
		cols +='<div class="col-md-3"><input type="text" style="width: 128px;" maxlength="2" class="form-control range_to" name="seat_row_to[]"  id="seat_row_to_'+ counter_comnt +'"   placeholder="Row To" onblur="getUserValue('+counter_comnt+')"/></div>';
		cols += '<div class="col-md-2"><div  class="btn-sm btn btn-danger m-btn m-btn--icon m-btn--pill ibtnDelCmntLink"> <span> <i class="la la-trash-o"></i> <span> Delete </span> </span> </div></div>';
		cols +='</div>';
		cols +='<div class="row_new_'+ counter_comnt +'"></div></div>';
        $("#auditorium_seat_div").append(cols).slideDown("slow");
        counter_comnt++;
    });

    $("#auditorium_seat_div").on("click", ".ibtnDelCmntLink", function (event) {       
		  $(this).closest('div.main').remove();         
        counter_comnt -= 1
    });
	
	
	
	
});




function allStorage() {

    var values = [],
        keys = Object.keys(localStorage),
        i = keys.length;

    while ( i-- ) {
        values.push( localStorage.getItem(keys[i]) );
    }

    return values;
}

// Add
function addAuditorium(){
	var local_storage_key = allStorage(); 
	$('#auditorium_key').val(Object.keys(localStorage));
	$('#auditorium_seats_map').val(local_storage_key);
	var div_id_or_class  = '#mesg_div';
	if($("#auditorium_name").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html(auditorium_name_msg_txt);
		hideErrorDiv(div_id_or_class);
		$("#auditorium_name").focus();
		return false;
	}
	if($("#auditorium_picture").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html(auditorium_pic_msg_txt);
		hideErrorDiv(div_id_or_class);
		$("#auditorium_picture").focus();
		return false;
	}
	
	
	
	if($("#auditorium_access").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html(auditorium_access_msg_txt);
		hideErrorDiv(div_id_or_class);
		$("#auditorium_access").focus();
		return false;
	}
	
	if($("#waze_name").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html(auditorium_waze_msg_txt);
		hideErrorDiv(div_id_or_class);
		$("#waze_name").focus();
		return false;
	}
	
	if($("#m_summernote_1").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html(auditorium_detail_msg_txt);
		hideErrorDiv(div_id_or_class);
		$("#m_summernote_1").focus();
		return false;
	}
	
	if($("#pac-input").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html(auditorium_address_msg_txt);
		hideErrorDiv(div_id_or_class);
		$("#pac-input").focus();
		return false;
	}
	
	var url;
    url = "../saveAuditorium";
   var formData = new FormData($('#form_add_aud')[0]);
   $.ajax({
	 url: url, 
	 type: "POST",
	data: formData,
	contentType: false,
	processData: false,
	dataType: "JSON",
	success: function(data) {
		
		if(data.status == 'duplicate'){
			$(div_id_or_class).show().addClass('alert alert-danger').html(data.message);
			hideErrorDiv(div_id_or_class);
		}else if(data.status == 'file_error'){
			$(div_id_or_class).show().addClass('alert alert-danger').html(data.message);
			hideErrorDiv(div_id_or_class);
		}else if(data.status) {
				//$('#modal-add-auditorium').modal('hide');
				//$('#form_add_aud')[0].reset();
			    //location.reload();	
			 window.location.href='../auditoriums';	
		}
	}, 
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			console.log(XMLHttpRequest.toSource());
		}
	});
	return false;

}

// Delete
function delete_data()
{
	var formData = new FormData($('#form_del_data')[0]);
	// ajax delete data from database
	$.ajax({
		url : "auditoriums/delete/"+$('#id').val(),
		type: "GET",
		dataType: "JSON",
		data: formData,
		processData: false,
		contentType: false,
		success: function(data)
		{
			$('#modal-delete').modal('hide');
			location.reload();
			//reload_table_c();
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			alert('Error deleting data');
		}
	});
}

// Edit function 
function edit(id)
{   
    $('#form_add_aud_e')[0].reset(); // reset form on modals
    //Ajax Load data from ajax
    $.ajax({
        url : "auditoriums/edit/" + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
			
            $('#id_e').val(data['id']);
			$('#auditorium_name_e').val(data['name']);
			$('#auditorium_picture_old').val(data['background_file']);
			$('#width_e').val(data['width']);
			$('#height_e').val(data['height']);
			var back_file = data['background_file'];
			if(back_file != '')
            {
                $('#label-photo-edit').text(auditorium_change_pic_txt); // label photo upload
                $('#photo-preview-edit div').html('<img src="'+data['file_web_path']+'/thumbs/'+data['background_file']+'" class="img-rounded img-thumbnail" height=100 width=100>'); // show photo
                
            }
            else
            {
                //$('#label-photo').text('Upload Photo'); // label photo upload
                $('#photo-preview div').text(common_no_photo_txt);
            }
			
			$('#modal-edit-auditorium').modal('show'); // show bootstrap modal when complete loaded
			
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}


// Update
function updateAuditorium(){
	var local_storage_key = allStorage(); 
	$('#auditorium_key').val(Object.keys(localStorage));
	$('#auditorium_seats_map').val(local_storage_key);
	var div_id_or_class  = '#mesg_div';
	if($("#auditorium_name").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html(auditorium_name_msg_txt);
		hideErrorDiv(div_id_or_class);
		$("#auditorium_name").focus();
		return false;
	}
	
	
	
	if($("#auditorium_access").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html(auditorium_access_msg_txt);
		hideErrorDiv(div_id_or_class);
		$("#auditorium_access").focus();
		return false;
	}
	
	if($("#waze_name").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html(auditorium_waze_msg_txt);
		hideErrorDiv(div_id_or_class);
		$("#waze_name").focus();
		return false;
	}
	
	if($("#m_summernote_1").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html(auditorium_detail_msg_txt);
		hideErrorDiv(div_id_or_class);
		$("#m_summernote_1").focus();
		return false;
	}
	
	if($("#pac-input").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html(auditorium_address_msg_txt);
		hideErrorDiv(div_id_or_class);
		$("#pac-input").focus();
		return false;
	}
	
	
	
	var url;
     url = "../../auditoriums/update";
   var formData = new FormData($('#form_add_aud_e')[0]);
   $.ajax({
	 url: url, 
	 type: "POST",
	data: formData,
	contentType: false,
	processData: false,
	dataType: "JSON",
	success: function(data) {
		
		if(data.status == 'duplicate'){
			$(div_id_or_class).show().addClass('alert alert-danger').html(data.message);
			hideErrorDiv(div_id_or_class);
		}else if(data.status == 'file_error'){
			$(div_id_or_class).show().addClass('alert alert-danger').html(data.message);
			hideErrorDiv(div_id_or_class);
		}else if(data.status) {
				//$('#modal-edit-auditorium').modal('hide');
			    //location.reload();	
				window.location.href='../../auditoriums';
		}
	}, 
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			console.log(XMLHttpRequest.toSource());
		}
	});
	return false;

}

// Remove removeAudSeatTicket
function removeAudSeatTicket(id){
	var info = 'id=' + id;
	if(confirm("Are you sure, you remove this Category?"))
	{
	$.ajax({
	type: "GET",
	url: "../../auditoriums/deleteAudSeats/"+id,
	data: info,
	success: function(data){
		$('#aud_seats_div_data_'+id).hide('slow').slideUp('slow');
		$('#aud_seats_div_data_row_'+id).hide('slow').slideUp('slow'); 
		
	}
	});
	
	}
	
}






