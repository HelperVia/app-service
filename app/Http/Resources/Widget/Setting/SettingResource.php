<?php

namespace App\Http\Resources\Widget\Setting;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "prechatform" => $this->prechatform ?? null,
            "postchatform" => $this->postchatform ?? null,
            "widget" => $this->widget ?? null,
        ];
    }
}
