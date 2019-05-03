<?php

namespace Tests;

use App\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function asUser()
    {
        return $this->be(factory(User::class)->create(), 'api');
    }

    public function asAdmin()
    {
        return $this->be(factory(User::class)->state('admin')->create(), 'api');
    }
}
