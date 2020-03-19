<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatePostValidate extends FormRequest
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
                'alignment'=> ['required',Rule::in(['left','center','right'])],
                'color' => 'required|min:1|max:1',
                'caption'=>'string|max:140',
                'text_location'=>'string|max:30',
                    'longitude'=>'string',
                    'latitude'=>'string',
            ];
        if(request()->type == "DOODLE")
            return [
            
                'media_url' => 'required|image|mimes:png,bmp|max:6096',
                'type' => ['required', Rule::in(['DOODLE'])],
                'caption'=>'string|max:140|',
                'text_location'=>'string|max:30',
                    'longitude'=>'string',
                    'latitude'=>'string',
            ];
          if(request()->type == "IMAGE")
            return [
            
                'media_url' => 'required|image|mimes:png,jpeg,jpg,gif|max:8096',
                'type' => ['required', Rule::in(['IMAGE'])],
                'caption'=>'string|max:140',
                'text_location'=>'string|max:30',
                    'longitude'=>'string',
                    'latitude'=>'string',
            ];  

             if(request()->type == "VIDEO")
            return [
            
                'media_url' => 'required|mimes:mp4,3gp,gif|',
                'type' => ['required', Rule::in(['VIDEO'])],
                'caption'=>'string|max:140',
                'text_location'=>'string|max:30',
                    'longitude'=>'string',
                    'latitude'=>'string',
            ];  
             if(request()->type == "AUDIO")
            return [
            
                'media_url' => 'required|mimes:mp3,wav|',
                'type' => ['required', Rule::in(['VIDEO'])],
                'caption'=>'string|max:140',
                'text_location'=>'string|max:30',
                    'longitude'=>'string',
                    'latitude'=>'string',
            ];  
            
         else
            return[
                'type'=> ['required',Rule::in(config('constants.enums.post_type'))]
            ] ;
         
              
    }
}
