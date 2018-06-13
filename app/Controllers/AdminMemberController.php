<?php
namespace App\Controllers;

use App\Models;
use App\Tools\Auth;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v;

/**
  Admin Member Controller
  Available Functions.
  1. members
  2. getAjaxMembersList
  3. getMemberById
  4. viewMemberById
  5. saveMember
  6. updateMember
  7. deleteMemberById
  
*/
class AdminMemberController extends Base 
{
	
	protected $container;
	protected $lang;
	// Class constructor
	public function __construct($container)
	{
		$this->container = $container;
		$this->lang =  $this->container->view['adminLang'];
	}
	
	 // Main function to display all members list
	public function members($request, $response) {
		
        $params = array( 'title' => $this->lang['member_all_txt'],
		                  'current_url' => $request->getUri()->getPath());
        return $this->render($response, ADMIN_VIEW.'/Member/members.twig',$params);
    }
	
	// Ajax members list
	public function getAjaxMembersList($request, $response){
		
		$isSearched = $drpSearch = false; // set to false if user did not search something
		$conditions = array();
		$whereData =  $customSearch = $prefix = '';
		if($request->getParam('query') != null){
			 $isSearched = true; // user has searched something
			 $fields = array('name', 'status');
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
		    $total   = Models\User::with(['memberdata'])->where('type','=','Member')->whereRaw($whereData)->count(); // get count 
		}else{
			$total   = Models\User::with(['memberdata'])->where('type','=','Member')->count(); // get count 
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
		    $members_list = Models\User::with(['memberdata'])->whereRaw($whereData)->skip($offset*$perPageLimit)->take($perPageLimit)->orderBy($field, $sort)->get();
		}else{
			$members_list = Models\User::with(['memberdata'])->where('type','=', 'Member')->skip($offset*$perPageLimit)->take($perPageLimit)->orderBy($field, $sort)->get();
		}
		
		$data = array();
		foreach($members_list as $get){
			$first_name = $get['memberdata']['first_name'];
			$city = $get['memberdata']['ville'];
			$country = $get['memberdata']['country'];
		  	$array_data = array();
			$array_data['id']  = $get->id;
			$array_data['member_id']  = $get->id;
            $array_data['name']  = $get->name;
            $array_data['first_name']  = $first_name;
			$array_data['email']  = $get->email;
			$array_data['city']  = $city;
			$array_data['country']  = $country;
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
	
	
	// Get Member by id
	public function getMemberById($request, $response, $args){
		$id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		$member = Models\User::find($id);
		if($member['user_picture'] == '' || $member['user_picture'] === null){
			$member['file_web_path'] = DEFAULT_IMG;
		}else{
		  $member['file_web_path'] = MEMBER_WEB_PATH.'/'.$member['user_picture'];
		}
		$member_meta = Models\Usermeta::where('user_id', '=', $id)->get();
		
		if( $member_meta->isEmpty() ){
			 echo json_encode(array('member' => $member, 'member_data' => ''));
		}else{
			 $member_meta[0]['dob'] = hr_date($member_meta[0]['dob']);
			 echo json_encode(array('member' => $member, 'member_data' => $member_meta));
		}
		
		
		
  }
  
  // View Member by id
	public function viewMemberById($request, $response, $args){
		$id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		$member = Models\User::find($id);
		if($member['user_picture'] == '' || $member['user_picture'] === null){
			$member['file_web_path'] = DEFAULT_IMG;
		}else{
		  $member['file_web_path'] = MEMBER_WEB_PATH.'/'.$member['user_picture'];
		}
		if($member['status'] == 1){
			$member['status'] = 'Active';
		}else{
		   $member['status'] = 'Inactive';
		}
		$member_meta = Models\Usermeta::where('user_id', '=', $id)->get();
		//echo $member_meta[0]['dob'];
		$member_meta[0]['dob'] = hr_date($member_meta[0]['dob']);
		
		if ($member) {
            echo json_encode(array('member' => $member, 'member_data' => $member_meta));
        }
		
  }
  
  // Save Member
  public function saveMember($request, $response){
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
		                        'message' => $this->lang['member_name_msg_txt']));
		   exit();
	   }elseif(empty($artistUsername)){
		   $isError = true;
		   echo json_encode(array("status" => 'error', 
		                        'message' => $this->lang['member_username_msg_txt']));
		   exit();
	   }elseif(empty($artistEmail)){
		   $isError = true;
		   echo json_encode(array("status" => 'error', 
		                        'message' => $this->lang['member_email_msg_txt']));
		   exit();
	   }else  if(!isValidEmail($artistEmail)){
		   $isError = true;
		 echo json_encode(array("status" => 'error', 
		                    'message' => $this->lang['member_valid_email_msg_txt']));
		 exit();
	   }else if(isValidEmail($artistEmail) && $emailExist){
		   $isError = true;
		    echo json_encode(array("status" => 'duplicate', 
		                  'message' => $this->lang['admin_email_label_txt'].' (<strong>'.$artistEmail. '</strong>) '.$this->lang['common_already_exist_txt']));
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
					   $resizedFile = MEMBER_ROOT_PATH.'/thumbs/'.$filename;
					   smart_resize_image($file , null,  THUMB_WEIGHT, THUMB_HEIGHT , false , $resizedFile , false , false ,100 );
					  move_uploaded_file($file, MEMBER_ROOT_PATH.'/'.$filename);
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
		   $user->password = $new_password;
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
	
	// Update Member
	public function updateMember($request, $response){
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
		                        'message' => $this->lang['member_name_msg_txt']));
		   exit();
	   }elseif(empty($artistUsername)){
		   $isError = true;
		   echo json_encode(array("status" => 'error', 
		                        'message' => $this->lang['member_username_msg_txt']));
		   exit();
	   }elseif(empty($artistEmail)){
		   $isError = true;
		   echo json_encode(array("status" => 'error', 
		                        'message' => $this->lang['member_email_msg_txt']));
		   exit();
	   }else  if(!isValidEmail($artistEmail)){
		   $isError = true;
		 echo json_encode(array("status" => 'error', 
		                    'message' => $this->lang['member_valid_email_msg_txt']));
		 exit();
	   }else if(isValidEmail($artistEmail) && $emailExist){
		   $isError = true;
		     echo json_encode(array("status" => 'duplicate', 
		                  'message' => $this->lang['admin_email_label_txt'].' (<strong>'.$artistEmail. '</strong>) '.$this->lang['common_already_exist_txt']));
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
					   $resizedFile = MEMBER_ROOT_PATH.'/thumbs/'.$filename;
					   smart_resize_image($file , null,  THUMB_WEIGHT, THUMB_HEIGHT , false , $resizedFile , false , false ,100 );
					  move_uploaded_file($file, MEMBER_ROOT_PATH.'/'.$filename);
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
					 @unlink(MEMBER_ROOT_PATH.'/thumbs/'.$user_picture_old);
					 @unlink(MEMBER_ROOT_PATH.'/'.$user_picture_old);
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
		   $first_name = $request->getParam('first_name');
		   $last_name = $request->getParam('last_name');
		   $address_1 = $request->getParam('address_1');
		   $address_2 = $request->getParam('address_2');
		   $street = $request->getParam('street');
		   $postal_code = $request->getParam('postal_code');
		   $phone_no = $request->getParam('phone_no');
		   $dob = $request->getParam('dob');
		   $country = $request->getParam('country');
		   $ville = $request->getParam('ville');
		    $member_meta = Models\Usermeta::where('user_id', '=', $id)->get();
			if( $member_meta->isEmpty() ){
				// Save data
				 $user_meta = new Models\Usermeta;
			     $user_meta->user_id = $id;
			     $user_meta->first_name = $first_name;
			     $user_meta->last_name = $last_name;
			     $user_meta->address_1 = $address_1;
			     $user_meta->address_2 = $address_2;
			     $user_meta->street = $street;
			     $user_meta->postal_code = $postal_code;
			     $user_meta->phone_no = $phone_no;
				 $user_meta->dob = mysql_date($dob);
				 $user_meta->country = $country;
				 $user_meta->ville = $ville;
			     $user_meta->save();
			}else{
				// Update data
				$data = array('user_id' => $id,
		                 'first_name' => $first_name,
						 'last_name' => $last_name,
						 'address_1' => $address_1,
						 'address_2' => $address_2,
						 'street' => $street,
						 'postal_code' => $postal_code,
						 'phone_no' => $phone_no,
						 'dob' => mysql_date($dob),
						 'country' => $country,
						 'ville' => $ville);
		      $update = Models\Usermeta::where('user_id', '=', $id)->update($data);
				
			}
		   return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode(array('status' => TRUE)));
	   }
	   
	   
	}
	
	// Delete Member by id
	public function deleteMemberById($request, $response, $args){
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
