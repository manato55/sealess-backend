<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Owner;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;


class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        // ユーザーの取得
        $user = User::where('email', $request->email)
            ->where('is_in_use',true)
            ->with('department','section')
            ->first();

        if(!$user) {
            $user = Owner::where('email',$request->email)
                ->first();
        }

        // 取得できない場合、パスワードが不一致の場合エラー
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw new HttpResponseException(
                response()->json([
                    'errors'=> [
                        'メールアドレスもしくはパスワードが一致しません。'
                    ]
                ], 422 )
            );
        }
        // tokenの作成
        $token = $user->createToken($request->device_name ?? 'undefined')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ], 200);
    }

    public function logout()
    {
        $user = User::find(Auth::user()->id);
        if(!$user) {
            $user = Owner::find(Auth::user()->id);
        }

        foreach ($user->tokens as $token) {
            $token->delete();
        }
    }

    public function fetchUser()
    {
        $user = User::find(Auth::user()->id);

        if(!$user) {
            $user = Owner::find(Auth::user()->id);
        }
        return $user;
    }
}
