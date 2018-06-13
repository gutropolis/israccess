var DatatableRemoteAjaxDemoCat = {
	
    init: function() {
       // var t;
        t = $(".m_datatable_cat").mDatatable({
            data: {
                type: "remote",
                source: {
                    read: {
                        url: "getAjaxCategoriesList",
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
                title: Category_Name,
                filterable: !1,
                width: 150,
                template: "{{name}}"
            }, {
                field: "slug",
                title: Category_Slug,
                filterable: !1,
                width: 150,
                template: "{{slug}}"
            },
			{
                field: "picto_file",
                title: Category_Picture,
                filterable: !1,
                width: 200,
                template: "<img src='{{picto_file}}' height=100 width=100 class='img-responsive img-circle' >"
            },{
                field: "is_for_home",
                title: For_Home,
                filterable: !1,
                width: 100,
                template: "{{is_for_home}}"
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
                    return '\t\t\t\t\t\t<a href="javascript:void(0);" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="'+EDIT+' '+ category_txt+'" onclick="edit('+[t.id]+')">\t\t\t\t\t\t\t<i class="la la-edit"></i>\t\t\t\t\t\t</a>\t\t\t\t\t\t<a href="javascript:void(0);" onclick="delete_function('+[t.id]+')" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" title="'+DELETE+' '+ category_txt+'">\t\t\t\t\t\t\t<i class="la la-trash"></i>\t\t\t\t\t\t</a>\t\t\t\t\t'
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
function addCategory(){
	var div_id_or_class  = '#mesg_div';
	if($("#category_name").val()=="")
	{
		$("#category_name").focus();
		$(div_id_or_class).show().addClass('alert alert-danger').html(category_name_msg_txt);
		hideErrorDiv(div_id_or_class);
		return false;
	}

	
	var url;
    url = "saveCategory";
   var formData = new FormData($('#form_add_cat')[0]);
   $.ajax({
	 url: url, 
	 type: "POST",
	data: formData,
	contentType: false,
	processData: false,
	dataType: "JSON",
	success: function(data) {
		
		if(data.status == 'duplicate'){
			$('#mesg_div').show().addClass('alert alert-danger').html(data.message);
			hideErrorMsg()
		}else if(data.status == 'file_error'){
			$('#mesg_div').show().addClass('alert alert-danger').html(data.message);
			hideErrorMsg()
		}else if(data.status) {
				$('#modal-add-category').modal('hide');
				$('#form_add_cat')[0].reset();
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
    $('#form_add_cat_e')[0].reset(); // reset form on modals
    //Ajax Load data from ajax
    $.ajax({
        url : "categories/edit/" + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
			
            $('#id_e').val(data['id']);
			$('#category_name_e').val(data['name']);
			$('#slug_e').val(data['slug']);
			$('#category_logo_old').val(data['picto_file']);
			$('#status_e').val(data['status']).prop('selected', true);
			if(data['is_for_home'] == '1'){
			  $('#is_for_home_e').attr('checked',true);
			}else{
			  $('#is_for_home_e').attr('checked',false);	
			}
			$('#home_slider_title_e').val(data['home_slider_title']);
			var picto_file = data['picto_file'];
			if(picto_file != '')
            {
                $('#label-photo-edit').text(category_change_pic_txt); // label photo upload
                $('#photo-preview-edit div').html('<img src="'+data['file_web_path']+'/'+data['picto_file']+'" class="img-rounded img-thumbnail" height=100 width=100>'); // show photo
                
            }
            else
            {
                //$('#label-photo').text('Upload Photo'); // label photo upload
                $('#photo-preview div').text(common_no_photo_txt);
            }
			
			$('#modal-edit-category').modal('show'); // show bootstrap modal when complete loaded
			
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

// Update
function updateCategory(){
	var div_id_or_class  = '#mesg_div_e';
	if($("#category_name_e").val()=="")
	{
		$("#category_name_e").focus();
		$(div_id_or_class).show().addClass('alert alert-danger').html(category_name_msg_txt);
		hideErrorDiv(div_id_or_class);
		return false;
	}
	
	
	
	var url;
     url = "categories/update";
   var formData = new FormData($('#form_add_cat_e')[0]);
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
				$('#modal-edit-category').modal('hide');
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
		url : "categories/delete/"+id,
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
    $('.del_title').html(DELETE+' '+ category_txt);
   $('.del_text').html(common_delete_confirm_msg_txt+' '+category_txt+'?');		
   $('#id').val(id);
   $('#modal-delete').modal('show');
}







