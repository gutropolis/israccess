<?php
namespace App\Models;
//use Illuminate\Database\Query\Builder@where;
use Illuminate\Database\Eloquent\Model as Eloquent;


class Category extends Eloquent
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
    protected $table = 'categories';
	
	/**
     * Define fillable columns
     *
     * @var string
     */
	
	protected $fillable = array(
	    'name',
		'picto_file',
		'status',
		'slug',
		'is_for_home',
		'home_slider_title'
	);
	
	/**
     * Define timestamps
     *
     * @var string
     */
	 
	 public $timestamps = false;
	
	 
    
    
    
}
