var DatatableRemoteAjaxDemoCat = {
	
    init: function() {
        //var t;
        t = $(".m_datatable_cit").mDatatable({
            data: {
                type: "remote",
                source: {
                    read: {
                        url: "getAjaxCitiesList",
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
                width: 40,
                selector: !1,
				textAlign:"center",
                selector:{
					id : "check_box_class",
					class:"m-checkbox--solid m-checkbox--brand"}
			}, 
			{
                field: "name",
                title: CITY_NAME_TXT,
                filterable: !1,
                width: 150,
                template: "{{name}}"
            },
			{
                field: "zipcode",
                title: ZIP_CODE_TXT,
                filterable: !1,
                width: 200,
                template: "{{zipcode}}"
            },
			 {
                field: "status",
                title: STATUS_TXT,
                template: function(t) {
                    var e = {
                        0: {
                            title: INACTIVE_STATUS,
                            class: "m-badge--danger"
                        },
                        1: {
                            title: ACTIVE_STATUS,
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
                    return '\t\t\t\t\t\t<a href="javascript:void(0);" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="'+EDIT_TXT +' '+ CITY_NAME +'" onclick="edit('+[t.id]+')">\t\t\t\t\t\t\t<i class="la la-edit"></i>\t\t\t\t\t\t</a>\t\t\t\t\t\t<a href="javascript:void(0);" onclick="delete_function('+[t.id]+')" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" title="'+DELETE_TXT +' '+ CITY_NAME +'">\t\t\t\t\t\t\t<i class="la la-trash"></i>\t\t\t\t\t\t</a>\t\t\t\t\t'
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
function addCity(){
	var div_id_or_class  = '#mesg_div';
	
	/*if($("#state_name").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html(STATE_MSG);
		hideErrorDiv(div_id_or_class);
		$("#state_name").focus();
		return false;
	}*/
	
	if($("#city_name").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html(CITY_MSG);
		hideErrorDiv(div_id_or_class);
		$("#city_name").focus();
		return false;
	}
	
	if($("#zip_code").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html(ZIP_MSG);
		hideErrorDiv(div_id_or_class);
		$("#zip_code").focus();
		return false;
	}

	
	var url;
    url = "saveCity";
   var formData = new FormData($('#form_add_city')[0]);
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
			
				$('#modal-add-city').modal('hide');
				$('#form_add_city')[0].reset();
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
    $('#form_add_city_e')[0].reset(); // reset form on modals
    //Ajax Load data from ajax
    $.ajax({
        url : "cities/edit/" + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
            $('#id_e').val(data['id']);
			$('#city_name_e').val(data['name']);
			//$('#state_name_e').val(data['state']);
			$('#zip_code_e').val(data['zipcode']);
			$('#status_e').val(data['status']).prop('selected', true);
			$('#modal-edit-city').modal('show'); // show bootstrap modal when complete loaded
			
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

// Update
function updateCity(){
	var div_id_or_class  = '#mesg_div_e';
	
	/*if($("#state_name_e").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html(STATE_MSG);
		hideErrorDiv(div_id_or_class);
		$("#state_name_e").focus();
		return false;
	}*/
	
	if($("#city_name_e").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html(CITY_MSG);
		hideErrorDiv(div_id_or_class);
		$("#city_name_e").focus();
		return false;
	}
	
	if($("#zip_code_e").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html(ZIP_MSG);
		hideErrorDiv(div_id_or_class);
		$("#zip_code_e").focus();
		return false;
	}
	
	
	var url;
     url = "cities/update";
   var formData = new FormData($('#form_add_city_e')[0]);
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
				$('#modal-edit-city').modal('hide');
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
		url : "cities/delete/"+id,
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
   $('.del_title').html(DELETE +' '+CITY_NAME);
   $('.del_text').html(common_delete_confirm_msg_txt+' '+ CITY_NAME +'?');	
   $('#id').val(id);
   $('#modal-delete').modal('show');
}

$(document).ready(function(){
	$(".state_name").keyup(function(){
		$.ajax({
		type: "POST",
		url: "getStates",
		data:'search_keyword='+$(this).val()+'&mode=add',
		beforeSend: function(){
			$(".state_name").css("background","#FFF url('') no-repeat 165px");
		},
		success: function(data){
			$(".suggesstion-box-add").show();
			$(".suggesstion-box-add").html(data);
			$(".state_name").css("background","#FFF");
		}
		});
	});
	
	$(".state_name_e").keyup(function(){
		$.ajax({
		type: "POST",
		url: "getStates",
		data:'search_keyword='+$(this).val()+'&mode=edit',
		beforeSend: function(){
			$(".state_name_e").css("background","#FFF url('') no-repeat 165px");
		},
		success: function(data){
			$(".suggesstion-box-edit").show();
			$(".suggesstion-box-edit").html(data);
			$(".state_name_e").css("background","#FFF");
		}
		});
	});
});

//To select country name
function selectCountry(val) {
	$(".state_name").val(val);
	$(".suggesstion-box-add").hide();
}

function selectCountryE(val) {
	$(".state_name_e").val(val);
	$(".suggesstion-box-edit").hide();
}









