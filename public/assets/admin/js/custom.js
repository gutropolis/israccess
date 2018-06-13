var HOSTINGURL = 'http://localhost/slim_project/public/';
//var HOSTINGURL = 'http://israel-access.com/';

//alert('DDD');return false;

function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i);
    return pattern.test(emailAddress);
}

$(function() {
	$("#fullname").focus(function() {
		$("#fullname-error").html("");
	});
	$("#email").focus(function() {
		$("#email-error").html("");
	});
	$("#password").focus(function() {
		$("#password-error").html("");
	});
	$("#rpassword").focus(function() {
		$("#rpassword-error").html("");
	});
	
	
	$('#signup_submit').on('click',function () {
		
		var fullname  = $.trim($("#fullname").val());
		var email 	  = $.trim($("#email").val());
		var password  = $.trim($("#password").val());
		var rpassword = $.trim($("#rpassword").val());
		var agree 	  = $.trim($("#agree").val());
		var errHas	  = false;
		
		if(!(fullname)) {
			$("#fullname-error").html('This field is required.');
			errHas=true;
		}
		if(!(email)) {
			$("#email-error").html('This field is required.');
			errHas=true;
		}else {
			if(!isValidEmailAddress(email)){
				$("#email-error").html("Email is not valid.");
				errHas=true;
			} else {
				
				$.ajax({
					url  	 : 'check_email_exist',
					type 	 : 'post',
					dataType : 'json',
					data	 : {'email' : email},
					success  : function(response){
						
						if (response.flag == true) {
							$("#email-error").html("Email is already exist.");
							errHas=true;
						}else{
						  alert('Errorrr');
						  return false;	
						}
					}
				});
				
			}
			
		}
		if(!(password)){
			
			$("#password-error").html('This field is required.');
			
			errHas=true;
			
		}
		if(!(rpassword)){
			
			$("#rpassword-error").html('This field is required.');
			
			
		}else{
			
			if(rpassword != password) {
				
				$("#rpassword-error").html('Password does not match.');
				
				errHas=true;
			}
			
		}
		if(!(agree)){
			$("#agree-error").html('This field is required.');
			errHas=true;
		}
		
		setTimeout(function(){
			
			if(errHas != true) {
				
				$.ajax({
					
					url  	 : 'saveuser',
					type 	 : 'post',
					dataType : 'json',
					data	 : {'fullname' : fullname,'email' : email,'password' : password},
					success  : function(response){
						
						if (response.flag == true) {
							$(".m-login__signup").hide();
							$(".m-login__signin").show();
							$(".m-login.m-login--1.m-login--signup .m-login__account").show();
						}
					}
					
				});
				
			}else{
				
				return false;
			}
			
			}, 2000);
		
		
		
	});
}); 