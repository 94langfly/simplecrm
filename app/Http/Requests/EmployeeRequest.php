<?php

namespace App\Http\Requests;

use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class EmployeeRequest extends FormRequest
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
        $employee_id = @$this->route('id');
        $httpMethod  = $this->method();
        
        $user_id = 0;
        $roles_password = null;
        
        $user = $this->user(); 
        
        if ($employee_id) {
            $user->load('employee');
            
            $dataEmployeeQuery = Employee::where('id', $employee_id);
            
            if ($user->employee) {
                $dataEmployeeQuery->where('company_id', $user->employee->company_id);
            }
            $dataEmployee = $dataEmployeeQuery->first();
            if ($dataEmployee == null) {
                throw new HttpResponseException(response()->json([
                    'success'   => false,
                    'message'   => 'emloyee id not valid.',
                    'data'      => null
                ], 422));
            }
            
            $user_id = $dataEmployee->user_id;
        } else if ($httpMethod == 'POST') {
            $roles_password = [
                'required', 
                'max:30', 
                'min:8', 
            ];
        } else if ($httpMethod == 'PUT') {
            $user_id = $user->id;
            $user->load('employee');
            $employee_id = $user->employee->id;
        }
        
        
        
        $roles = [
            'email' => [
                'required', 
                'email',
                'max:255', 
                Rule::unique('users')->ignore($user_id),
            ],
            'name' => [
                'required', 
                'max:255', 
                // Rule::unique('employee')->ignore($employee_id),
            ],
            'phone' => [
                'required', 
                'numeric', 
                'digits_between:10,15',
                Rule::unique('employee')->ignore($employee_id),
            ], 
            'address' => 'required',
            // 'role_id' => [
            //     'required', 
            //     Rule::exists('roles', 'id')->whereNot('id', 1),
            // ],
        ];
        
        if ($roles_password) {
            $roles['password'] = $roles_password;
        }
        
        return $roles;
    }
    
    public function withValidator(Validator $validator)
    {
        $user = $this->user(); 
        // role 2 = manager only
        if ($user->role_id != 2) {
            throw new HttpResponseException(response()->json([
                'success'   => false,
                'message'   => 'You are not authorized to perform this action.',
                'data'      => null
            ], 422));
        } 
    }
    
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ], 422));
    }
}
