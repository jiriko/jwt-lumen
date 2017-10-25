<?php

namespace App\Http\Controllers;

use App\Student;
use Illuminate\Http\Request;
use App\Filters\StudentFilters;
use App\Http\Resources\Student as StudentResource;

/**
 * @Resource("Students")
 */
class StudentsController extends Controller
{
    /**
     * Show students
     *
     * Get a JSON representation of all students
     *
     * @GET("/students{?query,sortBy,sort}")
     */
    public function index(StudentFilters $filters)
    {
        return StudentResource::collection(
            Student::fetch($filters)
        );
    }

    /**
     * Create student
     *
     * Creates a new student with a name and email.
     *
     * @Request({"name": "John Doe", "email": "johndoe@email.com"})
     * @POST("/students")
     */
    public function store()
    {
        $this->validate(request(), [
            'name' => 'required',
            'email' => 'required|email|unique:students'
        ]);

        $student = Student::create([
            'name' => request('name'),
            'email' => request('email')
        ]);

        return new StudentResource($student);
    }

    /**
     * Update student
     *
     * updates a student's data -- name, email.
     *
     * @Request({"name": "John Doe", "email": "johndoe@email.com"})
     * @PUT("/students/{id}")
     */
    public function update($id)
    {
        $student = Student::findOrFail($id);

        $this->validate(request(), [
            'email' => 'required|email|unique:students,email,' . $student->id,
            'name' => 'required'
        ]);

        $student->update([
            'email' => request('email'),
            'name' => request('name')
        ]);

        return response([]);
    }

    /**
     * Delete student
     *
     * Deletes the student record permanently.
     *
     * @DELETE("/students/{id}")
     */
    public function destroy($id)
    {
        Student::findOrFail($id)->delete();

        return response([]);
    }
}
