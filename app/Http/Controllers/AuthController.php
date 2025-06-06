<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * @group Authentication
     * 
     * Employee login
     * 
     * Authenticates an employee and returns an API token. Only active employees can log in.
     * 
     * @bodyParam email string required Employee's email address. Example: john@example.com
     * @bodyParam password string required Employee's password (minimum 6 characters). Example: password123
     * 
     * @response 200 scenario="success" {
     *   "message": "Login successful",
     *   "token": "1|abcdef1234567890abcdef1234567890abcdef1234567890abcdef1234567890",
     *   "employee": {
     *     "id": 1,
     *     "full_name": "John Doe",
     *     "email": "john@example.com",
     *     "phone": "+48123456789",
     *     "position": "front-end",
     *     "average_annual_salary": "75000.00",
     *     "is_active": true,
     *     "created_at": "2024-01-01T00:00:00.000000Z",
     *     "updated_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     * 
     * @response 401 scenario="invalid credentials" {
     *   "message": "Invalid credentials"
     * }
     * 
     * @response 401 scenario="inactive employee" {
     *   "message": "Account is inactive. Please contact administrator."
     * }
     * 
     * @response 422 scenario="validation error" {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "email": ["Email address is required."],
     *     "password": ["Password is required."]
     *   }
     * }
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $employee = Employee::where('email', $request->email)->first();

        if (!$employee || !Hash::check($request->password, $employee->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        if (!$employee->is_active) {
            return response()->json([
                'message' => 'Account is inactive. Please contact administrator.'
            ], 401);
        }

        $token = $employee->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'employee' => [
                'id' => $employee->id,
                'full_name' => $employee->full_name,
                'email' => $employee->email,
                'phone' => $employee->phone,
                'position' => $employee->position,
                'average_annual_salary' => $employee->average_annual_salary,
                'is_active' => $employee->is_active,
                'created_at' => $employee->created_at,
                'updated_at' => $employee->updated_at,
            ]
        ]);
    }
}
