<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LearnerProgressRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'course' => [
                'nullable',
                'string',
                'max:255'
            ],
            'sort' => [
                'nullable',
                'string',
                Rule::in(['asc', 'desc'])
            ]
        ];
    }

    public function messages()
    {
        return [
            'course.string' => 'The course filter must be a string.',
            'course.max' => 'The course filter may not be greater than 255 characters.',
            'sort.string' => 'The sort direction must be a string.',
            'sort.in' => 'The sort direction must be either "asc" or "desc".',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'course' => $this->query('course'),
            'sort' => strtolower($this->query('sort', 'asc'))
        ]);
    }
}
