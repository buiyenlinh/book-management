<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\BookRequest;
use App\Http\Requests\BookRequestUpdate;
use Illuminate\Support\Facades\Storage;

use App\Http\Resources\BookCollection;
use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Models\User;
use App\Models\Category;
use App\Models\Author;
use App\Models\Content;
use App\Http\InitData;

class BookController extends Controller
{
    use InitData;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $books = new BookCollection(Book::paginate(5));
        return $this->responseSuccess($books->response()->getData(true), 'Danh sách');
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
    public function store(BookRequest $request)
    {
        try {
            $user = User::where('token', $request->bearerToken())->first();

            $category = Category::find($request->category_id);
            if (!$category) {
                return $this->responseError('Thể loại sách này không tồn tại!');
            }

            $author = Author::find($request->author_id);
            if (!$author) {
                return $this->responseError('Tác giả này chưa có trong hệ thống!');
            }

            $mp3 = '';
            if ($request->file('mp3')) {
                $mp3 = $request->file('mp3')->store('public/mp3');
                $mp3 = Storage::url($mp3);
            }

            $cover_image = '';
            if ($request->file('cover_image')) {
                $cover_image = $request->file('cover_image')->store('public/images');
                $cover_image = Storage::url($cover_image);
            }

            $describe = '';
            $producer = '';
            if ($request->describe != 'undefined') {
                $describe = $request->describe;
            }
            if ($request->producer != 'undefined') {
                $producer = $request->producer;
            }

            $title = $request->title;
            $alias = $this->to_slug($request->alias);

            // Check title
            $countTitle = count(Book::where('title', $request->title)->get());

            $i = 1;
            while ($countTitle > 0) {
                $countTitle = count(Book::where('title', $request->title . ' 0' . $i)->get());
                $i++;
            }
            if ($i > 1) {
                $title = $request->title . ' 0' . ($i - 1);
            }
            

            // Check alias
            $countAlias = count(Book::where('alias', $alias)->get());
            $i = 1;
            while ($countAlias > 0) {
                $countAlias = count(Book::where('alias', $alias . '-0' . $i)->get());
                $i++;
            }
            if ($i > 1) {
                $alias = $alias . '-0' . ($i - 1); 
            }

            $book = Book::create([
                'title' => $title,
                'describe' => $describe,
                'language' => $request->language,
                'release_time' => $request->release_time,
                'cover_image' => $cover_image,
                'producer' => $producer,
                'mp3' => $mp3,
                'author_id' => $request->author_id,
                'category_id' => $request->category_id,
                'status' => $request->status,
                'username' => $user->username,
                'alias' => $alias
            ]);

            if ($request->content && $book->id) {
                foreach(json_decode ($request->content) as $item) {
                    if ($item) {
                        $content = Content::create([
                            'title' => $item->title,
                            'content' => $item->content,
                            'book_id' => $book->id,
                            'status' => 1,
                            'username' => $user->username
                        ]);
                    }
                }
            } else {
                return $this->responseError('Đã xảy ra lỗi! Vui lòng thử lại!');
            }
            
            
            return $this->responseSuccess($book, 'Thêm sách thành công!');
        } catch (\Exception $ex) {
            return $this->responseError([$ex->getMessage()], 'Đã xảy ra lỗi! Vui lòng thử lại!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $book = new BookResource(Book::find($id));
            return $this->responseSuccess($book);
        } catch (\Exception $ex) {
            return $this->responseError([$ex->getMessage()], 'Vui lòng thử lại!');
        }
    }
    

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function updateBook(BookRequestUpdate $request, $id) {
        try {
            $user = User::where('token', $request->bearerToken())->first();
            $book = Book::find($id);
            $title = $request->title;
            $alias = $this->to_slug($request->alias);

            if (!$book) {
                return $this->responseError('Sách này không tồn tại');
            }
            
            // Check title
            $countTitle = count(Book::where('title', $request->title)
                ->where('id', '!=', $id)->get());
            $i = 1;
            while ($countTitle > 0) {
                $countTitle = count(Book::where('title', $request->title . ' 0' . $i)
                ->where('id', '!=', $id)->get());
                $i++;
            }
            if ($i > 1) {
                $title = $request->title . ' 0' . ($i - 1);
            }
            
            // Check alias
            $countAlias = count(Book::where('alias', $alias)
                ->where('id', '!=', $id)->get());
            $i = 1;
            while ($countAlias > 0) {
                $countAlias = count(Book::where('alias', $alias . '-0' . $i)
                ->where('id', '!=', $id)->get());
                $i++;
            }
            if ($i > 1) {
                $alias = $alias . '-0' . ($i - 1);
            }

            $mp3 = '';
            $cover_image = '';
            if ($book->cover_image) {
                $cover_image = $book->cover_image;
            }
            if ($book->mp3) {
                $mp3 = $book->mp3;
            }
            
            if ($request->file('cover_image')) {
                Storage::delete($cover_image);
                $cover_image = $request->file('cover_image')->store('public/images');
                $cover_image = Storage::url($cover_image);
            }

            if ($request->file('mp3')) {
                Storage::delete($mp3);
                $mp3 = $request->file('mp3')->store('public/mp3');
                $mp3 = Storage::url($mp3);
            }

            Book::where('id', $id)
                ->update([
                    'title' => $title,
                    'describe' => $request->describe,
                    'language' => $request->language,
                    'release_time' => $request->release_time,
                    'cover_image' => $cover_image,
                    'producer' => $request->producer,
                    'author_id' => $request->author_id,
                    'mp3' => $mp3,
                    'category_id' => $request->category_id,
                    'status' => $request->status,
                    'alias' => $alias
                ]);

            if ($request->content) {
                foreach(json_decode ($request->content) as $item) {
                    if ($item) {
                        if ($item->id != null) {
                            Content::find($item->id)
                                ->update(
                                    ['title' => $item->title, 
                                    'content' => $item->content]
                                );
                        } else {
                            $content = Content::create([
                                'title' => $item->title,
                                'content' => $item->content,
                                'book_id' => $id,
                                'status' => 1,
                                'username' => $user->username
                            ]);
                        }
                    }
                }
            }

            $book = new BookResource(Book::find($id));
            return $this->responseSuccess($book, 'Cập nhật sách thành công');
        } catch (\Exception $ex) {
            return $this->responseError([$ex->getMessage()], 'Vui lòng thử lại!');
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $book = Book::find($id);
        if ($book->cover_image) {
            Storage::delete($book->cover_image);
        }
            
        if ($book->mp3) {
            Storage::delete($book->mp3);
        }

        $content = Content::where('book_id', $id)->delete();

        $book->delete();
        return $this->responseSuccess([], 'Xóa sách thành công');
    }

    /**
     * Search book
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function searchBook(Request $request) {
        try {
            $where = '';
            $books = Book::query();
            if ($request->has('title')) {
                $books = $books->where('title', 'LIKE', '%' . $request->title . '%');
            }

            if ($request->has('author_id')) {

                $books = $books->where('author_id', $request->author_id);
            }

            if ($request->has('category_id')) {
                $books = $books->where('category_id', $request->category_id);
            }

            $books = new BookCollection($books->paginate(5)->withQueryString());
            return $this->responseSuccess($books->response()->getData(true), 'Danh sách tìm kiếm');
        } catch (\Exception $ex) {
            return $this->responseError([$ex->getMessage()], 'Vui lòng thử lại!');
        }
    }
}
