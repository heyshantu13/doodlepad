<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserValidate extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

       
            return [
             
             'mobile' => 'required|string|min:10|max:10|unique:users',
            'fullname' => 'required|string|max:50',
            'password' => 'required|min:8|max:12',
           'username' => 'required|string|unique:users|alpha_dash|max:16'
         

        ];
    

        
    }

    public function message(){
        
    }
}
