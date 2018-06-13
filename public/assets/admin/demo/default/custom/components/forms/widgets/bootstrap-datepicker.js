var BootstrapDatepicker={init:function(){$(".m_datepicker_modal,.datepicker_class, .datepicker_event").datepicker({
	todayHighlight:!1,
	orientation:"bottom left",
	format: 'dd/mm/yyyy',
	autoclose:!0,
	templates:{leftArrow:'<i class="la la-angle-left"></i>',rightArrow:'<i class="la la-angle-right"></i>'}}),$("#m_datepicker_1, #m_datepicker_1_validate").datepicker({todayHighlight:!0,orientation:"bottom left",templates:{leftArrow:'<i class="la la-angle-left"></i>',rightArrow:'<i class="la la-angle-right"></i>'}}),
$("#m_datepicker_1_modal").datepicker({todayHighlight:!0,orientation:"bottom left",templates:{leftArrow:'<i class="la la-angle-left"></i>',rightArrow:'<i class="la la-angle-right"></i>'}}),$("#m_datepicker_2, #m_datepicker_2_validate").datepicker({todayHighlight:!0,orientation:"bottom left",templates:{leftArrow:'<i class="la la-angle-left"></i>',rightArrow:'<i class="la la-angle-right"></i>'}}),$("#m_datepicker_2_modal").datepicker({todayHighlight:!0,orientation:"bottom left",templates:{leftArrow:'<i class="la la-angle-left"></i>',rightArrow:'<i class="la la-angle-right"></i>'}}),$("#m_datepicker_3, #m_datepicker_3_validate").datepicker({todayBtn:"linked",clearBtn:!0,todayHighlight:!0,templates:{leftArrow:'<i class="la la-angle-left"></i>',rightArrow:'<i class="la la-angle-right"></i>'}}),$("#m_datepicker_3_modal").datepicker({todayBtn:"linked",clearBtn:!0,todayHighlight:!0,templates:{leftArrow:'<i class="la la-angle-left"></i>',rightArrow:'<i class="la la-angle-right"></i>'}}),$("#m_datepicker_4_1").datepicker({orientation:"top left",todayHighlight:!0,templates:{leftArrow:'<i class="la la-angle-left"></i>',rightArrow:'<i class="la la-angle-right"></i>'}}),$("#m_datepicker_4_2").datepicker({orientation:"top right",todayHighlight:!0,templates:{leftArrow:'<i class="la la-angle-left"></i>',rightArrow:'<i class="la la-angle-right"></i>'}}),$("#m_datepicker_4_3").datepicker({orientation:"bottom left",todayHighlight:!0,templates:{leftArrow:'<i class="la la-angle-left"></i>',rightArrow:'<i class="la la-angle-right"></i>'}}),$("#m_datepicker_4_4").datepicker({orientation:"bottom right",todayHighlight:!0,templates:{leftArrow:'<i class="la la-angle-left"></i>',rightArrow:'<i class="la la-angle-right"></i>'}}),$("#m_datepicker_5").datepicker({todayHighlight:!0,templates:{leftArrow:'<i class="la la-angle-left"></i>',rightArrow:'<i class="la la-angle-right"></i>'}}),$("#m_datepicker_6").datepicker({todayHighlight:!0,templates:{leftArrow:'<i class="la la-angle-left"></i>',rightArrow:'<i class="la la-angle-right"></i>'}})
	
	}};
	jQuery(document).ready(function(){	
	dates_function();
	    disable_past_dates();
		BootstrapDatepicker.init()});

function disable_past_dates(){
  var date = new Date();
date.setDate(date.getDate());

$('.m_datepicker_modal,.datepicker_class, .datepicker_event').datepicker({ 
    startDate: date,
	autoclose: true,
	todayHighlight:true
});

/**
var date = new Date();
date.setDate(date.getDate()-1);

$('#date').datepicker({ 
    startDate: date
});
**/	
}

function dates_function(){

$(".m_datepicker_modal,.datepicker_class, .datepicker_event").datepicker({
	orientation:"bottom left",
	format: 'dd/mm/yyyy',
	autoclose:!0
	});
};	