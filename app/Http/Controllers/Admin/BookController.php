<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\BookRequest;

use App\Models\Book;
use App\Models\User;
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

            $cover_image = $request->file('cover_image')->store('public');
            $book = Book::create([
                'title' => $request->title,
                'describe' => $request->describe,
                'language' => $request->language,
                'page_total' => $request->page_total,
                'cover_image' => $cover_image,
                'producer' => $request->producer,
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
        $book_check = Book::where('id', '!=', $id)
            ->where('title', $request->title)
            ->get()->toArray();
        if (count($book_check) > 0) {
            return $this->responseError('Tiêu đề này đã tồn tại');
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
        //
    }
}
