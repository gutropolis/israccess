<?php
namespace App\Controllers;

use App\Models;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v;
use Illuminate\Database\Capsule\Manager;

/**
  Admin Event Controller
  CRUDs for Events
  Available Functions
  1. events
  2. getAjaxEventsList
  3. saveEvent
  4. updateEvent
  5. getEventById
  6. deleteEventPictureById
  7. deleteEventById
  8. editEventById
  9. updateEventStatus
  10. uploadEventImages
  11.  eventsDontMiss
  12. ajaxDontMissEventsList
  13. eventsOfDay
  14. ajaxEventsOfDayList
  15. saveEventMultipleTimes
  16. deleteEventTimeById
  17. saveEventMultipleTickets
  18. saveEventRoles
  19. deleteEventTicketById
  20. deleteEventRoleById
  21. saveAudEventSeats
  22. makeJsonForFront
  23. saveEventSeatTicketsData
  24. updateEventSeatTicketsData
  25. eventMapPage
  26. getAjaxEventMapList
  27. eventMapAdd
  28. eventMapEdit
  29. saveEventSeatTicketMap
  30. updateEventSeatTicketMap
  31. deleteEventSeatById
  32. deleteEventSeatRowById
  33. deleteRowSeatById
  34. updateRowSeatsTable
  35. saveNewRowSeatsTable
  36. saveRowSeatsTable
  
*/

class AdminEventController extends Base 
{
	protected $container;
	protected $lang;
	public function __construct($container)
	{
		error_reporting(0);
		$this->container = $container;
		$this->lang =  $this->container->view['adminLang'];
	}
	
	 // Main function
	public function events($request, $response) {
        $params = array( 'title' => 'All Events',
		                 'current_url' => $request->getUri()->getPath());
        return $this->render($response, ADMIN_VIEW.'/Event/events.twig',$params);
    }
	
	// Ajax Events list
	public function getAjaxEventsList($request, $response){
		$event_group_id = $request->getParam('post_data')['event_group_id'];
		$isSearched = $drpSearch = false; // set to false if user did not search something
		$conditions = array();
		$whereData =  $customSearch = $prefix = '';
		if($request->getParam('query') != null){
			 $isSearched = true; // user has searched something
			 $fields = array('title', 'date');
			if(isset($request->getParam('query')['Status'])) {
				   $drpSearch = true;
				   $customSearch  .= " status = ".$request->getParam('query')['Status']." ";
			 }
			 if(isset($request->getParam('query')['generalSearch'])) {
				 $prefix = '';
			 foreach($fields as $field){
			    if(isset($request->getParam('query')['generalSearch'])) {
				     $conditions[] = "$field LIKE '%" . ($request->getParam('query')['generalSearch']) . "%'";
				  }
			   }
			 }
		 }
		 $query = '';
		 if(count($conditions) > 0) {
            $query .=  '('. implode (' OR ', $conditions). ')'; 
         }
		if($drpSearch){
			 if($query <> ''){
				 $whereData =  $query. ' AND '. $customSearch;
			 }else{
				 $whereData = $customSearch;
			 }
		}else{
			$whereData = $query;
		}

		// Look for sorting if any 
		$sort  = !empty($request->getParam('sort')['sort']) ? $request->getParam('sort')['sort'] : 'DESC';
        $field = !empty($request->getParam('sort')['field']) ? $request->getParam('sort')['field'] : 'id';
		
		$page     = $request->getParam('pagination')['page'];
		if( !empty($request->getParam('pagination')['pages']) ){
		  $pages    = $request->getParam('pagination')['pages'];
		}
		$per_page = $request->getParam('pagination')['perpage'];
		
		if($isSearched){
		    $total   = Models\Event::with(['city', 'auditorium'])->where('eventgroup_id', '=', $event_group_id)->whereRaw($whereData)->count(); // get count 
		}else{
			$total   = Models\Event::with(['city', 'auditorium'])->where('eventgroup_id', '=', $event_group_id)->count(); // get count 
		}
		
		if($page == 1){
		   $offset = 0;	
		   $perpage = 0;
		}else{
		  $offset = ($page-1);	
		  $perpage = $per_page;	
		}
		if($per_page <= 1){
		  $pages = intval($total/10);
	    }else{
	      $pages = intval($total/$per_page);
		}
		if($per_page <= 1){
		   $perPageLimit = 10;	
		}else{
		   $perPageLimit = $per_page;	
		}
		 
		if($isSearched){
		    $events_of_day_list = Models\Event::with(['city', 'auditorium'])->where('eventgroup_id', '=', $event_group_id)
			->whereRaw($whereData)->skip($offset*$perPageLimit)->take($perPageLimit)->orderBy($field, $sort)->get();
		}else{
			
			$events_of_day_list = Models\Event::with(['city', 'auditorium'])->where('eventgroup_id', '=', $event_group_id)
			->skip($offset*$perPageLimit)->take($perPageLimit)->orderBy($field, $sort)->get();
		}
		
		
		$data = array();
		foreach($events_of_day_list as $get){
			$event_group_title = ($get['eventgroup']['title'] == '') ? 'click to edit' : $get['eventgroup']['title'];
            
		  	$array_data = array();
			$title = ($get['title'] == '') ? 'click to edit' : $get['title'];
			$array_data['id']  = $get['id'];
            $array_data['title']  = '<a href="javascript:void(0);" title="Edit Event" onclick="edit_event('.$get['id'].')">'.$title.'</a>';
			$array_data['date']  = hr_date($get['date']);
			$array_data['city_name']  = $get['city']['name'];
			$array_data['auditorium_name']  = $get['auditorium']['name'];
			/*$array_data['group_name']  =  '<a href="javascript:void(0);" title="View Event Group" onclick="edit('.$get['eventgroup']['id'].')">'.$event_group_title.'</a>';*/
			$array_data['status']  = $get['status'];
			$array_data['seats_on_map']  = $get['seats_on_map'];
			$data[] = $array_data;
		}
		$meta = array("page" => $page,
						"pages" => $pages,
						"perpage" => $perPageLimit,
						"total" => $total,
						"sort" => $sort,
						"field" => $field
					);
		$output = array(
						"meta" => $meta,
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	
	}
	
	// Save Event
	public function saveEvent($request, $response){
		 $isError = false;
		$section =  $request->getParam('section'); 	 
	   $eventgroup_id =  $request->getParam('eventgroup_id'); 	 
	   $title_event =  $request->getParam('title_event');
	   $date_event =  $request->getParam('date_event');
	   $city_id =  $request->getParam('city_id');
	   $auditorium_id =  $request->getParam('auditorium_id');
	   $description =  $request->getParam('description');
	   $artist_name =  $request->getParam('artist_name');
	   $author_name =  $request->getParam('author_name');
	   $productor_name =  $request->getParam('productor_name');
	   $director_name =  $request->getParam('director_name');
	   $contributor_name =  $request->getParam('contributor_name');
	   $contributor_description =  $request->getParam('contributor_description');
	   $show_in_section =  $request->getParam('show_in_section');
	   $display_order =  $request->getParam('display_order');
	   $seats_on_map = $request->getParam('seats_on_map');
	   $status = $request->getParam('status');
	   $booking_fee = $request->getParam('booking_fee');
	   $event_ticket_type = $request->getParam('event_ticket_type');
	   $eventExist = Models\Event::where('title', '=', $title_event)->first();
	    if(empty($title_event)){
		   $isError = true;
		 echo json_encode(array("status" => 'error', 
		                        'message' => 'Please enter event title.'));
		 exit();	   
	   }else if(empty($city_id)){
		   $isError = true;
		 echo json_encode(array("status" => 'error', 
		                        'message' => 'Please select event city.'));
		 exit();	   
	   }else if(empty($auditorium_id)){
		   $isError = true;
		 echo json_encode(array("status" => 'error', 
		                        'message' => 'Please select event auditorium.'));
		 exit();	   
	   }else{
		  $isError = false;
	   }

	   if( !$isError ){
		   $display_order = ($display_order == '') ? 0 : $display_order;
		  // Save to events table
		   $event = new Models\Event;
		   $event->title = $title_event;
		   $event->date = mysql_date($date_event);
		   $event->created_at = date('Y-m-d H:i:s');
		   $event->updated_at = date('Y-m-d H:i:s');
		   $event->eventgroup_id = $eventgroup_id;
		   $event->city_id = $city_id;
		   $event->auditorium_id = $auditorium_id;
		   $event->artist_name = $artist_name;
		   $event->author_name = $author_name;
		   $event->productor_name = $productor_name;
		   $event->director_name = $director_name;
		   $event->description = htmlspecialchars($description);
		   $event->contributor_name = $contributor_name;
		   $event->contributor_description = htmlspecialchars($contributor_description);
		   $event->status = $status;
		   $event->section = $section;
		   $event->seats_on_map = $seats_on_map;
		   $event->event_ticket_type = $event_ticket_type;
		   $event->booking_fee = $booking_fee;
		   $event->save();	
		   
		   $event_id = $event->id; // Event id after save
		   if($event_id)
		   $this->uploadEventImages($event_id); // Upload event images
		   $this->saveEventMultipleTimes($event_id); // Save multiple event timings
		   $this->saveEventMultipleTickets($event_id, $seats_on_map);  // Save multiple event tickets
		   $this->saveEventRoles($event_id); // Save to event roles table
		   $this->saveAudEventSeats($event_id, $auditorium_id, $seats_on_map); // Save auditorium manual seats.
		  
		   
		   return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode(array('status' => TRUE))); 
	   }
	}
	
	
	
	// Update Event
	public function updateEvent($request, $response){
		 $isError = false;
	   $id =  $request->getParam('id_event_edit'); 	
	   $eventgroup_id = $request->getParam('eventgroup_id_edit'); 	
	   $section =  $request->getParam('section'); 	
	   $title_event =  $request->getParam('title_event');
	   $date_event =  $request->getParam('date_event');
	   $city_id =  $request->getParam('city_id');
	   $auditorium_id =  $request->getParam('auditorium_id');
	   $description =  $request->getParam('description');
	   $artist_name =  $request->getParam('artist_name');
	   $author_name =  $request->getParam('author_name');
	   $productor_name =  $request->getParam('productor_name');
	   $director_name =  $request->getParam('director_name');
	   $contributor_name =  $request->getParam('contributor_name');
	   $contributor_description =  $request->getParam('contributor_description');
	   $show_in_section =  $request->getParam('show_in_section');
	   $display_order =  $request->getParam('display_order');
	   $seats_on_map = $request->getParam('seats_on_map_edit');
	   $status = $request->getParam('status');
	   $booking_fee = $request->getParam('booking_fee');
	   
	   $eventExist = Models\Event::where('title', '=', $title_event)->where('id', '!=', $id)->first();
	    if(empty($title_event)){
		   $isError = true;
		 echo json_encode(array("status" => 'error', 
		                        'message' => 'Please enter event title.'));
		 exit();	   
	   }else if(empty($city_id)){
		   $isError = true;
		 echo json_encode(array("status" => 'error', 
		                        'message' => 'Please select event city.'));
		 exit();	   
	   }else if(empty($auditorium_id)){
		   $isError = true;
		 echo json_encode(array("status" => 'error', 
		                        'message' => 'Please select event auditorium.'));
		 exit();	   
	   }else{
		  $isError = false;
	   }
	   
	   if( !$isError ){
		   $display_order = ($display_order == '') ? 0 : $display_order;
		  // update events table
		   $data = array('title' => $title_event,
		                   'date' => mysql_date($date_event),
		                   'updated_at' => date('Y-m-d H:i:s'),
						   'eventgroup_id' => $eventgroup_id,
						   'city_id' => $city_id,
						   'auditorium_id' => $auditorium_id,
						   'artist_name' => $artist_name,
						   'author_name' => $author_name,
						   'productor_name' => $productor_name,
						   'director_name' => $director_name,
						   'description' => htmlspecialchars($description),
						   'contributor_name' => $contributor_name,
						   'contributor_description' => htmlspecialchars($contributor_description),
						   'section' => $section,
						   'status' => $status,
						   'seats_on_map' => $seats_on_map,
						   'booking_fee' => $booking_fee);
		   $event = Models\Event::where('id', '=', $id)->update($data);	
		   
		   $event_id = $id; // Event id 
		   if($event_id)
		   $this->uploadEventImages($event_id); // Upload event images
		   $this->saveEventMultipleTimes($event_id); // Save multiple event timings
		   $this->saveEventMultipleTickets($event_id, $seats_on_map);  // Save multiple event tickets
		   $this->saveEventRoles($event_id); // Save to event roles table
		   $this->saveAudEventSeats($event_id, $auditorium_id, $seats_on_map); // Save auditorium manual seats.
		   
		   return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode(array('status' => TRUE))); 
	   }
	}
	
	// Get Event  by id
	public function getEventById($request, $response, $args){
		$id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		$event = Models\Event::with(['city', 'auditorium'])->find($id);
		$event_pics = Models\Eventpicture::where('event_id', '=', $id)->get();
		$event_times = Models\EventTime::where('event_id', '=', $id)->get();
		$event_tickets = Models\EventTicket::where('event_id', '=', $id)->get();
		$event_roles = Models\EventRole::where('event_id', '=', $id)->get();
		if ($event) {
			$event['auditorium_name'] = $event['auditorium']['name'];
			$event['city_name'] =  $event['city']['name'];
			$event['section'] =   '';
			$event['date_e'] = hr_date($event['date']);
			//$event['description'] = html_entity_decode(htmlspecialchars_decode($event['description']), ENT_NOQUOTES); 
			$event['description'] = htmlspecialchars_decode($event['description']); 
			$event['status'] =  ($event['status'] == 1) ? 'Active' : 'Inactive';
			$images_list = array();
			if( !empty($event_pics) ){
				$path = EVENT_WEB_PATH.'/';
				foreach($event_pics as $row){
				  	$images_list[] = '<li class="hide_li_'.$row['id'].'"><img src="'.$path.$row['event_img'].'" class="img-thumbnail img-responsive inline-block" alt="Responsive image" height="200px" width="200px" style="height:200px" /><a  class="btn-sm btn btn-danger m-btn m-btn--icon m-btn--pill " onclick="remove_pic('.$row['id'].')" style="color:#fff"><i class="la la-trash-o"></i> Delete </a></li>';
				}
				$event['pics'] =  $images_list;
			}else{
			     $event['pics'] =  'No picture found.';
			}
			$timesArray = array();
			if( !empty($event_times) ){
				$i=0;
				 foreach($event_times as $time){
					 $timesArray[] = '<tr id="eventTime_'.$time['id'].'"><td class="col-sm-4" style="width: 39% !important;text-align:  left;padding-top: 20px;">Event Time</td><td class="col-sm-3"><input type="text" class="form-control time_picker" disabled id="event_time_'.$i.'" value="'.hrTime($time['event_time']).'" /></td><td><a  class="btn-sm btn btn-danger m-btn m-btn--icon m-btn--pill " onclick="remove_Time('.$time['id'].')" style="color:#fff"><i class="la la-trash-o"></i> Delete </a></td></tr>';
				$i++; }
			}
			$event['times'] =  $timesArray;
			
			$ticketsArray = array();
			if( !empty($event_tickets) ){
				$i=0;
				 foreach($event_tickets as $ticket){
					 $ticketsArray[] = '<tr id="eventTicket_'.$ticket['id'].'"><td class="col-sm-4" style="width: 20% !important;text-align:  left;padding-top: 20px;">Event Tickets</td><td  style="width:40%"><input type="text" class="form-control"  value="'.$ticket['ticket_type'].'" id="ticket_type_'.$ticket['id'].'"  disabled /></td><td  style="width: 20% !important;"><input type="text" class="form-control"  id="per_ticket_price_'.$ticket['id'].'"  value="'.$ticket['per_ticket_price'].'" disabled /></td><td  style="width:  20% !important;"><input type="text" class="form-control"  id="total_quantity_'.$ticket['id'].'"  value="'.$ticket['total_quantity'].'" disabled /></td><td><a  class="btn-sm btn btn-danger m-btn m-btn--icon m-btn--pill " onclick="remove_Ticket('.$ticket['id'].')" style="color:#fff"><i class="la la-trash-o"></i> Delete </a></td></tr>';
				$i++; }
			}
			
			$rolesArray = array();
			if( !empty($event_roles) ){
				$i=0;
				 foreach($event_roles as $role){
					 $rolesArray[] = '<tr id="eventRole_'.$role['id'].'"><td  style="width:40%">'.$role['role_label'].'</td><td  style="width: 80% !important;">'.$role['role_name'].'</td><td><a  class="btn-sm btn btn-danger m-btn m-btn--icon m-btn--pill " onclick="remove_Role('.$role['id'].')" style="color:#fff"><i class="la la-trash-o"></i> Delete </a></td></tr>';
				$i++; }
			}
			$event['tickets'] =  $ticketsArray;
			$event['roles'] =  $rolesArray;
            echo json_encode($event);
        }	
	}
	
	// Delete Event Picture
	public function deleteEventPictureById($request, $response, $args){
		$id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		// get this image name
		$pictureExist = Models\Eventpicture::where('id', '=', $id)->first()->event_img;
	   if($pictureExist){
		   // Unlink the picture
		   @unlink(EVENT_ROOT_PATH.'/'.$pictureExist);
	   }
		$delete = Models\Eventpicture::find($id)->delete();
		//return $response->withJson(json_encode(array("status" => TRUE)));
	}
	
	// Delete Event By Id
	public function deleteEventById($request, $response, $args){
		$id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];
        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		// get this image name
		$eventPictures = Models\Eventpicture::where('event_id', '=', $id)->get();
	   if( !empty($eventPictures) ){
		   foreach($eventPictures  as $row):
		     // Unlink the picture
		     @unlink(EVENT_ROOT_PATH.'/'.$row['event_img']);
		   endforeach;
		  // Now Delete from Event Images
		   $delete = Models\Eventpicture::where('event_id', '=', $id)->delete();
	   }
		$delete = Models\Event::find($id)->delete();
	}
	
	// Get Event  by id for Edit
	public function editEventById($request, $response, $args){
		$id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		$auditoriums =  Models\Auditorium::orderBy('name','ASC')->get();
		$cities_list =  Models\City::orderBy('name','ASC')->where('status', '=' ,1)->get();
		$event = Models\Event::with(['city', 'auditorium'])->find($id);
		$event_pics = Models\Eventpicture::where('event_id', '=', $id)->get();
		$event_times = Models\EventTime::where('event_id', '=', $id)->get();
		$auditorium_id = Models\Event::where('id', '=', $id)->first()->auditorium_id;
		/*$event_tickets = Models\EventSeatCategories::where('event_id', '=', $id)
		                                             ->where('auditorium_id', '=', $auditorium_id)
													 ->where('status', '=', 1)
													 ->orderBy('id', 'DESC')
													 ->get();*/
		//ddump($event_tickets); 
		$event_roles = Models\EventRole::where('event_id', '=', $id)->get();
		if ($event) {
			$event['date_e'] = hr_date($event['date']);
			$images_list = array();
			if( !empty($event_pics) ){
				$path = EVENT_WEB_PATH.'/';
				foreach($event_pics as $row){
				  	$images_list[] = '<li class="hide_li_'.$row['id'].'"><img src="'.$path.$row['event_img'].'" class=" img-responsive inline-block" alt="Responsive image" height="200px" width="200px" style="height:200px" /><a  class="btn-sm btn btn-danger m-btn m-btn--icon m-btn--pill " onclick="remove_pic('.$row['id'].')" style="color:#fff"><i class="la la-trash-o"></i> Delete </a></li>';
				}
			}
			$timesArray = array();
			if( !empty($event_times) ){
				$i=0;
				 foreach($event_times as $time){
					 $timesArray[] = '<tr id="eventTime_'.$time['id'].'"><td class="col-sm-4" style="width: 25% !important;text-align:  left;padding-top: 20px;">Event Time</td><td class="col-sm-3"><input type="text" class="form-control time_picker" name="event_time_old['.$i.']" id="event_time_'.$i.'" value="'.hrTime($time['event_time']).'" readonly placeholder="Select time" /><input type="hidden" name="event_time_old_id['.$i.']" value="'.$time['id'].'"></td><td><a  class="btn-sm btn btn-danger m-btn m-btn--icon m-btn--pill " onclick="remove_Time('.$time['id'].')" style="color:#fff"><i class="la la-trash-o"></i> Delete </a></td></tr>';
				$i++; }
			}
			
			/*if( $event_tickets->isEmpty() ){
				$ticketsArray[] = '<div class="row" style="padding-left:120px; color:red">No data found</div>';
			}else{
			   $ticketsArray = array();
			   $date_available = $date_expiry  = '';
				if( !empty($event_tickets) ){
					$i=0;$j=1;
					 foreach($event_tickets as $row){
						 if($i == 0){
							$date_available = hr_date($row['stock_available_date']);
							$date_expiry    = hr_date($row['stock_expiry_date']); 
						 }
						 $ticketsArray[] = '<div class="row" style="margin-top:10px">
					  <div class="col-md-1">'.$j.'</div>
					  <div class="col-md-5">'.$row['seat_category'].'</div>
					  <div class="col-md-3"><input type="text" class="form-control number_price_only" 
					  name="event_category_price[]" placeholder="Enter price" maxlength="4" value="'.$row['category_price'].'"></div>
					  <input type="hidden" name="row_id[]" value="'.$row['id'].'">
					  </div>';
					$i++; $j++;
					}
					$ticketsArray[] = '<div class="row" style="margin-top:10px">
					  <div class="col-md-3" style="margin-top:6px;">Select available date</div>
					  <div class="col-md-3"><input type="text" class="form-control m_date" name="stock_available_date" placeholder="Available" value="'.$date_available.'" ></div>
					  <div class="col-md-3" style="margin-top:6px;">Select expiry date</div>
					  <div class="col-md-3"><input type="text" class="form-control m_date" name="stock_expiry_date" placeholder="Expiry" value="'.$date_expiry.'"></div>
					  </div>';
				}else{
					$ticketsArray[] = '<div class="row" style="padding-left:120px; color:red">No data found</div>';
				}
			}*/
			// Event roles
			$rolesArray = array();
			if( !empty($event_roles) ){
				$i=0;
				 foreach($event_roles as $role){
					 $rolesArray[] = '<tr id="eventRole_'.$role['id'].'"><td  style="width:40%"><input type="text" class="form-control" required name="event_role_label_old['.$i.']" value="'.$role['role_label'].'" id="event_role_label_old_'.$i.'"  placeholder="Role label" /></td><td  style="width: 60% !important;"><input type="text" class="form-control" required name="event_role_name_old['.$i.']" id="event_role_name_'.$i.'"  value="'.$role['role_name'].'" placeholder="Role name" /></td><td><input type="hidden" name="event_role_label_old_id['.$i.']" value="'.$role['id'].'"><a  class="btn-sm btn btn-danger m-btn m-btn--icon m-btn--pill " onclick="remove_Role('.$role['id'].')" style="color:#fff"><i class="la la-trash-o"></i> Delete </a></td></tr>';
				$i++; }
				
			}
			$event['description'] = htmlspecialchars_decode($event['description']);
			$ticketsArray = '';
			$params = array('event' => $event,
			                'event_images' => $images_list,
							'cities_list' => $cities_list,
							'auditoriums' => $auditoriums,
							'times' => $timesArray,
							'tickets' => $ticketsArray,
							'roles' => $rolesArray);
            echo json_encode($params);
        }	
	}
	
	// Update Event status to Active or Inactive
	public function updateEventStatus($request, $response, $args){
		  $id = $args['id'];
		  $status = $args['status'];
		  $new_status = ($status == 1) ? 0 : 1;
		  $data = array('status' => $new_status);
		   $event = Models\Event::where('id', '=', $id)->update($data);	
		   return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode(array('status' => TRUE))); 	
	}
	
	// Save event images
	public function uploadEventImages($event_id){
		if(isset($_FILES) && !empty($_FILES) ) {
			$total = count($_FILES['event_img']['name']);
			$uploads_dir = EVENT_ROOT_PATH.'/';
			$validextensions = array("jpeg", "jpg", "png"); 
			for($i=0; $i < $total; $i++) {
				//Get the temp file path
                $file = $_FILES['event_img']['tmp_name'][$i];
				if($file <> ''){
				$ext = explode('.', basename($_FILES['event_img']['name'][$i]));   // Explode file name from dot(.)
				$file_extension = end($ext); // Store extensions in the variable.
				$event_img_name = md5(uniqid()) . "." . $ext[count($ext) - 1];
				$target_path = $uploads_dir . $event_img_name;
				if(in_array($file_extension, $validextensions)) {
					list($width, $height, $type, $attr) = getimagesize($file);
					if($width>200 || $height>200)
					{
						 smart_resize_image($_FILES['event_img']['name'][$i], null,  THUMB_WEIGHT+100, THUMB_HEIGHT+100 , false , $target_path , false , false ,100 );
						 // Save to event_images
						   $event_pic = new Models\Eventpicture;
						   $event_pic->event_id = $event_id;
						   $event_pic->event_img = $event_img_name;
						   $event_pic->save();	
					}else{
					  if (move_uploaded_file($_FILES['event_img']['tmp_name'][$i], $target_path)) {
						 // Save to event_images
						   $event_pic = new Models\Eventpicture;
						   $event_pic->event_id = $event_id;
						   $event_pic->event_img = $event_img_name;
						   $event_pic->save();	
					  }
					}
				}
			}
		  }
		}	
	}
	
	
	// Dont Miss function
	public function eventsDontMiss($request, $response) {
        $params = array( 'title' => 'All Dont Miss Events',
		                 'current_url' => $request->getUri()->getPath());
        return $this->render($response, ADMIN_VIEW.'/Event/dont_miss_events.twig',$params);
    }
	
	// Ajax Dont Miss Events list
	public function ajaxDontMissEventsList($request, $response){
		
		$isSearched = $drpSearch = false; // set to false if user did not search something
		$conditions = array();
		$whereData =  $customSearch = $prefix = '';
		if($request->getParam('query') != null){
			 $isSearched = true; // user has searched something
			 $fields = array('title', 'date_begin');
			if(isset($request->getParam('query')['Status'])) {
				   $drpSearch = true;
				   $customSearch  .= " status = ".$request->getParam('query')['Status']." ";
			 }
			 if(isset($request->getParam('query')['generalSearch'])) {
				 $prefix = '';
			 foreach($fields as $field){
			    if(isset($request->getParam('query')['generalSearch'])) {
				     $conditions[] = "$field LIKE '%" . ($request->getParam('query')['generalSearch']) . "%'";
				  }
			   }
			 }
		 }
		 $query = '';
		 if(count($conditions) > 0) {
            $query .=  '('. implode (' OR ', $conditions). ')'; 
         }
		if($drpSearch){
			 if($query <> ''){
				 $whereData =  $query. ' AND '. $customSearch;
			 }else{
				 $whereData = $customSearch;
			 }
		}else{
			$whereData = $query;
		}

		// Look for sorting if any 
		$sort  = !empty($request->getParam('sort')['sort']) ? $request->getParam('sort')['sort'] : 'DESC';
        $field = !empty($request->getParam('sort')['field']) ? $request->getParam('sort')['field'] : 'id';
		
		$page     = $request->getParam('pagination')['page'];
		if( !empty($request->getParam('pagination')['pages']) ){
		  $pages    = $request->getParam('pagination')['pages'];
		}
		$per_page = $request->getParam('pagination')['perpage'];
		
		if($isSearched){
		    $total   = Models\Event::where('show_in_section','=',3)->whereRaw($whereData)->count(); // get count 
		}else{
			$total   = Models\Event::where('show_in_section','=',3)->count(); // get count 
		}
		
		if($page == 1){
		   $offset = 0;	
		   $perpage = 0;
		}else{
		  $offset = ($page-1);	
		  $perpage = $per_page;	
		}
		if($per_page <= 1){
		  $pages = intval($total/10);
	    }else{
	      $pages = intval($total/$per_page);
		}
		if($per_page <= 1){
		   $perPageLimit = 10;	
		}else{
		   $perPageLimit = $per_page;	
		}
		if($isSearched){
		    $events_list = Models\Event::with(['city', 'auditorium', 'eventgroup'])->where('show_in_section','=',3)
			->whereRaw($whereData)->skip($offset*$perPageLimit)->take($perPageLimit)->orderBy($field, $sort)->get();
		}else{
			
			$events_list = Models\Event::with(['city', 'auditorium', 'eventgroup'])->where('show_in_section','=',3)
			->skip($offset*$perPageLimit)->take($perPageLimit)->orderBy($field, $sort)->get();
		}
		
		$data = array();
		foreach($events_list as $get){
		  	$array_data = array();
			$title = ($get['title'] == '') ? 'click to edit' : $get['title'];
			$array_data['id']  = $get['id'];
            $array_data['title']  = '<a href="javascript:void(0);" title="Edit Event" onclick="edit('.$get['id'].')">'.$title.'</a>';
			$array_data['date']  = hr_date($get['date']);
			$array_data['city_name']  = $get['city']['name'];
			$array_data['auditorium_name']  = $get['category']['name'];
			$array_data['status']  = $get['status'];
			$data[] = $array_data;
		}
		$meta = array("page" => $page,
						"pages" => $pages,
						"perpage" => $perPageLimit,
						"total" => $total,
						"sort" => $sort,
						"field" => $field
					);
		$output = array(
						"meta" => $meta,
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	
	}
	
	// Event of day function
	public function eventsOfDay($request, $response) {
        $params = array( 'title' => 'All Events Of Day',
		                 'current_url' => $request->getUri()->getPath());
        return $this->render($response, ADMIN_VIEW.'/Event/events_of_day.twig',$params);
    }
	
	// Ajax Events of Day list
	public function ajaxEventsOfDayList($request, $response){
		$dateformate = strtotime(date('Y-m-d'));  
	    $today = date('Y-m-d', $dateformate);
		$From = $today." 00:00:00";
		$To   = $today." 23:59:59";
		$isSearched = $drpSearch = false; // set to false if user did not search something
		$conditions = array();
		$whereData =  $customSearch = $prefix = '';
		if($request->getParam('query') != null){
			 $isSearched = true; // user has searched something
			 $fields = array('title', 'date');
			if(isset($request->getParam('query')['Status'])) {
				   $drpSearch = true;
				   $customSearch  .= " status = ".$request->getParam('query')['Status']." ";
			 }
			 if(isset($request->getParam('query')['generalSearch'])) {
				 $prefix = '';
			 foreach($fields as $field){
			    if(isset($request->getParam('query')['generalSearch'])) {
				     $conditions[] = "$field LIKE '%" . ($request->getParam('query')['generalSearch']) . "%'";
				  }
			   }
			 }
		 }
		 $query = '';
		 if(count($conditions) > 0) {
            $query .=  '('. implode (' OR ', $conditions). ')'; 
         }
		if($drpSearch){
			 if($query <> ''){
				 $whereData =  $query. ' AND '. $customSearch;
			 }else{
				 $whereData = $customSearch;
			 }
		}else{
			$whereData = $query;
		}

		// Look for sorting if any 
		$sort  = !empty($request->getParam('sort')['sort']) ? $request->getParam('sort')['sort'] : 'DESC';
        $field = !empty($request->getParam('sort')['field']) ? $request->getParam('sort')['field'] : 'id';
		
		$page     = $request->getParam('pagination')['page'];
		if( !empty($request->getParam('pagination')['pages']) ){
		  $pages    = $request->getParam('pagination')['pages'];
		}
		$per_page = $request->getParam('pagination')['perpage'];
		
		if($isSearched){
		    $total   = Models\Event::with(['city', 'auditorium', 'eventgroup'])->whereBetween('date', [$From, $To])->whereRaw($whereData)->count(); // get count 
		}else{
			$total   = Models\Event::with(['city', 'auditorium', 'eventgroup'])->whereBetween('date', [$From, $To])->count(); // get count 
		}
		
		if($page == 1){
		   $offset = 0;	
		   $perpage = 0;
		}else{
		  $offset = ($page-1);	
		  $perpage = $per_page;	
		}
		if($per_page <= 1){
		  $pages = intval($total/10);
	    }else{
	      $pages = intval($total/$per_page);
		}
		if($per_page <= 1){
		   $perPageLimit = 10;	
		}else{
		   $perPageLimit = $per_page;	
		}
		 
		if($isSearched){
		    $events_of_day_list = Models\Event::with(['city', 'auditorium', 'eventgroup'])->whereBetween('date', [$From, $To])
			->whereRaw($whereData)->skip($offset*$perPageLimit)->take($perPageLimit)->orderBy($field, $sort)->get();
		}else{
			
			$events_of_day_list = Models\Event::with(['city', 'auditorium', 'eventgroup'])->whereBetween('date', [$From, $To])
			->skip($offset*$perPageLimit)->take($perPageLimit)->orderBy($field, $sort)->get();
		}
		
		
		$data = array();
		foreach($events_of_day_list as $get){
			$event_group_title = ($get['eventgroup']['title'] == '') ? 'click to edit' : $get['eventgroup']['title'];
            
		  	$array_data = array();
			$title = ($get['title'] == '') ? 'click to edit' : $get['title'];
			$array_data['id']  = $get['id'];
            $array_data['title']  = '<a href="javascript:void(0);" title="View Event" onclick="view('.$get['id'].')">'.$title.'</a>';
			$array_data['date']  = hr_date($get['date']);
			$array_data['city_name']  = $get['city']['name'];
			$array_data['auditorium_name']  = $get['auditorium']['name'];
			$array_data['group_name']  =  '<a href="javascript:void(0);" title="View Event Group" onclick="edit('.$get['eventgroup']['id'].')">'.$event_group_title.'</a>';
			$array_data['status']  = $get['status'];
			
			$data[] = $array_data;
		}
		$meta = array("page" => $page,
						"pages" => $pages,
						"perpage" => $perPageLimit,
						"total" => $total,
						"sort" => $sort,
						"field" => $field
					);
		$output = array(
						"meta" => $meta,
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	
	}
	
	// Save event multiple timings
	public function saveEventMultipleTimes($event_id){
		  if( isset($_REQUEST['event_time']) && !empty($_REQUEST['event_time']) ){
			  foreach($_REQUEST['event_time'] as $key=>$time):
			     if( !empty($time) ){
				  // Save to event_times
				    $eventTime = new Models\EventTime;
					$eventTime->event_id = $event_id;
					$eventTime->event_time = mysqlTime($time);
					$eventTime->save();
				  }
			  endforeach;
		  }
		  if( isset($_REQUEST['event_time_old']) && !empty($_REQUEST['event_time_old']) ){
			  foreach($_REQUEST['event_time_old'] as $key=>$time):
			     if( !empty($time) ){
					 $id = $_REQUEST['event_time_old_id'][$key];
				  // Update to event_times
					$data = array('event_id' => $event_id,
					             'event_time' => mysqlTime($_REQUEST['event_time_old'][$key]));
					$eventTimeEdit = Models\EventTime::where('id', '=', $id)->update($data);	
				  }
			  endforeach;
		  }
		   
	}
	
	// Delete Event Time
	public function deleteEventTimeById($request, $response, $args){
		$id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		$delete = Models\EventTime::find($id)->delete();		
	}
	
	// Save event multiple tickets
	public function saveEventMultipleTickets($event_id, $seats_on_map){
		// If $seats_on_map variable is Y then it means disable manual seats for this event else save manual seats
		if($seats_on_map == 'Y'){
			// It means this auditorium has map so delete its data from event_tickets as ticket map will be used for this.
			$delete = Models\EventTicket::where('event_id', '=', $event_id)->delete();
		}else{
			// As this auditorium has no map so save its tickets
		  if( isset($_REQUEST['ticket_type']) && !empty($_REQUEST['ticket_type']) ){
			  foreach($_REQUEST['ticket_type'] as $key=>$ticket):
			     if( !empty($ticket) ){
				  // Save to event_tickets
				    $eventTicket = new Models\EventTicket;
					$eventTicket->event_id = $event_id;
					$eventTicket->ticket_type = $ticket;
					$eventTicket->per_ticket_price = $_REQUEST['per_ticket_price'][$key];
					$eventTicket->total_quantity = $_REQUEST['total_quantity'][$key];
					$eventTicket->save();
					
				  }
			  endforeach;
		  }
		  
		   if( isset($_REQUEST['ticket_type_old']) && !empty($_REQUEST['ticket_type_old']) ){
			  foreach($_REQUEST['ticket_type_old'] as $key=>$ticket):
			     if( !empty($ticket) ){
					 $id = $_REQUEST['event_ticket_old_id'][$key];
				    // Update to event_tickets
					$data = array('event_id' => $event_id,
					             'ticket_type' => $ticket ,
								 'per_ticket_price' => $_REQUEST['per_ticket_price_old'][$key],
								 'total_quantity' => $_REQUEST['total_quantity_old'][$key] 
								 );
					$eventTicket = Models\EventTicket::where('id', '=', $id)->update($data);	
				  }
			  endforeach;
		  }
		}  
	}
	
	
	// Save event roles
	public function saveEventRoles($event_id){
			// As this auditorium has no map so save its tickets
		  if( isset($_REQUEST['event_role_label']) && !empty($_REQUEST['event_role_label']) ){
			  foreach($_REQUEST['event_role_label'] as $key=>$role):
			     if( !empty($role) ){
				  // Save to event_roles
				    $eventRole = new Models\EventRole;
					$eventRole->event_id = $event_id;
					$eventRole->role_label = $role;
					$eventRole->role_name = $_REQUEST['event_role_name'][$key];
					$eventRole->save();
					
				  }
			  endforeach;
		  }
		  
		   if( isset($_REQUEST['event_role_label_old']) && !empty($_REQUEST['event_role_label_old']) ){
			  foreach($_REQUEST['event_role_label_old'] as $key=>$role):
			     if( !empty($role) ){
					 $id = $_REQUEST['event_role_label_old_id'][$key];
				    // Update to event_roles
					$data = array('event_id' => $event_id,
					             'role_label' => $role ,
								 'role_name' => $_REQUEST['event_role_name_old'][$key] 
								 );
					$eventRole = Models\EventRole::where('id', '=', $id)->update($data);	
				  }
			  endforeach;
		  } 
	}
	
	
	// Delete Event Ticket
	public function deleteEventTicketById($request, $response, $args){
		$id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		$delete = Models\EventTicket::find($id)->delete();		
	}
	
	// Delete Event Role
	public function deleteEventRoleById($request, $response, $args){
		$id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		$delete = Models\EventRole::find($id)->delete();		
	}
	
	
	
	
	
	// save event auditorium seats tickets
	public function saveAudEventSeats($event_id, $auditorium_id, $seats_on_map){
		if($seats_on_map == 'Y'){
		  // Delete from the table
		  $delete = Models\EventSeatCategories::where('auditorium_id', '=' , $auditorium_id)
		                           ->where('event_id', '=' , $event_id)
								   ->where('status', '=' , 1)
								   ->where('auditorium_id', '=' , $auditorium_id)
								   ->delete();	
		}else{
			// First get all the data of this auditorium + this event
			$seats_map = Models\EventSeatCategories::where('auditorium_id','=', $auditorium_id)
			                                           ->where('event_id','=', $event_id)
													   ->where('status', '=' , 1)
													   ->get();
			if( $seats_map->isEmpty() ){
				 $exist = 0;
			}else{
				$exist = 1;
			}
			// Get auditorium seat tickets 
			$seats_map = Models\AuditoriumSeatCategory::where('auditorium_id','=', $auditorium_id)->get();
			
			if(isset($_REQUEST['event_category_price']) && !empty($_REQUEST['event_category_price']) ){
				  foreach($_REQUEST['event_category_price'] as $key=>$category_price){
					   $range = range($seats_map[$key]['seat_row_from'], $seats_map[$key]['seat_row_to']);
					   $json_data = $seats_map[$key]['seat_rows_json'];
					   if($exist == 0){
						   $audESC = new Models\EventSeatCategories;  
						   $audESC->event_id        = $event_id;
						   $audESC->auditorium_id   = $auditorium_id;
						   $audESC->seat_category   = $seats_map[$key]['seat_category'];
						   $audESC->seat_row_from   = $seats_map[$key]['seat_row_from'];
						   $audESC->seat_row_to     = $seats_map[$key]['seat_row_to'];
						   $audESC->seat_rows_json  = $seats_map[$key]['seat_rows_json'];
						   $audESC->category_price  = $category_price;
						   $audESC->total_qantity   = $seats_map[$key]['total_qantity'];
						   $audESC->from_range      = $seats_map[$key]['from_range'];
						   $audESC->stock_available_date = mysql_date($_REQUEST['stock_available_date']);
						   $audESC->stock_expiry_date = mysql_date($_REQUEST['stock_expiry_date']);
						   $audESC->status = 1;
						   $audESC->net_total_quantity = 0;
						   $audESC->seat_json_for_front = $this->makeJsonForFront($range,$json_data);
						   $audESC->save(); 
					   }else{
						   $id = $_REQUEST['row_id'][$key];
						   $data = array( 'category_price' => $category_price,
						                  'seat_json_for_front' => $this->makeJsonForFront($range,$json_data),
										  'stock_available_date' => mysql_date($_REQUEST['stock_available_date']),
										  'stock_expiry_date' => mysql_date($_REQUEST['stock_expiry_date']) );
						   $update = Models\EventSeatCategories::where('id', '=', $id)->update($data);
					   }
				  }
			}
		}
	}
	
  
  // Prepare data for front end
  public function makeJsonForFront($range,$json_data){
	  $unserialized = unserialize($json_data);
	  $result = array();
	  foreach($unserialized as $key=>$row){
 	          $result[$range[$key]] = array('slider_range_from_value' => $row['slider_range_from_value'],
	                                       'slider_range_to_value' => $row['slider_range_to_value'] );
      } 
     return serialize($result);
  }
  
  // Save auditorium seats tickets
	public function saveEventSeatTicketsData($event_id){
		//ddump($_REQUEST); exit;
		$created_date = date('Y-m-d H:i:s');
		if( isset($_REQUEST['seat_category']) && !empty($_REQUEST['seat_category']) ){
			  $total_qantity = 0 ;
			  $i=0;
			  $j=0;
			  foreach($_REQUEST['seat_category'] as $key=>$seat_category):
			      $category_price = $_REQUEST['category_price'][$key]; 
				  $first_char = $_REQUEST['seat_row_from'][$key]; 
				  $second_char = $_REQUEST['seat_row_to'][$key]; 
			      $seat_rows_json = array();  
				  $loop_limit = sizeof($_REQUEST['seat_category']);
				  $total_qantity = 0;
				  foreach($_REQUEST['slider_range_'.$key] as $innerKey => $slider_range_name){
					  $total_qantity += sizeOfnumbers($slider_range_name);
					  $seat_rows_json[] = array('slider_range_from_value' => rangeFrom($slider_range_name),
											   'slider_range_to_value'   => rangeTo($slider_range_name));
				  }
				   $range = range(''.$first_char.'', ''.$second_char.'');
				   $json_data = serialize($seat_rows_json);
				   $seat_order = $_REQUEST['seat_order'];
				   if(isset($seat_order) && !empty($seat_order)) {
					   $seat_order = 2;
				   }else{
					  $seat_order = 1; 
				   }
				   $auditorium_id = $_REQUEST['auditorium_id'];
				   $audSTC = new Models\EventSeatCategories;
				   $audSTC->auditorium_id = $auditorium_id;
				   $audSTC->event_id = $event_id;
				   $audSTC->seat_category = $seat_category;
				   $audSTC->seat_row_from = $first_char;
				   $audSTC->seat_row_to = $second_char;
				   $audSTC->seat_rows_json = $json_data;
				   $audSTC->category_price = $category_price;
				   $audSTC->total_qantity = $total_qantity;
				   $audSTC->stock_available_date = mysql_date($_REQUEST['stock_available_date']);
				   $audSTC->stock_expiry_date = mysql_date($_REQUEST['stock_expiry_date']);
				   $audSTC->from_range  = join(',',range(''.$first_char.'', ''.$second_char.''));
				   $audSTC->seat_json_for_front = $this->makeJsonForFront($range,$json_data);
				   $audSTC->seat_order = $seat_order;
				   $audSTC->created_date = $created_date;
				   $audSTC->save(); 
			  $i++;
			  endforeach;
			  
		}
	}
	
	// Update auditorium seats tickets
	public function updateEventSeatTicketsData($event_id){
		
		
		$created_date = date('Y-m-d H:i:s');
		if( isset($_REQUEST['seat_category']) && !empty($_REQUEST['seat_category']) ){
			  $total_qantity = 0 ;
			  $i=0;
			  $j=0;
			  foreach($_REQUEST['seat_category'] as $key=>$seat_category):
			      $category_price = $_REQUEST['category_price'][$key]; 
				  $first_char = $_REQUEST['seat_row_from'][$key]; 
				  $second_char = $_REQUEST['seat_row_to'][$key]; 
			      $seat_rows_json = array();  
				  $loop_limit = sizeof($_REQUEST['seat_category']);
				  $total_qantity = 0;
				  foreach($_REQUEST['slider_range_'.$key] as $innerKey => $slider_range_name){
					  $total_qantity += sizeOfnumbers($slider_range_name);
					  $seat_rows_json[] = array('slider_range_from_value' => rangeFrom($slider_range_name),
											   'slider_range_to_value'   => rangeTo($slider_range_name));
				  }
				   $range = range(''.$first_char.'', ''.$second_char.'');
				   $json_data = serialize($seat_rows_json);
				   $seat_order = $_REQUEST['seat_order'];
				   if(isset($seat_order) && !empty($seat_order)) {
					   $seat_order = 2;
				   }else{
					  $seat_order = 1; 
				   }
				   if($seat_order == 2){
					   $total_qantity = intval($total_qantity/2);
				   }
				   $auditorium_id = $_REQUEST['auditorium_id'];
				   $audSTC = new Models\EventSeatCategories;
				   $audSTC->auditorium_id = $auditorium_id;
				   $audSTC->event_id = $event_id;
				   $audSTC->seat_category = $seat_category;
				   $audSTC->seat_row_from = $first_char;
				   $audSTC->seat_row_to = $second_char;
				   $audSTC->seat_rows_json = $json_data;
				   $audSTC->category_price = $category_price;
				   $audSTC->total_qantity = $total_qantity;
				   $audSTC->from_range  = join(',',range(''.$first_char.'', ''.$second_char.''));
				   $audSTC->seat_json_for_front = $this->makeJsonForFront($range,$json_data);
				   $audSTC->seat_order = $seat_order;
				   $audSTC->save(); 
			  $i++;
			  endforeach;
			  
		}
		if( isset($_REQUEST['seat_category_old']) && !empty($_REQUEST['seat_category_old']) ){
			  $total_qantity = 0 ;
			  $i=0;
			  $j=0;
			 
			  foreach($_REQUEST['seat_category_old'] as $key=>$seat_category):
			      $pk_id = $_REQUEST['audSeatRow_id'][$key];
			      $category_price = $_REQUEST['category_price_old'][$key]; 
				  $first_char = $_REQUEST['seat_row_from_old'][$key]; 
				  $second_char = $_REQUEST['seat_row_to_old'][$key]; 
			      $seat_rows_json = array();  
				  $loop_limit = sizeof($_REQUEST['seat_category_old']);
				  $total_qantity = 0;
				  foreach($_REQUEST['slider_range_old_'.$pk_id] as $innerKey => $slider_range_name){
					  $total_qantity += sizeOfnumbers($slider_range_name);
					  $seat_rows_json[] = array('slider_range_from_value' => rangeFrom($slider_range_name),
											   'slider_range_to_value'   => rangeTo($slider_range_name));
				  }
				  
				  $range = range(''.$first_char.'', ''.$second_char.'');
				   $json_data = serialize($seat_rows_json);
				   $seat_order = $_REQUEST['seat_order'];
				   if(isset($seat_order) && !empty($seat_order)) {
					   $seat_order = 2;
				   }else{
					  $seat_order = 1; 
				   }
				   if($seat_order == 2){
					   $total_qantity = intval($total_qantity/2);
				   }
				  $data = array(
				                'seat_category' => $seat_category, 
							     'seat_rows_json' => serialize($seat_rows_json),
							     'category_price' => $category_price,
							     'total_qantity' => $total_qantity,
								 'seat_json_for_front' => $this->makeJsonForFront($range,$json_data),
							     'from_range' => join(',',range(''.$first_char.'', ''.$second_char.'')),
								 'seat_order' => $seat_order
							  ); 
							  
				   $eventRole = Models\EventSeatCategories::where('id', '=', $pk_id)->update($data);	
			  $i++;
			  endforeach;
			  
		}
	}
	
	public function eventMapPage($request, $response, $args) {
		$id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		
        $params = array( 'title' => 'Event Manual Seats',
		                 'current_url' => $request->getUri()->getPath(),
						 'event_id' => $id);
        return $this->render($response, ADMIN_VIEW.'/Event/event_map_page.twig',$params);
    }
	// Ajax Events of Day list
	public function getAjaxEventMapList($request, $response){
		$event_id = $request->getParam('post_data')['event_id']; 
	    // Look for sorting if any 
		$sort  = !empty($request->getParam('sort')['sort']) ? $request->getParam('sort')['sort'] : 'DESC';
        $field = !empty($request->getParam('sort')['field']) ? $request->getParam('sort')['field'] : 'id';
		$page     = $request->getParam('pagination')['page'];
		if( !empty($request->getParam('pagination')['pages']) ){
		  $pages    = $request->getParam('pagination')['pages'];
		}
		$per_page = $request->getParam('pagination')['perpage'];
		
		$total   = Models\EventSeatCategories::with(['event'])->where('event_id','=', $event_id)
			->groupBy('created_date')->count(); // get count 
		
		if($page == 1){
		   $offset = 0;	
		   $perpage = 0;
		}else{
		  $offset = ($page-1);	
		  $perpage = $per_page;	
		}
		if($per_page <= 1){
		  $pages = intval($total/10);
	    }else{
	      $pages = intval($total/$per_page);
		}
		if($per_page <= 1){
		   $perPageLimit = 10;	
		}else{
		   $perPageLimit = $per_page;	
		}
		 
		
			$events_map_list = Models\EventSeatCategories::with(['event'])->where('event_id','=', $event_id)
			->skip($offset*$perPageLimit)->take($perPageLimit)->orderBy($field, $sort)
			 ->groupBy('created_date')->get();
		
		
		foreach($events_map_list as $get){
			
			$remaining_seats = $get['total_qantity'] - $get['net_total_quantity'];
		  	$array_data = array();
			$title = $get['event']['title'];
			$array_data['id']  = $get['id'];
            $array_data['title']  = '<a href="javascript:void(0);" title="View Event" onclick="view('.$get['event']['id'].')">'.$title.'</a>';
			$array_data['stock_available_date']  = hr_date($get['stock_available_date']);
			$array_data['stock_expiry_date']  = hr_date($get['stock_expiry_date']);
			$array_data['total_qantity']  = $get['total_qantity'];
			$array_data['remaining_seats'] = $remaining_seats;
			$array_data['status']  = $get['status'];
			
			$data[] = $array_data;
		}
		$meta = array("page" => $page,
						"pages" => $pages,
						"perpage" => $perPageLimit,
						"total" => $total,
						"sort" => $sort,
						"field" => $field
					);
		$output = array(
						"meta" => $meta,
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	
	}
	
	// Add event map page
	public function eventMapAdd($request, $response, $args) {
		$id = $args['id']; // Event id
	 
        $validations = [
            v::intVal()->validate($id)
        ];
		
        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		
		$event_data = Models\EventSeatCategories::find($id);

		$event = Models\Event::find($id);
		
		if($event_data['seats_on_map'] == 'Y'){
			return $this->response->withHeader('Location', base_url.'/admin/events/groups/edit/'. $event['event_group_id']);
			exit;
		}else{
			
        
		
		  $params = array( 'title' => 'Add map for event',
		                    'data' => $event);
        return $this->render($response, ADMIN_VIEW.'/Event/add_map.twig',$params);
		}
    }
	
	// Edit event map page
	public function eventMapEdit($request, $response, $args){
		$id = $args['id']; // Event id
		
        $validations = [
            v::intVal()->validate($id)
        ];
		

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		 
		//$event_data = Models\EventSeatCategories::where('event_id', '=', $id);
		
		$event = Models\Event::find($id);
		
		if($event['seats_on_map'] == 'Y'){
			return $this->response->withHeader('Location', base_url.'/admin/events/groups/edit/'. $event['event_group_id']);
			exit;
		}else{
			
		$event_date =  strtotime(date('d-m-Y',strtotime($event['date']))); 
	    $today = strtotime(date('d-m-Y'));
		if($event_date < $today){
			
		    $is_expired = 'Y';
			$disabled = 'disabled="disabled"';
			
		}else{
			$is_expired = 'N';
			$disabled = '';
			
			
		}
         $seats_map = Models\EventSeatCategories::where('event_id','=', $id)->get();
		 
		
		 //ddump($seats_map); exit;										  
		//exit;
		if( $seats_map->isEmpty() ){
			$seats_list = '';
			$seat_order = 1; 
		}else{
			$seats_list = '';
			$i=0;
			 foreach($seats_map as $key=>$row){
				
			 $counterRow = explode(',',$row['from_range']);
			    $onlick = 'removeAudSeatTicket('.$row['id'].')';
			    $seats_list .= '<div id="aud_seats_div_data_'.$row['id'].'" class="col-md-12">';
				$seats_list .= '<div class="row" style="padding-top:10px">';
                $seats_list .= '<div class="col-md-2">Category Name</div>';
                $seats_list .= '<div class="col-md-3">';
                $seats_list .= '<input type="text" class="form-control" name="seat_category_old['.$key.']" id="seat_category_old_'.$key.'" placeholder="Enter Category" value="'.$row['seat_category'].'">';
				$seats_list .= '<input type="hidden" name="seat_category_id_old['.$key.']" value="'.$row['id'].'">';
                $seats_list .= '</div>';
                $seats_list .= '<div class="col-md-2">';
                $seats_list .= '<input type="text" style="width: 128px;" maxlength="3" class="form-control"  id="seat_row_from_old_'.$key.'" placeholder="Row From" value="'.$row['seat_row_from'].'" disabled>';
                $seats_list .= '</div>';
                $seats_list .= '<div class="col-md-3">';
                $seats_list .= '<input type="text" style="width: 128px;" maxlength="3" class="form-control range_to"  id="seat_row_to_old_'.$key.'" placeholder="Row To" value="'.$row['seat_row_to'].'"  disabled>';
                $seats_list .= '</div>';
                $seats_list .= '<div class="col-md-2">';
                $seats_list .= '<div  class="btn-sm btn btn-danger m-btn m-btn--icon m-btn--pill" onclick="'.$onlick.'"> <span> <i class="la la-trash-o"></i> <span> Delete </span> </span> </div>';
                $seats_list .= '</div>';
                $seats_list .= '</div>';
			
			 foreach($counterRow as $keyInner=>$val){
				  $counter = time().$i.get_random_int();
				  //$key = time().get_random_int();
				  $table_id = time().get_random_int(3);
				$seats_list .= '<div class="row_new_'.($key.$val).'" id="div_row_rm_'.$table_id.'">';
				$seats_list .= '<div class="row" style="margin-bottom:20px !important">';
				$seats_list .= '<div class="col-md-2">&nbsp;</div>';
				$seats_list .= '<div class="col-md-2" style="margin-top:14px;">Row &nbsp;&nbsp; '.$counterRow[$keyInner].'</div>';
				$seats_list .= '<div class="col-md-7" style="margin-top:5px">';
				
				
						  
				  $seat_rows = Models\RowSeats::where('event_seat_categories_id','=', $row['id'])
				                              ->where('row_number','=', $counterRow[$keyInner])->get();
					//echo 'Row #'.$counterRow[$keyInner].'<br>';	
				$seats_list .= '<table class="table table-bordered table-hover" id="tableAddRow_'.$table_id.'">';
				$seats_list .= '<thead>';
                $seats_list .= '<tr>';
                $seats_list .= '<th class="text-left">From </th>';
                $seats_list .= '<th class="text-left">To</th>';
                $seats_list .= '<th class="text-left">Even Order?</th>';
                $seats_list .= '<th style="width:10px;">';
			    $seats_list .= '<span class="la la-remove default_c" id="addBtn_'.$row['id'].'" 
				onclick="del_row('.$key.','.$keyInner.',\''.$val.'\','.$counter.',\''.$counterRow[$keyInner].'\','.$table_id.')"></span>';
			    $seats_list .= '</th>';
                $seats_list .= '</tr>';
                $seats_list .= '</thead>';	
				$seats_list .= '<tbody>';					  
				 foreach($seat_rows as $innerKeyInner=>$seatRow){
					$onclickRow = 'removeAudSeatRow('.$seatRow['id'].')';
					$seat_order = $seatRow['seat_order'];
					$seats_list .= '<input type="hidden" class="row_number_old_id_new_class_'.$key.'_'.$keyInner.'" value="'.$seatRow['id'].'">';
					$seats_list .= '<input type="hidden" class="row_seat_id_old_'.$key.'_'.$keyInner.'" value="'.$seatRow['id'].'">';
					$seats_list .= '<tr id="tr_num_row_'.$seatRow['id'].'">';
					$seats_list .= '<td>';
					$seats_list .='<input type="text" name="from_value_old['.$key.']['.$keyInner.'][]" placeholder="From" class="form-control" value="'.$seatRow['seat_from'].'">';
					$seats_list .= '</td>';
					$seats_list .= '<td>';
					$seats_list .= '<input type="text" name="to_value_old['.$key.']['.$keyInner.'][]" placeholder="To" class="form-control" value="'.$seatRow['seat_to'].'">';
					$seats_list .= '</td>';
					$seats_list .= '<td>';
					$checked = '';
					 if($seat_order == 2){
						 $checked = 'checked="checked"';
					 }
					$seats_list .= '<input type="checkbox" name="seat_order_old['.$key.']['.$keyInner.'][]" class="form-control"  value="2" '.$checked.'>';
					$seats_list .= '<input type="hidden" name="row_number_old['.$key.']['.$keyInner.'][]" value="'.$counterRow[$keyInner].'">';
					$seats_list .= '<input type="hidden" name="row_seat_id_old['.$key.']['.$keyInner.'][]" value="'.$seatRow['id'].'">';
					$seats_list .= '</td>';
					$seats_list .= '<td>';
					$seats_list .= '<span class="la la-minus  default_c trRemove" id="addBtnRemove_'.$row['id'].'" onclick="'.$onclickRow.'"></span>';
					$seats_list .= '</td>';
					$seats_list .= '</tr>';
					
				 } 
				  $seats_list .= '</tbody>';
				  $seats_list .= '</table>';
				  $seats_list .= '</div>';
				  $seats_list .= '</div>';
				  $seats_list .= '</div>';
			 }
			 
			$seats_list .= '<div class="row">
							  <div class="col-md-2">&nbsp;</div>
							  <div class="col-md-2" style="margin-top:14px;">Category Price</div>
							  <div class="col-md-3">
								<input type="text" class="form-control" name="category_price_old['.$key.']" placeholder="Category Price" value="'.$row['category_price'].'">
							  </div>
							</div>';
			  
			$seats_list .= '</div>';
		 }
			
			
			
		}
	  //exit;
		  $params = array( 'title' => 'Set up seat map for event',
		                    'data' => $event,
							'seats_list' => $seats_list,
							'seat_order' => $seat_order,
							'is_expired' => $is_expired);
          return $this->render($response, ADMIN_VIEW.'/Event/edit_map.twig',$params);
		}
	}
	
	// Save event map
	public function saveEventSeatTicketMap($request, $response){
		  $event_id   = $request->getParam('id');
		  $data = array('status' => 0);
		  $eventRole = Models\EventSeatCategories::where('event_id', '=', $event_id)->update($data);	
		  $this->saveEventSeatTicketsData($event_id);
		   return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode(array('status' => TRUE)));
	}
	
	// Update event map
	public function updateEventSeatTicketMap($request, $response){
		  $event_id   = $request->getParam('id');
		  $auditorium_id =  Models\Event::where('id', '=', $event_id)->first()->auditorium_id;
		  //ddump($_REQUEST); exit;
		  
		  $this->saveRowSeatsTable($event_id, $auditorium_id);
		  $this->updateRowSeatsTable();
		  //$this->updateEventSeatTicketsData($event_id);
		   return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode(array('status' => TRUE)));
	}
	
	// Delete Event seat
	public function deleteEventSeatById($request, $response, $args){
		$id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		// echo $created_date. ' == '.$event_id; exit;
		 $delete = Models\RowSeats::where('event_seat_categories_id','=',$id)->delete();
		 $delete = Models\EventSeatCategories::find($id)->delete();
		//return $response->withJson(json_encode(array("status" => TRUE)));
	}
	
	// Delete Event seat row
	public function deleteEventSeatRowById($request, $response, $args){
		$id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		// echo $created_date. ' == '.$event_id; exit;
		 $delete = Models\RowSeats::find($id)->delete();
		//return $response->withJson(json_encode(array("status" => TRUE)));
	}
	
	// Delete seat row
	public function deleteRowSeatById($request, $response, $args){
		$id = $args['id'];
		$row_number = $args['row_number'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		$event_seat_categories_id =  Models\RowSeats::where('id', '=', $id)->first()->event_seat_categories_id;
		// get the range
		 $from_range =  Models\EventSeatCategories::where('id', '=', $event_seat_categories_id)->first()->from_range;
		
		 $from_range_exp = explode(',', $from_range);
		 $from_range_new = '';
		 foreach($from_range_exp as $exp){
			if($exp !=  $row_number){
			  $from_range_new .= $exp.',';	
			}
		 }
		 $from_range_update = rtrim($from_range_new,','); 
		 
		 if($from_range_update == ''){
			 $catDel = Models\EventSeatCategories::where('id', '=', $event_seat_categories_id)->delete();	
			 $delete = Models\RowSeats::where('event_seat_categories_id', '=',$event_seat_categories_id)->delete();
		 }else{
		   $data = array('from_range' => $from_range_update);
		   $eventCats = Models\EventSeatCategories::where('id', '=', $event_seat_categories_id)->update($data);	
		 }
		 		 
		 $delete = Models\RowSeats::where('event_seat_categories_id', '=',$event_seat_categories_id)
		                          ->where('row_number', '=',$row_number)->delete();
		//return $response->withJson(json_encode(array("status" => TRUE)));
	}
  
    // Update Rows Seats Table Data
    public function updateRowSeatsTable(){
		
	  if( isset($_REQUEST['seat_category_old']) && !empty($_REQUEST['seat_category_old']) ){
			 $m=0;
			  foreach($_REQUEST['seat_category_old'] as $key=>$seat_category):
			      $category_price = $_REQUEST['category_price_old'][$key]; 
				  $seat_category_id = $_REQUEST['seat_category_id_old'][$key]; 	
				  //echo 'Here = '. $seat_category_id. '<br>';		    
				   $seat_category = $seat_category;
				   $update_seat_category_table = array('seat_category' => $seat_category,
				                                      'category_price' => $category_price);	
													  
				   	//ddump($update_seat_category_table);								  
				   $updateSeatCats = Models\EventSeatCategories::where('id', '=', $seat_category_id)
				                     ->update($update_seat_category_table);	
				   $total_qantity = 0;
				   $k=0;
					 foreach($_REQUEST['from_value_old'][$key] as $innerkey=>$val){
						   $row_number = $_REQUEST['row_number_old'][$key][$innerkey];
						   $row_seat_id =  $_REQUEST['row_seat_id_old'][$key][$innerkey];
						   $seat_order_old = $_REQUEST['seat_order_old'][$key];
						   //ddump($seat_order_old);
						   $from = $val;
						   $to   = $_REQUEST['to_value_old'][$key][$innerkey];
						  if( !empty($from) && !empty($to) ){
							$total_qantity = findQuantity($from, $to);
							foreach($row_number as $innerKey1=>$val){
							 $event_seat_categories_id = $row_seat_id;
							 $row_number = $val;
							 $seat_from = $from[$innerKey1];
							 $seat_to = $to[$innerKey1];
							 $total_qantity = findQuantity($from[$innerKey1], $to[$innerKey1]);
							 $seat_order_new = $seat_order_old[$innerkey][$innerKey1];
							
							 if(isset($seat_order_new) && !empty($seat_order_new)) {
								 $seat_order = 2;
							  }else{
								 $seat_order = 1; 
							  }
							  if($seat_order == 2){
							    $total_qantity = intval($total_qantity/2);
							  }
							
							 $row_seat_id = $row_seat_id;
							 $update_row_seats_table = array('row_number' => $row_number,
															'seat_from' => $seat_from,
															'seat_to' => $seat_to,
															'total_qantity' => $total_qantity,
															'seat_order' => $seat_order);
							//ddump($update_row_seats_table);							
							 							
							 $updateSeatRows = Models\RowSeats::where('id', '=', $row_seat_id[$innerKey1])
				                     ->update($update_row_seats_table);			 
							}
						  }
				   $k++; }
				   // Check if new data is posted then save them
				   if( isset($_REQUEST['from_value_old_new']) && !empty($_REQUEST['from_value_old_new']) ){
				      
					  foreach($_REQUEST['from_value_old_new'][$key] as $innerkey=>$val){
						  $row_number = $_REQUEST['row_number_old_new'][$key][$innerkey];
						  $seat_order = $_REQUEST['seat_order_old_new'][$key][$innerkey];
						  $row_seat_id =  $_REQUEST['row_seat_id_old_new'][$key][$innerkey];
						  
						  $from = $val;
						  $to   = $_REQUEST['to_value_old_new'][$key][$innerkey];
						  if( !empty($from) && !empty($to) ){
							 
							 $total_qantity = 0;			
							foreach($row_number as $innerKey1=>$valD){
							
							$seat_order = $seat_order[$innerKey1];	
							if(isset($seat_order) && !empty($seat_order)) {
								 $seat_order = 2;
							  }else{
								 $seat_order = 1; 
							  }
							 
							 $total_qantity = findQuantity($from[$innerKey1], $to[$innerKey1]);
							 if($seat_order == 2){
							   $total_qantity = intval($total_qantity/2);
							 }
							 
							  $data = array('event_seat_categories_id' => $row_seat_id[$innerkey],
							                'row_number' => $row_number,
											'seat_from' => $from,
											'seat_to' => $to,
											'total_qantity' => $total_qantity,
											'seat_order' => $seat_order);
							 ///ddump($data);				
							 $event_seat_categories_id = $row_seat_id[$innerKey1];
							 $event_seat_categories_id_new = Models\RowSeats::
							                                 where('id', '=', $event_seat_categories_id)
															 ->first()->event_seat_categories_id;
							 $audSTCRowsNew = new Models\RowSeats;
							 $audSTCRowsNew->event_seat_categories_id = $event_seat_categories_id_new;
							 $audSTCRowsNew->row_number = $valD;
							 $audSTCRowsNew->seat_from = $from[$innerKey1];
							 $audSTCRowsNew->seat_to = $to[$innerKey1];
							 $audSTCRowsNew->total_qantity = $total_qantity;
							 $audSTCRowsNew->seat_order = $seat_order;
							 $audSTCRowsNew->save();	
							
							}				
						}
				   }
				  }
			 $m++;
			 //exit;
			  endforeach;
		}
	}
	
	// Save New Row seats table
	public function saveNewRowSeatsTable(){
	  if( isset($_REQUEST['from_value_old_new']) && !empty($_REQUEST['from_value_old_new']) ){
		  
			      $m=0;
				  foreach($_REQUEST['seat_category_old'] as $key=>$seat_category):
				  
				   $row_id_new = $_REQUEST['row_number_old_new'][$key];
				  
				   $k=0;
					 foreach($_REQUEST['from_value_old_new'][$key] as $innerkey=>$val){
						  $row_number = $_REQUEST['row_number_old_new'][$key][$innerkey];
						  $seat_order = $_REQUEST['seat_order_old_new'][$key][$innerkey];
						  $row_seat_id =  $_REQUEST['row_seat_id_old_new'][$key];
						 
						  $from = $val;
						  $to   = $_REQUEST['to_value_old_new'][$key][$innerkey];
						  if( !empty($from) && !empty($to) ){
							 
							  $data = array('event_seat_categories_id' => $row_seat_id[$innerKey],
							                'row_number' => $row_number,
											'seat_from' => $from,
											'seat_to' => $to,
											'total_qantity' => $total_qantity,
											'seat_order' => $seat_order);
							ddump($data);	
							 $total_qantity = 0;			
							foreach($row_number as $innerKey1=>$valD){
								
							if(isset($seat_order) && !empty($seat_order)) {
								 $seat_order = 2;
							  }else{
								 $seat_order = 1; 
							  }
							 
							 $total_qantity = findQuantity($from[$innerKey1], $to[$innerKey1]);
							 if($seat_order == 2){
							   $total_qantity = intval($total_qantity/2);
							 }
							 $event_seat_categories_id = $row_seat_id[$innerKey1];
							 $audSTCRowsNew = new Models\RowSeats;
							 $audSTCRowsNew->event_seat_categories_id = $event_seat_categories_id;
							 $audSTCRowsNew->row_number = $valD;
							 $audSTCRowsNew->seat_from = $from[$innerKey1];
							 $audSTCRowsNew->seat_to = $to[$innerKey1];
							 $audSTCRowsNew->total_qantity = $total_qantity;
							 $audSTCRowsNew->seat_order = $seat_order;
							 //$audSTCRowsNew->save();	
							
							
							}				
						  }
				   $k++; }
			 $m++;
			exit;
			 endforeach;
		}	
	}
	
	// Save Row seats table
	public function saveRowSeatsTable($event_id, $auditorium_id){
	  if( isset($_REQUEST['seat_category']) && !empty($_REQUEST['seat_category']) ){
			 $m=0;
			  foreach($_REQUEST['seat_category'] as $key=>$seat_category):
			  
			      $category_price = $_REQUEST['category_price'][$key]; 
				  $first_char = $_REQUEST['seat_row_from'][$key]; 
				  $second_char = $_REQUEST['seat_row_to'][$key]; 
				  $from_range = join(',',range(''.$first_char.'', ''.$second_char.''));
			      $event_seats = array('event_id' => $event_id,
				                       'auditorium_id' => $auditorium_id,
									   'seat_category' => $seat_category,
									   'seat_row_from' => $first_char,
									   'seat_row_to' => $second_char,
									   'category_price' => $category_price,
									   'from_range' => $from_range,
									   ); 				   
				   $audSTC = new Models\EventSeatCategories;
				   $audSTC->auditorium_id = $auditorium_id;
				   $audSTC->event_id = $event_id;
				   $audSTC->seat_category = $seat_category;
				   $audSTC->seat_row_from = $first_char;
				   $audSTC->seat_row_to = $second_char;
				   $audSTC->from_range  = join(',',range(''.$first_char.'', ''.$second_char.''));
				   $audSTC->category_price = $category_price;
				   $audSTC->save(); 
				   $row_id = $audSTC->id;
				   $total_qantity = 0;
				   $k=0;
					 foreach($_REQUEST['from_value'][$key] as $innerkey=>$val){
						  $row_number = $_REQUEST['row_number'][$key][$innerkey];
						  $seat_order_array = $_REQUEST['seat_order'][$key];
						  //ddump($seat_order_array);
						  $from = $val;
						  $to   = $_REQUEST['to_value'][$key][$innerkey];
						  if( !empty($from) && !empty($to) ){
							 
							  $data = array('event_seat_categories_id' => $row_id,
							                'row_number' => $row_number,
											'seat_from' => $from,
											'seat_to' => $to,
											'total_qantity' => $total_qantity,
											'seat_order' => $seat_order);
							//ddump($data);
										
							foreach($row_number as $innerKey1=>$val){
								
								$posted_seat_order = $seat_order_array[$innerkey][$innerKey1];
								
								if(isset($posted_seat_order) && !empty($posted_seat_order)) {
									 $seat_order = 2;
									 
								  }else{
									 $seat_order = 1; 
									
								  }
								$total_qantity = findQuantity($from, $to);
								 if($seat_order == 2){
								   $total_qantity = intval($total_qantity/2);
								 }
								 $audSTCRows = new Models\RowSeats;
								 $audSTCRows->event_seat_categories_id = $row_id;
								 $audSTCRows->row_number = $val;
								 $audSTCRows->seat_from = $from[$innerKey1];
								 $audSTCRows->seat_to = $to[$innerKey1];
								 $audSTCRows->total_qantity = findQuantity($from[$innerKey1], $to[$innerKey1]);
								 $audSTCRows->seat_order = $seat_order;
								 $audSTCRows->save();
								
							}				
						  }
				   $k++; }
			 $m++;
			 
			  endforeach;
		}	
	}
	
	
	
	
	
	
	
}
