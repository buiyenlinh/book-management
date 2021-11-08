<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookRequest extends FormRequest
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
            'title' => ['required', 'unique:books'],
            'describe' => 'required',
            'language' => 'required',
            'page_total' => 'required',
            'cover_image' => 'required',
            'producer' => 'required',
            'author' => 'required',
            'category_id' => 'required',
            'status' => 'required',
            'username' => 'required'
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
            'title.required' => 'Tiêu đề là bắt buộc',
            'title.unique' => 'Tiêu đề này đã tồn tại',
            'describe.required' => 'Mô tả là bắt buộc',
            'language.required' => 'Ngôn ngữ là bắt buộc',
            'page_total.required' => 'Tổng số trang là bắt buộc',
            'cover_image.required' => 'Ảnh bìa là bắt buộc',
            'author.required' => 'Tác giả là bắt buộc',
            'category_id.required' => 'Loại truyện là bắt buộc',
            'status.required' => 'Trạng thái là bắt buộc',
            'username.required' => 'Tên đăng nhập là bắt buộc',
        ];
    }
}
