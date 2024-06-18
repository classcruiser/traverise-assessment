<?php

namespace App\Models\Classes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSessionAddon extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function details()
    {
        return $this->belongsTo(ClassAddon::class, 'class_addon_id', 'id');
    }
}
