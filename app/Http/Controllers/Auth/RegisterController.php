<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterFormCheck;
use App\Http\Requests\RegisterUserCheck;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegisterMail;
use App\Mail\ReRegisterPassword;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;


class RegisterController extends Controller
{

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'department' => $data['department'],
            'section' => $data['section'],
            'job_title' => $data['jobTitle'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function register(RegisterFormCheck $request)
    {
        $user = $this->create($request->all());

        $path = storage_path().'/app/files/'.$user->id;
        if(!File::exists($path)) {
            File::makeDirectory($path, 0700, true);
        }

        return $user;
    }

    public function tokenCheck($token)
    {
        $tokenChecker =  $this->userService->token($token);
        if($tokenChecker) {
            return $tokenChecker;
        } else {
            throw new HttpResponseException(response()->json(null, 404));
        }
    }

    public function passwordTokenCheck($token)
    {
        return $this->userService->passwordReIssuanceToken($token);
    }

    public function officialRegistryOrdinaryUser(RegisterUserCheck $request)
    {
        $this->userService->officialRegistry($request);
    }

    public function linkIssuance(Request $request)
    {
        $link = $this->userService->passwordReRegisterLink($request->email);

        if(!$link) {
            throw new HttpResponseException(
                response()->json([
                    'errors'=> [
                        '?????????????????????????????????????????????'
                    ]
                ], 422 )
            );
        }

        Mail::to($link->email)->send(new ReRegisterPassword(
            config('const.MAIL.RE_REGISTER_PASSWORD'),
            config('const.LINK.RE_REGISTER_PASSWORD_LINK').$link->token,
            'reRegisterPassword'
        ));
    }

    public function passwordReRegister(RegisterUserCheck $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $this->userService->passwordRegsiter($request);
                $this->userService->deletePasswordIssuanceLink($request);
            });
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => '?????????????????????????????????'], 500);
        }
    }

}
