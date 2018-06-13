var DatatableRemoteAjaxDemoCat = {
	
    init: function() {
       // var t;
        t = $(".m_datatable_mem").mDatatable({
            data: {
                type: "remote",
                source: {
                    read: {
                        url: "getAjaxMembersList",
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
                field: "member_id",
                title: 'ID',
                filterable: !1,
                width: 50,
                template: "{{member_id}}"
            },  {
                field: "name",
                title: Member_Name,
                filterable: !1,
                width: 150,
                template: "{{name}}"
            },
			{
                field: "first_name",
                title: member_first_name_txt,
                filterable: !1,
                width: 100,
                template: "{{first_name}}"
            },
			{
                field: "email",
                title: Member_Email,
                filterable: !1,
                width: 200,
                template: "{{email}}"
            },
			{
                field: "city",
                title: member_ville_txt,
                filterable: !1,
                width: 50,
                template: "{{city}}"
            },
			
			{
                field: "country",
                title: member_country_txt,
                filterable: !1,
                width: 100,
                template: "{{country}}"
            },
			
			   {
                field: "Actions",
                width: 110,
                title: ACTIONS,
                sortable: !1,
                overflow: "visible",
                template: function(t, e, a) {
                    return '\t\t\t\t\t\t<a href="javascript:void(0);" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="'+common_view_txt+' '+ member_txt+'" onclick="view('+[t.id]+')">\t\t\t\t\t\t\t<i class="la la-eye"></i>\t\t\t\t\t\t</a>\t\t\t\t\t\t<a href="javascript:void(0);" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="'+EDIT+' '+ member_txt+'" onclick="edit('+[t.id]+')">\t\t\t\t\t\t\t<i class="la la-edit"></i>\t\t\t\t\t\t</a>\t\t\t\t\t\t<a href="javascript:void(0);" onclick="delete_function('+[t.id]+')" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" title="'+DELETE+' '+member_txt +'">\t\t\t\t\t\t\t<i class="la la-trash"></i>\t\t\t\t\t\t</a>\t\t\t\t\t'
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
function addMember(){
	var email_add = $("#artist_email").val();
	var div_id_or_class  = '#mesg_div';
	if($("#artist_name").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html(member_name_msg_txt);
		hideErrorDiv(div_id_or_class);
		$("#artist_name").focus();
		return false;
	}
    
	if($("#artist_username").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html(member_username_msg_txt);
		hideErrorDiv(div_id_or_class);
		$("#artist_username").focus();
		return false;
	}
	
	if($("#artist_email").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html(member_email_msg_txt);
		hideErrorDiv(div_id_or_class);
		$("#artist_email").focus();
		return false;
	}
	
	
	

	
	var url;
    url = "saveMember";
   var formData = new FormData($('#form_add_member')[0]);
   $.ajax({
	 url: url, 
	 type: "POST",
	data: formData,
	contentType: false,
	processData: false,
	dataType: "JSON",
	success: function(data) {
		
		if(data.status == 'error'){
			$('#mesg_div').show().addClass('alert alert-danger').html(data.message);
			hideErrorDiv(div_id_or_class);
		}else if(data.status == 'duplicate'){
			$('#mesg_div').show().addClass('alert alert-danger').html(data.message);
			hideErrorDiv(div_id_or_class);
		}else if(data.status == 'file_error'){
			$('#mesg_div').show().addClass('alert alert-danger').html(data.message);
			hideErrorDiv(div_id_or_class);
		}else if(data.status) {
				$('#modal-add-member').modal('hide');
				$('#form_add_member')[0].reset();
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
		url : "members/delete/"+$('#id').val(),
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
   $('.del_title').html(DELETE+' '+member_txt);
   $('.del_text').html(common_delete_confirm_msg_txt +member_txt+'?');	
   $('#id').val(id);
   $('#modal-delete').modal('show');
}

function edit(id)
{   
    $('#form_add_member_e')[0].reset(); // reset form on modals
   

    //Ajax Load data from ajax
    $.ajax({
        url : "members/edit/" + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
            $('#id_e').val(data['member']['id']);
			$('#artist_name_e').val(data['member']['name']);
			$('#artist_username_e').val(data['member']['username']);
			$('#artist_email_e').val(data['member']['email']);
			$('#status_e').val(data['member']['status']).prop('selected', true);
			$('#user_picture_old').val(data['member']['user_picture']);
			// Member meta data
			if( data['member_data'] == ''){
				$('#first_name_e').val('');
				$('#last_name_e').val('');
				$('#address_1_e').val('');
				$('#address_2_e').val('');
				$('#street_e').val('');
				$('#postal_code_e').val('');
				$('#phone_no_e').val('');
				$('#dob_e').val('');
				$('#country_e').val('');
				$('#ville_e').val('');
			}else{
			  $('#first_name_e').val(data['member_data'][0]['first_name']);
				$('#last_name_e').val(data['member_data'][0]['last_name']);
				$('#address_1_e').val(data['member_data'][0]['address_1']);
				$('#address_2_e').val(data['member_data'][0]['address_2']);
				$('#street_e').val(data['member_data'][0]['street']);
				$('#postal_code_e').val(data['member_data'][0]['postal_code']);
				$('#phone_no_e').val(data['member_data'][0]['phone_no']);
				$('#dob_e').val(data['member_data'][0]['dob']);
				$('#country_e').val(data['member_data'][0]['country']);
				$('#ville_e').val(data['member_data'][0]['ville']);	
			}
			var user_picture = data['member']['user_picture'];
			
                $('#label-photo-edit').text(member_change_pic_txt); // label photo upload
                $('#photo-preview-edit div').html('<img src="'+data['member']['file_web_path']+'" class="img-rounded img-thumbnail" height=100 width=100>'); // show photo
                
            
                
            
			$('#modal-edit-member').modal('show'); // show bootstrap modal when complete loaded
			
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

// Update
function updateMember(){
	var email_add = $("#artist_email_e").val();
	var div_id_or_class  = '#mesg_div_e';
	if($("#artist_name_e").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html(member_name_msg_txt);
		hideErrorDiv(div_id_or_class);
		$("#artist_name_e").focus();
		return false;
	}
    
	if($("#artist_username_e").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html(member_username_msg_txt);
		hideErrorDiv(div_id_or_class);
		$("#artist_username_e").focus();
		return false;
	}
	
	if($("#artist_email_e").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html(member_email_msg_txt);
		hideErrorDiv(div_id_or_class);
		$("#artist_email_e").focus();
		return false;
	}
	
	
	var url;
    url = "members/update";
   var formData = new FormData($('#form_add_member_e')[0]);
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
				$('#modal-edit-member').modal('hide');
				$('#form_add_member_e')[0].reset();
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


function view(id)
{   
    $('#form_add_member_e')[0].reset(); // reset form on modals
   

    //Ajax Load data from ajax
    $.ajax({
        url : "members/view/" + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
            
			$('#artist_name_v').text(data['member']['name']);
			$('#artist_username_v').text(data['member']['username']);
			$('#artist_email_v').text(data['member']['email']);
			$('#status_v').text(data['member']['status']);	
			$('#credit_v').text(data['member']['status']);	
			// Member meta data
			$('#first_name_v').text(data['member_data'][0]['first_name']);
			$('#last_name_v').text(data['member_data'][0]['last_name']);
			$('#address_1_v').text(data['member_data'][0]['address_1']);
			$('#address_2_v').text(data['member_data'][0]['address_2']);
			$('#street_v').text(data['member_data'][0]['street']);
			$('#postal_code_v').text(data['member_data'][0]['postal_code']);
			$('#phone_no_v').text(data['member_data'][0]['phone_no']);
			$('#dob_v').text(data['member_data'][0]['dob']);
			$('#country_v').text(data['member_data'][0]['country']);
			$('#ville_v').text(data['member_data'][0]['ville']);
			$('#label-photo-view').text(member_change_pic_txt); // label photo upload
			$('#photo-preview-view div').html('<img src="'+data['member']['file_web_path']+'" class="img-rounded img-thumbnail" height=100 width=100>'); // show photo
			$('#modal-view-member').modal('show'); // show bootstrap modal when complete loaded
			
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}







