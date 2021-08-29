<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;


class RegisterUserCheck extends FormRequest
{


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

        $route = $this->route()->getName();

        $rules = [];

        switch ($route) {
            case 'ordinary':
                $rules['section'] = 'required';
                $rules['jobTitle'] = 'required';
                $rules['name'] = ['required', 'max:30'];
                $rules['email'] = ['required', 'email', 'max:100', 'unique:users'];
                $rules['department'] = 'required';
                break;
            case 'passwordReRegister':
                $rules['password'] = ['required','min:8'];
                break;
            case 'officalRegistry':
                $rules['password'] = ['required','min:8'];
                break;
            case 'depAdmin':
                $rules['name'] = [
                    'required',
                    'max:30',
                    Rule::unique('users')->where(function ($q) {
                        return $q->where('company_id', Auth::user()->company_id);
                    })
                ];
                $rules['password'] = ['required','min:8'];
                $rules['email'] = [
                    'required',
                    'email',
                    'max:100',
                    Rule::unique('users')->where(function ($q) {
                        return $q->where('company_id', Auth::user()->company_id);
                    })
                ];
                $rules['department'] = 'required';
                break;
            case 'editDepUserInfo':
                $rules['name'] = [
                    'required',
                    'max:30',
                    Rule::unique('users')->where(function ($q) {
                        return $q->where('company_id', Auth::user()->company_id);
                    })
                ];
                $rules['email'] = ['required', 'email', 'max:100', 'unique:users,email,'.$request->userid.',id'];
                break;
            case 'changeDep':
                $rules['department'] = 'required';
                $rules['section'] = 'required';
                break;
            case 'changeDepAdminInfo':
                $rules['name'] = ['required', 'max:30', 'unique:users,name,'.$request->userid.',id'];
                $rules['email'] = ['required', 'email', 'max:100', 'unique:users,email,'.$request->userid.',id'];
                $rules['department'] = 'required';
                break;
            case 'companyRegister':
                $rules['name'] = ['required', 'max:30', 'unique:companies'];
                $rules['email'] = ['required', 'email', 'max:100', 'unique:companies'];
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
            'name.unique' => 'ユーザーネームが既に使用されています。',
            'name.required' => 'ユーザーネームを入力してください。',
            'name.max' => 'ユーザーネームは30文字以内で入力してください。',
            'department.required' => '部を選択してください。',
            'section.required' => '課を選択してください。',
            'jobTitle.required' => '役職を選択してください。',
            'email.required' => 'メールアドレスを入力してください。',
            'email.unique' => 'メールアドレスが既に使用されています。',
            'email.email' => 'メールアドレス以外が入力されています。',
            'email.max' => 'メールアドレスは100文字以内で入力してください。',
            'password.required' => 'パスワードを入力してください。',
            'password.min' => 'パスワードは8文字以上入力してください。',
        ];
    }
}
