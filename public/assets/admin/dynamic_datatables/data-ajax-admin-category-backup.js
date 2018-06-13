var DatatableRemoteAjaxDemoCat = {
	
    init: function() {
        //var t;
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
				saveState: {cookie: false},
                pageSize: 10,
                serverPaging: false,
				serverFiltering: false,
				serverSorting: false
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
                textAlign: "center",
				 selector:{
					id : "check_box_class",
					class:"m-checkbox--solid m-checkbox--brand"}
            }, {
                field: "name",
                title: "Category Name",
                filterable: !1,
                width: 150,
                template: "{{name}}"
            },
			{
                field: "picto_file",
                title: "Category Picture",
                filterable: !1,
                width: 200,
                template: "<img src='{{picto_file}}' height=100 width=100 >"
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
                    return '\t\t\t\t\t\t<div class="dropdown ' + (a.getPageSize() - e <= 4 ? "dropup" : "") + '">\t\t\t\t\t\t\t<a href="#" class="btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="dropdown">                                <i class="la la-ellipsis-h"></i>                            </a>\t\t\t\t\t\t  \t<div class="dropdown-menu dropdown-menu-right">\t\t\t\t\t\t    \t<a class="dropdown-item" href="#"><i class="la la-edit"></i> Edit Details</a>\t\t\t\t\t\t    \t<a class="dropdown-item" href="#"><i class="la la-leaf"></i> Update Status</a>\t\t\t\t\t\t    \t<a class="dropdown-item" href="#"><i class="la la-print"></i> Generate Report</a>\t\t\t\t\t\t  \t</div>\t\t\t\t\t\t</div>\t\t\t\t\t\t<a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Edit details">\t\t\t\t\t\t\t<i class="la la-edit"></i>\t\t\t\t\t\t</a>\t\t\t\t\t\t<a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" title="Delete">\t\t\t\t\t\t\t<i class="la la-trash"></i>\t\t\t\t\t\t</a>\t\t\t\t\t'
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
	
	if($("#category_name").val()=="")
	{
		$('#mesg_div').show().addClass('alert alert-danger').html('Please enter category name.');
		hideErrorMsg();
		$("#category_name").focus();
		return false;
	}else{
		$("#category_name").removeClass(".form-control.focus, .form-control:focus");  
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
			    location.reload();	
		}
	}, 
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			console.log(XMLHttpRequest.toSource());
		}
	});
	return false;

}







