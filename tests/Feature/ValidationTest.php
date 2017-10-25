<?php

namespace Tests\Feature;

use App\User;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ValidationTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();

        $this->token = JWTAuth::fromUser(create('App\User'));
    }

    /** @test */
    function it_returns_200_if_field_is_valid()
    {
        $this->withExceptionHandling();
        $user = create(User::class);

        $status = $this->json(
            'GET',
            'api/validation?type=email,unique&t=1&q=' . $user->email . '&id=' . $user->id . '&field=email',
            [],
            ['Authorization' => 'Bearer ' . $this->token . '']
        )->response->getStatusCode();


        $this->assertEquals(200, $status);
    }

    /** @test */
    function it_returns_422_if_field_is_invalid()
    {
        $this->withExceptionHandling();
        $user = create(User::class);

        //a request with an email that already exists
        $status = $this->json(
            'GET',
            'api/validation?field=email&type=email,unique&t=1&q=' . $user->email,
            [],
            ['Authorization' => 'Bearer ' . $this->token . '']
        )->response->getStatusCode();

        $this->assertEquals(422, $status);
    }

    /** @test */
    function it_requires_field_key_when_validation_is_unique()
    {
        $this->withExceptionHandling();
        $user = create(User::class);

        //request without the field key
        $status = $this->json(
            'GET',
            'api/validation?type=email,unique&q=' . $user->email . '&t=1&id=' . $user->id,
            [],
            ['Authorization' => 'Bearer ' . $this->token . '']
        )->response->getStatusCode();

        $this->assertEquals(422, $status);
    }

    /** @test */
    function it_requires_table_key_when_validation_is_unique()
    {
        $this->withExceptionHandling();
        $user = create(User::class);

        //request without the field key
        $status = $this->json(
            'GET',
            'api/validation?type=email,unique&q=' . $user->email . '&id=' . $user->id . '&field=email',
            [],
            ['Authorization' => 'Bearer ' . $this->token . '']
        )->response->getStatusCode();

        $this->assertEquals(422, $status);
    }

}
