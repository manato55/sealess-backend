<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CompanyService;
use App\Http\Requests\Company;
use App\Http\Requests\RegisterUserCheck;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegisterMail;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;


class CompanyController extends Controller
{

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    public function registerCompany(Company $request)
    {
        $existingCompany = $this->companyService->checkExistingCompany($request->email);
        // 既に本登録されている場合
        if($existingCompany === false) {
            return response()->json([
                'errors' => ['name' =>
                    ['既に本登録済みです。']
                ]
            ], 422);
        }

        $register = $this->companyService->create($request);

        if($register) {
            Mail::to($register->email)->send(new RegisterMail(
                config('const.MAIL.REGISTER_COMPANY_MAIL'),
                config('const.LINK.REGISTER_COMPANY_ADMIN_LINK').$register->token,
                $register->expired_at,
                'registerEmail'
            ));
        }
    }

    public function adminTokenCheck($token)
    {
        $tokenChecker = $this->companyService->token($token);
        if($tokenChecker) {
            return $tokenChecker;
        } else {
            throw new HttpResponseException(response()->json(null, 404));
        }
    }

    public function officialAdminRegistry(Company $request)
    {
        $newAdmin = $this->companyService->adminRegistry($request);

        // adminの登録に成功したらEmailVerificationテーブルのレコードを削除、及びCompanyテーブルのis_registeredをtrueにする。
        if($newAdmin) {
            try {
                DB::transaction(function () use ($newAdmin) {
                    $this->companyService->deleteRecordFromVerification($newAdmin->email);
                    $this->companyService->updateIsRegisteredToTrue($newAdmin->email);
                });
            } catch (Exception $e) {
                Log::error($e->getMessage());
                return response()->json(['error' => '登録できませんでした。'], 500);
            }
        } else {
            return response()->json(['error' => '登録できませんでした。'], 500);
        }
    }

    public function changeDepName(Company $request)
    {
        return $this->companyService->depName($request);
    }

    public function changeSecName(Company $request)
    {
        return $this->companyService->secName($request);
    }

    public function changeJobTitle(Company $request)
    {
        return $this->companyService->editJobTitle($request);
    }

    public function fetchJobTitle()
    {
        return $this->companyService->jobTitle();
    }

    public function deleteDep($id)
    {
        if($this->companyService->deleteDepartment($id) === false) {
            return response()->json([
                'error' => 'この部に紐づいているユーザがいるため削除できません。'
            ], 422);
        } else {
            return true;
        }
    }

    public function deleteSection($id)
    {
        if($this->companyService->deleteSec($id) === false) {
            return response()->json([
                'error' => 'この課に紐づいているユーザがいるため削除できません。'
            ], 422);
        } else {
            return true;
        }
    }

    public function deleteJobTitle($id)
    {
        if($id === "undefined") {
            return response()->json([
                'errors' => [
                    'jobTitle'=> '役職を選択してください。'
                ]
            ], 422);
        }

        if($this->companyService->removeJobTitle($id) === false) {
            return response()->json([
                'errors' => [
                    'jobTitle'=> 'この役職に紐づいているユーザがいるため削除できません。'
                ]
            ], 422);
        } else {
            return true;
        }
    }

    public function fetchSections($id)
    {
        return $this->companyService->fetchSectionsById($id);
    }

    public function changeDepAdminInfo(RegisterUserCheck $request)
    {
        return $this->companyService->changeDepAdmin($request);
    }

    public function registerDepAdmin(RegisterUserCheck $request)
    {
        $this->companyService->departmentAdmin($request);
    }

    public function sendRegisterEmail(RegisterUserCheck $request)
    {
        $verification = $this->companyService->registerEmail($request);

        Mail::to($verification[0])->send(new RegisterMail(
            config('const.MAIL.REGISTER_MAIL'),
            config('const.LINK.REGISTER_LINK').$verification[2],
            $verification[1],
            'registerEmail'
        ));
    }

    public function editNormalUserInfo(RegisterUserCheck $request)
    {
        return $this->companyService->normalUserInfo($request);
    }
}
