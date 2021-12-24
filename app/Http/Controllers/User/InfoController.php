<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\InitData;
use App\Models\Book;
use App\Models\Category;
use App\Models\Author;
use App\Http\Resources\BookCollection;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\AUthorCollection;
use App\Http\Resources\BookResource;

class InfoController extends Controller
{
    use InitData;
    public function getCategory() {
        $category = new CategoryCollection(Category::all());
        return $this->responseSuccess($category->response()->getData(true), 'Danh sách tất cả loại sách');
    }

    /**
     * Lấy danh sách sách có phân trang
     */
    public function getBook() {
        $books = new BookCollection(Book::paginate(5));
        return $this->responseSuccess($books->response()->getData(true), 'Danh sách sách');
    }

    /**
     * Lấy danh sách các sách mới
     */
    public function getNewBookList(Request $request) {
        try {
            $total = 8;
            if ($request->has('total')) {
                $total = $request->total;
            }

            $books = new BookCollection(Book::orderBy('created_at', 'desc')->limit($total)->get());
            return $this->responseSuccess($books->response()->getData(true), 'Danh sách sách mới');
        } catch (\Exception $ex) {
            return $this->responseError('Đã xảy ra lỗi! Vui lòng thử lại!', [$ex->getMessage()]);
        }
    }

    /**
     * Danh sách sách của một loại sách có phân trang
     */
    public function getBookByCategory(Request $request) {
        try {
            $category = Category::where('alias', $request->alias)->get();
            if (count($category) > 0) {
                $books = Book::where('category_id', $category[0]->id);
                $books = new BookCollection($books->paginate(5)->withQueryString());
                return $this->responseSuccess($books->response()->getData(true), 'Danh sách sách thuộc loại sách ' . $category[0]->name);
            } else {
                return $this->responseError('Loại sách không tồn tại');
            }
        } catch (\Exception $ex) {
            return $this->responseError('Đã xảy ra lỗi! Vui lòng thử lại!', [$ex->getMessage()]);
        }
    }

    /**
     * Danh sách sách tương tự
     */
    public function getSimilarBook(Request $request) {
        $set_number = 8;
        try {
            if ($request->has('set_number')) {
                $set_number = $request->set_number;
            }
            $book = Book::where('alias', $request->alias)->get();
            $similar_book = Book::where('category_id', $book[0]->category_id)
                ->where('id', '!=', $request->id)
                ->limit($set_number)->get();

            return $this->responseSuccess($similar_book, 'Danh sách sách tương tự');
        } catch (\Exception $ex) {
            return $this->responseError('Vui lòng thử lại', [$ex->getMessage()]);
        }
    }

    /**
     * Lấy thông tin sách
     */
    public function getInfoBook(Request $request) {
        try {
            $book = new BookResource(Book::where('alias', $request->alias)->first());
            return $this->responseSuccess($book, 'Thông tin sách');
        } catch (\Exception $ex) {
            return $this->responseError('Vui lòng thử lại', [$ex->getMessage()]);
        }
    }

    /**
     * Lấy danh sách tác giả
     */
    public function getAuthor() {
        try {
            $author = new AuthorCollection(Author::paginate(5));
            return $this->responseSuccess($author->response()->getData(true), 'Danh sách tác giả');
        } catch (\Exception $ex) {
            return $this->responseError('Vui lòng thử lại', [$ex->getMessage()]);
        }
    }

    /**
     * Lấy danh sách sách theo tác giả
     */
    public function getBookByAuthor(Request $request) {
        try {
            $alias = $request->alias;
            if ($alias == "") {
                return $this->responseError('Vui lòng thử lại');
            }
            $author = Author::where('alias', $request->alias)->first();
            if ($author->id > 0) {
                $books = new BookCollection(Book::where('author_id', $author->id)->get());
                return $this->responseSuccess($books, 'Danh sách sách theo tác giả');
            } else {
                return $this->responseError('Không tồn tại loại sách này');
            }
        } catch (\Exception $ex) {
            return $this->responseError('Vui lòng thử lại', [$ex->getMessage()]);
        }
    }
}
