<?php

namespace App\Models\Classes;

use App\Models\Traits\Activeable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ClassCategory extends Model
{
    use HasFactory;
    use BelongsToTenant;
    use Activeable;

    protected $guarded = [];

    protected $dates = ['created_at', 'updated_at', 'default_date'];

    public function classes()
    {
        return $this->hasMany(ClassSession::class);
    }

    public function sessions()
    {
        return $this->hasMany(ClassSession::class);
    }

    public function activeClasses()
    {
        return $this->classes()->where('is_active', 1);
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(ClassCategoryBlock::class);
    }
}
