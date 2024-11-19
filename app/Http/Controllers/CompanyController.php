<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Http\Requests\CreateCompanyRequest;
use App\Interfaces\CompanyRepositoryInterface;
use App\Interfaces\EmployeeRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CompanyController extends Controller
{
    private CompanyRepositoryInterface $companyRepository;
    private EmployeeRepositoryInterface $employeeRepository;
    
    public function __construct(
        CompanyRepositoryInterface $companyRepository,
        EmployeeRepositoryInterface $employeeRepository,
    )
    {
        $this->companyRepository = $companyRepository;
        $this->employeeRepository = $employeeRepository;
    }
    
    public function store(CreateCompanyRequest $request)
    {
        
        $dataCompany = [
            'name' => $request->input('company_name'),
            'email' => $request->input('company_email'),
            'phone' => $request->input('company_phone'),
        ];
        
        $dataEmployee = $request->only(
            'name',
            'phone',
            'address',
            'user_id',
        );
        
        $dataUser = [
            'role_id'   => 2,
            'email'     => $request->input('email'),
            'password'  => Hash::make('nananina12345'),
        ];
        
        DB::beginTransaction();
        
        try {
            $user_id = User::create($dataUser)->id;
            
            $company = $this->companyRepository->store($dataCompany);
            
            $dataEmployee['company_id'] = $company->id;
            $dataEmployee['user_id'] = $user_id;
            
            $this->employeeRepository->store($dataEmployee);
            
            DB::commit();
            
            $result = $company;
            
            return ApiResponseClass::sendResponse($result, 'company created successfully' , 200);
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e);
        }
    }
}
