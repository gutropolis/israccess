
 function FormLogin(){
	 	  $("#login_result").html('<img src="/assets/assets/img/ajax-loader.gif" />');
	 if (!$("#myform").validate().form()) {
	 
		 
	 }
	 else{

	   var email = $("input[name=email]").val();
      var password = $("input[name=password]").val();
	 

	    $.ajax({
				type: "get",
				url: 'login',
				data: { email:email, password:password }, 
				success: function( data ) {
					//var obj = jQuery.parseJSON( data);
					 var obj = data;
					console.log(obj.status);console.log(obj.msg);
					if(obj.status === '1'){
						$("#login_result").html('<div class="alert alert-info" role="alert"> '+obj.msg+'.</div>');
						
						
					}else{
						$("#login_result").html('<div class="alert alert-danger" role="alert"> '+obj.msg+'.</div>');
					}
					
					return false;
				}
		});
		return false;
	 }
   }

   function FullRegForm(){
	 if (!$("#registerform").validate().form()) {

	 }
	 else{
	     //alert('Welcome to full registration');
         var mydata = $('#registerform').serialize();
        // alert(mydata);
	    $.ajax({
        type: "get",
        url: 'register',
        data:mydata ,
        success: function( data ) {
					//var obj = jQuery.parseJSON( data);
					var obj = data; 
					console.log(obj.status);console.log(obj.msg);
					if(obj.status === '1'){
						$("#reg_result").html('<div class="alert alert-info" role="alert"> '+obj.msg+'.</div>');
						
						
					}else{
						$("#reg_result").html('<div class="alert alert-danger" role="alert"> '+obj.msg+'.</div>');
					}
					
					return false;
				}
		});
		return false;
	 }
   }
   
   function HalfRegForm(){
	  
	 if (!$("#halfregisterform").validate().form()) {
	 

	 }
	 else{

	   var firstname =$("input[name=firstname]").val();
      var lastname = $("input[name=lastname]").val();
	  var mymail = $("input[name=mymail]").val();
      var telehone = $("input[name=telephon]").val();
	 // alert(mymail);
	    $.ajax({
        type: "get",
        url: 'addhalf-user',
        data: {firstname:firstname,lastname:lastname, email:mymail},
        success: function( msg ) {
            alert( msg );
        }
		});
		
	 }
   }
 
    
   function FormChangeEmail(){
	 	  
	 if (!$("#myform").validate().form()) {
	 
		 
	 }
	 else{

	   var mymail = $("#mymail").val();
      var nymail = $("#nymail").val();
	 

	    $.ajax({
				type: "post",
				url: 'updateemail',
				data: { mymail:mymail, nymail:nymail }, 
				success: function( data ) {
					//var obj = jQuery.parseJSON( data);
					 var obj = data;
					console.log(obj.status);console.log(obj.msg);
					if(obj.status === '1'){
						$("#e_result").html('<div class="alert alert-info" role="alert"> '+obj.msg+'.</div>');
						
						
					}else{
						$("#e_result").html('<div class="alert alert-danger" role="alert"> '+obj.msg+'.</div>');
					}
					
					return false;
				}
		});
		return false;
	 }
   }
   
	function FormChangePassword(){
	 	  
	 if (!$("#changepass").validate().form()) {
	 
		 
	 }
	 else{

		var mypass = $("#mypass").val();
		var nypass = $("#nypass").val();
	 

	    $.ajax({
				type: "post",
				url: 'updatePassword',
				data: { mypass:mypass, nypass:nypass }, 
				success: function( data ) {
					//var obj = jQuery.parseJSON( data);
					 var obj = data;
					console.log(obj.status);console.log(obj.msg);
					if(obj.status === '1'){
						$("#p_result").html('<div class="alert alert-info" role="alert"> '+obj.msg+'.</div>');
						
						
					}else{
						$("#p_result").html('<div class="alert alert-danger" role="alert"> '+obj.msg+'.</div>');
					}
					
					return false;
				}
		});
		return false;
	 }
   }	
 
   function EditForm(){
		 if (!$("#registerform").validate().form()) {
 
		 }
		 else{
			 //alert('Welcome to full registration');
			 var mydata = $('#registerform').serialize();
			  var hdnurl = $("#hdnurl").val();
			  
			// alert(mydata);
			$.ajax({
			type: "post",
			url: hdnurl+'/mon-compte/edituser',
			data:mydata ,
			success: function( data ) {
						//var obj = jQuery.parseJSON( data);
						var obj = data; 
						console.log(obj.status);console.log(obj.msg);
						if(obj.status === '1'){
							$("#reg_result").html('<div class="alert alert-info" role="alert"> '+obj.msg+'.</div>');
							
							
						}else{
							$("#reg_result").html('<div class="alert alert-danger" role="alert"> '+obj.msg+'.</div>');
						}
						
						return false;
					}
			});
			return false;
		 }
		 return false;
   }
      

	  
 function FormLoginOrder(){
	 	  $("#login_result").html('<img src="/assets/assets/img/ajax-loader.gif" />');
	 if (!$("#myform").validate().form()) {
	 
		 
	 }
	 else{

	   var email = $("input[name=email]").val();
      var password = $("input[name=password]").val();
	  var redURL = $("#redUrlLogin").val();
	 

	    $.ajax({
				type: "get",
				url: 'loginorder',
				data: { email:email, password:password }, 
				success: function( data ) {
					//var obj = jQuery.parseJSON( data);
					 var obj = data;
					console.log(obj.status);console.log(obj.msg);
					if(obj.status === '1'){
						$("#login_result").html('<div class="alert alert-info" role="alert"> '+obj.msg+'.</div>');
						window.location.href = redURL;
						redURL
						
						
					}else{
						$("#login_result").html('<div class="alert alert-danger" role="alert"> '+obj.msg+'.</div>');
					}
					
					return false;
				}
		});
		return false;
	 }
   }

   function FullRegFormOrder(){
	 if (!$("#registerform").validate().form()) {

	 }
	 else{
	     //alert('Welcome to full registration');
         var mydata = $('#registerform').serialize();
		 var redURL = $("#redUrlRegister").val();
        // alert(mydata);
	    $.ajax({
        type: "get",
        url: 'registerorder',
        data:mydata ,
        success: function( data ) {
					//var obj = jQuery.parseJSON( data);
					var obj = data; 
					console.log(obj.status);console.log(obj.msg);
					if(obj.status === '1'){
						$("#reg_result").html('<div class="alert alert-info" role="alert"> '+obj.msg+'.</div>');
						window.location.href = redURL;
						
					}else{
						$("#reg_result").html('<div class="alert alert-danger" role="alert"> '+obj.msg+'.</div>');
					}
					
					return false;
				}
		});
		return false;
	 }
   }
   