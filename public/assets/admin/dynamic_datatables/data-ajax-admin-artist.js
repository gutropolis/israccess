var DatatableRemoteAjaxDemoCat = {
	
    init: function() {
        //var t;
        t = $(".m_datatable_art").mDatatable({
            data: {
                type: "remote",
                source: {
                    read: {
                        url: "getAjaxArtistsList",
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
                field: "user_picture",
                title: Profile_Picture,
                filterable: !1,
                width: 200,
                template: "<img src='{{user_picture}}' height=100 width=100 class='img-responsive img-circle' >"
            },  {
                field: "name",
                title: Artist_Name,
                filterable: !1,
                width: 150,
                template: "{{name}}"
            },
			{
                field: "username",
                title: Username,
                filterable: !1,
                width: 200,
                template: "{{username}}"
            },
			{
                field: "email",
                title: Artist_Email,
                filterable: !1,
                width: 150,
                template: "{{email}}"
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
                    return '\t\t\t\t\t\t<a href="javascript:void(0);" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="'+EDIT+' '+ artist_name+'" onclick="edit('+[t.id]+')">\t\t\t\t\t\t\t<i class="la la-edit"></i>\t\t\t\t\t\t</a>\t\t\t\t\t\t<a href="javascript:void(0);" onclick="delete_function('+[t.id]+')" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" title="'+DELETE+' '+ artist_name+'">\t\t\t\t\t\t\t<i class="la la-trash"></i>\t\t\t\t\t\t</a>\t\t\t\t\t'
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
	console.log(base_url_const_demo);
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
function addArtist(){
	var div_id_or_class  = '#mesg_div';
	var email_add = $("#artist_email").val();
	if($("#artist_name").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html(artist_name_msg_txt);
		hideErrorDiv(div_id_or_class);
		$("#artist_name").focus();
		return false;
	}
    
	if($("#artist_username").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html(artist_username_msg_txt);
		hideErrorDiv(div_id_or_class);
		$("#artist_username").focus();
		return false;
	}
	
	if($("#artist_email").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html(artist_email_msg_txt);
		hideErrorDiv(div_id_or_class);
		$("#artist_email").focus();
		return false;
	}
	
	

	
	var url;
    url = "saveArtist";
   var formData = new FormData($('#form_add_artist')[0]);
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
				$('#modal-add-artist').modal('hide');
				$('#form_add_artist')[0].reset();
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
		url : "artists/delete/"+$('#id').val(),
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
   $('.del_title').html(DELETE+' '+artist_name);
   $('.del_text').html(common_delete_confirm_msg_txt+' '+artist_name+'?');	
   $('#id').val(id);
   $('#modal-delete').modal('show');
}

function edit(id)
{   
    $('#form_add_artist_e')[0].reset(); // reset form on modals
   

    //Ajax Load data from ajax
    $.ajax({
        url : "artists/edit/" + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
			
            $('#id_e').val(data['id']);
			$('#artist_name_e').val(data['name']);
			$('#artist_username_e').val(data['username']);
			$('#artist_email_e').val(data['email']);
			$('#status_e').val(data['status']).prop('selected', true);
			$('#user_picture_old').val(data['user_picture']);
			var user_picture = data['user_picture'];
			if(user_picture == null)
            {
				
                $('#label-photo-edit').text(admin_pic_label_txt); // label photo upload
                $('#photo-preview-edit div').html('<img src="'+data['file_web_path']+'" class="img-rounded img-thumbnail" height=100 width=100>'); // show photo
                
            }else if(user_picture != null)
            {
                $('#label-photo-edit').text(admin_pic_label_txt); // label photo upload
                $('#photo-preview-edit div').html('<img src="'+data['file_web_path']+'/thumbs/'+user_picture+'" class="img-rounded img-thumbnail" height=100 width=100>'); // show photo
                
            }
            else
            {
                //$('#label-photo').text('Upload Photo'); // label photo upload
                $('#photo-preview div').text(common_no_photo_txt);
            }
			$('#modal-edit-artist').modal('show'); // show bootstrap modal when complete loaded
			
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

// Update
function updateArtist(){
	var email_add = $("#artist_email_e").val();
	var div_id_or_class  = '#mesg_div_e';
	if($("#artist_name_e").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html(artist_username_msg_txt);
		hideErrorDiv(div_id_or_class);
		$("#artist_name_e").focus();
		return false;
	}
    
	if($("#artist_username_e").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html(artist_username_msg_txt);
		hideErrorDiv(div_id_or_class);
		$("#artist_username_e").focus();
		return false;
	}
	
	if($("#artist_email_e").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html(artist_email_msg_txt);
		hideErrorDiv(div_id_or_class);
		$("#artist_email_e").focus();
		return false;
	}
	
	
	var url;
    url = "artists/update";
   var formData = new FormData($('#form_add_artist_e')[0]);
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
				$('#modal-edit-artist').modal('hide');
				$('#form_add_artist_e')[0].reset();
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





