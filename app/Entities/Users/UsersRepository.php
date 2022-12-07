<?php
namespace App\Entities\Users;

use App\Entities\BaseRepository;
use App\Exceptions\UserUpdateException;
use App\Services\Facades\Option;

/**
* Users Repository Class
*/
class UsersRepository extends BaseRepository
{

    protected $model;

    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function getUsers($networkId = null)
    {
        return $this->model->where(function($query) use ($networkId) {
                if ($networkId)
                    $query->where('network_id', $networkId);
            })
            ->with('network')
            ->paginate($this->_paginate);
    }

    public function searchUsers($q)
    {
        return $this->model->where('name','like','%'.$q.'%')
            ->with('network')
            ->paginate($this->_paginate);
    }

    public function create($userData)
    {
        $user           = $this->model;
        $user->username = $userData['username'];
        $user->email    = $userData['email'];
        $user->password = $userData['password'] ?: Option::get('default_password', 'secret');
        $user->name     = $userData['name'];
        $user->phone    = $userData['phone'];
        $user->gender_id = $userData['gender_id'];
        $user->role_id  = $userData['role_id'];
        $user->network_id = $userData['network_id'];
        $user->api_token = str_random(40);

        $user->save();

        return $user;
    }

    public function update($userData, $userId)
    {
        $user = $this->requireById($userId);

        $user->username = $userData['username'];
        $user->name     = $userData['name'];
        $user->email    = $userData['email'];
        $user->phone    = $userData['phone'];
        $user->network_id = $userData['network_id'];

        if ($userData['password'])
            $user->password = $userData['password'];

        $user->role_id  = $userData['role_id'];
        $user->gender_id = $userData['gender_id'];
        $user->is_active = $userData['is_active'];

        $user->save();

        return $user;

        // throw new UserUpdateException('Failed to update User');
    }
}