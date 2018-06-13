<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class AuditoriumSeatCategory extends Eloquent
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
    protected $table = 'auditorium_seat_categories';

	/**
     * Define fillable columns
     *
     * @var string
     */
	
	protected $fillable = array(
	    'auditorium_id',
		'seat_category',
		'seat_row_from',
		'seat_row_to',
		'seat_rows_json',  
		'category_price', 
		'total_qantity', 
		'from_range'
	);
	
	/**
     * Define timestamps
     *
     * @var string
     */
	 
	 public $timestamps = false;
	
    
    
    
}
