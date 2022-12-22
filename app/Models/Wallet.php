<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
  
class Wallet extends Model
{
    use HasFactory;
  
    public $table = "wallet";
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    protected $fillable = [
        'user_id',
        'currency_id',
        'amount',
        'pb_key',
        'pv_key',
    ];
  
    /**
     * User
     *
     * @return response()
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Receiver
     *
     * @return response()
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id', 'id');
    }
}