<?php

declare(strict_types=1);

namespace Tigusigalpa\LunarCrush\Laravel;

use Illuminate\Support\Facades\Facade;
use Tigusigalpa\LunarCrush\LunarCrushClient;
use Tigusigalpa\LunarCrush\Resources\AiResource;
use Tigusigalpa\LunarCrush\Resources\CategoriesResource;
use Tigusigalpa\LunarCrush\Resources\CoinsResource;
use Tigusigalpa\LunarCrush\Resources\CreatorsResource;
use Tigusigalpa\LunarCrush\Resources\PostsResource;
use Tigusigalpa\LunarCrush\Resources\SearchesResource;
use Tigusigalpa\LunarCrush\Resources\StocksResource;
use Tigusigalpa\LunarCrush\Resources\SystemResource;
use Tigusigalpa\LunarCrush\Resources\TopicsResource;

/**
 * Laravel facade exposing the fluent LunarCrush API.
 *
 * @method static CoinsResource coins()
 * @method static TopicsResource topics()
 * @method static CategoriesResource categories()
 * @method static CreatorsResource creators()
 * @method static PostsResource posts()
 * @method static StocksResource stocks()
 * @method static SearchesResource searches()
 * @method static SystemResource system()
 * @method static AiResource ai()
 * @method static array request(string $method, string $path, array $query = [])
 *
 * @see LunarCrushClient
 */
final class LunarCrushFacade extends Facade
{
    /**
     * Get the registered name of the component in the service container.
     */
    protected static function getFacadeAccessor(): string
    {
        return LunarCrushClient::class;
    }
}
