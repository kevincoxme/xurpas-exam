<?php

namespace Tests\Unit;

use Tests\TestCase;

class LoginTest extends TestCase
{
    public function test_successful_and_failed_login()
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

        $this->json('POST', 'api/login', $userData, ['Accept' => 'application/json'])
            ->assertStatus(201)
            ->assertJsonStructure([
                "access_token"
            ]);

        $userFailedData = [
            "email" => "backend123123@multicorp.com",
            "password" => "test121231233"
        ];

        $this->json('POST', 'api/login', $userFailedData, ['Accept' => 'application/json'])
            ->assertStatus(401)
            ->assertJson([
                "message" => "Invalid credentials."
            ]);
    }
}
