<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class PaymentType extends Eloquent
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
    protected $table = 'payment_type';
    
	/**
     * Define fillable columns
     *
     * @var string
     */
	
	protected $fillable = array(
	    'name',
		'payment_logo',
		'status'
	);
	
    /**
     * Define timestamps
     *
     * @var string
     */
	 
	 public $timestamps = false;
	 
    
}
