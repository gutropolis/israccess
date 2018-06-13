<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class TicektsTypeOfSeat extends Eloquent
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
    protected $table = 'ticket_type_of_seat';
    
    
	/**
     * Define fillable columns
     *
     * @var string
     */
	
	protected $fillable = array(
	    'seat_type_name',
		'status'
	);
	
	/**
     * Define timestamps
     *
     * @var string
     */
	 
	 public $timestamps = false;
	 
	 
    
}
