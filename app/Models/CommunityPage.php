<?php
namespace App\Models;
//use Illuminate\Database\Query\Builder@where;
use Illuminate\Database\Eloquent\Model as Eloquent;


class CommunityPage extends Eloquent
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
    protected $table = 'community_page';
	
	/**
     * Define fillable columns
     *
     * @var string
     */
	
	protected $fillable = array(
	    'title',
		'short_description',
		'full_description',
		'display_order',
		'status'
	);
	
	/**
     * Define timestamps
     *
     * @var string
     */
	 
	 public $timestamps = false;
	
	 
    
    
    
}
