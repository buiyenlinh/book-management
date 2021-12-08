<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Category;
use App\Models\Author;
use App\Models\Content;
use App\Http\Resources\AuthorResource;
use App\Http\Resources\ContentResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'title' => $this->title,
            'describe' => $this->describe,
            'language' => $this->language,
            'cover_image' => $this->cover_image,
            'release_year' => $this->release_year,
            'producer' => $this->producer,
            'author' => new AuthorResource(Author::find($this->author_id)),
            'content' => new ContentResource(Content::where('book_id', $this->id)->get()),
            'mp3' => $this->mp3,
            'category' => new CategoryResource(Category::find($this->category_id)),
            'status' => $this->status,
            'username' => $this->username,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
