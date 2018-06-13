  $(document).ready(function() {

  $('#myform').validate({ 
       rules: {


		   firstname: {
      required: true
      
    },
	 lastname: {
      required: true
      
    },
	 address: {
      required: true
      
    },
	   
    email: {
      required: true,
      email: true
    },
	 telephon: {
      required: true
	 
      
    },
	message: {
      required: true
	 
      
    },
           person_name: {
               required: true

           },

           password: {
               required: true

           },
           password_confirm : {
               required: true,
               equalTo : "#password"
           }
  },
  messages: {
	  firstname:
	  {
		  required: "Champs requis", 
	  },
      lastname:
	  {
		  required: "Champs requis", 
	  },
	  address:
	  {
		  required: "Champs requis", 
	  },
	  
    email: {
      required: "Entrer votre Email",
      email: "Entrez une adresse email valide"
    },
	telephon:
	  {
		  required: "Champs requis", 
	  },
	  message:
	  {
		  required: "Champs requis", 
	  },
      person_name:
          {
              required: "Champs requis",
          },
      password:
          {
              required: "Champs requis",
          },
      password_confirm:
          {
              required: "confirm password  is required",
              equalTo: "password  not match",
          },
       }
    });





  });
