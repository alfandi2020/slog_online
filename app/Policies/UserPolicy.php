<?php

namespace App\Policies;

use App\Entities\Users\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the user.
     *
     * @param  \App\Entities\Users\User  $authUser
     * @param  \App\Entities\Users\User  $userObject
     * @return mixed
     */
    public function view(User $authUser, User $userObject)
    {
        // All authenticated user can see other user profile page.
        return true;
    }

    /**
     * Determine whether the user can create users.
     *
     * @param \App\Entities\Users\User $authUser
     * @param \App\Entities\Users\User $userObject
     *
     * @return mixed
     */
    public function create(User $authUser, User $userObject)
    {
        // Only admin who can create new user.
        return $authUser->isAdmin();
    }

    /**
     * Determine whether the user can update the user.
     *
     * @param \App\Entities\Users\User $authUser
     * @param \App\Entities\Users\User $userObject
     *
     * @return mixed
     */
    public function update(User $authUser, User $userObject)
    {
        // Currently only admin can edit user data.
        return $authUser->isAdmin();
    }

    /**
     * Determine whether the user can delete the user.
     *
     * @param \App\Entities\Users\User $authUser
     * @param \App\Entities\Users\User $userObject
     *
     * @return mixed
     */
    public function delete(User $authUser, User $userObject)
    {
        return false;
        // Currently only admin can delete user data.
        // $getUserDependenciesCount = $this->getUserDependenciesCount($userObject->id);

        // return $authUser->isAdmin() && $getUserDependenciesCount == 0;
    }

    // private function getUserDependenciesCount($userId)
    // {
    //     $userDependenciesCount = 0;

    //     if ($count = DB::table('receipts')->where(['creator_id' => $userId])->count()) {
    //         $userDependenciesCount += $count;
    //     }

    //     return $userDependenciesCount;
    // }
}
