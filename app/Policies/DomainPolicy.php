<?php

namespace App\Policies;

use App\Domain;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DomainPolicy extends Policy
{
    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Domain $domain)
    {
        return true;
    }

    public function create(User $user)
    {
        return false;
    }

    public function update(User $user, Domain $domain)
    {
        return false;
    }

    public function delete(User $user, Domain $domain)
    {
        return false;
    }

    public function restore(User $user, Domain $domain)
    {
        return false;
    }

    public function forceDelete(User $user, Domain $domain)
    {
        return false;
    }
}
