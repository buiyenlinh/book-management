<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Category;

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
            'page_total' => $this->page_total,
            'cover_image' => $this->cover_image,
            'producer' => $this->producer,
            'author' => $this->author,
            'content' => $this->content,
            'mp3' => $this->mp3,
            'category' => new CategoryResource(Category::find($this->category_id)),
            'status' => $this->status,
            'username' => $this->username,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
