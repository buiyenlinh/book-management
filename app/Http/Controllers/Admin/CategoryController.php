<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\InitData;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryCollection;

class CategoryController extends Controller
{
    use InitData;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $category = new CategoryCollection(Category::paginate(5));
        return $this->responseSuccess($category->response()->getData(true), 'Danh sách loại truyện');
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
    public function store(CategoryRequest $request)
    {
        $user = User::where('token', $request->bearerToken())->first();
        $category = Category::create([
            'name' => $request->name,
            'username' => $user->username
        ]);

        // return response()->json([
        //     'status' => 'OK',
        //     'category' => $category
        // ]);

        return $this->responseSuccess($category, 'Thêm loại truyện thành công');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = Category::where('id', $id)->first();
        return $this->responseSuccess($category);
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
    public function update(Request $request, $id)
    {
        
        $category_check = Category::where('id', '!=', $id)
            ->where('name', $request->name)
            ->get()
            ->toArray();

        if (count($category_check) > 0) {
            return $this->responseError('Tên loại truyện này đã tồn tại', '', 200);
        }

        $request->validate(
            [ 'name' => 'required' ],
            [ 'name.required' => 'Tên loại truyện là bắt buộc' ]
        );

        Category::where('id', $id)
            ->update(['name' => $request->name]);

        $category = Category::where('id', $id)
            ->get()
            ->first();

        return $this->responseSuccess($category, 'Cập nhật loại truyện thành công');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::where('id', $id);
        $category->delete();
        return $this->responseSuccess([], 'Xóa loại truyện thành công');
    }
}
