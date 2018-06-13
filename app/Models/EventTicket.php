<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class EventTicket extends Eloquent
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
    protected $table = 'event_tickets';
	
	/**
     * Define fillable columns
     *
     * @var string
     */
	
	protected $fillable = array(
	    'event_id',
		'ticekt_type',
		'per_ticket_price',
		'total_quantity',
		'gate_id',
		'row_id',
		'seat_id',
		'type_of_seat_id'
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
	 
	 public function event(){
		 return $this->belongsTo('App\Models\Event');
	 }
	 
	 /**
     * Define the relationship 
     *
     * @var string
     */
	 
	 public function auditorium(){
		 return $this->belongsTo('App\Models\Auditorium');
	 }
	 
	 
    
}
