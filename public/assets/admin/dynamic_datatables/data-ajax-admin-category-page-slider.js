
var DatatableRemoteAjaxDemoCat = {
	
    init: function() {
        //var t;
        t = $(".m_datatable_slider").mDatatable({
            data: {
                type: "remote",
                source: {
                    read: {
                        url: "getAjaxCategorySlidersList",
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
                width: 20,
                selector: !1,
                textAlign: "center",
				selector:{
					id : "check_box_class",
					class:"m-checkbox--solid m-checkbox--brand"}
            },
			{
                field: "slider_picture",
                title: Slider_Picture,
                filterable: !1,
                width: 150,
                template: "<img src='{{slider_picture}}' height=100 width=100 class='img-responsive img-circle' >"
            },
			{
                field: "redirect_link",
                title: Redirect_Link,
                filterable: !1,
                width: 150,
                template: "{{redirect_link}}"
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
                    return '\t\t\t\t\t\t<a href="javascript:void(0);" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="'+EDIT+' '+ slider_txt+'" onclick="edit('+[t.id]+')">\t\t\t\t\t\t\t<i class="la la-edit"></i>\t\t\t\t\t\t</a>\t\t\t\t\t\t<a href="javascript:void(0);" onclick="delete_function('+[t.id]+')" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" title="'+DELETE+' '+slider_txt+'">\t\t\t\t\t\t\t<i class="la la-trash"></i>\t\t\t\t\t\t</a>\t\t\t\t\t'
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

// Add
function addSlider(){
	var div_id_or_class  = '#mesg_div';
	
	
	if($("#slider_picture").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html(slider_pic_msg_txt);
		hideErrorDiv(div_id_or_class);
		$("#slider_picture").focus();
		return false;
	}

	
	var url;
    url = "saveCategorySlider";
   var formData = new FormData($('#form_add_slider')[0]);
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
				$('#modal-add-slider').modal('hide');
				$('#form_add_slider')[0].reset();
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
    $('#form_add_slider_e')[0].reset(); // reset form on modals
    //Ajax Load data from ajax
    $.ajax({
        url : "category_page_sliders/edit/" + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
			
            $('#id_e').val(data['id']);
			$('#redirect_link_e').val(data['redirect_link']);
			$('#slider_picture_old').val(data['slider_picture']);
			$('#status_e').val(data['status']).prop('selected', true);
			var slider_picture = data['slider_picture'];
			if(slider_picture != '')
            {
                $('#label-photo-edit').text(slider_change_pic_txt); // label photo upload
                $('#photo-preview-edit div').html('<img src="'+data['file_web_path']+'/'+slider_picture+'" class="img-rounded img-thumbnail" height=100 width=100>'); // show photo
                
            }
            else
            {
                //$('#label-photo').text('Upload Photo'); // label photo upload
                $('#photo-preview div').text('(No photo)');
            }
			
			$('#modal-edit-slider').modal('show'); // show bootstrap modal when complete loaded
			
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

// Update
function updateSlider(){
	var div_id_or_class  = '#mesg_div_e';
	
	
	if($("#slider_picture_old").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html(slider_pic_msg_txt);
		hideErrorDiv(div_id_or_class);
		$("#slider_picture_e").focus();
		return false;
	}

	
	
	
	var url;
     url = "category_page_sliders/update";
   var formData = new FormData($('#form_add_slider_e')[0]);
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
				$('#modal-edit-slider').modal('hide');
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
		url : "category_page_sliders/delete/"+id,
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
   $('.del_title').html(DELETE+' '+ 'Categy page slider');
   $('.del_text').html(common_delete_confirm_msg_txt+' '+' category page slider'+'?');	
   $('#id').val(id);
   $('#modal-delete').modal('show');
}





