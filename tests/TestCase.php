<?php

namespace Tests;

use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function getAuthHeader(User $user)
    {
        return ['HTTP_Authorization' => 'Basic ' . base64_encode($user->email . ':password'),];
    }
}
