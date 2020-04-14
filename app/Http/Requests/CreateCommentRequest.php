<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateCommentRequest extends FormRequest
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
        if(request()->type == "TEXT")
            return [
            
                'text' => 'required_without:media_url',
                'media_url' => 'required_without:text',
                'type' => ['required', Rule::in(['TEXT'])],
            ];
        if(request()->type == "DOODLE")
            return [
            
                'media_url' => 'required|image|mimes:png,bmp|max:6096',
                'type' => ['required', Rule::in(['DOODLE'])],
                    
            ];
          if(request()->type == "IMAGE")
            return [
            
                'media_url' => 'required|image|mimes:png,jpeg,jpg,gif|max:8096',
                'type' => ['required', Rule::in(['IMAGE'])],
                
            ];  

             if(request()->type == "VIDEO")
            return [
            
                'media_url' => 'required|mimes:mp4,3gp,gif|',
                'type' => ['required', Rule::in(['VIDEO'])],
               
            ];  
             if(request()->type == "AUDIO")
            return [
            
                'media_url' => 'required|mimes:application/octet-stream,audio/mpeg,mpga,mp3,wav',
                'type' => ['required', Rule::in(['AUDIO'])],
               
            ];  
            
         else
            return[
                'type'=> ['required',Rule::in(config('constants.enums.post_type'))]
            ] ;
         
              
    }
}
