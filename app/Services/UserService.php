<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Department;
use App\Models\PasswordReset;
use App\Models\EmailVerification;
use App\Enums\UserType;
use App\Models\JobTitle;
use App\Models\Owner;
use App\Models\Section;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;

class UserService
{

    public function myInfo()
    {
        return User::where('id',Auth::user()->id)
            ->with('section','department','jobTitle')
            ->first();
    }

    public function ownerInfo()
    {
        return Owner::find(Auth::user()->id);
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
        $existingRecord = EmailVerification::where('token',$request->token)
            ->with('department')
            ->first();

        $user = User::create([
            'name' => $existingRecord->name,
            'email' => $existingRecord->email,
            'department_id' => $existingRecord->department_id,
            'section_id' => $existingRecord->section_id,
            'job_title_id' => $existingRecord->job_title_id,
            'password' => Hash::make($request->password),
            'user_type' => UserType::getValue('OrdinaryUser'),
            'company_id' => $existingRecord->department->company_id,
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

    public function depUsers()
    {
        return User::where('department_id', Auth::user()->department_id)
            ->where('id','!=',Auth::user()->id)
            ->where('user_type', 2)
            ->with('section','jobTitle')
            ->get();
    }

    public function deleteDepUserById($id)
    {
        User::find($id)->delete();
    }

    public function deleteDepAdminUserById($id)
    {
        User::find($id)->delete();
    }

    public function adminUsers()
    {
        return User::where('user_type', 1)
            ->where('company_id', Auth::user()->company_id)
            ->with('department')
            ->get();
    }

    public function noramlUsers($department)
    {
        return User::where('user_type', 2)
            ->where('department_id',$department)
            ->with('department', 'section')
            ->get();
    }

    public function changeDepartment($request)
    {
        $user = User::find($request->userid);
        $user->department_id = $request->department;
        $user->section_id = $request->section;
        $user->save();
    }

    public function createNewDepartment($department)
    {
        return Department::create([
            'name' => $department,
            'company_id' => Auth::user()->company_id,
        ]);
    }

    public function createNewSection($request)
    {
        return Section::create([
            'name' => $request->name,
            'department_id' => $request->department,
        ]);
    }

    public function createNewJobTitle($jobTitle)
    {
        return JobTitle::create([
            'name' => $jobTitle,
            'company_id' => Auth::user()->company_id,
        ]);
    }

    public function department()
    {
        return Department::where('company_id',Auth::user()->company_id)->get();
    }

    public function depANDSec()
    {
        return Department::where('company_id', Auth::user()->company_id)
            ->with('sections')
            ->get();
    }

}
