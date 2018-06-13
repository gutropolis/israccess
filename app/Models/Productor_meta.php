<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;



class Productor_meta extends Eloquent

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

    protected $table = 'productor_meta';

    

    

	/**

     * Define fillable columns

     *

     * @var string

     */

	

	protected $fillable = array( 
	    'user_id', 
		'company_name',
		'first_name', 
		'telephone',
		'office_phone',
		'company_phone', 
		'registration_date'
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
     public function user_meta(){		 
	     return $this->belongsTo('App\Models\User');	 
	 }

	 

    

}

