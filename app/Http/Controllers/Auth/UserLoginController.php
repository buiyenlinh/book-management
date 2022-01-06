<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Http\InitData;
use App\Models\User;
use App\Http\Resources\UserResource;

class UserLoginController extends Controller
{
    use InitData;
    /**
     * Đăng nhập cho user not admin
     */
    public function login(Request $request) {
        try {
            if (Auth::attempt([
                'username' => $request->username,
                'password' =>$request->password,    
                'active' => $request->active
            ])) {
                $user = Auth::user();
                $data['token_user'] = 'M' . $user->id . Str::random(80);
                $data = User::where('id', $user->id)
                    ->update(['token_user' => $data['token_user']]);

                return $this->responseSuccess($data, 'Đăng nhập thành công');
            } else {
                return $this->responseError('Tên đăng nhập hoặc mật khẩu sai', 401);
            }
        } catch (Exception $ex) {
            return $this->responseError([$ex->getMessage()], 'Đã xảy ra lỗi, vui lòng thử lại');
        }
    }


    /**
     * Đăng nhập với google, dành cho user not admin
     */
    public function loginWithGoogle(Request $request) {
        if ($request->email == '' || $request->oauth2 == '') {
            return $this->responseError('Vui lòng thử lại');
        }

        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            $user = User::create([
                'username' => $request->email,
                'password' => bcrypt($request->fullname),
                'fullname' => $request->fullname,
                'email' => $request->email,
                'active' => 1,
                'avatar' => $request->avatar,
                'role_id' => 4,
                'oauth2' => '',
                'token' => ''
            ]);
        }

        $token = 'U' . $user->id . Str::random(80);
        
        $user->update([
            'token_user' => $token,
            'oauth2' => 'google|' . $request->oauth2,
        ]);
        
        return $this->responseSuccess($user, 'Đăng nhập thành công');
    }
}
