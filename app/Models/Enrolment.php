<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrolment extends Model
{
    use HasFactory;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'enrolments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'learner_id',
        'course_id',
        'progress',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'progress' => 'decimal:2',
    ];

    /**
     * Get the learner that owns the enrollment.
     */
    public function learner()
    {
        return $this->belongsTo(Learner::class);
    }

    /**
     * Get the course that this enrollment belongs to.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}