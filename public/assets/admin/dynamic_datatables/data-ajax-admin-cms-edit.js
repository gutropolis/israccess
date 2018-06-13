


// Update
function updateCms(){
	var div_id_or_class  = '#mesg_div_cms';
	if($("#title").val()=="")
	{
		//$("#title").focus();
		$(div_id_or_class).show().addClass('alert alert-danger').html('Please enter cms title.');
		hideErrorDiv(div_id_or_class);
		return false;
	}
	
	if($("#m_summernote_1").val()=="")
	{
		//$("#price_min").focus();
		$(div_id_or_class).show().addClass('alert alert-danger').html('Please enter cms description.');
		hideErrorDiv(div_id_or_class);
		return false;
	}
	
	
    
	var url;
    url = "../../cms/update";
   var formData = new FormData($('#edit_cms_from')[0]);
   $.ajax({
	 url: url, 
	 type: "POST",
	data: formData,
	contentType: false,
	processData: false,
	dataType: "JSON",
	success: function(data) {
		
		if(data.status == 'error'){
			$(div_id_or_class).show().addClass('alert alert-danger').html(data.message);
			hideErrorDiv(div_id_or_class);
		}else if(data.status == 'duplicate'){
			$(div_id_or_class).show().addClass('alert alert-danger').html(data.message);
			hideErrorDiv(div_id_or_class);
		}else if(data.status == 'file_error'){
			$(div_id_or_class).show().addClass('alert alert-danger').html(data.message);
			hideErrorDiv(div_id_or_class);
		}else if(data.status) {
			    //location.reload();	
				window.location.href='../../cms';
		}
	}, 
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			console.log(XMLHttpRequest.toSource());
		}
	});
	return false;

}









