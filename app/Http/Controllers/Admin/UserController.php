<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserCollection;


use App\Http\InitData;
use App\Models\User;


class UserController extends Controller
{
    use InitData;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user =  new UserCollection(User::paginate(5));
        return $this->responseSuccess($user->response()->getData(true), 'User list');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $userLogin = User::where('token', $request->bearerToken())->first();

        if ($userLogin->role->level >= $request->role_id) {
            return $this->responseError(['role' => 'Không có quyền tạo người dùng có quyền này'], '', 422);
        }

        if (!$request->file('avatar')) {
            return $this->responseError(['avatar' => 'Ảnh đại diện là bắt buộc'], '', 422);
        }
        $avatar = $request->file('avatar')->store('public/images');
        $avatar = Storage::url($avatar);
        if ($request->role_id == 1) {
            return $this->responseError('Người dùng không được chọn quyền này', '', 422);
        }
        
        $birthday = $request->birthday;

        $user = new UserResource(User::create([
            'fullname' => $request->fullname,
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'active' => $request->active,
            'gender' => $request->gender,
            'birthday' => $birthday,
            'address' => $request->address,
            'avatar' => $avatar,
            'role_id' => $request->role_id
        ]));

        return $this->responseSuccess($user, 'Tạo người dùng thành công');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $user = User::where('id', $id)->get()->first();
        $user = new UserResource(User::find($id));
        return $this->responseSuccess($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateUser(Request $request, $id)
    {
        $request->validate(
            [
                'fullname' => 'required',
                'active' => 'required',
                'gender' => 'required',
                'address' => 'required',
                'birthday' => 'required'
            ],
            [
                'fullname.required' => 'Họ và tên là bắt buộc',
                'active.required' => 'Trạng thái là bắt buộc',
                'gender.required' => 'Giới tính là bắt buộc',
                'address.required' => 'Địa chỉ là bắt buộc',
                'birthday.required' => 'Ngày sinh là bắt buộc'
            ]
        );

        $userLoginId = User::where('token', $request->bearerToken())->get()->first()->role_id;
        $user = User::find($id);
        
        if (!isset($user)) {
            return $this->responseError(['user' => 'Người dùng này không tồn tại'], '', 422);
        }

        if($user->role->level == 1) {
            return $this->responseError(['role' => 'Không có quyền thay đổi người dùng này'], '', 422);
        }

        if ($userLoginId <= 2 && $userLoginId < $user->role->level) {
            $birthday = $request->birthday;
            $avatar = $user->avatar;
            if ($request->file('avatar')) {
                Storage::delete($avatar);
                $avatar = $request->file('avatar')->store('public/images');
                $avatar = Storage::url($avatar);
            }

            $user = $user->update([
                'fullname' => $request->fullname,
                'active' => $request->active,
                'gender' => $request->gender,
                'avatar' => $avatar,
                'birthday' => $birthday,
                'address' => $request->address
            ]);

            return $this->responseSuccess($user, 'Cập nhật người dùng thành công');
        }

        return $this->responseError(['role' => 'Không có quyền thay đổi người dùng này'], '', 422);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $userLoginId = User::where('token', $request->bearerToken())->get()->first()->role_id;
        $user = User::find($id);
        
        if (!isset($user)) {
            return $this->responseError(['user' => 'Người dùng này không tồn tại'], '', 422);
        }

        if($user->role->level == 1) {
            return $this->responseError(['role' => 'Không có quyền xóa người dùng này'], '', 422);
        }

        if ($userLoginId <= 2 && $userLoginId < $user->role->level) {
            Storage::delete($user->avatar);
            $user->delete();    
            return $this->responseSuccess([], 'Xóa người dùng thành công');
        } else {
            return $this->responseError(['role' => 'Không có quyền xóa người dùng này'], '', 422);
        }
    }

    /**
     * Search user
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function searchUser(Request $request) {
        try {
            $where = '';
            $users = User::query();
            if ($request->has('fullname')) {
                $users = $users->where('fullname', 'LIKE', '%' . $request->fullname . '%');
            }

            if ($request->has('role_id')) {
                $users = $users->where('role_id', $request->role_id);
            }
            
            if ($request->has('gender')) {
                $users = $users->where('gender', $request->gender);
            }

            $users = new UserCollection($users->paginate(5)->withQueryString());
            return $this->responseSuccess($users->response()->getData(true), 'Danh sách tìm kiếm');
        } catch (\Exception $ex) {
            return $this->responseError([$ex->getMessage()], 'Vui lòng thử lại!');
        }
    }
}
