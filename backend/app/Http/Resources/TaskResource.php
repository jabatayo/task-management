<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
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
            'status' => $this->status,
            'priority' => $this->priority,
            'due_date' => $this->due_date?->format('Y-m-d'),
            'tags' => $this->tags,
            'created_by' => $this->created_by,
            'assigned_to' => $this->assigned_to,
            'created_by_user' => $this->createdBy ? [
                'id' => $this->createdBy->id,
                'name' => $this->createdBy->name,
                'email' => $this->createdBy->email,
                'roles' => $this->createdBy->roles ?? [],
                'created_at' => $this->createdBy->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $this->createdBy->updated_at->format('Y-m-d H:i:s'),
            ] : null,
            'assigned_to_user' => $this->assignedTo ? [
                'id' => $this->assignedTo->id,
                'name' => $this->assignedTo->name,
                'email' => $this->assignedTo->email,
                'roles' => $this->assignedTo->roles ?? [],
                'created_at' => $this->assignedTo->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $this->assignedTo->updated_at->format('Y-m-d H:i:s'),
            ] : null,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
