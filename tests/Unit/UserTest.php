<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic unit test example.
     */


    public function test_user_duplication()
    {
        $user1 = User::make([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 123456
        ]);

        $user2 = User::make([
            'name' => 'John Doe',
            'email' => 'johns@example.com',
            'password' => 123456
        ]);

        $this->assertTrue($user1->email != $user2->email);
    }
}
