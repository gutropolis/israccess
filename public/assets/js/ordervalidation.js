  $(document).ready(function() {

  $('#myform').validate({ 
       rules: {
	mymail: {
      required: true,
      email: true
    },
	nymail: {
      required: true,
      email: true
    },
	cymail: {
      required: true,
      equalTo : "#nymail"
    }
	
  },
  messages: {
	  
    mymail: {
      required: "Entrez votre email actuel",
      email: "email invalide"
    },
	nymail: {
      required: "Entrez votre nouvel email",
      email: "email invalide"
    }
	,
	cymail: {
      required: "Confirmez votre nouvel email",
      equalTo : "email ne correspond pas"
    }
       }
    });
	
	$('#changepass').validate({ 
       rules: {
	mypass: {
      required: true,
    },
	nypass: {
      required: true,
    },
	cypass: {
      required: true,
      equalTo : "#nypass"
    }
	
  },
  messages: {
	  
    mypass: {
      required: "Entrez votre mot passe actuel",
     
    },
	nypass: {
      required: "Entrez votre nouveau mot de passe",
      
    }
	,
	cypass: {
      required: "Confirmez votre nouveau mot de passe",
      equalTo : "Mot de passe ne correspond pas"
    }
       }
    })





  });
