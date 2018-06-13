<?php
namespace App\Controllers;

use App\Models;
use App\Tools\Auth;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v;

/**
   Admin Producdtor Controller
   Available Functions.
   1. productors
   2. getAjaxProductorsList
   3. getProductorById
   4. saveProductor
   5. updateProductor
   6. deleteProductorById
   
*/


class AdminProductorController extends Base 
{
	
	protected $container;
	protected $lang;
	// Class constructor 
	public function __construct($container)
	{
		$this->container = $container;
		$this->lang =  $this->container->view['adminLang'];
	}
	
	 // Main function to display list of productors
	public function productors($request, $response) {
		
        $params = array( 'title' => $this->lang['productor_all_txt'],
		                 'current_url' => $request->getUri()->getPath());
        return $this->render($response, ADMIN_VIEW.'/Productor/productors.twig',$params);
    }
	
	// Ajax Productor list
	public function getAjaxProductorsList($request, $response){
		
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
		$sort  = !empty($request->getParam('sort')['sort']) ? $request->getParam('sort')['sort'] : 'DESC';
        $field = !empty($request->getParam('sort')['field']) ? $request->getParam('sort')['field'] : 'id';
		
		$page     = $request->getParam('pagination')['page'];
		if( !empty($request->getParam('pagination')['pages']) ){
		  $pages    = $request->getParam('pagination')['pages'];
		}
		$per_page = $request->getParam('pagination')['perpage'];
		
		if($isSearched){
		    $total   = Models\User::with(['productormeta'])->where('type','=','Productor')->whereRaw($whereData)->count(); // get count 
		}else{
			$total   = Models\User::with(['productormeta'])->where('type','=','Productor')->count(); // get count 
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
		    $productors_list = Models\User::with(['productormeta'])->whereRaw($whereData)->skip($offset*$perPageLimit)->take($perPageLimit)->orderBy($field, $sort)->get();
		}else{
			$productors_list = Models\User::with(['productormeta'])->where('type','=', 'Productor')->skip($offset*$perPageLimit)->take($perPageLimit)->orderBy($field, $sort)->get();
		}
		
		
		$data = array();
		foreach($productors_list as $get){
		  	$array_data = array();
			$array_data['id']  = $get->id;
            $array_data['company_name']  = $get['productormeta']['company_name'];
			$array_data['name']  = $get->name;
            $array_data['first_name']  = $get['productormeta']['first_name'];
			$array_data['email']  = $get->email;
			$array_data['telephone']  = $get['productormeta']['telephone'];
			$array_data['office_phone']  = $get['productormeta']['office_phone'];
			$array_data['company_phone']  = $get['productormeta']['company_number'];
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
	
	
	// Get Productor by id
	public function getProductorById($request, $response, $args){
		$id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		$productor = Models\User::find($id);
		if($productor['user_picture'] == '' || $artist['user_picture'] === null){
			$productor['file_web_path'] = DEFAULT_IMG;
		}else{
		  $productor['file_web_path'] = PRODUCTOR_WEB_PATH;
		}
		
		$productor_meta = Models\Productor_meta::where('user_id', '=', $id)->get();
		
		if( $productor_meta->isEmpty() ){
			 echo json_encode(array('productor' => $productor, 'productor_data' => ''));
		}else{
			 echo json_encode(array('productor' => $productor, 'productor_data' => $productor_meta));
		}
		
		
  }
  
  // Save Productor
  public function saveProductor($request, $response){
	   $isError = false;
	   
	   $artistName   = $request->getParam('artist_name');
	   $artistUsername   = $request->getParam('artist_username');
	   $artistEmail = $request->getParam('artist_email');
	   $type = $request->getParam('type');
	   $status = $request->getParam('status');
	   $first_name   = $request->getParam('first_name');
	   $company_name = $request->getParam('company_name');
	   $telephone = $request->getParam('telephone');
	   $office_phone = $request->getParam('office_phone');
	   $company_number = $request->getParam('company_number');
	   
	   $emailExist = Models\User::where('email', '=', $artistEmail)->first();
	   if(empty($first_name)){
		   $isError = true;
		   echo json_encode(array("status" => 'error', 
		                        'message' => 'Please enter productor first name.'));
		   exit();
	   }elseif(empty($artistName)){
		   $isError = true;
		   echo json_encode(array("status" => 'error', 
		                        'message' => 'Please enter productor full name.'));
		   exit();
	   }else  if(!isValidEmail($artistEmail)){
		   $isError = true;
		 echo json_encode(array("status" => 'error', 
		                    'message' => 'Please enter valid email.'));
		 exit();
	   }elseif(empty($company_name)){
		   $isError = true;
		   echo json_encode(array("status" => 'error', 
		                        'message' => 'Please enter company name.'));
		   exit();
	   }elseif(empty($telephone)){
		   $isError = true;
		   echo json_encode(array("status" => 'error', 
		                        'message' => 'Please enter productor telephone.'));
		   exit();
	   }elseif(empty($office_phone)){
		   $isError = true;
		   echo json_encode(array("status" => 'error', 
		                        'message' => 'Please enter productor office phone.'));
		   exit();
	   }else{
	     $user_picture = '';
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
		   $user_id = $user->id;
		   
		   $prod = new Models\Productor_meta;
		   $prod->user_id = $user_id;
		   $prod->company_name = $company_name;
		   $prod->first_name = $first_name;
		   $prod->telephone = $telephone;
		   $prod->office_phone  = $office_phone;
		   $prod->company_number = $company_number;
		   $prod->registration_date = date('Y-m-d');
		    $prod->save();
		   return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode(array('status' => TRUE)));
	   }
	   
	   
	}
	
	// Update Productor
	public function updateProductor($request, $response){
	   $isError = false;
	   $id   = $request->getParam('id');
	   $artistName   = $request->getParam('artist_name');
	   $artistUsername   = $request->getParam('artist_username');
	   $artistEmail = $request->getParam('artist_email');
	   $type = $request->getParam('type');
	   $status = $request->getParam('status');
	   $first_name   = $request->getParam('first_name');
	   $company_name = $request->getParam('company_name');
	   $telephone = $request->getParam('telephone');
	   $office_phone = $request->getParam('office_phone');
	   $company_number = $request->getParam('company_number');
	   $user_picture_old = $request->getParam('user_picture_old');
	   $emailExist = Models\User::where('email', '=', $artistEmail)->where('id', '!=', $id)->first();
	   if(empty($first_name)){
		   $isError = true;
		   echo json_encode(array("status" => 'error', 
		                        'message' => 'Please enter productor first name.'));
		   exit();
	   }elseif(empty($artistName)){
		   $isError = true;
		   echo json_encode(array("status" => 'error', 
		                        'message' => 'Please enter productor full name.'));
		   exit();
	   }else  if(!isValidEmail($artistEmail)){
		   $isError = true;
		 echo json_encode(array("status" => 'error', 
		                    'message' => 'Please enter valid email.'));
		 exit();
	   }elseif(empty($company_name)){
		   $isError = true;
		   echo json_encode(array("status" => 'error', 
		                        'message' => 'Please enter company name.'));
		   exit();
	   }elseif(empty($telephone)){
		   $isError = true;
		   echo json_encode(array("status" => 'error', 
		                        'message' => 'Please enter productor telephone.'));
		   exit();
	   }elseif(empty($office_phone)){
		   $isError = true;
		   echo json_encode(array("status" => 'error', 
		                        'message' => 'Please enter productor office phone.'));
		   exit();
	   }else{
	     $user_picture = '';
		 $isError = false;   
	   }
	   
	   
		
	   if(!$isError){
		   // update to users table
		   $data = array('name' => $artistName,
						 'email' => $artistEmail,
						 'status' => $status);
		   $user = Models\User::where('id', '=', $id)->update($data);
		   
		   $meta = array('user_id' => $id,
		                 'company_name' => $company_name,
						 'first_name' => $first_name,
						 'telephone' => $telephone,
						 'office_phone' => $office_phone,
						 'company_number' => $company_number);
		   $user_meta = Models\Productor_meta::where('user_id', '=', $id)->update($meta);
		   return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode(array('status' => TRUE)));
	   }
	   
	   
	}
	
	// Delete Productor by id
	public function deleteProductorById($request, $response, $args){
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
