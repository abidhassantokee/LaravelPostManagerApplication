<?php

namespace Tests;

use App\Models\V1\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Holds the jwt auth token
     * @var
     */
    protected $token;

    /**
     * Log the user in
     */
    protected function signIn()
    {
        User::factory()->create([
            'email' => 'test@test.com',
            'password' => 'test1234'
        ]);

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('POST', '/api/v1/auth/login', [
            'email' => 'test@test.com',
            'password' => 'test1234'
        ]);

        $content = json_decode($response->getContent());

        $this->token = $content->access_token;
    }
}
