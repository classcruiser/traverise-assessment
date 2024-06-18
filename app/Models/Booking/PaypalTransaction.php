<?php

namespace App\Models\Booking;

use App\Models\Classes\ClassMultiPassPayment;
use App\Models\Classes\ClassPayment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class PaypalTransaction extends Model
{
    use HasFactory;

    use BelongsToTenant;

    protected $guarded = [];

    public function payment()
    {
        switch ($this->payment_model) {
            case 'payments':
                return $this->belongsTo(Payment::class, 'payment_id', 'id');

            case 'class_payments':
                return $this->belongsTo(ClassPayment::class, 'payment_id', 'id');

            case 'class_multi_pass_payments':
                return $this->belongsTo(ClassMultiPassPayment::class, 'payment_id');
        }
    }
}
