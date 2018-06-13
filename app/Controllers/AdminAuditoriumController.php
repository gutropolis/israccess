<?php
namespace App\Controllers;

use App\Models;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v;

/*
*  Admin Auditorium Controller
*  CRUD of Auditorium
   Available Functions
   1. auditoriums
   2. getAjaxAuditoriumsList
   3. add
   4. saveAuditorium
   5. getAuditoriumById
   6. updateAuditorium
   7. deleteAuditoriumById
   8. getAuditoriumMapById
   9. saveAuditoriumSeatTickets
   10. deleteAudSeatsById
   11. getAuditoriumSeatMapById
   12. getAuditoriumSeatMapEventById
   
*/
class AdminAuditoriumController extends Base 
{
	protected $container;
	protected $lang;
	/*
	*  Class Constructor
	*/
	public function __construct($container)
	{
		$this->container = $container;
		$this->lang =  $this->container->view['adminLang'];
	}
	
	
	// Main function to display list of auditorium
	public function auditoriums($request, $response) {
		
        $params = array( 'title' => $this->lang['auditorium_all_txt'],
		                 'current_url' => $request->getUri()->getPath());
        return $this->render($response, ADMIN_VIEW.'/Auditorium/auditoriums.twig',$params);
    }
	
	// Ajax Auditoriums list
	public function getAjaxAuditoriumsList($request, $response){
		$isSearched = $drpSearch = false; // set to false if user did not search something
		$conditions = array();
		$whereData =  $customSearch = $prefix = '';
		$whereData =  $customSearch = $prefix = '';
		if($request->getParam('query') != null){
			 $isSearched = true; // user has searched something
			 $fields = array('name');
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
		    $total   = Models\Auditorium::whereRaw($whereData)->count(); // get count 
		}else{
			$total   = Models\Auditorium::get()->count(); // get count 
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
		    $categories_list = Models\Auditorium::whereRaw($whereData)->skip($offset*$perPageLimit)->take($perPageLimit)->orderBy($field, $sort)->get();
		}else{
			$categories_list = Models\Auditorium::skip($offset*$perPageLimit)->take($perPageLimit)->orderBy($field, $sort)->get();
		}
		
		$data = array();
		foreach($categories_list as $get){
			$aud_file = $get->background_file;
			// Check if a picture is not uploaded show default picture
			//if($aud_file <> '' && file_exists(AUDITORIUM_ROOT_PATH.'/'.$aud_file)){
			if($aud_file <> ''){	
				$background_file = AUDITORIUM_WEB_PATH.'/thumbs/'.$aud_file;
			}else{
			  	$background_file = DEFAULT_IMG;
			}
			
		  	$array_data = array();
			$array_data['id']  = $get->id;
            $array_data['name']  = $get->name;
            $array_data['background_file']  = $background_file;
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
    
	// add auditorium function
	public function add($request, $response) {
		
        $params = array( 'title' => $this->lang['auditorium_add_txt'],
		                 'current_url' => $request->getUri()->getPath());
        return $this->render($response, ADMIN_VIEW.'/Auditorium/add.twig',$params);
    }
	
	// Save auditorium from admin
	public function saveAuditorium($request, $response){
		
		
	   $auditoriumName = $request->getParam('auditorium_name');
	   $cityName = $request->getParam('city_name');
	   $auditoriumAccess = $request->getParam('auditorium_access');
	   $wazeName = $request->getParam('waze_name');
	   $description = $request->getParam('description');
	   $auditoriumAddress = $request->getParam('auditorium_address');
	   $lat = $request->getParam('lat');
	   $lng = $request->getParam('lng');
	   $type = $request->getParam('type');
	   $auditorium_key = $request->getParam('auditorium_key');
	   $auditorium_map  = $request->getParam('auditorium_seats_map');
	   
	   $auditoriumExist = Models\Auditorium::where('name', '=', $auditoriumName)->first();
	   if($auditoriumExist){
		 echo json_encode(array("status" => 'duplicate', 
		                  'message' => $this->lang['auditorium_txt'].' (<strong>'.$auditoriumName. '</strong>) '.$this->lang['common_already_exist_txt']));
		 exit();	   
	   }else{
		   if(isset($_FILES) && $_FILES['auditorium_picture']['name'] <> ''){
			   $allowedExtensions = allowedExtensions();
			   $file = $_FILES['auditorium_picture']['tmp_name'];
			   $file_name = explode('.',$_FILES['auditorium_picture']['name']);
			   $file_extension = end($file_name);
			   if(!in_array($file_extension, $allowedExtensions)){
				   echo json_encode(array("status" => 'file_error', 
					               'message' => $this->lang['common_file_with_type_txt'].' [<strong>'.$file_extension. '</strong>] '.$this->lang['common_is_not_allowed_txt']));	
				   exit();
			   }else{
				    $file_new_name = md5(uniqid());
				    $filename = $file_new_name.'.'.$file_extension;
					$file = $_FILES['auditorium_picture']['tmp_name'];
				   if(!empty($file)){
					   $resizedFile = AUDITORIUM_ROOT_PATH.'/thumbs/'.$filename;
					   smart_resize_image($file, null,  THUMB_WEIGHT, THUMB_HEIGHT , false , $resizedFile , false , false ,100 );
					  move_uploaded_file($file, AUDITORIUM_ROOT_PATH.'/'.$filename);
					  $background_file = $filename;
					 }else{
					  $background_file = '';	
				   }

			   }
			   
		   }else{
			   $background_file = '';
		   }
		   // Save to auditorium table
		   $aud = new Models\Auditorium;
		   $aud->name = $auditoriumName;
		   $aud->background_file = $background_file;
		   $aud->address = $auditoriumAddress;
		   $aud->access = $auditoriumAccess;
		   $aud->waze_name = $wazeName;
		   $aud->detail = $description;
		   $aud->lat = $lat;
		   $aud->lng = $lng;
		   $aud->type = $type;
		   $aud->auditorium_key = $auditorium_key;
	       $aud->auditorium_map  = $auditorium_map;
		   $aud->save();
		   $auditorium_id = $aud->id;
		   return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode(array('status' => TRUE)));
		  
	   }
	   
	   
	}
	
	
	// Get Auditorium by id
	public function getAuditoriumById($request, $response, $args){
		
		$id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		$auditoriums = Models\Auditorium::find($id);
		$auditoriums['file_web_path'] = AUDITORIUM_WEB_PATH;
		$auditoriums['lat'] = ($auditoriums['lat'] == '') ? 31.771959  : $auditoriums['lat'];
		$auditoriums['lng'] = ($auditoriums['lng'] == '') ? 35.217018 : $auditoriums['lng'];
		$url = explode('/', $request->getUri()->getPath());
		$current_url = $url[0].'/'.$url[1].'/'.$url[2];
		// get booking seats for this auditorium
		$auditoriums_seats = Models\AuditoriumSeatCategory::where('auditorium_id','=',$id)->get();
		
		if( $auditoriums_seats->isEmpty() ){
		    $seats_list = '';
		}else{
			
			$seats_list = '';
			$i=0;
			foreach($auditoriums_seats as $key=>$row){
				$counterRow = explode(',',$row['from_range']);
				
			$seats_list .= '<div id="aud_seats_div_data_'.$row['id'].'" class="col-md-12">
								<div class="row" style="padding-top:10px">
								  <div class="col-md-2">Category Name</div>
								  <div class="col-md-3">
									<input type="text" class="form-control" name="seat_category_old['.$i.']" id="seat_category_'.$row['id'].'"  placeholder="Enter Category" value="'.$row['seat_category'].'" />
								  </div>
								  <div class="col-md-2">
									<input type="text" style="width: 128px;" maxlength="1" class="form-control" name="seat_row_from[]" id="seat_row_from_'.$row['id'].'" readonly disabled placeholder="Row From" value="'.$row['seat_row_from'].'" />
								  </div>
								  <div class="col-md-3">
									<input type="text" style="width: 128px;" maxlength="2" class="form-control range_to" name="seat_row_to[]"  id="seat_row_to_'.$row['id'].'" readonly disabled  value="'.$row['seat_row_to'].'"  placeholder="Row To" onkeyup="getUserValue('.$row['id'].')"/>
								  </div>
								  <div class="col-md-2">
									<div  class="btn-sm btn btn-danger m-btn m-btn--icon m-btn--pill" onclick="removeAudSeatTicket('.$row['id'].')"> <span> <i class="la la-trash-o"></i> <span> Delete </span> </span> </div>
								  </div>
								  </div>';
								  
								  $aud_json = unserialize($row['seat_rows_json']);
								  
								  foreach($aud_json as $keyIn=>$rowJ){
									
								  $seats_list .= '<div class="row_new_1_'.$keyIn.'">
								  <div class="row" style="margin-bottom:20px !important">
								  <div class="col-md-2">&nbsp;</div>
								  <div class="col-md-2" style="margin-top:14px;">Row &nbsp;&nbsp; '.$counterRow[$keyIn].'</div>
								  <div class="col-md-7">
								  <div class="m-ion-range-slider"><input type="hidden" value="'.$rowJ['slider_range_from_value'].';'.$rowJ['slider_range_to_value'].'" name="slider_range_old[]" class="m_slider_3_slider slider_range_edit" /></div>
								  </div>
								  </div>
								  </div>';
								 }
								$seats_list .= '</div>';
							$seats_list .='<div class="row" id="aud_seats_div_data_row_'.$row['id'].'" >
								  <div class="col-md-2">&nbsp;</div>
								  <div class="col-md-2" style="margin-top:14px;">Category Price</div>
								  <div class="col-md-3"><input type="text" class="form-control" name="category_price_old[]"   placeholder="Category Price" value="'.$row['category_price'].'" /></div>
								  </div>
								  <input type="hidden" name="audSeatRow_id['.$i.']" value="'.$row['id'].'">
								  <input type="hidden" name="seat_row_from_old['.$i.']" value="'.$row['seat_row_from'].'">
								  <input type="hidden" name="seat_row_to_old['.$i.']" value="'.$row['seat_row_to'].'">
								  ';
							
			$i++;} 
			
		}
		
		/*ddump($auditoriums_seats); 
		exit;*/
		$params = array( 'title' => $this->lang['audiotrium_update_txt'],
		                'data' => $auditoriums,
						'current_url' => $current_url,
						'seats_list' => $seats_list);
        return $this->render($response, ADMIN_VIEW.'/Auditorium/edit.twig',$params);
		
	}
	
	// Update auditorium from admin
	public function updateAuditorium($request, $response){
		//ddump($_REQUEST); exit;
	   $id   = $request->getParam('id');
	   $auditoriumName = $request->getParam('auditorium_name');
	   $auditoriumPictureOld = $request->getParam('auditorium_picture_old');
	   $cityName = $request->getParam('city_name');
	   $auditoriumAccess = $request->getParam('auditorium_access');
	   $wazeName = $request->getParam('waze_name');
	   $description = $request->getParam('description');
	   $auditoriumAddress = $request->getParam('auditorium_address');
	   $lat = $request->getParam('lat');
	   $lat_old = $request->getParam('lat_old');
	   $lng = $request->getParam('lng');
	   $lng_old = $request->getParam('lng_old');
	   $type = $request->getParam('type');
	   $auditorium_key = $request->getParam('auditorium_key');
	   $auditorium_map  = $request->getParam('auditorium_seats_map');
	   $auditoriumExist = Models\Auditorium::where('name', '=', $auditoriumName)->where('id', '!=', $id)->first();
	   if($auditoriumExist){
		 echo json_encode(array("status" => 'duplicate', 
		                  'message' => $this->lang['auditorium_txt'].' (<strong>'.$auditoriumName. '</strong>) '.$this->lang['common_already_exist_txt']));
		 exit();	   
	   }else{
		   if(isset($_FILES) && $_FILES['auditorium_picture']['name'] <> ''){
			   $allowedExtensions = allowedExtensions();
			   $file = $_FILES['auditorium_picture']['tmp_name'];
			   $file_name = explode('.',$_FILES['auditorium_picture']['name']);
			   $file_extension = end($file_name);
			   if(!in_array($file_extension, $allowedExtensions)){
				   echo json_encode(array("status" => 'file_error', 
					               'message' => $this->lang['common_file_with_type_txt'].' [<strong>'.$file_extension. '</strong>] '.$this->lang['common_is_not_allowed_txt']));	
				   exit();
			   }else{
				    $file_new_name = md5(uniqid());
				    $filename = $file_new_name.'.'.$file_extension;
					$file = $_FILES['auditorium_picture']['tmp_name'];
				   if(!empty($file)){
					   $resizedFile = AUDITORIUM_ROOT_PATH.'/thumbs/'.$filename;
					   smart_resize_image($file , null,  THUMB_WEIGHT, THUMB_HEIGHT , false , $resizedFile , false , false ,100 );
					  move_uploaded_file($file, AUDITORIUM_ROOT_PATH.'/'.$filename);
					  $background_file = $filename;
					 }else{
					  $background_file = '';	
				   }

			   }
			   
		   }else{
			   $background_file = '';
		   }
           
		   if($_FILES['auditorium_picture']['tmp_name'] <> ''){
			   // Delete old images
			   if($auditoriumPictureOld <> ''){
					 @unlink(AUDITORIUM_ROOT_PATH.'/thumbs/'.$auditoriumPictureOld);
					 @unlink(AUDITORIUM_ROOT_PATH.'/'.$auditoriumPictureOld);
			   }
			   $background_file = $background_file;
		   }else{
			   $background_file = $auditoriumPictureOld; 
		   }
		   if($lat == $lat_old || empty($lat) ){
			   $lat = $lat_old;
		   }else{
			   $lat = $lat;
		   }
		   
		   if($lng == $lng_old || empty($lng)){
			   $lng = $lng_old;
		   }else{
			   $lng = $lng;
		   }
		  
		   
		   // Save to auditorium table
		   $data = array('name' => $auditoriumName,
		                 'background_file' => $background_file,
						 'address' => $auditoriumAddress,
						 'access' => $auditoriumAccess,
						 'waze_name' => $wazeName,
						 'detail' => $description,
						 'lat' => $lat,
						 'lng' => $lng,
						 'type' => $type,
						 'auditorium_key' => $auditorium_key,
	                     'auditorium_map'  => $auditorium_map);
		   $auditorium = Models\Auditorium::where('id', '=', $id)->update($data);
		   return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode(array('status' => TRUE)));
		  
	   }
	   
	   
	}
	
	// Delete Auditorium
	public function deleteAuditoriumById($request, $response, $args){
		$id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		// Check if this category has a picture uploaded.
		$pictureExist = Models\Auditorium::where('id', '=', $id)->first()->background_file;
	   if($pictureExist){
		   // Unlink the picture
		   @unlink(AUDITORIUM_ROOT_PATH.'/thumbs/'.$pictureExist);
		   @unlink(AUDITORIUM_ROOT_PATH.'/'.$pictureExist);
	   }
		$delete = Models\Auditorium::find($id)->delete();
		return $response->withJson(json_encode(array("status" => TRUE)));
	}
	
	// Get Auditorium map  by id
	public function getAuditoriumMapById($request, $response, $args){
		$id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		$with_map = Models\Auditorium::where('id', '=', $id)->first()->with_map;
		if($with_map == 0 || empty($with_map) ){
			$with_map_value = 'No';
		}else{
			$with_map_value = 'Yes';
		}
		echo  $with_map_value;
		
	}
	
	
	// Save auditorium seats tickets
	public function saveAuditoriumSeatTickets($auditorium_id){
		//ddump($_REQUEST);
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
				 /*$data = array('auditorium_id' => $auditorium_id,
				              'seat_category' => $seat_category, 
							  'seat_row_from' => $first_char,
							  'seat_row_to' => $second_char,
							  'seat_rows_json' => serialize($seat_rows_json),
							  'category_price' => 123,
							  'total_qantity' => $total_qantity
							  ); */
				   $audSTC = new Models\AuditoriumSeatCategory;
				   $audSTC->auditorium_id = $auditorium_id;
				   $audSTC->seat_category = $seat_category;
				   $audSTC->seat_row_from = $first_char;
				   $audSTC->seat_row_to = $second_char;
				   $audSTC->seat_rows_json = serialize($seat_rows_json);
				   $audSTC->category_price = $category_price;
				   $audSTC->total_qantity = $total_qantity;
				   $audSTC->from_range  = join(',',range(''.$first_char.'', ''.$second_char.''));
				   $audSTC->save(); 
			  $i++;
			  endforeach;
			  
		}
		
		if( isset($_REQUEST['seat_category_old']) && !empty($_REQUEST['seat_category_old']) ){
			  $total_qantity = 0 ;
			  $i=0;
			  $j=0;
			  foreach($_REQUEST['seat_category_old'] as $key=>$seat_category):
			      $category_price = $_REQUEST['category_price_old'][$key]; 
				  $first_char = $_REQUEST['seat_row_from_old'][$key]; 
				  $second_char = $_REQUEST['seat_row_to_old'][$key]; 
			      $seat_rows_json = array();  
				  $loop_limit = sizeof($_REQUEST['seat_category_old']);
				  $total_qantity = 0;
				  foreach($_REQUEST['slider_range_old'] as $innerKey => $slider_range_name){
					  $total_qantity += sizeOfnumbers($slider_range_name);
					  $seat_rows_json[] = array('slider_range_from_value' => rangeFrom($slider_range_name),
											   'slider_range_to_value'   => rangeTo($slider_range_name));
				  }
				  $id = $_REQUEST['audSeatRow_id'][$key];
				  $data = array('auditorium_id' => $auditorium_id,
				                'seat_category' => $seat_category, 
							     //'seat_rows_json' => serialize($seat_rows_json),
							     'category_price' => $category_price,
							     //'total_qantity' => $total_qantity,
							     'from_range' => join(',',range(''.$first_char.'', ''.$second_char.''))
							  ); 
							  
				   /*$audSTC = new Models\AuditoriumSeatCategory;
				   $audSTC->auditorium_id = $auditorium_id;
				   $audSTC->seat_category = $seat_category;
				   $audSTC->seat_row_from = $first_char;
				   $audSTC->seat_row_to = $second_char;
				   $audSTC->seat_rows_json = serialize($seat_rows_json);
				   $audSTC->category_price = $category_price;
				   $audSTC->total_qantity = $total_qantity;*/
				   $eventRole = Models\AuditoriumSeatCategory::where('id', '=', $id)->update($data);	
			  $i++;
			  endforeach;
			  
		}
	}
	
	
	
	// Delete Auditorium Seat Tickets
	public function deleteAudSeatsById($request, $response, $args){
		$id = $args['id'];
		
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		
		$delete = Models\AuditoriumSeatCategory::find($id)->delete();
		//return $response->withJson(json_encode(array("status" => TRUE)));
	}
	
	// Get Auditorium seat map  by id
	public function getAuditoriumSeatMapById($request, $response, $args){
		$id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		$seats_map = Models\AuditoriumSeatCategory::where('auditorium_id','=', $id)->get();
		$cat_list = '';
		if( $seats_map->isEmpty() ){
			$cat_list .= '<div class="row" style="padding-left:120px; color:red">No data found</div>';
		}else{
			$i=1;
			foreach($seats_map as $row){
			  	$cat_list .= '<div class="row" style="margin-top:10px">
				  <div class="col-md-1">'.$i.'</div>
				  <div class="col-md-4">'.$row['seat_category'].'</div>
				  <div class="col-md-3"><input type="text" class="form-control number_price_only" name="event_category_price[]" placeholder="Enter price" maxlength="4"></div>
				  <div class="col-md-2"></div>
				  <div class="col-md-2"></div>
				  </div>';
				  
			$i++;}
			$cat_list .= '<div class="row" style="margin-top:10px">
				  <div class="col-md-3" style="margin-top:6px;">Select available date</div>
				  <div class="col-md-3"><input type="text" class="form-control m_date" name="stock_available_date" placeholder="Available" ></div>
				  <div class="col-md-3" style="margin-top:6px;">Select expiry date</div>
				  <div class="col-md-3"><input type="text" class="form-control m_date" name="stock_expiry_date" placeholder="Expiry"></div>
				  </div>';
		}
		echo $cat_list;
		
	}
	
	
	// Get the auditorium seat map for this event
	public function getAuditoriumSeatMapEventById($request, $response, $args){
		$auditorium_id = $args['id'];
		$event_id = $args['event_id'];
		
        $validations = [
            v::intVal()->validate($auditorium_id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		
		$validationsE = [
            v::intVal()->validate($event_id)
        ];

        if ($this->validate($validationsE) === false) {
            return $response->withStatus(400);
        }
		
		
		$seats_map = Models\EventSeatCategories::where('event_id', '=', $event_id)
		                                             ->where('auditorium_id', '=', $auditorium_id)
													 ->where('status', '=', 1)
													 ->orderBy('id', 'DESC')
													 ->get();
		$cat_list = '';
		// Check if empty
		if( $seats_map->isEmpty() ){
			$seats_map = Models\AuditoriumSeatCategory::where('auditorium_id','=', $auditorium_id)->get();
			if( $seats_map->isEmpty() ){
				$cat_list .= '<div class="row" style="padding-left:120px; color:red">No data found</div>';
			}else{
			$i=1;
			foreach($seats_map as $row){
			  	$cat_list .= '<div class="row" style="margin-top:10px">
				  <div class="col-md-1">'.$i.'</div>
				  <div class="col-md-4">'.$row['seat_category'].'</div>
				  <div class="col-md-3"><input type="text" class="form-control number_price_only" name="event_category_price[]" placeholder="Enter price" maxlength="4"></div>
				  <div class="col-md-2"></div>
				  <div class="col-md-2"></div>
				  </div>';
				  
			$i++;}
			$cat_list .= '<div class="row" style="margin-top:10px">
				  <div class="col-md-3" style="margin-top:6px;">Select available date</div>
				  <div class="col-md-3"><input type="text" class="form-control m_date" name="stock_available_date" placeholder="Available" ></div>
				  <div class="col-md-3" style="margin-top:6px;">Select expiry date</div>
				  <div class="col-md-3"><input type="text" class="form-control m_date" name="stock_expiry_date" placeholder="Expiry"></div>
				  </div>';
			}
		}else{
			$i=1;
			foreach($seats_map as $row){
			  	$cat_list .= '<div class="row" style="margin-top:10px">
				  <div class="col-md-1">'.$i.'</div>
				  <div class="col-md-4">'.$row['seat_category'].'</div>
				  <div class="col-md-3"><input type="text" class="form-control number_price_only" name="event_category_price[]" placeholder="Enter price" maxlength="4"></div>
				  <div class="col-md-2"></div>
				  <div class="col-md-2"></div>
				  <input type="hidden" name="row_id[]" value="'.$row['id'].'">
				  </div>';
				  
			$i++;}
			$cat_list .= '<div class="row" style="margin-top:10px">
				  <div class="col-md-3" style="margin-top:6px;">Select available date</div>
				  <div class="col-md-3"><input type="text" class="form-control m_date" name="stock_available_date" placeholder="Available" ></div>
				  <div class="col-md-3" style="margin-top:6px;">Select expiry date</div>
				  <div class="col-md-3"><input type="text" class="form-control m_date" name="stock_expiry_date" placeholder="Expiry"></div>
				  </div>';
		}
		echo $cat_list;											 
											 
		
	}
	
	
	
	
	
}
