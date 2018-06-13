<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class EventTime extends Eloquent
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
    protected $table = 'event_times';
    
    
	/**
     * Define fillable columns
     *
     * @var string
     */
	
	protected $fillable = array(
	    'event_id',
		'event_time'
	);
	
	/**
     * Define timestamps
     *
     * @var string
     */
	 
	 public $timestamps = false;
	 
	 
    
}
