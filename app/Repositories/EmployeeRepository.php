<?php

namespace App\Repositories;

use App\Models\Employee;

use App\Interfaces\EmployeeRepositoryInterface;

class EmployeeRepository implements EmployeeRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    
    public function index(){
        return Employee::all();
    }

    public function getById($id){
       return Employee::find($id);
    }

    public function store(array $data){
       return Employee::create($data);
    }

    public function update(array $data,$id){
       return Employee::whereId($id)->update($data);
    }
    
    public function delete($id){
        Employee::destroy($id);
    }
    
    public function paginate($request, $company_id = null) {
        $query = Employee::with(['user.role']);

        if ($company_id) {
            $query->where('company_id', $company_id);
        }

        if ($request->user->role_id == 3) {
            // employee only
            $query->whereHas('user', function ($nest_query) {
                $nest_query->where('role_id', 3);
            });
        } else if ($request->user->role_id == 1) {
            // if super user need to know company from the user
            $query->with(['company']);
        }
        
        $search = $request->input('search');
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $sortBy = $request->input('sort_by', 'name'); 
        $sortDirection = $request->input('sort_direction', 'asc'); 
        $sortDirection = strtolower($sortDirection);
        
        if (in_array($sortBy, ['name', 'created_at']) AND in_array($sortDirection, ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortDirection);
        }

        return $query->paginate();
    }
}
