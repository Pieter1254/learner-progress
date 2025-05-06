<?php

namespace App\Http\Controllers;

use App\Models\Learner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LearnerProgressController extends Controller
{
    public function index(Request $request)
    {
        try {
            $courseFilter = $request->query('course');
            $sortDirection = $request->query('sort', 'asc');

            $learnersQuery = Learner::with(['enrolments.course']);

            if ($courseFilter) {
                $learnersQuery->whereHas('enrolments.course', function ($query) use ($courseFilter) {
                    $query->where('name', 'like', '%' . $courseFilter . '%');
                });
            }

            $learners = $learnersQuery->get()
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

            if (in_array($sortDirection, ['asc', 'desc'])) {
                $learners = $learners->sortBy(function ($learner) {
                    return $learner['enrollments'][0]['progress'] ?? 0;
                }, SORT_REGULAR, $sortDirection === 'desc')->values();
            }

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
