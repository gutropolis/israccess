var DatatableRemoteAjaxDemoCat = {
	
    init: function() {
       // var t;
        t = $(".m_events_of_day").mDatatable({
            data: {
                type: "remote",
                source: {
                    read: {
                        url: "getAjaxEventOfDayList",
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
                width: 100,
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
                field: "group_name",
                title: "Group Name",
                filterable: !0,
                width: 100,
                template: "{{group_name}}"
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
                width: 80,
                title: "Actions",
                sortable: !1,
                overflow: "visible",
                template: function(t, e, a) {
					
                    return '\t\t\t\t\t\t<a href="javascript:void(0);" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="View Event" onclick="view('+[t.id]+')" >\t\t\t\t\t\t\t<i class="la la-eye"></i>\t\t\t\t\t\t</a>\t\t\t\t\t'
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


// Remove Event picture
function remove_pic(id){
	var info = 'id=' + id;
	if(confirm("Are you sure, you remove this picture?"))
	{
	$.ajax({
	type: "GET",
	url: "./events/deleteEventPic/"+id,
	data: info,
	success: function(data){
		$('.hide_li_'+id).remove().slideUp('slow'); 
	}
	});
	
	}
	
}

function view(id)
{   

     //Ajax Load data from ajax
    $.ajax({
        url : "./events/get/" + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
			var status = data['status'];
			/*if(status == 'Active'){
				 var sts = '<span class="m-badge m-badge--success m-badge--wide">Active</span>';	
			}else{
			  var sts = '<span class="m-badge m-badge--danger m-badge--wide">Inactive</span>';	
			}*/
            $('#event_title_e').text(data['title']);
			$('#section_e').text(data['section']);
			$('#date_e').text(data['date_e']);
			$('#status_e').text(status);
			$('#city_e').text(data['city_name']);
			$('#auditorium_e').text(data['auditorium_name']);
			$('#artist_name_vev').text(data['artist_name']);
			$('#author_name_vev').text(data['author_name']);
			$('#productor_name_vev').text(data['productor_name']);
			$('#director_name_vev').text(data['director_name']);
			$('#evevt_description_e').text(data['description']);
			$('#contributor_name_e').text(data['contributor_name']); // Contributor Name
			$('#contributor_description_e').text(data['contributor_description']); // Contributor description
			$('ul.pic-list').html(data['pics']);
			$('#modal-view-event').modal('show'); // show bootstrap modal when complete loaded
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}


function edit(id){
    window.location.href='events/groups/edit/'+id;	
}









