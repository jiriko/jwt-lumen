<?php

namespace App\Http\Controllers;

use App\Enrollment;
use Illuminate\Http\Request;

class EnrollmentsController extends Controller
{

    /**
     * Create enrollment
     *
     * Creates a new subject enrollment for a student.
     *
     * @Request({"student_id": 1, "student_id": 2})
     * @POST("/enrollments")
     */
    public function store()
    {
        $this->validate(request(), [
            'student_id' => 'required',
            'student_id' => 'required'
        ]);

        $enrollment = Enrollment::create(
            [
                'student_id' => request('student_id'),
                'subject_id' => request('subject_id')
            ]
        );

        return response(['data' => $enrollment]);
    }

    /**
     * Delete enrollment
     *
     * Deletes the subject enrollment for a student permanently.
     *
     * @DELETE('/enrollments/{id}')
     */
    public function destroy($id)
    {
        $enrollment = Enrollment::findOrFail($id);
        $enrollment->delete();

        return response([]);
    }
}
