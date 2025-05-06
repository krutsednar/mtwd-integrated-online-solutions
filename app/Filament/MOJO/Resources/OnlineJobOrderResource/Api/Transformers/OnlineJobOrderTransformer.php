<?php
namespace App\Filament\MOJO\Resources\OnlineJobOrderResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\OnlineJobOrder;

/**
 * @property OnlineJobOrder $resource
 */
class OnlineJobOrderTransformer extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->resource->toArray();
    }
}
