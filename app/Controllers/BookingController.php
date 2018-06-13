<?php

namespace App\Controllers;
use App\Models\User;
use App\Models\Usermeta;
use App\Models\Event;
use App\Models\EventGrComment;
use App\Models\EventGrFiles;
use App\Models\EventTicket;
use App\Models\Eventgroup;
use App\Models\Category;
use App\Models\Auditorium;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\RowSeats;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v;

 


class BookingController extends BaseController{ 
		
		public $errorPayment=array();
		public $responsePayment=array();
		public $supplier = 'acarragrotok'; // enter your supplier name
		public $tranzilapw = 'Il37LK'; //enter your tranzila pw 
		public $tranzila_api_host = 'https://secure5.tranzila.com';
		public $tranzila_api_path = '/cgi-bin/tranzila71u.cgi';
		public $invoicecode   =array();
		
		public function getBooking($request, $response) {
			   return $this->render($response,  'public/booking/booking.twig',$this->data); 
		}
		
		
		
		
		public function showHtmlPendingOrder($bookingfees,$giftcard,$total_item){
			 $totalAmountTicket='';
			//Add Car$tt System here //
			 $bodyCar .=' <div class="carttable">';
							
							if(isset($_SESSION["cart_products"])){ 
							  
							  if(count($_SESSION["cart_products"])>0){
								foreach($_SESSION["cart_products"] as $cartItem){
									 $bodyCar .='<div id="repeatble" >';
									 $bodyCar .='  <div class="cartaction">
														<ul>
															<li> <input name="deleteItem" type="radio" id="radioButtonContainerId"  onclick="deleteItem('.$cartItem['eventgroup_id'].')"  > </li>
															<li><a href="#"><i class="fas fa-trash-alt"></i></a></li>
														</ul>
													</div>
													<div class="cartimg">
														<a href="#"><img src="'.$cartItem['event_group_picture'].'" ></a>
													</div>
													<div class="cartdetails">
														<div class="cartItem">
															<h2>Evenement</h2>
															<div class="evencon">
																<p>'.$cartItem['event_name'].'</p>
															</div>
														</div>
													     <div class="cartlefu">
																<h2>Lieu   </h2>
																<div class="evencon">
																	<p>'.$cartItem['event_auditorium'].' '.$cartItem['event_city'].' </p>
																</div>
															</div>
															<div class="cartdate">
																<h2>  date </h2>
																<div class="evencon">
																	<p> '.$cartItem['event_date'].'</p>
																</div>
															</div>
															<div class="cartnbre">
																<h2>Nbre de places </h2>
																<div class="evencon">
																	<p>Row '.$cartItem['ticket_row'].' - '.$cartItem['qtx'].' places</p>
																</div>
															</div>
															<div class="cartcat">
																<h2>Categorie</h2>
																<div class="evencon">
																	<P>'.$cartItem['ticket_category'].' </P>
																</div>
															</div>
															<div class="cartprice">
																<h2>Prix/Place</h2>
																<div class="evencon">
																	<p><b>'.$cartItem['price'].' ₪</b></p>
																</div>
															</div>
														 </div>
													 </div> 
													';
									$totalAmountTicket=$totalAmountTicket+($cartItem['price']*$cartItem['qtx']);
									$_SESSION['totalamount']=$totalAmountTicket;
								}
								 
								    
							  } 
					
				 	$bodyCar .=' <div class="actionsection">
                                    <div class="asCon">
                                        <div class="asLT">
                                            <p>MONTANT TOTAL des billets</p>
                                        </div>
                                        <div class="asRT">
                                            <p><b>'.$totalAmountTicket.'₪</b></p>
                                        </div>
                                    </div>
                                    <div class="asCon">
                                        <div class="asLT">
                                            <p>frais de gestion</p>
                                        </div>
                                        <div class="asRT">
                                            <p><b>'.$total_item.'x '.$bookingfees.'₪</b></p>
                                        </div>
                                    </div>
                                     <!--
									<div class="asCon redcol">
                                        <div class="asLT">
                                            <p>montant total de la commande</p>
                                        </div>
                                        <div class="asRT">
                                            <p><b>'.($total_item*$bookingfees+$totalAmountTicket).'₪</b></p>
                                        </div>
                                    </div>
                                   <div class="asCon">
                                        <div class="asLT">
                                            <p>MONTANT CARTE CADEAU</p>
                                        </div>
                                        <div class="asRT">
                                            <p><b>'.$giftcard.'₪</b></p>
                                        </div>
                                    </div>  -->
                                    <div class="asCon blackcol">
                                        <div class="asLT">
                                            <p>RESTE À RÉGLER</p>
                                        </div>';
								//	$net_totalAmount = 	((2*$bookingfees+$totalAmountTicket)-$giftcard);
								$net_totalAmount = 	($total_item*$bookingfees+$totalAmountTicket) ;
										
								$bodyCar .=' 
                                        <div class="asRT">
                                            <p><b>'.$net_totalAmount.'₪</b></p>
                                        </div>
                                    </div>

                                    <a href="'.WEB_PATH.'/validate-cart?step=payment#paymentscroll"  class="commandelink"   ><i class="fas fa-lock"></i> &nbsp;&nbsp;&nbsp; valider ma réservation</a>
                                </div>';	  
							  
							  
							  
							}
				$bodyCar .=' </div>';	
				
				return $bodyCar;
		}
		public function getEventShop($request, $response){
			
			 //print_r($_SESSION['cart_extra']);exit;
			//Array ( [giftcard] => 1 [bookingfees] => 4 [booking_fee_item] => 1 [net_amt] => 68 ) 
			$bookingfees='0';
			$giftcard='0';
			$booking_fee_item='0';
			if(isset($_SESSION['cart_extra'])){
					$bookingfees= $_SESSION['cart_extra']['bookingfees'];
					$giftcard= intval($_SESSION['cart_extra']['giftcard']);
					$total_seat_qtx= $_SESSION['total_seat_qtx'];
					$booking_fee_item=  $total_seat_qtx;;
			}
			
			
			$step		= $request->getParam('step'); 
			$orderstatus		= $request->getParam('status');; 
			$stmsg		= $request->getParam('stmsg');; 
			$bodyCar  =''; 
			$totalAmountTicket='';
			$extraData=array();
			
			 
			//print_r($_SESSION);
			 
			 //Add Car$tt System here //
			 
							
			if(isset($_SESSION["cart_products"])){ 
			  
				  if(count($_SESSION["cart_products"])>0){
					 // print_r($_SESSION["cart_products"]);
					foreach($_SESSION["cart_products"] as $cartItem){
						
						$totalAmountTicket=$totalAmountTicket+(intval($cartItem['price'])* intval($cartItem['qtx']));
						$_SESSION['totalamount']=$totalAmountTicket;
					}    
				  }  
				$net_totalAmount = 	($booking_fee_item*$bookingfees+$totalAmountTicket) ;   
			} 			
			//$extraData['net_amt'] = $net_totalAmount;		 
                                
                         
			 //$_SESSION['cart_extra']=$extraData;
			 
			 //End here //
 
				
			$tag=3;
						
		    $this->data['tag'] = $tag;
			//echo 'Booking Free:'.$booking_fee_item;exit;
			$this->data['cartItems'] = $this->showHtmlPendingOrder($bookingfees,$giftcard,$booking_fee_item);
			$this->data['step'] = $step;
			$this->data['ordstatus'] = $orderstatus;
			$this->data['stmsg'] = $stmsg;
			 
			$this->data['base_url'] =  WEB_PATH;
			 
            $this->data['tab'] = $tab;   
			 $this->data['countryList'] = $this->getAllCountry();    
			 //print_r($this->data['countryList']);
		      
			return $this->render($response,  'public/booking/booking.twig',$this->data); 
			 
			
		}
		public function addEventTicketCart($request, $response){
			//unset($_SESSION["isMember"]);
			//exit;
			if(isset($_SESSION["cart_products"])){
						unset($_SESSION["cart_products"]);
			}			
			$extraData=array();
			$bookingfees='1';
			$giftcard='1';
			
			//$extraData['bookingfees'] = $bookingfees;
			$extraData['giftcard'] = $giftcard;
		    
			$ticket_type		= $request->getParam('ticket_type'); 
			$eventIds = $request->getParam('event');                 //Event Id
			$eventPriceArr = $request->getParam('ticket_price');     //Ticket Price
			$eventTicketType = $request->getParam('ticket_type');    //Ticket Category like left,right
			$ticket_type_ids = $request->getParam('ticket_type_id');    //Ticket Category like left,right
			 
			$eventQtyArr = $request->getParam('seat_qty');         //Quantity of seat have booked
			//$eventSeatQt = $request->getParam('seat_qty');          //Seat Quantity
			$eventTicketArr = $request->getParam('ticket_rows');//print_r($eventTicketArr);
			$evtime = $request->getParam('evtime');                 //Event Time
			$totalAvailableTicket  = $request->getParam('totalavailabletkthdn');       //Total Available Seats
			$seat_number  = $request->getParam('seat_number'); 
			
            /*
			[event]
			[evtime]
			[ticket_price]
			[ticket_type]
			[ticket_rows]
			[totalavailabletkthdn]
			[seat_qty] */

			$countT= count($ticket_type);
			
			if($countT >0){
				$j=0;
				for($i=0;$i<$countT; $i++){
					
					$qtx= $eventQtyArr[$i]; 
					
					if(intval($qtx) > 0 ){  
					$j=$j+1;
								$dataShop = array();
								$event_id= $eventIds[$i]; 
							 
								$eventArr = Event::where('id',$event_id)->get();
								$event_list = getEventList($eventArr); 
								 
								foreach($event_list  as $ev){
									$dataShop['event_name'] = $ev['title'];
									$dataShop['event_ticket_type'] = $ev['event_ticket_type'];
									//Add booking fees here //
									$dataShop['booking_fee'] = $ev['booking_fee'];
									$extraData['bookingfees'] =  $ev['booking_fee'];
									$bookingfees = $ev['booking_fee'];;
									
									$dataShop['eventgroup_id'] = $ev['eventgroup_id'];
									
									
									$dataShop['artist_name'] = $ev['artist_name'];
									$dataShop['artist_name'] = $ev['artist_name'];
									$dataShop['event_auditorium'] = $ev['event_auditorium'];
									$dataShop['event_auditorium_address'] = $ev['event_auditorium_address'];
									
									$dataShop['event_city'] = $ev['event_city'];  	
									$dataShop['event_group_picture'] = EVENTGROUP_WEB_PATH.'/'.$ev['event_group_picture'];  
									$dataShop['event_date'] =  $ev['date_d'].'/'.$ev['date_m'].'/'.$ev['date_Y'];  
									
								}			
								$dataShop['event_id'] = $event_id;  
								$rowseat=$eventTicketArr[$i]; 
								    
									$a=explode("-",$rowseat); 
									$ticket_row = $a[1];
									$ticket_row_id = $a[0];
									$bookingSeatQtx=$eventQtyArr[$i];  
									$booking_time = $evtime[$i];
									$total_available_seat = $totalAvailableTicket[$i];
											 
			                        $ticket_type_id = $ticket_type_ids[$i];
									$price= $eventPriceArr[$i];
									$ticket_category= $eventTicketType[$i]; 


									//Add New Field 
									$seat_number_display= $seat_number[$i]; 
								 


									$dataShop['qtx'] = $qtx;
									$dataShop['event_id'] = $event_id;
									$dataShop['price'] = $price; 
									$dataShop['ticket_type'] = $ev['event_ticket_type'];;  //Type of ticket ->eticket, free placement 
						            $dataShop['ticket_category'] = $ticket_category; 		//eg cate1, cat2	
									//$dataShop['ticket_seat'] = $seat_number;    //
									
									$dataShop['seat_qty'] = $bookingSeatQtx; 		//Total Seat Quantity Booked 
									$dataShop['ticketuid'] = $event_id.$ticket_type_id."-".$ticket_category; 	
									$dataShop['ticket_type_id'] =$ticket_type_id;
									 
							        $dataShop['total_amount'] = $qtx*$price;     // Total Amount
									$dataShop['booking_time'] = $booking_time;   //Booking Time
									$dataShop['total_available_seat'] = $total_available_seat;

									$dataShop['seat_number'] = $seat_number_display;  //Display Seat Number
									$dataShop['ticket_row'] = $ticket_row;  //Ticket Row ID
									$dataShop['ticket_row_id'] = $ticket_row_id;  //Ticket Row ID
									
								 
			 

					 
								if(isset($_SESSION["cart_products"])){  
								   
									//if session var already exist
									if(isset($_SESSION["cart_products"][$dataShop['ticketuid'] ]  )) //check item exist in products array
									{
										unset($_SESSION["cart_products"][$dataShop['ticketuid']]); //unset old array item
									}          
								}
								$_SESSION["cart_products"][$dataShop['ticketuid']] = $dataShop;   	
					}
					 
				}
				$extraData['booking_fee_item'] =  $j;
				 
			}
			
 			  
			 $bodyCar  =''; 
			 $totalAmountTicket='';
			 $total_quantity_seat='0';
			 //Add Car$tt System here //
			 $bodyCar .=' <div class="carttable">';
							
							if(isset($_SESSION["cart_products"])){ 
							  
							  if(count($_SESSION["cart_products"])>0){
								$itemCount =   count($_SESSION["cart_products"]);
								foreach($_SESSION["cart_products"] as $cartItem){
									
									 $bodyCar .='<div id="repeatble" >';
									 $bodyCar .='  <div class="cartaction">
														<ul>
															<li><input name="deleteItem" type="radio" id="radioButtonContainerId"  onclick="deleteItem('.$cartItem['eventgroup_id'].')"  ></li>
															<li><a href="#"><i class="fas fa-trash-alt"></i></a></li>
														</ul>
													</div>
													<div class="cartimg">
														<a href="#"><img src="'.$cartItem['event_group_picture'].'" ></a>
													</div>
													<div class="cartdetails">
														<div class="cartItem">
															<h2>Evenement</h2>
															<div class="evencon">
																<p>'.$cartItem['event_name'].'</p>
															</div>
														</div>
													     <div class="cartlefu">
																<h2>Lieu   </h2>
																<div class="evencon">
																	<p>'.$cartItem['event_auditorium'].' '.$cartItem['event_city'].' </p>
																</div>
															</div>
															<div class="cartdate">
																<h2>  date </h2>
																<div class="evencon">
																	<p> '.$cartItem['event_date'].'</p>
																</div>
															</div>
															<div class="cartnbre">
																<h2>Nbre de places </h2>
																<div class="evencon">
																<p>Row '.$cartItem['ticket_row'].' - '.$cartItem['qtx'].' places</p>
																</div>
															</div>
															<div class="cartcat">
																<h2>Categorie</h2>
																<div class="evencon">
																	<P>'.$cartItem['ticket_category'].' </P>
																</div>
															</div>
															<div class="cartprice">
																<h2>Prix/Place</h2>
																<div class="evencon">
																	<p><b>'.$cartItem['price'].' ₪</b></p>
																</div>
															</div>
														 </div>
													 </div> 
													';
													$total_quantity_seat=$total_quantity_seat+intval($cartItem['qtx']);
									$totalAmountTicket=$totalAmountTicket+($cartItem['price']*$cartItem['qtx']);
									$_SESSION['totalamount']=$totalAmountTicket;
									$_SESSION['total_seat_qtx']=$total_quantity_seat;
								}
								 
								    
							  } 
					
				 	$bodyCar .=' <div class="actionsection">
                                    <div class="asCon">
                                        <div class="asLT">
                                            <p>MONTANT TOTAL des billets</p>
                                        </div>
                                        <div class="asRT">
                                            <p><b>'.$totalAmountTicket.'₪</b></p>
                                        </div>
                                    </div>
                                    <div class="asCon">
                                        <div class="asLT">
                                            <p>frais de gestion</p>
                                        </div>
                                        <div class="asRT">
                                            <p><b>'.$total_quantity_seat.'x '.$bookingfees.'₪</b></p>
                                        </div>
                                    </div>
                                     <!--
									<div class="asCon redcol">
                                        <div class="asLT">
                                            <p>montant total de la commande</p>
                                        </div>
                                        <div class="asRT">
                                            <p><b>'.($total_quantity_seat*$bookingfees+$totalAmountTicket).'₪</b></p>
                                        </div>
                                    </div>
                                   <div class="asCon">
                                        <div class="asLT">
                                            <p>MONTANT CARTE CADEAU</p>
                                        </div>
                                        <div class="asRT">
                                            <p><b>'.$giftcard.'₪</b></p>
                                        </div>
                                    </div>  -->
                                    <div class="asCon blackcol">
                                        <div class="asLT">
                                            <p>RESTE À RÉGLER</p>
                                        </div>';
								//	$net_totalAmount = 	((2*$bookingfees+$totalAmountTicket)-$giftcard);
								$net_totalAmount = 	($total_quantity_seat*$bookingfees+$totalAmountTicket) ;
										
								$bodyCar .=' 
                                        <div class="asRT">
                                            <p><b>'.$net_totalAmount.'₪</b></p>
                                        </div>
                                    </div>';
									if(isset($_SESSION['memberId']) && intval($_SESSION['memberId']) >0  ){
									   $bodyCar .='<a href="'.WEB_PATH.'/validate-cart?step=payment"  class="commandelink"   ><i class="fas fa-lock"></i> &nbsp;&nbsp;&nbsp; valider ma réservation</a>';
                                 
								   }else{
									   $bodyCar .='<a href="'.WEB_PATH.'/validate-cart?step=login"  class="commandelink"   ><i class="fas fa-lock"></i> &nbsp;&nbsp;&nbsp; valider ma réservation</a>';
                                	   
								   }
                                    
                                $bodyCar .=' </div>';  
							  	  
							  
							  
							  
							}else{
							//Add Empty Order Informaton here //
				$bodyCar .=' <div role="tabpanel" class="tab-pane fade active show" id="Confirmation">
								<h3>VOTRE PANIER EST VIDE</h3>
								<a href="/"  ><h5>  CONTINUEZ VOTRE VISTE </h5></a>
								 
							</div>';
							}
							
							
							//End here //
				$bodyCar .=' </div>';		   
										
						$extraData['net_amt'] = $net_totalAmount;		 
                                
                         
			 $_SESSION['cart_extra']=$extraData;
			 
			 //End here //
			 //print_r($_REQUEST);
			 //print_r($_SESSION["cart_products"]);
			 $orderhtml=$this->getOrderDetail(); 
				
					
		 
			
			
				
					$tag=3;
						
					$this->data['tag'] = $tag;
					$this->data['cartItems'] = $bodyCar;
					$this->data['orderDetail'] = $orderhtml;
					$this->data['countryList'] = $this->getAllCountry();   
					 $this->data['h1'] = 'mon-panier';
					//print_r($this->data['countryList']);
					return $this->render($response,  'public/booking/booking.twig',$this->data); 
			 
			
			  
		}
		public function testOrder($request, $response){
			 $order_id=$request->getAttribute('order'); 
			  $userid=$request->getAttribute('user'); 
			//$order_id='18';
			//$userid='1018';
			$this->sendTicketInformationEmail( $order_id,$userid);
		}
		
		
		
		public function addCartOrderV22($request, $response){
			//use App\Models\Order;
			//use App\Models\OrderItems;
			
			$data=array(); 
		 
			if(isset($_SESSION['memberId']) && intval($_SESSION['memberId']) >0  ){
				
				
				if(  isset($_SESSION["cart_products"]) && count($_SESSION["cart_products"])> 0 ){
							 $userid=$_SESSION['memberId'];  
			        
			
					    if(isset($_SESSION["cart_extra"])){ 
								
										$cartvalue		= $request->getParam('cartvalue'); 
										$cartExpM		= $request->getParam('cartExpM'); 
										$cartExpY		= $request->getParam('cartExpY');
										$cartExpY 		=substr($cartExpY,2,4)	;  					
										$cvv		= $request->getParam('cvv'); 
										$cette		= $request->getParam('cette');

								 //$totalAmt = $_SESSION["cart_extra"]['net_amt'];
										 
										 
								$totalAmt = '1';
								$data['expiration_date'] = $cartExpM.'/'.$cartExpY;
								$data['total_amount'] = $totalAmt;
								$data['currency'] = '1';
								$data['credit_card_number'] = $cartvalue;
								 
								$data['expiration_Year'] =$cartExpY;
								$data['mycvv'] =$cvv;
								$data['id_number'] ='01';
								
								$ip_Addresss = $this->get_client_ip();
								$data['ipaddress'] = $ip_Addresss;
								$data['orderid'] = '2323232';
								$paymentID='0';
								
								//$token = $this->donfo_create_token($data );
							    
                                //Token Start Here //
								$token ='abc';
								//Remove
										$paymentID= '38232932923';
										 $order_id =  $this->confirmOrder($paymentID);
										//$order_id='18';
										
										if(intval($order_id) >0){
											
											 $this->sendTicketInformationEmail( $order_id,$userid);
										}
										$args =array('step'=> 'confirm_order','status'=> '1');
                                         //unset($_SESSION["cart_products"]);
										 return $response->withRedirect($this->router->pathFor('confirmbooking',[],$args));
										 exit;
								
								if ($token !=''){
									//if there is a token - make a tranaction
									 $this->donfo_transaction($data ,$token);
									 
									 
									 
									 if($this->responsePayment['tranStatus'] =='1' ){
										 $paymentID= $this->responsePayment['transation_id'];
										$order_id =  $this->confirmOrder($paymentID);
										if(intval($order_id) >0){
											 $this->sendTicketInformationEmail( $order_id,$userid);
										}
										 
										$tag=5;
										$this->data['tag']=$tag;
										$this->data['orderDetail']=$this->getOrderDetail();
										//print_r($this->data['orderDetail']);
										//exit;
										$args =array('step'=> 'confirm_order','status'=> '1');
                                         //unset($_SESSION["cart_products"]);
										 return $response->withRedirect($this->router->pathFor('confirmbooking',[],$args));
									 } else{
										 $args =array('step'=> 'confirm_order','status'=> '0'); 
										 return $response->withRedirect($this->router->pathFor('confirmbooking',[],$args));
										
									 }
									 
									 
								}else{
										$args =array('step'=> 'payment','status'=> '0','stmsg'=>"Le jeton n'est pas valide. Veuillez contacter votre administration"); 
										 return $response->withRedirect($this->router->pathFor('confirmbooking',[],$args));
									
									  
								}
						 		 							
						}				 
			    }else{
										$args =array('step'=> 'payment','status'=> '0','stmsg'=>'Premier article dans votre panier'); 
										 return $response->withRedirect($this->router->pathFor('confirmbooking',[],$args));
				} 
								
								 
			}
			
			 	 
			
			
		}
		public function addCartOrder($request, $response){
			//use App\Models\Order;
			//use App\Models\OrderItems;
			
			$data=array(); 
		 
			if(isset($_SESSION['memberId']) && intval($_SESSION['memberId']) >0  ){
				
				
				if(  isset($_SESSION["cart_products"]) && count($_SESSION["cart_products"])> 0 ){
							 $userid=$_SESSION['memberId'];  
			        
			
					    if(isset($_SESSION["cart_extra"])){ 
								
										$cartvalue		= $request->getParam('cartvalue'); 
										$cartExpM		= $request->getParam('cartExpM'); 
										$cartExpY		= $request->getParam('cartExpY');
										$cartExpY 		=substr($cartExpY,2,4)	;  					
										$cvv		= $request->getParam('cvv'); 
										$cette		= $request->getParam('cette');

								 //$totalAmt = $_SESSION["cart_extra"]['net_amt'];
										 
										 
								$totalAmt = '1';
								$data['expiration_date'] = $cartExpM.'/'.$cartExpY;
								$data['total_amount'] = $totalAmt;
								$data['currency'] = '1';
								$data['credit_card_number'] = $cartvalue;
								 
								$data['expiration_Year'] =$cartExpY;
								$data['mycvv'] =$cvv;
								$data['id_number'] ='01';
								
								$ip_Addresss = $this->get_client_ip();
								$data['ipaddress'] = $ip_Addresss;
								$data['orderid'] = '2323232';
								$paymentID='0';
								
								$token = $this->donfo_create_token($data );
							    
                                //Token Start Here //
								 
								//Remove

								
								if ($token !=''){
									//if there is a token - make a tranaction
									 $this->donfo_transaction($data ,$token);
									 
									 
									 
									 if($this->responsePayment['tranStatus'] =='1' ){
										 $paymentID= $this->responsePayment['transation_id'];
										$order_id =  $this->confirmOrder($paymentID);
										if(intval($order_id) >0){
											 $this->sendTicketInformationEmail( $order_id,$userid);
										}
										 
										$tag=5;
										$this->data['tag']=$tag;
										$this->data['orderDetail']=$this->getOrderDetail();
										//print_r($this->data['orderDetail']);
										//exit;
										$args =array('step'=> 'confirm_order','status'=> '1');
                                         unset($_SESSION["cart_products"]);
										 return $response->withRedirect($this->router->pathFor('confirmbooking',[],$args));
									 } else{
										 $args =array('step'=> 'confirm_order','status'=> '0'); 
										 return $response->withRedirect($this->router->pathFor('confirmbooking',[],$args));
										
									 }
									 
									 
								}else{
										$args =array('step'=> 'payment','status'=> '0','stmsg'=>"Le jeton n'est pas valide. Veuillez contacter votre administration"); 
										 return $response->withRedirect($this->router->pathFor('confirmbooking',[],$args));
									
									  
								}
						 		 							
						}				 
			    }else{
										$args =array('step'=> 'payment','status'=> '0','stmsg'=>'Premier article dans votre panier'); 
										 return $response->withRedirect($this->router->pathFor('confirmbooking',[],$args));
				} 
								
								 
			}
			
			 	 
			
			
		}
		
		public function confirmOrder($paymentID){
			 
								$order = new Order();
								$userid=$_SESSION['memberId'];  
								$order->customer_id = $userid; 
								$order->payment_type = 'Tranzilla';
								$order->created_on = date('Y-m-d h:i:s'); 
								$totalAmt = $_SESSION["cart_extra"]['net_amt'];
								$order->total_amount =  $totalAmt;
								$order->payment_id =  $paymentID;
								$order->save(); 
								if($order->id > 0){
									$invoice = intval('18000');
									$invoice_id = $invoice+$order->id;
									$invoice_number='FA'.$invoice_id;
									$reservation_number='CA'.$invoice_id;
									$xxx = intval('74444');
									$xxx = intval($xxx)+$userid;
									$customer_number='CANO'.$xxx.'XX' ;

									$this->invoicecode['invoice_number']=$invoice_number;
									$this->invoicecode['reservation_number']=$reservation_number;
									$this->invoicecode['customer_number']=$customer_number;
									
									Order::where('id', $order->id)->update(['invoice_number' => $invoice_number]);
								} 
								if(isset($_SESSION["cart_products"])){ 
								
								$itemCount= count($_SESSION["cart_products"]);
			  
									  if(count($_SESSION["cart_products"])>0){

											foreach($_SESSION["cart_products"] as $cartItem){	
											   $orderitem = new OrderItems();
												$orderitem->order_id = $order->id; 
												$orderitem->product_id = $cartItem['event_id']; 
												$orderitem->quantity =  $cartItem['qtx']; 
												$orderitem->price =  $cartItem['price']; 
												$orderitem->type_product = 'event'; 
												$orderitem->ticket_category = $cartItem['ticket_category']; 
												
												$orderitem->ticket_row = $cartItem['ticket_row'];
												$orderitem->seat_qty = $cartItem['seat_qty'];
												$orderitem->booking_time = $cartItem['booking_time'];
												$orderitem->event_ticket_category_id = $cartItem['ticket_type_id'];
												$orderitem->available_seats = $cartItem['total_available_seat'];
												$orderitem->ticket_type = $cartItem['ticket_type']; 
												$orderitem->created_on = date('Y-m-d h:i:s'); 
												$orderitem->ticket_row_id =   $cartItem['ticket_row_id'];
												
												$orderitem->save(); //echo 'event'.$orderitem->id;
													$table_pk_id = $cartItem['ticket_type_id'];
													$booked_seats_quantity = $cartItem['qtx'];
													$row_id =  $cartItem['ticket_row_id'];
													 
													update_row_Quantity($row_id,$booked_seats_quantity ); 
												 	  	 
											}

									  }
									  if(isset($_SESSION["cart_extra"]['bookingfees'])){
										  $orderitem = new OrderItems();
												$orderitem->order_id = $order->id; 
												$orderitem->product_id = '0'; 
												$total_seat_qtx= $_SESSION['total_seat_qtx'];
												$orderitem->quantity =  $total_seat_qtx; 
												$orderitem->created_on = date('Y-m-d h:i:s');
												$orderitem->price =  $_SESSION["cart_extra"]['bookingfees']; 

												$orderitem->type_product = 'booking_fees'; 
												$orderitem->ticket_type = ''; 
												
												$orderitem->save();  echo 'bookingfees'.$orderitem->id;
									  }
									  
									  
									   /*
									   if(isset($_SESSION["cart_extra"]['giftcard'])){
										  $orderitem = new OrderItems();
												$orderitem->order_id = $order->id; 
												$orderitem->product_id = '0'; 
												$orderitem->quantity =  '1'; 
												$orderitem->created_on = date('Y-m-d h:i:s'); 
												$orderitem->price =  $_SESSION["cart_extra"]['giftcard']; 
												
												$orderitem->type_product = 'giftcard'; 
												$orderitem->ticket_type = ''; 
												
												$orderitem->save(); echo 'giftcard'.$orderitem->id;
									  }
									  */
									   
											 
								}
								
								
								
								
								return intval($order->id);
		}
		
		public function getOrderData($order_id){
			
			
			$orderItemHtm='';
			$ordersArr=array();
		    if($order_id > 0){		
						/* Get order Items Detail here */
						
							$extraData=array();
							$bookingfees='1';
							$giftcard='1';
							
							 $orderEvents = OrderItems::select('product_id')->where('order_id',$order_id)->distinct()->get();
							  
							 if(count($orderEvents) > 0 ){
								$dataShop=array();
								$dataShop['type'] ='event';
								$total_ticket_price =0;
								 foreach($orderEvents  as $item){
									 
								    $event_id = $item['product_id'];
									if(intval($event_id)> 0){  
									 //Event Detail have Added Here //
										$eventArr = Event::where('id',$event_id)->get();
										 
										$event_list = getEventList($eventArr); 
										
										foreach($event_list  as $ev){
											
											$middleFormat = strtotime($ev['date']);  
												 
										    $dataShop['time_range']  =date('H:i', $middleFormat);
											//$dataShop['type'] ='event';
											$dataShop['order_id'] ='IXXI-'.$order_id;
											$dataShop['event_name'] = $ev['title'];
											$dataShop['artist_name'] = $ev['artist_name'];
											$dataShop['event_auditorium'] = $ev['event_auditorium'];
											$dataShop['event_auditorium_address'] = $ev['event_auditorium_address'];
											$dataShop['event_ticket_type'] = $ev['event_ticket_type'];
											$dataShop['productor_name'] = $ev['productor_name'];
											$dataShop['artist_name'] = $ev['artist_name'];
											$dataShop['event_city'] = $ev['event_city'];  	
											$dataShop['event_group_picture'] = EVENTGROUP_WEB_PATH.'/'.$ev['event_group_picture'];  
											$dataShop['event_date'] =  $ev['date_d'].'/'.$ev['date_m'];  
											$dataShop['event_date_com'] =  date('Y-m-d', $middleFormat); 
											
										}
										 
										/*=========Add Ticket row, Price, others ==========*/
										 
												$rowsItems = OrderItems::where('order_id',$order_id)->where('product_id', $event_id)->get();
												 $totalQuantity =0;
												 if(count($rowsItems) > 0 ){ 
												  $rowArr=array();
													foreach($rowsItems  as $item){
													
														$rowArr['ticket_row']=$item['ticket_row'];
														$qtx =$item['quantity'];;
														$price =$item['price'];;
														$rowArr['quantity']=$item['quantity'];
														$totalQuantity += intval($item['quantity']);
														$rowArr['price']=$item['price'];
														$rowArr['ticket_row']=$item['ticket_row'];
														$rowArr['ticket_category'] = $item['ticket_category']; 
														$dataShop['booking_time'] = $item['booking_time'];
														
														$totalA = $qtx*$price;
													 
														$total_ticket_price =$total_ticket_price +$totalA;
														 
														$dataShop['ticket_info'][] =$rowArr;
														
													}
												 }
												 $dataShop['total_seats'] = $totalQuantity;
										
										/* ===========End here ===========================*/
										 	
									}	
										
								 } 	 
							 }
							 /*=========Fetch Other Item Price ==========*/
										$rowsItems = OrderItems::where('order_id',$order_id)->where('product_id','0')->get();
										if(count($rowsItems) > 0 ){ 
										  $rowArr=array();
											foreach($rowsItems  as $item){
												
											 
												$qtx =$item['quantity'];;
												$price =$item['price'];;
												 
												$totalA = $qtx*$price;
												
												$total_ticket_price =$total_ticket_price +$totalA;
												 
												 
												
											}
										 }
										 $dataShop['total_amount'] = $total_ticket_price;
							  /*================End here =======================*/
							 
							  
						  /* End here       */
						  
						$ordersArr[] = $dataShop;
				 }
			 
		 
		  return $ordersArr;
			
		}
		public function sendTicketInformationEmail( $order_id,$userid){
			
				$msgArra= array(); 
				$msgArra['invoice_number'] = $this->invoicecode['invoice_number'];
				$msgArra['reservation_number'] = $this->invoicecode['reservation_number'];
				$msgArra['customer_number'] = $this->invoicecode['customer_number']; 
				
				$orderData = $this->getOrderData($order_id);
				 
				if(count($orderData) > 0){
						 foreach($orderData as $array_data){
										$msgArra['auditorium_name'] = $array_data['event_auditorium']; 
										 $msgArra['auditorium_city'] = $array_data['event_city']; 
										 $msgArra['productor_name'] = $array_data['productor_name'];   
										 $msgArra['artist_name'] =  $array_data['artist_name'];  
										 $msgArra['event_name'] = $array_data['event_name']; 
										 
										 $msgArra['event_date'] = $array_data['event_date']; 
										 $msgArra['event_hour'] = $array_data['booking_time'];;
										  
										 
										 
										 $msgArra['total_price'] = $array_data['total_amount'];
										 
										 
										 $msgArra['number_of_order'] = $msgArra['reservation_number'];
										  $msgArra['total_seats'] = $array_data['total_seats'];
										 
										
									     $userid=$_SESSION['memberId'];  

										 $userInfo = User::where('id',$userid)->first();
										 $userMeta = Usermeta::where('user_id',$userid)->first();  
						 
										 
										 $msgArra['client_nom'] = $userInfo['name'];
										 $email = $userInfo['email'];
										 $msgArra['client_address'] = $userMeta['address_1'];
										 $msgArra['client_city'] = $userMeta['address_1'];
										 $msgArra['client_code'] = $userMeta['ville'];
										 $msgArra['client_country'] = $userMeta['country'];
										 $msgArra['client_id'] = $msgArra['customer_number']; 
										 $msgArra['site_url'] = WEB_PATH;  
										 $ticketSeatArr = $array_data['ticket_info'];
										  $ticketType = $array_data['event_ticket_type'];
									     // $ticketType = '3';
										 $eventSt='';
										 $sidecatbar='';
										 
										 if(count($ticketSeatArr)){
												foreach($ticketSeatArr as $ticket){
													
													$sidecatbar .='<tr>
																		<td colspan="2" style="font-size: 12px; color: #313132; padding: 11px 0 ;font-family: \'Roboto Condensed\', sans-serif;">
																			<p style="margin:0;">'.$ticket['ticket_category'].' </p>
																			<p style="margin:0;">Rang '.$ticket['ticket_row'].' place '.$ticket['quantity'].' </p>
																		</td>
																	</tr>';
																	  if($ticketType=='1'|| $ticketType=='2'){  //For Free placement
																			 $eventSt.=' <tr>
																							<td style="padding-left: 17px; font-family: \'Roboto Condensed\', sans-serif;" width="33%">
																								<table width="100%" cellspacing="0" cellpadding="0">
																									<tbody><tr>
																										<td style="font-size: 10px; text-align: center; font-family: \'Roboto Condensed\', sans-serif;">EMPLACEMENT</td>
																									</tr>
																									<tr>
																										<td style="padding: 5px 0; text-align: center; font-family: \'Roboto Condensed\', sans-serif; border: 2px solid #313132;">
																											
																												'.$ticket['ticket_category'].'
																											
																										</td>
																									</tr>
																								</tbody></table>
																							</td>
																							<td style="padding-left: 30px; font-family:  \'Roboto Condensed\', sans-serif;" width="20%">
																								<table width="100%" cellspacing="0" cellpadding="0">
																									<tbody><tr>
																										<td style="font-size: 10px; text-align: center; font-family:  \'Roboto Condensed\', sans-serif;">RANG</td>
																									</tr>
																									<tr>
																										<td style="padding: 5px 0; text-align: center; font-family:  \'Roboto Condensed\', sans-serif; border: 2px solid #313132;">
																											
																												'.$ticket['ticket_row'].'
																										
																										</td>
																									</tr>
																								</tbody></table>
																							</td>
																							<td style="padding-left: 35px; font-family:  \'Roboto Condensed\', sans-serif;" width="20%">
																								<table width="100%" cellspacing="0" cellpadding="0">
																									<tbody><tr>
																										<td style="font-size: 10px; text-align: center; font-family:  \'Roboto Condensed\', sans-serif;">PLACE</td>
																									</tr>
																									<tr>
																										<td style="padding: 5px 0; text-align: center; font-family:  \'Roboto Condensed\', sans-serif; border: 2px solid #313132;">
																											
																												'.$ticket['quantity'].'
																											
																										</td>
																									</tr>
																								</tbody></table>
																							</td>
																							<td width="30%">
																							<table width="100%" cellspacing="0" cellpadding="0">
																							<tbody><tr><td style="padding:10px;"><img src="'.WEB_PATH.'/emailtemplate/img/barcode.jpg" style="width:100px;"></td></tr>
																						</tbody></table>
																						</td>
																						</tr>';
																	  }else{
																		  $eventSt.='<tr>
																							<td style="padding-left: 17px; font-family: \'Roboto Condensed\', sans-serif;" width="33%">
																								<table width="100%" cellspacing="0" cellpadding="0">
																									<tbody><tr>
																										<td style="font-size: 10px; text-align: center; font-family:\'Roboto Condensed\', sans-serif;">EMPLACEMENT</td>
																									</tr>
																									<tr>
																										<td style="padding: 5px 0; text-align: center; font-family:\'Roboto Condensed\', sans-serif; border: 2px solid #313132;">
																											
																												'.$ticket['ticket_category'].'
																											
																										</td>
																									</tr>
																								</tbody></table>
																							</td>
																							<td style="padding-left: 30px; font-family: \'Roboto Condensed\', sans-serif;" width="33%">
																								<table width="100%" cellspacing="0" cellpadding="0">
																									<tbody><tr>
																										<td style="font-size: 10px; text-align: center; font-family: \'Roboto Condensed\', sans-serif;">RANG</td>
																									</tr>
																									<tr>
																										<td style="padding: 5px 0; text-align: center; font-family: \'Roboto Condensed\', sans-serif; border: 2px solid #313132;">
																											
																												'.$ticket['ticket_row'].'
																										
																										</td>
																									</tr>
																								</tbody></table>
																							</td>
																							<td style="padding-left: 35px; font-family: \'Roboto Condensed\', sans-serif;" width="34%">
																								<table width="100%" cellspacing="0" cellpadding="0">
																									<tbody><tr>
																										<td style="font-size: 10px; text-align: center; font-family: \'Roboto Condensed\', sans-serif;">PLACE</td>
																									</tr>
																									<tr>
																										<td style="padding: 5px 0; text-align: center; font-family: \'Roboto Condensed\', sans-serif; border: 2px solid #313132;">
																											
																												'.$ticket['quantity'].'
																											
																										</td>
																									</tr>
																								</tbody></table>
																							</td>
																						</tr>';
																	  }
																		
													
														}
												 }
										 $msgArra['seat_management'] = $eventSt;
										 $msgArra['seat_management_sidebar'] = $sidecatbar;
										 
										   $subject ='Order confirmation from CultureAccess';
										   sendEmail('',$email,$subject,$msgArra, 'confirmation-inscription.html');
										   
										  
										  if($ticketType=='1'){  //For Free placement 
										     	$msgArra['ticket_type'] = $ticketType; 
											    $subject ='CultureAccess Free Placement Ticket Information';
											   sendEmail('',$email,$subject,$msgArra, 'confirm-free-placement.html');
										  }elseif($ticketType=='2'){   //With E-TICKET
										  	$msgArra['ticket_type'] = $ticketType; 
										         $subject ='CultureAccess E-TICKET Information';
											   sendEmail('',$email,$subject,$msgArra, 'confirmation-eticket.html');
										  }elseif($ticketType=='3'){                 //With countermark
										  	$msgArra['ticket_type'] = $ticketType; 
										       $subject ='CultureAccess Countermark Information';
											   sendEmail('',$email,$subject,$msgArra, 'confirmation-countermark.html');
										  }else{
											  
										  }
										 
										 //sendEmail('',$email,$subject,$msgArra, 'confirmation-ticket.html');
						 }
				}
				 

				//confirm ticket functionality
											 
						  
		}
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		/* Confirm the order here  */
		public function getOrderDetail(){
			$extraData=array();
			$bookingfees='1';
			$giftcard='1';
			
			$order_id='1';
			$orderArr=array();
			$OrderItems = OrderItems::where('order_id',$order_id)->get();
			
			foreach($OrderItems  as $item){
						$dataShop = array();
						 
						$quantity = $item['quantity'];
						$price = $item['price'];
						$event_id = $item['product_id'];
						$type_product = $item['type_product'];
						$ticket_type = $item['ticket_type'];
						$qtx = $item['quantity'];
						if($type_product =='event'){ 
									 
													$eventArr = Event::where('id',$event_id)->get();
													$event_list = getEventList($eventArr); 
													 
													foreach($event_list  as $ev){
														$dataShop['type'] ='event';
														$dataShop['event_name'] = $ev['title'];
														$dataShop['artist_name'] = $ev['artist_name'];
														$dataShop['event_auditorium'] = $ev['event_auditorium'];
														$dataShop['event_auditorium_address'] = $ev['event_auditorium_address'];
														
														$dataShop['event_city'] = $ev['event_city'];  	
														$dataShop['event_group_picture'] = EVENTGROUP_WEB_PATH.'/'.$ev['event_group_picture'];  
														$dataShop['event_date'] =  $ev['date_d'].'/'.$ev['date_m'].'/'.$ev['date_Y'];  
														
													}

														$dataShop['event_id'] = $event_id;  									
														$price= $price;
														 
														$dataShop['qtx'] = $qtx;
														$dataShop['event_id'] = $event_id;
														$dataShop['price'] = $price;
														$dataShop['ticket_type'] = $item['ticket_type']; 		
														$dataShop['ticketuid'] = $event_id."-".$ticket_type; 	
														$dataShop['total_amount'] = $qtx*$price;								
										
										
								 
						
											
						
					}
					else{
												 $dataShop['type'] = $type_product;
 
														$dataShop['event_id'] = $event_id;  									
														 
														 
														$dataShop['qtx'] = $qtx;
														$dataShop['event_id'] = $event_id;
														$dataShop['price'] = $price;
														$dataShop['ticket_type'] = $item['ticket_type']; 		
														$dataShop['ticketuid'] = $event_id."-".$ticket_type; 	
														$dataShop['total_amount'] = $qtx*$price;								
										
										
								 
						
											 
					}
					$orderArr[] = $dataShop;
			
			
					 
				
					 
			}
			$_SESSION['orderarr']=$orderArr;
			
			return   $this->showOrderHtml($orderArr);   
		}
		
		public function showOrderHtml($orderArr){
			$bodyCar  =''; 
			 $totalAmountTicket='';
			 //Add Car$tt System here //
			 $bodyCar .=' <div class="carttable">';
							
						 	  
							  if(count($orderArr)>0){
								foreach($orderArr as $cartItem){
									
									if($cartItem['type'] == 'event'){
									
											 $bodyCar .='<div id="repeatble" >';
											 $bodyCar .='  <div class="cartaction">
																<ul>
																	<li><input name="deleteItem" type="radio" id="radioButtonContainerId"  onclick="deleteItem('.$cartItem['eventgroup_id'].')" ></li>
																	<li><a href="#"><i class="fas fa-trash-alt"></i></a></li>
																</ul>
															</div>
															<div class="cartimg">
																<a href="#"><img src="'.$cartItem['event_group_picture'].'" ></a>
															</div>
															<div class="cartdetails">
																<div class="cartItem">
																	<h2>Evenement</h2>
																	<div class="evencon">
																		<p>'.$cartItem['event_name'].'</p>
																	</div>
																</div>
																 <div class="cartlefu">
																		<h2>Lieu   </h2>
																		<div class="evencon">
																			<p>'.$cartItem['event_auditorium'].' '.$cartItem['event_city'].' </p>
																		</div>
																	</div>
																	<div class="cartdate">
																		<h2>  date </h2>
																		<div class="evencon">
																			<p> '.$cartItem['event_date'].'</p>
																		</div>
																	</div>
																	<div class="cartnbre">
																		<h2>Nbre de places </h2>
																		<div class="evencon">
																			<p>'.$cartItem['qtx'].' places</p>
																		</div>
																	</div>
																	<div class="cartcat">
																		<h2>Categorie</h2>
																		<div class="evencon">
																			<P>'.$cartItem['ticket_category'].' </P>
																		</div>
																	</div>
																	<div class="cartprice">
																		<h2>Prix/Place</h2>
																		<div class="evencon">
																			<p><b>'.$cartItem['price'].' ₪</b></p>
																		</div>
																	</div>
																 </div>
															 </div> 
															';
											$totalAmountTicket=$totalAmountTicket+($cartItem['price']*$cartItem['qtx']);
											$_SESSION['totalamount']=$totalAmountTicket;
									}else{
										if($cartItem['type'] == 'booking_fees'){
											$bodyCar .=' <div class="actionsection">';
											$bodyCar .='  	<div class="asCon">
																<div class="asLT">
																	<p>MONTANT TOTAL des billets</p>
																</div>
																<div class="asRT">
																	<p><b>'.$totalAmountTicket.'₪</b></p>
																</div>
															</div>';
															$bookingfee=$cartItem['price'];
											$bodyCar .='  	<div class="asCon">
																<div class="asLT">
																	<p>frais de gestion</p>
																</div>
																<div class="asRT">
																	<p><b>2x '.$bookingfees.'₪</b></p>
																</div>
															</div>';
											$bodyCar .='  		<div class="asCon redcol">
																	<div class="asLT">
																		<p>montant total de la commande</p>
																	</div>
																	<div class="asRT">
																		<p><b>'.(2*$bookingfees+$totalAmountTicket).'₪</b></p>
																	</div>
																</div>';
										}
										if($cartItem['type'] == 'giftcard'){
											$giftcard  =$cartItem['price'];
											$bodyCar .='  <div class="asCon">
																<div class="asLT">
																	<p>MONTANT CARTE CADEAU</p>
																</div>
																<div class="asRT">
																	<p><b>'.$giftcard.'₪</b></p>
																</div>
															</div>
															<div class="asCon blackcol">
																<div class="asLT">
																	<p>RESTE À RÉGLER</p>
																</div>';  
																$net_totalAmount = 	((2*$bookingfees+$totalAmountTicket)-$giftcard);
										
																$bodyCar .=' 
																		<div class="asRT">
																			<p><b>'.$net_totalAmount.'₪</b></p>
																		</div>
																	</div>

																	<a href="javascript:void(0)" id="confirmbooking" class="commandelink"><i class="fas fa-lock"></i> &nbsp;&nbsp;&nbsp; valider ma réservation <span>(paiement sécurisé)</span></a>
																</div>';
																$bodyCar .=' </div>';
										}
									}
									
								}
								 
								    
							  } 
					  return  $bodyCar;
		}
		
		  public function getPayment($request, $response) {
			   // unset($_SESSION["cart_products"]);
			// print_r($_SESSION["cart_products"]);exit;
				$bookingfees='1';
				$giftcard='1';
			   $Fulltotalpay= 2*$bookingfees+$_SESSION["totalamount"]-$giftcard;

					$tag=4;
				$this->data['total']=$_SESSION["totalamount"];
				$this->data['totalamount']=$Fulltotalpay;
					$this->data['tag']=$tag;
					$this->data['tag']=$tag;
				$this->data['cart_products']=$_SESSION["cart_products"];
				return $this->render($response,  'public/booking/booking.twig',$this->data);
			}
		
			
		 public function getAllCountry(){
				$countries = array("AF" => "Afghanistan",
								"AX" => "Åland Islands",
								"AL" => "Albania",
								"DZ" => "Algeria",
								"AS" => "American Samoa",
								"AD" => "Andorra",
								"AO" => "Angola",
								"AI" => "Anguilla",
								"AQ" => "Antarctica",
								"AG" => "Antigua and Barbuda",
								"AR" => "Argentina",
								"AM" => "Armenia",
								"AW" => "Aruba",
								"AU" => "Australia",
								"AT" => "Austria",
								"AZ" => "Azerbaijan",
								"BS" => "Bahamas",
								"BH" => "Bahrain",
								"BD" => "Bangladesh",
								"BB" => "Barbados",
								"BY" => "Belarus",
								"BE" => "Belgium",
								"BZ" => "Belize",
								"BJ" => "Benin",
								"BM" => "Bermuda",
								"BT" => "Bhutan",
								"BO" => "Bolivia",
								"BA" => "Bosnia and Herzegovina",
								"BW" => "Botswana",
								"BV" => "Bouvet Island",
								"BR" => "Brazil",
								"IO" => "British Indian Ocean Territory",
								"BN" => "Brunei Darussalam",
								"BG" => "Bulgaria",
								"BF" => "Burkina Faso",
								"BI" => "Burundi",
								"KH" => "Cambodia",
								"CM" => "Cameroon",
								"CA" => "Canada",
								"CV" => "Cape Verde",
								"KY" => "Cayman Islands",
								"CF" => "Central African Republic",
								"TD" => "Chad",
								"CL" => "Chile",
								"CN" => "China",
								"CX" => "Christmas Island",
								"CC" => "Cocos (Keeling) Islands",
								"CO" => "Colombia",
								"KM" => "Comoros",
								"CG" => "Congo",
								"CD" => "Congo, The Democratic Republic of The",
								"CK" => "Cook Islands",
								"CR" => "Costa Rica",
								"CI" => "Cote D'ivoire",
								"HR" => "Croatia",
								"CU" => "Cuba",
								"CY" => "Cyprus",
								"CZ" => "Czech Republic",
								"DK" => "Denmark",
								"DJ" => "Djibouti",
								"DM" => "Dominica",
								"DO" => "Dominican Republic",
								"EC" => "Ecuador",
								"EG" => "Egypt",
								"SV" => "El Salvador",
								"GQ" => "Equatorial Guinea",
								"ER" => "Eritrea",
								"EE" => "Estonia",
								"ET" => "Ethiopia",
								"FK" => "Falkland Islands (Malvinas)",
								"FO" => "Faroe Islands",
								"FJ" => "Fiji",
								"FI" => "Finland",
								"FR" => "France",
								"GF" => "French Guiana",
								"PF" => "French Polynesia",
								"TF" => "French Southern Territories",
								"GA" => "Gabon",
								"GM" => "Gambia",
								"GE" => "Georgia",
								"DE" => "Germany",
								"GH" => "Ghana",
								"GI" => "Gibraltar",
								"GR" => "Greece",
								"GL" => "Greenland",
								"GD" => "Grenada",
								"GP" => "Guadeloupe",
								"GU" => "Guam",
								"GT" => "Guatemala",
								"GG" => "Guernsey",
								"GN" => "Guinea",
								"GW" => "Guinea-bissau",
								"GY" => "Guyana",
								"HT" => "Haiti",
								"HM" => "Heard Island and Mcdonald Islands",
								"VA" => "Holy See (Vatican City State)",
								"HN" => "Honduras",
								"HK" => "Hong Kong",
								"HU" => "Hungary",
								"IS" => "Iceland",
								"IN" => "India",
								"ID" => "Indonesia",
								"IR" => "Iran, Islamic Republic of",
								"IQ" => "Iraq",
								"IE" => "Ireland",
								"IM" => "Isle of Man",
								"IL" => "Israel",
								"IT" => "Italy",
								"JM" => "Jamaica",
								"JP" => "Japan",
								"JE" => "Jersey",
								"JO" => "Jordan",
								"KZ" => "Kazakhstan",
								"KE" => "Kenya",
								"KI" => "Kiribati",
								"KP" => "Korea, Democratic People's Republic of",
								"KR" => "Korea, Republic of",
								"KW" => "Kuwait",
								"KG" => "Kyrgyzstan",
								"LA" => "Lao People's Democratic Republic",
								"LV" => "Latvia",
								"LB" => "Lebanon",
								"LS" => "Lesotho",
								"LR" => "Liberia",
								"LY" => "Libyan Arab Jamahiriya",
								"LI" => "Liechtenstein",
								"LT" => "Lithuania",
								"LU" => "Luxembourg",
								"MO" => "Macao",
								"MK" => "Macedonia, The Former Yugoslav Republic of",
								"MG" => "Madagascar",
								"MW" => "Malawi",
								"MY" => "Malaysia",
								"MV" => "Maldives",
								"ML" => "Mali",
								"MT" => "Malta",
								"MH" => "Marshall Islands",
								"MQ" => "Martinique",
								"MR" => "Mauritania",
								"MU" => "Mauritius",
								"YT" => "Mayotte",
								"MX" => "Mexico",
								"FM" => "Micronesia, Federated States of",
								"MD" => "Moldova, Republic of",
								"MC" => "Monaco",
								"MN" => "Mongolia",
								"ME" => "Montenegro",
								"MS" => "Montserrat",
								"MA" => "Morocco",
								"MZ" => "Mozambique",
								"MM" => "Myanmar",
								"NA" => "Namibia",
								"NR" => "Nauru",
								"NP" => "Nepal",
								"NL" => "Netherlands",
								"AN" => "Netherlands Antilles",
								"NC" => "New Caledonia",
								"NZ" => "New Zealand",
								"NI" => "Nicaragua",
								"NE" => "Niger",
								"NG" => "Nigeria",
								"NU" => "Niue",
								"NF" => "Norfolk Island",
								"MP" => "Northern Mariana Islands",
								"NO" => "Norway",
								"OM" => "Oman",
								"PK" => "Pakistan",
								"PW" => "Palau",
								"PS" => "Palestinian Territory, Occupied",
								"PA" => "Panama",
								"PG" => "Papua New Guinea",
								"PY" => "Paraguay",
								"PE" => "Peru",
								"PH" => "Philippines",
								"PN" => "Pitcairn",
								"PL" => "Poland",
								"PT" => "Portugal",
								"PR" => "Puerto Rico",
								"QA" => "Qatar",
								"RE" => "Reunion",
								"RO" => "Romania",
								"RU" => "Russian Federation",
								"RW" => "Rwanda",
								"SH" => "Saint Helena",
								"KN" => "Saint Kitts and Nevis",
								"LC" => "Saint Lucia",
								"PM" => "Saint Pierre and Miquelon",
								"VC" => "Saint Vincent and The Grenadines",
								"WS" => "Samoa",
								"SM" => "San Marino",
								"ST" => "Sao Tome and Principe",
								"SA" => "Saudi Arabia",
								"SN" => "Senegal",
								"RS" => "Serbia",
								"SC" => "Seychelles",
								"SL" => "Sierra Leone",
								"SG" => "Singapore",
								"SK" => "Slovakia",
								"SI" => "Slovenia",
								"SB" => "Solomon Islands",
								"SO" => "Somalia",
								"ZA" => "South Africa",
								"GS" => "South Georgia and The South Sandwich Islands",
								"ES" => "Spain",
								"LK" => "Sri Lanka",
								"SD" => "Sudan",
								"SR" => "Suriname",
								"SJ" => "Svalbard and Jan Mayen",
								"SZ" => "Swaziland",
								"SE" => "Sweden",
								"CH" => "Switzerland",
								"SY" => "Syrian Arab Republic",
								"TW" => "Taiwan, Province of China",
								"TJ" => "Tajikistan",
								"TZ" => "Tanzania, United Republic of",
								"TH" => "Thailand",
								"TL" => "Timor-leste",
								"TG" => "Togo",
								"TK" => "Tokelau",
								"TO" => "Tonga",
								"TT" => "Trinidad and Tobago",
								"TN" => "Tunisia",
								"TR" => "Turkey",
								"TM" => "Turkmenistan",
								"TC" => "Turks and Caicos Islands",
								"TV" => "Tuvalu",
								"UG" => "Uganda",
								"UA" => "Ukraine",
								"AE" => "United Arab Emirates",
								"GB" => "United Kingdom",
								"US" => "United States",
								"UM" => "United States Minor Outlying Islands",
								"UY" => "Uruguay",
								"UZ" => "Uzbekistan",
								"VU" => "Vanuatu",
								"VE" => "Venezuela",
								"VN" => "Viet Nam",
								"VG" => "Virgin Islands, British",
								"VI" => "Virgin Islands, U.S.",
								"WF" => "Wallis and Futuna",
								"EH" => "Western Sahara",
								"YE" => "Yemen",
								"ZM" => "Zambia",
								"ZW" => "Zimbabwe");
								return $countries;

			}
		public function confirmPayment($request, $response){
						 
		 }
	 
			public function donfo_create_token($data){
					 
						
						
					 
					$tranzilatoken = '';
					
					$expdate = str_replace("/", "", $data['expiration_date']);
					$expdate = preg_replace('/\s+/', '', $expdate);
					// Prepare transaction parameters>
					$query_parameters['supplier'] = $this->supplier;
				    $query_parameters['sum'] = $data['total_amount'];  
				 	$query_parameters['currency'] = $data['currency']; //Type of currency 1 NIS, 2 USD, 978 EUR, 826 GBP, 392 JPY
					$query_parameters['ccno'] = $data['credit_card_number']; // Test card number = '12312312'
					
				    $query_parameters['expyear'] = $data['expiration_Year'] ;// Card expiry date: mmyy ='0820'
				    $query_parameters['expdate'] = $expdate ;// Card expiry date: mmyy ='0820'
					$query_parameters['mycvv'] = $data['mycvv'] ;// Card expiry date: mmyy ='0820'
					 
					if($data['currency'] == 1){
				        $query_parameters['myid'] = $data['id_number']; // ID number = '12312312'
					}
					else{
				  //      $query_parameters['myid'] = '';
					}
					$query_parameters['myid'] = '';
					 
				 //   $query_parameters['cred_type'] = '1'; // This field specifies the type of transaction, 1 - normal transaction, 6 - credit, 8 - payments
					$query_parameters['TranzilaPW'] = $this->tranzilapw; // Token password if required
				  //  $query_parameters['tranmode'] = 'V'; // Mode for verify transaction
					$query_parameters['TranzilaTK'] = 1;
					// Prepare query string
					$query_string = '';
					foreach ($query_parameters as $name => $value) {
						$query_string .= $name . '=' . $value . '&';
					}
					$query_string = substr($query_string, 0, -1); // Remove trailing '&'
					// Initiate CURL
					$cr = curl_init();
					curl_setopt($cr, CURLOPT_URL,$this->tranzila_api_host.$this->tranzila_api_path);
					curl_setopt($cr, CURLOPT_POST, 1);
					curl_setopt($cr, CURLOPT_FAILONERROR, true);
					curl_setopt($cr, CURLOPT_POSTFIELDS, $query_string);
					curl_setopt($cr, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($cr, CURLOPT_SSL_VERIFYPEER, 0);
					// Execute request
					$result = curl_exec($cr);
					$error = curl_error($cr);
					if (!empty($error)) {
						return ($error);
					}
					curl_close($cr);
					// Preparing associative array with response data
					$response_array = explode('&', $result);  
					if (count($response_array) >= 1) {
						foreach ($response_array as $value) {
							$tmp = explode('=', $value);
							if (count($tmp) > 1) {
								$response_assoc[$tmp[0]] = $tmp[1];
							}
						}
					}
				   // Analyze the result string
					if (!isset($response_assoc['TranzilaTK'])) {
						$this->errorPayment['token'] ='Token is not Valid. Please contact to Administration';
						//getting a token was unsuccessful
						
						//getting a token was unsuccessful 
					   /*
						foreach($response_assoc as $k=> $v){
							$res = 	filter_var($v, FILTER_SANITIZE_STRING);
									echo '<div class="error">'.$k.': <p>'.$res.'</p></div>';
							
						} */
						 
					}  else {
						$tranzilatoken = $response_assoc['TranzilaTK']; //TranzilaTK
					   //submit tranzila token  value into the tranzilatoken field
						
					}
					return $tranzilatoken;
			}

 
			/**
			* Send transaction data from Caldera form to Tranzila and recieve confirmation code.
			*/
			public function donfo_transaction($data ,$token){
				
			 
				
				$field_id = 'fld_7259702'; //confirmation field
				$tranzilaconfirmation = '';
				$expdate = str_replace("/", "", $data['expiration_date']);
				$expdate = preg_replace('/\s+/', '', $expdate);
				// Prepare transaction parameters>
				$query_parameters['supplier']   = $this->supplier;
				$query_parameters['sum']        = $data['total_amount'];       //Transaction sum
				$query_parameters['currency']   = $data['currency'];           //Type of currency 1 NIS, 2 USD, 978 EUR, 826 GBP, 392 JPY
				$query_parameters['ccno']       = $data['credit_card_number']; // Test card number = '12312312'
				$query_parameters['expdate']    = $expdate ;


				    $query_parameters['expyear'] = $data['expiration_Year'] ;// Card expiry date: mmyy ='0820'
				 
					$query_parameters['mycvv'] = $data['mycvv'] ;// Card expiry date: mmyy ='0820'
				// Card expiry date: mmyy ='0820'
				if($data['currency'] == 1){
					$query_parameters['myid'] = $data['id_number']; // ID number = '12312312'
				 }
				 else{
					 $query_parameters['myid'] = '';
				 }
				 $query_parameters['myid'] = '';
				$query_parameters['cred_type']  = '1';                         // This field specifies the type of transaction, 1 - normal transaction, 6 - credit, 8 - payments
				$query_parameters['TranzilaPW'] = $this->tranzilapw;                 // Token password if required
				$query_parameters['tranmode']   = 'A';                         // Mode for verify transaction
				$query_parameters['TranzilaTK'] = $token;
				/* if ($data['frequency']!= 1){ if there are payments
					//multiple payments
					$query_parameters['fpay'] = $data['total_amount'];
					$query_parameters['spay'] = $data['total_amount'];
					$query_parameters['npay'] = 12;
				} */
				// Prepare query string
				$query_string = '';
				foreach ($query_parameters as $name => $value) {
					$query_string .= $name . '=' . $value . '&';
				}
				$query_string = substr($query_string, 0, -1); // Remove trailing '&'
				// Initiate CURL
				$cr = curl_init();
				curl_setopt($cr, CURLOPT_URL,$this->tranzila_api_host.$this->tranzila_api_path);
				curl_setopt($cr, CURLOPT_POST, 1);
				curl_setopt($cr, CURLOPT_FAILONERROR, true);
				curl_setopt($cr, CURLOPT_POSTFIELDS, $query_string);
				curl_setopt($cr, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($cr, CURLOPT_SSL_VERIFYPEER, 0);
				// Execute request
				 $result = curl_exec($cr);
				 //Need to remove later//
				      /*
						$abc=array();
						$abc[] = 'Response=000';
						$abc[] = 'mycvv=0';
						$abc[] = 'TranzilaTK=a8658bd932e83f64661';
						$abc[] = 'myid=01';
						$abc[] = 'cred_type=1';
						$abc[] = 'ccno=4661';
						$abc[] = 'expyear=18';
						$abc[] = 'DclickTK=';
						$abc[] = 'supplier=acarragrotok';
						$abc[] = 'expdate=0718';
						$abc[] = 'tranmode=A';
						$abc[] = 'sum=1';
						$abc[] = 'ConfirmationCode=0000000';
						$abc[] = 'Responsesource=0';
						$abc[] = 'Responsecvv=3';

						$abc[] = 'Responseid=3';
						$abc[] = 'Tempref=01850001';
						$abc[] = 'DBFIsForeign=1';
						$abc[] = 'DBFcard=2';
						$abc[] = 'cardtype=2';
						$abc[] = 'DBFcardtype=2';
						$abc[] = 'cardissuer=2';
						$abc[] = 'DBFsolek=6';
						$abc[] = 'cardaquirer=6';
						$abc[] = 'tz_parent=acarragrotok';

						$result =$abc;*/
				// End remove here //   
				$error = curl_error($cr);
				if (!empty($error)) {
					die ($error);
				}
				curl_close($cr);
				// Preparing associative array with response data
				// print_r($result);
			    $response_array = explode('&', $result); //print_r($response_array);
				
				
				//Need to remove later//
				
					//$response_array = $result;
				  
				// End here // 
				 
				 //print_r($response_array);exit;
				$response_assoc = array();
				if (count($response_array) > 1) {
					foreach ($response_array as $value) {
						$tmp = explode('=', $value);
						if (count($tmp) > 1) {
							$response_assoc[$tmp[0]] = $tmp[1];
						}
					}
				}
				 
				// Analyze the result string
				if (!isset($response_assoc['Response'])) {
				    $this->errorPayment['token'] ='There is authentication issue!!';
		            $this->responsePayment['tranStatus']= '0';
					 
					/**
					 * When there is no 'Response' parameter it either means
					 * that some pre-transaction error happened (like authentication
					 * problems), in which case the result string will be in HTML format,
					 * explaining the error, or the request was made for generate token only
					 * (in this case the response string will contain only 'TranzilaTK'
					 * parameter)
					 */
				} else if ($response_assoc['Response'] !== '000') {
					$this->errorPayment['token'] ='Transaction get Failure due to bad creditcard information';
		            $this->responsePayment['tranStatus']= '0';
				 
					// Any other than '000' code means transaction failure
					// (bad card, expiry, etc..)
				}
				else {
					    $tranzilaconfirmation = $response_assoc['Tempref']; //ConfirmationCode 
						$this->responsePayment['tranStatus']= '1';  
						$this->responsePayment['transation_id']= $tranzilaconfirmation;  
					
				}
				 //print_r($this->responsePayment);exit;
			}
			
			 function get_client_ip()
			 {
				  $ipaddress = '';
				  if (getenv('HTTP_CLIENT_IP'))
					  $ipaddress = getenv('HTTP_CLIENT_IP');
				  else if(getenv('HTTP_X_FORWARDED_FOR'))
					  $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
				  else if(getenv('HTTP_X_FORWARDED'))
					  $ipaddress = getenv('HTTP_X_FORWARDED');
				  else if(getenv('HTTP_FORWARDED_FOR'))
					  $ipaddress = getenv('HTTP_FORWARDED_FOR');
				  else if(getenv('HTTP_FORWARDED'))
					  $ipaddress = getenv('HTTP_FORWARDED');
				  else if(getenv('REMOTE_ADDR'))
					  $ipaddress = getenv('REMOTE_ADDR');
				  else
					  $ipaddress = 'UNKNOWN';

				  return $ipaddress;
			 }
			 
			 public function  ajaxcallRemoveItem($request, $response){
				  $eventgroupid=$request->getAttribute('grpid'); //fetch the category id
				if(isset($_SESSION["cart_products"])){
							unset($_SESSION["cart_products"]);
				} 
				$jsonData = array('status' => '1', 'msg' =>'Bienvenue sur Culturaccess.com');
				return $response->withHeader('Content-type','application/json')->write(json_encode($jsonData));
                exit();	 
			 }
			 
			 
			 
}