<?php

namespace App\Filament\Admin\Resources\OnlineJobOrderResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Admin\Resources\OnlineJobOrderResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Admin\Resources\OnlineJobOrderResource\Api\Transformers\OnlineJobOrderTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = OnlineJobOrderResource::class;


    /**
     * Show OnlineJobOrder
     *
     * @param Request $request
     * @return OnlineJobOrderTransformer
     */
    public function handler(Request $request)
    {
        $id = $request->route('id');
        
        $query = static::getEloquentQuery();

        $query = QueryBuilder::for(
            $query->where(static::getKeyName(), $id)
        )
            ->first();

        if (!$query) return static::sendNotFoundResponse();

        return new OnlineJobOrderTransformer($query);
    }
}
