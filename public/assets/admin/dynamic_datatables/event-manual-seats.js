
function makeid() {
  var text = "";
  //var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
  var possible = "0123456789";
  for (var i = 0; i < 2; i++)
    text += possible.charAt(Math.floor(Math.random() * possible.length));
  return text;
}

function makeid2() {
  var text = "";
  //var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
  var possible = "0123456789084";
  for (var i = 0; i < 2; i++)
    text += possible.charAt(Math.floor(Math.random() * possible.length));
  return text;
}

var counterGlobal;

function getUserValue(counter) {
	
	var previous = null;
	var previous_counter = null;
	counterGlobal = counter;
	
    var user_value_from = $("#seat_row_from_"+counter).val();
	var user_value_to = $("#seat_row_to_"+counter).val();
	
	 $("#seat_row_to_"+counter).focus(function () {
                 previous = $(this).val();

     });
	 
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
		
	}else{
	    start_value  = user_value_from;
		end_value    = user_value_to;
		is_number = 'N';
		
	}
    var k = Date.now();
	var kk = Date.now();
	$("#seat_row_to_"+counter).blur(function () {
         if (previous != $(this).val()){
			$("div.row_new_"+counter).html('');	
			var j;
			
			if(user_value_to  !=''){
				var m=0;
			for(i = start_value; i <= parseInt(end_value); i++){
				
				if(is_number == 'Y'){
					 j = String.fromCharCode(i).toUpperCase();
				}else{
					j = i; 
				}
				rows_cols += '<div class="row" style="margin-bottom:20px !important">';
				rows_cols += '<div class="col-md-2">&nbsp;</div>';
				rows_cols += '<div class="col-md-2" style="margin-top:14px;">Row &nbsp&nbsp '+ j +'</div>';
				rows_cols += '<div class="col-md-7" style="margin-top:5px">';
				
				
				
				// For Each Row define range 
				rows_cols += '<table class="table table-bordered table-hover" id="tableAddRow_'+k+'">';
				rows_cols += '<thead>';
				rows_cols += '<tr >';
				rows_cols += '<th class="text-left">From </th>';
				rows_cols += '<th class="text-left">To</th>';
				rows_cols += '<th class="text-left">Even Order?</th>';
				rows_cols += '<th style="width:10px;">';
                rows_cols += '<span class="la la-plus default_c" id="addBtn_'+k+'" onclick="add_row('+counter+','+m+','+k+','+kk+',\''+j+'\')"></span>';
				rows_cols += '</th>';
				rows_cols += '</tr>';
				rows_cols += '</thead>';
				rows_cols += '<tbody>';
				rows_cols += '<tr id="tr_0">';
				rows_cols += '<td><input type="text" name="from_value['+counter+']['+m+'][]"  placeholder="From" class="form-control"/></td>';
				rows_cols += '<td><input type="text" name="to_value['+counter+']['+m+'][]"  placeholder="To" class="form-control"/></td>';
				rows_cols += '<td>';
		        rows_cols += '<input type="checkbox" name="seat_order['+counter+']['+m+'][]"   class="form-control" value="2"/>';
				rows_cols += '<input type="hidden" name="row_number['+counter+']['+m+'][]" value="'+ j +'"  />';
		        rows_cols += '</td>';
				rows_cols += '<td ><span class="la la-minus default_c trRemove" id="addBtnRemove_'+k+'"></span></td>';
				rows_cols += '</tr>';
				rows_cols += '</tbody>';
				rows_cols += '</table>';
				
				
				rows_cols += '</div>'; // Div with class col-md-7 End
				
				rows_cols += '</div>';
				k=k+makeid2();
				kk=kk+makeid();
			m++;	
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
	 });			 
	
}



 var i = 1;
    function add_row(counter,l,k,kk,j)
    {
		
		var cols = "";
		cols += '<tr id="tabl_row_'+i+'_'+kk+'">';
		cols += '<td>';
		cols += '<input type="text" name="from_value['+counter+']['+l+'][]"  placeholder="From" class="form-control"/>';
		cols += '</td>';
		cols += '<td>';
		cols += '<input type="text" name="to_value['+counter+']['+l+'][]"  placeholder="To" class="form-control"/>';
		cols += '<input type="hidden" name="row_number['+counter+']['+l+'][]" value="'+ j +'"  />';
		cols += '</td>';
		cols += '<td>';
		cols += '<input type="checkbox" name="seat_order['+counter+']['+l+'][]"   class="form-control" value="2"/>';
		cols += '</td>';
		cols += '<td>';
		cols += '<span class="la la-minus default_c trRemove" id="addBtn_' + i + '" ></span>';
		cols += '</td>';
		cols += '<tr>';
		//var tempTr = $(cols);
        var tempTr = $(cols).on('click', function () {
           //$(this).closest('tr').remove(); 
           $(document.body).on('click', '.trRemove', function (e) {
                $(this).closest('tr').remove();  
            });
        });
        $("#tableAddRow_"+k).append(tempTr)
        i++;
    }
	
	
	 var i = 1;
    function add_update_row(counter,l,k,kk,j,table_id)
    {
		var current_id = $('.row_number_old_id_new_class_'+ counter +'_'+ l).val();
		var cols = "";
		cols += '<tr id="tabl_row_'+i+'_'+kk+'">';
		cols += '<td>';
		cols += '<input type="text" name="from_value_old_new['+counter+']['+l+'][]"  placeholder="From" class="form-control"/>';
		cols += '</td>';
		cols += '<td>';
		cols += '<input type="text" name="to_value_old_new['+counter+']['+l+'][]"  placeholder="To" class="form-control"/>';
		cols += '<input type="hidden" name="row_number_old_new['+counter+']['+l+'][]" value="'+ j +'"  />';
		cols += '</td>';
		cols += '<td>';
		cols += '<input type="checkbox" name="seat_order_old_new['+counter+']['+l+'][]"   class="form-control"/>';
		cols += '<input type="hidden" name="row_seat_id_old_new['+counter+']['+l+'][]" value="'+ current_id +'"  />';
		cols += '</td>';
		cols += '<td>';
		cols += '<span class="la la-minus default_c trRemove" id="addBtn_' + i + '" ></span>';
		cols += '</td>';
		cols += '<tr>';
		//var tempTr = $(cols);
        var tempTr = $(cols).on('click', function () {
           //$(this).closest('tr').remove(); 
           $(document.body).on('click', '.trRemove', function (e) {
                $(this).closest('tr').remove();  
            });
        });
        $("#tableAddRow_"+table_id).append(tempTr)
        i++;
    }
	
	 var i = 1;
    function del_row(counter,l,k,kk,j,table_id)
    {
		var current_id = $('.row_seat_id_old_'+ counter +'_'+ l).val();
		var info = 'id=' + current_id;
		if(confirm("Are you sure, you want to remove this row?"))
		{
		$.ajax({
		type: "GET",
		url: "../../events/deleteRowSeat/"+current_id+'/'+k,
		data: info,
		success: function(data){
			$('#tableAddRow_'+table_id).hide('slow').slideUp('slow');
			$('div#div_row_rm_'+table_id).hide('slow').slideUp('slow');
			
		}
		});
		
		}
    }
	
	function del_row11(i,k){
		alert('Here = '+ k);
         $("#tabl_row_"+k+"").remove();  
          
	}
	
$(document).ready(function () {
	/*$('.addBtn').on('click', function () {
        //var trID;
        //trID = $(this).closest('tr'); // table row ID 
        addTableRow();
    });
	 $('.addBtnRemove').click(function () {
        $(this).closest('tr').remove();  
    })*/
   
	
	// For Event Groups Comments
    var counter_comnt = 0;
    $("#addrowSeats").on("click", function () {
		
        var cols = "<div class='main'>";
		cols +='<div class="row" style="padding-top:10px">';
		cols +='<div class="col-md-2">Category Name</div>';
		cols +='<div class="col-md-3"><input type="text" class="form-control" name="seat_category[]" id="seat_category_'+ counter_comnt +'"  placeholder="Enter Category" /></div>';
		cols +='<div class="col-md-2"><input type="text" style="width: 128px;" maxlength="3" class="form-control" name="seat_row_from[]" id="seat_row_from_'+ counter_comnt +'"  placeholder="Row From" /></div>';
		cols +='<div class="col-md-3"><input type="text" style="width: 128px;" maxlength="3" class="form-control range_to" name="seat_row_to[]"  id="seat_row_to_'+ counter_comnt +'"   placeholder="Row To" onchange="getUserValue('+counter_comnt+')"/></div>';
		cols += '<div class="col-md-2"><div  class="btn-sm btn btn-danger m-btn m-btn--icon m-btn--pill ibtnDelCmntLink"> <span> <i class="la la-trash-o"></i> <span> Delete </span> </span> </div></div>';
		cols +='</div>';
		cols +='<div class="row_new_'+ counter_comnt +'"></div>';
		
			cols += '</div>';	
        $("#auditorium_seat_div").append(cols).slideDown("slow");
        counter_comnt++;
    });

    $("#auditorium_seat_div").on("click", ".ibtnDelCmntLink", function (event) {       
		  $(this).closest('div.main').remove();         
        counter_comnt -= 1
    });
	
	
	
	
});




function saveEventMap(){
	var div_id_or_class  = '#mesg_div';
	
	
	 var event_id = $('#id').val();
	var url;
     url = "../../events/saveEventSeatTicketMap";
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
			    location.reload();	
				window.location.href='../../events/eventMap/'+event_id;
		}
	}, 
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			console.log(XMLHttpRequest.toSource());
		}
	});
	return false;
}

function updateEventMap(){
	 var div_id_or_class  = '#mesg_div';
	var event_group_id = $('#event_group_id').val();
	
	var url;
     url = "../../events/updateEventSeatTicketMap";
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
				window.location.href='../../events/groups/edit/'+event_group_id;
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
	url: "../../events/deleteEventSeat/"+id,
	data: info,
	success: function(data){
		$('#aud_seats_div_data_'+id).hide('slow').slideUp('slow');
		$('#aud_seats_div_data_row_'+id).hide('slow').slideUp('slow'); 
		
	}
	});
	
	}
	
}

// Remove seat row
function removeAudSeatRow(id){	
  var info = 'id=' + id;
	if(confirm("Are you sure, you remove this seat row?"))
	{
	$.ajax({
	type: "GET",
	url: "../../events/deleteEventSeatRow/"+id,
	data: info,
	success: function(data){
		$('#tr_num_row_'+id).hide('slow').slideUp('slow');
		
	}
	});
	
	}
}






