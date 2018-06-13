<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class CategoryPageSlider extends Eloquent
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
    protected $table = 'category_page_slider';
    
    
	/**
     * Define fillable columns
     *
     * @var string
     */
	
	protected $fillable = array(
	    'slider_picture',
		'redirect_link',
		'status'
	);
	
	/**
     * Define timestamps
     *
     * @var string
     */
	 
	 public $timestamps = false;
	 
	 
    
}
