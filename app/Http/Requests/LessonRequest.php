<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Lesson request
 * @package App\Http\Requests
 * @author Alexander Kalksov <munlightshadow@gmail.com>
 */
class LessonRequest extends FormRequest
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
                    'title' => 'required|string|max:255',
                    'description' => 'required|string|max:1000',
                ];

            case 'PUT':
                return [
                    'title' => [
                        'string',
                        'max:255',
                    ],
                    'description' => 'string|max:1000',
                ];
        }
    }
}
