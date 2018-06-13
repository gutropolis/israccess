<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;



class Usermeta extends Eloquent

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

    protected $table = 'user_meta';

    

    

	/**

     * Define fillable columns

     *

     * @var string

     */

	

	protected $fillable = array( 
	    'user_id', 
		'first_name', 'last_name',
		'address_1','address_2', 
		'street', 
		'postal_code',  
		'dob',
		'phone_no',
		'country',
		'email',
		'ville'
	);

	

	/**

     * Define timestamps

     *

     * @var string

     */

	 

	 public $timestamps = false;

	 

	 

    

}

