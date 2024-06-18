<?php

namespace App\Models\Booking;

use App\Models\Traits\Activeable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable;
    use BelongsToTenant;
    use HasRoles;
    use Activeable;
    use HasFactory;

    protected static function newFactory(): Factory
    {
        return \Database\Factories\UserFactory::new();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    //protected $table = 'users';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getTaxAttribute()
    {
        $data = $this->tax_setting ?? '1,1';
        $data = explode(',', $data);

        return [
            'hotel_tax' => $data[0],
            'goods_tax' => $data[1],
        ];
    }

    public function rooms()
    {
        return $this->hasMany(AgentRoom::class, 'user_id', 'id');
    }

    public function histories()
    {
        return $this->hasMany(UserHistory::class);
    }

    public function getAllowedCampsDecodedAttribute()
    {
        return collect(json_decode($this->allowed_camps));
    }

    public function allowedCampsFormatted($locations)
    {
        $allowed_camps = collect(json_decode($this->allowed_camps));

        return $allowed_camps->map(function ($camp) use ($locations) {
            return '<span class="tippy" data-tippy-content="' . $locations[$camp]->name
                    . '">' . $locations[$camp]->abbr . '</span>';
        })->implode(', ');
    }
}
