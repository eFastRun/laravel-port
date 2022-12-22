<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;
  
    public $table = "currency";
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    protected $fillable = [
        'symbol',
        'name',
        'type',
        'api_id',
    ];
}