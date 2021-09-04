<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\UserService;
use App\Http\Requests\RegisterUserCheck;
use App\Http\Requests\Company;




class UserController extends Controller
{
    public function __construct(
        UserService $userService
    )
    {
        $this->userService = $userService;
    }

    public function me(){
        if(Auth::user()->user_type === 99) {
            return $this->userService->ownerInfo();
        } else {
            return $this->userService->myInfo();
        }

    }

    public function fetchDepUsers()
    {
        return $this->userService->depUsers();
    }

    public function deleteDepUser($id)
    {
        return $this->userService->deleteDepUserById($id);
    }

    public function fetchAdminUsers()
    {
        return $this->userService->adminUsers();
    }

    public function fetchNormalUsers($department)
    {
        return $this->userService->noramlUsers($department);
    }

    public function changeDepartment(RegisterUserCheck $request)
    {
        return $this->userService->changeDepartment($request);
    }

    public function deleteDepAdminUser($id)
    {
        return $this->userService->deleteDepAdminUserById($id);
    }

    public function departmentRegistry(Company $request)
    {
        return $this->userService->createNewDepartment($request->name);
    }

    public function sectionRegistry(Company $request)
    {
        return $this->userService->createNewSection($request);
    }

    public function jobTitleRegistry(Company $request)
    {
        return $this->userService->createNewJobTitle($request->name);
    }

    public function fetchDepartment()
    {
        return $this->userService->department();
    }

    public function fetchDepartmentANDSection()
    {
        return $this->userService->depANDSec();
    }
}
