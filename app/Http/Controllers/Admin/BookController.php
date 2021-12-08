<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\BookRequest;
use Illuminate\Support\Facades\Storage;

use App\Http\Resources\BookCollection;
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

            $book = Book::create([
                'title' => $request->title,
                'describe' => $request->describe,
                'language' => $request->language,
                'release_time' => $request->release_time,
                'cover_image' => $cover_image,
                'producer' => $request->producer,
                'mp3' => $mp3,
                'author_id' => $request->author_id,
                'category_id' => $request->category_id,
                'status' => $request->status,
                'username' => $user->username
            ]);

            foreach($request->content as $item) {
                $content = Content::create([
                    'title' => $item['title_content'],
                    'content' => $item['content'],
                    'book_id' => $book->id,
                    'status' => 1,
                    'username' => $user->username
                ]);
            }
            
            return $this->responseSuccess($book, 'Thêm sách thành công!');
        } catch (\Exception $ex) {
            return $this->responseSuccess([$ex->getMessage()], 'Đã xảy ra lỗi! Vui lòng thử lại!');
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

    public function updateBook(BookRequest $request, $id) {

        // $book_check = Book::where('id', '!=', $id)
        //     ->where('title', $request->title)
        //     ->get();
        
        // if (count($book_check) > 0) {
        //     return $this->responseError('Tiêu đề này đã tồn tại', '', 200);
        // }

        // $request->validate([
        //         'title' => 'required',
        //         'describe' => 'required',
        //         'language' => 'required',
        //         'page_total' => 'required',
        //         'producer' => 'required',
        //         'author' => 'required',
        //         'category_id' => 'required',
        //         'status' => 'required'
        //     ],
        //     [
        //         'title.required' => 'Tiêu đề là bắt buộc',
        //         'describe.required' => 'Mô tả là bắt buộc',
        //         'language.required' => 'Ngôn ngữ là bắt buộc',
        //         'page_total.required' => 'Tổng số trang là bắt buộc',  
        //         'author.required' => 'Tác giả là bắt buộc',
        //         'category_id.required' => 'Loại truyện là bắt buộc',
        //         'status.required' => 'Trạng thái là bắt buộc'
        //     ]
        // );

        $book = Book::find($id);
        if (!$book) {
            return $this->responseError('Sách này không tồn tại');
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
                'title' => $request->title,
                'describe' => $request->describe,
                'language' => $request->language,
                'release_time' => $request->release_time,
                'cover_image' => $cover_image,
                'producer' => $request->producer,
                'author_id' => $request->author_id,
                'mp3' => $mp3,
                'category_id' => $request->category_id,
                'status' => $request->status,
            ]);

        foreach($request->content as $item) {
            Content::where('title_content', $item['title_content'])
                ->where('book_id', $id)
                ->update([
                    'title' => $item['title_content'],
                    'content' => $item['content'],
                    'status' => 1
                ]);
        }

        $book = new BookResource(Book::find($id));
        return $this->responseSuccess($book, 'Cập nhật sách thành công');
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
}
