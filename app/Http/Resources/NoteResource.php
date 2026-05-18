<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;

class NoteResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'event_date' => $this->event_date,
            'is_completed' => $this->is_completed,
            'created_at' => $this->created_at,
            'category_id' => $this->category_id,
            'category' => $this->category?->name,
            'comments_count' => $this->comments_count,
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
