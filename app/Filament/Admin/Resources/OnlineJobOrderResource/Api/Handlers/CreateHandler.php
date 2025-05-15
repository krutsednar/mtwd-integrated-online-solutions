<?php
namespace App\Filament\Admin\Resources\OnlineJobOrderResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Admin\Resources\OnlineJobOrderResource;
use App\Filament\Admin\Resources\OnlineJobOrderResource\Api\Requests\CreateOnlineJobOrderRequest;

class CreateHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = OnlineJobOrderResource::class;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Create OnlineJobOrder
     *
     * @param CreateOnlineJobOrderRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateOnlineJobOrderRequest $request)
    {
        $model = new (static::getModel());

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}