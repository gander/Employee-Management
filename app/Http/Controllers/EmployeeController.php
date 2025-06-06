<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\QueryBuilder\QueryBuilder;

class EmployeeController extends Controller
{
    /**
     * @group Employees
     * 
     * List employees
     * 
     * Returns a paginated list of all employees with filtering and sorting capabilities.
     * 
     * @queryParam filter[full_name] string Filter by full name. Example: John Doe
     * @queryParam filter[email] string Filter by email address. Example: john@example.com
     * @queryParam filter[position] string Filter by position. Example: front-end
     * @queryParam filter[is_active] boolean Filter by active status. Example: true
     * @queryParam sort string Sort results. Available fields: full_name, email, position, created_at. Example: -created_at
     * @queryParam page[number] integer Page number. Example: 1
     * @queryParam page[size] integer Items per page (max 100). Example: 15
     * 
     * @response 200 scenario="success" {
     *   "data": [
     *     {
     *       "id": 1,
     *       "full_name": "John Doe",
     *       "email": "john@example.com",
     *       "phone": "+48123456789",
     *       "position": "front-end",
     *       "average_annual_salary": "75000.00",
     *       "is_active": true,
     *       "created_at": "2024-01-01T00:00:00.000000Z",
     *       "updated_at": "2024-01-01T00:00:00.000000Z"
     *     }
     *   ],
     *   "links": {
     *     "first": "http://localhost/api/employees?page[number]=1",
     *     "last": "http://localhost/api/employees?page[number]=5",
     *     "prev": null,
     *     "next": "http://localhost/api/employees?page[number]=2"
     *   },
     *   "meta": {
     *     "current_page": 1,
     *     "from": 1,
     *     "last_page": 5,
     *     "per_page": 15,
     *     "to": 15,
     *     "total": 75
     *   }
     * }
     */
    public function index()
    {
        return QueryBuilder::for(Employee::class)
            ->allowedFilters([
                'full_name',
                'email', 
                'position',
                'is_active'
            ])
            ->allowedSorts([
                'full_name',
                'email',
                'position', 
                'created_at'
            ])
            ->select([
                'id',
                'full_name',
                'email',
                'phone',
                'position',
                'average_annual_salary',
                'is_active',
                'created_at',
                'updated_at'
            ])
            ->jsonPaginate();
    }

    /**
     * @group Employees
     * 
     * Create new employee
     * 
     * Creates a new employee with complete address information. Handles both residential and correspondence addresses in a single request.
     * 
     * @authenticated
     * 
     * @bodyParam full_name string required Employee's full name. Example: John Doe
     * @bodyParam email string required Employee's email address (must be unique). Example: john.doe@example.com
     * @bodyParam phone string optional Employee's phone number. Example: +48123456789
     * @bodyParam average_annual_salary number optional Employee's annual salary. Example: 75000.50
     * @bodyParam position string required Employee's position. Must be one of: front-end, back-end, pm, designer, tester. Example: front-end
     * @bodyParam password string required Employee's password (minimum 6 characters). Example: password123
     * @bodyParam residential_address_country string required Residential address country. Example: Poland
     * @bodyParam residential_address_postal_code string required Residential address postal code. Example: 00-123
     * @bodyParam residential_address_city string required Residential address city. Example: Warsaw
     * @bodyParam residential_address_house_number string required Residential address house number. Example: 15A
     * @bodyParam residential_address_apartment_number string optional Residential address apartment number. Example: 5
     * @bodyParam different_correspondence_address boolean required Whether correspondence address is different from residential. Example: true
     * @bodyParam correspondence_address_country string optional Correspondence address country (required if different_correspondence_address is true). Example: Germany
     * @bodyParam correspondence_address_postal_code string optional Correspondence address postal code (required if different_correspondence_address is true). Example: 10115
     * @bodyParam correspondence_address_city string optional Correspondence address city (required if different_correspondence_address is true). Example: Berlin
     * @bodyParam correspondence_address_house_number string optional Correspondence address house number (required if different_correspondence_address is true). Example: 22
     * @bodyParam correspondence_address_apartment_number string optional Correspondence address apartment number. Example: 10
     * @bodyParam is_active boolean optional Whether employee is active (defaults to false). Example: true
     * 
     * @response 201 scenario="success" {
     *   "message": "Employee created successfully",
     *   "employee": {
     *     "id": 1,
     *     "full_name": "John Doe",
     *     "email": "john.doe@example.com",
     *     "phone": "+48123456789",
     *     "position": "front-end",
     *     "average_annual_salary": "75000.50",
     *     "residential_address_country": "Poland",
     *     "residential_address_postal_code": "00-123",
     *     "residential_address_city": "Warsaw",
     *     "residential_address_house_number": "15A",
     *     "residential_address_apartment_number": "5",
     *     "different_correspondence_address": true,
     *     "correspondence_address_country": "Germany",
     *     "correspondence_address_postal_code": "10115",
     *     "correspondence_address_city": "Berlin",
     *     "correspondence_address_house_number": "22",
     *     "correspondence_address_apartment_number": "10",
     *     "is_active": true,
     *     "created_at": "2024-01-01T00:00:00.000000Z",
     *     "updated_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     * 
     * @response 422 scenario="validation error" {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "email": ["This email address is already registered."],
     *     "position": ["Position must be one of: front-end, back-end, pm, designer, tester."]
     *   }
     * }
     */
    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $validatedData['password'] = Hash::make($validatedData['password']);
        $validatedData['is_active'] = $validatedData['is_active'] ?? false;

        $employee = Employee::create($validatedData);

        return response()->json([
            'message' => 'Employee created successfully',
            'employee' => $employee->fresh()
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
