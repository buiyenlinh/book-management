<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\AuthorCollection;
use App\Http\Resources\AuthorResource;
use App\Models\Author;
use App\Models\Book;
use App\Http\InitData;
use App\Http\Requests\AuthorRequest;

class AuthorController extends Controller
{
    use InitData;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $author = new AuthorCollection(Author::all());
            return $this->responseSuccess($author, 'Danh sách tác giả');
        } catch (\Exception $ex) {
           return $this->responseError([$ex->getMessage()], 'Đã xảy ra lỗi! Vui lòng thử lại!');
        }
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
    public function store(AuthorRequest $request)
    {
        try {
            $author = Author::create([
                'fullname' => $request->fullname,
                'introduce' => $request->introduce
            ]);

            return $this->responseSuccess($author, 'Thêm tác giả thành công!');
        } catch (\Exception $ex) {
            return $this->reponseError([$ex->getMessage()], 'Đã xảy ra lỗi! Vui lòng thử lại!');
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
            $author = new AuthorResource(Author::find($id));
            return $this->responseSuccess($author);
        } catch (\Exception $ex) {
            return $this->responseError([$ex->getMessage()], 'Vui lòng thử lại!');
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
        try {
            if ($request->fullname) {
                $author = Author::find($id)
                ->update([
                    'fullname' => $request->fullname,
                    'introduce' => $request->introduce
                ]);
                $author = new AuthorResource(Author::find($id));
                return $this->responseSuccess($author, 'Cập nhật tác giả thành công!');
            } else {
                return $this->responseError('Họ tên tác giả là bắt buộc');
            }
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
        try {
            $author = Author::find($id);
            if ($author) {
                $book = Book::where('author_id', $id)->get();
                if (count($book) > 0) {
                    return $this->responseError('Tác giả này có sách trong hệ thống không được xóa');
                }
                $author->delete();
                return $this->responseSuccess([], 'Xóa tác giả thành công');
            } else {
                return $this->responseError('Tác giả này không tồn tại!');
            }
        } catch (\Exception $ex) {
            return $this->responseError([$ex->getMessage()], 'Vui lòng thử lại!');
        }
    }
}
