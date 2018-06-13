<?php
namespace App\Controllers;
use App\Models\User;
use App\Models\Event;
use App\Models\EventGrComment;
use App\Models\EventGroupRole;
use App\Models\EventSeatCategories;
use App\Models\EventGrFiles;
use App\Models\EventTicket;
use App\Models\Eventgroup;
use App\Models\Category;
use App\Models\Auditorium;
use App\Models\EventTime;
use App\Models\City;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v;

 
class EventgroupController extends BaseController{

     
	public function getEventDetailByID($request, $response){
  
			$event_list=array(); 
			$event_grp_commentList=array();
			$event_grp_imageList=array();
			
			 $eventgroupid=$request->getAttribute('id'); //fetch the category id
			//$eventgroupid='2'; //fetch the category id
		    $event_group_list=  Eventgroup::where('id',$eventgroupid) 
											   ->get();
		    $commentList=  EventGrComment::where('eventgroup_id',$eventgroupid)->get();
			$event_grp_commentList=  getCommentList($commentList);
			 
			$event_grp_vidList   =  EventGrFiles::where('eventgroup_id',$eventgroupid)->where('file_type','vid')->get();
			$event_grp_imgList   =  EventGrFiles::where('eventgroup_id',$eventgroupid)->where('file_type','img')->get();
	        $eventgrpRoles   =  EventGroupRole::where('eventgroup_id',$eventgroupid)->get();   

			 $dataSlider = array();
			 $dataEvents=array();
			 $dataEventRepresentive=array();
			 $dataEventAuditorium=array();
			 $category_id=0;

			 if(count($event_group_list) > 0 ){
					 foreach($event_group_list as $get) { 
						 $array_data = array();
						  $evgrpID = $get['id'];
						 $array_data['event_group_id']  = $get['id'];
						 $array_data['group_picture']  = $get['group_picture'];
						  $array_data['group_thumbnail']  = $get['group_thumbnail'];
						 $array_data['event_group_picture']  = $get['group_picture'];
						 $array_data['event_group_title']  = html_entity_decode($get['title']);
						 $array_data['event_group_price_min']  = intval($get['price_min']);
						 
						 $array_data['event_group_price_min']  = intval($get['price_min']);
						 $array_data['event_group_description']  = htmlspecialchars_decode($get['description']);  
						 
						 $array_data['en_savoir_block1_name']  = htmlspecialchars_decode($get['en_savoir_block1_name']);  
						 $array_data['en_savoir_block2_name']  = htmlspecialchars_decode($get['en_savoir_block2_name']);  

						 $array_data['event_group_desc']  = htmlspecialchars_decode($get['en_savoir_desc1']);  
						 $array_data['event_group_desc2']  = htmlspecialchars_decode($get['en_savoir_desc2']);  
						 
						 $array_data['event_group_artist_name']  = $get['artist_name'];
						 $array_data['event_group_author_name']  = $get['author_name'];  
						 $array_data['event_group_productor_name']  = $get['productor_name'];
						 $array_data['event_group_director_name']  = $get['director_name'];
						 //
						 
					 
						 
						 
						 $array_data['event_artist_id']  = $get['artist_id'];
						  
					     $eventArr=$get['events'] ;
						 $dataEvents=$get['events'] ;
						 $city_name='';
						 $contributor='';
						 $director='';
						 $auditorium='';
						 if(count($eventArr) > 0 ){



							 //Event Loop Start here
						 	$c= count($eventArr);
						 	$i=0;
						 	 $acity=array();
							 foreach( $eventArr as $ev){
								 $i=$i+1;
								 
								  $contributor=$contributor.$ev['contributor'].",";
								  $director=$director.$ev['director'].",";
								  $auditorium=  $auditorium.$ev['auditorium']['name'].",";
								

								     if (!in_array($ev['city']['name'], $acity))
									  { 
									     	if($c!= $i){
										 	    $city_name= $city_name.$ev['city']['name'] ." - ";  
										 	} else{
										 		 $city_name= $city_name.$ev['city']['name'] ." ";  
										 	}

										 	$acity[]=$ev['city']['name'];

									  }
								
								 
								    
								  //Loop For city
								 
							 }
						  
						 }
						 $array_data['event_group_auditorium']  =  $auditorium;
						 $array_data['event_group_contributor']  =  $contributor;
						 $array_data['event_group_director']  =  $director;
						 $array_data['event_group_cityname']  =  $city_name;  
						 
						 
						 //Event Category Detail Start Here //
						 $eventCategoryArr=$get['category'];
						 $eventArtistArr=$get['artist'];
						 $array_data['event_category_title']  =  $eventCategoryArr['name'];
						 $array_data['event_category_id']  =  $eventCategoryArr['id'];
						 $category_id= $eventCategoryArr['id'];
						 $array_data['event_artist_name']  =  $eventArtistArr['name'];
						 $array_data['event_artist_pic']  =  $eventArtistArr['user_picture'];
						 $array_data['event_group_begin']  = hr_date($get['date_begin']);
						 $array_data['event_group_end']  = hr_date($get['date_end']);
						  
						 $dataSlider[] = $array_data;
					 } 
			 } 
           
			$eventList =  Event::where('eventgroup_id',$eventgroupid)->where('status',1)->orderBy('date', 'asc')->limit(3)->get();
			 
			 if(count($eventList)> 0){
				   foreach( $eventList as $ev){
								$array_data = array();

								$array_data['event_id']=  $ev['id'];

								$event_id = $ev['id'];
								$evTimeArr = EventTime::where('event_id',$event_id)->get();
							 $evT=array();
							 $evTS=array();
							 if(count($evTimeArr) > 0 ){
							 foreach($evTimeArr as $evTime){

								 $evTS[] =   date('H:i', strtotime($evTime['event_time'])); 

							 }
							 $evT[]=$evTS;
							 
							 }
							    $array_data['event_times']  = $evTS;
							    

								$array_data['event_group_id']=  $ev['eventgroup_id'];
								$array_data['event_title']=  $ev['title'];
								$array_data['event_date']=  $ev['date'];
								$array_data['city_name']=  $ev['city']['name'];
								
                                $middleFormat = strtotime($ev['date']);  
								$weekDay= date('N', $middleFormat);
							    $array_data['rep_day']=   $this->DaysLg($weekDay);
								$array_data['rep_day_f']=  date('d/m', $middleFormat);
								$array_data['rep_day_time']=  date('H:i', $middleFormat);
 
								$array_data['time_range']  =date('H:i', $middleFormat);
								
								 
						 
								$dataEventRepresentive[] =  $array_data;
							  
							  //Regarding to Auditorium Listing
							  $array_auditorium = array();
							  
							  $array_auditorium['auditorium_id']=  $ev['auditorium']['id'];
							  $array_auditorium['auditorium_name']= $ev['auditorium']['name'];
							  
							  $array_auditorium['auditorium_background_file']= $ev['auditorium']['background_file'];
							  $array_auditorium['auditorium_width']= $ev['auditorium']['width'];
							  $array_auditorium['auditorium_height']= $ev['auditorium']['height'];
							  $array_auditorium['auditorium_address']= $ev['auditorium']['address'];
							   $array_auditorium['auditorium_access']= $ev['auditorium']['access'];
							  $array_auditorium['auditorium_waze_name']= $ev['auditorium']['waze_name'];
							  $array_auditorium['auditorium_detail']= $ev['auditorium']['detail'];
							  $array_auditorium['auditorium_lat']= $ev['auditorium']['lat'];
							  $array_auditorium['auditorium_long']= $ev['auditorium']['lng'];
							  $array_auditorium['auditorium_type']= $ev['auditorium']['type'];
							 
							  $dataEventAuditorium[] =  $array_auditorium;
					  }
				 
				  
			 }
			
			// print_r($dataEventRepresentive);
			 //exit;
			 
			 /*  Fetch all Auditorium and their events */
			 $auditoriumList =  Event::select('auditorium_id')->where('eventgroup_id',$eventgroupid)->where('status',1)->orderBy('date', 'asc')->get();
			 //print_r($auditoriumList);
			 $auditorium_ids=array();
			 foreach ($auditoriumList as $audi){  
			 
			          $auditorium_ids[] = $audi['auditorium_id'];     
			 }
		     array_unique($auditorium_ids);  
			 $au = array(); 
			 if(count($auditorium_ids) > 0 ){
			    
				 $auditoriumMaps = array();
				 foreach($auditorium_ids as $audsingle){
				  $dataP=array();
				   $eventinformation='';
				    $auditoriumdetail =   Auditorium::where('id',$audsingle)->first();
					 
					$dataP['name'] = $auditoriumdetail['name']; 
					$eventinformation = $eventinformation.'<b>Auditorium Name:'.$auditoriumdetail['name'].'</b><br />';
					$dataP['background_file'] = $auditoriumdetail['background_file'];
					$dataP['address'] = $auditoriumdetail['address'];
					$eventinformation = $eventinformation.'<b>Address:'.$auditoriumdetail['address'].'</b><br />';
					$dataP['lat'] = $auditoriumdetail['lat'];
					$dataP['lng'] = $auditoriumdetail['lng']; 
					
				    $audiEventArr= Event::where('eventgroup_id',$eventgroupid)->where('auditorium_id',$audsingle)->where('status',1)->orderBy('date', 'asc')->get();
                    $i=0;
					if(count($audiEventArr) > 0){
					++$i;
					 $audiEventDet =array();
					    foreach ($audiEventArr as $audiEv){
								 $audiEventDet['event_title'] = $audiEv['title'];
								  $audiEventDet['event_city'] = $audiEv['city']['name'];
								 $eventinformation = $eventinformation.'<b>Event:'.$audiEv['title'].'</b><br />';
								 $audiEventDet['event_date'] = $audiEv['date'];
								 if($audiEv['date']!=''){
											$middleFormat = strtotime($audiEv['date']);  
											$dateF= date('d F Y', $middleFormat);  
											$eventinformation = $eventinformation.'<b>Date:</b>'.$dateF.'</b><br />';
								 }
								 
								 $eventinformation = $eventinformation.'<b>City:</b>'.$audiEventDet['event_city'].'</b><br />';
								 $eventinformation = $eventinformation.'<b>Artist:</b>'.$audiEv['artist_name'].'</b><br />';
								 
								 $audiEventDet['event_description'] = $audiEv['description'];
								 $audiEventDet['event_artist_name'] = $audiEv['artist_name']; 
								 $dataP['event'][] = $audiEventDet;
								 if(count($audiEventArr)!=$i){
									$eventinformation = $eventinformation.'<b><hr /></b><br />';
								 }
					    }
					
					}//echo $eventinformation;
					 $dataP['event_info'] = $eventinformation;
						$au[]=		$dataP;			 
				 } 
				
			 }
			 
			  /*End here  */
			 
			   /** Add date from and to for event group b event calendar **/
            $fromToEventDate='';

            //->where('date', '>', $currentD)
           $currentD= date('Y-m-d');
           //->where('date', '>', $currentD)
             $query = Event::where('eventgroup_id',$eventgroupid)
			                     ->where('status','1')
			                     ->orderBy('date', 'asc');
			 $eventDArr = $query->get();
            
            if(count($eventDArr) > 0 ){
            	$totE = count($eventDArr);
            	if($totE > 1){
            		 $fromToEventDate='DU&nbsp; '.date('d/m/Y', strtotime($eventDArr[0]['date'])).'&nbsp;&nbsp;AU '.date('d/m/Y', strtotime($eventDArr[$totE-1]['date'])) ;

            	}else{
					$fromToEventDate='LE&nbsp;'.date('d/m/Y', strtotime($eventDArr[0]['date']));  
            	}
            	 

            }

		    $arrCategory  =  Category::where('id',$category_id)->get()->first();; //for getting category
			$this->data['catDetail'] = $arrCategory; 
			 $this->data['eventListRep'] = $dataEventRepresentive;
			 $this->data['evgrpEventRange']=$fromToEventDate;
			$this->data['auditoriumList'] = $dataEventAuditorium; 
			
			$this->data['eventdetailList'] =$dataSlider;
			$this->data['mapAudiEventList'] = $au; 
			$this->data['commentEventGrpList'] = $event_grp_commentList; 
			$this->data['commentEventGrpVideoList'] = $event_grp_vidList; 
			$this->data['commentEventGrpImageList'] = $event_grp_imgList; 
			$this->data['eventgrpRolList'] =$eventgrpRoles;
			 
			
			$this->data['egImgURL']  = EVENTGROUP_WEB_PATH.'/';
			$this->data['eventdetailImgURL']  = EVENTGROUP_EN_SAVOIR_WEB_PATH.'/';
			$this->data['event_fb_url']  = WEB_PATH."/eventgroup/".$eventgroupid;   
		    $this->data['event_fb_encode_url']  = urlencode(WEB_PATH."/eventgroup/".$eventgroupid);   
			$this->data['event_twitter_desc']  = urlencode(stripUnwantedTagsAndAttrs($array_data['event_group_description'])); 
			$this->data['videoImgURL']  = EVENTGROUP_VID_WEB_PATH.'/';
			
			
			$this->data['evgrpid'] = $evgrpID;
            $this->data['eventCalendar'] = $this->getCalendarActivity($eventgroupid);

            $totE = count($eventDArr);
             $this->data['totalEvent'] = $totE;
			return $this->render($response, 'public/eventgroup/event_group_detail.twig',$this->data); 
    }
  
  
  function strafter($string, $substring) {
     $pos = strpos($string, $substring);
    if ($pos === false)
      return $string;
      else  
       return(substr($string, $pos+strlen($substring)));
     }
function strbefore($string, $substring) {
  $pos = strpos($string, $substring);
  if ($pos === false)
   return $string;
  else  
   return(substr($string, 0, $pos));
}

 
	public function searchEventGroups($request, $response)
	{
		
		//Get url parameters
		 	$middleFormat ='';
				$dateparameter= $request->getParam('daterange');
				//$startdate=$dateFrom;
				if($dateparameter!= ''){
				$startdate=$this->strbefore($dateparameter,'-');
				$enddate=$this->strafter($dateparameter,'-');
				$dateFrom=date("d-m-Y",strtotime($startdate));
				$dateEnd=date("d-m-Y",strtotime($enddate));
			}
				//echo $dateFrom.'<br/>';
				//echo $dateEnd;
				//exit;
				//$dateTo='';
			    $dataSlider=array();
				$typeName='';
				$type='';
				 if( $request->getParam('location') != null)
				 {
				 	$location = $request->getParam('location');

				 	 }
				 	 else
				 	 	{ 
				 	 	 $location = null;
				 	 	}
				  if( $request->getParam('type') != null)
				  {

				  $type = $request->getParam('type'); 

					if(intval($type) > 0 )
					{
						  $cateArr =      Category::where('id',$type)->first();  
						  $typeName = $cateArr['name'];
					  }
					  
				  }else{  
				    $type = null;
				  }
					if( $dateFrom != ''){
						$filter_date = $dateFrom; //echo  $filter_date; ;
						
						
						if($filter_date !=''){		   
									$middleFormat = strtotime($filter_date);  
									$dateF= date('Y-m-d', $middleFormat);  
									$dateDisplayFormat= date('d F Y', $middleFormat);
									$mdFormat = strtotime($dateEnd);
									$dateE= date('Y-m-d', $mdFormat);
									$dateEndDisplayFormat= date('d F Y', $mdFormat);
									$dateFrom= $dateF." 00:00:00";
									$dateTo= $dateE." 23:59:59";
									
									
									  $this->data['searchStartDisplayDate']  = $dateDisplayFormat;
									 $this->data['searchEndDisplayDate']  = $dateEndDisplayFormat;
							
									 $this->data['searchFilterDate']  = $dateparameter;
								}
								else
								{ 
							$this->data['searchDisplayDate']  ='';
							}						
					}else{  
						$filter_date = null;
						$dateFrom=  '';
						$dateTo= '';
					}
				  
		         $this->data['searchLocation']  = $location;
				 $this->data['searchType']  = $typeName;
				 
		 
				$page =$request->getParam('page');
				$per_page = 6;
				$page = (isset($page) && is_numeric($page) ) ? $page : 1;
				
				$start = ($page-1) * $per_page;
		
				// query for seach where('title', 'LIKE', '%'. 'For' .'%')->
				       $auditorium_ids=array();
					  $query = Event::query(); 

  
					   
						if( $request->getParam('location') != null){
				                $arrnotfound=array();
					
								$arrnotfound[]='-1';
								$location = $request->getParam('location'); 
 								$locatinname=City::select('name')->where('id',$location)->first();
								$auditoriumArr =Auditorium::select('id')->where('address', 'LIKE', '%'.$locatinname['name'].'%')->get();
								$auditorium_ids=array();
								foreach ($auditoriumArr as $audi){  $auditorium_ids[] = $audi['id'];  }
							
								if(count($auditorium_ids) > 0){ 
								    $query = $query->wherein('auditorium_id',$auditorium_ids);
									
								}else{  
									   $query =$query->wherein('auditorium_id',$arrnotfound);
								}
								 
					   }
					   if( $request->getParam('filter-date')  != null){
					   
								
								 $dateparameter= $request->getParam('filter-date');  // works
								$startdate=$this->strbefore($dateparameter,'-');
				$enddate=$this->strafter($dateparameter,'-');
				$dateFrom=date("d-m-Y",strtotime($startdate));
				$dateEnd=date("Y-m-d",strtotime($enddate));
								$filter_date=$dateFrom;
								if($filter_date !=''){		   
									$middleFormat = strtotime($filter_date);  
									$dateF= date('Y-m-d', $middleFormat);
									$dateDisplayFormat= date('d F Y', $middleFormat);
									$mdFormat = strtotime($dateEnd);
									$dateE=date('Y-m-d', $mdFormat);
									$dateEndDisplayFormat= date('d F Y', $mdFormat);
									$dateFrom= $dateF." 00:00:00";
									$dateTo= $dateE." 23:59:59";
									$this->data['searchFilterDate']  = $dateparameter;
									 $this->data['searchStartDisplayDate']  = $dateDisplayFormat;
									 $this->data['searchEndDisplayDate']  = $dateEndDisplayFormat;
									
								}
							 
								
						$query =$query->whereBetween('date', [$dateFrom, $dateTo]);		  
								
					   }
					   
					  
						$eventArr = $query->get();
						 
                       $event_grp_ids=array();
						foreach ($eventArr as $even){  $event_grp_ids[] = $even['eventgroup_id'];  } 
						//print_r($event_grp_ids);
						 $query = Eventgroup::query(); 
						 if($type!= null || $type!= ''){
							$query =$query->where('category_id',  $type);
						 }
						 
							$query =$query->whereIn('id',  $event_grp_ids);
						  
						  
						$event_group_list =  $query->limit($per_page)->offset($start)->orderBy('id')->get(); 
						 $count = $query->limit($per_page)->offset($start)->orderBy('id')->count();
				
				// $event_group_list = Eventgroup::limit($per_page)->offset($start)->orderBy('id')->get(); 
				 foreach($event_group_list as $get) { 
						 $array_data = array();
						 
						 $array_data['event_group_id']  = $get['id'];
						 $array_data['group_picture']  = $get['group_picture'];
						 $array_data['event_group_picture']  = $get['group_picture'];
						 $array_data['title']  = html_entity_decode($get['title']);
						 
						  $array_data['event_group_begin']  = hr_date($get['date_begin']);
						 $array_data['event_group_end']  = hr_date($get['date_end']);
						  
						  
					     $eventArr=$get['events'] ;
						 
						 $city_name='';
						 $contributor='';
						 $director='';
						 $auditorium='';
						 if(count($eventArr) > 0 ){
							 //Event Loop Start here
						 	$coun =count($eventArr);
						 	$i = 0 ; 
							 foreach( $eventArr as $ev){
								 
								  $i = $i+1 ; 
								  $contributor=$contributor.$ev['contributor'].",";
								  $director=$director.$ev['director'].",";
								  $auditorium=  $auditorium.$ev['auditorium']['name'].",";
								   
								  $city_name= $city_name.$ev['city']['name'] ." - ";  
								  //Loop For city
								 
							 }
						  
						 }
						 
						 $array_data['event_group_auditorium']  =  $auditorium;
						 
						 $array_data['event_group_cityname']  =  $city_name;  
						  
						 $dataSlider[] = $array_data;
					 }
				$this->data['category_id']=$type;

				$this->data['locationname']=$locatinname['name'];
				$this->data['rows'] =  $dataSlider;
				$this->data['page']=$page;
				$this->data['searchCateogories'] =  Category::orderBy('id')->get();
				$this->data['searchCity'] =City::orderBy('id')->get();
				$this->data['pages']=ceil($count / $per_page); 
				$this->data['egImgURL']  = EVENTGROUP_WEB_PATH.'/';
			 
				return $this->render($response, 'public/eventgroup/search_event_group.twig', $this->data); 
	}
	
	//*=================Add ========================*//
	        
			
			function getEventListByDate($eventdate,$eventgroupid){
				 
						$query = Event::query(); 

								 	$middleFormat = strtotime($eventdate);  
									$dateF= date('Y-m-d', $middleFormat);
									$dateFrom= $dateF." 00:00:00";
									$dateTo= $dateF." 23:59:59";
								$query =$query->whereBetween('date', [$dateFrom, $dateTo]);


								$query =$query->where('eventgroup_id', $eventgroupid);

								
						 		$eventArr = $query->get();

								$eventList='';
								if(count($eventArr) > 0 ){
								   foreach(   $eventArr as $ev){
								   	$event_id=$ev['id'];
								   
							$evTimearr = EventTime::where('event_id',$event_id)->get();
							 $evT=array();
							 


								    $city_name =   $ev['city']['name'];
									$timeda =   $ev['date'];
									$timeFormat = strtotime($timeda);  
									$event_time =   date('h:i', $timeFormat);
									 if($city_name!=''){
									 	$title='';

									  // $title = '<a href="javascript:void();" class="showEvn"  id="'.$ev['id'].'"    >'.$city_name.'&nbsp';
									   if(count($evTimearr) > 0 ){
							 foreach($evTimearr as $evTime){
							 	$eventTime =   date('H:i', strtotime($evTime['event_time']));
							 	$title .= '<a href="javascript:void(0);" class="showEvn"     onclick="clickEventBtn(\''.$ev['id'].'\',\''.$eventTime.'\');"  id="'.$ev['id'].'"   data-time="'.$eventTime.'"   >'.$city_name.'&nbsp'.$eventTime.'</a> </br>';
								 //$title.= $evTime['event_time'].'</br>';
							 }
							 $evT[]=$evTS;
							 }
							 //$title .='</a>';
									    //$title .='&nbsp'.$event_time.'</a>'
									 }else{
									   //$title = '<a href="/order" >'.$ev['title'].'-'.$event_time.'</a>';
									 }
								     $eventList.= '<p>'.$title.'</p>';
								   }
								   
								}
							return 	$eventList;     
			}
			
			
			function ajaxCalendarWithEvents($request, $response){ 
				/* date settings */ 
				$eventgroupid=$request->getAttribute('id'); //fetch the category id

				return  $this->getCalendarActivity($eventgroupid);
				
			}
			  
			function draw_calendar($month,$year,$events = array(),$eventgroupid){

			 
				/* draw table */
				$calendar = '';

				/* table headings */
				$headings = array('DIMANCHE','LUNDI','MARDI','MERCREDI','JEUDI','VENDREDI','SAMEDI');
				$calendar.= '<div class="daterow"><ul>';
				foreach ($headings as $dayname){
					$calendar.= '<li>'.$dayname.'</li>';

				}
				$calendar.= '</ul></div>';



				/* days and weeks vars now ... */
				$running_day = date('w',mktime(0,0,0,$month,1,$year));
				$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
				$days_in_this_week = 1;
				$day_counter = 0;
				$dates_array = array();
				/* row for week one */
				$calendar.= '<div class="datecount"><ul>';

				/* print "blank" days until the first of the current week */
				for($x = 0; $x < $running_day; $x++):
					$calendar.= '<li>&nbsp;</li>';
					 $days_in_this_week++;
				endfor;

				/* keep going with days.... */
				for($list_day = 1; $list_day <= $days_in_month; $list_day++):
					$calendar.= '<li> ';
					/* add in the day number */
					
					$completedEventDate= $year."-".$month."-".$list_day;
					$eventSpan= $this->getEventListByDate($completedEventDate,$eventgroupid);
					if($eventSpan!=''){ 
						$calendar.= '<span  class="red"  >'.$list_day.'</span>';
						$calendar.= $eventSpan;
					}else{

					   $calendar.= $list_day;
					}					
									   

					$calendar.= ' </li>';
					if($running_day == 6):
						$calendar.= '</ul></div>';
						if(($day_counter+1) != $days_in_month):
							$calendar.= '<div class="datecount"><ul>';
						endif;
						$running_day = -1;
						$days_in_this_week = 0;
					endif;
					$days_in_this_week++; $running_day++; $day_counter++;
				endfor;

				/* finish the rest of the days in the week */
				 
				if($days_in_this_week < 8 ):
					for($x = 1; $x <= (8 - $days_in_this_week); $x++):
					 $dayleft =  8 - $days_in_this_week ;
						if( $dayleft != '7'){
							$calendar.= '<li>&nbsp;</li>';
						 }
					endfor;
				endif;
				 

				
				/* final row */
				$calendar.= '</ul></div>';

				/** DEBUG **/

				/* all done, return result */
				return $calendar;

			}
			function getCalendarActivity($eventgroupid){

				/* date settings */
				$month = (int) (isset($_GET['month']) ? $_GET['month'] : date('m'));
				$year = (int)  (isset($_GET['year']) ? $_GET['year'] : date('Y'));
				/* select month control */

				$selectedMonth= date('F',mktime(0,0,0,$month,1,$year));
					/*
							$select_month_control = '<select name="month" id="month">';
							for($x = 1; $x <= 12; $x++) {
								$select_month_control.= '<option value="'.$x.'"'.($x != $month ? '' : ' selected="selected"').'>'.date('F',mktime(0,0,0,$x,1,$year)).'</option>';
							}
							$select_month_control.= '</select>';
					*/
					/* select year control */
				$year_range = 7;
				$selectedYear= $year;
				/*
					   $select_year_control = '<select name="year" id="year">';
					   for($x = ($year-floor($year_range/2)); $x <= ($year+floor($year_range/2)); $x++) {
						   $select_year_control.= '<option value="'.$x.'"'.($x != $year ? '' : ' selected="selected"').'>'.$x.'</option>';
					   }
					   $select_year_control.= '</select>';
			  */
				/* "next month" control */
				$finalCalendar='';

				//$finalCalendar=$finalCalendar.'<div id="ajaxCalendar">';
				$next_month_link = '<a href="javascript:void(0);" onclick="getCalendar(\''.($month != 12 ? $month + 1 : 1).'\',\''.($month != 12 ? $year : $year + 1).'\')" class="control"><i class="fas fa-chevron-right"></i></a>';

				/* "previous month" control */
				$previous_month_link = '<a href="javascript:void(0);" onclick="getCalendar(\''.($month != 1 ? $month - 1 : 12).'\',\''.($month != 1 ? $year : $year - 1).'\')" class="control"><i class="fas fa-chevron-left"></i></a>';


				/* bringing the controls together */
				$controls = '<form method="get"><div class="monthrow"><ul><li>'.$previous_month_link.'</li> <li><span>'.$this->ChangeCalanderLg($month).' '.$year.'</span></li><li>'.$next_month_link.' </li></ul></div></form>';
 
				$finalCalendar=$finalCalendar.$controls;
				$finalCalendar=$finalCalendar.$this->draw_calendar($month,$year,$events= array(),$eventgroupid);
				//$finalCalendar=$finalCalendar.'</div>';
				return $finalCalendar;
			}
			
			
			
			
			
			
			
			
			
			
			/** Event of day */
			
		public function getEventgroupOfDay($request, $response){
				 
					//Get url parameters
					$middleFormat ='';
					$dateFrom= '';
					$dateTo='';
					$dataSlider=array();

					$page =$request->getParam('page');
					$per_page = 10;
					$page = (isset($page) && is_numeric($page) ) ? $page : 1;

					$start = ($page-1) * $per_page;


					$auditorium_ids=array();
					$query = Event::query();  
                   $middleFormat = strtotime(date('Y-m-d'));  
				   $dateF= date('Y-m-d', $middleFormat);
									$dateFrom= $dateF." 00:00:00";
									$dateTo= $dateF." 23:59:59";
					$query =$query->whereBetween('date', [$dateFrom, $dateTo]);
				   
				   
				   

					$eventArr =  $query->limit($per_page)->offset($start)->orderBy('id')->get(); 
					$event_group_list = getEventList($eventArr); 	

					$count =  count( $query->get()->count());

                    

					$this->data['rows'] =  $event_group_list;
					$this->data['page']=$page;
					$this->data['pages']=ceil($count / $per_page); 
					$this->data['pages']=ceil($count / $per_page); 
					$this->data['egImgURL']  = EVENTGROUP_WEB_PATH.'/';
					$this->data['eventImgURL']  = EVENT_WEB_PATH.'/';
					$this->data['topHeaderText'] =  getCmsList('5'); //First Right side Ads 
					$this->data['eventCurrentD']  = date('d/m/Y');
		 
					return $this->render($response, 'public/eventgroup/events-of-day.twig', $this->data); 
					 
		  }
		  
		  
		public function getDonMissEventGroup($request, $response){
				 
				 
				//Get url parameters
					$middleFormat ='';
					$dateFrom= '';
					$dateTo='';
					$dataSlider=array();

					$page =$request->getParam('page');
					$per_page = 10;
					$page = (isset($page) && is_numeric($page) ) ? $page : 1;

					$start = ($page-1) * $per_page;


					$auditorium_ids=array();
					$query = Event::query();  


					$eventArr =  $query->limit($per_page)->offset($start)->orderBy('id')->get(); 
					$event_group_list = getEventList($eventArr); 	

					$count =  count( Event::all());



					$this->data['rows'] =  $event_group_list;
					$this->data['page']=$page;

					$this->data['pages']=ceil($count / $per_page); 
					$this->data['egImgURL']  = EVENTGROUP_WEB_PATH.'/';
					$this->data['eventImgURL']  = EVENT_WEB_PATH.'/';
				    $this->data['topHeaderText'] =  getCmsList('4'); //First Right side Ads 
					return $this->render($response, 'public/eventgroup/do-no-miss.twig', $this->data); 
					 
		  }
		  function ajxModelEventBody($request, $response){ 
				/* date settings */ 
				 
				 $groupid='0';
                  if( $request->getParam('eventgrpid') != null){$groupid = $request->getParam('eventgrpid'); } 
				  if($groupid=='0'){exit;}
				  
				  $event_list =  Event::where('id', $groupid)->get();
			      
				   
				  $array_data  = getEventList($event_list);
					$groupid=$array_data[0]["eventgroup_id"];
						$popupBody=' <div class="bookBox">
										<div class="book"><a href="#"><img src="'.EVENTGROUP_WEB_PATH.'/'.$array_data[0]['event_group_picture'].'"></a> <a href="#" class="encore"> > Encore +</a></div>
											 <div class="bookDetails">
											  <h1 id="exampleModalLabel"><a href="#">'.$array_data[0]['artist_name'].'<span>'.$array_data[0]['title'].'</span></a></h1>
											  <p>'.$array_data[0]['date'].'</p>
											  <p>'.$array_data[0]['event_auditorium'].'  <span>'.$array_data[0]['event_city'].'</span></p>
											  <div class="blockquoteText">
												  <p><a href="#">Contributrice : '.$array_data[0]['contributor_name'].' </a> 
												  '.$array_data[0]['contributor_description'].'</p>
 
											 
											  </div>
											  <div class="reserverbtn"><a href="eventgroup/'.$groupid.'" >je réserve</a></div>
											  </div>
										</div>
										 <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span> </button>    ';  
						 
					 
				return $popupBody;
				
			}
			
			
			function ajaxcallEventOrder($request, $response){ 
				/* date settings */ 
			 
				 $eventid='0';
                  if( $request->getParam('evnt_id') != null){$eventid = $request->getParam('evnt_id'); } 
				  if($eventid=='0'){exit;}
				  
				  $event_list =  Event::where('id', $eventid)->get();
				   
				  
				  $timeE =  $request->getParam('dataTime'); 
				  // $timein =   strtotime($timeE);
				  $timein =  $timeE;
				   
				  $array_data  = getEventList($event_list);
				  foreach($array_data as $row){
                  
				   
				  $ticket_list =  EventSeatCategories::where('event_id', $eventid)->get();	
				   
				   
				   
				//  $bodyText= '<h3 ><span>'.$row['artist_name'].'</span>'.$row['date_D'].' '.$row['date_F'].' '.$row['date_j'].', '.$row['date_Y'].' at '.$timein.' <br /> '.$row['event_city'].'</h3>';
                  	  $bodyText= '<h3 ><span>'.$row['title'].'</span>'.$row['event_auditorium'].'<br />'.$row['date_D'].' '.$row['date_F'].' '.$row['date_j'].', '.$row['date_Y'].' at '.$timein.' <br /> </h3>';
				  //$ticket_list =  EventTicket::where('event_id', $row['id'])->get();	 
					if(count($ticket_list) > 0 ){
					
							 $bodyText .= '<div class="catsection">
												<div class="cattitlecon">
													
													<div class="ctrow">
														<h2>catégorie</h2>
													</div>
													<div class="ctrow ">
														<h2>tarif</h2>
													</div>
													<div class="ctrow ">
														<h2>Row</h2>
													</div>
													<div class="ctrow ">
														<h2>Seats</h2>
													</div>
													
													<div class="ctrow">
														<h2>quantité</h2>
													</div>
													</div>
												</div>';
							$bodyText .= '<form name="setCartItem"   action="'.base_url.'/mon-panier"  >' ;	
								 							
								foreach($ticket_list as $ticket){		
                                       $totalQuanity=intval($ticket['total_qantity']);	
 							           $seat_row_from = $ticket['seat_row_from'];
									   $seat_row_to = $ticket['seat_row_to'];
									  
								$bodyText .= '<div class="catconinner">
													<div class="inner">
													<div class="ctrow bonone1">
														<p>'.$ticket['seat_category'].'</p>
													</div>
													 <div class="ctrow pricerow">
													 <p>
													   <strong>'.$ticket['category_price'].'<span>&#8362;</span></strong>
													   <input type="hidden" name="event[]" value="'.$row['id'].'"   > 	
													   <input type="hidden" name="evtime[]" value="'.$timeE.'"   > 	
													   <input type="hidden" name="ticket_price[]" value="'.$ticket['category_price'].'"  > 
													   <input type="hidden" name="ticket_type[]" value="'.$ticket['seat_category'].'"  > 
													   <input type="hidden" name="ticket_type_id[]" value="'.$ticket['id'].'"  > 
													   
													 </p>
													
													</div>
													<div class="ctrow">
															<div class="select-style">
																	<select name="ticket_rows[]" class="tickrow" data-seat="seat_qty_from_'.$ticket['id'].'" data-quantity="seat_qty_to_'.$ticket['id'].'" id="ticket_rows_'.$ticket['id'].'">';
																	$i=0;
																	if (is_numeric($seat_row_from)){
																			if(intval($seat_row_from) > 0){
																				 $bodyText .= '<option></option>';
																				  for($i=$seat_row_from; $i<= $seat_row_to; $i++){
																					  $bodyText .= '<option value="'.$ticket['id'].'-'.$i.'">'.$i.'</option>';
																				  }
																			} 
																	}
																	else{
																		 for($i = $seat_row_from ; $i <= $seat_row_to ; $i++){
																			$bodyText .= '<option value="'.$ticket['id'].'-'.$i.'">'.$i.'</option>';
																		 }
																			 
																	}
																	  
										$bodyText .= 			    '</select>
															</div>
														</div>
														<div class="ctrow">
															 <div id="totalavailabletkt_'.$ticket['id'].'">XX</div>
															 <input type="hidden" name="totalavailabletkthdn[]" id="totalavailabletkthdn_'.$ticket['id'].'"   > 
															 ';
																	 
										$bodyText .= 			    ' 
															 
														</div>
														<div class="ctrow">
															<div class="select-style">
																	<select name="seat_qty[]" id="seat_qty_'.$ticket['id'].'" class="qtyto"  >'; 
										$bodyText .= 			    '<option value="">0</option></select>
															</div>
														</div>
													  
													</div>
												
										     </div>';
								}
								
								$bodyText .= '<div id="reservebook"><button  class="btn-select">je réserve</button></div>';
							   $bodyText .= '</form> ';
						}
									
					}
				  
				 $bodyText .= 			    '</div>';
				return $bodyText;
				
			}

			function ajaxcallRawSeat($request, $response){ 
			   $strList='';
				 $rowseat=$request->getAttribute('rowno'); //fetch the category id
				 
				 $a=explode("-",$rowseat); 
				 $id=$a[0];
				 $row_number = $a[1];
				 $audArr =   EventSeatCategories::where('id', $id)->first() ;
 				 $total_qantity =$audArr['total_qantity'];
				 $audArr =$audArr['seat_json_for_front']; 
				  
				$array_restored_from_db = unserialize($audArr);//print_r($array_restored_from_db );
				//print_r($array_restored_from_db);
				$dataA=array();
				//print $seat_number;
				foreach($array_restored_from_db as $k => $v){
					  
						  if($row_number == $k){
						 
							 $dataA['from'] = $v['slider_range_from_value']; 
							 $dataA['to'] = $v['slider_range_to_value'] ;
						   }
					  
				}
				$user_booking_seat ='30';
				if($user_booking_seat > $total_qantity){
					$user_booking_seat = $user_booking_seat - $total_qantity;
				} 
				$availableSeat  = '';
				if(count($total_qantity)> 0 ){
					$strList.='<option value=""></option> ';
					for($i = 1; $i<= intval($user_booking_seat);$i++){
						$strList.='<option value="'.$i.'">'.$i.'</option> ';
					}
				}
				
				 $jsonData = array('available_ticket_quantity' => $total_qantity, 'choose_ticket_quantity' => $strList);
					return $response->withHeader('Content-type','application/json')->write(json_encode($jsonData));
					exit();	 
               		
			}
			function ChangeCalanderLg($month){

					$monthno =$month;
					$monthname='';

					switch ($monthno) {
					    case 1:
					        $monthname='janvier';
					        break;
					    case 2:
					        $monthname='février';
					        break;
							case 3:
					        $monthname='mars';
					        break;
							case 4:
					        $monthname='avril';
					        break;
							case 5:
					        $monthname='mai';
					        break;
							case 6:
					        $monthname='juin';
					        break;
							case 7:
					        $monthname='juillet';
					        break;
							case 8:
					        $monthname='août';
					        break;
							case 9:
					        $monthname='septembre';
					        break;
							case 10:
					        $monthname='octobre';
					        break;
							case 11:
					        $monthname='novembre';
					        break;
							case 12:
					        $monthname='décembre';
					        break;
					}
					return $monthname;
		    }
			
			function DaysLg($month){

					$monthno =$month;
					$monthname='';

					switch ($monthno) {
					    case 1:
					        $monthname='Dim'; //Sun
					        break;
					    case 2:
					        $monthname='Lun';
					        break;
						case 3:
					        $monthname='Mar';
					        break;
					    case 4:
					        $monthname='Mer';
					        break;
						case 5:
					        $monthname='Jeu';
					        break;
						case 6:
					        $monthname='Ven';
					        break;
						case 7:
					        $monthname='Sam';
					        break;
							  
					}
					return $monthname;
		    }
 
}