<?php
namespace App\Controllers;

use App\Models;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v;

/*
* Admin Category Controller
* CRUD of Categories
  Available Functions
  1. categories
  2. ajaxCategoriesList
  3. getCategoryById
  4. saveCategory
  5. updateCategory
  6. deleteCategoryById
  7.
  
*/
class AdminCategoryController extends Base 
{
	protected $container;
	protected $lang;
	// Class Constructor
	public function __construct($container)
	{
		$this->container = $container;
		$this->lang =  $this->container->view['adminLang'];
	}
	
	
	// Main function to display list of categories in Admin
	public function categories($request, $response) {
        $params = array( 'title' => $this->lang['category_all_txt'],
		                 'current_url' => $request->getUri()->getPath());
        return $this->render($response, ADMIN_VIEW.'/Category/categories.twig',$params);
    }
	
	// Ajax Category list
	public function ajaxCategoriesList($request, $response){
		
		$isSearched = $drpSearch = false; // set to false if user did not search something
		$conditions = array();
		$whereData =  $customSearch = $prefix = '';
		if($request->getParam('query') != null){
			 $isSearched = true; // user has searched something
			 $fields = array('name', 'slug', 'is_for_home', 'status');
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
		    $total   = Models\Category::whereRaw($whereData)->count(); // get count 
		}else{
			$total   = Models\Category::get()->count(); // get count 
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
		    $categories_list = Models\Category::whereRaw($whereData)->skip($offset*$perPageLimit)->take($perPageLimit)->orderBy($field, $sort)->get();
		}else{
			$categories_list = Models\Category::skip($offset*$perPageLimit)->take($perPageLimit)->orderBy($field, $sort)->get();
		}
		
		$data = array();
		foreach($categories_list as $get){
			// Check if a picture is not uploaded show default picture
			if($get->picto_file <> '' && file_exists(CATEGORY_ROOT_PATH.'/'.$get->picto_file)){
				$catPicture = CATEGORY_WEB_PATH.'/'.$get->picto_file;
			}else{
			  	$catPicture = DEFAULT_IMG;
			}
			
		  	$array_data = array();
			$array_data['id']  = $get->id;
            $array_data['name']  = $get->name;
            $array_data['picto_file']  = $catPicture;
			$array_data['slug']  = $get->slug;
			$array_data['is_for_home']  = ($get->is_for_home == '1') ? 'Yes' : 'No';
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
	
	// Get Category by id
	public function getCategoryById($request, $response, $args){
		$id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		$category = Models\Category::find($id);
		if ($category) {
            $category['file_web_path'] = CATEGORY_WEB_PATH;
			$category['home_slider_title'] = htmlspecialchars_decode($category['home_slider_title']);
			return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode($category));
        }
		
	}
	
	/*
	*  Save Category from admin
	*/
	public function saveCategory($request, $response){
	   $categoryName = $request->getParam('category_name');
	   $slug = $request->getParam('slug');
	   $is_for_home = $request->getParam('is_for_home');
	   $home_slider_title = $request->getParam('home_slider_title');
	   $status = $request->getParam('status');
	   $categoryExist = Models\Category::where('name', '=', $categoryName)->first();
	   if( empty($categoryName) ){
		  echo json_encode(array("status" => 'error', 
		                  'message' => $this->lang['category_name_msg_txt']));
		 exit();	   
	   }else if($categoryExist){
		 echo json_encode(array("status" => 'duplicate', 
		                  'message' => $this->lang['category_txt'].' (<strong>'.$categoryName. '</strong>) '.$this->lang['common_already_exist_txt']));
		 exit();	   
	   }else{
		   if(isset($_FILES) && $_FILES['category_logo']['name'] <> ''){
			   $allowedExtensions = allowedExtensions();
			   $file = $_FILES['category_logo']['tmp_name'];
			   $file_name = explode('.',$_FILES['category_logo']['name']);
			   $file_extension = end($file_name);
			   if(!in_array($file_extension, $allowedExtensions)){
				  echo json_encode(array("status" => 'file_error', 
					               'message' => $this->lang['common_file_with_type_txt'].' [<strong>'.$file_extension. '</strong>] '.$this->lang['common_is_not_allowed_txt']));
				   exit();
			   }else{
				    $file_new_name = md5(uniqid());
				    $filename = $file_new_name.'.'.$file_extension;
				   if(!empty($file)){
					  move_uploaded_file($file, CATEGORY_ROOT_PATH.'/'.$filename);
					  $categoryPicture = $filename;
					 }else{
					  $categoryPicture = '';	
				   }

			   }
			   
		   }else{
			   $categoryPicture = '';
		   }
		   $slug = empty($slug) ?  str_replace(' ', '_', $categoryName) :  str_replace(' ', '_', $slug);
		   $for_home = !isset($is_for_home) ? '0' : '1';
		   // Save to category table
		   $cat = new Models\Category;
		   $cat->name = $categoryName;
		   $cat->slug = strtolower($slug);
		   $cat->picto_file = $categoryPicture;
		   $cat->status = $status;
		   $cat->is_for_home = $for_home;
		   $cat->home_slider_title = $home_slider_title;
		   $cat->save();
		  return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode(array('status' => TRUE)));
	   }
	   
	   
	   
	}
	
	// Update Category from admin
	public function updateCategory($request, $response){
		
	   $id   = $request->getParam('id');
	   $categoryName = $request->getParam('category_name');
	   $is_for_home = $request->getParam('is_for_home');
	   $home_slider_title = $request->getParam('home_slider_title');
	   $slug = $request->getParam('slug');
	   $categoryPictureOld = $request->getParam('category_logo_old');
	   $status = $request->getParam('status');
	   $categoryExist = Models\Category::where('name', '=', $categoryName)->where('id', '!=', $id)->first();
	   if( empty($categoryName) ){
		  echo json_encode(array("status" => 'error', 
		                  'message' => $this->lang['category_name_msg_txt']));
		 exit();	   
	   }else if($categoryExist){
		 echo json_encode(array("status" => 'duplicate', 
		                  'message' => $this->lang['category_txt'].' (<strong>'.$categoryName. '</strong>) '.$this->lang['common_already_exist_txt']));
		 exit();	   
	   }else{
		   if(isset($_FILES) && $_FILES['category_logo']['name'] <> ''){
			   $allowedExtensions = allowedExtensions();
			   $file = $_FILES['category_logo']['tmp_name'];
			   $file_name = explode('.',$_FILES['category_logo']['name']);
			   $file_extension = end($file_name);
			   if(!in_array($file_extension, $allowedExtensions)){
				   echo json_encode(array("status" => 'file_error', 
					               'message' => $this->lang['common_file_with_type_txt'].' [<strong>'.$file_extension. '</strong>] '.$this->lang['common_is_not_allowed_txt']));	
				   exit();
			   }else{
				    $file_new_name = md5(uniqid());
				    $filename = $file_new_name.'.'.$file_extension;
				   if(!empty($file)){
					  move_uploaded_file($file, CATEGORY_ROOT_PATH.'/'.$filename);
					  $categoryPicture = $filename;
					 }else{
					  $categoryPicture = '';	
				   }

			   }
			   
		   }else{
			   $categoryPicture = '';
		   }
		   if($_FILES['category_logo']['tmp_name'] <> ''){
			   // Delete old images
			   if($categoryPictureOld <> ''){
				 @unlink(CATEGORY_ROOT_PATH.'/'.$categoryPictureOld);
			   }
		   }else{
			    $categoryPicture = $categoryPictureOld; 
		   }
		   
		   // Save to category table
		   $slug = empty($slug) ?  str_replace(' ', '_', $categoryName) :  str_replace(' ', '_', $slug);
		   $for_home = !isset($is_for_home) ? '0' : '1';
		   $data = array('name' => $categoryName,
		                 'picto_file' => $categoryPicture,
						 'slug' => strtolower($slug),
						 'is_for_home' => $for_home, 
						 'home_slider_title' => htmlspecialchars($home_slider_title),
						 'status' => $status);
		  $category = Models\Category::where('id', '=', $id)->update($data);					 
		  return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode(array('status' => TRUE)));
	   }
	}
	
	// Delete Category
	public function deleteCategoryById($request, $response, $args){
		 $id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		// Check if this category has a picture uploaded.
		$pictureExist = Models\Category::where('id', '=', $id)->first()->picto_file;
	   if($pictureExist){
		   // Unlink the picture
		   @unlink(CATEGORY_ROOT_PATH.'/'.$pictureExist);
	   }
		$delete = Models\Category::find($id)->delete();
		return $response->withJson(json_encode(array("status" => TRUE)));
		
	}
	
	
	
	
	
	
	
	
	
}
