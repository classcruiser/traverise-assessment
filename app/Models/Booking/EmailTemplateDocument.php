<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplateDocument extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function email()
    {
        return $this->belongsTo(EmailTemplate::class);
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
