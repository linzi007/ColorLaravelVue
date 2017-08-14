<?php

namespace App\Http\Requests;

class DriverRequest extends Request
{
    public function rules()
    {
        switch($this->method())
        {
            // CREATE
            case 'POST':
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'name' => 'string|required|max:20',
                    'mobile' => 'string|required|digits:11',
                    'code' => 'string|max:20',
                    'description' => 'string|nullable|max:200'
                ];
            }
            case 'GET':
            case 'DELETE':
            default:
            {
                return [];
            }
        }
    }

    public function messages()
    {
        return [
            // Validation messages
        ];
    }
}
