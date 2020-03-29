<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Lesson request
 * @package App\Http\Requests
 * @author Alexander Kalksov <munlightshadow@gmail.com>
 */
class StudentRequest extends FormRequest
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
        switch ($this->getMethod()) {
            case 'GET':
                return [
                    'sort' => 'in:id,title,description',
                    'order' => 'in:asc,desc',
                    'countOnPage' => 'integer|min:1',
                    'page' => 'integer|min:1'
                ];
            case 'POST':
                return [
                    'name' => 'required|string|max:50',
                    'last_name' => 'required|string|max:50',
                    'phone' => 'required|string|max:15|phone_number',
                    'email' => 'string|max:1000|email',
                ];

            case 'PUT':
                return [
                    'name' => [
                        'string',
                        'max:50',
                    ],
                    'last_name' => 'string|max:50',
                    'phone' => 'string|max:15|phone_number',
                    'email' => 'string|max:100|email',
                ];
            case 'DELETE':
                return [];                
        }
    }
}