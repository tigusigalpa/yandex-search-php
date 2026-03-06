<?php

namespace Tigusigalpa\YandexSearch\Laravel;

use Illuminate\Support\ServiceProvider;
use Tigusigalpa\YandexCloudClient\YandexCloudClient;
use Tigusigalpa\YandexSearch\YandexSearchClient;

class YandexSearchServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/yandex-search.php',
            'yandex-search'
        );

        $this->app->singleton(YandexSearchClient::class, function ($app) {
            $oauthToken = config('yandex-search.oauth_token');
            $folderId = config('yandex-search.folder_id');

            if (empty($oauthToken)) {
                throw new \RuntimeException(
                    'Yandex OAuth token is not configured. Please set YANDEX_OAUTH_TOKEN in your .env file.'
                );
            }

            if (empty($folderId)) {
                throw new \RuntimeException(
                    'Yandex Folder ID is not configured. Please set YANDEX_FOLDER_ID in your .env file.'
                );
            }

            $cloudClient = $app->make(YandexCloudClient::class);

            return new YandexSearchClient($cloudClient, $folderId);
        });

        $this->app->alias(YandexSearchClient::class, 'yandex-search');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/yandex-search.php' => config_path('yandex-search.php'),
            ], 'yandex-search-config');
        }
    }

    public function provides(): array
    {
        return [
            YandexSearchClient::class,
            'yandex-search',
        ];
    }
}
