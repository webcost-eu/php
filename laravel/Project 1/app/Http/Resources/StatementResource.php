<?php

namespace App\Http\Resources;

use App\Models\Statement;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read Statement $resource
 */
class StatementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'client_first_name' => $this->resource->client_first_name,
            'client_last_name' => $this->resource->client_last_name,
            'ordered_date' => $this->resource->ordered_date?->format('d-m-Y'),
            'submission_date' => $this->resource->submission_date?->format('d-m-Y'),
            'receipt_date' => $this->resource->receipt_date?->format('d-m-Y'),
            'valid_from_date' => $this->resource->valid_from_date?->format('d-m-Y'),
            'valid_until_date' => $this->resource->valid_until_date?->format('d-m-Y'),
            'employer_id' => $this->resource->employer_id,
            'employer' => EmployerResource::make($this->whenLoaded('employer')),
            'client_id' => $this->resource->client_id,
            'client' => ClientResource::make($this->whenLoaded('client')),
            'lead_authority_id' => $this->resource->lead_authority_id,
            'lead_authority' => LeadAuthorityResource::make($this->whenLoaded('leadAuthority')),
            'status_id' => $this->resource->status_id,
            'status' => StatementStatusResource::make($this->whenLoaded('status')),
            'responsible_person_id' => $this->resource->responsible_person_id,
            'responsible_person' => UserResource::make($this->whenLoaded('responsiblePerson')),
            'comment' => $this->resource->comment,
            'files' => FileResource::collection($this->whenLoaded('files')),
            'history' => HistoryResource::collection($this->whenLoaded('history')),
            'created_at' => $this->resource->created_at?->format('d-m-Y'),
            'updated_at' => $this->resource->updated_at?->format('d-m-Y'),
        ];
    }
}
