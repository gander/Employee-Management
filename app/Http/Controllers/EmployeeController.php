<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
