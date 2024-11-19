<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Http\Requests\EmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Interfaces\EmployeeRepositoryInterface;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    private EmployeeRepositoryInterface $employeeRepository;
    
    public function __construct(
        EmployeeRepositoryInterface $employeeRepository,
    )
    {
        $this->employeeRepository = $employeeRepository;
    }
    
    public function paginate(Request $request) 
    {
        $authUser = $request->user;
        $userEmployee = $authUser->load('employee');
        $company_id = ($userEmployee) ? $userEmployee->company_id : null;
        
        try {
            $getEmployee = $this->employeeRepository->paginate($request, $company_id);
            
            $getEmployee->load('user.role');
            
            $result = $getEmployee;
            
            return ApiResponseClass::sendResponse(
                EmployeeResource::collection($result)->response()->getData(true),
                'employee get detail successfully', 
                200
            );
        } catch (\Exception $e) {
            return ApiResponseClass::throw($e);
        }
        
    }
    
    public function store(EmployeeRequest $request)
    {
        $authUser = $request->user;
        $userEmployee = $authUser->load('employee');
        
        $dataEmployee = $request->only(
            'name',
            'phone',
            'address',
            'user_id',
        );
        
        $dataEmployee['company_id'] = $userEmployee->employee->company_id;
        
        $dataUser = [
            'role_id'   => 3, //$request->input('role_id'),
            'email'     => $request->input('email'),
            'password'  => Hash::make($request->input('password')),
        ];
        
        DB::beginTransaction();
        
        try {
            $user_id = User::create($dataUser)->id;
            
            $dataEmployee['user_id'] = $user_id;
            
            $employee = $this->employeeRepository->store($dataEmployee);
            
            $employee->load('user.role');
            
            DB::commit();
            
            $result = $employee;
            
            return ApiResponseClass::sendResponse(
                new EmployeeResource($result), 
                'employee created successfully', 
                200
            );
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e);
        }
    }
    
    public function update(EmployeeRequest $request, $id = null)
    {
        $authUser = $request->user;
        $userEmployee = $authUser->load('employee');
        if ($id == null) {
            $id = $userEmployee->employee->id;
        }
        
        $getEmployee = $this->employeeRepository->getById($id);
        
        if ($getEmployee == null OR ($userEmployee->employee != null AND $userEmployee->employee->company_id != $getEmployee->company_id)) {
            return ApiResponseClass::badrequest(
                'emloyee id not valid.', 
                404
            );
        }
        
        $dataEmployee = $request->only(
            'name',
            'phone',
            'address',
            'user_id',
        );
        
        $dataUser = [
            // 'role_id'   => 3, //$request->input('role_id'),
            'email'     => $request->input('email'),
        ];
        
        if ($request->input('password')) {
            $dataUser['password'] = Hash::make($request->input('password'));
        }
        
        DB::beginTransaction();
        
        try {
            User::where('id', $getEmployee->user_id)->update($dataUser);
            
            $getEmployee->update($dataEmployee);
            
            $getEmployee->refresh();
            
            $getEmployee->load('user.role');
            
            DB::commit();
            
            $result = $getEmployee;
            
            return ApiResponseClass::sendResponse(
                new EmployeeResource($result), 
                'employee updated successfully', 
                200
            );
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e);
        }
    }
    
    public function get(Request $request, $id = null)
    {
        $authUser = $request->user;
        $userEmployee = $authUser->load('employee');
        if ($id == null) {
            $id = $userEmployee->employee->id;
        }
        
        $getEmployee = $this->employeeRepository->getById($id);
        
        if ($getEmployee == null OR ($userEmployee->employee != null AND $userEmployee->employee->company_id != $getEmployee->company_id)) {
            return ApiResponseClass::badrequest(
                'emloyee id not valid.', 
                404
            );
        }
        
        $getEmployee->load('user.role');
        
        if ($authUser->role_id == 3 AND $getEmployee->user->role_id != 3) {
            return ApiResponseClass::badrequest('You are not authorized to perform this action.', 422);
        }
        
        $result = $getEmployee;
        
        
        return ApiResponseClass::sendResponse(
            new EmployeeResource($result), 
            'employee get detail successfully', 
            200
        );
    }
    
    public function delete(Request $request, $id)
    {
        $authUser = $request->user;
        $userEmployee = $authUser->load('employee');
        if ($authUser->role_id == 3) {
            return ApiResponseClass::badrequest('You are not authorized to perform this action.', 422);
        }
        
        $getEmployee = $this->employeeRepository->getById($id);
        
        $getEmployee->load('user');
        
        if ($getEmployee == null OR $getEmployee->user->role_id != 3 OR ($userEmployee->employee != null AND $userEmployee->employee->company_id != $getEmployee->company_id)) {
            return ApiResponseClass::badrequest(
                'emloyee id not valid.', 
                404
            );
        }
        
        try {
            User::where('id', $getEmployee->user_id)->delete();
            
            $getEmployee->delete();
            
            $result = null;
            
            return ApiResponseClass::sendResponse($result, 'employee deleted successfully' , 200);
        } catch (\Exception $e) {
            return ApiResponseClass::throw($e);
        }
    }
}
