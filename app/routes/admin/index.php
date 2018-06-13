<?php 
/*-------------------------------------------------------------------
|                  Admin Pages Routes  By Amin                       |
---------------------------------------------------------------------*/
$app->get("/admin", "App\Controllers\AdminLoginController:checkAccess");
$app->get("/admin/", "App\Controllers\AdminLoginController:checkAccess");
$app->get("/admin/login", "App\Controllers\AdminLoginController:login");
$app->post("/admin/check_login", "App\Controllers\AdminLoginController:signIn");

$app->group('/admin', function () use ($app) { 
     // Dashboard + general admin routes
     $app->get("/dashboard", "App\Controllers\AdminDashboardController:dashboard");
	 $app->get("/profile/edit/{id}", "App\Controllers\AdminLoginController:getProfile");
	 $app->post("/display_selected_option_popup", "App\Controllers\AdminController:display_selected_option_popup");
	 $app->post("/edit_bulk_data", "App\Controllers\AdminController:edit_bulk_data");
	 $app->post("/profile/update", "App\Controllers\AdminLoginController:updateProfile");
	 $app->get("/settings", "App\Controllers\AdminController:settings");
	 $app->post("/save_settings", "App\Controllers\AdminController:save_settings");
	 $app->post("/updateSiteMode", "App\Controllers\AdminController:updateSiteMode");
	 $app->post("/profile/changePassword", "App\Controllers\AdminLoginController:changePassword");
	 $app->post("/adminLanguage/{langId}", "App\Controllers\AdminController:adminLanguage");
	 
	 
	 // All cities routes 
	 $app->get("/cities", "App\Controllers\AdminCityController:cities");
	 $app->post("/saveCity", "App\Controllers\AdminCityController:saveCity");
	 $app->get("/cities/edit/{id}", "App\Controllers\AdminCityController:getCityById");
	 $app->post("/cities/update", "App\Controllers\AdminCityController:updateCity");
	 $app->get("/cities/delete/{id}", "App\Controllers\AdminCityController:deleteCityById");
	 $app->post("/getAjaxCitiesList", "App\Controllers\AdminCityController:ajaxCityList"); 
	 $app->post("/getStates", "App\Controllers\AdminCityController:getStates");
	 
	 
	 // All category routes 
	 $app->get("/categories", "App\Controllers\AdminCategoryController:categories");
	 $app->post("/saveCategory", "App\Controllers\AdminCategoryController:saveCategory");
	 $app->get("/categories/edit/{id}", "App\Controllers\AdminCategoryController:getCategoryById");
	 $app->post("/categories/update", "App\Controllers\AdminCategoryController:updateCategory");
	 $app->get("/categories/delete/{id}", "App\Controllers\AdminCategoryController:deleteCategoryById");
	 $app->post("/getAjaxCategoriesList", "App\Controllers\AdminCategoryController:ajaxCategoriesList"); 
	 
	 
	 // All auditorium routes
	 $app->get("/auditoriums", "App\Controllers\AdminAuditoriumController:auditoriums");
	 $app->post("/getAjaxAuditoriumsList", "App\Controllers\AdminAuditoriumController:getAjaxAuditoriumsList"); 
	 $app->get("/auditoriums/add", "App\Controllers\AdminAuditoriumController:add");
	 $app->post("/saveAuditorium", "App\Controllers\AdminAuditoriumController:saveAuditorium");
	 $app->get("/auditoriums/getAuditoriumMap/{id}", "App\Controllers\AdminAuditoriumController:getAuditoriumMapById");
	 $app->get("/auditoriums/edit/{id}", "App\Controllers\AdminAuditoriumController:getAuditoriumById");
	 $app->post("/auditoriums/update", "App\Controllers\AdminAuditoriumController:updateAuditorium");
	 $app->get("/auditoriums/delete/{id}", "App\Controllers\AdminAuditoriumController:deleteAuditoriumById");
	 $app->get("/auditoriums/deleteAudSeats/{id}", "App\Controllers\AdminAuditoriumController:deleteAudSeatsById");
	 $app->get("/auditoriums/getAuditoriumSeatMap/{id}", "App\Controllers\AdminAuditoriumController:getAuditoriumSeatMapById");
	  $app->get("/auditoriums/getAuditoriumSeatMapEvent/{id}/{event_id}", "App\Controllers\AdminAuditoriumController:getAuditoriumSeatMapEventById");
	 
	 // All artists routes
	 $app->get("/artists", "App\Controllers\AdminArtistController:artists");
	 $app->post("/saveArtist", "App\Controllers\AdminArtistController:saveArtist");
	 $app->get("/artists/edit/{id}", "App\Controllers\AdminArtistController:getArtistById");
	 $app->post("/artists/update", "App\Controllers\AdminArtistController:updateArtist");
	 $app->get("/artists/delete/{id}", "App\Controllers\AdminArtistController:deleteArtistById");
	 $app->post("/getAjaxArtistsList", "App\Controllers\AdminArtistController:getAjaxArtistsList"); 
	 
	  // All members routes
	 $app->get("/members", "App\Controllers\AdminMemberController:members");
	 $app->post("/saveMember", "App\Controllers\AdminMemberController:saveMember");
	 $app->get("/members/edit/{id}", "App\Controllers\AdminMemberController:getMemberById");
	 $app->get("/members/view/{id}", "App\Controllers\AdminMemberController:viewMemberById");
	 $app->post("/members/update", "App\Controllers\AdminMemberController:updateMember");
	 $app->get("/members/delete/{id}", "App\Controllers\AdminMemberController:deleteMemberById");
	 $app->post("/getAjaxMembersList", "App\Controllers\AdminMemberController:getAjaxMembersList"); 
	 
	 // All productors routes
	 $app->get("/productors", "App\Controllers\AdminProductorController:productors");
	 $app->post("/saveProductor", "App\Controllers\AdminProductorController:saveProductor");
	 $app->get("/productors/edit/{id}", "App\Controllers\AdminProductorController:getProductorById");
	 $app->post("/productors/update", "App\Controllers\AdminProductorController:updateProductor");
	 $app->get("/productors/delete/{id}", "App\Controllers\AdminProductorController:deleteProductorById");
	 $app->post("/getAjaxProductorsList", "App\Controllers\AdminProductorController:getAjaxProductorsList"); 
	 
	 
	 
	 // All Events Group routes
	 $app->get("/events/groups", "App\Controllers\AdminEventGroupController:eventgroups");
	 $app->post("/events/groups/saveEventGroup", "App\Controllers\AdminEventGroupController:saveEventGroup");
	 $app->get("/events/groups/get/{id}", "App\Controllers\AdminEventGroupController:getEventGroupById");
	 $app->get("/events/groups/edit/{id}", "App\Controllers\AdminEventGroupController:editEventGroupById");
	 $app->post("/events/groups/edit/update", "App\Controllers\AdminEventGroupController:updateEventGroup");
	 $app->get("/events/groups/delete/{id}", "App\Controllers\AdminEventGroupController:deleteEventGroupById");
	 $app->post("/events/groups/getAjaxEventGroupsList", "App\Controllers\AdminEventGroupController:getAjaxEventGroupsList"); 
	 $app->post("/events/groups/upload/{id}", "App\Controllers\AdminEventGroupController:uploadImages");
	 $app->post("/events/groups/removeFile/{file_name}", "App\Controllers\AdminEventGroupController:removeFile");
	 $app->get("/events/groups/removeVideoLink/{id}", "App\Controllers\AdminEventGroupController:removeVideoLink");
	 $app->get("/events/groups/edit/deleteGroupComment/{id}", "App\Controllers\AdminEventGroupController:deleteGroupComment");
	 $app->get("/events/groups/duplicate/{id}", "App\Controllers\AdminEventGroupController:duplicateEventGroup");
	 $app->get("/events/groups/updateEventGroupStatus/{id}", "App\Controllers\AdminEventGroupController:updateGroupStatus");
	 $app->get("/events/groups/deleteEventGroupRole/{id}", "App\Controllers\AdminEventGroupController:deleteEventGroupRole");
	 
	 // All Events routes
	 $app->get("/events", "App\Controllers\AdminEventController:events");
	 $app->post("/events/saveEvent", "App\Controllers\AdminEventController:saveEvent");
	 $app->get("/events/get/{id}", "App\Controllers\AdminEventController:getEventById");
	 $app->get("/events/edit/{id}", "App\Controllers\AdminEventController:editEventById");
	 $app->post("/events/update", "App\Controllers\AdminEventController:updateEvent");
	 $app->get("/events/deleteEventPic/{id}", "App\Controllers\AdminEventController:deleteEventPictureById");
	 $app->get("/events/deleteEventTime/{id}", "App\Controllers\AdminEventController:deleteEventTimeById");
	 $app->get("/events/deleteEventTicket/{id}", "App\Controllers\AdminEventController:deleteEventTicketById");
	 $app->get("/events/delete/{id}", "App\Controllers\AdminEventController:deleteEventById");
	 $app->get("/events/updateEventStatus/{id}/{status}", "App\Controllers\AdminEventController:updateEventStatus");
	 $app->post("/events/getAjaxEventsList", "App\Controllers\AdminEventController:getAjaxEventsList"); 
	 $app->get("/events/deleteEventRole/{id}", "App\Controllers\AdminEventController:deleteEventRoleById");
	 $app->get("/events/eventMap/{id}", "App\Controllers\AdminEventController:eventMapPage");
	 $app->get("/events/eventMapAdd/{id}", "App\Controllers\AdminEventController:eventMapAdd");
	 $app->get("/events/eventMapEdit/{id}", "App\Controllers\AdminEventController:eventMapEdit");
	 $app->post("/events/saveEventSeatTicketMap", "App\Controllers\AdminEventController:saveEventSeatTicketMap");
	 $app->post("/events/updateEventSeatTicketMap", "App\Controllers\AdminEventController:updateEventSeatTicketMap");
	 $app->post("/events/getAjaxEventMapList", "App\Controllers\AdminEventController:getAjaxEventMapList");
	 $app->get("/events/deleteEventSeat/{id}", "App\Controllers\AdminEventController:deleteEventSeatById");
	 $app->get("/events/deleteEventSeatRow/{id}", "App\Controllers\AdminEventController:deleteEventSeatRowById");
	 $app->get("/events/deleteRowSeat/{id}/{row_number}", "App\Controllers\AdminEventController:deleteRowSeatById");
	 
	 
	 
	 // Dont Miss page
	  $app->get("/dont-miss", "App\Controllers\AdminEventController:eventsDontMiss");
	  $app->post("/getAjaxEventDontMissList", "App\Controllers\AdminEventController:ajaxDontMissEventsList"); 
	  $app->get("/event-day", "App\Controllers\AdminEventController:eventsOfDay");
	  $app->post("/getAjaxEventOfDayList", "App\Controllers\AdminEventController:ajaxEventsOfDayList");
	  
	      
	 
	 // All slider routes 
	 $app->get("/sliders", "App\Controllers\AdminSliderController:sliders");
	 $app->post("/saveSlider", "App\Controllers\AdminSliderController:saveSlider");
	 $app->get("/sliders/edit/{id}", "App\Controllers\AdminSliderController:getSliderById");
	 $app->post("/sliders/update", "App\Controllers\AdminSliderController:updateSlider");
	 $app->get("/sliders/delete/{id}", "App\Controllers\AdminSliderController:deleteSliderById");
	 $app->post("/getAjaxSlidersList", "App\Controllers\AdminSliderController:ajaxSlidersList"); 
	 
	 // All advertisement routes 
	 $app->get("/advertisements", "App\Controllers\AdminAdvertisementController:advertisements");
	 $app->post("/saveAdd", "App\Controllers\AdminAdvertisementController:saveAd");
	 $app->get("/advertisements/edit/{id}", "App\Controllers\AdminAdvertisementController:getAdById");
	 $app->post("/advertisements/update", "App\Controllers\AdminAdvertisementController:updateAd");
	 $app->get("/advertisements/delete/{id}", "App\Controllers\AdminAdvertisementController:deleteAdById");
	 $app->post("/getAjaxAdsList", "App\Controllers\AdminAdvertisementController:ajaxAdsList"); 
	 
	 // All category page slider routes 
	 $app->get("/category_page_sliders", "App\Controllers\AdminCategorySliderController:category_page_sliders");
	 $app->post("/saveCategorySlider", "App\Controllers\AdminCategorySliderController:saveCategorySlider");
	 $app->get("/category_page_sliders/edit/{id}", "App\Controllers\AdminCategorySliderController:getCategorySliderById");
	 $app->post("/category_page_sliders/update", "App\Controllers\AdminCategorySliderController:updateCategorySlider");
	 $app->get("/category_page_sliders/delete/{id}", "App\Controllers\AdminCategorySliderController:deleteCategorySliderById");
	 $app->post("/getAjaxCategorySlidersList", "App\Controllers\AdminCategorySliderController:ajaxCategorySlidersList"); 
	 
	 // All partners routes 
	 $app->get("/partners", "App\Controllers\AdminPartnerController:partners");
	 $app->post("/savePartner", "App\Controllers\AdminPartnerController:savePartner");
	 $app->get("/partners/edit/{id}", "App\Controllers\AdminPartnerController:getPartnerById");
	 $app->post("/partners/update", "App\Controllers\AdminPartnerController:updatePartner");
	 $app->get("/partners/delete/{id}", "App\Controllers\AdminPartnerController:deletePartnerById");
	 $app->post("/getAjaxPartnersList", "App\Controllers\AdminPartnerController:ajaxPartnersList"); 
	 
	 
	 // All cms routes 
	 $app->get("/cms", "App\Controllers\AdminCmsController:cms");
	 $app->get("/cms/get/{id}", "App\Controllers\AdminCmsController:getCmsById");
	 $app->post("/cms/update", "App\Controllers\AdminCmsController:updateCms");
	 
	 
	 // All payment types routes 
	 $app->get("/paymentTypes", "App\Controllers\AdminPaymentTypeController:paymentTypes");
	 $app->post("/savePaymentType", "App\Controllers\AdminPaymentTypeController:savePaymentType");
	 $app->get("/paymentTypes/edit/{id}", "App\Controllers\AdminPaymentTypeController:getPaymentTypeById");
	 $app->post("/paymentTypes/update", "App\Controllers\AdminPaymentTypeController:updatePaymentType");
	 $app->get("/paymentTypes/delete/{id}", "App\Controllers\AdminPaymentTypeController:deletePaymentTypeById");
	 $app->post("/getAjaxPaymentTypesList", "App\Controllers\AdminPaymentTypeController:ajaxPaymentTypesList"); 
	 
	 
	 // All communities routes 
	 $app->get("/community", "App\Controllers\AdminCommunityController:communities");
	 $app->post("/saveCommunity", "App\Controllers\AdminCommunityController:saveCommunity");
	 $app->get("/community/edit/{id}", "App\Controllers\AdminCommunityController:getCommunityById");
	 $app->post("/community/update", "App\Controllers\AdminCommunityController:updateCommunity");
	 $app->get("/community/delete/{id}", "App\Controllers\AdminCommunityController:deleteCommunityById");
	 $app->post("/getAjaxCommunitiesList", "App\Controllers\AdminCommunityController:ajaxCommunitiesList"); 
	 
	 
	 // All community page routes 
	 $app->get("/community_page", "App\Controllers\AdminCommunityPageController:community_page");
	 $app->post("/saveCommunityPage", "App\Controllers\AdminCommunityPageController:saveCommunityPage");
	 $app->get("/community_page/add", "App\Controllers\AdminCommunityPageController:add");
	 $app->get("/community_page/edit/{id}", "App\Controllers\AdminCommunityPageController:getCommunityPageById");
	 $app->post("/community_page/update", "App\Controllers\AdminCommunityPageController:updateCommunityPage");
	 $app->get("/community_page/delete/{id}", "App\Controllers\AdminCommunityPageController:deleteCommunityPageById");
	 $app->post("/getAjaxCommunityPageList", "App\Controllers\AdminCommunityPageController:ajaxCommunityPageList"); 
	 
	 
	 // All currencies routes 
	 $app->get("/currencies", "App\Controllers\AdminCurrencyController:currencies");
	 $app->post("/saveCurrency", "App\Controllers\AdminCurrencyController:saveCurrency");
	 $app->get("/currencies/edit/{id}", "App\Controllers\AdminCurrencyController:getCurrenyById");
	 $app->post("/currencies/update", "App\Controllers\AdminCurrencyController:updateCurrency");
	 $app->get("/currencies/delete/{id}", "App\Controllers\AdminCurrencyController:deleteCurrencyById");
	 $app->post("/getAjaxCurrenciesList", "App\Controllers\AdminCurrencyController:ajaxCurrencyList"); 
	 
	 // All orders routes
	 $app->get("/orders", "App\Controllers\AdminOrderController:orders");
	 $app->get("/orders/view/{id}", "App\Controllers\AdminOrderController:getOrderById");
	 $app->get("/orders/delete/{id}", "App\Controllers\AdminOrderController:deleteOrderById");
	 $app->post("/getAjaxOrdersList", "App\Controllers\AdminOrderController:getAjaxOrdersList");
	 
	 // All sections routes 
	 $app->get("/sections", "App\Controllers\AdminSectionController:sections");
	 $app->post("/saveSection", "App\Controllers\AdminSectionController:saveSection");
	 $app->get("/sections/edit/{id}", "App\Controllers\AdminSectionController:getSectionById");
	 $app->post("/sections/update", "App\Controllers\AdminSectionController:updateSection");
	 $app->get("/sections/delete/{id}", "App\Controllers\AdminSectionController:deleteSectionById");
	 $app->post("/getAjaxSectionsList", "App\Controllers\AdminSectionController:ajaxSectionsList"); 
	 
})->add('App\Middleware\AdminAuthMiddleware');

$app->get("/admin/logout", "App\Controllers\AdminLoginController:logout"); // log out


