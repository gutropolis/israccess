var DatatableRemoteAjaxDemoCat = {
	
    init: function() {
       // var t;
        t = $(".m_datatable_aud").mDatatable({
			
            data: {
                type: "remote",
                source: {
                    read: {
                        url: "getAjaxAuditoriumsList",
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
            }, {
                field: "name",
                title: Auditorium_Name,
                filterable: !1,
                width: 400,
                template: "{{name}}"
            },
			{
                field: "background_file",
                title: Auditorium_Picture,
                filterable: !1,
                width: 200,
                template: "<img src='{{background_file}}' height=100 width=100 class='img-responsive img-circle' >"
            },
			
			   {
                field: "Actions",
                width: 110,
                title: ACTIONS,
                sortable: !1,
                overflow: "visible",
                template: function(t, e, a) {
                    return '\t\t\t\t\t\t<a href="javascript:void(0);" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="'+EDIT+' ' + AUDITORIUM_TXT+'" onclick="edit('+[t.id]+')">\t\t\t\t\t\t\t<i class="la la-edit"></i>\t\t\t\t\t\t</a>\t\t\t\t\t\t<a href="javascript:void(0);" onclick="delete_function('+[t.id]+')" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" title="'+DELETE+' '+AUDITORIUM_TXT+'">\t\t\t\t\t\t\t<i class="la la-trash"></i>\t\t\t\t\t\t</a>\t\t\t\t\t'
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
function addAuditorium(){
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
		$(div_id_or_class).show().addClass('alert alert-danger').html('Please select auditorium picture.');
		hideErrorDiv(div_id_or_class);
		$("#auditorium_picture").focus();
		return false;
	}
	
	
	var url;
    url = "saveAuditorium";
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
				$('#modal-add-auditorium').modal('hide');
				$('#form_add_aud')[0].reset();
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
  window.location.href='./auditoriums/edit/'+id;
}

// Update
function updateAuditorium(){
	var div_id_or_class  = '#mesg_div_e';
	if($("#auditorium_name_e").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html(auditorium_name_msg_txt);
		hideErrorDiv(div_id_or_class);
		$("#auditorium_name_e").focus();
		return false;
	}
	
	
	
	var url;
     url = "auditoriums/update";
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
				$('#modal-edit-auditorium').modal('hide');
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

// Delete Function
function delete_function(id){
   $('.del_title').html(DELETE+' '+auditorium_txt);
   $('.del_text').html(common_delete_confirm_msg_txt +' '+auditorium_txt +'?');	
   $('#id').val(id);
   $('#modal-delete').modal('show');
}





