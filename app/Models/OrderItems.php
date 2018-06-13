<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class OrderItems extends Eloquent
{
    /**
     * Define table primary key
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Define table name
     *
     * @var string
     */
    protected $table = 'orderitems';
	
	/**
     * Define fillable columns
     *
     * @var string
     */
	
	protected $fillable = array(
	    'order_id',
		'type_product',
		'ticket_type',
		'product_id',
		'ticket_row',
		'quantity',
		'price',
		'created_on',
		'producer_id',
		'ticket_category',
		'seat_qty',
		'available_seats',
		'booking_time',
        'event_ticket_category_id',
        'ticket_row_id'
		 
	);
	
	/**
     * Define timestamps
     *
     * @var string
     */
	 
	 public $timestamps = false;
	 
	 /**
     * Define the relationship 
     *
     * @var string
     */
	 
	 public function Order(){
		 return $this->belongsTo('App\Models\Order');
	 }
	 
	 
	  
 
}
