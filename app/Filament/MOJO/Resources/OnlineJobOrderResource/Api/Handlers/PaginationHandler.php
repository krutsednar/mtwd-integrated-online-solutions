<?php
namespace App\Filament\MOJO\Resources\OnlineJobOrderResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use App\Filament\MOJO\Resources\OnlineJobOrderResource;
use App\Filament\MOJO\Resources\OnlineJobOrderResource\Api\Transformers\OnlineJobOrderTransformer;

class PaginationHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = OnlineJobOrderResource::class;


    /**
     * List of OnlineJobOrder
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function handler()
    {
        $query = static::getEloquentQuery();

        $query = QueryBuilder::for($query)
        ->allowedFields($this->getAllowedFields() ?? [])
        ->allowedSorts($this->getAllowedSorts() ?? [])
        ->allowedFilters($this->getAllowedFilters() ?? [])
        ->allowedIncludes($this->getAllowedIncludes() ?? [])
        ->paginate(request()->query('per_page', 100))
        ->appends(request()->query());

        return OnlineJobOrderTransformer::collection($query);
    }
}
