<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\InitData;
use App\Models\Category;
use App\Models\User;
use App\Models\Book;
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
        return $this->responseSuccess($category->response()->getData(true), 'Danh sách loại sách');
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
        
        $name = $request->name;
        $alias = $this->to_slug($request->alias);
        // Check name
        $countCategory = count(Category::where('name', $request->name)->get());
        $i = 1;
        while ($countCategory > 0) {
            $countCategory = count(Category::where('name', $request->name . ' 0' . $i)->get());
            $i = $i + 1;
        }
        if ($i > 1) {
            $name = $request->name . ' 0' . ($i - 1);
        }

        // check alias

        $countAlias = count(Category::where('alias', $alias)->get());
        $i = 1;
        while ($countAlias > 0) {
            $countAlias = count(Category::where('alias', $alias . '-0' . $i)->get());
            $i = $i + 1;
        }
        if ($i > 1) {
            $alias = $alias . '-0' . ($i - 1);
        }

        $category = Category::create([
            'name' => $name,
            'username' => $user->username,
            'alias' => $alias
        ]);

        return $this->responseSuccess($category, 'Thêm loại sách thành công');
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
        $request->validate(
            [ 'name' => 'required', 'alias' => 'required' ],
            [ 'name.required' => 'Tên loại sách là bắt buộc', 'alias.required' => 'Đường dẫn là bắt buộc' ] 
        );

        $name = $request->name;
        $alias = $request->alias;
        // Check name
        $countCategory = count(Category::where('name', $request->name)
            ->where('id', '!=', $id)->get());
        $i = 1;
        while ($countCategory > 0) {
            $countCategory = count(Category::where('name', $request->name . ' 0' . $i)
            ->where('id', '!=', $id)->get());
            $i = $i + 1;
        }
    
        if ($i > 1) {
            $name = $request->name . ' 0' . ($i - 1);
        }

        // check alias
        $alias = $this->to_slug($request->alias);
        $countAlias = count(Category::where('alias', $alias)
            ->where('id', '!=', $id)->get());
        $i = 1;
        while ($countAlias > 0) {
            $countAlias = count(Category::where('alias', $alias . '-0' . $i)
                ->where('id', '!=', $id)->get());
            $i = $i + 1;
        }
        if ($i > 1) {
            $alias = $alias . '-0' . ($i - 1);
        }

        Category::find($id)->update([
                'name' => $name,
                'alias' => $alias
            ]);

        $category = Category::find($id)
            ->get()
            ->first();

        return $this->responseSuccess($category, 'Cập nhật loại sách thành công');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::find($id); 
        if ($category->id) {   
            $book = Book::where('category_id', $id)->get();
            if (count($book) > 0) {
                return $this->responseError('Loại sách này có sách không được xóa.');
            }
        } else {
            return $this->responseError('Loại sách không tồn tại');
        }
        $category->delete();
        return $this->responseSuccess([], 'Xóa loại sách thành công');
    }

    public function search(Request $request) {
        try {
            $where = '';
            $categories = Category::query();
            if ($request->has('name')) {
                $categories = $categories->where('name', 'LIKE', '%' . $request->name . '%');
            }

            $categories = new CategoryCollection($categories->paginate(5)->withQueryString());
            return $this->responseSuccess($categories->response()->getData(true), 'Danh sách tìm kiếm');
        } catch (\Exception $ex) {
            return $this->responseError([$ex->getMessage()], 'Vui lòng thử lại!');
        }
    }



}
