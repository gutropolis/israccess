<?php
namespace App\Controllers;

use App\Models;
use App\Tools\Auth;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v;
/*
*  Admin Aritst Controller
*  CRUD of Artist
   Available Functions
   1. artists
   2. getAjaxArtistsList
   3. getArtistById
   4. saveArtist
   5. updateArtist
   6. deleteArtistById
  
*/
class AdminArtistController extends Base 
{
	protected $container;
	protected $lang;
	// Class constructor
	public function __construct($container)
	{
		$this->container = $container;
		$this->lang =  $this->container->view['adminLang'];
	}
	
	 // Main function to display list of artists
	public function artists($request, $response) {
        $params = array( 'title' => $this->lang['artist_all_txt'],
		                 'current_url' => $request->getUri()->getPath());
        return $this->render($response, ADMIN_VIEW.'/Artist/artists.twig',$params);
    }
	
	// Ajax Category list
	public function getAjaxArtistsList($request, $response){
		
		$isSearched = $drpSearch = false; // set to false if user did not search something
		$conditions = array();
		$whereData =  $customSearch = $prefix = '';
		if($request->getParam('query') != null){
			 $isSearched = true; // user has searched something
			 $fields = array('name', 'username','email', 'status');
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
		    $total   = Models\User::where('type','=','Artist')->whereRaw($whereData)->count(); // get count 
		}else{
			$total   = Models\User::where('type','=','Artist')->count(); // get count 
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
		    $categories_list = Models\User::whereRaw($whereData)->skip($offset*$perPageLimit)->take($perPageLimit)->orderBy($field, $sort)->get();
		}else{
			$categories_list = Models\User::where('type','=', 'Artist')->skip($offset*$perPageLimit)->take($perPageLimit)->orderBy($field, $sort)->get();
		}
		
		$data = array();
		foreach($categories_list as $get){
			$user_picture = $get->user_picture;
			if($user_picture <> '' && $user_picture !== null ){	
				$user_picture = ARTIST_WEB_PATH.'/thumbs/'.$user_picture;
			}else{
			  	$user_picture = DEFAULT_IMG;
			}
			
		  	$array_data = array();
			$array_data['id']  = $get->id;
			$array_data['user_picture']  = $user_picture;
            $array_data['name']  = $get->name;
            $array_data['username']  = $get->username;
			$array_data['email']  = $get->email;
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
	
	
	// Get Artist by id
	public function getArtistById($request, $response, $args){
		$id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		$artist = Models\User::find($id);
		if($artist['user_picture'] == '' || $artist['user_picture'] === null){
			$artist['file_web_path'] = DEFAULT_IMG;
		}else{
		  $artist['file_web_path'] = ARTIST_WEB_PATH;
		}
		if ($artist) {
            echo json_encode($artist);
        }
		
  }
  /*
  *  Save Artist
  */
  public function saveArtist($request, $response){
	   $isError = false;
	   $artistName   = $request->getParam('artist_name');
	   $artistUsername   = $request->getParam('artist_username');
	   $artistEmail = $request->getParam('artist_email');
	   $type = $request->getParam('type');
	   $status = $request->getParam('status');
	   $emailExist = Models\User::where('email', '=', $artistEmail)->first();
	   if(empty($artistName)){
		   $isError = true;
		   echo json_encode(array("status" => 'error', 
		                        'message' => $this->lang['artist_name_msg_txt']));
		   exit();
	   }elseif(empty($artistUsername)){
		   $isError = true;
		   echo json_encode(array("status" => 'error', 
		                        'message' => $this->lang['artist_username_msg_txt']));
		   exit();
	   }elseif(empty($artistEmail)){
		   $isError = true;
		   echo json_encode(array("status" => 'error', 
		                        'message' => $this->lang['artist_email_msg_txt']));
		   exit();
	   }else  if(!isValidEmail($artistEmail)){
		   $isError = true;
		 echo json_encode(array("status" => 'error', 
		                    'message' => $this->lang['artist_valid_email_msg_txt']));
		 exit();
	   }else if(isValidEmail($artistEmail) && $emailExist){
		   $isError = true;
		    echo json_encode(array("status" => 'duplicate', 
		                  'message' => 'Email (<strong>'.$artistEmail. '</strong>) '.$this->lang['common_already_exist_txt']));
		   exit();	   
	   }else{
		   if(isset($_FILES) && $_FILES['user_picture']['name'] <> ''){
			   $allowedExtensions = allowedExtensions();
			   $file = $_FILES['user_picture']['tmp_name'];
			   $file_name = explode('.',$_FILES['user_picture']['name']);
			   $file_extension = end($file_name);
			   if(!in_array($file_extension, $allowedExtensions)){
				   echo json_encode(array("status" => 'file_error', 
					               'message' => $this->lang['common_file_with_type_txt'].' [<strong>'.$file_extension. '</strong>] '.$this->lang['common_is_not_allowed_txt']));	
				   exit();
			   }else{
				   $file = $_FILES['user_picture']['tmp_name'];
				    $file_new_name = md5(uniqid());
				    $filename = $file_new_name.'.'.$file_extension;
					$file = $_FILES['user_picture']['tmp_name'];
				   if(!empty($file)){
					   $resizedFile = ARTIST_ROOT_PATH.'/thumbs/'.$filename;
					   
					   smart_resize_image($file , null,  THUMB_WEIGHT, THUMB_HEIGHT , false , $resizedFile , false , false ,100 );
					  move_uploaded_file($file, ARTIST_ROOT_PATH.'/'.$filename);
					  $user_picture = $filename;
					 }else{
					  $user_picture = '';	
				   }

			   }
			   
		   }else{
			   $user_picture = '';
		   }
		   $isError = false;   
	   }
	   
	   if(!$isError){
		   $rand_password = get_random_string(6);
		   $AuthObj = new  Auth();
		   $new_password = $AuthObj->changePassword($rand_password);
		   // Save to users table
		   $user = new Models\User;
		   $user->name = $artistName;
		   $user->username = $artistUsername;
		   $user->email = $artistEmail;
		   $user->password =  $new_password; 
		   $user->status = $status;
		   $user->credit = 0.00;
		   $user->type = $type;
		   $user->user_picture = $user_picture;
		   $user->save();
		   return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode(array('status' => TRUE)));
	   }
	   
	   
	}
	
	/*
	*  Update artist
	*/
	public function updateArtist($request, $response){
	   $isError = false;
	   $id   = $request->getParam('id');
	   $artistName   = $request->getParam('artist_name');
	   $artistUsername   = $request->getParam('artist_username');
	   $artistEmail = $request->getParam('artist_email');
	   $type = $request->getParam('type');
	   $status = $request->getParam('status');
	   $user_picture_old = $request->getParam('user_picture_old');
	   $emailExist = Models\User::where('email', '=', $artistEmail)->where('id', '!=', $id)->first();
	   if(empty($artistName)){
		   $isError = true;
		   echo json_encode(array("status" => 'error', 
		                        'message' => $this->lang['artist_name_msg_txt']));
		   exit();
	   }elseif(empty($artistUsername)){
		   $isError = true;
		   echo json_encode(array("status" => 'error', 
		                        'message' => $this->lang['artist_username_msg_txt']));
		   exit();
	   }elseif(empty($artistEmail)){
		   $isError = true;
		   echo json_encode(array("status" => 'error', 
		                        'message' => $this->lang['artist_email_msg_txt']));
		   exit();
	   }else  if(!isValidEmail($artistEmail)){
		   $isError = true;
		 echo json_encode(array("status" => 'error', 
		                    'message' => $this->lang['artist_valid_email_msg_txt']));
		 exit();
	   }else if(isValidEmail($artistEmail) && $emailExist){
		   $isError = true;
		    echo json_encode(array("status" => 'duplicate', 
		                  'message' => 'Email (<strong>'.$artistEmail. '</strong>) '.$this->lang['common_already_exist_txt']));
		   exit();	   
	   }else{
		   if(isset($_FILES) && $_FILES['user_picture']['name'] <> ''){
			   $allowedExtensions = allowedExtensions();
			   $file = $_FILES['user_picture']['tmp_name'];
			   $file_name = explode('.',$_FILES['user_picture']['name']);
			   $file_extension = end($file_name);
			   if(!in_array($file_extension, $allowedExtensions)){
				   echo json_encode(array("status" => 'file_error', 
					               'message' => $this->lang['common_file_with_type_txt'].' [<strong>'.$file_extension. '</strong>] '.$this->lang['common_is_not_allowed_txt']));	
				   exit();
			   }else{
				    $file_new_name = md5(uniqid());
				    $filename = $file_new_name.'.'.$file_extension;
					 $file = $_FILES['user_picture']['tmp_name'];
				   if(!empty($file)){
					   $resizedFile = ARTIST_ROOT_PATH.'/thumbs/'.$filename;
					   
					   smart_resize_image($file , null,  THUMB_WEIGHT, THUMB_HEIGHT , false , $resizedFile , false , false ,100 );
					  move_uploaded_file($file, ARTIST_ROOT_PATH.'/'.$filename);
					  $user_picture = $filename;
					 }else{
					  $user_picture = '';	
				   }

			   }
			   
		   }else{
			   $user_picture = '';
		   }
		$isError = false;   
	   }
	   
	   if($_FILES['user_picture']['tmp_name'] <> ''){
			   // Delete old images
			   if($user_picture_old <> ''){
					 @unlink(ARTIST_ROOT_PATH.'/thumbs/'.$user_picture_old);
					 @unlink(ARTIST_ROOT_PATH.'/'.$user_picture_old);
			   }
			   $user_picture = $user_picture;
		   }else{
			   $user_picture = $user_picture_old; 
		   }
	   
		
	   if(!$isError ){
		   // update to users table
		   $data = array('name' => $artistName,
		                 'username' => $artistUsername,
						 'email' => $artistEmail,
						 'user_picture' => $user_picture,
						 'status' => $status);
		   $user = Models\User::where('id', '=', $id)->update($data);
		  return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode(array('status' => TRUE)));
	   }
	   
	   
	}
	
	/*
	*  Delete Artist by id
	*/
	public function deleteArtistById($request, $response, $args){
		$id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		$delete = Models\User::find($id)->delete();
		return $response->withJson(json_encode(array("status" => TRUE)));
	}
	
	
	
	
}
