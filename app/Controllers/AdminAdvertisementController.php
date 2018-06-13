<?php
namespace App\Controllers;

use App\Models\User;
use App\Models;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v;
/*
*   Admin Advertisement Controller 
*   CRUD of Advertisement
    Availble Functions
	1. advertisements
	2. ajaxAdsList
	3. getAdById
	4. saveAd
	5. updateAd
	6. deleteAdById
	
*/
class AdminAdvertisementController extends Base 
{
	
	protected $container;
	protected $lang;
	// Class Constructor
	public function __construct($container)
	{
		$this->container = $container;
		$this->lang =  $this->container->view['adminLang'];
	}
	
	/*
	* Main function to display list of advertisments
	*/
	public function advertisements($request, $response) {
		
        $params = array( 'title' => 'Advertisements',
		                 'current_url' => $request->getUri()->getPath());
        return $this->render($response, ADMIN_VIEW.'/Advertisement/advertisements.twig',$params);
    }
	
	// Ajax ads list
	public function ajaxAdsList($request, $response){
		
		$isSearched = $drpSearch = false; // set to false if user did not search something
		$conditions = array();
		$whereData =  $customSearch = $prefix = '';
		if($request->getParam('query') != null){
			 $isSearched = true; // user has searched something
			 $fields = array('title',  'redirect_link');
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
		    $total   = Models\Advertisement::whereRaw($whereData)->count(); // get count 
		}else{
			$total   = Models\Advertisement::get()->count(); // get count 
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
		    $ads_list = Models\Advertisement::whereRaw($whereData)->skip($offset*$perPageLimit)->take($perPageLimit)->orderBy($field, $sort)->get();
		}else{
			$ads_list = Models\Advertisement::skip($offset*$perPageLimit)->take($perPageLimit)->orderBy($field, $sort)->get();
		}
		
		$data = array();
		foreach($ads_list as $get){
			// Check if a picture is not uploaded show default picture
			if($get->ad_picture <> '' && file_exists(ADS_ROOT_PATH.'/'.$get->ad_picture)){
				$catPicture = ADS_WEB_PATH.'/'.$get->ad_picture;
			}else{
			  	$catPicture = DEFAULT_IMG;
			}
			
		  	$array_data = array();
			$array_data['id']  = $get->id;
            $array_data['title']  = $get->title;
            $array_data['add_picture']  = $catPicture;
			$array_data['redirect_link']  = empty($get->redirect_link) ? '#' :  '<a href="'.$get->redirect_link.'" target="_blank">'.$get->redirect_link.'</a>';
			//$array_data['display_order']  = $get->display_order;
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
	
	// Get Ad by id
	public function getAdById($request, $response, $args){
		$id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		$category = Models\Advertisement::find($id);
		if ($category) {
            $category['file_web_path'] = ADS_WEB_PATH;
			return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode($category));
        }
		
	}
	
	/*
	* SAve Advertisement
	*/
	public function saveAd($request, $response){
	   $title = $request->getParam('title');
	   $redirect_link = $request->getParam('redirect_link');
	   $status = $request->getParam('status');
	   $titleExist = Models\Advertisement::where('title', '=', $title)->first();
	   if( empty($title) ){
		  
		 echo json_encode(array("status" => 'error', 
		                  'message' => 'Please enter advertisement title'));
		 exit();	   
	   }else if($titleExist){
		 echo json_encode(array("status" => 'duplicate', 
		                  'message' => 'Advertisement with (<strong>'.$title. '</strong>) already exists'));
		 exit();	   
	   }else{
		   if(isset($_FILES) && $_FILES['ad_picture']['name'] <> ''){
			   $allowedExtensions = allowedExtensions();
			   $file = $_FILES['ad_picture']['tmp_name'];
			   $file_name = explode('.',$_FILES['ad_picture']['name']);
			   $file_extension = end($file_name);
			   if(!in_array($file_extension, $allowedExtensions)){
				   echo json_encode(array("status" => 'file_error', 
					               'message' => $this->lang['common_file_with_type_txt'].' [<strong>'.$file_extension. '</strong>] '.$this->lang['common_is_not_allowed_txt']));	
				   exit();
			   }else{
				    $file_new_name = md5(uniqid());
				    $filename = $file_new_name.'.'.$file_extension;
				   if(!empty($file)){
					  move_uploaded_file($file, ADS_ROOT_PATH.'/'.$filename);
					  $slider_picture = $filename;
					 }else{
					  $slider_picture = '';	
				   }

			   }
			   
		   }else{
			   $slider_picture = '';
		   }
		   // Save to sliders table
		   $slider = new Models\Advertisement;
		   $slider->title = $title;
		   $slider->ad_picture = $slider_picture;
		   $slider->redirect_link = $redirect_link;
		   $slider->created_on =  date('Y-m-d H:i:s');
		   $slider->status = $status;
		   $slider->save();
		  return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode(array('status' => TRUE)));
	   }
	   
	   
	   
	}
	
	/* 
	* Update Advertisement
	*/
	public function updateAd($request, $response){
		
	   $id   = $request->getParam('id');
	   $title = $request->getParam('title');
	   $redirect_link = $request->getParam('redirect_link');
	   $status = $request->getParam('status');
	   $ad_picture_old = $request->getParam('ad_picture_old');
	   $status = $request->getParam('status');
	   $adExist = Models\Advertisement::where('title', '=', $title)->where('id', '!=', $id)->first();
	   if( empty($title) ){
		  
		 echo json_encode(array("status" => 'error', 
		                  'message' => 'Please enter advertisement title'));
		 exit();	   
	   }else if($adExist){
		 echo json_encode(array("status" => 'duplicate', 
		                  'message' => 'Advertisement with (<strong>'.$title. '</strong>) already exist.'));
		 exit();	   
	   }else{
		   if(isset($_FILES) && $_FILES['ad_picture']['name'] <> ''){
			   $allowedExtensions = allowedExtensions();
			   $file = $_FILES['ad_picture']['tmp_name'];
			   $file_name = explode('.',$_FILES['ad_picture']['name']);
			   $file_extension = end($file_name);
			   if(!in_array($file_extension, $allowedExtensions)){
				  echo json_encode(array("status" => 'file_error', 
					               'message' => $this->lang['common_file_with_type_txt'].' [<strong>'.$file_extension. '</strong>] '.$this->lang['common_is_not_allowed_txt']));		
				   exit();
			   }else{
				    $file_new_name = md5(uniqid());
				    $filename = $file_new_name.'.'.$file_extension;
				   if(!empty($file)){
					  move_uploaded_file($file, ADS_ROOT_PATH.'/'.$filename);
					  $slider_picture = $filename;
					 }else{
					  $slider_picture = '';	
				   }

			   }
			   
		   }else{
			   $slider_picture = '';
		   }
		   if($_FILES['ad_picture']['tmp_name'] <> ''){
			   // Delete old images
			   if($ad_picture_old <> ''){
				 @unlink(ADS_ROOT_PATH.'/'.$ad_picture_old);
			   }
		   }else{
			    $slider_picture = $ad_picture_old; 
		   }
		   
		   // Save to slider table
		   $data = array('title' => $title,
		                 'ad_picture' => $slider_picture,
						 'redirect_link' => $redirect_link,
						 'status' => $status);
		  $category = Models\Advertisement::where('id', '=', $id)->update($data);					 
		  return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode(array('status' => TRUE)));
	   }
	}
	
	/* 
	* Delete ad
	*/
	public function deleteAdById($request, $response, $args){
		 $id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		// Check if this slider has a picture uploaded.
		$pictureExist = Models\Advertisement::where('id', '=', $id)->first()->ad_picture;
	   if($pictureExist){
		   // Unlink the picture
		   @unlink(ADS_ROOT_PATH.'/'.$pictureExist);
	   }
		$delete = Models\Advertisement::find($id)->delete();
		return $response->withJson(json_encode(array("status" => TRUE)));
		
	}
	
	
	
	
	
	
	
	
	
}
