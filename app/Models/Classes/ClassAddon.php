<?php

namespace App\Models\Classes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ClassAddon extends Model
{
    use HasFactory;
    use BelongsToTenant;

    public const PICTURE_BASE_PATH = '/tenancy/assets/images/class-addons/';

    protected $guarded = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'rate_type', 'base_price', 'unit_name',
        'description', 'min_unit', 'max_unit', 'sort',
        'is_active', 'admin_only', 'add_default'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'base_price' => 'double',
        'min_unit' => 'integer',
        'max_unit' => 'integer',
        'sort' => 'integer',
        'is_active' => 'boolean',
        'admin_only' => 'boolean',
        'add_default' => 'boolean'
    ];

    public function classes()
    {
        return $this->hasMany(ClassSessionAddon::class);
    }
}
