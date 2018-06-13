
var DatatableRemoteAjaxDemoCat = {
	
    init: function() {
       // var t;
        t = $(".m_datatable_partners").mDatatable({
            data: {
                type: "remote",
                source: {
                    read: {
                        url: "getAjaxPartnersList",
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
            }, 
			{
                field: "partner_logo",
                title: "Partner Logo",
                filterable: !1,
                width: 200,
                template: "<img src='{{partner_logo}}' height=100 width=100 class='img-responsive img-circle' >"
            },
			{
                field: "partner_url",
                title: "Redirect Link",
                filterable: !1,
                width: 200,
                template: "{{partner_url}}"
            },
			 {
                field: "status",
                title: "Status",
                template: function(t) {
                    var e = {
                        0: {
                            title: "Inactive",
                            class: "m-badge--danger"
                        },
                        1: {
                            title: "Active",
                            class: " m-badge--success"
                        }
                    };
                    return '<span class="m-badge ' + e[t.status].class + ' m-badge--wide">' + e[t.status].title + "</span>"
                }
            },  {
                field: "Actions",
                width: 110,
                title: "Actions",
                sortable: !1,
                overflow: "visible",
                template: function(t, e, a) {
                    return '\t\t\t\t\t\t<a href="javascript:void(0);" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Edit Partner" onclick="edit('+[t.id]+')">\t\t\t\t\t\t\t<i class="la la-edit"></i>\t\t\t\t\t\t</a>\t\t\t\t\t\t<a href="javascript:void(0);" onclick="delete_function('+[t.id]+')" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" title="Delete Partner">\t\t\t\t\t\t\t<i class="la la-trash"></i>\t\t\t\t\t\t</a>\t\t\t\t\t'
                }
            }]
        }), $("#refresh_table").on('click', function(){
                t.reload(); 
		  }), 
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


// Add
function addPartner(){
	var div_id_or_class  = '#mesg_div';
	if($("#partner_logo").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html('Please select Partner Logo.');
		hideErrorDiv(div_id_or_class);
		$("#partner_logo").focus();
		return false;
	}
	
	if($("#partner_url").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html('Please select redirect url.');
		hideErrorDiv(div_id_or_class);
		$("#partner_url").focus();
		return false;
	}

	
	var url;
    url = "savePartner";
   var formData = new FormData($('#form_add_partner')[0]);
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
				$('#modal-add-partner').modal('hide');
				$('#form_add_partner')[0].reset();
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
    $('#form_add_partner_e')[0].reset(); // reset form on modals
    //Ajax Load data from ajax
    $.ajax({
        url : "partners/edit/" + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
			
            $('#id_e').val(data['id']);
			$('#partner_url_e').val(data['partner_url']);
			$('#partner_logo_old').val(data['partner_logo']);
			$('#status_e').val(data['status']).prop('selected', true);
			var partner_logo = data['partner_logo'];
			if(partner_logo != '')
            {
                $('#label-photo-edit').text('Change Partner Logo'); // label photo upload
                $('#photo-preview-edit div').html('<img src="'+data['file_web_path']+'/'+partner_logo+'" class="img-rounded img-thumbnail" height=100 width=100>'); // show photo
                
            }
            else
            {
                //$('#label-photo').text('Upload Photo'); // label photo upload
                $('#photo-preview div').text('(No photo)');
            }
			
			$('#modal-edit-partner').modal('show'); // show bootstrap modal when complete loaded
			
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

// Update
function updatePartner(){
	var div_id_or_class  = '#mesg_div_e';
	if($("#partner_logo_old").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html('Please select partner logo.');
		hideErrorDiv(div_id_or_class);
		$("#partner_logo_old").focus();
		return false;
	}
	
	
	
	var url;
     url = "partners/update";
   var formData = new FormData($('#form_add_partner_e')[0]);
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
				$('#modal-edit-partner').modal('hide');
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
		url : "partners/delete/"+id,
		type: "GET",
		dataType: "JSON",
		data: formData,
		processData: false,
		contentType: false,
		success: function(data)
		{
			$('#modal-delete').modal('hide');
			location.reload();
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			alert('Error deleting data');
		}
	});
}

// Delete Function
function delete_function(id){
   $('.del_title').html('Delete Partner');
   $('.del_text').html('Are you sure you want to delete this Partner?');	
   $('#id').val(id);
   $('#modal-delete').modal('show');
}





