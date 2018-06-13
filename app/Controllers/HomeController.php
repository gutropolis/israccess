<?php
namespace App\Controllers;
use App\Models\User;
use App\Models\Event;
use App\Models\Eventgroup;
use App\Models\Category;
use App\Models\City;
use App\Models\Partner;
use App\Models\Slider;
use App\Models\Section;
use App\Models\CommunityPage;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class HomeController extends BaseController{

public function showPopup($request, $response){
	$data=array();
		$catSlider=array();
 	    $dataTheatureArr=array();
		$dataConcertArr=array();
		$dataCultureArr=array();
		$homeSliderArr=array();
		
		$homeSliderArr = Slider::where('status','1')->orderBy('display_order','asc')->get(); 	

			 
				$categoryid = $request->getAttribute('id'); //fetch the category id
				
				$homeSections = Section::where('status','1')->get(); 	
                 	 
				 
				if(count($homeSections)> 0){
					
					 
					 foreach ($homeSections as $cat){
						$fetchEventList=array();
						$category_id= $cat['id'];
						$home_slider_title= $cat['section_title'];
					 
						
						$event_list_sectio =  Eventgroup::where('section',$category_id)->orderBy('date_begin', 'asc')->limit('4')->get(); 	
						$event_list=getEventGroupList($event_list_sectio);
						 
						$fetchEventList['catID'] = $category_id;
						$fetchEventList['home_slider_title'] = html_entity_decode($home_slider_title);
						$fetchEventList['home_cat_name'] = html_entity_decode($home_slider_title);
						 
						$fetchEventList['eventList'] = $event_list;
						$catSlider[]=$fetchEventList;
							
					 } 				 
					
					  
				}
				 
				 $this->data['catSlider'] = $catSlider;
				 
				/*
				$firstCatId = '389'; // Theature
				$event_list_section1=  Eventgroup::where('category_id',$firstCatId)->get(); 
				$secondCatId = '390'; // Concert
				$event_list_section2=  Eventgroup::where('category_id',$secondCatId)->get(); // 
				$thirdCatId = '391'; // Culture
				$event_list_section3=  Eventgroup::where('category_id',$thirdCatId)->get(); // 
			      */
			
			 
 
			//End here//
         /*End here */ 
		 
		 /*
			$dataTheatureArr = getEventGroupList($event_list_section1);  
		 
			$dataConcertArr = getEventGroupList($event_list_section2);
			$dataCultureArr = getEventGroupList($event_list_section3);
			 */
		 
 
              
		$this->data['h1'] = 'Home';  
		 $this->data['sectionSlider'] =  Category::limit(4)->orderBy('id')->get();
		 
		/* 
		 $this->data['theatureEvents']  = $dataTheatureArr;	 
		 $this->data['firstCatId']  = $firstCatId;	
		 //For first slider
		$this->data['theatureEvents']  = $dataTheatureArr;	 
		$this->data['secondCatId']  = $secondCatId;	
		
		$this->data['concertEvents']  = $dataConcertArr;
	 
		$this->data['thirdCatId']  = $thirdCatId;	
		
		$this->data['cultureEvents']  = $dataCultureArr;
		*/
		$city=City::all();
		$this->data['egImgURL']  = EVENTGROUP_WEB_PATH.'/';
		$this->data['sliderImgURL']  = SLIDER_WEB_PATH.'/';
		$this->data['partnerImgURL']  = PARTNER_WEB_PATH.'/';
        $this->data['searchCateogories'] =  Category::orderBy('id')->get();
		$this->data['homeSliders'] =  $homeSliderArr;
		$this->data['homePartnerLogo'] = Partner::where('status','1')->get();
		$this->data['city'] =  $city;
	    $this->data['is_popup_show'] =  '1';
	    return $this->render($response, HOME_VIEW.'/home.twig',$this->data);
	
}

    public function getHome($request, $response){
		
		
		$data=array();
		$catSlider=array();
 	    $dataTheatureArr=array();
		$dataConcertArr=array();
		$dataCultureArr=array();
		$homeSliderArr=array();
		
		$homeSliderArr = Slider::where('status','1')->orderBy('display_order','asc')->get(); 	

			 
				$categoryid = $request->getAttribute('id'); //fetch the category id
				
				$homeSections = Section::where('status','1')->get(); 	
                 	 
				 
				if(count($homeSections)> 0){
					
					 
					 foreach ($homeSections as $cat){
						$fetchEventList=array();
						$category_id= $cat['id'];
						$home_slider_title= $cat['section_title'];
					 
						
						$event_list_sectio =  Eventgroup::where('section',$category_id)->orderBy('date_begin', 'asc')->limit('4')->get(); 	
						$event_list=getEventGroupList($event_list_sectio);
						 
						$fetchEventList['catID'] = $category_id;
						$fetchEventList['home_slider_title'] = html_entity_decode($home_slider_title);
						$fetchEventList['home_cat_name'] = html_entity_decode($home_slider_title);
						 
						$fetchEventList['eventList'] = $event_list;
						$catSlider[]=$fetchEventList;
							
					 } 				 
					
					  
				}
				 
				 $this->data['catSlider'] = $catSlider;
				 
				/*
				$firstCatId = '389'; // Theature
				$event_list_section1=  Eventgroup::where('category_id',$firstCatId)->get(); 
				$secondCatId = '390'; // Concert
				$event_list_section2=  Eventgroup::where('category_id',$secondCatId)->get(); // 
				$thirdCatId = '391'; // Culture
				$event_list_section3=  Eventgroup::where('category_id',$thirdCatId)->get(); // 
			      */
			
			 
 
			//End here//
         /*End here */ 
		 
		 /*
			$dataTheatureArr = getEventGroupList($event_list_section1);  
		 
			$dataConcertArr = getEventGroupList($event_list_section2);
			$dataCultureArr = getEventGroupList($event_list_section3);
			 */
		 
 
              
		$this->data['h1'] = 'Home';  
		 $this->data['sectionSlider'] =  Category::limit(4)->orderBy('id')->get();
		 
		/* 
		 $this->data['theatureEvents']  = $dataTheatureArr;	 
		 $this->data['firstCatId']  = $firstCatId;	
		 //For first slider
		$this->data['theatureEvents']  = $dataTheatureArr;	 
		$this->data['secondCatId']  = $secondCatId;	
		
		$this->data['concertEvents']  = $dataConcertArr;
	 
		$this->data['thirdCatId']  = $thirdCatId;	
		
		$this->data['cultureEvents']  = $dataCultureArr;
		*/
		$city=City::all();
		$this->data['egImgURL']  = EVENTGROUP_WEB_PATH.'/';
		$this->data['sliderImgURL']  = SLIDER_WEB_PATH.'/';
		$this->data['partnerImgURL']  = PARTNER_WEB_PATH.'/';
        $this->data['searchCateogories'] =  Category::orderBy('id')->get();
		$this->data['homeSliders'] =  $homeSliderArr;
		$this->data['homePartnerLogo'] = Partner::where('status','1')->get();
		$this->data['city'] =  $city;
	  
	    return $this->render($response, HOME_VIEW.'/home.twig',$this->data);
    }
	
	
	public function comingSoon(RequestInterface $request, ResponseInterface $response){
		return $this->response->withStatus(200)->withHeader('Location', base_url.'/coming-soon.php'); 
    }
	
	
	
	public function checkOrder($request, $response){  
		$this->data['giftitem'] = '1'; 
	  return $this->render($response, HOME_VIEW.'/order.twig',$this->data); 
	}
	public function getItemBySearch($request, $response){ 	 
	   return $this->render($response, HOME_VIEW.'/mysearch.twig',$this->data); 
	}
	public function upcomingEvent($request, $response){ 	 
	   return $this->render($response, HOME_VIEW.'/do-no-miss.twig',$this->data); 
	}
	public function eventDay($request, $response){
		 return $this->render($response, HOME_VIEW.'/events-of-day.twig',$this->data); 
    }
	
	/* ===========Add code on 22 April =============*/
	
	 public function checkMyOrder($request, $response){
		 
		 if(isMemberLogin()==true) 
		 {
			return $this->render($response, HOME_VIEW.'/myorder.twig',$this->data); 
		 }
		else
		{
			return $this->render($response,  'public/booking/booking.twig',$this->data);
		}
        

    }
    public function getCommunity($request, $response){
            $this->data['communitynev']='1';
			$this->data['community_access_header'] =  getCmsList('6'); //First Right side Ads 
		 
			$this->data['community_access_right'] =  getCmsList('7'); //First Right side Ads 
			$communityArr = CommunityPage::where('status','1')->orderBy('display_order','asc')->get(); 
			
			$this->data['communityArr'] =  $communityArr; //First Right side Ads 
			
			
			 
        return $this->render($response, HOME_VIEW.'/culturaccess-community.twig',$this->data);

    }
   
    public function getCommunityFullDesc($request, $response){
		$id=$request->getAttribute('id'); 
		$communityArr = CommunityPage::where('id',$id)->first(); 
         if(count($communityArr) > 0){
             $content ='<div class="tllTitle">
							<h3  id="toutelaliste">LES militants DE CULTURACCESS</h3><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						</div>
						<div class="tllContent">
							<div class="tllinnerContent" >
								 '.$communityArr['full_description'].'
							</div>
						</div>';
		 }
			echo $content;exit;		 
	}
    public function getTheaterComedyzero($request, $response){

        return $this->render($response, HOME_VIEW.'/theater-comedy-00.twig',$this->data);

    }
    public function getTheaterComedyone($request, $response){

        return $this->render($response, HOME_VIEW.'/theater-comedy-01.twig',$this->data);

    }
	
	
	//Add categories controller //
	
	 public function getTheaterComedy($request, $response){

        return $this->render($response, HOME_VIEW.'/theater-comedy.twig');

    }
	
	 public function getConcerts($request, $response){

        return $this->render($response, HOME_VIEW.'/concerts-musique.twig');

    }
	 public function getOperaDense($request, $response){

        return $this->render($response, HOME_VIEW.'/opera-danse.twig');

    }
	 public function getCultureExpo($request, $response){

        return $this->render($response, HOME_VIEW.'/culture-expos.twig');

    }
	 public function getSports($request, $response){

        return $this->render($response, HOME_VIEW.'/sports-loisirs.twig');

    }
	 public function getTourismVisitGuide($request, $response){

        return $this->render($response, HOME_VIEW.'/tourisme-visite-guide.twig');

    }
	
	
	/*========End here  ============================*/
	
	
	
	 public function GetAllCategories($request, $response)
    {
        $mycateg = Categories::all();
        echo $mycateg;
    }

	public function getAllUsers($request, $response)
	{
		
		return $this->render($response, 'admin/partial/user/index.twig');
    }

	public function getUsersById($request, $response)
	{
		$id=$request->getAttribute('id');
		$userid=User::where('id', $id)->first();
		return $this->render($response,'admin/partial/user/edit.twig',['mytable' => $userid]);
    }

	public function GetAllAuditoriums($request, $response)
	{
		$auditorium = Auditorium::all();

		echo $auditorium;
    }
	public function GetAllArtist($request, $response)
	{
		$artist = Artist::all();

		echo $artist;
    }
	public function getAllEvents_Group($request, $response)
	{
		$eventg = Events_Group::all();

		echo $eventg;
    }

	public function getAllEvents($request, $response)
	{
		
		return $this->_view->render($response, 'admin/partial/event/index.twig');
    }

	public function getEventById($request, $response)
	{
		$id=$request->getAttribute('id');
		$userid=User::where('id', $id)->first();
		return $this->_view->render($response,'admin/partial/event/edit.twig',['mytable' => $userid]);
    }


	public function getPost($request, $response)
	{

		return $this->_view->render($response,'admin/partial/post/create_post.twig');
    }



	//start base

	public function getStateColors($request, $response)
	{

		return $this->_view->render($response,'admin/partial/base/state_colors.twig');
    }

	public function getTypography($request, $response)
	{

		return $this->_view->render($response,'admin/partial/base/typography.twig');
    }
	public function getStack($request, $response)
	{

		return $this->_view->render($response,'admin/partial/base/stack.twig');
    }

	public function getTable($request, $response)
	{

		return $this->_view->render($response,'admin/partial/base/table.twig');
    }

	public function getProgress($request, $response)
	{

		return $this->_view->render($response,'admin/partial/base/progress.twig');
    }

	public function getModal($request, $response)
	{

		return $this->_view->render($response,'admin/partial/base/modal.twig');
    }

	public function getAlert($request, $response)
	{

		return $this->_view->render($response,'admin/partial/base/alert.twig');
    }

	public function getPopover($request, $response)
	{

		return $this->_view->render($response,'admin/partial/base/popover.twig');
    }

	public function getTooltip($request, $response)
	{

		return $this->_view->render($response,'admin/partial/base/tooltip.twig');
    }

	public function getBlockui($request, $response)
	{

		return $this->_view->render($response,'admin/partial/base/blockui.twig');
    }
	
	//add comunity data with email functionality
	
	 public function AddCommunity($request, $response)
    {
        $firstname = $request->getParam('firstname');
        $lastname = $request->getParam('lastname');
        $address = $request->getParam('address');
        $email = $request->getParam('email');
        $telephon = $request->getParam('telephon');
        $usermessage = $request->getParam('message');

        $msgArra= array();
        $msgArra['first_name'] = $firstname;
        $msgArra['last_name'] = $lastname;
        $msgArra['address'] = $address;
        $msgArra['email'] = $email;
        $msgArra['telephon'] = $telephon;
        $msgArra['message'] = $usermessage;
        $from = '';
        $subject ='Your logni credemtoa;s jere ';

       sendEmail($from,$email,$subject,$msgArra, 'register.html');

        return $this->render($response, HOME_VIEW.'/culturaccess-community.twig',$this->data);
    }
	 public function getAllCountry(){
		$countries = array("AF" => "Afghanistan",
						"AX" => "Åland Islands",
						"AL" => "Albania",
						"DZ" => "Algeria",
						"AS" => "American Samoa",
						"AD" => "Andorra",
						"AO" => "Angola",
						"AI" => "Anguilla",
						"AQ" => "Antarctica",
						"AG" => "Antigua and Barbuda",
						"AR" => "Argentina",
						"AM" => "Armenia",
						"AW" => "Aruba",
						"AU" => "Australia",
						"AT" => "Austria",
						"AZ" => "Azerbaijan",
						"BS" => "Bahamas",
						"BH" => "Bahrain",
						"BD" => "Bangladesh",
						"BB" => "Barbados",
						"BY" => "Belarus",
						"BE" => "Belgium",
						"BZ" => "Belize",
						"BJ" => "Benin",
						"BM" => "Bermuda",
						"BT" => "Bhutan",
						"BO" => "Bolivia",
						"BA" => "Bosnia and Herzegovina",
						"BW" => "Botswana",
						"BV" => "Bouvet Island",
						"BR" => "Brazil",
						"IO" => "British Indian Ocean Territory",
						"BN" => "Brunei Darussalam",
						"BG" => "Bulgaria",
						"BF" => "Burkina Faso",
						"BI" => "Burundi",
						"KH" => "Cambodia",
						"CM" => "Cameroon",
						"CA" => "Canada",
						"CV" => "Cape Verde",
						"KY" => "Cayman Islands",
						"CF" => "Central African Republic",
						"TD" => "Chad",
						"CL" => "Chile",
						"CN" => "China",
						"CX" => "Christmas Island",
						"CC" => "Cocos (Keeling) Islands",
						"CO" => "Colombia",
						"KM" => "Comoros",
						"CG" => "Congo",
						"CD" => "Congo, The Democratic Republic of The",
						"CK" => "Cook Islands",
						"CR" => "Costa Rica",
						"CI" => "Cote D'ivoire",
						"HR" => "Croatia",
						"CU" => "Cuba",
						"CY" => "Cyprus",
						"CZ" => "Czech Republic",
						"DK" => "Denmark",
						"DJ" => "Djibouti",
						"DM" => "Dominica",
						"DO" => "Dominican Republic",
						"EC" => "Ecuador",
						"EG" => "Egypt",
						"SV" => "El Salvador",
						"GQ" => "Equatorial Guinea",
						"ER" => "Eritrea",
						"EE" => "Estonia",
						"ET" => "Ethiopia",
						"FK" => "Falkland Islands (Malvinas)",
						"FO" => "Faroe Islands",
						"FJ" => "Fiji",
						"FI" => "Finland",
						"FR" => "France",
						"GF" => "French Guiana",
						"PF" => "French Polynesia",
						"TF" => "French Southern Territories",
						"GA" => "Gabon",
						"GM" => "Gambia",
						"GE" => "Georgia",
						"DE" => "Germany",
						"GH" => "Ghana",
						"GI" => "Gibraltar",
						"GR" => "Greece",
						"GL" => "Greenland",
						"GD" => "Grenada",
						"GP" => "Guadeloupe",
						"GU" => "Guam",
						"GT" => "Guatemala",
						"GG" => "Guernsey",
						"GN" => "Guinea",
						"GW" => "Guinea-bissau",
						"GY" => "Guyana",
						"HT" => "Haiti",
						"HM" => "Heard Island and Mcdonald Islands",
						"VA" => "Holy See (Vatican City State)",
						"HN" => "Honduras",
						"HK" => "Hong Kong",
						"HU" => "Hungary",
						"IS" => "Iceland",
						"IN" => "India",
						"ID" => "Indonesia",
						"IR" => "Iran, Islamic Republic of",
						"IQ" => "Iraq",
						"IE" => "Ireland",
						"IM" => "Isle of Man",
						"IL" => "Israel",
						"IT" => "Italy",
						"JM" => "Jamaica",
						"JP" => "Japan",
						"JE" => "Jersey",
						"JO" => "Jordan",
						"KZ" => "Kazakhstan",
						"KE" => "Kenya",
						"KI" => "Kiribati",
						"KP" => "Korea, Democratic People's Republic of",
						"KR" => "Korea, Republic of",
						"KW" => "Kuwait",
						"KG" => "Kyrgyzstan",
						"LA" => "Lao People's Democratic Republic",
						"LV" => "Latvia",
						"LB" => "Lebanon",
						"LS" => "Lesotho",
						"LR" => "Liberia",
						"LY" => "Libyan Arab Jamahiriya",
						"LI" => "Liechtenstein",
						"LT" => "Lithuania",
						"LU" => "Luxembourg",
						"MO" => "Macao",
						"MK" => "Macedonia, The Former Yugoslav Republic of",
						"MG" => "Madagascar",
						"MW" => "Malawi",
						"MY" => "Malaysia",
						"MV" => "Maldives",
						"ML" => "Mali",
						"MT" => "Malta",
						"MH" => "Marshall Islands",
						"MQ" => "Martinique",
						"MR" => "Mauritania",
						"MU" => "Mauritius",
						"YT" => "Mayotte",
						"MX" => "Mexico",
						"FM" => "Micronesia, Federated States of",
						"MD" => "Moldova, Republic of",
						"MC" => "Monaco",
						"MN" => "Mongolia",
						"ME" => "Montenegro",
						"MS" => "Montserrat",
						"MA" => "Morocco",
						"MZ" => "Mozambique",
						"MM" => "Myanmar",
						"NA" => "Namibia",
						"NR" => "Nauru",
						"NP" => "Nepal",
						"NL" => "Netherlands",
						"AN" => "Netherlands Antilles",
						"NC" => "New Caledonia",
						"NZ" => "New Zealand",
						"NI" => "Nicaragua",
						"NE" => "Niger",
						"NG" => "Nigeria",
						"NU" => "Niue",
						"NF" => "Norfolk Island",
						"MP" => "Northern Mariana Islands",
						"NO" => "Norway",
						"OM" => "Oman",
						"PK" => "Pakistan",
						"PW" => "Palau",
						"PS" => "Palestinian Territory, Occupied",
						"PA" => "Panama",
						"PG" => "Papua New Guinea",
						"PY" => "Paraguay",
						"PE" => "Peru",
						"PH" => "Philippines",
						"PN" => "Pitcairn",
						"PL" => "Poland",
						"PT" => "Portugal",
						"PR" => "Puerto Rico",
						"QA" => "Qatar",
						"RE" => "Reunion",
						"RO" => "Romania",
						"RU" => "Russian Federation",
						"RW" => "Rwanda",
						"SH" => "Saint Helena",
						"KN" => "Saint Kitts and Nevis",
						"LC" => "Saint Lucia",
						"PM" => "Saint Pierre and Miquelon",
						"VC" => "Saint Vincent and The Grenadines",
						"WS" => "Samoa",
						"SM" => "San Marino",
						"ST" => "Sao Tome and Principe",
						"SA" => "Saudi Arabia",
						"SN" => "Senegal",
						"RS" => "Serbia",
						"SC" => "Seychelles",
						"SL" => "Sierra Leone",
						"SG" => "Singapore",
						"SK" => "Slovakia",
						"SI" => "Slovenia",
						"SB" => "Solomon Islands",
						"SO" => "Somalia",
						"ZA" => "South Africa",
						"GS" => "South Georgia and The South Sandwich Islands",
						"ES" => "Spain",
						"LK" => "Sri Lanka",
						"SD" => "Sudan",
						"SR" => "Suriname",
						"SJ" => "Svalbard and Jan Mayen",
						"SZ" => "Swaziland",
						"SE" => "Sweden",
						"CH" => "Switzerland",
						"SY" => "Syrian Arab Republic",
						"TW" => "Taiwan, Province of China",
						"TJ" => "Tajikistan",
						"TZ" => "Tanzania, United Republic of",
						"TH" => "Thailand",
						"TL" => "Timor-leste",
						"TG" => "Togo",
						"TK" => "Tokelau",
						"TO" => "Tonga",
						"TT" => "Trinidad and Tobago",
						"TN" => "Tunisia",
						"TR" => "Turkey",
						"TM" => "Turkmenistan",
						"TC" => "Turks and Caicos Islands",
						"TV" => "Tuvalu",
						"UG" => "Uganda",
						"UA" => "Ukraine",
						"AE" => "United Arab Emirates",
						"GB" => "United Kingdom",
						"US" => "United States",
						"UM" => "United States Minor Outlying Islands",
						"UY" => "Uruguay",
						"UZ" => "Uzbekistan",
						"VU" => "Vanuatu",
						"VE" => "Venezuela",
						"VN" => "Viet Nam",
						"VG" => "Virgin Islands, British",
						"VI" => "Virgin Islands, U.S.",
						"WF" => "Wallis and Futuna",
						"EH" => "Western Sahara",
						"YE" => "Yemen",
						"ZM" => "Zambia",
						"ZW" => "Zimbabwe");
						return $countries;

	}
     public function getResistration($request, $response) {

     	if(isset($_SERVER['HTTP_REFERER'])) {
    $previous = $_SERVER['HTTP_REFERER'];
         } 
          $this->data['previous_url'] = $previous;
	 	
		 $this->data['countryList'] = $this->getAllCountry();
		  return $this->render($response,  'public/register/register.twig',$this->data);
		 
		 }

	 public function getContactus($request, $response) {
	 	
		 
		  return $this->render($response,  'public/contactus/contactus.twig',$this->data);
		 
		 }
		 
		 //code for reset password page
     public function resetPassword($request, $response){
		 
			$token=$request->getAttribute('token');
			//echo $token;exit;
			$this->data['token']=$token;
			return $this->render($response,  'public/forget_pass/reset_pass.twig',$this->data);
	
        }
	
}