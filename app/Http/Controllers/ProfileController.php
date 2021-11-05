<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends BaseController
{
    private $userService;

    public function __construct(UserService $userService)
    {
        parent::__construct();

        $this->middleware('auth');
        $this->userService = $userService;
    }

    public function readProfile()
    {
        $parameters['readById'] = Auth::id();

        return $this->userService->read($parameters);
    }

    public function updateProfile()
    {

    }

    public function changePassword(Request $request)
    {


    }
}
