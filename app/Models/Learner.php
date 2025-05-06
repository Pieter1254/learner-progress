<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Learner extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fullName',
        'email',
        // Add other relevant fields
    ];

    /**
     * Get the enrollments for the learner.
     */
    public function enrolments()
    {
        return $this->hasMany(Enrolment::class);
    }
}