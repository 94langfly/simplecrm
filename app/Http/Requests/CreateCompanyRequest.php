<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class CreateCompanyRequest extends FormRequest
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
        return [
            'company_name' => [
                'required', 
                'max:255', 
                Rule::unique('company', 'name'),
            ],
            'company_email' => [
                'required', 
                'email',
                'max:255', 
                Rule::unique('company', 'email'),
            ],
            'company_phone' => [
                'required', 
                'numeric', 
                'digits_between:10,15',
                Rule::unique('company', 'phone'),
            ],
            'email' => [
                'required',
                'email', 
                'max:255', 
                Rule::unique('users'),
            ],
            'password' => [
                'required', 
                'max:30', 
                'min:8', 
            ],
            'name' => [
                'required', 
                'max:255', 
            ],
            'phone' => [
                'required', 
                'numeric', 
                'digits_between:10,15',
                Rule::unique('employee'),
            ], 
            'address' => 'required',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $user = $this->user(); 
        // role 1 = super user
        if ($user->role_id != 1) {
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
