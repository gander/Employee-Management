<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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

    /**
     * @group Authentication
     * 
     * Forgot password
     * 
     * Sends a password reset token for the employee. The token is valid for 60 minutes.
     * 
     * @bodyParam email string required Employee's email address. Example: john@example.com
     * 
     * @response 200 scenario="success" {
     *   "message": "Password reset token has been sent. Please check your email.",
     *   "token": "abcdef1234567890abcdef1234567890abcdef1234567890abcdef1234567890"
     * }
     * 
     * @response 422 scenario="validation error" {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "email": ["No employee found with this email address."]
     *   }
     * }
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $email = $request->validated()['email'];
        
        // Delete any existing tokens for this email
        DB::table('password_reset_tokens')->where('email', $email)->delete();
        
        // Generate a new token
        $token = Str::random(64);
        
        // Store the token
        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);
        
        // In a real application, you would send this token via email
        // For this demo, we'll return it in the response
        return response()->json([
            'message' => 'Password reset token has been sent. Please check your email.',
            'token' => $token
        ]);
    }

    /**
     * @group Authentication
     * 
     * Reset password
     * 
     * Resets the employee's password using the provided token. The token must be valid and not expired (60 minutes).
     * 
     * @bodyParam email string required Employee's email address. Example: john@example.com
     * @bodyParam token string required Password reset token received via email. Example: abcdef1234567890abcdef1234567890abcdef1234567890abcdef1234567890
     * @bodyParam password string required New password (minimum 6 characters). Example: newpassword123
     * @bodyParam password_confirmation string required Password confirmation (must match password). Example: newpassword123
     * 
     * @response 200 scenario="success" {
     *   "message": "Password has been successfully reset."
     * }
     * 
     * @response 400 scenario="invalid token" {
     *   "message": "Invalid or expired password reset token."
     * }
     * 
     * @response 422 scenario="validation error" {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "password": ["Password confirmation does not match."],
     *     "token": ["Reset token is required."]
     *   }
     * }
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        // Find the token record
        $tokenRecord = DB::table('password_reset_tokens')
            ->where('email', $validated['email'])
            ->first();
        
        if (!$tokenRecord) {
            return response()->json([
                'message' => 'Invalid or expired password reset token.'
            ], 400);
        }
        
        // Check if token is valid
        if (!Hash::check($validated['token'], $tokenRecord->token)) {
            return response()->json([
                'message' => 'Invalid or expired password reset token.'
            ], 400);
        }
        
        // Check if token is not expired (60 minutes)
        $tokenCreated = \Carbon\Carbon::parse($tokenRecord->created_at);
        $tokenAge = $tokenCreated->diffInMinutes(now());
        if ($tokenAge > 60) {
            // Delete expired token
            DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();
            
            return response()->json([
                'message' => 'Invalid or expired password reset token.'
            ], 400);
        }
        
        // Update employee password
        $employee = Employee::where('email', $validated['email'])->first();
        $employee->password = Hash::make($validated['password']);
        $employee->save();
        
        // Delete the used token
        DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();
        
        return response()->json([
            'message' => 'Password has been successfully reset.'
        ]);
    }
}
