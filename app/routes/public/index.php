<?php 
/*-------------------------------------------------------------------
|                  Public/Home Pages Routes  By Amin                 |
---------------------------------------------------------------------*/
$app->get("/", "App\Controllers\HomeController:showPopup");
$app->group('/', function () use ($app) {
  
  
	$app->get("home", "App\Controllers\HomeController:getHome");  // Home page

	$app->get("contactus", "App\Controllers\HomeController:getContactus");  // Contactus page
	$app->get("ajaxcallCommunity/{id}", "App\Controllers\HomeController:getCommunityFullDesc");  // Contactus page

	
	$app->get("category/{id}", "App\Controllers\CategoryController:getEventGroupsByCatID")->setName('category-events');  // Home page
	
	$app->get("eventgroup/{id}", "App\Controllers\EventgroupController:getEventDetailByID")->setName('eventgroupdetail');  // Home page
	
	$app->get("mysearch", "App\Controllers\EventgroupController:searchEventGroups");   // My Search page
	$app->post("mysearch", "App\Controllers\EventgroupController:searchEventGroups");   // My Search page
	
	
	//Booking Controllers//
    $app->post("booking", "App\Controllers\BookingController:getBooking")->setName('booking');  
	 $app->get("booking", "App\Controllers\BookingController:getBooking")->setName('booking'); 	 // Booking page  
	 
	$app->get("mon-panier", "App\Controllers\BookingController:addEventTicketCart"); 	 // Booking page
    $app->post("mon-panier", "App\Controllers\BookingController:addEventTicketCart"); 	
	
	$app->get("validate-cart", "App\Controllers\BookingController:getEventShop"); 
	$app->post("validate-cart", "App\Controllers\BookingController:getEventShop")->setName('confirmbooking'); 
 
		
	$app->get("confirmOrder", "App\Controllers\BookingController:addCartOrder"); 	 // Booking page 
	$app->post("confirmOrder", "App\Controllers\BookingController:addCartOrder"); 
	
	$app->get("paymentOrder", "App\Controllers\BookingController:getPayment"); 	 // Booking page 
	
    $app->get("make-payment", "App\Controllers\BookingController:confirmPayment"); 	 // Booking page 
	$app->get("ajaxcallRemoveItem/{grpid}", "App\Controllers\BookingController:ajaxcallRemoveItem");
	
	$app->get("cadeau", "App\Controllers\HomeController:checkOrder");   // Order page 	
	$app->get("testOrder/{order}/{user}", "App\Controllers\BookingController:testOrder");
  
   $app->get("ajaxcalleventgroup/{id}", "App\Controllers\EventgroupController:ajaxCalendarWithEvents")->setName('eventgroupdetail');
   $app->get("ajaxcalleventpopup", "App\Controllers\EventgroupController:ajxModelEventBody");
   $app->get("ajaxcallEventOrder", "App\Controllers\EventgroupController:ajaxcallEventOrder");
   $app->get("ajaxcallRawSeat/{rowno}", "App\Controllers\EventgroupController:ajaxcallRawSeat");
   $app->get("ajaxcallPriceRaw/{id}", "App\Controllers\EventgroupController:ajaxcallPriceRaw");
 
   
  
   $app->get("event-day", "App\Controllers\EventgroupController:getEventgroupOfDay");   // Event Day page 
  
   
	$app->get("do-not-miss", "App\Controllers\EventgroupController:getDonMissEventGroup");   // Upcoming Events page 	
 
		/* =======Code add by Gutropolis Team ==================*/
	
		$app->get("myorder", "App\Controllers\HomeController:checkMyOrder");   // My Order page
		$app->post("myorder", "App\Controllers\HomeController:checkMyOrder");   // My Order page
		$app->get("la-communaute", "App\Controllers\HomeController:getCommunity");   // CulturAccess-Community page
		$app->get("theater-comedy", "App\Controllers\HomeController:getTheaterComedy");   // Theater-Comedy page
		$app->get("theater-comedy-00", "App\Controllers\HomeController:getTheaterComedyzero");   // Theater-Comedy-00 page
		$app->get("theater-comedy-01", "App\Controllers\HomeController:getTheaterComedyone");   // Theater-Comedy-01 page
		
			$app->get("concerts-musique", "App\Controllers\HomeController:getConcerts");   // Theater-Comedy-01 page
			$app->get("opera-danse", "App\Controllers\HomeController:getOperaDense");   // Theater-Comedy-01 page
			$app->get("culture-expos", "App\Controllers\HomeController:getCultureExpo");   // Theater-Comedy-01 page
			$app->get("sports-loisirs", "App\Controllers\HomeController:getSports");   // Theater-Comedy-01 page
			$app->get("tourisme-visite-guide", "App\Controllers\HomeController:getTourismVisitGuide");   // Theater-Comedy-01 page

		
	
	/*=============End code here ==============================*/
	
	//code on 7 may
	$app->post("add_culturaccess-community", "App\Controllers\UserController:AddCommunity");
	$app->get("add_culturaccess-community", "App\Controllers\UserController:AddCommunity");
	$app->post("add-order", "App\Controllers\HomeController:AddOrder");   // Order page
	
	
	
	
	
	//shopping cart
 $app->get("shoopingcart", "App\Controllers\ShoppingcartController:getCart");
 $app->get("viewcart/{id}", "App\Controllers\ShoppingcartController:veiwCart");
 $app->get("viewcart", "App\Controllers\ShoppingcartController:CartView")->setName('viewcart');

 
 //for login functionality
    $app->get("login", "App\Controllers\UserController:signIn");
	$app->post("login", "App\Controllers\UserController:signIn");
    $app->get("register", "App\Controllers\UserController:registerFull");
     
	
	$app->get("loginorder", "App\Controllers\UserController:signInOrder");
	$app->get("registerorder", "App\Controllers\UserController:registerOnCart");
	$app->get("forget-pwd", "App\Controllers\UserController:forgetPwd");
	
	 
	
	
	
	//my order forms
	$app->get("mon-compte/{case}", "App\Controllers\UserController:myDashboard");
	$app->post("mon-compte/updateemail", "App\Controllers\UserController:updateEmail");
	//$app->get("mon-compte/updateemail", "App\Controllers\UserController:updateEmail");
	$app->post("mon-compte/updatePassword", "App\Controllers\UserController:updatePassword");
	$app->post("mon-compte/edituser", "App\Controllers\UserController:editUser");
	$app->get("logout", "App\Controllers\UserController:LogOut");


	//registration

	$app->get("registration", "App\Controllers\HomeController:getResistration"); 
	//$app->post("registration", "App\Controllers\HomeController:showregister");
	$app->get("sendemail", "App\Controllers\UserController:sendingemail"); 
	$app->get("contact_email", "App\Controllers\UserController:SendMailByContact"); 
	 

	 //reset password
	
	$app->get("reset_password/{token}", "App\Controllers\HomeController:resetPassword");

	$app->get("password_reset", "App\Controllers\UserController:PasswordReset");
	$app->post("password_reset", "App\Controllers\UserController:PasswordReset");
	
});
?>