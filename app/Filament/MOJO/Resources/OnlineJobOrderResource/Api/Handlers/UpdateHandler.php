<?php
namespace App\Filament\MOJO\Resources\OnlineJobOrderResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\MOJO\Resources\OnlineJobOrderResource;
use App\Filament\MOJO\Resources\OnlineJobOrderResource\Api\Requests\UpdateOnlineJobOrderRequest;

class UpdateHandler extends Handlers {
    public static string | null $uri = '/{id}';
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
        $id = $request->route('id');

        $model = static::getModel()::find($id);

        if (!$model) return static::sendNotFoundResponse();

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Update Resource");
    }
}