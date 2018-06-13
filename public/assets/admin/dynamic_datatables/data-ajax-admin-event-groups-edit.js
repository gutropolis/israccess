//jQuery(document).ready(function() {
	var event_group_id = $('event_id').val();
//});
var DatatableRemoteAjaxDemoEvents = {
	
    init: function() {
       // var t;
        t = $(".m_datatable_group_events").mDatatable({
            data: {
                type: "remote",
                source: {
                    read: {
                        url: "../../../events/getAjaxEventsList",
						//headers: {'x-my-custom-header': 'some_value', 'x-test-header': 'the_value'},
						params: {
							// custom query params
							post_data: {
							   event_group_id: event_group_id
							}
						 },
                        map: function(t) {
                            var e = t;
                            return void 0 !== t.data && (e = t.data), e
                        }
                    }
                },
				saveState: {cookie: false, webstorage: false},
                pageSize: 10,
                serverPaging: true,
				serverFiltering: true,
				serverSorting: true
            },
            layout: {
                scroll: !1,
                footer: !1
            },
            sortable: !1,
            pagination: !0,
            toolbar: {
                items: {
                    pagination: {
                        pageSizeSelect: [10, 20, 30, 50, 100]
                    }
                }
            },
            search: {
                input: $("#generalSearch")
            },
            columns: [{
                field: "id",
                title: "#",
                sortable: !1,
                width: 30,
                selector: !1,
                textAlign: "center",
				 selector:{
					id : "check_box_class",
					class:"m-checkbox--solid m-checkbox--brand"}
            }, {
                field: "title",
                title: "Title",
                filterable: !1,
                width: 200,
                template: "{{title}}"
            },
			{
                field: "date",
                title: "Date",
                filterable: !0,
                width: 100,
                template: "{{date}}"
            },
			{
                field: "city_name",
                title: "City",
                filterable: !1,
                width: 120,
                template: "{{city_name}}"
            },
			{
                field: "auditorium_name",
                title: "Auditorium",
                filterable: !0,
                width: 180,
                template: "{{auditorium_name}}"
            },
			{
                field: "status",
                title: "Status",
				width: 80,
                template: function(t) {
                    var e = {
                        0: {
                            title: "Inactive",
                            class: "m-badge--danger",
							statusTitle : "Active"
                        },
                        1: {
                            title: "Active",
                            class: " m-badge--success",
							statusTitle : "Inactive"
                        }
                    };
                    return '<span class="m-badge ' + e[t.status].class + ' m-badge--wide">' + e[t.status].title + "</span>"
                }
            },
			{
                field: "Actions",
                width: 120,
                title: "Actions",
                sortable: !1,
                overflow: "visible",
                template: function(t, e, a) {
					if(t.seats_on_map == 'N'){
						var event_aud_map_link = '\t\t\t\t\t\t<a  href="javascript:void(0);" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Set up map" onclick="set_map('+[t.id]+')" >\t\t\t\t\t\t\t<i class="la la-map" style="color:green"></i>\t\t\t\t\t\t</a>\t\t\t\t\t';
						
					}else{
						var event_aud_map_link = '\t\t\t\t\t\t<a  href="javascript:void(0);" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Manual Seat Map  is not allowed" onclick="no_set_map('+[t.id]+')" >\t\t\t\t\t\t\t<i class="la la-ban" style="color:red"></i>\t\t\t\t\t\t</a>\t\t\t\t\t';
					}
                    return '\t\t\t\t\t\t<a style="display:none"  href="javascript:void(0);" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="View Event" onclick="view('+[t.id]+')" >\t\t\t\t\t\t\t<i class="la la-eye"></i>\t\t\t\t\t\t</a>\t\t\t\t\t\t<a href="javascript:void(0);"  class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Edit Event" onclick="edit_event('+[t.id]+')">\t\t\t\t\t\t\t<i class="la la-edit"></i>\t\t\t\t\t\t</a>\t\t\t\t\t\t<a href="javascript:void(0);" onclick="delete_function_event('+[t.id]+')" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" title="Delete Event">\t\t\t\t\t\t\t<i class="la la-trash"></i>\t\t\t\t\t\t</a>\t\t\t\t\t'+event_aud_map_link
                }
            }]
        }), $("#refresh_table").on('click', function(){
          t.reload(); /*DatatableRemoteAjaxDemoCat.init();*/ }), 
		  $("#m_form_status").on("change", function() {
            t.search($(this).val(), "Status")
        }), /*$("#m_form_type").on("change", function() {
            t.search($(this).val(), "Type")
        }),*/ $("#m_form_status, #m_form_type").selectpicker()
    }
	
};

function no_set_map(id){
   alert('Auditorium manual seat ticket map is disabled for this event');	
}
function set_map(id){
   window.location.href='../../../events/eventMapEdit/'+id;
}

jQuery(document).ready(function() {
	change_select2_placehoder();
    DatatableRemoteAjaxDemoEvents.init();
	
});

function setTimepicker(){
  $(".time_picker").timepicker({ "autoclose": true});	
}
function change_select2_placehoder(){
  $("#m_select2_3").select2({
    placeholder: "Select city",
    allowClear: true
});	
$("#m_select2_4").select2({
    placeholder: "Select auditorium",
    allowClear: true
});	
$("#m_select2_1_validate").select2({
    placeholder: "Select category",
    allowClear: true
});	
$("#m_select2_2_validate").select2({
    placeholder: "Select artist",
    allowClear: true
});	
}




// Update
function updateEventGroup(){
	//alert('Herer');
	var div_id_or_class  = '#mesg_div_event_group';
	if($("#title").val()=="")
	{
		//$("#title").focus();
		$(div_id_or_class).show().addClass('alert alert-danger').html('Please enter event group title.');
		hideErrorDiv(div_id_or_class);
		return false;
	}
	if($("#thumbnail_title").val()=="")
	{
		//$("#en_savoir_block1_name").focus();
		$(div_id_or_class).show().addClass('alert alert-danger').html('Please enter Card Title.');
		hideErrorDiv(div_id_or_class);
		return false;
	}
	
	if($("#price_min").val()=="")
	{
		//$("#price_min").focus();
		$(div_id_or_class).show().addClass('alert alert-danger').html('Please enter event group minimum price.');
		hideErrorDiv(div_id_or_class);
		return false;
	}
	
	if($(".artist_id").val()=="")
	{
		//$(".artist_id").focus();
		$(div_id_or_class).show().addClass('alert alert-danger').html('Please select event group artist.');
		hideErrorDiv(div_id_or_class);
		return false;
	}
	
	if($(".category_id").val()=="")
	{
		//$(".category_id").focus();
		$(div_id_or_class).show().addClass('alert alert-danger').html('Please select event group category.');
		hideErrorDiv(div_id_or_class);
		return false;
	}
	
	if($(".date_begin").val()=="")
	{
		//$(".date_begin").focus();
		$(div_id_or_class).show().addClass('alert alert-danger').html('Please select begin date.');
		hideErrorDiv(div_id_or_class);
		return false;
	}
	
	if($(".date_end").val()=="")
	{
		$(".date_end").focus();
		$(div_id_or_class).show().addClass('alert alert-danger').html('Please select  end date.');
		hideErrorDiv(div_id_or_class);
		return false;
	}
	
	if($("#m_summernote_1").val()=="")
	{
		$("#m_summernote_1").focus();
		$(div_id_or_class).show().addClass('alert alert-danger').html('Please enter description.');
		hideErrorDiv(div_id_or_class);
		return false;
	}
	
    
	var url;
    url = "./update";
   var formData = new FormData($('#edit_event_group_from')[0]);
   $.ajax({
	 url: url, 
	 type: "POST",
	data: formData,
	contentType: false,
	processData: false,
	dataType: "JSON",
	success: function(data) {
		
		if(data.status == 'error'){
			$(div_id_or_class).show().addClass('alert alert-danger').html(data.message);
			hideErrorDiv(div_id_or_class);
		}else if(data.status == 'duplicate'){
			$(div_id_or_class).show().addClass('alert alert-danger').html(data.message);
			hideErrorDiv(div_id_or_class);
		}else if(data.status == 'file_error'){
			$(div_id_or_class).show().addClass('alert alert-danger').html(data.message);
			hideErrorDiv(div_id_or_class);
		}else if(data.status) {
			    location.reload();	
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
		url : "groups/delete/"+$('#id').val(),
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



function view(id)
{   

     $('#viewEventRolesRow').html('');
    //Ajax Load data from ajax
    $.ajax({
        url : "../../../events/get/" + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
			//var data = JSON.stringify(data);
            $('#event_title_e').text(data['title']);
			$('#section_e').text(data['section']);
			$('#date_e').text(data['date_e']);
			$('#status_e').text(data['status']);
			$('#city_e').text(data['city_name']);
			$('#auditorium_e').text(data['auditorium_name']);
			$('#artist_name_vev').text(data['artist_name']);
			$('#author_name_vev').text(data['author_name']);
			$('#productor_name_vev').text(data['productor_name']);
			$('#director_name_vev').text(data['director_name']);
			$('#evevt_description_e').text(data['description']);
			$('#contributor_name_e').text(data['contributor_name']); // Contributor Name
			$('#contributor_description_e').text(data['contributor_description']); // Contributor description
			$('ul.pic-list').html(data['pics']);
			$('#eventTimingsView').html(data['times']);
			if(data['seats_on_map'] == 'Y'){
			  	$('#map_yes').text('Yes');
				$('#map_no').text('Disabled');
			}else{
				$('#map_yes').text('Disabled');
				$('#map_no').text('Yes');
			}
			$('#eventTicketsView').html(data['tickets']);
			$('#viewEventRolesRow').html(data['roles']);
			$('#modal-view-event').modal('show'); // show bootstrap modal when complete loaded
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

// Event
function addEvent(){
	var div_id_or_class  = '#mesg_div_event';
	if($("#title_event").val()=="")
	{
		//$("#title_event").focus();
		$(div_id_or_class).show().addClass('alert alert-danger').html('Please enter event title.');
		hideErrorDiv(div_id_or_class);
		return false;
	}
	
	if($("#date_event").val()=="")
	{
		//$("#date_event").focus();
		$(div_id_or_class).show().addClass('alert alert-danger').html('Please enter event date.');
		hideErrorDiv(div_id_or_class);
		return false;
	}
	
	if($(".city_id").val()=="")
	{
		//$(".city_id").focus();
		$(div_id_or_class).show().addClass('alert alert-danger').html('Please select event city.');
		hideErrorDiv(div_id_or_class);
		return false;
	}
	
	if($(".auditorium_id").val()=="")
	{
		//$(".auditorium_id").focus();
		$(div_id_or_class).show().addClass('alert alert-danger').html('Please select event auditorium.');
		hideErrorDiv(div_id_or_class);
		return false;
	}
	
	
    
	var url;
    url = "../../../events/saveEvent";
   var formData = new FormData($('#form_add_event_frm')[0]);
   $.ajax({
	 url: url, 
	 type: "POST",
	data: formData,
	contentType: false,
	processData: false,
	dataType: "JSON",
	success: function(data) {
		
		if(data.status == 'error'){
			$(div_id_or_class).show().addClass('alert alert-danger').html(data.message);
			hideErrorDiv(div_id_or_class);
		}else if(data.status == 'duplicate'){
			$(div_id_or_class).show().addClass('alert alert-danger').html(data.message);
			hideErrorDiv(div_id_or_class);
		}else if(data.status == 'file_error'){
			$(div_id_or_class).show().addClass('alert alert-danger').html(data.message);
			hideErrorDiv(div_id_or_class);
		}else if(data.status) {
			    $('#modal-add-event').modal('hide');
				$('#form_add_event_frm')[0].reset();
			    location.reload();	
		}
	}, 
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			console.log(XMLHttpRequest.toSource());
		}
	});
	return false;

}

function edit_old(id){
	alert('In progress');
    //window.location.href='groups/edit/'+id;	
}


$(document).ready(function () {
	$('body').on('focus',".m_date", function(){
		 $(this).datepicker({
			todayHighlight:!0,
			orientation:"bottom left",
			format: 'dd/mm/yyyy',
			autoclose:!0
		 });
	});
	$('body').on('focus',".time_picker", function(){
    $(this).timepicker({'minuteStep':1,'showSeconds':!0,'showMeridian':!1,'snapToStep':!0,'timeFormat': 'H:m', 'autoclose': true, 'minuteStep': 5,'disableFocus': true,'template': 'dropdown','showInputs': true, showSeconds: false});
});
$('body').on('focus',".number_price_only", function(){
$(this).keydown(function (e) {
            if (e.shiftKey || e.ctrlKey || e.altKey) { // if shift, ctrl or alt keys held down
                e.preventDefault();         // Prevent character input
            } else {
                var n = e.keyCode;
                if (!((n == 8)              // backspace
                        || (n == 46)                // delete
                        || (n >= 35 && n <= 40)     // arrow keys/home/end
                        || (n >= 48 && n <= 57)     // numbers on keyboard
                        || (n >= 96 && n <= 105))   // number on keypad
                        ) {
                    e.preventDefault();
                    // alert("in if");
                    // Prevent character input
                }
            }
        });
});

// For Adding Event Time
    var counterTime = 1;
    $("#addrowTime").on("click", function () {
        var newRow = $('<tr>');
		$(".time_picker").timepicker({ 'timeFormat': 'H:m', 'autoclose': true, 'minuteStep': 5,'disableFocus': true,'template': 'dropdown','showInputs': true,'minuteStep':1,'showSeconds':!0,'showMeridian':!1,'snapToStep':!0, showSeconds: false});
        var cols = "";
        cols += '<td class="col-sm-4" style="width: 15% !important;text-align:  left;padding-top: 20px;">Select Time</td>';
        cols += '<td class="col-sm-3">';
		cols += '<input type="text" class="form-control time_picker" name="event_time[]" id="event_time_'+ counterTime +'" readonly placeholder="Select time" />';
		cols += '</td>';
        cols += '<td><div  class="btn-sm btn btn-danger m-btn m-btn--icon m-btn--pill ibtnDelTime"> <span> <i class="la la-trash-o"></i> <span> Delete </span> </span> </div></td></tr>';
		
        $("table.order-list-event-time").append(newRow.append(cols)).slideDown("slow");
        counterTime++;
    });

    $("table.order-list-event-time").on("click", ".ibtnDelTime", function (event) {
        $(this).closest("tr").remove();       
        counterTime -= 1
    });
	
	// For Updating Event Time
    var counterTimeUpdate = 1;
    $("#addrowTimeEdit").on("click", function () {
        var newRow = $('<tr>');
		$(".time_picker").timepicker({ 'timeFormat': 'H:m', 'autoclose': true, 'minuteStep': 5,'disableFocus': true,'template': 'dropdown','showInputs': true,'minuteStep':1,'showSeconds':!0,'showMeridian':!1,'snapToStep':!0,showSeconds: false});
        var cols = "";
        cols += '<td class="col-sm-4" style="width: 15% !important;text-align:  left;padding-top: 20px;">Select Time</td>';
        cols += '<td class="col-sm-3">';
		cols += '<input type="text" class="form-control time_picker" name="event_time[]" id="event_time_'+ counterTimeUpdate +'" readonly placeholder="Select time" />';
		cols += '</td>';
        cols += '<td><div  class="btn-sm btn btn-danger m-btn m-btn--icon m-btn--pill ibtnDelTimeEdit"> <span> <i class="la la-trash-o"></i> <span> Delete </span> </span> </div></td></tr>';
		
        $("table.order-list-event-time-Edit").append(newRow.append(cols)).slideDown("slow");
        counterTimeUpdate++;
    });

    $("table.order-list-event-time-Edit").on("click", ".ibtnDelTimeEdit", function (event) {
        $(this).closest("tr").remove();       
        counterTimeUpdate -= 1
    });
	
	
	
	
// For Adding Event Roles
    var counterEVGRole = 1;
    $("#addrowEVGRole").on("click", function () {
        var newRow = $('<tr>');
        var cols = "";
        cols += '<td  style="width:40%">';
		cols += '<input type="text" class="form-control" required name="event_role_label[]" id="event_role_label_'+ counterEVGRole +'"  placeholder="Role label" />';
		cols += '</td>';
		 cols += '<td  style="width: 60% !important;">';
		cols += '<input type="text" class="form-control " required name="event_role_name[]" id="event_role_name_'+ counterEVGRole +'"  placeholder="Role name" />';
		cols += '</td>';
        cols += '<td><div  class="btn-sm btn btn-danger m-btn m-btn--icon m-btn--pill ibtnDelEVGRole"> <span> <i class="la la-trash-o"></i> <span> Delete </span> </span> </div></td></tr>';
		
        $("table.order-list-event-group-roles").append(newRow.append(cols)).slideDown("slow");
        counterEVGRole++;
    });

    $("table.order-list-event-group-roles").on("click", ".ibtnDelEVGRole", function (event) {
        $(this).closest("tr").remove();       
        counterEVGRole -= 1
    });

// For Adding Event Roles
    var counterRole = 1;
    $("#addrowRole").on("click", function () {
        var newRow = $('<tr>');
        var cols = "";
        cols += '<td  style="width:40%">';
		cols += '<input type="text" class="form-control" required name="event_role_label[]" id="event_role_label_'+ counterRole +'"  placeholder="Role label" />';
		cols += '</td>';
		 cols += '<td  style="width: 60% !important;">';
		cols += '<input type="text" class="form-control " required name="event_role_name[]" id="event_role_name_'+ counterRole +'"  placeholder="Role name" />';
		cols += '</td>';
        cols += '<td><div  class="btn-sm btn btn-danger m-btn m-btn--icon m-btn--pill ibtnDelTicket"> <span> <i class="la la-trash-o"></i> <span> Delete </span> </span> </div></td></tr>';
		
        $("table.order-list-event-roles").append(newRow.append(cols)).slideDown("slow");
        counterRole++;
    });

    $("table.order-list-event-roles").on("click", ".ibtnDelTicket", function (event) {
        $(this).closest("tr").remove();       
        counterRole -= 1
    });
	
	// For Edit Event Roles
    var counterRoleEdit = 1;
    $("#addrowRoleEdit").on("click", function () {
        var newRow = $('<tr>');
        var cols = "";
        cols += '<td  style="width:40%">';
		cols += '<input type="text" class="form-control" required name="event_role_label[]" id="event_role_label_'+ counterRoleEdit +'"  placeholder="Role label" />';
		cols += '</td>';
		 cols += '<td  style="width: 60% !important;">';
		cols += '<input type="text" class="form-control " required name="event_role_name[]" id="event_role_name_'+ counterRoleEdit +'"  placeholder="Role name" />';
		cols += '</td>';
        cols += '<td><div  class="btn-sm btn btn-danger m-btn m-btn--icon m-btn--pill ibtnDelTicketRoleEdit"> <span> <i class="la la-trash-o"></i> <span> Delete </span> </span> </div></td></tr>';
		
        $("table.order-list-event-roles-edit > #EditEventRolesDivRow").append(newRow.append(cols)).slideDown("slow");
        counterRoleEdit++;
    });

    $("table.order-list-event-roles-edit > #EditEventRolesDivRow").on("click", ".ibtnDelTicketRoleEdit", function (event) {
        $(this).closest("tr").remove();       
        counterRoleEdit -= 1
    });

// For Adding Event Ticket
    var counterTicket = 1;
    $("#addrowTicket").on("click", function () {
        var newRow = $('<tr>');
        var cols = "";
        cols += '<td class="col-sm-4" style="width: 20% !important;text-align:  left;padding-top: 20px;">Enter Tickets</td>';
        cols += '<td  style="width:40%">';
		cols += '<input type="text" class="form-control" required name="ticket_type[]" id="ticket_type_'+ counterTicket +'"  placeholder="Enter Type" />';
		cols += '</td>';
		 cols += '<td  style="width: 20% !important;">';
		cols += '<input type="text" class="form-control number_price_only" required name="per_ticket_price[]" id="per_ticket_price_'+ counterTicket +'"  placeholder="Price" />';
		cols += '</td>';
		 cols += '<td  style="width:  20% !important;">';
		cols += '<input type="text" class="form-control number_price_only" required name="total_quantity[]" id="total_quantity_'+ counterTicket +'"  placeholder="Total Tickets" />';
		cols += '</td>';
        cols += '<td><div  class="btn-sm btn btn-danger m-btn m-btn--icon m-btn--pill ibtnDelTicket"> <span> <i class="la la-trash-o"></i> <span> Delete </span> </span> </div></td></tr>';
		
        $("table.order-list-event-tickets").append(newRow.append(cols)).slideDown("slow");
        counterTicket++;
    });

    $("table.order-list-event-tickets").on("click", ".ibtnDelTicket", function (event) {
        $(this).closest("tr").remove();       
        counterTicket -= 1
    });
	
	// For Adding Event Ticket Edit
    var counterTicketE = 1;
    $("#addrowTicketEdit").on("click", function () {
        var newRow = $('<tr>');
        var cols = "";
        cols += '<td class="col-sm-4" style="width: 20% !important;text-align:  left;padding-top: 20px;">Enter Tickets</td>';
        cols += '<td  style="width:40%">';
		cols += '<input type="text" class="form-control" required name="ticket_type[]" id="ticket_type_'+ counterTicketE +'"  placeholder="Enter Type" />';
		cols += '</td>';
		 cols += '<td  style="width: 20% !important;">';
		cols += '<input type="text" class="form-control number_price_only" required name="per_ticket_price[]" id="per_ticket_price_'+ counterTicketE +'"  placeholder="Price" />';
		cols += '</td>';
		 cols += '<td  style="width:  20% !important;">';
		cols += '<input type="text" class="form-control number_price_only" required name="total_quantity[]" id="total_quantity_'+ counterTicketE +'"  placeholder="Total Tickets" />';
		cols += '</td>';
        cols += '<td><div  class="btn-sm btn btn-danger m-btn m-btn--icon m-btn--pill ibtnDelTicket"> <span> <i class="la la-trash-o"></i> <span> Delete </span> </span> </div></td></tr>';
		
        $("table.order-list-event-tickets-edit-new").append(newRow.append(cols)).slideDown("slow");
        counterTicketE++;
    });

    $("table.order-list-event-tickets-edit-new").on("click", ".ibtnDelTicket", function (event) {
        $(this).closest("tr").remove();       
        counterTicketE -= 1
    });
	
      
	
	
	
    // For Add
    var counter = 1;
    $("#addrow").on("click", function () {
        var newRow = $('<tr>');
        var cols = "";
        cols += '<td><input type="file" class="form-control m-input" name="event_img[]" id="event_img_' + counter + '"><span class="help-block duplicate error">Please upload an image of 200*200</span></td>';
        cols += '<td><div  class="btn-sm btn btn-danger m-btn m-btn--icon m-btn--pill ibtnDel"> <span> <i class="la la-trash-o"></i> <span> Delete </span> </span> </div></td></tr>';

        $("table.order-list").append(newRow.append(cols)).slideDown("slow");
        counter++;
    });

    $("table.order-list").on("click", ".ibtnDel", function (event) {
        $(this).closest("tr").remove();       
        counter -= 1
    });
	
	
	// For Edit
    var counter_edit = 1;
    $("#addrowEdit").on("click", function () {
        var newRow = $('<tr>');
        var cols = "";
        cols += '<td><input type="file" class="form-control m-input" name="event_img[]" id="event_img_' + counter_edit + '"><span class="help-block duplicate error">Please upload an image of 200*200</span></td>';
        cols += '<td><div  class="btn-sm btn btn-danger m-btn m-btn--icon m-btn--pill ibtnDel"> <span> <i class="la la-trash-o"></i> <span> Delete </span> </span> </div></td></tr>';
        
        $("table.order-list-edit").append(newRow.append(cols)).slideDown("slow");
        counter_edit++;
    });

    $("table.order-list-edit").on("click", ".ibtnDel", function (event) {
        $(this).closest("tr").remove();       
        counter_edit -= 1
    });
	
	
	
	// For Event Groups Video Embed Link
    var counter_editVG = 1;
    $("#addrowVideoLink").on("click", function () {
        var newRow = $('<tr>');
        var cols = "";
        cols += '<td class="col-sm-4" style="padding-top: 9px; width:60%"><input type="text" class="form-control m-input" name="file_name[]" id="file_name_' + counter_editVG + '"></td>';
		 /*cols += '<td class="col-sm-2" style="padding-top: 9px; width:30%"><input type="file" class="form-control m-input" name="video_img[]" id="video_img_' + counter_editVG + '"></td>';*/
        cols += '<td class="col-sm-2"><div  class="btn-sm btn btn-danger m-btn m-btn--icon m-btn--pill ibtnDelVidLink"> <span> <i class="la la-trash-o"></i> <span> Delete </span> </span> </div></td></tr>';
        
        $("table.order-list-event-group-videos").append(newRow.append(cols)).slideDown("slow");
        counter_editVG++;
    });

    $("table.order-list-event-group-videos").on("click", ".ibtnDelVidLink", function (event) {
        $(this).closest("tr").remove();       
        counter_editVG -= 1
    });
	
	// For Event Groups Comments
    var counter_comnt = 1;
    $("#addrowComments").on("click", function () {
        var cols = "";
		cols +='<br><div class="row">';
               cols += '<div class="col-md-12" style="border: 2px dashed #5867dd;padding:10px;">';
               cols += '<table id="myTable" class="tabl-order-list-sec1-comments">';
                 cols += '<thead>';
                   cols += '<tr>';
                     cols += '<td class="col-sm-8">&nbsp;</td>';
                     cols += '<td class="col-sm-4"><div  class="btn-sm btn btn-danger m-btn m-btn--icon m-btn--pill ibtnDelCmntLink"> <span> <i class="la la-trash-o"></i> <span> Delete </span> </span> </div></td>';
                   cols += '</tr>';
                 cols += '</thead>';
                 cols += '<tbody>';
                   cols += '<tr>';
                     cols += '<td colspan="2" class="col-sm-4">Title</td>';
                   cols += '</tr>';
                   cols += '<tr>';
                     cols += '<td colspan="2" ><input type="text" class="form-control m-input" name="comment_tile[]" id="title_' + counter_edit + '"></td>';
                   cols += '</tr>';
                   cols += '<tr>';
                     cols += '<td colspan="2" class="col-sm-4">Rating</td>';
                   cols += '</tr>';
                   cols += '<tr>';
                     cols += '<td colspan="2" class="col-sm-4"><div class="m-radio-inline">';
                         cols += '<label class="m-radio">';
                           cols += '<input type="radio" name="ratings['+ counter_comnt +']" id="ratings_' + counter_comnt + '" value="1" checked>1 <span></span> </label>';
                        cols += '<label class="m-radio">';
                         cols += ' <input type="radio" name="ratings['+ counter_comnt +']" id="ratings_' + counter_comnt + '" value="2">2 <span></span> </label>';
                        cols += '<label class="m-radio">';
                          cols += '<input type="radio" name="ratings['+ counter_comnt +']" id="ratings_' + counter_comnt + '" value="3">3 <span></span> </label>';
                        cols += '<label class="m-radio">';
                          cols += '<input type="radio" name="ratings['+ counter_comnt +']" id="ratings_' + counter_comnt + '" value="4">4 <span></span> </label>';
                        cols += '<label class="m-radio">';
                         cols += ' <input type="radio" name="ratings['+ counter_comnt +']" id="ratings_' + counter_comnt + '" value="5">5 <span></span> </label>';
                        cols += '<label class="m-radio">';
                          cols += '<input type="radio" name="ratings['+ counter_comnt +']" id="ratings_' + counter_comnt + '" value="6">6 <span></span> </label>';
                        cols += '<label class="m-radio">';
                          cols += '<input type="radio" name="ratings['+ counter_comnt +']" id="ratings_' + counter_comnt + '" value="7">7 <span></span> </label>';
                        cols += '<label class="m-radio">';
                          cols += '<input type="radio" name="ratings['+ counter_comnt +']" id="ratings_' + counter_comnt + '" value="8">8 <span></span> </label>';
                        cols += '<label class="m-radio">';
                          cols += '<input type="radio" name="ratings['+ counter_comnt +']" id="ratings_' + counter_comnt + '" value="9">9 <span></span> </label>';
                        cols += '<label class="m-radio">';
                          cols += '<input type="radio" name="ratings['+ counter_comnt +']" id="ratings_' + counter_comnt + '" value="10">10 <span></span> </label>';
                     cols += ' </div></td>';
                  cols += '</tr>';
                  cols += '<tr>';
                    cols += '<td colspan="2" class="col-sm-4">Signature</td>';
                  cols += '</tr>';
                 cols += ' <tr>';
                    cols += '<td colspan="2" class="col-sm-4"><input type="text" class="form-control m-input" name="signature[]" id="signature_' + counter_comnt + '"></td>';
                  cols += '</tr>';
                  cols += '<tr>';
                    cols += '<td colspan="2" class="col-sm-4">Comments</td>';
                 cols += ' </tr>';
                  cols += '<tr>';
                    cols += '<td colspan="2" class="col-sm-4"><textarea class="form-control m-input" rows="5" name="comments[]" id="comments_' + counter_comnt + '"></textarea></td>';
                 cols += ' </tr>';
                cols += '</tbody> </table>';
              cols += '<br>';
             cols += ' </div>';
              cols += '</div>';
        $("#section1_commment_div").append(cols).slideDown("slow");
        counter_comnt++;
    });

    $("#section1_commment_div").on("click", ".ibtnDelCmntLink", function (event) {
        $(this).closest('div.row').remove();       
        counter_comnt -= 1
    });  

});

function edit_event(id)
{   
    $('#showManualTickets').hide();
    $("#seats_on_map1_edit").attr('checked', false);
    $("#seats_on_map2_edit").attr('checked', false);
    $('#EditEventRolesDivRow').html('');
    //$("#m_select2_4_edit").select2().val('');
    //Ajax Load data from ajax
    $.ajax({
        url : "../../../events/edit/" + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
			//alert('section='+data['event']['booking_fee']);
            $('.overview_Desc').summernote('code', data['event']['description']);
			$('#section_edit').val(data['event']['section']).prop('selected', true);
			$('#m_select2_3_edit').select2().val(data['event']['city_id']).trigger('change');
			//$('#auditorium_id_edit').val(data['event']['auditorium_id']).prop('selected', true);
			$('#auditorium_id_edit').select2().val(data['event']['auditorium_id']).trigger('change'); 
			// select2('val','asp');
            $('#title_event_edit').val(data['event']['title']);
			 $('#booking_fee_edit').val(data['event']['booking_fee']);
			$('#date_event_edit').val(data['event']['date_e']);
			$('#section_e_drop').val(data['event']['section']).prop('selected', true);
			$('#contributor_edit').val(data['event']['contributor']);
			$('#artist_name_ev').val(data['event']['artist_name']);
			$('#author_name_ev').val(data['event']['author_name']);
			$('#productor_name_ev').val(data['event']['productor_name']);
			$('#director_name_ev').val(data['event']['director_name']);
			$('#director_edit').val(data['event']['director']);
			$('#status_edit').val(data['event']['status']).prop('selected', true);
			$('#event_ticket_type_edit').val(data['event']['event_ticket_type']).prop('selected', true);
			$('#contributor_name_ev').val(data['event']['contributor_name']);
			$('#contributor_description_ev').val(data['event']['contributor_description']);
			$('#id_event_edit').val(data['event']['id']);
			$('ul.pic-list-edit').html(data['event_images']);
			$('#eventTimings').html(data['times']);
			if(data['event']['seats_on_map'] == 'N'){
			    $('#showManualTickets').show();	
			    $('#eventTickets').html(data['tickets']);
			}else{
				$('#showManualTickets').hide();
				$('#eventTickets').html('');	
			}
			if(data['event']['seats_on_map'] == 'Y'){
			   $("#seats_on_map1_edit").attr('checked', 'checked');
			}else{
			  $("#seats_on_map2_edit").attr('checked', 'checked');
			}
			$('#EditEventRolesDivRow').html(data['roles']);
			$('.note-editor.note-frame .note-editing-area .note-editable').css('padding', '100px !important'); 
			//$('#m_select2_4_edit').unbind( "change" );
			$('#modal-edit-event').modal('show'); // show bootstrap modal when complete loaded
			
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}


// Event
function updateEvent(){
	var div_id_or_class  = '#mesg_div_event_up';
	if($("#title_event_edit").val()=="")
	{
		//$("#title_event_edit").focus();
		$(div_id_or_class).show().addClass('alert alert-danger').html('Please enter event title.');
		hideErrorDiv(div_id_or_class);
		return false;
	}
	
	if($("#date_event_edit").val()=="")
	{
		//$("#date_event_edit").focus();
		$(div_id_or_class).show().addClass('alert alert-danger').html('Please enter event date.');
		hideErrorDiv(div_id_or_class);
		return false;
	}
	
	if($(".city_edit").val()=="")
	{
		//$(".city_edit").focus();
		$(div_id_or_class).show().addClass('alert alert-danger').html('Please select event city.');
		hideErrorDiv(div_id_or_class);
		return false;
	}
	
	if($(".aud_edit").val()=="")
	{
		//$(".aud_edit").focus();
		$(div_id_or_class).show().addClass('alert alert-danger').html('Please select event auditorium.');
		hideErrorDiv(div_id_or_class);
		return false;
	}
	
	
    
	var url;
    url = "../../../events/update";
   var formData = new FormData($('#form_add_event_frm_update')[0]);
   $.ajax({
	 url: url, 
	 type: "POST",
	data: formData,
	contentType: false,
	processData: false,
	dataType: "JSON",
	success: function(data) {
		
		if(data.status == 'error'){
			$(div_id_or_class).show().addClass('alert alert-danger').html(data.message);
			hideErrorDiv(div_id_or_class);
		}else if(data.status == 'duplicate'){
			$(div_id_or_class).show().addClass('alert alert-danger').html(data.message);
			hideErrorDiv(div_id_or_class);
		}else if(data.status == 'file_error'){
			$(div_id_or_class).show().addClass('alert alert-danger').html(data.message);
			hideErrorDiv(div_id_or_class);
		}else if(data.status) {
			    $('#modal-edit-event').modal('hide');
				//$('#form_add_event_frm')[0].reset();
			    location.reload();	
		}
	}, 
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			console.log(XMLHttpRequest.toSource());
		}
	});
	return false;

}
// Remove Event picture
function remove_pic(id){
	var info = 'id=' + id;
	if(confirm("Are you sure, you remove this picture?"))
	{
	$.ajax({
	type: "GET",
	url: "../../../events/deleteEventPic/"+id,
	data: info,
	success: function(data){
		$('.hide_li_'+id).remove().slideUp('slow'); 
	}
	});
	
	}
	
}

// Delete Function for the Event
function delete_function_event(id){
var info = 'id=' + id;
	if(confirm("Are you sure, you remove this event?"))
	{
	$.ajax({
	type: "GET",
	url: "../../../events/delete/"+id,
	data: info,
	success: function(data){
		//$('.hide_li_'+id).remove().slideUp('slow'); 
		location.reload();
	}
	});
	
	}
}
// Remove comments
function removeComment(id){
	var info = 'id=' + id;
	if(confirm("Are you sure, you remove this comment?"))
	{
	$.ajax({
	type: "GET",
	url: "./deleteGroupComment/"+id,
	data: info,
	success: function(data){
		$('#section1_commment_div_data_'+id).hide().slideUp('slow'); 
	}
	});
	
	}
}


// Remove Event Group video Link
function remove_link(id){
	var info = 'id=' + id;
	if(confirm("Are you sure, you remove this video link?"))
	{
	$.ajax({
	type: "GET",
	url: "../removeVideoLink/"+id,
	data: info,
	success: function(data){
		$('#rowDel_'+id).remove().slideUp('slow'); 
	}
	});
	
	}
	
}

var event_group_id = $('#event_id').val();
var funtion_drop_url = "../upload/"+event_group_id;
Dropzone.options.myAwesomeDropzone = false;
        Dropzone.autoDiscover = false;
        $("#m-dropzone-two").dropzone({    
		    addRemoveLinks: true,              
			url: funtion_drop_url,
			maxFilesize: 10,
			maxFiles: 100,
			acceptedFiles: "image/jpeg,image/png,image/gif",
			init: function() {
			thisDropzone = this;
			$.post(funtion_drop_url, 
			function(data) {
			data = JSON.parse(data);	
			$.each(data, function(key,value){
				var mockFile = { rowId: value.row_id, serverId: value.name, size: value.size };
				thisDropzone.emit("addedfile", mockFile);
				thisDropzone.emit("thumbnail", mockFile, value.dir+value.name);
				});
				$('.dz-progress').remove();
			});
			 this.on("queuecomplete", function (file) {
				  //location.reload();
				  
			  });
			this.on("success", function(file, response) {
			   file.serverId = response; 
			   
			});
			this.on("removedfile", function(file) {
				if (!file.serverId) { return; }
				     $.post("../removeFile/"+file.serverId); 
				});	
			}
        });


function mark_it(id,status){
	if(id != '' && status != ''){
		var info = 'id='+id+'&status='+status;
		if(status == 0){
			var sts = 'Active';
		}else{
			var sts = 'Inactive';
		}
		if(confirm("Are you sure, you want to make this event "+ sts +"?"))
	   {
		$.ajax({
		type: "GET",
		url: "../../../events/updateEventStatus/"+id+'/'+status,
		//data: info,
		success: function(data){
			location.reload();
		}
		});
	  }
	}
}


// Remove Event Time
function remove_Time(id){
	var info = 'id=' + id;
	if(confirm("Are you sure, you remove this Event Time?"))
	{
	$.ajax({
	type: "GET",
	url: "../../../events/deleteEventTime/"+id,
	data: info,
	success: function(data){
		$('#eventTime_'+id).remove().slideUp('slow'); 
	}
	});
	
	}
	
}


// Remove Event Ticket
function remove_Ticket(id){
	var info = 'id=' + id;
	if(confirm("Are you sure, you remove this Event Ticket?"))
	{
	$.ajax({
	type: "GET",
	url: "../../../events/deleteEventTicket/"+id,
	data: info,
	success: function(data){
		$('#eventTicket_'+id).remove().slideUp('slow'); 
	}
	});
	
	}
	
}


// Remove Event Role
function remove_Role(id){
	var info = 'id=' + id;
	if(confirm("Are you sure, you remove this Event Role?"))
	{
	$.ajax({
	type: "GET",
	url: "../../../events/deleteEventRole/"+id,
	data: info,
	success: function(data){
		$('#eventRole_'+id).remove().slideUp('slow'); 
	}
	});
	
	}
	
}


// Remove Event Group Role
function remove_EventGroupRole(id){
	var info = 'id=' + id;
	if(confirm("Are you sure, you remove this Event Group Role?"))
	{
	$.ajax({
	type: "GET",
	url: "../deleteEventGroupRole/"+id,
	data: info,
	success: function(data){
		$('#eventEVGRole_'+id).remove().slideUp('slow'); 
	}
	});
	
	}
	
}


function checkAuditoriumMap(sel)
{
	// Get the selected auditorium here
    alert(sel.value);
}

$(function(){
	$('input[name=seats_on_map]').change(function(){
		var selected_option = $( 'input[name=seats_on_map]:checked' ).val();
		if(selected_option == 'N'){
			//$('#AddEventTicketDiv').show('slow');
		}else{
			//$('#AddEventTicketDivRow').html('');
		   // $('#AddEventTicketDiv').hide('slow');
		}
	});
});

$(function(){
	$('input[name=seats_on_map_edit]').change(function(){
		var selected_option = $( 'input[name=seats_on_map_edit]:checked' ).val();
		if(selected_option == 'N'){
			//$('#showManualTickets').show('slow');
		}else{
			//$('#eventTickets').html('');
		    //$('#showManualTickets').hide('slow');
		}
	});
});

$(function(){
$(".number_price_only_1").keydown(function (e) {
            if (e.shiftKey || e.ctrlKey || e.altKey) { // if shift, ctrl or alt keys held down
                e.preventDefault();         // Prevent character input
            } else {
                var n = e.keyCode;
                if (!((n == 8)              // backspace
                        || (n == 46)                // delete
                        || (n >= 35 && n <= 40)     // arrow keys/home/end
                        || (n >= 48 && n <= 57)     // numbers on keyboard
                        || (n >= 96 && n <= 105))   // number on keypad
                        ) {
                    e.preventDefault();
                    // alert("in if");
                    // Prevent character input
                }
            }
        });
});

$(function(){
    $('#m_select2_4').on('change', function() {
		$('#AddEventTicketDivRow').html('');
      var auditorium_id = $("#m_select2_4 option:selected").val();
	  if(auditorium_id != ''){
	  var info = 'id=' + auditorium_id;
		$.ajax({
		type: "GET",
		url: "../../../auditoriums/getAuditoriumSeatMap/"+auditorium_id,
		data: '',
		success: function(data){
			/*if(data == 'No'){
				$('#AddEventTicketDiv').show('slow');
			}else{*/
				//$('#AddEventTicketDivRow').html(data);
				/*$('#AddEventTicketDiv').hide('slow');
			}*/
			//$('#section1_commment_div_data_'+id).hide().slideUp('slow'); 
		}
		});
	  }
    })
});

// Edit

function getaudmap(){
    $('.aud_edit').on('change', function() {
		$('#eventTickets').html('');
      var auditorium_id = $(".aud_edit option:selected").val();
	  if(auditorium_id != ''){
	  var info = 'id=' + auditorium_id;
		$.ajax({
		type: "GET",
		url: "../../../auditoriums/getAuditoriumSeatMap/"+auditorium_id,
		data: '',
		success: function(data){
			/*if(data == 'No'){
				$('#AddEventTicketDiv').show('slow');
			}else{*/
				//$('#eventTickets').html(data);
				/*$('#AddEventTicketDiv').hide('slow');
			}*/
			//$('#section1_commment_div_data_'+id).hide().slideUp('slow'); 
		}
		});
	  }
    })
}


function allStorage() {

    var values = [],
        keys = Object.keys(localStorage),
        i = keys.length;

    while ( i-- ) {
        values.push( localStorage.getItem(keys[i]) );
    }

    return values;
}


function changeAud(sel){
  	/*var ticket_type = $('#event_ticket_type').val();
	var auditorium_id  = sel.value;
	if(ticket_type == ''){
		alert('Please select Ticket Type');
		$('#AddEventTicketDivRow').html('');
		$('#event_ticket_type').val('4');
	}else if(ticket_type == 2 || ticket_type == 3){
		$('#AddEventTicketDivRow').html('');
		 
		  if(auditorium_id != ''){
		  var info = 'id=' + auditorium_id;
			$.ajax({
			type: "GET",
			url: "../../../auditoriums/getAuditoriumSeatMap/"+auditorium_id,
			data: '',
			success: function(data){
				$('#AddEventTicketDivRow').html(data);
			}
			});
		  }
		
	}else{
	  	$('#AddEventTicketDivRow').html('');
	}*/
}
function changeTicketType(sel)
{
	/*var auditorium_id = $('#auditorium_id').val();
	var ticket_type  = sel.value;
	if(auditorium_id == ''){
		alert('Please select auditorium first');
		$('#event_ticket_type').val('4');
	}else if(ticket_type == 2 || ticket_type == 3){
		$('#AddEventTicketDivRow').html('');
		 
		  if(auditorium_id != ''){
		  var info = 'id=' + auditorium_id;
			$.ajax({
			type: "GET",
			url: "../../../auditoriums/getAuditoriumSeatMap/"+auditorium_id,
			data: '',
			success: function(data){
				$('#AddEventTicketDivRow').html(data);
			}
			});
		  }
		
	}else{
	  	$('#AddEventTicketDivRow').html('');
	}*/
	
}


function changeAudEdit(sel){
	/*
  	var ticket_type = $('#event_ticket_type_edit').val();
	var event_id = $('#id_event_edit').val();
	var auditorium_id  = sel.value;
	if(ticket_type == ''){
		alert('Please select Ticket Type');
		$('#eventTickets').html('');
		$('#event_ticket_type').val('4');
	}else if(ticket_type == 2 || ticket_type == 3){
		$('#eventTickets').html('');
		 
		  if(auditorium_id != ''){
		  var info = 'id=' + auditorium_id;
			$.ajax({
			type: "GET",
			url: "../../../auditoriums/getAuditoriumSeatMapEvent/"+auditorium_id+'/'+event_id,
			data: '',
			success: function(data){
				$('#eventTickets').html(data);
			}
			});
		  }
		
	}else{
	  	$('#eventTickets').html('');
	}
	*/
}
function changeTicketTypeEdit(sel)
{
	/*
	var auditorium_id = $('#auditorium_id_edit').val();
	var event_id = $('#id_event_edit').val();
	var ticket_type  = sel.value;
	if(auditorium_id == ''){
		alert('Please select auditorium first');
		$('#event_ticket_type').val('4');
	}else if(ticket_type == 2 || ticket_type == 3){
		$('#eventTickets').html('');
		 
		  if(auditorium_id != ''){
		  var info = 'id=' + auditorium_id;
			$.ajax({
			type: "GET",
			url: "../../../auditoriums/getAuditoriumSeatMapEvent/"+auditorium_id+'/'+event_id,
			data: '',
			success: function(data){
				$('#eventTickets').html(data);
			}
			});
		  }
		
	}else{
	  	$('#eventTickets').html('');
	}
	*/
	
}










