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
            'title' => ['required'],
            'language' => 'required',
            'author_id' => 'required',
            'category_id' => 'required',
            'status' => 'required',
            'alias' => ['required'],
            'release_time' => 'required'
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
            // 'title.unique' => 'Tiêu đề này đã tồn tại',
            'language.required' => 'Ngôn ngữ là bắt buộc',
            'author_id.required' => 'Tác giả là bắt buộc',
            'release_time.required' => 'Thời gian phát hành là bắt buộc',
            'category_id.required' => 'Loại truyện là bắt buộc',
            'status.required' => 'Trạng thái là bắt buộc',
            'alias.required' => 'Đường dẫn là bắt buộc',
            // 'alias.unique' => 'Đường dẫn này đã tồn tại'
        ];
    }
}
