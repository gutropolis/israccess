<?php
namespace App\Controllers;

use App\Models\User;
use App\Models;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v;

/*
* Admin City Controller
* CRUD for City
  Available Functions
  1. cities
  2. getStates
  3. ajaxCityList
  4. getCityById
  5. saveCity
  6. updateCity
  7. deleteCityById
  
  
  
*/
class AdminCityController extends Base 
{
	protected $container;
	protected $lang;
	// Class Constructor
	public function __construct($container)
	{
		$this->container = $container;
		$this->lang =  $this->container->view['adminLang'];
	}
	// Main function for displaying list of cities in admin
	public function cities($request, $response) {
		$cities_list = Models\City::where('status', '=', 1)->get();
		$state_list = '';
		$yesSate = false;
		if( !empty($cities_list) ){
		foreach($cities_list as $row){
			if($row['state'] != ''){
		      $states_array[] = $row['state'];	
			}
		}
		foreach($cities_list as $row){
			if($row['state'] != ''){
				$yesSate = true;
		      $cities_array[] = $row['state'];	
			}
		}
		  if($yesSate){
		    $state_list = "'".implode("','", $cities_array)."'";
		  }
		}
		// Prepare an array
		$data = array('data' => $states_array);
        $params = array( 'title' => $this->lang['city_all_txt'],
		                  'city' => $cities_list,
						  'state_list' => $state_list,
						  'test' => $data,
						  'current_url' => $request->getUri()->getPath() );
        return $this->render($response, ADMIN_VIEW.'/City/cities.twig',$params);
    }
	// Get states list
	public function getStates($request, $response){
		$search_keyword = $request->getParam('search_keyword');
		$mode = $request->getParam('mode');
		$result = '';
		if($search_keyword){
			$cities_list = Models\City::where('state', 'LIKE', '%'.$search_keyword.'%')->groupBy('state')->get();
		     ?>
		     <ul id="country-list">
             <?php 
			foreach($cities_list as $row) {
				$state_name = trim($row["state"]);
				if($mode == 'add'){
				?>
			   <li onclick="selectCountry('<?php echo $state_name; ?>')"><?php echo $state_name;?></li>
			 <?php } else{ ?>
              <li onclick="selectCountryE('<?php echo $state_name; ?>')"><?php echo $state_name;?></li>
             <?php } } ?>
			</ul>
	      <?php }
		
	}
	
	// Ajax Category list
	public function ajaxCityList($request, $response){
		$isSearched = $drpSearch = false; // set to false if user did not search something
		$conditions = array();
		$whereData =  $customSearch = $prefix = '';
		if($request->getParam('query') != null){
			 $isSearched = true; // user has searched something
			 $fields = array('name', 'zipcode', 'status');
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
		if($request->getParam('sort') != null){
			  $sort = $request->getParam('sort')['sort'];	
			  $field = $request->getParam('sort')['field'];	
		}else{
			 $sort = 'desc';	
			 $field = 'id';	
			
		}
		
		$page     = $request->getParam('pagination')['page'];
		if( !empty($request->getParam('pagination')['pages']) ){
		  $pages    = $request->getParam('pagination')['pages'];
		}
		$per_page = $request->getParam('pagination')['perpage'];
		
		if($isSearched){
		    $total   = Models\City::whereRaw($whereData)->count(); // get count 
		}else{
			$total   = Models\City::get()->count(); // get count 
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
		    $cities_list = Models\City::whereRaw($whereData)->skip($offset*$perPageLimit)->take($perPageLimit)->orderBy($field, $sort)->get();
		}else{
			$cities_list = Models\City::skip($offset*$perPageLimit)->take($perPageLimit)->orderBy($field, $sort)->get();
		}
		
		$data = array();
		foreach($cities_list as $get){
		  	$array_data = array();
			$array_data['id']  = $get->id;
            $array_data['name']  = $get->name;
            //$array_data['state']  = $get->state;
			$array_data['zipcode']  = $get->zipcode;
			$array_data['status']  = $get->status;
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
	
	// Get City by id
	public function getCityById($request, $response, $args){
		$id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		$city = Models\City::find($id);
		if ($city) {
			return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode($city));
        }
		
	}
	
	// Save city from Admin
	public function saveCity($request, $response){
	   $isError = false;
	  // $state_name = $request->getParam('state_name');
	   $city_name = $request->getParam('city_name');
	   $zip_code = $request->getParam('zip_code');
	   $status = $request->getParam('status');
	   $cityExist = Models\City::where('name', '=', $city_name)->first();
	   if( $cityExist){
		  
		 echo json_encode(array("status" => 'duplicate', 
		                  'message' => $this->lang['city_txt'].' (<strong>'.$city_name. '</strong>) '.$this->lang['common_already_exist_txt'].'.'));
		 exit();	   
	   }else  if( empty($city_name) ){
		  
		 echo json_encode(array("status" => 'error', 
		                  'message' => $this->lang['city_msg_txt']));
		 exit();	   
	   } else if(empty($zip_code)){
		   
		 echo json_encode(array("status" => 'error', 
		                  'message' => $this->lang['city_zip_msg_txt']));
		 exit();	   
	   } else{
		   // Save to cities table
		   $city = new Models\City;
		   $city->name = $city_name;
		   $city->status = $status;
		   $city->country_id = 1;
		   //$city->state = $state_name;
		   $city->zipcode = $zip_code;
		   $city->save();
		   
		   return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode(array('status' => TRUE)));  
	   }
	     
	}
	
	// Update City from Admin
	public function updateCity($request, $response){
		
	   $id   = $request->getParam('id');
	  // $state_name = $request->getParam('state_name');
	   $city_name = $request->getParam('city_name');
	   $zip_code = $request->getParam('zip_code');
	   $status = $request->getParam('status');
	   $cityExist = Models\City::where('name', '=', $city_name)->where('id', '!=', $id)->first();
	   if( $cityExist){
		  
		 echo json_encode(array("status" => 'duplicate', 
		                  'message' => $this->lang['city_txt'].' (<strong>'.$city_name. '</strong>) '.$this->lang['common_already_exist_txt'].'.'));
		 exit();	   
	   }else  if( empty($city_name) ){
		  
		 echo json_encode(array("status" => 'error', 
		                  'message' => $this->lang['city_msg_txt']));
		 exit();	   
	   } else if(empty($zip_code)){
		   
		 echo json_encode(array("status" => 'error', 
		                  'message' => $this->lang['city_zip_msg_txt']));
		 exit();	   
	   } else{
		   
		   // Save to category table
		   $data = array('name' => $city_name,
						 'zipcode' => $zip_code,
						 'status' => $status);
		  $city = Models\City::where('id', '=', $id)->update($data);					 
		  return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode(array('status' => TRUE)));
	   }
	}
	
	// Delete City
	public function deleteCityById($request, $response, $args){
		 $id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		$delete = Models\City::find($id)->delete();
		return $response->withJson(json_encode(array("status" => TRUE)));
		
	}
	
	
	
	
	
	
	
	
	
}
