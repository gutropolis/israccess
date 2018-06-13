<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Section extends Eloquent
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
    protected $table = 'sections';
    
    /**
     * Define fillable columns
     *
     * @var string
     */
	
	protected $fillable = array(
	    'section_title',
		'section_name',
		'status',
		'display_order'
	);
	
    /**
     * Define timestamps
     *
     * @var string
     */
	 
	 public $timestamps = false;
	 /**
     * Define the relationship 
     *
     * @var string
     */
	 
	 public function event(){
		 return $this->belongsTo('App\Models\Event');
	 }
    
}
