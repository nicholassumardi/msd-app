<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'name'                                  => 'nullable|string|max:255',
            'identity_card'                         => 'nullable|string|max:255',
            'date_of_birth'                         => 'nullable|date',
            'gender'                                => 'nullable|string',
            'religion'                              => 'nullable|string',
            'education'                             => 'nullable|string',
            'marital_status'                        => 'nullable|string',
            'phone'                                 => 'nullable|string',
            'address'                               => 'nullable|string',
            'company_id'                            => 'nullable',
            'department_id'                         => 'nullable',
            'employee_type'                         => 'nullable|string',
            'section'                               => 'nullable|string',
            'position_code'                         => 'nullable|string',
            'schedule_type'                         => 'nullable|string',
            'status_twiji'                          => 'nullable|string',
            'join_date'                             => 'nullable|date',
            'leave_date'                            => 'nullable|date',
            'userEmployeeNumbers.*.id'              => 'nullable',
            'userEmployeeNumbers.*.employee_number' => 'nullable|string',
            'userEmployeeNumbers.*.registry_date'   => 'nullable|date',
            'userCertificates.*.id'                 => 'nullable',
            'userCertificates.*.certificate_id'     => 'nullable',
            'userCertificates.*.certificate_name'   => 'nullable',
            'userCertificates.*.description'        => 'nullable',
            'userCertificates.*.expiration_date'    => 'nullable|date',
            'status'                                => 'nullable|integer',
        ];
    }
}
