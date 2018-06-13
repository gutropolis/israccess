<?php
namespace App\Models;
//use Illuminate\Database\Query\Builder@where;
use Illuminate\Database\Eloquent\Model as Eloquent;


class Setting extends Eloquent
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
    protected $table = 'settings';
	
	/**
     * Define fillable columns
     *
     * @var string
     */
	
	protected $fillable = array(
	    'site_phone',
		'facebook_link',
		'twitter_link',
		'instagram_link'
	);
	
	/**
     * Define timestamps
     *
     * @var string
     */
	 
	 public $timestamps = false;
	
	 
    
    
    
}
