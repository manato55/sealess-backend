<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
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
        $user = User::where('email', $request->email)->first();

        // 取得できない場合、パスワードが不一致の場合エラー
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'message' => [__('メールアドレスもしくはパスワードが一致しません。')],
            ]);
        }
        // tokenの作成
        $token = $user->createToken($request->device_name ?? 'undefined')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ], 200);
    }

    /**
     * Handle a logout request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $user = User::find(Auth::user()->id);

        foreach ($user->tokens as $token) {
            $token->delete();
        }
    }

    public function fetchUser()
    {
        return User::find(Auth::user()->id);
    }
}
