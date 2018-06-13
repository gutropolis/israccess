<?php
namespace App\Controllers;

use App\Models;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v;

/**
  Admin order Controller
  Available Functions.
  1. orders
  2. getAjaxOrdersList
  3. getOrderById
  4. deleteOrderById
 
  
*/

class AdminOrderController extends Base 
{
	
	 // Main function to display all orders list
	public function orders($request, $response) {
        $params = array( 'title' => 'All Orders',
		                 'current_url' => $request->getUri()->getPath());
        return $this->render($response, ADMIN_VIEW.'/Order/orders.twig',$params);
    }
	
	// Ajax Orders list
	public function getAjaxOrdersList($request, $response){
		
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
		    $total   = Models\Order::whereRaw($whereData)->count(); // get count 
		}else{
			$total   = Models\Order::get()->count(); // get count 
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
		    $orders_list = Models\Order::with(['Customer'])->whereRaw($whereData)
			                   ->skip($offset*$perPageLimit)->take($perPageLimit)
							   ->orderBy($field, $sort)->get();
		}else{
			$orders_list = Models\Order::with(['Customer'])
			                   ->skip($offset*$perPageLimit)->take($perPageLimit)
							   ->orderBy($field, $sort)->get();
		}
			
		$data = array();
		foreach($orders_list as $get){
		  	$array_data = array();
			$array_data['id']  = $get->id;
            $array_data['customer_name']  = $get['Customer']['name'];
			$array_data['total_amount']  = $get->total_amount;
            $array_data['payment_type']  = $get->payment_type;
			$array_data['created_on']  =  hr_date($get->created_on);
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
	
	
	// Get Order by id
	public function getOrderById($request, $response, $args){
		$id = $args['id'];
		
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		$order = Models\Order::find($id);
		// Get customer data asssociated to this order
		$customer = Models\User::with(['customerdata'])->where('id','=',$order['customer_id'])->get();
		// Get order detail
		$order_itmes_data = Models\OrderItems::where('order_id','=',$order['id'])->get();
		
		
		$order_itmes_list  = '';
		$sub_total = 0;
		$booking_fee = 0;
		if( !$order_itmes_data->isEmpty()){
		
			foreach($order_itmes_data as $row){
				if($row['type_product'] != 'booking_fees'){
				 $sub_total += $row['seat_qty']*$row['price']*$row['quantity'];
				 $total_seat = $row['seat_qty']*$row['price']*$row['quantity'];
			     $event_name =   Models\Event::where('id', '=', $row['product_id'])->first()->title;
			     $order_itmes_list  .= '<tr>
									<td>'.$event_name.'</td>
									<td>'.$row['ticket_category'].'</td>
									<td>'.$row['ticket_row'].'</td>
									<td class="text-center">'.$row['seat_qty'].'</td>
									<td class="text-center">'.$row['price'].'</td>
									<td class="text-center">'.$row['quantity'].'</td>
									<td class="text-right">'.$total_seat.'</td>
								</tr>';
				}else{
					$booking_fee += $row['price'];
				}
			  	$producer_id = $row['producer_id'];				
			}
			
			
			
		}
		
		if($producer_id == 1){
			$producer_data = Models\User::find($producer_id);
			$product_meta_data = array('full_name' => $producer_data['name'],
			                          'email' => $producer_data['email']);
		}else{
			$producer_data = Models\Productor_meta::where('user_id','=', $producer_id)->get();
			$product_meta_data = array('full_name' => $producer_data[0]['company_name'],
			                          'email' => $producer_data[0]['telephone'].'<br>'.
									  $producer_data[0]['company_number'].'<br>');
		}
		
		//ddump($product_meta_data); exit;
		$total = $sub_total + $booking_fee;
		//ddump($order_itmes_data); exit;
		$order_date = date('F j , Y',  strtotime($order['created_on']));
		$params = array( 'title' => 'Order Detail',
		                'order' => $order,
						'customer_data' => $customer[0]['customerdata'],
						'order_date' => $order_date,
						'items_list' => $order_itmes_list,
						'sub_total' => $sub_total,
						'booking_fee' => $booking_fee,
						'total' => $total,
						'producer_data' => $product_meta_data);
        return $this->render($response, ADMIN_VIEW.'/Order/view.twig',$params);
		
  }
  
  
	
	
	// Delete order by di
	public function deleteOrderById($request, $response, $args){
		$id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		// First Delete all its order items
		//$delete = Models\Order::find($id)->delete();
		return $response->withJson(json_encode(array("status" => TRUE)));
	}
	
	
	
	
}
