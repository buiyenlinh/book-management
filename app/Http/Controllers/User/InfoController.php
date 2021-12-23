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
            $category = Category::find($request->category_id);
            if ($category) {
                $books = Book::where('category_id', $request->category_id);
                $books = new BookCollection($books->paginate(5)->withQueryString());
                return $this->responseSuccess($books->response()->getData(true), 'Danh sách sách thuộc loại sách ' . $category->name);
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
            $book = Book::find($request->id);
            $similar_book = Book::where('category_id', $book->category_id)
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
            $book = new BookResource(Book::find($request->id));
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
            $author_id = $request->author_id;
            if ($author_id < 0 || $author_id == null || $author_id == "") {
                return $this->responseError('Vui lòng thử lại');
            }

            $books = new BookCollection(Book::where('author_id', $author_id)->get());
            
            return $this->responseSuccess($books, 'Danh sách sách theo tác giả');
        } catch (\Exception $ex) {
            return $this->responseError('Vui lòng thử lại', [$ex->getMessage()]);
        }
    }
}
