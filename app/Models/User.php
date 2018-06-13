<?php
namespace App\Models;
use Illuminate\Database\Capsule;

use Illuminate\Database\Eloquent\Model as Eloquent;

class User extends Eloquent
{
	/**
     * Define table primary key
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Define fillable columns
     *
     * @var string
     */
    protected $fillable = array(
		'name',
        'username',
        'email',
        'password',
		'status',
		'credit',
		'type'
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
	 
	 public function memberdata(){
		 return $this->hasOne('App\Models\Usermeta');
	 }
	 
	 /**
     * Define the relationship 
     *
     * @var string
     */
	 
	 public function customerdata(){
		 return $this->hasOne('App\Models\Usermeta');
	 }
	 
	 
	 /**
     * Define the relationship 
     *
     * @var string
     */
     public function productormeta(){		 
	     return $this->hasOne('App\Models\Productor_meta');	 
	 }
	 
	 
	
}