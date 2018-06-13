<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class RowSeats extends Eloquent
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
    protected $table = 'row_seats';
    
	/**
     * Define fillable columns
     *
     * @var string
     */
	
	protected $fillable = array(
	    'event_seat_categories_id',
		'row_number',
		'seat_from',
		'seat_to',
		'total_qantity',
		'net_total_quantity',
		'seat_order'
	);
	
    /**
     * Define timestamps
     *
     * @var string
     */
	 
	 public $timestamps = false;
	 
	 
	 
    
}
