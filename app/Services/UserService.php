<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\PasswordReset;
use App\Models\EmailVerification;
use App\Enums\UserType;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;

class UserService
{

    public function departmentAdmin($request)
    {
        return User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'department' => $request->department,
            'section' => null,
            'job_title' => null,
            'user_type' => UserType::getValue('DepAdmin'),
        ]);
    }

    public function registerEmail($request)
    {
        // 同じメールアドレスがテーブル上にあるのを避ける
        EmailVerification::where('email', $request->email)->delete();

        $newVerification = EmailVerification::create([
            'email' => $request->email,
            'name' => $request->name,
            'department' => $request->department,
            'section' => $request->section,
            'job_title' => $request->jobTitle,
            'token' => Str::random(50),
            'expired_at' => Carbon::now()->addHours(5),
        ]);

        return [
            $newVerification->email,
            $newVerification->expired_at,
            $newVerification->token,
        ];
    }

    public function token($token)
    {
        return EmailVerification::where('token', $token)->first();
    }

    public function passwordReIssuanceToken($token)
    {
        return PasswordReset::where('token', $token)->first();
    }

    public function officialRegistry($request)
    {
        $existingRecord = EmailVerification::where('token',$request->token)->first();

        $user = User::create([
            'name' => $existingRecord->name,
            'email' => $existingRecord->email,
            'department' => $existingRecord->department,
            'section' => $existingRecord->section,
            'job_title' => $existingRecord->job_title,
            'password' => Hash::make($request->password),
            'user_type' => UserType::getValue('OrdinaryUser'),
        ]);

        // userの登録に成功したらEmailVefificationのレコードは削除
        if($user) {
            $existingRecord->delete();
        }
    }

    public function passwordReRegisterLink($email)
    {
        $existingEmail = User::where('email',$email)->first();

        if($existingEmail === null) {
            return false;
        }

        // PasswordResetテーブルに同じメールアドレスが存在しないようにする
        $duplicateEmail = PasswordReset::where('email',$email)->first();
        if($duplicateEmail !== null) {
            $duplicateEmail->delete();
        }

        return PasswordReset::create([
            'token' => Str::random(50),
            'email' => $existingEmail->email,
        ]);
    }

    public function passwordRegsiter($request)
    {
        $user = User::whereHas('passwordReset', function($q) use($request) {
            $q->where('token', $request->token);
        })->first();

        $user->password = Hash::make($request->password);
        return $user->save();
    }

    public function deletePasswordIssuanceLink($request)
    {
        return PasswordReset::where('token',$request->token)->delete();
    }

}
