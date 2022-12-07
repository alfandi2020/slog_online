<?php

namespace App\Entities\Users;

use App\Entities\Networks\Network;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laracasts\Presenter\PresentableTrait;

class User extends Authenticatable
{
    use Notifiable, PresentableTrait;

    protected $presenter = UserPresenter::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
        'username', 'dob', 'phone', 'gender_id',
        'role_id', 'network_id', 'is_active',
        'last_seen',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function network()
    {
        return $this->belongsTo(Network::class);
    }

    public function role()
    {
        return Role::getById($this->role_id);
    }

    public function gender()
    {
        return $this->gender_id == 1 ? trans('user.gender_male') : trans('user.gender_female');
    }

    public function getGenderAttribute()
    {
        return $this->gender_id == 1 ? trans('user.gender_male') : trans('user.gender_female');
    }

    public function getStatusAttribute()
    {
        return $this->is_active == 1 ? trans('app.active_status') : trans('app.inactive_status');
    }

    public function nameLink()
    {
        return link_to_route('admin.users.show', $this->name, [$this->id], [
            'title' => trans(
                'app.show_detail_title',
                ['name' => $this->name, 'type' => trans('user.user')]
            ),
        ]);
    }

    public function isAdmin()
    {
        return $this->role_id == 1;
    }

    public function isAccounting()
    {
        return $this->role_id == 2;
    }

    public function isSalesCounter()
    {
        return $this->role_id == 3;
    }

    public function isWarehouse()
    {
        return $this->role_id == 4;
    }

    public function isCustomerService()
    {
        return $this->role_id == 5;
    }

    public function isCashier()
    {
        return $this->role_id == 6;
    }

    public function isCourier()
    {
        return $this->role_id == 7;
    }

    public function isBranchHead()
    {
        return $this->role_id == 9;
    }
}
