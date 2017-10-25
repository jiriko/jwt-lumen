<?php

namespace Tests\Feature;

use App\Student;
use App\Subject;
use App\Enrollment;
use Tests\TestCase;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tymon\JWTAuth\Facades\JWTAuth;

class StudentTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();
        $this->token = JWTAuth::fromUser(create('App\User'));
    }

    /** @test */
    function a_user_can_create_a_student()
    {
        $student = [
            'name' => 'Jiriko Lapa',
            'email' => 'jiriko@yahoo.com'
        ];

        $response = $this->json(
            'POST',
            '/api/students',
            $student,
            ['Authorization' => 'Bearer ' . $this->token . '']
        );

        $this->assertEquals(201, $response->response->getStatusCode());

        $this->assertEquals(Student::first()->name, $student['name']);
    }

    /** @test */
    function a_user_can_update_a_student()
    {
        $student = create(Student::class);
        $newName = 'New Name';

        $code = $this->json(
            'PUT',
            '/api/students/' . $student->id,
            [
                'name' => $newName,
                'email' => $student->email
            ],
            ['Authorization' => 'Bearer ' . $this->token . '']
        )->response->getStatusCode();

        $this->assertEquals(200, $code);

        $this->assertEquals($newName, $student->fresh()->name);
    }

    /** @test */
    function a_user_can_delete_a_student()
    {
        $student = create(Student::class);

        $code  = $this->json(
            'DELETE',
            '/api/students/' . $student->id,
            [],
            ['Authorization' => 'Bearer ' . $this->token . '']
        )->response->getStatusCode();

        $this->assertEquals(200, $code);

        $this->assertEmpty(Student::find($student->id));
    }

    /** @test */
    function a_user_can_fetch_all_students()
    {
        $student1 = create(Student::class);
        $student2 = create(Student::class);
        $subject = create(Subject::class, ['name' => 'Math 5']);
        Enrollment::create(['student_id' => $student1->id, 'subject_id' => $subject->id]);

        $data = $this->json(
            'GET',
            '/api/students',
            [],
            ['Authorization' => 'Bearer ' . $this->token . '']
        )->response->getOriginalContent()['data'];

        $this->assertCount(2, $data);
        $this->assertCount(1, $data[0]['subjects']);
    }

    /** @test */
    function a_user_can_fetch_pagination_meta()
    {
        $student1 = create(Student::class);
        $student2 = create(Student::class);

        $response = $this->json(
            'GET',
            '/api/students',
            [],
            ['Authorization' => 'Bearer ' . $this->token . '']
        )->response->getOriginalContent();

        $this->assertArrayHasKey('links', $response);
        $this->assertArrayHasKey('meta', $response);
    }
}
