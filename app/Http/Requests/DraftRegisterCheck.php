<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\contracts\Validation\Validator;
use Illuminate\Http\Request;


class DraftRegisterCheck extends FormRequest
{

    private $cnt = 0;
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
        $rules = [
            'title' => ['required', 'max:30'],
            'content' => ['required'],
            'route' => ['required'],
            // 'file0' => ['required'],
        ];

        while(true) {
            $file = 'file'.$this->cnt;
            if (!isset($request->{$file})) {
                break;
            }
            // ファイルの中身が空(0byte)の場合バリデーションエラーとなる
            $rules[$file] = ['mimes:docx,pdf'];

            $this->cnt++;
        }

        return $rules;

    }

    protected function failedValidation(Validator $validator)
    {
        $response['errors']  = $validator->errors()->toArray();

        throw new HttpResponseException(
            response()->json($response, 422)
        );
    }

    public function messages()
    {
        $msg = [
            'title.required' => '件名は必ず入力してください。',
            'title.max' => '件名は30文字以内で入力してください。',
            'file0.required' => 'ファイルが選択されていません。',
            'content.required' => '説明文は必ず入力してください。',
            'route.required' => '回付ルートが設定されていません。',
        ];

        for($i=0;$i<$this->cnt;$i++) {
            $fileNumber = $i+1;
            $display = $fileNumber.'つめの';
            // 添付されているファイルがひとつの場合は表示しない
            if($this->cnt === 1) {
                $display = null;
            }
            $msg["file{$i}.mimes"] = "{$display}ファイル形式はpdfかdocxのみアップロード可能です。";
        }

        return $msg;
    }
}
