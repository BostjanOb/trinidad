<?php

namespace App\Policies;

use App\User;

class UserPolicy extends Policy
{
    public function index(User $user)
    {
        return false;
    }

    public function view(User $user, User $model)
    {
        return $user->id == $model->id;
    }

    public function create(User $user)
    {
        return false;
    }

    public function update(User $user, User $model)
    {
        return $user->id == $model->id;
    }

    public function delete(User $user, User $model)
    {
        return false;
    }
}
