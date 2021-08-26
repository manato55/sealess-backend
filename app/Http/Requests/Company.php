<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



class Company extends FormRequest
{

    private $label;

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
    public function rules(Request $request)
    {
        $this->label = $request->label;

        $route = $this->route()->getName();

        $rules = [];

        switch ($route) {
            case 'companyRegister':
                $rules['name'] = ['required', 'max:30'];
                $rules['email'] = ['required', 'email', 'max:100'];
                break;
            case 'officalAdminRegistry':
                $rules['name'] = ['required', 'max:30'];
                $rules['password'] = ['required','min:8'];
                break;
            case 'sectionRegistry':
                $rules['name'] = [
                    'required',
                    'max:30',
                    Rule::unique('sections')->where(function ($q) use($request) {
                        return $q->where('department_id', $request->department);
                    })
                ];
                $rules['department'] = ['required'];
                break;
            case 'departmentRegistry':
                $rules['name'] = [
                    'required',
                    'max:30',
                    Rule::unique('departments')->where(function ($q) {
                        return $q->where('company_id', Auth::user()->company_id);
                    })
                ];
                break;
            case 'jobTitleRegistry':
                $rules['name'] = [
                    'required',
                    'max:30',
                    Rule::unique('job_titles')->where(function ($q) {
                        return $q->where('company_id', Auth::user()->company_id);
                    })
                ];
                break;
            case 'changeDepName':
                $rules['name'] = [
                    'required',
                    'max:30',
                    Rule::unique('departments')->where(function ($q) {
                        return $q->where('company_id', Auth::user()->company_id);
                    })
                ];
                break;
            case 'changeSecName':
                $rules['name'] = [
                    'required',
                    'max:30',
                    Rule::unique('sections')->where(function ($q) use($request) {
                        return $q->where('department_id', $request->department_id);
                    })
                ];
                break;
        }

        return $rules;
    }

    protected function failedValidation(Validator $validator)
    {
        $response['errors']  = $validator->errors()->toArray();

        throw new HttpResponseException(
            response()->json( $response, 422 )
        );
    }

    public function messages()
    {
        return [
            'name.unique' => "{$this->label}が既に使用されています。",
            'name.required' => "{$this->label}を入力してください。",
            'name.max' => "{$this->label}は30文字以内で入力してください。",
            'department.required' => '部を選択してください。',
            'email.required' => 'メールアドレスを入力してください。',
            'email.unique' => 'メールアドレスが既に使用されています。',
            'email.email' => 'メールアドレス以外が入力されています。',
            'email.max' => 'メールアドレスは100文字以内で入力してください。',
            'password.required' => 'パスワードを入力してください。',
            'password.min' => 'パスワードは8文字以上入力してください。',
        ];
    }
}
