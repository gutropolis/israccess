<?php 

// All site constants 

define('HOME_VIEW', 'public/home'); // Home controller page

define('ADMIN_VIEW', 'admin'); // Admin controller page

if(ENVIRONMENT == 'production'){

	// Define for Production Server

   define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT']); 

}else{

	// Define for Development Server

	define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT'].'/event_site_v1/public'); 

}

define('WEB_PATH', base_url);



// Categories upload folder root & web path

define('CATEGORY_ROOT_PATH', ROOT_PATH.'/uploads/categories'); // For upload purpose only

define('CATEGORY_WEB_PATH', WEB_PATH.'/uploads/categories'); // For displaying picture with web path



// For auditorium

define('AUDITORIUM_ROOT_PATH', ROOT_PATH.'/uploads/auditoriums'); // For upload purpose only

define('AUDITORIUM_WEB_PATH', WEB_PATH.'/uploads/auditoriums'); // For displaying picture with web path



// For Event Groups

define('EVENTGROUP_ROOT_PATH', ROOT_PATH.'/uploads/eventgroups'); // For upload

define('EVENTGROUP_WEB_PATH', WEB_PATH.'/uploads/eventgroups'); // For displaying picture



// for EN SAVOIR TAB

define('EVENTGROUP_EN_SAVOIR_ROOT_PATH', EVENTGROUP_ROOT_PATH.'/en_savoir_tab'); // For upload

define('EVENTGROUP_EN_SAVOIR_WEB_PATH', EVENTGROUP_WEB_PATH.'/en_savoir_tab'); // For displaying picture



// For Sliders

define('SLIDER_ROOT_PATH', ROOT_PATH.'/uploads/sliders'); // For upload

define('SLIDER_WEB_PATH', WEB_PATH.'/uploads/sliders'); // For displaying picture


// For Advertisement
define('ADS_ROOT_PATH', ROOT_PATH.'/uploads/advertisements'); // For upload
define('ADS_WEB_PATH', WEB_PATH.'/uploads/advertisements'); // For displaying picture



// For Category page Sliders
define('CAT_PAGE_SLIDER_ROOT_PATH', ROOT_PATH.'/uploads/category_page_sliders'); // For upload
define('CAT_PAGE_SLIDER_WEB_PATH', WEB_PATH.'/uploads/category_page_sliders'); // For displaying picture

// For Events

define('EVENT_ROOT_PATH', ROOT_PATH.'/uploads/events'); // For upload

define('EVENT_WEB_PATH', WEB_PATH.'/uploads/events'); // For displaying picture



// For Partners

define('PARTNER_ROOT_PATH', ROOT_PATH.'/uploads/partners'); // For upload

define('PARTNER_WEB_PATH', WEB_PATH.'/uploads/partners'); // For displaying picture



// For Event Groups Video Links

define('EVENTGROUP_VID_ROOT_PATH', EVENTGROUP_ROOT_PATH.'/videos'); // For upload

define('EVENTGROUP_VID_WEB_PATH',  EVENTGROUP_WEB_PATH.'/videos'); // For displaying picture



// For Payment Types

define('PAYMENT_TYPE_ROOT_PATH', ROOT_PATH.'/uploads/payment_types'); // For upload

define('PAYMENT_TYPE_WEB_PATH', WEB_PATH.'/uploads/payment_types'); // For displaying picture



// For Profiles

define('PROFILE_ROOT_PATH', ROOT_PATH.'/uploads/profiles'); // For upload

define('PROFILE_WEB_PATH', WEB_PATH.'/uploads/profiles'); // For displaying picture



// For Member profiles

define('MEMBER_ROOT_PATH', PROFILE_ROOT_PATH.'/members'); // For upload

define('MEMBER_WEB_PATH', PROFILE_WEB_PATH.'/members'); // For displaying picture



// For Artist profiles

define('ARTIST_ROOT_PATH', PROFILE_ROOT_PATH.'/artists'); // For upload

define('ARTIST_WEB_PATH', PROFILE_WEB_PATH.'/artists'); // For displaying picture



// For Productor profiles

define('PRODUCTOR_ROOT_PATH', PROFILE_ROOT_PATH.'/productors'); // For upload

define('PRODUCTOR_WEB_PATH', PROFILE_WEB_PATH.'/productors'); // For displaying picture



// For Admin profile

define('ADMIN_ROOT_PATH', PROFILE_ROOT_PATH.'/admin'); // For upload

define('ADMIN_WEB_PATH', PROFILE_WEB_PATH.'/admin'); // For displaying picture



// Image thumbnails size

define('THUMB_HEIGHT',               100);

define('THUMB_WEIGHT',               100);



// API Key for Google MAP

define('GOOGLE_MAP_API_KEY', 'AIzaSyB71UFfFPV48FQNNfKofdfTW-P6K3EbXwc');



define('DEFAULT_IMG', WEB_PATH.'/uploads/default/default.png'); 

// Default profile picture

define('DEFAULT_PROFILE_IMG', WEB_PATH.'/uploads/default/default-profile.jpg');





define('COPY_RIGHT', date('Y'));



