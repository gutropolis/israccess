<?php
namespace App\Controllers;

use App\Models\User;
use App\Models;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v;

/*
*  Admin Community Controller
*  CRUDs for Community
   Available Functions
   1. communities
   2. ajaxCommunitiesList
   3. getCommunityById
   4. saveCommunity
   5. updateCommunity
   6. deleteCommunityById
*/
class AdminCommunityController extends Base 
{
	protected $container;
	// Class constructor
	public function __construct($container)
	{
		$this->container = $container;
	}
	// Main function to display commmunities list in admin
	public function communities($request, $response) {
        $params = array( 'title' => 'Community in numbers',
		                 'current_url' => $request->getUri()->getPath());
        return $this->render($response, ADMIN_VIEW.'/Community/communities.twig',$params);
    }
	
	
	// Ajax Communities list
	public function ajaxCommunitiesList($request, $response){
		$isSearched = $drpSearch = false; // set to false if user did not search something
		$conditions = array();
		$whereData =  $customSearch = $prefix = '';
		if($request->getParam('query') != null){
			 $isSearched = true; // user has searched something
			 $fields = array('title', 'numbers');
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
		    $total   = Models\Community::whereRaw($whereData)->count(); // get count 
		}else{
			$total   = Models\Community::get()->count(); // get count 
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
		    $communities_list = Models\Community::whereRaw($whereData)->skip($offset*$perPageLimit)->take($perPageLimit)->orderBy($field, $sort)->get();
		}else{
			$communities_list = Models\Community::skip($offset*$perPageLimit)->take($perPageLimit)->orderBy($field, $sort)->get();
		}
		
		$data = array();
		foreach($communities_list as $get){
		  	$array_data = array();
			$array_data['id']  = $get->id;
            $array_data['title']  = $get->title;
            $array_data['numbers']  = $get->numbers;
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
	
	// Get Community by id
	public function getCommunityById($request, $response, $args){
		$id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		$city = Models\Community::find($id);
		if ($city) {
			return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode($city));
        }
		
	}
	// Save community from Admin
	public function saveCommunity($request, $response){
		//ddump($_REQUEST);
	   $isError = false;
	   $title = $request->getParam('title');
	   $numbers = $request->getParam('numbers');
	   $status = $request->getParam('status');
	   $communityExist = Models\Community::where('title', '=', $title)->first();
	   if( $communityExist){
		  
		 echo json_encode(array("status" => 'duplicate', 
		                  'message' => 'Community (<strong>'.$title. '</strong>) already exist.'));
		 exit();	   
	   }else  if( empty($numbers) ){
		  
		 echo json_encode(array("status" => 'error', 
		                  'message' => 'Please enter community member number'));
		 exit();	   
	   }else{
		   // Save to communties table
		   $community = new Models\Community;
		   $community->title = $title;
		   $community->numbers = $numbers;
		   $community->status = $status;
		   $community->save();
		   
		   return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode(array('status' => TRUE)));  
	   }
	   
	   
	   
	   
	}
	
	// Update Community from Admin
	public function updateCommunity($request, $response){
		
	   $id   = $request->getParam('id');
	   $title = $request->getParam('title');
	   $numbers = $request->getParam('numbers');
	   $status = $request->getParam('status');
	   $communityExist = Models\Community::where('title', '=', $title)->where('id', '!=', $id)->first();
	   if( $communityExist){
		  
		 echo json_encode(array("status" => 'duplicate', 
		                  'message' => 'Community (<strong>'.$title. '</strong>) already exist.'));
		 exit();	   
	   }else  if( empty($numbers) ){
		  
		 echo json_encode(array("status" => 'error', 
		                  'message' => 'Please enter community member number'));
		 exit();	   
	   }else{
		   
		   // Save to communities table
		   $data = array('title' => $title,
		                 'numbers' => $numbers,
						 'status' => $status);
		  $city = Models\Community::where('id', '=', $id)->update($data);					 
		  return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode(array('status' => TRUE)));
	   }
	}
	
	// Delete Community From Admin
	public function deleteCommunityById($request, $response, $args){
		 $id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		$delete = Models\Community::find($id)->delete();
		return $response->withJson(json_encode(array("status" => TRUE)));
		
	}
	
	
	
	
	
	
	
	
	
}
