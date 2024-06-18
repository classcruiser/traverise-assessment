<?php

namespace App\Models\Classes;

use App\Models\Booking\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassPaymentRecord extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const PICTURE_BASE_PATH = '/tenancy/assets/class-records/';

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
        'verified_at' => 'datetime:Y-m-d',
        'data' => 'array'
    ];

    public function payment()
    {
        return $this->belongsTo(ClassPayment::class, 'class_payment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'verify_by', 'id')->withDefault([
            'id' => null,
            'name' => '---'
        ]);
    }
}
