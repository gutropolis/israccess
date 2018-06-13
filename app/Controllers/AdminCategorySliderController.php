<?php
namespace App\Controllers;

use App\Models\User;
use App\Models;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v;

/*
*  Admin Category Slider Controller
*  CRUD for Category Slider from Admin
   Available Functions
   1. category_page_sliders
   2. ajaxCategorySlidersList
   3. getCategorySliderById
   4. saveCategorySlider
   5. updateCategorySlider
   6. deleteCategorySliderById

*/

class AdminCategorySliderController extends Base 
{
	
	protected $container;
	protected $lang;
	// Class Constructor 
	public function __construct($container)
	{
		$this->container = $container;
		$this->lang =  $this->container->view['adminLang'];
	}
	
	// Main function  to display category page sliders
	public function category_page_sliders($request, $response) {
        $params = array( 'title' => 'Category Page Sliders',
		                 'current_url' => $request->getUri()->getPath());
        return $this->render($response, ADMIN_VIEW.'/CategoryPageSlider/category_page_slider.twig',$params);
    }
	
	// Ajax Category Sliders list
	public function ajaxCategorySlidersList($request, $response){
		
		$isSearched = $drpSearch = false; // set to false if user did not search something
		$conditions = array();
		$whereData =  $customSearch = $prefix = '';
		if($request->getParam('query') != null){
			 $isSearched = true; // user has searched something
			 $fields = array('status', 'redirect_link');
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
		    $total   = Models\CategoryPageSlider::whereRaw($whereData)->count(); // get count 
		}else{
			$total   = Models\CategoryPageSlider::get()->count(); // get count 
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
		    $sliders_list = Models\CategoryPageSlider::whereRaw($whereData)->skip($offset*$perPageLimit)->take($perPageLimit)->orderBy($field, $sort)->get();
		}else{
			$sliders_list = Models\CategoryPageSlider::skip($offset*$perPageLimit)->take($perPageLimit)->orderBy($field, $sort)->get();
		}
		
		$data = array();
		foreach($sliders_list as $get){
			// Check if a picture is not uploaded show default picture
			if($get->slider_picture <> '' && file_exists(CAT_PAGE_SLIDER_ROOT_PATH.'/'.$get->slider_picture)){
				$catPicture = CAT_PAGE_SLIDER_WEB_PATH.'/'.$get->slider_picture;
			}else{
			  	$catPicture = DEFAULT_IMG;
			}
			
		  	$array_data = array();
			$array_data['id']  = $get->id;
            $array_data['slider_picture']  = $catPicture;
			$array_data['redirect_link']  = empty($get->redirect_link) ? '#' :  '<a href="'.$get->redirect_link.'" target="_blank">'.$get->redirect_link.'</a>';
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
	
	// Get Category Slider by id
	public function getCategorySliderById($request, $response, $args){
		$id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		$category = Models\CategoryPageSlider::find($id);
		if ($category) {
            $category['file_web_path'] = CAT_PAGE_SLIDER_WEB_PATH;
			return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode($category));
        }
		
	}
	// Save category slider
	public function saveCategorySlider($request, $response){
	   $redirect_link = $request->getParam('redirect_link');
	   $status = $request->getParam('status');
	   $categoryExist = Models\CategoryPageSlider::where('redirect_link', '=', $redirect_link)->first();
	   if( empty($redirect_link) ){
		  
		 echo json_encode(array("status" => 'error', 
		                  'message' => 'Please enter redirect link'));
		 exit();	   
	   }else{
		   if(isset($_FILES) && $_FILES['slider_picture']['name'] <> ''){
			   $allowedExtensions = allowedExtensions();
			   $file = $_FILES['slider_picture']['tmp_name'];
			   $file_name = explode('.',$_FILES['slider_picture']['name']);
			   $file_extension = end($file_name);
			   if(!in_array($file_extension, $allowedExtensions)){
				   echo json_encode(array("status" => 'file_error', 
					               'message' => $this->lang['common_file_with_type_txt'].' [<strong>'.$file_extension. '</strong>] '.$this->lang['common_is_not_allowed_txt']));	
				   exit();
			   }else{
				    $file_new_name = md5(uniqid());
				    $filename = $file_new_name.'.'.$file_extension;
				   if(!empty($file)){
					  move_uploaded_file($file, CAT_PAGE_SLIDER_ROOT_PATH.'/'.$filename);
					  $slider_picture = $filename;
					 }else{
					  $slider_picture = '';	
				   }

			   }
			   
		   }else{
			   $slider_picture = '';
		   }
		   // Save to sliders table
		   $slider = new Models\CategoryPageSlider;
		   $slider->slider_picture = $slider_picture;
		   $slider->redirect_link = $redirect_link;
		   $slider->status = $status;
		   $slider->save();
		  return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode(array('status' => TRUE)));
	   }
	   
	   
	   
	}
	
	// Update Category Slider from admin
	public function updateCategorySlider($request, $response){
		
	   $id   = $request->getParam('id');
	   $redirect_link = $request->getParam('redirect_link');
	   $slider_picture_old = $request->getParam('slider_picture_old');
	   $status = $request->getParam('status');
	   $sliderExist = Models\CategoryPageSlider::where('redirect_link', '=', $redirect_link)->where('id', '!=', $id)->first();
	   if( empty($redirect_link) ){
		  
		 echo json_encode(array("status" => 'error', 
		                  'message' => 'Please enter redirect link'));
		 exit();	   
	   }else if($sliderExist){
		 echo json_encode(array("status" => 'duplicate', 
		                  'message' => $this->lang['slider_txt'].' (<strong>'.$picture_caption. '</strong>) '.$this->lang['common_already_exist_txt']));
		 exit();	   
	   }else{
		   if(isset($_FILES) && $_FILES['slider_picture']['name'] <> ''){
			   $allowedExtensions = allowedExtensions();
			   $file = $_FILES['slider_picture']['tmp_name'];
			   $file_name = explode('.',$_FILES['slider_picture']['name']);
			   $file_extension = end($file_name);
			   if(!in_array($file_extension, $allowedExtensions)){
				  echo json_encode(array("status" => 'file_error', 
					               'message' => $this->lang['common_file_with_type_txt'].' [<strong>'.$file_extension. '</strong>] '.$this->lang['common_is_not_allowed_txt']));		
				   exit();
			   }else{
				    $file_new_name = md5(uniqid());
				    $filename = $file_new_name.'.'.$file_extension;
				   if(!empty($file)){
					  move_uploaded_file($file, CAT_PAGE_SLIDER_ROOT_PATH.'/'.$filename);
					  $slider_picture = $filename;
					 }else{
					  $slider_picture = '';	
				   }

			   }
			   
		   }else{
			   $slider_picture = '';
		   }
		   if($_FILES['slider_picture']['tmp_name'] <> ''){
			   // Delete old images
			   if($slider_picture_old <> ''){
				 @unlink(CAT_PAGE_SLIDER_ROOT_PATH.'/'.$slider_picture_old);
			   }
		   }else{
			    $slider_picture = $slider_picture_old; 
		   }
		   
		   // Save to slider table
		   $data = array(
		                 'slider_picture' => $slider_picture,
						 'redirect_link' => $redirect_link,
						 'status' => $status);
		  $category = Models\CategoryPageSlider::where('id', '=', $id)->update($data);					 
		  return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode(array('status' => TRUE)));
	   }
	}
	
	// Delete Category Slider
	public function deleteCategorySliderById($request, $response, $args){
		 $id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		// Check if this slider has a picture uploaded.
		$pictureExist = Models\CategoryPageSlider::where('id', '=', $id)->first()->slider_picture;
	   if($pictureExist){
		   // Unlink the picture
		   @unlink(CAT_PAGE_SLIDER_ROOT_PATH.'/'.$pictureExist);
	   }
		$delete = Models\CategoryPageSlider::find($id)->delete();
		return $response->withJson(json_encode(array("status" => TRUE)));
		
	}
	
	
	
	
	
	
	
	
	
}
