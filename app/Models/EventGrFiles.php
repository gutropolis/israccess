<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class EventGrFiles extends Eloquent
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
    protected $table = 'event_group_files';
    
	/**
     * Define fillable columns
     *
     * @var string
     */
	
	protected $fillable = array(
	    'eventgroup_id',
	    'file_name',
		'file_type',
		'video_img'
	);
	
	/**
     * Define timestamps
     *
     * @var string
     */
	 
	 public $timestamps = false;
	 
	
	 
	
	 
	 
    
    
}
