<?php

namespace Tests\Unit;

use Tests\TestCase;

class RegistrationTest extends TestCase
{
    public function test_successful_and_failed_registration()
    {
        $userData = [
            "email" => "backend@multicorp.com",
            "password" => "test123"
        ];

        $this->json('POST', 'api/register', $userData, ['Accept' => 'application/json'])
            ->assertStatus(201)
            ->assertJson([
                "message" => "User successfully registered."
            ]);

        $this->json('POST', 'api/register', $userData, ['Accept' => 'application/json'])
            ->assertStatus(400)
            ->assertJson([
                "message" => "Email already taken."
            ]);
    }
}
