<?php

namespace App\Http\Controllers;

use App\Models\Learner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LearnerProgressController extends Controller
{
    public function index()
    {
        try {
            $learners = Learner::with(['enrolments.course'])
                ->get()
                ->map(function ($learner) {
                    return [
                        'id' => $learner->id,
                        'fullName' => $learner->fullName,
                        'enrollments' => $learner->enrolments->map(function ($enrolment) {
                            return [
                                'courseId' => $enrolment->course_id,
                                'courseName' => $enrolment->course->name ?? 'Unknown Course',
                                'progress' => (float) $enrolment->progress,
                            ];
                        })->toArray()
                    ];
                });
            
            return response()->json([
                'learners' => $learners,
                'status' => 'success'
            ]);
        } catch (\Exception $e) {
            Log::error('Learner Progress API error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching learner progress data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}