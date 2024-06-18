<?php

namespace App\Models\Classes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassMultiPassSession extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function pass()
    {
        return $this->belongsTo(ClassMultiPass::class, 'class_multi_pass_id', 'id');
    }
}
