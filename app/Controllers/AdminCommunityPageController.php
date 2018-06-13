<?php
namespace App\Controllers;

use App\Models\User;
use App\Models;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v;

/*
*  Admin Community Page Controller
*  CRUDs for community page 

// Available Functions
* 1. community_page
* 2. ajaxCommunityPageList
* 3. getCommunityPageById
* 4. add
* 5. saveCommunityPage
* 6. updateCommunityPage
* 7. deleteCommunityPageById

*/
class AdminCommunityPageController extends Base 
{
	protected $container;
	// Class constructor
	public function __construct($container)
	{
		$this->container = $container;
	}
	// Main function to display comm=munity page lists
	public function community_page($request, $response) {
        $params = array( 'title' => 'Community Page',
		                 'current_url' => $request->getUri()->getPath());
        return $this->render($response, ADMIN_VIEW.'/CommunityPage/community_page.twig',$params);
    }
	
	
	// Ajax Communities list
	public function ajaxCommunityPageList($request, $response){
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
		    $total   = Models\CommunityPage::whereRaw($whereData)->count(); // get count 
		}else{
			$total   = Models\CommunityPage::get()->count(); // get count 
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
		    $communities_list = Models\CommunityPage::whereRaw($whereData)->skip($offset*$perPageLimit)->take($perPageLimit)->orderBy($field, $sort)->get();
		}else{
			$communities_list = Models\CommunityPage::skip($offset*$perPageLimit)->take($perPageLimit)->orderBy($field, $sort)->get();
		}
		
		$data = array();
		foreach($communities_list as $get){
		  	$array_data = array();
			$array_data['id']  = $get->id;
            $array_data['title']  = strip_tags(htmlspecialchars_decode($get->title));
            $array_data['display_order']  = $get->display_order;
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
	
	// Get Community Page by id
	public function getCommunityPageById($request, $response, $args){
		$id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		$page_data = Models\CommunityPage::find($id);
		$url = explode('/', $request->getUri()->getPath());
		$current_url = $url[0].'/'.$url[1].'/'.$url[2];
		$params = array( 'title' => 'Edit Community Page',
		                'data' => $page_data,
						'current_url' => $current_url);
						
        return $this->render($response, ADMIN_VIEW.'/CommunityPage/edit.twig',$params);
		
	}
	
	// add function
	public function add($request, $response) {
		
        $params = array( 'title' => 'Add Community Page',
		                 'current_url' => $request->getUri()->getPath());
        return $this->render($response, ADMIN_VIEW.'/CommunityPage/add.twig',$params);
    }
	// Sav to community page
	public function saveCommunityPage($request, $response){
	   $isError = false;
	   $title = $request->getParam('title');
	   $short_description = $request->getParam('short_description');
	   $full_description = $request->getParam('full_description');
	   $display_order = $request->getParam('display_order');
	   $status = $request->getParam('status');
	   $communityExist = Models\CommunityPage::where('title', '=', $title)->first();
	   if( $communityExist){
		  
		 echo json_encode(array("status" => 'duplicate', 
		                  'message' => 'Community page(<strong>'.$title. '</strong>) already exist.'));
		 exit();	   
	   }else  if( empty($title) ){
		  
		 echo json_encode(array("status" => 'error', 
		                  'message' => 'Please enter community page title'));
		 exit();	   
	   }else  if( empty($short_description) ){
		  
		 echo json_encode(array("status" => 'error', 
		                  'message' => 'Please enter community page short description'));
		 exit();	   
	   }else  if( empty($full_description) ){
		  
		 echo json_encode(array("status" => 'error', 
		                  'message' => 'Please enter community page full description'));
		 exit();	   
	   }else{
		   // Save to communty page table
		   $community = new Models\CommunityPage;
		   $community->title = ($title);
		   $community->short_description = ($short_description);
		   $community->full_description = ($full_description);
		   $community->display_order = $display_order;
		   $community->status = $status;
		   $community->save();
		   
		   return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode(array('status' => TRUE)));  
	   }
	   
	   
	   
	   
	}
	
	// Update Community Page
	public function updateCommunityPage($request, $response){
		
	   $id   = $request->getParam('id');
	   $title = $request->getParam('title');
	   $short_description = $request->getParam('short_description');
	   $full_description = $request->getParam('full_description');
	   $display_order = $request->getParam('display_order');
	   $status = $request->getParam('status');
	   $communityExist = Models\CommunityPage::where('title', '=', $title)->where('id', '!=', $id)->first();
	   if( $communityExist){
		  
		 echo json_encode(array("status" => 'duplicate', 
		                  'message' => 'Community page (<strong>'.$title. '</strong>) already exist.'));
		 exit();	   
	   }else  if( empty($title) ){
		  
		 echo json_encode(array("status" => 'error', 
		                  'message' => 'Please enter community page title'));
		 exit();	   
	   }else  if( empty($short_description) ){
		  
		 echo json_encode(array("status" => 'error', 
		                  'message' => 'Please enter community page short description'));
		 exit();	   
	   }else  if( empty($full_description) ){
		  
		 echo json_encode(array("status" => 'error', 
		                  'message' => 'Please enter community page full description'));
		 exit();	   
	   }else{
		   
		   // Save to community page table
		   $data = array('title' => ($title),
		                 'short_description' => ($short_description),
						 'full_description' => ($full_description),
						 'display_order' => $display_order,
						 'status' => $status);
		  $city = Models\CommunityPage::where('id', '=', $id)->update($data);					 
		  return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode(array('status' => TRUE)));
	   }
	}
	
	// Delete Community Page
	public function deleteCommunityPageById($request, $response, $args){
		 $id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		$delete = Models\CommunityPage::find($id)->delete();
		return $response->withJson(json_encode(array("status" => TRUE)));
		
	}
	
	
	
	
	
	
	
	
	
}
