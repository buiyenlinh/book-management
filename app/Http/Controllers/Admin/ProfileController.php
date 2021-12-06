<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Http\InitData;

class ProfileController extends Controller
{
    use InitData;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = new UserResource(User::where('token', $request->bearerToken())->get()->first());
        return $this->responseSuccess($user, 'Thông tin cá nhân');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $request->validate(
            [
                'fullname' => 'required',
                'gender' => 'required',
                'address' => 'required',
                'birthday' => 'required'
            ],
            [
                'fullname.required' => 'Họ và tên là bắt buộc',
                'gender.required' => 'Giới tính là bắt buộc',
                'address.required' => 'Địa chỉ là bắt buộc',
                'birthday.required' => 'Ngày sinh là bắt buộc'
            ]
        );

        $user = User::where('token', $request->bearerToken())->get()->first();

        $birthday = $request->birthday;
        $avatar = $user->avatar;
        if ($request->file('avatar')) {
            Storage::delete($avatar);
            $avatar = $request->file('avatar')->store('public/images');
            $avatar = Storage::url($avatar);
        }

        $user = $user->update([
            'fullname' => $request->fullname,
            'gender' => $request->gender,
            'avatar' => $avatar,
            'birthday' => $birthday,
            'address' => $request->address
        ]);

        $user = new UserResource(User::find($user));

        return $this->responseSuccess($user, 'Cập nhật thành công');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteAvatar(Request $request)
    {
        $user = User::where('token', $request->bearerToken())->get()->first();

        if ($user->avatar) {
            Storage::delete($user->avatar);
        }
        $user = $user->update([
            'avatar' => ''
        ]);

        $user = new UserResource(User::find($user));
        return $this->responseSuccess($user, 'Xóa ảnh đại diện thành công');
    }
}
