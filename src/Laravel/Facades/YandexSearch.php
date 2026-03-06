<?php

namespace Tigusigalpa\YandexSearch\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use Tigusigalpa\YandexSearch\Resources\WebSearchResource;
use Tigusigalpa\YandexSearch\Resources\GenSearchResource;
use Tigusigalpa\YandexSearch\Resources\ImageSearchResource;
use Tigusigalpa\YandexSearch\Resources\WordstatResource;
use Tigusigalpa\YandexSearch\Resources\OperationResource;

/**
 * @method static WebSearchResource web()
 * @method static GenSearchResource gen()
 * @method static ImageSearchResource images()
 * @method static WordstatResource wordstat()
 * @method static OperationResource operations()
 * @method static string getFolderId()
 * @method static \Tigusigalpa\YandexCloudClient\YandexCloudClient getCloudClient()
 * @method static \GuzzleHttp\ClientInterface getHttpClient()
 *
 * @see \Tigusigalpa\YandexSearch\YandexSearchClient
 */
class YandexSearch extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'yandex-search';
    }
}
