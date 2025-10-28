<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StructureRequest extends FormRequest
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
            'department_id'             => 'required',
            'job_code_id'               => 'nullable',
            'parent_id'                 => 'nullable',
            'position_code_structure'   => 'nullable',
            'structure_type'            => 'nullable',
            'name'                      => 'required',
            'quota'                     => 'required',
        ];
    }
}
