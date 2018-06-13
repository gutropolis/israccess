<?php
namespace App\Controllers;
use App\Models;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v;

/**
 Admin Currency Controller
 Available Functions
 1.  currencies
 2.  ajaxCurrencyList
 3.  getCurrenyById
 4.  saveCurrency
 5.  updateCurrency
 6.  deleteCurrencyById

 
*/
class AdminCurrencyController extends Base 
{
	protected $container;
	protected $lang;
	public function __construct($container)
	{
		$this->container = $container;
		$this->lang =  $this->container->view['adminLang'];
	}
	
	// Main function
	public function currencies($request, $response) {
		
        $params = array( 'title' => $this->lang['currency_all_txt'],
		                 'current_url' => $request->getUri()->getPath());
        return $this->render($response, ADMIN_VIEW.'/Currency/currencies.twig',$params);
    }
	
	// Ajax Currencies list
	public function ajaxCurrencyList($request, $response){
		$isSearched = $drpSearch = false; // set to false if user did not search something
		$conditions = array();
		$whereData =  $customSearch = $prefix = '';
		if($request->getParam('query') != null){
			 $isSearched = true; // user has searched something
			 $fields = array('name', 'symbol', 'status');
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
		    $total   = Models\Currency::whereRaw($whereData)->count(); // get count 
		}else{
			$total   = Models\Currency::get()->count(); // get count 
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
		    $categories_list = Models\Currency::whereRaw($whereData)->skip($offset*$perPageLimit)->take($perPageLimit)->orderBy($field, $sort)->get();
		}else{
			$categories_list = Models\Currency::skip($offset*$perPageLimit)->take($perPageLimit)->orderBy($field, $sort)->get();
		}
		
		$data = array();
		foreach($categories_list as $get){
			
			
		  	$array_data = array();
			$array_data['id']  = $get->id;
            $array_data['name']  = $get->name;
            $array_data['symbol']  = $get->symbol;
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
	
	// Get Currency by id
	public function getCurrenyById($request, $response, $args){
		$id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		$city = Models\Currency::find($id);
		if ($city) {
			return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode($city));
        }
		
	}
	
	// Save currency from admin
	public function saveCurrency($request, $response){
		//ddump($_REQUEST);
	   $isError = false;
	   $name = $request->getParam('name');
	   $symbol = $request->getParam('symbol');
	   $status = $request->getParam('status');
	   $currencyExist = Models\Currency::where('name', '=', $name)->first();
	   if( $currencyExist){
		  
		 echo json_encode(array("status" => 'duplicate', 
		                  'message' => $this->lang['currency_name'].' (<strong>'.$name. '</strong>) '.$this->lang['common_already_exist_txt']));
		 exit();	   
	   }else  if( empty($name) ){
		  
		 echo json_encode(array("status" => 'error', 
		                  'message' => $this->lang['currency_name_msg_txt']));
		 exit();	   
	   }else  if( empty($symbol) ){
		  
		 echo json_encode(array("status" => 'error', 
		                  'message' => $this->lang['currency_symbol_msg_txt']));
		 exit();	   
	   }  else{
		   // Save to currencies table
		   $city = new Models\Currency;
		   $city->name = $name;
		   $city->symbol = $symbol;
           $city->status = $status;
		   $city->save();
		   
		   return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode(array('status' => TRUE)));  
	   }
	   
	   
	   
	   
	}
	
	// Update Currency
	public function updateCurrency($request, $response){
		
	   $id   = $request->getParam('id');
	   $name = $request->getParam('name');
	   $symbol = $request->getParam('symbol');
	   $status = $request->getParam('status');
	   $currencyExist = Models\Currency::where('name', '=', $name)->where('id', '!=', $id)->first();
	   if( $currencyExist){
		  
		 echo json_encode(array("status" => 'duplicate', 
		                  'message' => $this->lang['currency_name'].' (<strong>'.$name. '</strong>) '.$this->lang['common_already_exist_txt']));
		 exit();	   
	   }else  if( empty($name) ){
		  
		 echo json_encode(array("status" => 'error', 
		                  'message' => $this->lang['currency_name_msg_txt']));
		 exit();	   
	   }else  if( empty($symbol) ){
		  
		 echo json_encode(array("status" => 'error', 
		                  'message' => $this->lang['currency_symbol_msg_txt']));
		 exit();	   
	   } else{
		   
		   // Save to currencies table
		   $data = array('name' => $name,
		                 'symbol' => $symbol,
						 'status' => $status);
		  $city = Models\Currency::where('id', '=', $id)->update($data);					 
		  return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode(array('status' => TRUE)));
	   }
	}
	
	// Delete Currency
	public function deleteCurrencyById($request, $response, $args){
		 $id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		$delete = Models\Currency::find($id)->delete();
		return $response->withJson(json_encode(array("status" => TRUE)));
		
	}
	
	
	
	
	
	
	
	
	
}
