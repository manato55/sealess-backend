<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\contracts\Validation\Validator;


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
    public function rules()
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
                $rules['name'] = ['required', 'max:30'];
                $rules['password'] = ['required','min:8'];
                $rules['email'] = ['required', 'email', 'max:100', 'unique:users'];
                $rules['department'] = 'required';
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
