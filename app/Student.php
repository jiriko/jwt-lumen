<?php

namespace App;

use App\Filters\StudentFilters;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Student extends BaseModel
{
    public function subjects()
    {
        return $this->belongsToMany(Subject::class,'enrollments')->withPivot('id');
    }

    public static function fetch(StudentFilters $filters)
    {
        return static::with('subjects')
            ->filter($filters)
            ->paginate(5);
    }
}
