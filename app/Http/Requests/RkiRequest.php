<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class RkiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_structure_mapping_id' => ['nullable'],
            'ikw_id' => ['required', 'array', 'distinct'],
            'ikw_id.*' => [
                'required',
                function ($attribute, $value, $fail) {
                    $usmId = $this->user_structure_mapping_id ?? 0;

                    $exists = DB::table('your_table_name')
                        ->where('user_structure_mapping_id', $usmId)
                        ->where('ikw_id', $value ?? 0)
                        ->exists();

                    if ($exists) {
                        $fail("IKW already exist in this collection.");
                    }
                }
            ],
        ];
    }
}
