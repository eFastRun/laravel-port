<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Currency;
  
class Transaction extends Model
{
    use HasFactory;
  
    public $table = "transaction";

    /**
     * Write code on Method
     *
     * @return response()
     */
    protected $fillable = [
        'user_id',
        'action_type',
        'currency_id',
        'receiver_id',
        'amount',
    ];
  
    /**
     * User
     *
     * @return response()
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function currency() {
        return $this->belongsTo(Currency::class, 'currency_id', 'id');
    }
}