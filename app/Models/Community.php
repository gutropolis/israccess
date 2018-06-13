<?php
namespace App\Models;
//use Illuminate\Database\Query\Builder@where;
use Illuminate\Database\Eloquent\Model as Eloquent;


class Community extends Eloquent
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
    protected $table = 'communities';
	
	/**
     * Define fillable columns
     *
     * @var string
     */
	
	protected $fillable = array(
	    'title',
		'numbers',
		'status'
	);
	
	/**
     * Define timestamps
     *
     * @var string
     */
	 
	 public $timestamps = false;
	
	 
    
    
    
}
