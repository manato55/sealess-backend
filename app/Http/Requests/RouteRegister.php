<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class RouteRegister extends FormRequest
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
    public function rules(Request $request)
    {
        return [
            'label' => ['required', 'max:30', 'unique:routes,label,'.Auth::user()->id.',user_id'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response['error']  = $validator->errors()->toArray();

        throw new HttpResponseException(
            response()->json( $response, 422 )
        );
    }

    public function messages()
    {
        return [
            'label.required' => '登録名を入力してください。',
            'label.unique' => '入力した登録名を既に使用しています。',
            'label.max' => '登録名は30文字以内にしてください。',
        ];
    }


}
