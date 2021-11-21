<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\BookRequest;
use Illuminate\Support\Facades\Storage;

use App\Models\Book;
use App\Models\User;
use App\Models\Category;
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
        $books = Book::paginate(5);
        return $this->responseSuccess($books, 'Books list');
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

            $category = Category::where('id', $request->category_id)->get()->first();
            if (!$category) {
                return $this->responseError('Thể loại truyện này không tồn tại!');
            }

            $cover_image = $request->file('cover_image')->store('public/images');
            $cover_image = Storage::url($cover_image);
            $mp3 = $request->file('mp3')->store('public/mp3');
            $mp3 = Storage::url($mp3);

            $book = Book::create([
                'title' => $request->title,
                'describe' => $request->describe,
                'language' => $request->language,
                'page_total' => $request->page_total,
                'cover_image' => $cover_image,
                'producer' => $request->producer,
                'content' => $request->content,
                'mp3' => $mp3,
                'author' => $request->author,
                'category_id' => $request->category_id,
                'status' => $request->status,
                'username' => $user->username
            ]);

            return $this->responseSuccess($book, 'ok');
        } catch (\Exception $ex) {
            return $this->responseSuccess([$ex->getMessage()], 'Something went wrong');
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
            $book = Book::where('id', $id)->get()->first();
            return $this->responseSuccess($book);
        } catch (\Exception $ex) {
            return $this->responseError([$ex->getMessage()], 'Something went wrong');
        }
    }
    

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function updateBook(Request $request, $id) {

        $book_check = Book::where('id', '!=', $id)
            ->where('title', $request->title)
            ->get()->toArray();
        
        if (count($book_check) > 0) {
            return $this->responseError('Tiêu đề này đã tồn tại', '', 200);
        }

        $request->validate([
                'title' => 'required',
                'describe' => 'required',
                'language' => 'required',
                'page_total' => 'required',
                'cover_image' => 'required',
                'producer' => 'required',
                'author' => 'required',
                'category_id' => 'required',
                'status' => 'required'
            ],
            [
                'title.required' => 'Tiêu đề là bắt buộc',
                'describe.required' => 'Mô tả là bắt buộc',
                'language.required' => 'Ngôn ngữ là bắt buộc',
                'page_total.required' => 'Tổng số trang là bắt buộc',
                'cover_image.required' => 'Ảnh bìa là bắt buộc',
                'author.required' => 'Tác giả là bắt buộc',
                'category_id.required' => 'Loại truyện là bắt buộc',
                'status.required' => 'Trạng thái là bắt buộc'
            ]
        );

        $book = Book::where('id', $id)->get()->first();
        $cover_image = $book->cover_image;
        $mp3 = $book->mp3;
        
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
                'page_total' => $request->page_total,
                'cover_image' => $cover_image,
                'producer' => $request->producer,
                'author' => $request->author,
                'content' => $request->title,
                'mp3' => $mp3,
                'category_id' => $request->category_id,
                'status' => $request->status,
            ]);

        $book = Book::where('id', $id)->get()->first();
        return $this->responseSuccess($book, 'Successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $book = Book::where('id', $id);
        if ($book->cover_image) {
            Storage::delete($book->cover_image);
        }
            
        if ($book->mp3) {
            Storage::delete($book->mp3);
        }

        $book->delete();
        return $this->responseSuccess([], 'Successfully deleted');
    }
}
