<?php 
/*-------------------------------------------------------------------
|                  Public/Home Pages Routes  By Amin                 |
---------------------------------------------------------------------*/
$app->get("/", "App\Controllers\HomeController:comingSoon");
$app->group('/', function () use ($app) {
    $app->get("home", "App\Controllers\HomeController:getHome");  // Home page
	$app->get("booking", "App\Controllers\HomeController:getBooking");	 // Booking page
	$app->get("order", "App\Controllers\HomeController:checkOrder");   // Order page 	
	$app->get("mysearch", "App\Controllers\HomeController:getItemBySearch");   // My Search page 
	$app->get("do-not-miss", "App\Controllers\HomeController:upcomingEvent");   // Upcoming Events page 	
	$app->get("event-day", "App\Controllers\HomeController:eventDay");   // Event Day page 
		/* =======Code add by Gutropolis Team ==================*/
	
		$app->get("myorder", "App\Controllers\HomeController:checkMyOrder");   // My Order page
		$app->get("culturaccess-community", "App\Controllers\HomeController:getCommunity");   // CulturAccess-Community page
		$app->get("theater-comedy", "App\Controllers\HomeController:getTheaterComedy");   // Theater-Comedy page
		$app->get("theater-comedy-00", "App\Controllers\HomeController:getTheaterComedyzero");   // Theater-Comedy-00 page
		$app->get("theater-comedy-01", "App\Controllers\HomeController:getTheaterComedyone");   // Theater-Comedy-01 page
		
			$app->get("concerts-musique", "App\Controllers\HomeController:getConcerts");   // Theater-Comedy-01 page
			$app->get("opera-danse", "App\Controllers\HomeController:getOperaDense");   // Theater-Comedy-01 page
			$app->get("culture-expos", "App\Controllers\HomeController:getCultureExpo");   // Theater-Comedy-01 page
			$app->get("sports-loisirs", "App\Controllers\HomeController:getSports");   // Theater-Comedy-01 page
			$app->get("tourisme-visite-guide", "App\Controllers\HomeController:getTourismVisitGuide");   // Theater-Comedy-01 page

		
	
	/*=============End code here ==============================*/
});
?>