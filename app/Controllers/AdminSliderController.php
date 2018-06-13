<?php
namespace App\Controllers;

use App\Models\User;
use App\Models;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v;

/**
  Admin Slider Controller
  Available Functions.
  1. sliders
  2. ajaxSlidersList
  3. getSliderById
  4. saveSlider
  5. updateSlider
  6. deleteSliderById
  
*/
class AdminSliderController extends Base 
{
	
	protected $container;
	protected $lang;
	// Class constructor
	public function __construct($container)
	{
		$this->container = $container;
		$this->lang =  $this->container->view['adminLang'];
	}
	
	// Main function to display sliders list
	public function sliders($request, $response) {
        $params = array( 'title' => $this->lang['slider_all_txt'],
		                 'current_url' => $request->getUri()->getPath());
        return $this->render($response, ADMIN_VIEW.'/Slider/sliders.twig',$params);
    }
	
	// Ajax Sliders list
	public function ajaxSlidersList($request, $response){
		
		$isSearched = $drpSearch = false; // set to false if user did not search something
		$conditions = array();
		$whereData =  $customSearch = $prefix = '';
		if($request->getParam('query') != null){
			 $isSearched = true; // user has searched something
			 $fields = array('picture_caption', 'display_order', 'redirect_link');
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
		    $total   = Models\Slider::whereRaw($whereData)->count(); // get count 
		}else{
			$total   = Models\Slider::get()->count(); // get count 
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
		    $sliders_list = Models\Slider::whereRaw($whereData)->skip($offset*$perPageLimit)->take($perPageLimit)->orderBy($field, $sort)->get();
		}else{
			$sliders_list = Models\Slider::skip($offset*$perPageLimit)->take($perPageLimit)->orderBy($field, $sort)->get();
		}
		
		$data = array();
		foreach($sliders_list as $get){
			// Check if a picture is not uploaded show default picture
			if($get->slider_picture <> '' && file_exists(SLIDER_ROOT_PATH.'/'.$get->slider_picture)){
				$catPicture = SLIDER_WEB_PATH.'/'.$get->slider_picture;
			}else{
			  	$catPicture = DEFAULT_IMG;
			}
			
		  	$array_data = array();
			$array_data['id']  = $get->id;
            $array_data['picture_caption']  = $get->picture_caption;
            $array_data['slider_picture']  = $catPicture;
			$array_data['redirect_link']  = empty($get->redirect_link) ? '#' :  '<a href="'.$get->redirect_link.'" target="_blank">'.$get->redirect_link.'</a>';
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
	
	// Get Slider by id
	public function getSliderById($request, $response, $args){
		$id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		$category = Models\Slider::find($id);
		if ($category) {
            $category['file_web_path'] = SLIDER_WEB_PATH;
			return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode($category));
        }
		
	}
	
	// Save slider
	public function saveSlider($request, $response){
	   $picture_caption = $request->getParam('picture_caption');
	   $redirect_link = $request->getParam('redirect_link');
	   $display_order = $request->getParam('display_order');
	   $status = $request->getParam('status');
	   $categoryExist = Models\Slider::where('picture_caption', '=', $picture_caption)->first();
	   if( empty($picture_caption) ){
		  
		 echo json_encode(array("status" => 'error', 
		                  'message' => $this->lang['slider_caption_msg_txt']));
		 exit();	   
	   }else if($categoryExist){
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
					  move_uploaded_file($file, SLIDER_ROOT_PATH.'/'.$filename);
					  $slider_picture = $filename;
					 }else{
					  $slider_picture = '';	
				   }

			   }
			   
		   }else{
			   $slider_picture = '';
		   }
		   // Save to sliders table
		   $slider = new Models\Slider;
		   $slider->picture_caption = $picture_caption;
		   $slider->slider_picture = $slider_picture;
		   $slider->redirect_link = $redirect_link;
		   $slider->display_order =  $display_order;
		   $slider->status = $status;
		   $slider->save();
		  return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode(array('status' => TRUE)));
	   }
	   
	   
	   
	}
	
	// Update Slider
	public function updateSlider($request, $response){
		
	   $id   = $request->getParam('id');
	   $picture_caption = $request->getParam('picture_caption');
	   $redirect_link = $request->getParam('redirect_link');
	   $status = $request->getParam('status');
	   $slider_picture_old = $request->getParam('slider_picture_old');
	   $display_order = $request->getParam('display_order');
	   $status = $request->getParam('status');
	   $sliderExist = Models\Slider::where('picture_caption', '=', $picture_caption)->where('id', '!=', $id)->first();
	   if( empty($picture_caption) ){
		  
		 echo json_encode(array("status" => 'error', 
		                  'message' => $this->lang['slider_caption_msg_txt']));
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
					  move_uploaded_file($file, SLIDER_ROOT_PATH.'/'.$filename);
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
				 @unlink(SLIDER_ROOT_PATH.'/'.$slider_picture_old);
			   }
		   }else{
			    $slider_picture = $slider_picture_old; 
		   }
		   
		   // Save to slider table
		   $data = array('picture_caption' => $picture_caption,
		                 'slider_picture' => $slider_picture,
						 'redirect_link' => $redirect_link,
						 'display_order' => $display_order,
						 'status' => $status);
		  $category = Models\Slider::where('id', '=', $id)->update($data);					 
		  return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode(array('status' => TRUE)));
	   }
	}
	
	// Delete Slider
	public function deleteSliderById($request, $response, $args){
		 $id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		// Check if this slider has a picture uploaded.
		$pictureExist = Models\Slider::where('id', '=', $id)->first()->slider_picture;
	   if($pictureExist){
		   // Unlink the picture
		   @unlink(SLIDER_ROOT_PATH.'/'.$pictureExist);
	   }
		$delete = Models\Slider::find($id)->delete();
		return $response->withJson(json_encode(array("status" => TRUE)));
		
	}
	
	
	
	
	
	
	
	
	
}
