// Variable to hold request
var request;

// Bind to the submit event of our form
$("#admin_login").click(function(event){
		var postForm = $('#admin_login_frm').serialize();
        $.ajax({ //Process the form using $.ajax()
            type      : 'POST', //Method type
            url       : 'check_login', //Your form processing file URL
            data      : postForm, //Forms name
            dataType  : 'json',
            success   : function(data) {
			if (!data.status) { //If fails
				if (data.message) { //Returned if any error from process.php
					$('.log_msg').fadeIn(100).html(data.message); //Throw relevant error
				}
			}
			else {
					// Redirect to admin dashboard page
					window.location.href='dashboard';
				}
			}
        });
        event.preventDefault(); //Prevent the default submit	
});

