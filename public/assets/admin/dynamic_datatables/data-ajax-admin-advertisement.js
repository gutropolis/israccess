
var DatatableRemoteAjaxDemoCat = {
	
    init: function() {
        //var t;
        t = $(".m_datatable_adv").mDatatable({
            data: {
                type: "remote",
                source: {
                    read: {
                        url: "getAjaxAdsList",
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
            }, {
                field: "title",
                title: "Title",
                filterable: !1,
                width: 150,
                template: "{{title}}"
            },
			{
                field: "add_picture",
                title: "Ad Picture",
                filterable: !1,
                width: 150,
                template: "<img src='{{add_picture}}' height=100 width=100 class='img-responsive img-circle' >"
            },
			{
                field: "redirect_link",
                title: Redirect_Link,
                filterable: !1,
                width: 150,
                template: "{{redirect_link}}"
            },
			/*{
                field: "display_order",
                title: section_display_order_txt,
                filterable: !1,
                width: 150,
                template: "{{display_order}}"
            },*/
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
                    return '\t\t\t\t\t\t<a href="javascript:void(0);" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="'+EDIT+'  Advertisement" onclick="edit('+[t.id]+')">\t\t\t\t\t\t\t<i class="la la-edit"></i>\t\t\t\t\t\t</a>\t\t\t\t\t\t<a href="javascript:void(0);" onclick="delete_function('+[t.id]+')" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" title="'+DELETE+' Advertisement">\t\t\t\t\t\t\t<i class="la la-trash"></i>\t\t\t\t\t\t</a>\t\t\t\t\t'
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
	if($("#title").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html("Please enter advertisement title");
		hideErrorDiv(div_id_or_class);
		$("#title").focus();
		return false;
	}
	
	if($("#ad_picture").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html("Please select advertisement picture");
		hideErrorDiv(div_id_or_class);
		$("#ad_picture").focus();
		return false;
	}

	
	var url;
    url = "saveAdd";
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
        url : "advertisements/edit/" + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
			
            $('#id_e').val(data['id']);
			$('#title_e').val(data['title']);
			$('#redirect_link_e').val(data['redirect_link']);
			$('#ad_picture_old').val(data['ad_picture']);
			$('#status_e').val(data['status']).prop('selected', true);
			var add_picture = data['ad_picture'];
			if(add_picture != '')
            {
                $('#label-photo-edit').text(slider_change_pic_txt); // label photo upload
                $('#photo-preview-edit div').html('<img src="'+data['file_web_path']+'/'+add_picture+'" class="img-rounded img-thumbnail" height=100 width=100>'); // show photo
                
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
	if($("#title_e").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html("Please enter advertisement title");
		hideErrorDiv(div_id_or_class);
		$("#title_e").focus();
		return false;
	}
	
	if($("#ad_picture_old").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html("Please upload advertisement picture");
		hideErrorDiv(div_id_or_class);
		$("#ad_picture_old").focus();
		return false;
	}

	
	
	
	var url;
     url = "advertisements/update";
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
		url : "advertisements/delete/"+id,
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
   $('.del_title').html(DELETE+' Advertisement');
   $('.del_text').html(common_delete_confirm_msg_txt+' Advertisement?');	
   $('#id').val(id);
   $('#modal-delete').modal('show');
}





