//jQuery(document).ready(function() {
	var event_id = $('#event_id').val();
//});


var DatatableRemoteAjaxDemoEvents = {
	
    init: function() {
       // var t;
        t = $(".m_datatable_cat_map").mDatatable({
            data: {
                type: "remote",
                source: {
                    read: {
                        url: "../../events/getAjaxEventMapList",
						//headers: {'x-my-custom-header': 'some_value', 'x-test-header': 'the_value'},
						params: {
							// custom query params
							post_data: {
							   event_id: event_id
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
                field: "stock_available_date",
                title: "Date Available",
                filterable: !1,
                width: 110,
                template: "{{stock_available_date}}"
            },
			{
                field: "stock_expiry_date",
                title: "Date Expiry",
                filterable: !0,
                width: 100,
                template: "{{stock_expiry_date}}"
            },
			{
                field: "total_qantity",
                title: "Total Quantity",
                filterable: !0,
                width: 120,
                template: "{{total_qantity}}"
            },
			{
                field: "remaining_seats",
                title: "Remaining Seats",
                filterable: !0,
                width: 120,
                template: "{{remaining_seats}}"
            },
			{
                field: "status",
                title: "Status",
				width: 80,
                template: function(t) {
                    var e = {
                        0: {
                            title: "Expired",
                            class: "m-badge--danger",
							statusTitle : "Expired"
                        },
                        1: {
                            title: "Available",
                            class: " m-badge--success",
							statusTitle : "Available"
                        }
                    };
                    return '<span class="m-badge ' + e[t.status].class + ' m-badge--wide">' + e[t.status].title + "</span>"
                }
            },
			{
                field: "Actions",
                width: 150,
                title: "Actions",
                sortable: !1,
                overflow: "visible",
                template: function(t, e, a) {
					
                    return '\t\t\t\t\t\t<a href="javascript:void(0);"  class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Edit Event Map" onclick="edit_event('+[t.id]+')">\t\t\t\t\t\t\t<i class="la la-edit"></i>\t\t\t\t\t\t</a>\t\t\t\t\t\t<a href="javascript:void(0);" onclick="delete_function_event('+[t.id]+')" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" title="Delete Event Map">\t\t\t\t\t\t\t<i class="la la-trash"></i>\t\t\t\t\t\t</a>\t\t\t\t\t'
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


jQuery(document).ready(function() {
	
    DatatableRemoteAjaxDemoEvents.init();
	
});





// Delete
function delete_data()
{
	var formData = new FormData($('#form_del_data')[0]);
	// ajax delete data from database
	$.ajax({
		url : "groups/delete1/"+$('#id').val(),
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






$(document).ready(function () {});

function edit_event(id)
{   
   window.location.href='../../events/eventMapEdit/'+id;  
}


// Delete Function for the Event
function delete_function_event(id){
var info = 'id=' + id;
	if(confirm("Are you sure, you remove these seats?"))
	{
	$.ajax({
	type: "GET",
	url: "../../events/deleteEventSeat/"+id,
	data: info,
	success: function(data){
		//$('.hide_li_'+id).remove().slideUp('slow'); 
		location.reload();
	}
	});
	
	}
}













