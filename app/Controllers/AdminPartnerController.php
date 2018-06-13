<?php
namespace App\Controllers;

use App\Models\User;
use App\Models;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v;

/**
  Admin Partner Controller
  Availble Functions.
  1. partners
  2. ajaxPartnersList
  3. getPartnerById
  4. savePartner
  5. updatePartner
  6. deletePartnerById
 

*/


class AdminPartnerController extends Base 
{
	
	// Main function to display partners list 
	public function partners($request, $response) {
        $params = array( 'title' => 'All Partners',
		                 'current_url' => $request->getUri()->getPath());
        return $this->render($response, ADMIN_VIEW.'/Partner/partners.twig',$params);
    }
	
	// Ajax Partners list
	public function ajaxPartnersList($request, $response){
		
		$isSearched = $drpSearch = false; // set to false if user did not search something
		$conditions = array();
		$whereData =  $customSearch = $prefix = '';
		if($request->getParam('query') != null){
			 $isSearched = true; // user has searched something
			 $fields = array('picture_caption', 'redirect_link');
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
		    $total   = Models\Partner::whereRaw($whereData)->count(); // get count 
		}else{
			$total   = Models\Partner::get()->count(); // get count 
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
		    $partners_list = Models\Partner::whereRaw($whereData)->skip($offset*$perPageLimit)->take($perPageLimit)->orderBy($field, $sort)->get();
		}else{
			$partners_list = Models\Partner::skip($offset*$perPageLimit)->take($perPageLimit)->orderBy($field, $sort)->get();
		}

		$data = array();
		foreach($partners_list as $get){
			// Check if a picture is not uploaded show default picture
			if($get->partner_logo <> '' && file_exists(PARTNER_ROOT_PATH.'/'.$get->partner_logo)){
				$partnerLogo = PARTNER_WEB_PATH.'/'.$get->partner_logo;
			}else{
			  	$partnerLogo = DEFAULT_IMG;
			}
			
		  	$array_data = array();
			$array_data['id']  = $get->id;
            $array_data['partner_logo']  = $partnerLogo;
			$array_data['partner_url']  = empty($get->partner_url) ? '#' :  '<a href="'.$get->partner_url.'" target="_blank">'.$get->partner_url.'</a>';
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
	
	// Get Partner by id
	public function getPartnerById($request, $response, $args){
		$id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		$category = Models\Partner::find($id);
		if ($category) {
            $category['file_web_path'] = PARTNER_WEB_PATH;
			return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode($category));
        }
		
	}
	
	// Save partner
	public function savePartner($request, $response){
	   $partner_logo = $request->getParam('partner_logo');
	   $partner_url = $request->getParam('partner_url');
	   $status = $request->getParam('status');
	   $partnerExist = Models\Partner::where('partner_url', '=', $partner_url)->first();
	   if($partnerExist){
		 echo json_encode(array("status" => 'duplicate', 
		                  'message' => 'Partner URL (<strong>'.$partner_url. '</strong>) already exist.'));
		 exit();	   
	   }else{
		   if(isset($_FILES) && $_FILES['partner_logo']['name'] <> ''){
			   $allowedExtensions = allowedExtensions();
			   $file = $_FILES['partner_logo']['tmp_name'];
			   $file_name = explode('.',$_FILES['partner_logo']['name']);
			   $file_extension = end($file_name);
			   if(!in_array($file_extension, $allowedExtensions)){
				   echo json_encode(array("status" => 'file_error', 
					               'message' => 'File with Type [<strong>'.$file_extension. '</strong>] is not allowed.'));	
				   exit();
			   }else{
				    $file_new_name = md5(uniqid());
				    $filename = $file_new_name.'.'.$file_extension;
				   if(!empty($file)){
					  move_uploaded_file($file, PARTNER_ROOT_PATH.'/'.$filename);
					  $partner_logo = $filename;
					 }else{
					  $partner_logo = '';	
				   }

			   }
			   
		   }else{
			   $partner_logo = '';
		   }
		   // Save to partners table
		   $partner = new Models\Partner;
		   $partner->partner_logo = $partner_logo;
		   $partner->partner_url = $partner_url;
		   $partner->status = $status;
		   $partner->save();
		  return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode(array('status' => TRUE)));
	   }
	   
	   
	   
	}
	
	// Update Partner
	public function updatePartner($request, $response){
		
	   $id   = $request->getParam('id');
	   $partner_logo = $request->getParam('partner_logo');
	   $partner_url = $request->getParam('partner_url');
	   $status = $request->getParam('status');
	   $partner_logo_old = $request->getParam('partner_logo_old');
	   $status = $request->getParam('status');
	   $partnerExist = Models\Partner::where('partner_url', '=', $partner_url)->where('id', '!=', $id)->first();
	   if($partnerExist){
		 echo json_encode(array("status" => 'duplicate', 
		                  'message' => 'Partner (<strong>'.$partner_url. '</strong>) already exist.'));
		 exit();	   
	   }else{
		   if(isset($_FILES) && $_FILES['partner_logo']['name'] <> ''){
			   $allowedExtensions = allowedExtensions();
			   $file = $_FILES['partner_logo']['tmp_name'];
			   $file_name = explode('.',$_FILES['partner_logo']['name']);
			   $file_extension = end($file_name);
			   if(!in_array($file_extension, $allowedExtensions)){
				   echo json_encode(array("status" => 'file_error', 
					               'message' => 'File with Type [<strong>'.$file_extension. '</strong>] is not allowed.'));	
				   exit();
			   }else{
				    $file_new_name = md5(uniqid());
				    $filename = $file_new_name.'.'.$file_extension;
				   if(!empty($file)){
					  move_uploaded_file($file, PARTNER_ROOT_PATH.'/'.$filename);
					  $partner_logo = $filename;
					 }else{
					  $partner_logo = '';	
				   }

			   }
			   
		   }else{
			   $partner_logo = '';
		   }
		   if($_FILES['partner_logo']['tmp_name'] <> ''){
			   // Delete old images
			   if($partner_logo_old <> ''){
				 @unlink(PARTNER_ROOT_PATH.'/'.$partner_logo_old);
			   }
		   }else{
			    $partner_logo = $partner_logo_old; 
		   }
		   
		   // Save to slider table
		   $data = array('partner_logo' => $partner_logo,
						 'partner_url' => $partner_url,
						 'status' => $status);
		  $category = Models\Partner::where('id', '=', $id)->update($data);					 
		  return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode(array('status' => TRUE)));
	   }
	}
	
	// Delete Partner
	public function deletePartnerById($request, $response, $args){
		 $id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		// Check if this partner has a picture uploaded.
		$pictureExist = Models\Partner::where('id', '=', $id)->first()->partner_logo;
	   if($pictureExist){
		   // Unlink the picture
		   @unlink(PARTNER_ROOT_PATH.'/'.$pictureExist);
	   }
		$delete = Models\Partner::find($id)->delete();
		return $response->withJson(json_encode(array("status" => TRUE)));
		
	}
	
	
	
	
	
	
	
	
	
}
