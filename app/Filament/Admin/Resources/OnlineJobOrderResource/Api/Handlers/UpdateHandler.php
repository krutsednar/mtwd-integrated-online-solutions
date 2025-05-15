<?php
namespace App\Filament\Admin\Resources\OnlineJobOrderResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Admin\Resources\OnlineJobOrderResource;
use App\Filament\Admin\Resources\OnlineJobOrderResource\Api\Requests\UpdateOnlineJobOrderRequest;

class UpdateHandler extends Handlers {
    public static string | null $uri = '/{jo_number}';
    public static string | null $resource = OnlineJobOrderResource::class;

    public static function getMethod()
    {
        return Handlers::PUT;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }


    /**
     * Update OnlineJobOrder
     *
     * @param UpdateOnlineJobOrderRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(UpdateOnlineJobOrderRequest $request)
    {
        $jo_number = $request->route('jo_number');

        $model = static::getModel()::find($jo_number);

        if (!$model) return static::sendNotFoundResponse();

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Update Record");
    }
}
