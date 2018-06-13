<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Payment extends Eloquent
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
    protected $table = 'payments';
    
    
    
}
