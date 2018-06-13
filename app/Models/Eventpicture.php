<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Eventpicture extends Eloquent
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
    protected $table = 'event_images';
	
	/**
     * Define fillable columns
     *
     * @var string
     */
	
	protected $fillable = array(
	    'event_id',
		'event_img'
	);
	
	/**
     * Define timestamps
     *
     * @var string
     */
	 
	 public $timestamps = false;
	 
	 
    
    
    
}
