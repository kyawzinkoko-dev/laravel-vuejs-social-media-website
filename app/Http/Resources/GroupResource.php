<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class GroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'about' => $this->about,
            'role' => $this->currentUserGroup?->role,
            'status' => $this->currentUserGroup?->status,
            'auto_approve' => $this->auto_approve,
            'description' => Str::words($this->about, 5),
            'thumbnail_url' => $this->thumbnail_path ? Storage::url($this->thumbnail_path)  : '/img/default_background.jpeg',
            'cover_url' => $this->cover_path ? Storage::url($this->cover_path)  : '/img/default_background.jpeg',
            'created_at' => $this->created_at,
            'user_id' => $this->user_id,

        ];
    }
}
