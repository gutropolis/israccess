<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Auditorium extends Eloquent
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
    protected $table = 'auditoriums';

	/**
     * Define fillable columns
     *
     * @var string
     */
	
	protected $fillable = array(
	    'name',
		'background_file',
		'address',  // New column
		'access', // New column
		'waze_name', // New column
		'detail', // New column
		'lat',
		'lng',
		'type',
		'auditorium_key',
		'auditorium_map'
	);
	
	/**
     * Define timestamps
     *
     * @var string
     */
	 
	 public $timestamps = false;
	
    
    
    
}
