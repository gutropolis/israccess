<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class EventRole extends Eloquent
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
    protected $table = 'event_roles';
    
	/**
     * Define fillable columns
     *
     * @var string
     */
	
	protected $fillable = array(
	    'event_id',
		'role_label',
		'role_name'
		
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
	 
	public function events(){
		 return $this->hasMany('App\Models\Event');
	 }
	 
	
	 
    
    
}
