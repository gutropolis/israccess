$(document).ready(function() {
	$('#uform').validate({ 
       rules: {
	address: {
      required: true
      
    },
	street: {
      required: true
      
    },
	addressline: {
      required: true
      
    },
	postalcode: {
      required: true
      
    },
	country: {
      required: true
      
    },
	phoneno: {
      required: true
      
    },
	
	email: {
      required: true,
	  email:true
      
    }
	
	
  },
  messages: {
	  
    address: {
      required: "Address is required",
    },
	street: {
      required: "Street is required",
      
    }
	,
	addressline: {
      required: "this field is required",
      
    },
	postalcode: {
      required: "postal-code is required",
      
    },
	phoneno: {
      required: "Phone no is required",
      
    },
	country:{
      required: "Country is required",
      
    },
	email: {
      required: "email is required",
	  email:" invalid email "
      
    }
       }
    });
	
	});