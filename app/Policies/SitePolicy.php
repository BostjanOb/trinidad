<?php

namespace App\Policies;

use App\Site;
use App\User;

class SitePolicy extends Policy
{
    public function index(User $user)
    {
        return true;
    }

    public function view(User $user, Site $server)
    {
        return true;
    }

    public function create(User $user)
    {
        return false;
    }

    public function update(User $user, Site $server)
    {
        return false;
    }

    public function delete(User $user, Site $server)
    {
        return false;
    }
}
