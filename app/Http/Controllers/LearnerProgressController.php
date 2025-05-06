<?php

namespace App\Http\Controllers;

use App\Http\Requests\LearnerProgressRequest;
use App\Models\Learner;
use Illuminate\Support\Facades\Log;

class LearnerProgressController extends Controller
{
    public function index(LearnerProgressRequest $request)
    {
        try {
            $validated = $request->validated();
            $courseFilter = $validated['course'] ?? null;
            $sortDirection = $validated['sort'] ?? 'asc';

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
                        // average progress for sorting
                        if (empty($learner['enrollments'])) {
                            return 0;
                        }
                        $total = array_sum(array_column($learner['enrollments'], 'progress'));
                        return $total / count($learner['enrollments']);
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
