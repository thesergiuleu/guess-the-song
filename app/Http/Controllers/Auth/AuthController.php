<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Services\PassportService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * @var PassportService
     */
    private $passportService;

    public function __construct(PassportService $passportService)
    {

        $this->passportService = $passportService;
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->all();
        $user = User::create(array_merge($data, ['password' => Hash::make($data['password'])]));
        if (!$user) {
            return response_fail();
        }
        return $this->passportService->getTokens($data['email'], $data['password']);
    }
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function login(Request $request)
    {
        $data = $request->only(['email', 'password']);

        return $this->passportService->getTokens($data['email'], $data['password']);
    }
}
