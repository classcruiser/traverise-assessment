<?php

namespace App\Models\Classes;

use App\Models\Booking\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassMultiPassPaymentRecord extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'amount' => 'double',
        'amount_paid' => 'double',
        'paid_at' => 'datetime:Y-m-d',
        'verified_at' => 'datetime:Y-m-d'
    ];

    public function payment()
    {
        return $this->belongsTo(ClassMultiPassPayment::class, 'multi_pass_payment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'verify_by', 'id')->withDefault([
            'id' => null,
            'name' => '---'
        ]);
    }
}
