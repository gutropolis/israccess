var DatatableRemoteAjaxDemoCat = {
	
    init: function() {
        //var t;
        t = $(".m_datatable_currency").mDatatable({
            data: {
                type: "remote",
                source: {
                    read: {
                        url: "getAjaxCurrenciesList",
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
                title: CURRENCY_NAME_TEXT,
                filterable: !1,
                width: 200,
                template: "{{name}}"
            },
			{
                field: "symbol",
                title: CURRENCY_SYMBOL_TXT,
                filterable: !1,
                width: 200,
                template: "{{symbol}}"
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
                    return '\t\t\t\t\t\t<a href="javascript:void(0);" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="'+EDIT +' '+CURRENCY_NAME+'" onclick="edit('+[t.id]+')">\t\t\t\t\t\t\t<i class="la la-edit"></i>\t\t\t\t\t\t</a>\t\t\t\t\t\t<a href="javascript:void(0);" onclick="delete_function('+[t.id]+')" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" title="'+DELETE +' '+ CURRENCY_NAME+'">\t\t\t\t\t\t\t<i class="la la-trash"></i>\t\t\t\t\t\t</a>\t\t\t\t\t'
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
function addCurrency(){
	var div_id_or_class  = '#mesg_div';
	
	if($("#name").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html(currency_name_msg_txt);
		hideErrorDiv(div_id_or_class);
		$("#name").focus();
		return false;
	}
	
	if($("#symbol").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html(currency_symbol_msg_txt);
		hideErrorDiv(div_id_or_class);
		$("#symbol").focus();
		return false;
	}
	
	
	var url;
    url = "saveCurrency";
   var formData = new FormData($('#form_add_currency')[0]);
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
			
				$('#modal-add-currency').modal('hide');
				$('#form_add_currency')[0].reset();
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
    $('#form_add_currency_e')[0].reset(); // reset form on modals
    //Ajax Load data from ajax
    $.ajax({
        url : "currencies/edit/" + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
            $('#id_e').val(data['id']);
			$('#name_e').val(data['name']);
			$('#symbol_e').val(data['symbol']);
			$('#status_e').val(data['status']).prop('selected', true);
			$('#modal-edit-currency').modal('show'); // show bootstrap modal when complete loaded
			
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

// Update
function updateCurrency(){
	var div_id_or_class  = '#mesg_div_e';
	
	if($("#name_e").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html(currency_name_msg_txt);
		hideErrorDiv(div_id_or_class);
		$("#name_e").focus();
		return false;
	}
	
	if($("#symbol_e").val()=="")
	{
		$(div_id_or_class).show().addClass('alert alert-danger').html(currency_symbol_msg_txt);
		hideErrorDiv(div_id_or_class);
		$("#symbol_e").focus();
		return false;
	}
	
	
	
	var url;
     url = "currencies/update";
   var formData = new FormData($('#form_add_currency_e')[0]);
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
				$('#modal-edit-currency').modal('hide');
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
		url : "currencies/delete/"+id,
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
   $('.del_title').html(DELETE+' '+CURRENCY_NAME);
   $('.del_text').html(common_delete_confirm_msg_txt+' '+CURRENCY_NAME +'?');	
   $('#id').val(id);
   $('#modal-delete').modal('show');
}








