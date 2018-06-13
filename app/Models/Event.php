<?php
namespace App\Models;
//use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model as Eloquent;
//use Illuminate\Database\Capsule\Manager;
class Event extends Eloquent
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
    protected $table = 'events';
	
	/**
     * Define fillable columns
     *
     * @var string
     */
	
	protected $fillable = array(
	    'title',
		'date',
		'created_at',
		'updated_at',
		'eventgroup_id',
		'city_id',
		'auditorium_id',
		'status',
		'section',
		'description',
		'artist_name',
		'author_name',
		'productor_name',
		'director_name',
		'contributor_name',
		'contributor_description',
		'seats_on_map',
		'auditorium_key',
		'auditorium_seats_map',
		'event_ticket_type',
		'booking_fee'
		
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
	 
	 public function city(){
		 return $this->belongsTo('App\Models\City');
	 }
	 
	 /**
     * Define the relationship 
     *
     * @var string
     */
	 
	 public function auditorium(){
		 return $this->belongsTo('App\Models\Auditorium');
	 }
	 
	 /**
     * Define the relationship 
     *
     * @var string
     */
	 
	 public function picture(){
		 return $this->hasMany('App\Models\Eventpicture');
	 }
	 
	 /**
     * Define the relationship 
     *
     * @var string
     */
     public function eventgroup(){		 
	     return $this->belongsTo('App\Models\Eventgroup');	 
	 }
	 
	 /**
     * Define the relationship 
     *
     * @var string
     */
     public function eventseats(){		 
	     return $this->belongsTo('App\Models\EventSeatCategories');	 
	 }
	 
	 
	 
	 
    
}
