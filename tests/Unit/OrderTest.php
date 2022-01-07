<?php

namespace Tests\Unit;

use Tests\TestCase;

class OrderTest extends TestCase
{
    public function test_successful_and_failed_order()
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

        $response = $this->json('POST', 'api/login', $userData, ['Accept' => 'application/json'])->getOriginalContent();
        $access_token = $response['access_token'];

        $this->seed();

        $orderData = [
            "product_id" => "1",
            "quantity" => "2"
        ];

        $this->json('POST', 'api/order', $orderData, [
            'Accept' => 'application/json',
            'Authorization' => "Bearer $access_token"
        ])
        ->assertStatus(201)
        ->assertJson([
            "message" => "You have successfully ordered this product."
        ]);

        $failedOrderData = [
            "product_id" => "2",
            "quantity" => "9999"
        ];

        $this->json('POST', 'api/order', $failedOrderData, [
            'Accept' => 'application/json',
            'Authorization' => "Bearer $access_token"
        ])
        ->assertStatus(400)
        ->assertJson([
            "message" => "Failed to order this product due to unavailability of the stock."
        ]);
    }
}
