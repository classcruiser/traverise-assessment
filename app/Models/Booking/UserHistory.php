<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class UserHistory extends Model
{
    use BelongsToTenant;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $table = 'user_histories';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
