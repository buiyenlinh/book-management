<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\InitData;
use App\Models\Book;
use App\Models\Category;
use App\Http\Resources\BookCollection;
use App\Http\Resources\CategoryCollection;

class InfoController extends Controller
{
    use InitData;
    public function getCategory(Request $request) {
        $page = 5;
        if ($request->set_page) {
            $page = $request->set_page;
        }
        $category = new CategoryCollection(Category::paginate($page));
        return $this->responseSuccess($category->response()->getData(true), 'Danh sách tất cả loại sách');
    }

    public function getBook() {
        $books = new BookCollection(Book::paginate(5));
        return $this->responseSuccess($books->response()->getData(true), 'Danh sách tất cả sách');
    }

    public function getBookByCategory(Request $request) {
        try {
            $category = Category::find($request->category_id);
            if ($category) {
                $books = Book::where('category_id', $request->category_id);
                $books = new BookCollection(Book::paginate(5)->withQueryString());
                return $this->responseSuccess($books->response()->getData(true), 'Danh sách sách thuộc loại sách ' . $category->name);
            } else {
                return $this->responseError('Loại sách không tồn tại');
            }
        } catch (\Exception $ex) {
            return $this->responseError('Đã xảy ra lỗi! Vui lòng thử lại!', [$ex->getMessage()]);
        }
    }
}
