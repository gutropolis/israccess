
var DatatableRemoteAjaxDemoCat = {
	
    init: function() {
        //var t;
        t = $(".m_datatable_orders").mDatatable({
            data: {
                type: "remote",
                source: {
                    read: {
                        url: "getAjaxOrdersList",
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
            sortable: !1,
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
                field: "customer_name",
                title: "Customer Name",
                filterable: !0,
                width: 250,
                template: "{{customer_name}}"
            }, {
                field: "total_amount",
                title: "Total Amount",
                filterable: !1,
                width: 100,
                template: "{{total_amount}}"
            },
			{
                field: "payment_type",
                title: "Payment  Type",
                filterable: !1,
                width: 200,
                template: "{{payment_type}}"
            },
			{
                field: "created_on",
                title: "Date Ordered",
                filterable: !1,
                width: 150,
                template: "{{created_on}}"
            },
			
			   {
                field: "Actions",
                width: 80,
                title: "Actions",
                sortable: !1,
                overflow: "visible",
                template: function(t, e, a) {
                    return '\t\t\t\t\t\t<a href="javascript:void(0);" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="View Order" onclick="view('+[t.id]+')">\t\t\t\t\t\t\t<i class="la la-eye"></i>\t\t\t\t\t\t</a>\t\t\t\t\t\t<a style="display:none" href="javascript:void(0);" onclick="delete_function('+[t.id]+')" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" title="Delete Productor">\t\t\t\t\t\t\t<i class="la la-trash"></i>\t\t\t\t\t\t</a>\t\t\t\t\t'
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





function view(id)
{   
    window.location.href='./orders/view/'+id;
}







