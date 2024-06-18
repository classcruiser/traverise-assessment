<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplateCondition extends Model
{
    use HasFactory;

    protected $fillable = ['email_template_id', 'column', 'operator', 'value'];

    public function email_template()
    {
        return $this->belongsTo(EmailTemplate::class);
    }
}
