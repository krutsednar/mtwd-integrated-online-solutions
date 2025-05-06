<?php
namespace App\Filament\MOJO\Resources\OnlineJobOrderResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\MOJO\Resources\OnlineJobOrderResource;
use Illuminate\Routing\Router;


class OnlineJobOrderApiService extends ApiService
{
    protected static string | null $resource = OnlineJobOrderResource::class;

    public static function handlers() : array
    {
        return [
            Handlers\CreateHandler::class,
            Handlers\UpdateHandler::class,
            Handlers\DeleteHandler::class,
            Handlers\PaginationHandler::class,
            Handlers\DetailHandler::class
        ];

    }
}
