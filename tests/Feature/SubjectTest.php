<?php

namespace Tests\Feature;

use App\Subject;
use Tests\TestCase;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tymon\JWTAuth\Facades\JWTAuth;

class SubjectTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();
        $this->token = JWTAuth::fromUser(create('App\User'));
    }

    /** @test */
    function a_user_can_fetch_all_subjects()
    {
        $subject = create(Subject::class, ['name' => 'Math 5']);
        $subject2 = create(Subject::class, ['name' => 'Math 3']);
        $subject3 = create(Subject::class, ['name' => 'Math 2']);

        $data = $this->json(
            'GET',
            '/api/subjects',
            [],
            ['Authorization' => 'Bearer ' . $this->token . '']
        )->response->getOriginalContent()['data'];

        $this->assertCount(3, $data);
        $this->assertEquals(
            [
                'id' => $subject->id,
                'name' => $subject->name
            ],
            $data[0]
        );
    }

    /** @test */
    function a_user_can_filter_for_subjects()
    {
        $subject = create(Subject::class, ['name' => 'Math 5']);
        $subject2 = create(Subject::class, ['name' => 'English 3']);
        $subject3 = create(Subject::class, ['name' => 'Mapeh 2']);

        $data = $this->json(
            'GET',
            '/api/subjects?name=Ma',
            [],
            ['Authorization' => 'Bearer ' . $this->token . '']
        )->response->getOriginalContent()['data'];

        $this->assertCount(2, $data);

        $this->assertNotContains(
            [
                'id' => $subject2->id,
                'name' => $subject2->name
            ],
            $data);

        $this->assertContains(
            [
                'id' => $subject3->id,
                'name' => $subject3->name
            ],
            $data
        );

        $this->assertContains(
            [
                'id' => $subject->id,
                'name' => $subject->name
            ],
            $data
        );
    }
}
