<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Learner;

class LearnerProgressController extends Controller
{
    public function index()
    {
        $learners = Learner::with(['enrolments.course'])
            ->get()
            ->map(function ($learner) {
                return [
                    'id' => $learner->id,
                    'fullName' => $learner->fullName,
                    'enrollments' => $learner->enrolments->map(function ($enrolment) {
                        return [
                            'courseId' => $enrolment->course_id,
                            'courseName' => $enrolment->course->name,
                            'progress' => (float) $enrolment->progress,
                        ];
                    })->toArray()
                ];
            });
        
        return response()->json([
            'learners' => $learners
        ]);
    }
    
}
