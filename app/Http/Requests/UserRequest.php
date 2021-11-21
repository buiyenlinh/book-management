<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'fullname' => 'required',
            'username' => ['required', 'unique:users'],
            'password' => 'required',
            'active' => 'required',
            'gender' => 'required',
            'birthday' => 'required',
            'address' => 'required',
            'role_id' => 'required', 
        ];
    }

    /** 
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'fullname.required' => 'Tên người dùng là bắt buộc',
            'username.required' => 'Tên đăng nhập là bắt buộc',
            'username.unique' => 'Tên đăng nhập đã tồn tại',
            'password.required' => 'Mật khẩu là bắt buộc',
            'active.required' => 'Trạng thái là bắt buộc',
            'gender.required' => 'Giới tính là bắt buộc',
            'birthday.required' => 'Ngày sinh là bắt buộc',
            'address.required' => 'Địa chỉ là bắt buộc',
            'role_id.required' => 'Quyền người dùng là bắt buộc',
        ];
    }
}
