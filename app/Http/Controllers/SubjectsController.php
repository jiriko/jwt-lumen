<?php

namespace App\Http\Controllers;

use App\Subject;
use App\Filters\SubjectFilters;
use App\Http\Resources\Subject as SubjectResource;

/**
 * @Resource("Subjects")
 */
class SubjectsController extends Controller
{
    /**
     * Show subjects
     *
     * Get a JSON representation of all subjects
     *
     * @GET("/subjects{?name}")
     */
    public function index(SubjectFilters $filters)
    {
        return SubjectResource::collection(
            Subject::filter($filters)->paginate(15)
        );
    }

    /**
     * Create subject
     *
     * Creates a new subject with a name
     *
     * @Request({"name": "Filipino 101"})
     * @POST("/subjects")
     */
    public function store()
    {
        $this->validate(request(), [
            'name' => 'required|unique:subjects'
        ]);

        $subject = Subject::create(
            [
                'name' => request('name')
            ]
        );

        return new SubjectResource($subject);
    }

    /**
     * Update subject
     *
     * updates a student's data -- name.
     *
     * @Request({"name": "Filipino 102"})
     * @PUT("/subjects/{id}")
     */
    public function update($id)
    {
        $subject = Subject::findOrFail($id);

        $this->validate(request(), [
            'name' => 'required|unique:subjects,name,' . $subject->id,
        ]);

        $subject->update([
            'name' => request('name')
        ]);

        return response([]);
    }

    /**
     * Delete subject
     *
     * Deletes the subject record permanently.
     *
     * @DELETE("/subjects/{id}")
     */
    public function destroy($id)
    {
        $subject = Subject::findOrFail($id);

        $subject->delete();

        return response([]);
    }
}
