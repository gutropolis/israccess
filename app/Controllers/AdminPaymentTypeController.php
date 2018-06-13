<?php
namespace App\Controllers;

use App\Models\User;
use App\Models;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v;

/**
  Admin Pyament Type Controller
  1. paymentTypes
  2. ajaxPaymentTypesList
  3. getPaymentTypeById
  4. savePaymentType
  5. updatePaymentType
  6. deletePaymentTypeById
  
*/


class AdminPaymentTypeController extends Base 
{
	
	// Main function to display list of all the payment types
	public function paymentTypes($request, $response) {
        $params = array( 'title' => 'All Payment Types',
		                 'current_url' => $request->getUri()->getPath());
        return $this->render($response, ADMIN_VIEW.'/PaymentType/paymentTypes.twig',$params);
    }
	
	// Ajax Paymetn Type list
	public function ajaxPaymentTypesList($request, $response){
		
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
		    $total   = Models\PaymentType::whereRaw($whereData)->count(); // get count 
		}else{
			$total   = Models\PaymentType::get()->count(); // get count 
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
		    $payment_types_list = Models\PaymentType::whereRaw($whereData)->skip($offset*$perPageLimit)->take($perPageLimit)->orderBy($field, $sort)->get();
		}else{
			$payment_types_list = Models\PaymentType::skip($offset*$perPageLimit)->take($perPageLimit)->orderBy($field, $sort)->get();
		}
		
		$data = array();
		foreach($payment_types_list as $get){
			// Check if a picture is not uploaded show default picture
			if($get->payment_logo <> '' && file_exists(PAYMENT_TYPE_ROOT_PATH.'/'.$get->payment_logo)){
				$paymentTypePicture = PAYMENT_TYPE_WEB_PATH.'/'.$get->payment_logo;
			}else{
			  	$paymentTypePicture = DEFAULT_IMG;
			}
			
		  	$array_data = array();
			$array_data['id']  = $get->id;
            $array_data['name']  = $get->name;
            $array_data['payment_logo']  = $paymentTypePicture;
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
	
	// Get payment Type by id
	public function getPaymentTypeById($request, $response, $args){
		$id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		$category = Models\PaymentType::find($id);
		if ($category) {
            $category['file_web_path'] = PAYMENT_TYPE_WEB_PATH;
			return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode($category));
        }
		
	}
	
	// Save paymetn type
	public function savePaymentType($request, $response){
	   $name = $request->getParam('name');
	   $status = $request->getParam('status');
	   $paymentTypeExist = Models\PaymentType::where('name', '=', $name)->first();
	   if($paymentTypeExist){
		 echo json_encode(array("status" => 'duplicate', 
		                  'message' => 'Payment Type (<strong>'.$name. '</strong>) already exist.'));
		 exit();	   
	   }else{
		   if(isset($_FILES) && $_FILES['payment_logo']['name'] <> ''){
			   $allowedExtensions = allowedExtensions();
			   $file = $_FILES['payment_logo']['tmp_name'];
			   $file_name = explode('.',$_FILES['payment_logo']['name']);
			   $file_extension = end($file_name);
			   if(!in_array($file_extension, $allowedExtensions)){
				   echo json_encode(array("status" => 'file_error', 
					               'message' => 'File with Type [<strong>'.$file_extension. '</strong>] is not allowed.'));	
				   exit();
			   }else{
				    $file_new_name = md5(uniqid());
				    $filename = $file_new_name.'.'.$file_extension;
				   if(!empty($file)){
					  move_uploaded_file($file, PAYMENT_TYPE_ROOT_PATH.'/'.$filename);
					  $payment_logo = $filename;
					 }else{
					  $payment_logo = '';	
				   }

			   }
			   
		   }else{
			   $payment_logo = '';
		   }
		   // Save to sliders table
		   $payment_type = new Models\PaymentType;
		   $payment_type->name = $name;
		   $payment_type->payment_logo = $payment_logo;
		   $payment_type->status = $status;
		   $payment_type->save();
		  return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode(array('status' => TRUE)));
	   }
	   
	   
	   
	}
	
	// Update Payment Type
	public function updatePaymentType($request, $response){
		
	   $id   = $request->getParam('id');
	   $name = $request->getParam('name');
	   $status = $request->getParam('status');
	   $payment_logo_old = $request->getParam('payment_logo_old');
	   $status = $request->getParam('status');
	   $paymentTypeExist = Models\PaymentType::where('name', '=', $name)->where('id', '!=', $id)->first();
	   if($paymentTypeExist){
		 echo json_encode(array("status" => 'duplicate', 
		                  'message' => 'Payment Type (<strong>'.$name. '</strong>) already exist.'));
		 exit();	   
	   }else{
		   if(isset($_FILES) && $_FILES['payment_logo']['name'] <> ''){
			   $allowedExtensions = allowedExtensions();
			   $file = $_FILES['payment_logo']['tmp_name'];
			   $file_name = explode('.',$_FILES['payment_logo']['name']);
			   $file_extension = end($file_name);
			   if(!in_array($file_extension, $allowedExtensions)){
				   echo json_encode(array("status" => 'file_error', 
					               'message' => 'File with Type [<strong>'.$file_extension. '</strong>] is not allowed.'));	
				   exit();
			   }else{
				    $file_new_name = md5(uniqid());
				    $filename = $file_new_name.'.'.$file_extension;
				   if(!empty($file)){
					  move_uploaded_file($file, PAYMENT_TYPE_ROOT_PATH.'/'.$filename);
					  $payment_logo = $filename;
					 }else{
					  $payment_logo = '';	
				   }

			   }
			   
		   }else{
			   $payment_logo = '';
		   }
		   if($_FILES['payment_logo']['tmp_name'] <> ''){
			   // Delete old images
			   if($payment_logo_old <> ''){
				 @unlink(PAYMENT_TYPE_ROOT_PATH.'/'.$payment_logo_old);
			   }
		   }else{
			    $payment_logo = $payment_logo_old; 
		   }
		   
		   // Save to slider table
		   $data = array('name' => $name,
		                 'payment_logo' => $payment_logo,
						 'status' => $status);
		  $category = Models\PaymentType::where('id', '=', $id)->update($data);					 
		  return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode(array('status' => TRUE)));
	   }
	}
	
	// Delete Payment type
	public function deletePaymentTypeById($request, $response, $args){
		 $id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		// Check if this slider has a picture uploaded.
		$pictureExist = Models\PaymentType::where('id', '=', $id)->first()->payment_logo;
	   if($pictureExist){
		   // Unlink the picture
		   @unlink(PAYMENT_TYPE_ROOT_PATH.'/'.$pictureExist);
	   }
		$delete = Models\PaymentType::find($id)->delete();
		return $response->withJson(json_encode(array("status" => TRUE)));
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
}
