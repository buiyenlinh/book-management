<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Http\InitData;
use App\Models\User;

class LoginController extends Controller
{
    use InitData;
    public function login(Request $request) {
        try {
            if (Auth::attempt([
                'username' => $request->username,
                'password' =>$request->password,    
                'active' => $request->active
            ])) {
                $user = Auth::user();
                $data['token'] = 'M' . $user->id . Str::random(80);

                return $this->responseSuccess($data, 'Successful login');
            } else {
                return $this->responseError('Unauthorised', 401);
            }
        } catch (Exception $ex) {
            return $this->responseError([$ex->getMessage()], 'Something was wrong');
        }
    }
}
