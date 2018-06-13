<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Eventgroup extends Eloquent
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
    protected $table = 'events_group';
    
	/**
     * Define fillable columns
     *
     * @var string
     */
	
	protected $fillable = array(
	    'group_thumbnail',
	    'group_picture',
	    'title',
		/*'artist_id',*/
		'date_begin',
		'date_end',
		'description',
		'price_min',
		'category_id',
		'section',
		'is_for_home',
		'en_savoir_block1_name',
		'en_savoir_desc1',
		'en_savoir_block2_name',
		'en_savoir_desc2',
		'artist_name',
		'author_name',
		'productor_name',
		'director_name',
		'status',
		'photo_title',
		'thumbnail_title',
		'event_group_slug',
		'producer_id'
		
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
	 
	 public function artist(){
		 return $this->belongsTo('App\Models\User');
	 }
	 
	 /**
     * Define the relationship 
     *
     * @var string
     */
	 
	 public function category(){
		 return $this->belongsTo('App\Models\Category');
	 }
	 
	 
	 /**
     * Define the relationship 
     *
     * @var string
     */
	 
	public function events(){
		 return $this->hasMany('App\Models\Event');
	 }
	 
	 /**
     * Define the relationship 
     *
     * @var string
     */
	 
	public function eventsPictures(){
		 return $this->hasMany('App\Models\Eventpicture');
	 }
	 
	 
    
    
}
