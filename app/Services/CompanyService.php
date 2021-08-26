<?php

namespace App\Services;

use App\Models\Company;
use App\Models\User;
use App\Models\AdminEmailVerification;
use App\Models\Department;
use App\Models\Section;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class CompanyService
{

    public function create($request)
    {
        $company = Company::create([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if($company) {
            return AdminEmailVerification::create([
                'email' => $request->email,
                'token' => Str::random(50),
                'expired_at' => Carbon::now()->addHours(5),
            ]);
        }
    }

    public function checkExistingCompany($email)
    {
        // 同じメールアドレスがテーブル上にあるのを避ける
        $existingCompany = Company::where('email', $email)->first();
        // まだ本登録されていない場合は、既存のレコードを削除して、新規に作り直す。
        if($existingCompany && !$existingCompany->is_registered) {
            $existingCompany->delete();
            $this->deleteAdminEmailVerification($email);
        // 新規登録
        } else if(!$existingCompany) {
            return true;
        // 既に本登録済み
        } else {
            return false;
        }
    }

    private function deleteAdminEmailVerification($email)
    {
        return AdminEmailVerification::where('email',$email)->delete();
    }

    public function token($token)
    {
        return AdminEmailVerification::where('token', $token)->first();
    }

    public function adminRegistry($request)
    {
        $companyInfo = AdminEmailVerification::where('token',$request->token)
            ->with('company')
            ->first();

        return User::create([
            'name' => $request->name,
            'email' => $companyInfo->email,
            'password' => Hash::make($request->password),
            'user_type' => 0,
            'company_id' => $companyInfo->company->id,
        ]);
    }

    public function deleteRecordFromVerification($email)
    {
        return AdminEmailVerification::where('email',$email)->delete();
    }

    public function updateIsRegisteredToTrue($email)
    {
        $company = Company::where('email',$email)->first();
        $company->is_registered = true;
        return $company->save();
    }

    public function depName($request)
    {
        $dep = Department::find($request->department_id);
        $dep->name = $request->name;
        $dep->save();
    }

    public function secName($request)
    {
        $sec = Section::find($request->section_id);
        $sec->name = $request->name;
        $sec->save();
    }

    public function deleteSec($id)
    {
        $sec = Section::where('id', $id)->with('users')->first();

        // 部に紐づいているユーザーがいる場合は削除不可
        if(count($sec->users) > 0) {
            return false;
        } else {
            $sec->delete();
        }
    }

}
