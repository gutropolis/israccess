<?php
namespace App\Controllers;
use App\Models\User;
use App\Models\Usermeta;
use App\Tools\Auth;
  
use App\Models\Event;
use App\Models\EventGrComment;
use App\Models\EventGrFiles;
use App\Models\EventTicket;
use App\Models\Eventgroup;
use App\Models\Category;
use App\Models\Auditorium;
use App\Models\Order;
use App\Models\OrderItems;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v;

class UserController extends BaseController {
		
		
		
	  public function signIn($request, $response) {
 
			$AuthObj = new  Auth();
			$email = $request->getParam('email');
			$password = $request->getParam('password');
			
			if($email == '' ||  $password == '' ){
				$jsonData = array('status' => '0','msg' => 'S il vous plaît mettre un Email!!');
			}else{
			
						$user = $AuthObj->Userattempt(
							$request->getParam('email'),
							$request->getParam('password')
						);

		 
						if(!$user) {
					
							$jsonData = array('status' => '0','msg' => 'Désolé... vos identifiants sont incorrects');
							
							
						}else{
							
									
							$jsonData = array('status' => '1', 'msg' =>'Bienvenue sur Culturaccess.com');
							
						}
			}

			 	return $response->withHeader('Content-type','application/json')->write(json_encode($jsonData));
                exit();	 


		}
       public function forgetPwd($request, $response) {
 
		 
			$email = $request->getParam('email');
			$from=$email;
			$token_lenth=8;
			//$password = '123456';
			$token=$this->getToken($token_lenth);

			//$new_password = password_hash($password, PASSWORD_BCRYPT); 
			if($email == ''  ){
				$jsonData = array('status' => '0','msg' => 'Please put your Email!!');
			}else{
			
						  $UsermailExist = User::select('id','name','email')->where('email',$email)->first();
	
							if(!$UsermailExist)
							{
								 $jsonData = array('status' => '0','msg' => "Votre email n'existe pas ");
							}
							else
							{
								$userid = intval($UsermailExist['id']);
								
								$msgArra= array();
									$msgArra['name'] = $UsermailExist['name']; 
									$msgArra['email'] = $UsermailExist['email']; 
									//$msgArra['password'] = $password; 
									$msgArra['site_url'] = WEB_PATH; 
									$msgArra['link']='<a href='.WEB_PATH.'/reset_password/'.$token.'><h1>Click Here</h1> </a>';
									
									
									$subject ='Reset your password for Isral Access';

									//confirm ticket functionality
								/*
									$msgArra['auditorium_name'] = 'GESHER THEATER'; 
									$msgArra['auditorium_city'] = 'Tel-Aviv';
									$msgArra['productor_name'] = 'Israel Production '; 
									$msgArra['artist_name'] = 'Alavier Guedj'; 
									$msgArra['event_name'] = 'Alavier Guedj Rohande le'; 
									
									$msgArra['event_date'] = '20/06/2018';
									$msgArra['event_hour'] = '20h30';
									$msgArra['category_name'] = 'cat1';
									$msgArra['category_row'] = '8';
									$msgArra['category_seat'] = '2';
									$msgArra['total_price'] = '100';
									$msgArra['number_of_order'] = '4353453';
									$msgArra['client_nom'] = 'Ajay Thakur';
									$msgArra['client_address'] = 'Chandigarh';
									$msgArra['client_city'] = 'dehradoon';
									$msgArra['client_code'] = '175090';
									$msgArra['client_country'] = 'Nepal'; 
									$msgArra['client_id'] = '9879'; 
									*/
								 
								 User::where('id', $userid)->update(['token' => $token]);

								 sendEmail($from,$email,$subject,$msgArra, 'forget-pwd.html');



								 $jsonData = array('status' => '1', 'msg' => "Nous avons défini votre mot de passe, s'il vous plaît vérifier votre email !!");
							} 
			} 
			return $response->withHeader('Content-type','application/json')->write(json_encode($jsonData));
			exit();	 
		}

  //generate token 

		function getToken($length){
		 $token = "";
		 $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		 $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
		 $codeAlphabet.= "0123456789";
		 $max = strlen($codeAlphabet); // edited

		for ($i=0; $i < $length; $i++) {
			$token .= $codeAlphabet[random_int(0, $max-1)];
		}

		return $token;
	}
    public function registerShort($request, $response) {

       // $allPostPutVars = $request->getParsedBody();

       // $new_password = password_hash($allPostPutVars['password'], PASSWORD_BCRYPT);

        $Fullname=$request->getParam('firstname').$request->getParam('lastname');

        $create = User::create([

            'name' => $request->getParam('firstname'),

            //'username' => $allPostPutVars['lastname'],

            'email' => $request->getParam('email'),

            //'password' => $new_password,


            'type'=>'Member'

        ]);
				if($create->id > 0 ) {
			
					$jsonData = array('status' => "Enregistrement Enregistrer avec succès");
					
					
				}else{
					
					$jsonData = array('status' => "L'enregistrement a échoué");
					
				}
				
			    return $response->withHeader('Content-type','application/json')->write(json_encode($jsonData));
                exit();	
         
        // echo 'homw controller working';

    }
		
	 public function registerFull($request, $response) {
         //$allPostPutVars = $request->getParsedBody();

         $new_password = password_hash($request->getParam('password'), PASSWORD_BCRYPT);

         $Fullname=$request->getParam('firstname').$request->getParam('lastname');
         $name=$request->getParam('firstname');
         $firstname=$request->getParam('lastname');
		 $userEmail = $request->getParam('email');
	     $UsermailExist = User::select('id')->where('email',$userEmail)->first();
	
			if($UsermailExist)
			{
				 $jsonData = array('status' => '0','msg' => "Email existe déjà !!");
			}
			else
			{
				
				$create = User::create([

					 'name' => $name, 
					 'email' => $request->getParam('email'), 
					 'password' => $new_password,
					 'status' => '1', 
					 'type'=>'Member',
					 'created_on' => date('Y-m-d h:i:s')  

				 ]);
				 if(intval($create->id) > 0 ) {
					  
											$usermeta = new Usermeta();
											$usermeta->user_id = $create->id; 
											$usermeta->first_name = $firstname; 
											//$usermeta->last_name =  $request->getParam('lastname'); 
											$usermeta->address_1 =  $request->getParam('address'); 
											$usermeta->address_2 = $request->getParam('c_address'); 
											$usermeta->street = $request->getParam('firstname'); 
											$usermeta->ville = $request->getParam('ville'); 
											
											$usermeta->postal_code = $request->getParam('postal_code'); 
											$usermeta->phone_no = $request->getParam('telephon'); 
											$usermeta->dob = $request->getParam('year').'-'.$request->getParam('month').'-'.$request->getParam('day'); 
											$usermeta->country = $request->getParam('country');
											
											
											
											$usermeta->save(); 

											$msgArra= array();
											        $msgArra['first_name'] = $name; 
											        $msgArra['last_name'] = $firstname; 
											        $msgArra['address'] =  $request->getParam('address'); 
											        $msgArra['email'] =$userEmail;
											        $msgArra['telephon'] = $request->getParam('telephon'); 
											        $msgArra['message'] = 'abcdedf ';
											        $from = '';
											        $subject ='Your registration credemtoa;s jere ';
											       $email=$request->$userEmail;
											        $from=$request->$userEmail;

											 sendEmail($from,$email,$subject,$msgArra, 'register.html');
											
											  if(intval($create->id) > 0 ) {
													$_SESSION['isMember'] = 'Okay';
													$_SESSION['memberId'] = $create->id;
													$_SESSION['memberName'] =  $Fullname;
													$_SESSION['memberEmail'] = $request->getParam('email');
													$jsonData = array('status' => '1', 'msg' => "Vous avez enregistré avec succès !!");
													
													
													
												}else{
													
													$jsonData = array('status' => '0','msg' => 'Veuillez réessayer!!');
												}


				 } 
				
			}
         

         return $response->withHeader('Content-type','application/json')->write(json_encode($jsonData)); exit();	 
    }
	
	 public function updateEmail($request, $response){
		 $oldmail=$request->getParam('mymail');
		 $newmail=$request->getParam('nymail');
		 
		 //echo $oldmail;
		 //echo $newmail;
		 
		 $userid=$_SESSION['memberId']; 
		 if(isset($_SESSION['memberId']) && intval($_SESSION['memberId']) ){

            $UsermailExist = User::select('id')->where('email',$newmail)->first();
			
			if($UsermailExist)
			{
				 $jsonData = array('status' => '0','msg' => 'Email existe déjà !!');
			}
			else
			{
				User::where('id', $userid)->update(['email' => $newmail]);
				$jsonData = array('status' => '1', 'msg' => "L'email a changé avec succès !!'");
			}
		 }else {
		 
		   $jsonData = array('status' => '0', 'msg' => "Veuillez vous connecter pour cette action");
		 }
		  
		return $response->withHeader('Content-type','application/json')->write(json_encode($jsonData)); exit();	
	 }
	 
	  public function updatePassword($request, $response) {
		 $oldpass=$request->getParam('mypass');
		 $newpass=$request->getParam('nypass');
		 $old_password = password_hash($oldpass, PASSWORD_BCRYPT);
		 $new_password = password_hash($newpass, PASSWORD_BCRYPT);
		
			if(isset($_SESSION['memberId']) && intval($_SESSION['memberId']) ){
				 $userid=$_SESSION['memberId'];  
				 User::where('id',$userid)->update(['password' => $new_password]);
					$jsonData = array('status' => '1', 'msg' => 'Mot de passe ont changé avec succès!');
			 }else {
			 
			   $jsonData = array('status' => '1', 'msg' => 'Veuillez vous connecter pour cette action');
			 }
		 
		  
			return $response->withHeader('Content-type','application/json')->write(json_encode($jsonData)); exit();	
	 }
	  public function editUser($request, $response) {
		  //$allPostPutVars = $request->getParsedBody();
			if(isset($_SESSION['memberId']) && intval($_SESSION['memberId']) ){
							 $userid=$_SESSION['memberId'];  
							 $name=$request->getParam('firstname');
							 $firstname=$request->getParam('lastname');
								
							 $UsermetaExist = Usermeta::select('id')->where('user_id',$userid)->first();  
								if($name!=''){
									User::where('id', $userid)->update(['name' => $name]);
								}
								if($UsermetaExist){
									 $dob = $request->getParam('year').'-'.$request->getParam('month').'-'.$request->getParam('day');
									   Usermeta::where('user_id', '=', $userid)
										->update([
											//'first_name' => $firstname,
											//'last_name' => $request->getParam('lastname'),
											'first_name' => $firstname,
											'address_1' => $request->getParam('address'),
											'address_2' => $request->getParam('c_address'),
											'street' => $request->getParam('firstname'),
											'ville' => $request->getParam('ville'),
											'postal_code' => $request->getParam('postal_code'),
											'phone_no' => $request->getParam('telephon'),
											'dob' => $dob,
											'country' => $request->getParam('country'),
										]);
								} else{
									       $usermeta = new Usermeta();
											$usermeta->user_id = $userid; 
											$usermeta->first_name = $firstname; 
											//$usermeta->last_name =  $firstname; 
											$usermeta->address_1 =  $request->getParam('address'); 
											$usermeta->address_2 = $request->getParam('c_address'); 
											$usermeta->street = $request->getParam('firstname'); 
											$usermeta->ville = $request->getParam('ville'); 
											
											$usermeta->postal_code = $request->getParam('postal_code'); 
											$usermeta->phone_no = $request->getParam('telephon'); 
											$usermeta->dob = $dob; 
											$usermeta->country = $request->getParam('country'); 
											$usermeta->save();  
								} 
							    $jsonData = array('status' => '1', 'msg' =>'Votre profil a été mis à jour!!');
							 
			}else{
					$jsonData = array('status' => '1', 'msg' =>'Veuillez vous connecter pour cette action');
			}			
          
         

         return $response->withHeader('Content-type','application/json')->write(json_encode($jsonData)); exit();
	 
	  }
	  
	  
	  
	 
	// Logout

	public function LogOut($request, $response) {

		 @session_start();

		 session_unset($_SESSION['isMember']);

		 session_destroy();

		 return $response->withRedirect(base_url.'/home');		

    }
	public function myDashboard($request, $response) {
		
			$case=$request->getAttribute('case');  
			 
			if(isset($_SESSION['memberId']) && intval($_SESSION['memberId']) ){
					$userid=$_SESSION['memberId']; 
			}else{
				return $this->response->withStatus(200)->withHeader('Location', base_url.'/home'); 
				
			}
		   $this->data['countryList'] = $this->getAllCountry(); 
		   $userid=$_SESSION['memberId'];
		   $usermeta = Usermeta::where('user_id', '=', $userid)->first();
		   $user  = User::where('id', '=', $userid)->first();
		     $dob= $usermeta['dob'];  //1933-10-7
		 
		   if($dob!=''){
		 
		    
		   $dobArr = explode("-", $dob); 
		    $this->data['userdobY'] = $dobArr[0];
			$this->data['userdobM'] = $dobArr[1];
			$this->data['userdobD'] = $dobArr[2];
		   } 
		   
		   $this->data['userInfo'] = $user;
		   $this->data['userMeta'] = $usermeta;
		   $this->data['case'] = $case;
		   $bookArr = $this->bookingHistory();
		  
		   $orderItemHtm= $this->showOrderHistoryHtml($bookArr );
		   $recentOrderItem= $this->recentTicketItem($bookArr );
		   $this->data['bookingHis'] = $orderItemHtm;
		   $this->data['recentbooking'] = $recentOrderItem;
		   $this->data['h1'] = 'Bonjour';  
		  
		   return $this->render($response,  'public/auth/myprofile.twig',$this->data); 
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
	
	//SignIn for order page
	public function signInOrder($request, $response) {
 
			$AuthObj = new  Auth();
			$email = $request->getParam('email');
			$password = $request->getParam('password');
			
			if($email == '' ||  $password == '' ){
				$jsonData = array('status' => '0','msg' => "S'il vous plaît mettez l'email et le mot de passe!");
			}else{
			
						$user = $AuthObj->Userattempt(
							$request->getParam('email'),
							$request->getParam('password')
						);

		 
						if(!$user) {
					
							$jsonData = array('status' => '0','msg' => "Email de l'utilisateur ou mot de passe incorrect !!");
							
							
						}else{
							
									
							$jsonData = array('status' => '1', 'msg' => 'Connexion réussie');
							
						}
			}

			 	return $response->withHeader('Content-type','application/json')->write(json_encode($jsonData));
                exit();	 


		}
		 public function registerOnCart($request, $response) {
         //$allPostPutVars = $request->getParsedBody();

         $new_password = password_hash($request->getParam('password'), PASSWORD_BCRYPT);

         $Fullname=$request->getParam('firstname').$request->getParam('lastname');
         $name=$request->getParam('firstname');
         $firstname=$request->getParam('lastname');
		 $userEmail = $request->getParam('email');
	     $UsermailExist = User::select('id')->where('email',$userEmail)->first();
	
			if($UsermailExist)
			{
				 $jsonData = array('status' => '0','msg' => 'Email existe déjà !!');
			}
			else
			{
				
				$create = User::create([

					 'name' => $name, 
					 'email' => $request->getParam('email'), 
					 'password' => $new_password,
					 'status' => '1', 
					 'type'=>'Member',
					 'created_on' => date('Y-m-d h:i:s')  

				 ]);
				 if(intval($create->id) > 0 ) {
					  
											$usermeta = new Usermeta();
											$usermeta->user_id = $create->id; 
											$usermeta->first_name = $firstname; 
											//$usermeta->last_name =  $request->getParam('lastname'); 
											$usermeta->address_1 =  $request->getParam('address'); 
											$usermeta->address_2 = $request->getParam('c_address'); 
											$usermeta->street = $request->getParam('firstname'); 
											$usermeta->ville = $request->getParam('ville'); 
											
											$usermeta->postal_code = $request->getParam('postal_code'); 
											$usermeta->phone_no = $request->getParam('telephon'); 
											$usermeta->dob = $request->getParam('year').'-'.$request->getParam('month').'-'.$request->getParam('day'); 
											$usermeta->country = $request->getParam('country');
											
											
											
											$usermeta->save();  
											
											  if(intval($create->id) > 0 ) {
													$_SESSION['isMember'] = 'Okay';
													$_SESSION['memberId'] = $create->id;
													$_SESSION['memberName'] =  $Fullname;
													$_SESSION['memberEmail'] = $request->getParam('email');
													$jsonData = array('status' => '1', 'msg' =>'Votre inscription a bien été enregistrée`');
													
													//email functionality

													 $msgArra= array();
											        $msgArra['first_name'] = $request->getParam('firstname'); 
											        $msgArra['last_name'] = $request->getParam('lastname'); 
											        $msgArra['address'] = $request->getParam('address'); ;
											        $msgArra['email'] =$request->getParam('email');
											        $msgArra['telephon'] =$request->getParam('telephon'); ;
											        $msgArra['message'] = ' ';
											       $msgArra['site_url'] = WEB_PATH;
											        $from = '';
											        $subject ='Your logni credemtoa;s jere ';
											       $email=$request->getParam('email');
											        $from=$request->getParam('email');
											       //sendEmail($from,$email,$subject,$msgArra, 'register.html');
													sendEmail($from,$email,$subject,$msgArra, 'register.html');
													
												}else{
													
													$jsonData = array('status' => '0','msg' => 'Veuillez réessayer!!');
												}


				 } 
				
			}
         

			 return $response->withHeader('Content-type','application/json')->write(json_encode($jsonData)); exit();	 
		} 
		public function bookingHistory(){
		 
			$orderItemHtm='';
			$ordersArr=array();
		
			if(isset($_SESSION['memberId']) && intval($_SESSION['memberId']) ){
				$userid=$_SESSION['memberId']; 

				$orderArr = Order::where('customer_id',$userid)->get();
				foreach($orderArr as $ord){
				
				 $order_id  = $ord->id;
				 if($order_id > 0){		
						/* Get order Items Detail here */
						
							$extraData=array();
							$bookingfees='1';
							$giftcard='1';
							
							 $orderEvents = OrderItems::select('product_id')->where('order_id',$order_id)->distinct()->get();
							  
							 if(count($orderEvents) > 0 ){
								$dataShop=array();
								$dataShop['type'] ='event';
								$total_ticket_price =0;
								 foreach($orderEvents  as $item){
									 
								    $event_id = $item['product_id'];
									if(intval($event_id)> 0){  
									 //Event Detail have Added Here //
										$eventArr = Event::where('id',$event_id)->get();
										 
										$event_list = getEventList($eventArr); 
										
										foreach($event_list  as $ev){
											
											$middleFormat = strtotime($ev['date']);  
												 
										    $dataShop['time_range']  =date('H:i', $middleFormat);
											//$dataShop['type'] ='event';
											$dataShop['order_id'] ='IXXI-'.$order_id;
											$dataShop['event_name'] = $ev['title'];
											$dataShop['artist_name'] = $ev['artist_name'];
											$dataShop['event_auditorium'] = $ev['event_auditorium'];
											$dataShop['event_auditorium_address'] = $ev['event_auditorium_address'];
											$dataShop['event_ticket_type'] = $ev['event_ticket_type'];
											$dataShop['artist_name'] = $ev['artist_name'];
											$dataShop['event_city'] = $ev['event_city'];  	
											$dataShop['event_group_picture'] = EVENTGROUP_WEB_PATH.'/'.$ev['event_group_picture'];  
											$dataShop['event_date'] =  $ev['date_d'].'/'.$ev['date_m'];  
											$dataShop['event_date_com'] =  date('Y-m-d', $middleFormat); 
											
										}
										 
										/*=========Add Ticket row, Price, others ==========*/
										 
												$rowsItems = OrderItems::where('order_id',$order_id)->where('product_id', $event_id)->get();
												 $totalQuantity =0;
												 if(count($rowsItems) > 0 ){ 
												  $rowArr=array();
													foreach($rowsItems  as $item){
													
														$rowArr['ticket_row']=$item['ticket_row'];
														$qtx =$item['quantity'];;
														$price =$item['price'];;
														$rowArr['quantity']=$item['quantity'];
														$totalQuantity += intval($item['quantity']);
														$rowArr['price']=$item['price'];
														$rowArr['ticket_row']=$item['ticket_row'];
														$rowArr['ticket_category'] = $item['ticket_category']; 
														$dataShop['booking_time'] = $item['booking_time'];
														
														$totalA = $qtx*$price;
													 
														$total_ticket_price =$total_ticket_price +$totalA;
														 
														$dataShop['ticket_info'][] =$rowArr;
														
													}
												 }
												 $dataShop['total_seats'] = $totalQuantity;
										
										/* ===========End here ===========================*/
										 	
									}	
										
								 } 	 
							 }
							 /*=========Fetch Other Item Price ==========*/
										$rowsItems = OrderItems::where('order_id',$order_id)->where('product_id','0')->get();
										if(count($rowsItems) > 0 ){ 
										  $rowArr=array();
											foreach($rowsItems  as $item){
												
											 
												$qtx =$item['quantity'];;
												$price =$item['price'];;
												 
												$totalA = $qtx*$price;
												
												$total_ticket_price =$total_ticket_price +$totalA;
												 
												 
												
											}
										 }
										 $dataShop['total_amount'] = $total_ticket_price;
							  /*================End here =======================*/
							 
							  
						  /* End here       */
						  
						$ordersArr[] = $dataShop;
				 }
					
				} 
				//$orderItemHtm= $this->showOrderHistoryHtml($ordersArr );
			}
		 
		  return $ordersArr;
 
			//$_SESSION['orderarr']=$orderArr;
			
			//return   $this->showOrderHtml($orderArr);   
		 
	}
	
	public function showOrderHistoryHtml($orderArr ){
		  
		$bodyCar  =''; 
			 $totalAmountTicket='';
			 
			 //Add Car$tt System here //
						$bodyCar .=' <div class="carttable">';
							  if(count($orderArr)>0){
								  
								  $i=0;
								  $dataC ='';
									foreach($orderArr as $cartItem){
										$i=$i+1;
										if($cartItem['type'] == 'event'){
											 
												if(date('Y-m-d') > $cartItem['event_date_com'] ){
													
														  $dataC.='<div class="orderrow">
																<h5>'.$i.'</h5>
																<div class="orderCol">
																	<div class="orderinner">
																	<div class="orderColLt">
																		<h4>éVèNEMENT</h4>
																	</div>
																	<div class="orderColRt">
																		<p><span class="red">'.$cartItem['artist_name'].'</span> |<strong> '.$cartItem['event_name'].' </strong>|<span class="red"> '.$cartItem['event_auditorium'].' '.$cartItem['event_city'].' / DIM '.$cartItem['event_date'].' '.$cartItem['booking_time'].'</span>  </p>
																	</div>
																	</div>
																	<div class="orderinner"> 
																   <div class="ltOrderrow">
																	<div class="orderColLt">
																		<h4>N° de commande</h4>
																	</div>
																	<div class="orderColRt">
																		<p><span class="red">'.$cartItem['order_id'].'</span>  </p>
																	</div>
																   </div>
																   <div class="rtOrderrow">
																	<div class="orderColLt">
																		<h4>montant de  la commande</h4>
																	</div>
																	<div class="orderColRt">
																		<p><span class="red">'.$cartItem['total_amount'].'NIS / '.$cartItem['total_seats'].' Places </span>  </p>
																	</div>
																   </div>
																	</div>
																	 
																 
																 
																</div>
															</div>';
														 
												} 
										
										}
								 
									} 
									if($dataC == ''){ 
										 $msgP="<h5 class='text-pink'>VOUS N’AVEZ AUCUNE COMMANDE EN COURS</h5><h5>C’EST LE MOMENT DE FAIRE UN TOUR SUR LE SITE  ET DE SéLECTIONNER VOS PROCHAINS éVéNEMENTS</h5>";
										 $dataC.= $msgP  ;  
									}
									$bodyCar  .=$dataC;									
							  }else{ 
									$bodyCar.='<h5 class="text-pink">VOUS N’AVEZ AUCUNE COMMANDE EN COURS</h5><h5>C’EST LE MOMENT DE FAIRE UN TOUR SUR LE SITE  ET DE SéLECTIONNER VOS PROCHAINS éVéNEMENTS</h5>';  
							  }
						$bodyCar  .=' </div>';
					
		 return  $bodyCar;
		
	}
	public function recentTicketItem($orderArr){
		  
			 $totalAmountTicket='';
		     
			  if(count($orderArr)>0){
				 $bodyCar .=' <div class="carttable">';
				  $i=0;
					foreach($orderArr as $cartItem){
						$i=$i+1;  
						if($cartItem['type'] == 'event'){
							
							 if($cartItem['event_date_com']  >= date('Y-m-d')){  
															$bodyCar.='<div class="orderrow">
																<h5>'.$i.'</h5>
																<div class="orderCol">
																	<div class="orderinner">
																	<div class="orderColLt">
																		<h4>éVèNEMENT</h4>
																	</div>
																	<div class="orderColRt">
																		<p><span class="red">'.$cartItem['artist_name'].'</span> |<strong> '.$cartItem['event_name'].' </strong>|<span class="red"> '.$cartItem['event_auditorium'].' '.$cartItem['event_city'].' / DIM '.$cartItem['event_date'].' '.$cartItem['booking_time'].'</span>  </p>
																	</div>
																	</div>
																	<div class="orderinner"> 
																   <div class="ltOrderrow">
																	<div class="orderColLt">
																		<h4>N° de commande</h4>
																	</div>
																	<div class="orderColRt">
																		<p><span class="red">'.$cartItem['order_id'].'</span>  </p>
																	</div>
																   </div>
																   <div class="rtOrderrow">
																	<div class="orderColLt">
																		<h4>montant de  la commande</h4>
																	</div>
																	<div class="orderColRt">
																		<p><span class="red">'.$cartItem['total_amount'].'NIS / '.$cartItem['total_seats'].' Places </span>  </p>
																	</div>
																   </div>
																	</div>
																	<div class="orderinner">
																	<div class="orderColLt">
																		<h4>Placement </h4>
																	</div>
																	<div class="orderColRt orderColRt-block">';
															$ticketInfo =array();		
															$ticketInfo = 	$cartItem['ticket_info'];
															if(count($ticketInfo) > 0){
																foreach($ticketInfo as $tick){
																	$bodyCar.='<p><span class="red"> Rang '.$tick['ticket_row'].' </span><span class="red"> Place '.$tick['quantity'].' </span><span class="red"> '.$tick['ticket_category'].'</span>  </p>';	
																}
															}
																		
												$bodyCar.='			</div>
																	</div>
																	<div class="orderinner">
																	<div class="orderColLt">
																		<h4>Impression De mes E-Tickets </h4>
																	</div>
																	<div class="orderColRt">
																		<div class="orderColLt1">
																		<p>Les E-Tickets sont munis d’un code barre qui autorise l’accès à l’événement à un seul spectateur.
						En cas de perte ou d’une double duplication seule la première personne détentrice du billet pourra accéder à l’évènement. </p></div>
						<div class="imgcon"><a href="#"><img src="'.WEB_PATH.'/assets/assets/img/ticket.png"></a></div>

																	
																	</div>
																	</div>
																</div>
															</div>'; 
							 
							 
							 
							 }
							 
							 
							 
						}
						
					}
					$bodyCar .=' </div>'; 
			  }else{
					 $bodyCar.='<h5 class="text-pink">AUCUNE Reservation en cours.</h5><h5>C’EST LE MOMENT DE FAIRE UN TOUR SUR LE SITE  ET DE SéLECTIONNER VOS PROCHAINS éVéNEMENTS </h5>';  
			  }
			  
		 return  $bodyCar;
	}
	
	 public function sendingemail($request, $response) {
	 	$subject=$request->getParam('subject');
	 	$message=$request->getParam('message');

			$email='ajaythakur9329@rediffmail.com';
			$from='';

			$msgArra= array();

			$msgArra['email'] = $email;
			$msgArra['message'] = $message;
			$msgArra['site_url'] = WEB_PATH;
			sendEmail($from,$email,$subject,$msgArra,'confirmation-ticket.html');

			$jsonData = array('status' => '1', 'msg' =>'E-mail envoyé avec succès');

			return $response->withHeader('Content-type','application/json')->write(json_encode($jsonData));
	 	 exit();
	 }
	
	public function SendMailByContact($request, $response) {
		$firstname = $request->getParam('firstname');
        $lastname = $request->getParam('lastname');
		$message=$request->getParam('message');
		$email=$request->getParam('email');
		$to =  $this->data['admin_email'];
		 
		$subject='Email by contact us';
		 $msgArra= array();
        $msgArra['first_name'] = $firstname;
        $msgArra['last_name'] = $lastname;
        $msgArra['email'] = $email;
        $msgArra['message'] = $message;
         $msgArra['site_url'] = WEB_PATH;
		 sendEmail($email,$to,$subject,$msgArra,'contact_us.html');
 
		$jsonData = array('status' => '1', 'msg' =>'E-mail envoyé avec succès');

		return $response->withHeader('Content-type','application/json')->write(json_encode($jsonData));
		exit;

	}
	
	//code for reset password 
	public function PasswordReset($request, $response) {

		$newPassword=$request->getParam('npass');
		$token=$request->getParam('token');
			$new_password = password_hash($newPassword, PASSWORD_BCRYPT);

		$TokenExist = User::where('token', $token)->first();
		if(!$TokenExist)
		{
			
			//echo 'your passowrd reset successfully';
			//exit;
			 $jsonData = array('status' => '0', 'msg' => "
Quelque chose s'est mal passé!!");
			 
		}	
		else
		{
			User::where('token', $token)->update(['password' => $new_password]);
           $jsonData = array('status' => '1', 'msg' => "
Réinitialiser le mot de passe !!");
		}
		 

		//echo 'your passowrd reset successfully';

		return $response->withHeader('Content-type','application/json')->write(json_encode($jsonData));
		exit;
		}
			

			public function AddCommunity($request, $response) {
				$firstname=$request->getParam('firstname');
				$lastname=$request->getParam('lastname');
				$address=$request->getParam('address');
				$email=$request->getParam('email');
				$telephon=$request->getParam('telephon');
				$message=$request->getParam('message');

				$to =  $this->data['admin_email'];
		 
		$subject='Email by AddCommunity';
		 $msgArra= array();
        $msgArra['first_name'] = $firstname;
        $msgArra['last_name'] = $lastname;
        $msgArra['email'] = $email;
        $msgArra['address'] = $address;
        $msgArra['message'] = $message;
         $msgArra['site_url'] = WEB_PATH;
		 sendEmail($email,$to,$subject,$msgArra,'community-access.html');
 
		$jsonData = array('status' => '1', 'msg' =>'E-mail envoyé avec succès');

		return $response->withHeader('Content-type','application/json')->write(json_encode($jsonData));
		exit;



				


		
		}

}