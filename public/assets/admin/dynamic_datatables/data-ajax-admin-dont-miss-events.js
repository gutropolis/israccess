var DatatableRemoteAjaxDemoCat = {
	
    init: function() {
       // var t;
        t = $(".m_dont_miss_events").mDatatable({
            data: {
                type: "remote",
                source: {
                    read: {
                        url: "getAjaxEventDontMissList",
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
                width: 30,
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
                field: "date",
                title: "Date",
                filterable: !0,
                width: 150,
                template: "{{date}}"
            },
			{
                field: "city_name",
                title: "City",
                filterable: !1,
                width: 100,
                template: "{{city_name}}"
            },
			{
                field: "auditorium_name",
                title: "Auditorium",
                filterable: !0,
                width: 100,
                template: "{{auditorium_name}}"
            },
			{
                field: "status",
                title: "Status",
                template: function(t) {
                    var e = {
                        0: {
                            title: "Inactive",
                            class: "m-badge--danger",
							statusTitle : "Active"
                        },
                        1: {
                            title: "Active",
                            class: " m-badge--success",
							statusTitle : "Inactive"
                        }
                    };
                    return '<span class="m-badge ' + e[t.status].class + ' m-badge--wide">' + e[t.status].title + "</span>"
                }
            },
			{
                field: "Actions",
                width: 180,
                title: "Actions",
                sortable: !1,
                overflow: "visible",
                template: function(t, e, a) {
					
                    return '\t\t\t\t\t\t<a  href="javascript:void(0);" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Change Status" onclick="mark_status('+[t.id]+')" >\t\t\t\t\t\t\t<i class="la la-recycle"></i>\t\t\t\t\t\t</a>\t\t\t\t\t\t<a  href="javascript:void(0);" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Duplicate Event Group" onclick="duplicate_function('+[t.id]+')" >\t\t\t\t\t\t\t<i class="la la-copy"></i>\t\t\t\t\t\t</a>\t\t\t\t\t\t<a href="javascript:void(0);" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="View Event Group" onclick="view('+[t.id]+')" >\t\t\t\t\t\t\t<i class="la la-eye"></i>\t\t\t\t\t\t</a>\t\t\t\t\t\t<a href="javascript:void(0);"  class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Edit Event Group" onclick="edit('+[t.id]+')">\t\t\t\t\t\t\t<i class="la la-edit"></i>\t\t\t\t\t\t</a>\t\t\t\t\t\t<a href="javascript:void(0);" onclick="delete_function('+[t.id]+')" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" title="Delete Event Group">\t\t\t\t\t\t\t<i class="la la-trash"></i>\t\t\t\t\t\t</a>\t\t\t\t\t'
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









