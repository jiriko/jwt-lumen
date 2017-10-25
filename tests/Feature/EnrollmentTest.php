<?php

namespace Tests\Feature;

use App\Student;
use App\Subject;
use App\Enrollment;
use Tests\TestCase;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tymon\JWTAuth\Facades\JWTAuth;

class EnrollmentTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();
        $this->token = JWTAuth::fromUser(create('App\User'));
    }

    /** @test */
    function students_can_enroll_to_subjects()
    {
        $this->withExceptionHandling();

        $student = create(Student::class);
        $subject1 = create(Subject::class, ['name' => 'Chemistry']);

        $response = $this->json(
            'POST',
            'api/enrollments',
            ['student_id' => $student->id, 'subject_id' => $subject1->id],
            ['Authorization' => 'Bearer ' . $this->token . '']
        );

        $this->assertEquals(200, $response->response->getStatusCode());

        $this->assertEquals($subject1->id, $student->subjects->first()->id);
    }

    /** @test */
    function students_can_unenroll_a_subject()
    {
        $student = create(Student::class);
        $subject1 = create(Subject::class, ['name' => 'Chemistry']);
        $enrollment = Enrollment::create(['subject_id' => $subject1->id, 'student_id' => $student->id]);

        $this->assertEquals($subject1->id, $student->subjects->first()->id);

        $response = $this->json(
            'DELETE',
            'api/enrollments/' . $enrollment->id,
            [],
            ['Authorization' => 'Bearer ' . $this->token . '']
        );

        $this->assertEquals(200, $response->response->getStatusCode());

        $this->assertEmpty($student->fresh()->subjects);
    }
}
