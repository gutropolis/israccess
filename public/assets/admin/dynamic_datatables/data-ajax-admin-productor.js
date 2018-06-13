
var DatatableRemoteAjaxDemoCat = {
	
    init: function() {
        //var t;
        t = $(".m_datatable_pro").mDatatable({
            data: {
                type: "remote",
                source: {
                    read: {
                        url: "getAjaxProductorsList",
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
                width: 20,
                selector: !1,
                textAlign: "center",
				 selector:{
					id : "check_box_class",
					class:"m-checkbox--solid m-checkbox--brand"}
            },{
                field: "company_name",
                title: productor_comp_name,
                filterable: !1,
                width: 150,
                template: "{{company_name}}"
            }, {
                field: "name",
                title: productor_name,
                filterable: !1,
                width: 150,
                template: "{{name}}"
            },
			{
                field: "first_name",
                title: productor_first_name,
                filterable: !1,
                width: 150,
                template: "{{first_name}}"
            },
			{
                field: "email",
                title: productor_mail,
                filterable: !1,
                width: 150,
                template: "{{email}}"
            },
			{
                field: "telephone",
                title: productor_telephone,
                filterable: !1,
                width: 80,
                template: "{{telephone}}"
            }/*,
			{
                field: "office_phone",
                title: productor_office_phone,
                filterable: !1,
                width: 80,
                template: "{{office_phone}}"
            },
			{
                field: "company_phone",
                title: productor_company_number,
                filterable: !1,
                width: 80,
                template: "{{company_phone}}"
            }*/,
			
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
                width: 100,
                title: ACTIONS,
                sortable: !1,
                overflow: "visible",
                template: function(t, e, a) {
                    return '\t\t\t\t\t\t<a href="javascript:void(0);" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="'+EDIT+' '+ productor_txt +'" onclick="edit('+[t.id]+')">\t\t\t\t\t\t\t<i class="la la-edit"></i>\t\t\t\t\t\t</a>\t\t\t\t\t\t<a href="javascript:void(0);" onclick="delete_function('+[t.id]+')" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" title="'+DELETE+' '+ productor_txt +'">\t\t\t\t\t\t\t<i class="la la-trash"></i>\t\t\t\t\t\t</a>\t\t\t\t\t'
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
function addProductor(){
	var email_add = $("#artist_email").val();
	var div_id_or_class  = '#mesg_div';
	if($("#first_name").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html('Please enter productor first name');
		hideErrorDiv(div_id_or_class);
		$("#first_name").focus();
		return false;
	}
	
	if($("#artist_name").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html(productor_name_msg_txt);
		hideErrorDiv(div_id_or_class);
		$("#artist_name").focus();
		return false;
	}
    
	
	
	if($("#artist_email").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html(productor_email_msg_txt);
		hideErrorDiv(div_id_or_class);
		$("#artist_email").focus();
		return false;
	}
	
	if( !validateEmail(email_add)) { 
		$(div_id_or_class).show().addClass('alert alert-danger').html('Enter valid email address');
		hideErrorDiv(div_id_or_class);
		$("#artist_email").focus();
		return false;
	}
	
	if($("#company_name").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html('Please enter company name');
		hideErrorDiv(div_id_or_class);
		$("#company_name").focus();
		return false;
	}
	
	if($("#telephone").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html('Please enter productor telephone');
		hideErrorDiv(div_id_or_class);
		$("#telephone").focus();
		return false;
	}
	
	if($("#office_phone").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html('Please enter productor office phone number');
		hideErrorDiv(div_id_or_class);
		$("#office_phone").focus();
		return false;
	}
	
	if($("#company_number").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html('Please enter productor company number');
		hideErrorDiv(div_id_or_class);
		$("#company_number").focus();
		return false;
	}
	
	
	
	
	
	var url;
    url = "saveProductor";
   var formData = new FormData($('#form_add_productor')[0]);
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
				$('#modal-add-productor').modal('hide');
				$('#form_add_productor')[0].reset();
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
		url : "productors/delete/"+$('#id').val(),
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
   $('.del_title').html(DELETE +' '+productor_txt);
   $('.del_text').html( common_delete_confirm_msg_txt+productor_txt+'?');	
   $('#id').val(id);
   $('#modal-delete').modal('show');
}

function edit(id)
{   
    $('#form_add_productor_e')[0].reset(); // reset form on modals
   

    //Ajax Load data from ajax
    $.ajax({
        url : "productors/edit/" + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
			
            $('#id_e').val(data['productor']['id']);
			$('#artist_name_e').val(data['productor']['name']);
			$('#artist_email_e').val(data['productor']['email']);
			$('#status_e').val(data['productor']['status']).prop('selected', true);
			$('#first_name_e').val(data['productor_data'][0]['first_name']);
			$('#company_name_e').val(data['productor_data'][0]['company_name']);
			$('#telephone_e').val(data['productor_data'][0]['telephone']);
			$('#office_phone_e').val(data['productor_data'][0]['office_phone']);
			$('#company_number_e').val(data['productor_data'][0]['company_number']);
			
			
			$('#modal-edit-productor').modal('show'); // show bootstrap modal when complete loaded
			
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

// Update
function updateProductor(){
	var email_add = $("#artist_email_e").val();
	var div_id_or_class  = '#mesg_div_e';
	if($("#first_name_e").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html('Please enter productor first name');
		hideErrorDiv(div_id_or_class);
		$("#first_name_e").focus();
		return false;
	}
	
	if($("#artist_name_e").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html(productor_name_msg_txt);
		hideErrorDiv(div_id_or_class);
		$("#artist_name_e").focus();
		return false;
	}
    
	
	
	if($("#artist_email_e").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html(productor_email_msg_txt);
		hideErrorDiv(div_id_or_class);
		$("#artist_email_e").focus();
		return false;
	}
	
	if( !validateEmail(email_add)) { 
		$(div_id_or_class).show().addClass('alert alert-danger').html('Enter valid email address');
		hideErrorDiv(div_id_or_class);
		$("#artist_email_e").focus();
		return false;
	}
	
	if($("#company_name_e").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html('Please enter company name');
		hideErrorDiv(div_id_or_class);
		$("#company_name_e").focus();
		return false;
	}
	
	if($("#telephone_e").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html('Please enter productor telephone');
		hideErrorDiv(div_id_or_class);
		$("#telephone_e").focus();
		return false;
	}
	
	if($("#office_phone_e").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html('Please enter productor office phone number');
		hideErrorDiv(div_id_or_class);
		$("#office_phone_e").focus();
		return false;
	}
	
	if($("#company_number_e").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html('Please enter productor company number');
		hideErrorDiv(div_id_or_class);
		$("#company_number_e").focus();
		return false;
	}
	
	
	
	var url;
    url = "productors/update";
   var formData = new FormData($('#form_add_productor_e')[0]);
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
				$('#modal-edit-productor').modal('hide');
				$('#form_add_productor_e')[0].reset();
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



function validateEmail($email) {
   var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
  return emailReg.test( $email );
}




