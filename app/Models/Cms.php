<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Cms extends Eloquent
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
    protected $table = 'cms';
    
    
	/**
     * Define fillable columns
     *
     * @var string
     */
	
	protected $fillable = array(
	    'title',
		'description'
	);
	
	/**
     * Define timestamps
     *
     * @var string
     */
	 
	 public $timestamps = false;
	 
	 
    
}
