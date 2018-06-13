<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class EventGrComment extends Eloquent
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
    protected $table = 'event_group_comments';
    
	/**
     * Define fillable columns
     *
     * @var string
     */
	
	protected $fillable = array(
	    'eventgroup_id',
	    'title',
		'ratings',
		'comments',
		'signature',
		'for_section'
		
	);
	
	/**
     * Define timestamps
     *
     * @var string
     */
	 
	 public $timestamps = false;
	 
	
    
    
}
