<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeviceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'device_id'    => $this->device_id,
            'name'         => $this->name,
            'phone_number' => $this->phone_number,
            'status'       => $this->status ?? 'disconnected',
            'is_connected' => $this->isConnected(),
            'jid'          => $this->jid,
            'last_seen_at' => $this->last_seen_at?->toIso8601String(),
            'created_at'   => $this->created_at?->toIso8601String(),
            'updated_at'   => $this->updated_at?->toIso8601String(),
        ];
    }
}

