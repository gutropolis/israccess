<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Slider extends Eloquent
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
    protected $table = 'sliders';
    
    
	/**
     * Define fillable columns
     *
     * @var string
     */
	
	protected $fillable = array(
	    'picture_caption',
		'slider_picture',
		'redirect_link',
		'status',
		'description',
		'display_order'
	);
	
	/**
     * Define timestamps
     *
     * @var string
     */
	 
	 public $timestamps = false;
	 
	 
    
}
