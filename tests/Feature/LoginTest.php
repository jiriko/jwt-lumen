<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Laravel\Lumen\Testing\DatabaseMigrations;

class LoginTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_login_requires_correct_and_valid_credentials()
    {
        $correctPassword = 'validPassword';
        $correctEmail = 'correct@email.com';
        $user = create('App\User', [
            'password' => app('hash')->make($correctPassword),
            'email' => $correctEmail
        ]);

        $this->json('POST', '/users/login', [
            'email' => 'invalidemail',
            'password' => ''
        ])->seeJson([
            'email' => ['The email must be a valid email address.'],
            'password' => ['The password field is required.']
        ]);

        $response = $this->call('POST', '/users/login', [
            'email' => $correctEmail,
            'password' => 'incorrectPassword'
        ]);

        $this->assertEquals(401, $response->status());

        $response = $this->call('POST', '/users/login', [
            'email' => 'incorrectEmail@gmail.com',
            'password' => $correctPassword
        ]);

        $this->assertEquals(401, $response->status());
    }


    /** @test */
    public function a_user_can_login_and_receive_a_jwt_token()
    {
        $user = create('App\User', ['password' => app('hash')->make('password123')]);

        $response = $this->call('POST',  '/users/login', [
            'email' => $user->email,
            'password' => 'password123'
        ]);

        $this->assertTokenInBodyAndHeader($response);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function a_token_can_be_refreshed()
    {
        $this->signIn();
        $token = JWTAuth::fromUser(auth()->user());

        $response = $this->json('POST', '/users/refresh-token', [], ['Authorization' => 'Bearer ' . $token . '']);

        $this->assertTokenInBodyAndHeader($response->response);

        $this->assertFalse($response->response->getOriginalContent()['token'] === $token);
    }

    /**
     * @param $response
     */
    protected function assertTokenInBodyAndHeader($response)
    {
        $this->assertArrayHasKey('token', $response->getOriginalContent());

        $this->assertNotEmpty($response->headers->get('authorization'));
    }
}
