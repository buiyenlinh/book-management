<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\RoleResource;

use App\Models\Role;

class UserResource extends JsonResource
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
            'fullname' => $this->fullname,
            'username' => $this->username,
            'active' => $this->active,
            'gender' => $this->gender,
            'birthday' => $this->birthday,
            'address' => $this->address,
            'avatar' => $this->avatar,
            // 'role' => new RoleResource($this->role()->first()), // role () là function được defined trong Model User
            'role' => new RoleResource(Role::find($this->role_id)), // Sử dụng này cũng được, nhưng phải khai báo thêm Model Role
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
