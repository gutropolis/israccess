<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Advertisement extends Eloquent
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
    protected $table = 'advertisements';
    
    
	/**
     * Define fillable columns
     *
     * @var string
     */
	
	protected $fillable = array(
	    'title',
		'ad_picture',
		'redirect_link',
		'status',
		'created_on'
	);
	
	/**
     * Define timestamps
     *
     * @var string
     */
	 
	 public $timestamps = false;
	 
	 
    
}
