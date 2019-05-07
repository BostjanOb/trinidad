<?php

namespace App\Policies;

use App\User;
use App\Server;

class ServerPolicy extends Policy
{
    public function index(User $user)
    {
        return true;
    }

    public function view(User $user, Server $server)
    {
        return true;
    }

    public function create(User $user)
    {
        return false;
    }

    public function update(User $user, Server $server)
    {
        return false;
    }

    public function delete(User $user, Server $server)
    {
        return false;
    }
}