var DatatableRemoteAjaxDemoCat = {
	
    init: function() {
       // var t;
        t = $(".m_datatable_sections").mDatatable({
            data: {
                type: "remote",
                source: {
                    read: {
                        url: "getAjaxSectionsList",
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
			//bStateSave: !1,
            sortable: !0,
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
                width: 40,
                selector: !1,
                textAlign: "center",
				selector:{
					id : "check_box_class",
					class:"m-checkbox--solid m-checkbox--brand"}
            },  {
                field: "section_title",
                title: Section_Title,
                filterable: !1,
                width: 150,
                template: "{{section_title}}"
            },
			{
                field: "section_name",
                title: Section_Name,
                filterable: !1,
                width: 200,
                template: "{{section_name}}"
            },{
                field: "display_order",
                title: Display_Order,
                filterable: !1,
                width: 150,
                template: "{{display_order}}"
            },
			 {
                field: "status",
                title: STATUS,
                template: function(t) {
                    var e = {
                        0: {
                            title: INACTIVE,
                            class: "m-badge--danger"
                        },
                        1: {
                            title: ACTIVE,
                            class: " m-badge--success"
                        }
                    };
                    return '<span class="m-badge ' + e[t.status].class + ' m-badge--wide">' + e[t.status].title + "</span>"
                }
            },  {
                field: "Actions",
                width: 110,
                title: ACTIONS,
                sortable: !1,
                overflow: "visible",
                template: function(t, e, a) {
                    return '\t\t\t\t\t\t<a href="javascript:void(0);" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="'+EDIT+' Section" onclick="edit('+[t.id]+')">\t\t\t\t\t\t\t<i class="la la-edit"></i>\t\t\t\t\t\t</a>\t\t\t\t\t\t<a href="javascript:void(0);" onclick="delete_function('+[t.id]+')" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" title="'+DELETE+' Section">\t\t\t\t\t\t\t<i class="la la-trash"></i>\t\t\t\t\t\t</a>\t\t\t\t\t'
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
    DatatableRemoteAjaxDemoCat.init()
});

function reloadTable()
{
    t.reload(); //reload datatable ajax 
}

function hideErrorMsg(){
  setTimeout(function(){
    $('#mesg_div').delay(1000).slideUp(300); 
 }, 1000);
}

// Add
function addSection(){
	var div_id_or_class  = '#mesg_div';
	if($("#section_title").val()=="")
	{
		//$("#section_title").focus();
		$(div_id_or_class).show().addClass('alert alert-danger').html(section_title_msg_txt);
		hideErrorDiv(div_id_or_class);
		return false;
	}
	if($("#section_name").val()=="")
	{
		//$("#section_title").focus();
		$(div_id_or_class).show().addClass('alert alert-danger').html(section_name_msg_txt);
		hideErrorDiv(div_id_or_class);
		return false;
	}

	
	var url;
    url = "saveSection";
   var formData = new FormData($('#form_add_section')[0]);
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
				$('#modal-add-section').modal('hide');
				$('#form_add_section')[0].reset();
			    //location.reload();	
				reloadTable();
		}
	}, 
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			console.log(XMLHttpRequest.toSource());
		}
	});
	return false;

}

// Edit function 
function edit(id)
{   
    $('#form_add_section_e')[0].reset(); // reset form on modals
    //Ajax Load data from ajax
    $.ajax({
        url : "sections/edit/" + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
			
            $('#id_e').val(data['id']);
			$('#section_title_e').val(data['section_title']);
			$('#section_name_e').val(data['section_name']);
			$('#display_order_e').val(data['display_order']);
			$('#status_e').val(data['status']).prop('selected', true);
			$('#modal-edit-section').modal('show'); // show bootstrap modal when complete loaded
			
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

// Update
function updateSection(){
	var div_id_or_class  = '#mesg_div_e';
	if($("#section_title_e").val()=="")
	{
		//$("#section_title_e").focus();
		$(div_id_or_class).show().addClass('alert alert-danger').html(section_title_msg_txt);
		hideErrorDiv(div_id_or_class);
		return false;
	}
	if($("#section_name_e").val()=="")
	{
		//$("#section_title").focus();
		$(div_id_or_class).show().addClass('alert alert-danger').html(section_name_msg_txt);
		hideErrorDiv(div_id_or_class);
		return false;
	}
	
	
	var url;
     url = "sections/update";
   var formData = new FormData($('#form_add_section_e')[0]);
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
				$('#modal-edit-section').modal('hide');
			    //location.reload();	
				reloadTable();
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
	var id = $('#id').val();
	var formData = new FormData($('#form_del_data')[0]);
	// ajax delete data from database
	$.ajax({
		url : "sections/delete/"+id,
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

// Delete Function
function delete_function(id){
   $('.del_title').html('Delete Section');
   $('.del_text').html('Are you sure you want to delete this Section?');	
   $('#id').val(id);
   $('#modal-delete').modal('show');
}







