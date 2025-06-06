<?php

/*
 * Copyright (c) 2025 Adam Gąsowski
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkDeleteEmployeesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_ids' => ['required', 'array', 'min:1', 'max:100'],
            'employee_ids.*' => ['required', 'integer', 'min:1', 'exists:employees,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'employee_ids.required' => 'Employee IDs are required.',
            'employee_ids.array' => 'Employee IDs must be provided as an array.',
            'employee_ids.min' => 'At least one employee ID must be provided.',
            'employee_ids.max' => 'Maximum 100 employees can be deleted at once.',
            'employee_ids.*.required' => 'Each employee ID is required.',
            'employee_ids.*.integer' => 'Each employee ID must be an integer.',
            'employee_ids.*.min' => 'Each employee ID must be a positive number.',
            'employee_ids.*.exists' => 'One or more employee IDs do not exist.',
        ];
    }
}
