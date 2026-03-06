# Yandex Search PHP SDK

![Yandex Search PHP SDK](https://github.com/user-attachments/assets/863bba30-6a15-4eb6-bcf5-f3ed9c19139a)

[![Latest Version](https://img.shields.io/packagist/v/tigusigalpa/yandex-search-php.svg?style=flat-square)](https://packagist.org/packages/tigusigalpa/yandex-search-php)
[![PHP Version](https://img.shields.io/packagist/php-v/tigusigalpa/yandex-search-php.svg?style=flat-square)](https://packagist.org/packages/tigusigalpa/yandex-search-php)
[![License](https://img.shields.io/packagist/l/tigusigalpa/yandex-search-php.svg?style=flat-square)](https://packagist.org/packages/tigusigalpa/yandex-search-php)

PHP 8.0+ SDK, который даёт доступ ко всем
возможностям [Yandex Search API](https://aistudio.yandex.ru/docs/ru/search-api/concepts/index.html) прямо из вашего PHP-
или Laravel-приложения. Классический веб-поиск, генеративные ответы от ИИ, поиск картинок, аналитика ключевых слов — всё
в одном пакете.

> Часть экосистемы Яндекса для PHP:  
> [yandex-cloud-client-php](https://github.com/tigusigalpa/yandex-cloud-client-php) · [yandexgpt-php](https://github.com/tigusigalpa/yandexgpt-php)

[🇬🇧 English version](README-en.md)

> 📖 **[Полная документация доступна на Wiki](https://github.com/tigusigalpa/yandex-search-php/wiki)**

## Что внутри

- **Веб-поиск** — текстовый поиск с сортировкой, группировкой, регионами и семейным фильтром
- **Генеративный поиск** — задайте вопрос, получите ответ от ИИ с реальными источниками (на базе YandexGPT)
- **Поиск изображений** — ищите по описанию или загружайте картинку для обратного поиска
- **Wordstat** — популярность ключевых слов, динамика, распределение по регионам (как Яндекс Вордстат, только через API)
- **Асинхронность** — запускайте поиск и забирайте результат позже
- **Типобезопасные DTO** — каждый ответ API приходит в виде объекта, а не сырого массива
- **Готовность к Laravel** — service provider, фасад, публикация конфига, DI — всё из коробки
- **Тестируемость** — подставьте мок HTTP-клиента и тестируйте без обращений к реальному API

## Требования

- PHP 8.0+
- Аккаунт [Yandex Cloud](https://cloud.yandex.ru/) с подключённым Search API
- OAuth-токен (как получить — см. [oauth.yandex.ru](https://oauth.yandex.ru/))

## Установка

```bash
composer require tigusigalpa/yandex-search-php
```

Пакет зависит от [`tigusigalpa/yandex-cloud-client-php`](https://github.com/tigusigalpa/yandex-cloud-client-php) для
аутентификации

## Быстрый старт

### Обычный PHP

Три строки до первого поиска:

```php
<?php

require_once 'vendor/autoload.php';

use Tigusigalpa\YandexCloudClient\YandexCloudClient;
use Tigusigalpa\YandexSearch\YandexSearchClient;

$cloudClient = new YandexCloudClient('ВАШ_OAUTH_ТОКЕН');
$searchClient = new YandexSearchClient($cloudClient, 'ВАШ_FOLDER_ID');

$results = $searchClient->web()->search('Laravel PHP framework');

echo "Найдено: {$results->found} результатов\n";
foreach ($results->documents as $doc) {
    echo "{$doc->title} — {$doc->url}\n";
}
```

Вот и всё. Облачный клиент сам обменивает OAuth-токен на IAM-токен, вам не нужно думать об авторизации.

### Laravel

#### 1. Опубликуйте конфиг

```bash
php artisan vendor:publish --tag=yandex-search-config
```

#### 2. Добавьте ключи в `.env`

```env
YANDEX_OAUTH_TOKEN=ваш_oauth_токен
YANDEX_FOLDER_ID=ваш_folder_id
```

#### 3. Используйте фасад где угодно

```php
use Tigusigalpa\YandexSearch\Laravel\Facades\YandexSearch;

// Обычный веб-поиск
$results = YandexSearch::web()->search('Laravel');

// Или спросите ИИ
$response = YandexSearch::gen()->search([
    ['role' => 'ROLE_USER', 'content' => 'Что такое Laravel?']
]);

echo $response->answer;
```

#### 4. Или инжектите клиент

```php
namespace App\Http\Controllers;

use Tigusigalpa\YandexSearch\YandexSearchClient;

class SearchController extends Controller
{
    public function __construct(
        private YandexSearchClient $yandexSearch
    ) {}

    public function search(Request $request)
    {
        $results = $this->yandexSearch->web()->search(
            $request->input('query')
        );

        return view('search.results', compact('results'));
    }
}
```

---

## Справочник API

### Веб-поиск

Основа основ — текстовый поиск по вебу. Результат приходит в виде `SearchResultDTO` с коллекцией объектов
`SearchDocumentDTO`.

#### Простой поиск

```php
$results = $searchClient->web()->search('PHP программирование');

echo "Найдено: {$results->found} результатов\n";
echo "Найдено (читаемый формат): {$results->foundHuman}\n";

foreach ($results->documents as $doc) {
    echo "Заголовок: {$doc->title}\n";
    echo "URL: {$doc->url}\n";
    echo "Домен: {$doc->domain}\n";
    echo "Отрывок: {$doc->passage}\n";
}
```

#### Тонкая настройка

Передайте массив опций — сортировка, группировка, пагинация, регион и многое другое:

```php
$results = $searchClient->web()->search('Laravel framework', [
    'searchType' => 'SEARCH_TYPE_RU',
    'familyMode' => 'FAMILY_MODE_MODERATE',
    'page' => 0,
    'sortSpec' => [
        'sortMode' => 'SORT_MODE_BY_TIME',
        'sortOrder' => 'SORT_ORDER_DESC'
    ],
    'groupSpec' => [
        'groupMode' => 'GROUP_MODE_DEEP',
        'groupsOnPage' => 10,
        'docsInGroup' => 3
    ],
    'maxPassages' => 3,
    'region' => 213, // Москва
]);
```

#### Асинхронный поиск

Не хотите ждать? Запустите поиск и заберите результат позже:

```php
$operation = $searchClient->web()->searchAsync('PHP уроки');

echo "ID операции: {$operation->id}\n";

// ...через какое-то время...
$result = $searchClient->operations()->get($operation->id);

if ($result->done) {
    $searchResults = $result->response; // SearchResultDTO
    echo "Найдено: {$searchResults->found} результатов\n";
}
```

#### Справочник параметров

| Параметр              | Тип    | Что делает                                                                                                                    |
|-----------------------|--------|-------------------------------------------------------------------------------------------------------------------------------|
| `searchType`          | string | Поисковый индекс: `SEARCH_TYPE_RU`, `SEARCH_TYPE_TR`, `SEARCH_TYPE_COM`, `SEARCH_TYPE_KK`, `SEARCH_TYPE_BE`, `SEARCH_TYPE_UZ` |
| `familyMode`          | string | Фильтр контента: `FAMILY_MODE_NONE`, `FAMILY_MODE_MODERATE`, `FAMILY_MODE_STRICT`                                             |
| `page`                | int    | Номер страницы, начиная с 0                                                                                                   |
| `fixTypoMode`         | string | Автоисправление опечаток: `FIX_TYPO_MODE_ON`, `FIX_TYPO_MODE_OFF`                                                             |
| `sortSpec.sortMode`   | string | `SORT_MODE_BY_RELEVANCE` или `SORT_MODE_BY_TIME`                                                                              |
| `sortSpec.sortOrder`  | string | `SORT_ORDER_ASC` или `SORT_ORDER_DESC`                                                                                        |
| `groupSpec.groupMode` | string | `GROUP_MODE_FLAT` или `GROUP_MODE_DEEP`                                                                                       |
| `maxPassages`         | int    | Сколько текстовых сниппетов возвращать на документ                                                                            |
| `region`              | int    | ID региона Яндекса для геозависимых результатов                                                                               |

---

### Генеративный поиск

А вот тут начинается самое интересное. Генеративный эндпоинт отправляет ваш вопрос в YandexGPT, который анализирует
реальные результаты поиска и формирует ответ со ссылками на источники.

```php
$response = $searchClient->gen()->search([
    ['role' => 'ROLE_USER', 'content' => 'Что такое фреймворк Laravel?']
]);

echo "Ответ: {$response->answer}\n";
echo "Формат списком: " . ($response->isBulletAnswer ? 'да' : 'нет') . "\n";
echo "Отклонён: " . ($response->isAnswerRejected ? 'да' : 'нет') . "\n";

foreach ($response->sources as $source) {
    echo "- {$source->title} ({$source->url})" . ($source->used ? ' [использован]' : '') . "\n";
}
```

#### Ограничение области поиска

Параметры `site`, `host` и `url` взаимоисключающие — выберите один, чтобы ограничить, где ИИ ищет информацию:

```php
// Искать только на laravel.com
$response = $searchClient->gen()->search(
    [['role' => 'ROLE_USER', 'content' => 'Laravel маршрутизация']],
    ['site' => 'laravel.com']
);

// Искать только на php.net
$response = $searchClient->gen()->search(
    [['role' => 'ROLE_USER', 'content' => 'PHP лучшие практики']],
    ['host' => 'php.net']
);

// Искать только по конкретному URL
$response = $searchClient->gen()->search(
    [['role' => 'ROLE_USER', 'content' => 'Руководство по установке']],
    ['url' => 'https://laravel.com/docs']
);
```

#### Многоходовой диалог

Нужны уточняющие вопросы? Просто передайте полную историю сообщений:

```php
$messages = [
    ['role' => 'ROLE_USER', 'content' => 'Что такое Laravel?'],
    ['role' => 'ROLE_ASSISTANT', 'content' => 'Laravel — это PHP веб-фреймворк...'],
    ['role' => 'ROLE_USER', 'content' => 'Как его установить?']
];

$response = $searchClient->gen()->search($messages);
```

---

### Поиск изображений

Ищите картинки по текстовому описанию или загрузите своё изображение для обратного поиска.

#### По тексту

```php
$results = $searchClient->images()->search('закат над горами');

echo "Всего: {$results->total} изображений\n";

foreach ($results->images as $image) {
    echo "URL: {$image->url}\n";
    echo "Формат: {$image->format}, {$image->width}x{$image->height}\n";
    echo "Источник: {$image->pageTitle} ({$image->pageUrl})\n";
}
```

#### С фильтрами

Отфильтруйте по формату, размеру, ориентации или доминирующему цвету:

```php
$results = $searchClient->images()->search('кошки', [
    'imageSpec' => [
        'format' => 'IMAGE_FORMAT_JPEG',
        'size' => 'IMAGE_SIZE_LARGE',
        'orientation' => 'IMAGE_ORIENTATION_HORIZONTAL',
        'color' => 'IMAGE_COLOR_COLOR'
    ]
]);
```

**Справочник фильтров:**

| Фильтр        | Значения                                                                                                                                                                                                                                 |
|---------------|------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `format`      | `IMAGE_FORMAT_JPEG`, `IMAGE_FORMAT_GIF`, `IMAGE_FORMAT_PNG`                                                                                                                                                                              |
| `size`        | `IMAGE_SIZE_ENORMOUS`, `IMAGE_SIZE_LARGE`, `IMAGE_SIZE_MEDIUM`, `IMAGE_SIZE_SMALL`, `IMAGE_SIZE_TINY`, `IMAGE_SIZE_WALLPAPER`                                                                                                            |
| `orientation` | `IMAGE_ORIENTATION_VERTICAL`, `IMAGE_ORIENTATION_HORIZONTAL`, `IMAGE_ORIENTATION_SQUARE`                                                                                                                                                 |
| `color`       | `IMAGE_COLOR_COLOR`, `IMAGE_COLOR_GRAYSCALE`, `IMAGE_COLOR_RED`, `IMAGE_COLOR_ORANGE`, `IMAGE_COLOR_YELLOW`, `IMAGE_COLOR_GREEN`, `IMAGE_COLOR_CYAN`, `IMAGE_COLOR_BLUE`, `IMAGE_COLOR_VIOLET`, `IMAGE_COLOR_WHITE`, `IMAGE_COLOR_BLACK` |

#### Обратный поиск по изображению

Есть картинка и хотите найти похожие? Три способа передать исходное изображение:

```php
// По URL
$results = $searchClient->images()->searchByImage([
    'url' => 'https://example.com/image.jpg'
]);

// По данным (base64)
$imageData = base64_encode(file_get_contents('path/to/image.jpg'));
$results = $searchClient->images()->searchByImage([
    'data' => $imageData
]);

// По CBIR ID из предыдущего поиска
$results = $searchClient->images()->searchByImage([
    'id' => 'cbir_id_из_предыдущего_поиска'
]);
```

---

### Wordstat

Аналитика ключевых слов — как [Яндекс Вордстат](https://wordstat.yandex.ru/), только через API, а значит, можно
автоматизировать.

#### Топ связанных фраз

Что ещё ищут вместе с вашим ключевым словом?

```php
$top = $searchClient->wordstat()->getTop('Laravel framework', [
    'numPhrases' => 50,
    'regions' => [213], // Москва
    'devices' => 'DEVICE_ALL'
]);

echo "Фраза: {$top->phrase}\n";
foreach ($top->topPhrases as $phrase) {
    echo "- {$phrase['phrase']}: {$phrase['count']} запросов\n";
}
```

#### Динамика во времени

Как менялась популярность ключевого слова:

```php
$dynamics = $searchClient->wordstat()->getDynamics('PHP программирование', [
    'regions' => [213],
    'devices' => 'DEVICE_DESKTOP'
]);

foreach ($dynamics->dynamics as $period) {
    echo "{$period['date']}: {$period['count']} запросов\n";
}
```

#### Распределение по регионам

В каких регионах ваше ключевое слово популярно?

```php
$distribution = $searchClient->wordstat()->getRegionsDistribution('Laravel');

foreach ($distribution->regionsDistribution as $region) {
    echo "{$region['name']}: {$region['count']}\n";
}
```

#### Дерево регионов

Полная иерархия кодов регионов (пригодится для построения фильтров):

```php
$tree = $searchClient->wordstat()->getRegionTree();

foreach ($tree->regions as $region) {
    echo "ID: {$region['id']} — {$region['name']}\n";
}
```

**Фильтр по устройствам:** `DEVICE_ALL`, `DEVICE_DESKTOP`, `DEVICE_PHONE`, `DEVICE_TABLET`

---

## Конфигурация

| Параметр      | Переменная окружения | Описание                       |
|---------------|----------------------|--------------------------------|
| `folder_id`   | `YANDEX_FOLDER_ID`   | ID каталога Yandex Cloud       |
| `oauth_token` | `YANDEX_OAUTH_TOKEN` | OAuth-токен для аутентификации |

**Где взять:**

1. **OAuth-токен** — зайдите на [oauth.yandex.ru](https://oauth.yandex.ru/), создайте приложение и получите токен с
   нужными правами
2. **Folder ID** — откройте [консоль Yandex Cloud](https://console.cloud.yandex.ru/), создайте или выберите каталог и
   скопируйте его ID

## Как работает аутентификация

Вам не нужно думать про IAM-токены, их обновление или заголовки авторизации. Всю эту работу берёт на себя [
`yandex-cloud-client-php`](https://github.com/tigusigalpa/yandex-cloud-client-php):

- конвертирует OAuth-токен в IAM-токен
- автоматически обновляет его до истечения срока
- подставляет заголовок `Authorization: Bearer <IAM_TOKEN>` в каждый запрос

Просто передайте OAuth-токен и folder ID — остальное произойдёт само.

## Тестирование

```bash
# Запуск тестов
composer test

# Статический анализ (PHPStan level 8)
composer phpstan

# Проверка стиля кода (PSR-12)
composer cs-check

# Автоисправление стиля
composer cs-fix
```

## Участие в разработке

Нашли баг? Есть идея? PR приветствуются.

1. Форкните репозиторий
2. Создайте ветку (`git checkout -b feature/my-feature`)
3. Закоммитьте изменения
4. Запушьте и откройте Pull Request

## Лицензия

MIT — полный текст в файле [LICENSE](LICENSE).

## Полезные ссылки

- [Документация Yandex Search API](https://aistudio.yandex.ru/docs/ru/search-api/concepts/index.html)
- [Yandex AI Studio](https://aistudio.yandex.ru/)
- [Этот пакет на GitHub](https://github.com/tigusigalpa/yandex-search-php)
- [Этот пакет на Packagist](https://packagist.org/packages/tigusigalpa/yandex-search-php)
- [yandex-cloud-client-php](https://github.com/tigusigalpa/yandex-cloud-client-php) — слой аутентификации, на котором
  построен этот пакет
- [yandexgpt-php](https://github.com/tigusigalpa/yandexgpt-php) — PHP SDK для генерации текста через YandexGPT и
  YandexART

## Автор

**Игорь Сазонов** — [@tigusigalpa](https://github.com/tigusigalpa) · [sovletig@gmail.com](mailto:sovletig@gmail.com)
