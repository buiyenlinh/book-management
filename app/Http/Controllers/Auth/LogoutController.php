<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\InitData;
use App\Models\User;

class LogoutController extends Controller
{
    use InitData;
    public function logout(Request $request) {
        try {
            $user = User::where('token', $request->bearerToken());
            $user->update(['token' => '']);
            return $this->responseSuccess('Đăng xuất thành công');
        } catch (\Exception $ex) {
            return $this->responseError([$ex->getMessage()], 'Đăng xuất không thành công!');
        }
    }
}
