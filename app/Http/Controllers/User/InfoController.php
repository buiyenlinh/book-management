<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\InitData;
use Illuminate\Support\Facades\Storage;
use App\Models\Book;
use App\Models\Category;
use App\Models\Author;
use App\Models\Content;
use App\Models\User;
use App\Http\Resources\BookCollection;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\AUthorCollection;
use App\Http\Resources\ContentCollection;
use App\Http\Resources\BookResource;
use App\Http\Resources\UserResource;

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
                return $this->responseSuccess([]);
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
            if (count($book) == 1) {
                $similar_book = Book::where('category_id', $book[0]->category_id)
                    ->where('id', '!=', $request->id)
                    ->limit($set_number)->get();

                return $this->responseSuccess($similar_book, 'Danh sách sách tương tự');
            } else {
                return $this->responseSuccess([], 'Danh sách sách tương tự');
            }
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
            $author = Author::where('alias', $request->alias)->get();
            if (count($author) > 0) {
                $books = new BookCollection(Book::where('author_id', $author[0]->id)->paginate(5));
                $response = [
                    'books' => $books->response()->getData(true),
                    'author' => $author[0]
                ];
                return $this->responseSuccess($response, 'Danh sách sách theo tác giả');
            } else {
                return $this->responseSuccess([], 'Danh sách sách theo tác giả');
            }
        } catch (\Exception $ex) {
            return $this->responseError('Vui lòng thử lại', [$ex->getMessage()]);
        }
    }

    /**
     * Lấy chương / phần nội dung của sách
     */
    public function getContentChapter(Request $request) {
        $book = Book::where('alias', $request->book)->get();
        if (count($book) > 0) {
            $chapter_list = new ContentCollection(Content::where('book_id', $book[0]->id)->get());
            $content = Content::where('book_id', $book[0]->id)
            ->where('alias', $request->alias_content)
            ->first();
            $response = [
                'content' => $content,
                'book' => $book[0],
                'chapter_list' => $chapter_list
            ];
            return $this->responseSuccess($response, 'Nội dung');
        } else {
            return $this->responseSuccess([], 'Nội dung');
        }
        return $content;
    }

    /**
     * Lấy thông tin user
     */
    public function getProfileUser(Request $request) {
        $user = new UserResource(User::where('token_user', $request->bearerToken())->first());
        return $this->responseSuccess($user, 'Thông tin cá nhân');  
    }

    /**
     * Xóa ảnh đại diện user
     */
    public function deleteAvatarUser(Request $request) {
        $user = User::where('token_user', $request->bearerToken())->get()->first();
        if (!filter_var($user->avatar, FILTER_VALIDATE_URL)) { 
            if ($user->avatar) {
                Storage::delete($user->avatar);
            }
        }
        
        $user = $user->update([
            'avatar' => ''
        ]);

        $user = User::where('token_user', $request->bearerToken())->get()->first();

        $user = new UserResource(User::find($user->id));
        return $this->responseSuccess($user, 'Xóa ảnh đại diện thành công');
    }

    /**
     * Cập nhật profile
     */
    public function updateProfileUser(Request $request)
    {
        $user = User::where('token_user', $request->bearerToken())->get()->first();
        $avatar = $user->avatar;
        if ($request->file('avatar')) {
            Storage::delete($avatar);
            $avatar = $request->file('avatar')->store('public/images');
            $avatar = Storage::url($avatar);
        }

        $fullname = $user->fullname;
        $gender = $user->gender;
        $birthday = $user->birthday;
        $address = $user->address;

        if ($request->fullname) {
            $fullname = $request->fullname;
        }

        if ($request->gender) {
            $gender = $request->gender;
        }

        if ($request->gender) {
            $gender = $request->gender;
        }

        if ($request->address) {
            $address = $request->address;
        }

        if ($request->birthday) {
            $birthday = $request->birthday;
        }

        $user->update([
            'fullname' => $fullname,
            'gender' => $gender,
            'avatar' => $avatar,
            'birthday' => $birthday,
            'address' => $address
        ]);

        $user = new UserResource(User::find($user->id));

        return $this->responseSuccess($user, 'Cập nhật thông tin thành công');
    }
}
