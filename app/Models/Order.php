<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Order extends Eloquent
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
    protected $table = 'orders';
	
	/**
     * Define fillable columns
     *
     * @var string
     */
	
	protected $fillable = array(
	    'customer_id',
		'total_amount',
		'payment_type',
		'created_on',
		'updated_on','invoice_number','payment_id'  
		
		 
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
	 
	 public function Customer(){
		 return $this->belongsTo('App\Models\User');
	 }
	 
	 /**
     * Define the relationship 
     *
     * @var string
     */
	 
	public function orderItems(){
		 return $this->hasMany('App\Models\OrderItems');
	 }
	 
 
}
