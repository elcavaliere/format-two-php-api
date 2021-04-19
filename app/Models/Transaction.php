<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /** @const пополнение счета */
    const TYPE_REFILL = 1;
    /** @const списание со счета */
    const TYPE_DEBIT = 2;
    /** @const Возвращаемая транзакция */
    const TYPE_REFUND = 3;

    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get the post that owns the comment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
