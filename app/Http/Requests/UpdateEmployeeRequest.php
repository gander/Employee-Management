<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $employeeId = $this->route('employee');
        
        return [
            'full_name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'email',
                Rule::unique('employees', 'email')->ignore($employeeId)
            ],
            'phone' => 'sometimes|nullable|string|max:20',
            'average_annual_salary' => 'sometimes|nullable|numeric|decimal:0,2',
            'position' => 'sometimes|required|in:front-end,back-end,pm,designer,tester',
            'password' => 'sometimes|string|min:6',
            
            // Residential address (optional in update)
            'residential_address_country' => 'sometimes|required|string|max:255',
            'residential_address_postal_code' => 'sometimes|required|string|max:20',
            'residential_address_city' => 'sometimes|required|string|max:255',
            'residential_address_house_number' => 'sometimes|required|string|max:20',
            'residential_address_apartment_number' => 'sometimes|nullable|string|max:20',
            
            // Correspondence address flag
            'different_correspondence_address' => 'sometimes|required|boolean',
            
            // Correspondence address (conditional)
            'correspondence_address_country' => 'required_if:different_correspondence_address,true|string|max:255',
            'correspondence_address_postal_code' => 'required_if:different_correspondence_address,true|string|max:20',
            'correspondence_address_city' => 'required_if:different_correspondence_address,true|string|max:255',
            'correspondence_address_house_number' => 'required_if:different_correspondence_address,true|string|max:20',
            'correspondence_address_apartment_number' => 'sometimes|nullable|string|max:20',
            
            'is_active' => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'full_name.required' => 'Full name is required.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'position.required' => 'Position is required.',
            'position.in' => 'Position must be one of: front-end, back-end, pm, designer, tester.',
            'password.min' => 'Password must be at least 6 characters.',
            'average_annual_salary.numeric' => 'Average annual salary must be a number.',
            'average_annual_salary.decimal' => 'Average annual salary can have maximum 2 decimal places.',
            
            'residential_address_country.required' => 'Residential address country is required.',
            'residential_address_postal_code.required' => 'Residential address postal code is required.',
            'residential_address_city.required' => 'Residential address city is required.',
            'residential_address_house_number.required' => 'Residential address house number is required.',
            
            'different_correspondence_address.required' => 'Please specify if correspondence address is different.',
            'different_correspondence_address.boolean' => 'Different correspondence address must be true or false.',
            
            'correspondence_address_country.required_if' => 'Correspondence address country is required when different address is specified.',
            'correspondence_address_postal_code.required_if' => 'Correspondence address postal code is required when different address is specified.',
            'correspondence_address_city.required_if' => 'Correspondence address city is required when different address is specified.',
            'correspondence_address_house_number.required_if' => 'Correspondence address house number is required when different address is specified.',
        ];
    }
}
