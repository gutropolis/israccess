<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="assets/assets/img/favicons/favicons.png">

    <title>Coming Soon</title>

    <!-- Bootstrap core CSS -->    
	<link href="coming-soon/css/bootstrap.css" rel="stylesheet">
    <link href="coming-soon/css/comming-soon.css" rel="stylesheet">
 
    <!-- Custom styles for this template -->
    <link href="https://fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i" rel="stylesheet"> 
    <link href="https://fonts.googleapis.com/css?family=Ubuntu:300,300i,400,400i,500,500i,700,700i" rel="stylesheet"> 
  </head>

  <body class="comming-soon">
  <div class="wrap">
  <section class="topSection">
  	<div class="topcontent">
    	<a href="#"><img src="coming-soon/assets/img/logo_beta.png" width="381" height="176"></a>
        <h1 class="text-pink">L’histoire commence <span>le JEUDI 31 mai  à 18h</span></h1>
        <h3>nous sommes impatients, ÉMUS... ET UN PEU fébriles...
<span>mais nous savons pouvoir compter sur votre soutien... Merci</span></h3>
    </div>
  </section>
  <section class="footSection">
  	<div class="footcontent">
		<div class="row">
   	    <div class="col-md-4">
			<figure><img src="coming-soon/assets/img/comedy.png"></figure>
		</div>
		
		 <div class="col-md-8">
			<h2>évènement</h2>
			<div class="grand-festival"><h3><span>Le Mercredi 30 Mai | 18h</span> <strong>Ouverture de la Billetterie</strong> Grand Festival d’humour Francophone</h3></div>
			<h3><span>Sur culturaccess.com </br>
			et de 18h à 22h au 0733-202-400</span> </h3>
			
			<ul class="socialLinks">
        	<li><a href="#"><img src="comingsoon/assets/img/facebook.png"></a></li>
        	<li><a href="#"><img src="comingsoon/assets/img/twitte.png"></a></li>
        	<li><a href="#"><img src="comingsoon/assets/img/instragram.png"></a></li>
        	<li><a href="#"><img src="comingsoon/assets/img/email.png"></a></li>
        </ul>
		 </div>
		</div>
		
        
        <!--div class="formcon">
			 <form method="post" action="" id="frmcomingsoon">
				<input   type="text" name="txtEmail" id="txtEmail" placeholder="votre e-mail">
				<input  id="btnValidate" name="btnValidate"  type="button" value="biensur" class="biensur">
			 </form>
			<//?php

   //print_r($_REQUEST);
			
			
			//if(isset($_POST['txtEmail'])){
                 //$email= $_POST['txtEmail'];
//$success='1';
                  //file = fopen("comingsoon/contact/contact.csv","a");
				  //textE= $email.",";
					//	fputcsv($file,explode(',',$textE));
					//fclose($file); 
					//if($success=='1'){
					//	echo '<span style="text-align:center;color: yellow;">Thank you for contact us. We will contact you shortly!!</span>';
					//	
					//}
			//}			//?>
        </div-->
        
    </div>
  </section>
  </div>
 
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
	 <script type = "text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script type = "text/javascript">
    function ValidateEmail(email) {
        var expr = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
        return expr.test(email);
    };
    $("#btnValidate").live("click", function () {
        if (!ValidateEmail($("#txtEmail").val())) {
            alert("Invalid email address.");
			
			
			 
        }
        else {
            //alert("Valid email address.");
			$("#frmcomingsoon").submit();
        }
    });
</script>
	
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script>window.jQuery || document.write('<script src="comingsoon/assets/js/vendor/jquery-slim.min.js"><\/script>')</script>
    <script src="comingsoon/assets/js/vendor/popper.min.js"></script>
    <script src="comingsoon/js/bootstrap.min.js"></script>
	

	
  </body>
</html>
