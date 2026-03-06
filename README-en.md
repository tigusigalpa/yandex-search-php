# Yandex Search PHP SDK

![Yandex Search PHP SDK](https://github.com/user-attachments/assets/863bba30-6a15-4eb6-bcf5-f3ed9c19139a)

[![Latest Version](https://img.shields.io/packagist/v/tigusigalpa/yandex-search-php.svg?style=flat-square)](https://packagist.org/packages/tigusigalpa/yandex-search-php)
[![PHP Version](https://img.shields.io/packagist/php-v/tigusigalpa/yandex-search-php.svg?style=flat-square)](https://packagist.org/packages/tigusigalpa/yandex-search-php)
[![License](https://img.shields.io/packagist/l/tigusigalpa/yandex-search-php.svg?style=flat-square)](https://packagist.org/packages/tigusigalpa/yandex-search-php)

A PHP 8.0+ SDK that lets you tap into the full power
of [Yandex Search API](https://aistudio.yandex.ru/docs/ru/search-api/concepts/index.html) from your PHP or Laravel
application. Whether you need classic web search, AI-generated answers, image lookup, or keyword analytics — this
package has you covered.

> Part of the Yandex ecosystem for PHP:  
> [yandex-cloud-client-php](https://github.com/tigusigalpa/yandex-cloud-client-php) · [yandexgpt-php](https://github.com/tigusigalpa/yandexgpt-php)

[🇷🇺 Читать на русском](README.md)

## What's Inside

- **Web Search** — classic text search with sorting, grouping, regions, and family filters
- **Generative Search** — ask a question, get an AI-synthesized answer backed by real sources (powered by YandexGPT)
- **Image Search** — find images by description or do a reverse image lookup
- **Wordstat** — keyword popularity, trends over time, and regional breakdown (think Yandex Wordstat via API)
- **Async support** — fire off a search and poll for results later
- **Type-safe DTOs** — every API response is mapped to a dedicated object, no guessing at array keys
- **Laravel-ready** — service provider, facade, config publishing, dependency injection — all out of the box
- **Testable** — inject a mock HTTP client and test without hitting the real API

## Requirements

- PHP 8.0+
- A [Yandex Cloud](https://cloud.yandex.ru/) account with Search API enabled
- An OAuth token (see [how to get one](https://oauth.yandex.ru/))

## Installation

```bash
composer require tigusigalpa/yandex-search-php
```

The package depends on [`tigusigalpa/yandex-cloud-client-php`](https://github.com/tigusigalpa/yandex-cloud-client-php)
for authentication, so make sure it's installed too:

```bash
composer require tigusigalpa/yandex-cloud-client-php
```

## Quick Start

### Plain PHP

Three lines to your first search:

```php
<?php

require_once 'vendor/autoload.php';

use Tigusigalpa\YandexCloudClient\YandexCloudClient;
use Tigusigalpa\YandexSearch\YandexSearchClient;

$cloudClient = new YandexCloudClient('YOUR_OAUTH_TOKEN');
$searchClient = new YandexSearchClient($cloudClient, 'YOUR_FOLDER_ID');

$results = $searchClient->web()->search('Laravel PHP framework');

echo "Found: {$results->found} results\n";
foreach ($results->documents as $doc) {
    echo "{$doc->title} — {$doc->url}\n";
}
```

That's it. The cloud client handles the OAuth → IAM token exchange under the hood, so you never deal with tokens
directly.

### Laravel

#### 1. Publish the config

```bash
php artisan vendor:publish --tag=yandex-search-config
```

#### 2. Add credentials to `.env`

```env
YANDEX_OAUTH_TOKEN=your_oauth_token_here
YANDEX_FOLDER_ID=your_folder_id_here
```

#### 3. Use the facade anywhere

```php
use Tigusigalpa\YandexSearch\Laravel\Facades\YandexSearch;

// Classic web search
$results = YandexSearch::web()->search('Laravel');

// Or ask the AI
$response = YandexSearch::gen()->search([
    ['role' => 'ROLE_USER', 'content' => 'What is Laravel?']
]);

echo $response->answer;
```

#### 4. Or inject the client

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

## API Reference

### Web Search

The bread and butter — text search across the web. Results come back as a `SearchResultDTO` containing a collection of
`SearchDocumentDTO` objects.

#### Simple search

```php
$results = $searchClient->web()->search('PHP programming');

echo "Found: {$results->found} results\n";
echo "Found (human-readable): {$results->foundHuman}\n";

foreach ($results->documents as $doc) {
    echo "Title: {$doc->title}\n";
    echo "URL: {$doc->url}\n";
    echo "Domain: {$doc->domain}\n";
    echo "Passage: {$doc->passage}\n";
}
```

#### Fine-tuned search

Pass an array of options to control sorting, grouping, pagination, region, and more:

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
    'region' => 213, // Moscow
]);
```

#### Async search

Don't want to wait? Fire a search and check back later:

```php
$operation = $searchClient->web()->searchAsync('PHP tutorials');

echo "Operation ID: {$operation->id}\n";

// ...some time later...
$result = $searchClient->operations()->get($operation->id);

if ($result->done) {
    $searchResults = $result->response; // SearchResultDTO
    echo "Found: {$searchResults->found} results\n";
}
```

#### Parameter reference

| Parameter             | Type   | What it does                                                                                                              |
|-----------------------|--------|---------------------------------------------------------------------------------------------------------------------------|
| `searchType`          | string | Search index: `SEARCH_TYPE_RU`, `SEARCH_TYPE_TR`, `SEARCH_TYPE_COM`, `SEARCH_TYPE_KK`, `SEARCH_TYPE_BE`, `SEARCH_TYPE_UZ` |
| `familyMode`          | string | Content filter: `FAMILY_MODE_NONE`, `FAMILY_MODE_MODERATE`, `FAMILY_MODE_STRICT`                                          |
| `page`                | int    | Page number, 0-based                                                                                                      |
| `fixTypoMode`         | string | Auto-correct typos: `FIX_TYPO_MODE_ON`, `FIX_TYPO_MODE_OFF`                                                               |
| `sortSpec.sortMode`   | string | `SORT_MODE_BY_RELEVANCE` or `SORT_MODE_BY_TIME`                                                                           |
| `sortSpec.sortOrder`  | string | `SORT_ORDER_ASC` or `SORT_ORDER_DESC`                                                                                     |
| `groupSpec.groupMode` | string | `GROUP_MODE_FLAT` or `GROUP_MODE_DEEP`                                                                                    |
| `maxPassages`         | int    | How many text snippets to return per document                                                                             |
| `region`              | int    | Yandex region ID for geo-targeted results                                                                                 |

---

### Generative Search

This is where it gets interesting. The generative endpoint sends your question to YandexGPT, which reads real search
results and synthesizes an answer with source links.

```php
$response = $searchClient->gen()->search([
    ['role' => 'ROLE_USER', 'content' => 'What is the Laravel framework?']
]);

echo "Answer: {$response->answer}\n";
echo "Bullet format: " . ($response->isBulletAnswer ? 'yes' : 'no') . "\n";
echo "Rejected: " . ($response->isAnswerRejected ? 'yes' : 'no') . "\n";

foreach ($response->sources as $source) {
    echo "- {$source->title} ({$source->url})" . ($source->used ? ' [used]' : '') . "\n";
}
```

#### Narrow the search scope

The `site`, `host`, and `url` parameters are mutually exclusive — pick one to restrict where the AI looks for
information:

```php
// Only search within laravel.com
$response = $searchClient->gen()->search(
    [['role' => 'ROLE_USER', 'content' => 'Laravel routing']],
    ['site' => 'laravel.com']
);

// Only search within php.net
$response = $searchClient->gen()->search(
    [['role' => 'ROLE_USER', 'content' => 'PHP best practices']],
    ['host' => 'php.net']
);

// Only search a specific URL
$response = $searchClient->gen()->search(
    [['role' => 'ROLE_USER', 'content' => 'Installation guide']],
    ['url' => 'https://laravel.com/docs']
);
```

#### Multi-turn conversations

Want follow-up questions? Just pass the full message history:

```php
$messages = [
    ['role' => 'ROLE_USER', 'content' => 'What is Laravel?'],
    ['role' => 'ROLE_ASSISTANT', 'content' => 'Laravel is a PHP web framework...'],
    ['role' => 'ROLE_USER', 'content' => 'How do I install it?']
];

$response = $searchClient->gen()->search($messages);
```

---

### Image Search

Find images by text description, or do a reverse lookup starting from an image you already have.

#### By text

```php
$results = $searchClient->images()->search('sunset over mountains');

echo "Total: {$results->total} images\n";

foreach ($results->images as $image) {
    echo "URL: {$image->url}\n";
    echo "Format: {$image->format}, {$image->width}x{$image->height}\n";
    echo "From: {$image->pageTitle} ({$image->pageUrl})\n";
}
```

#### With filters

Narrow down by format, size, orientation, or dominant color:

```php
$results = $searchClient->images()->search('cats', [
    'imageSpec' => [
        'format' => 'IMAGE_FORMAT_JPEG',
        'size' => 'IMAGE_SIZE_LARGE',
        'orientation' => 'IMAGE_ORIENTATION_HORIZONTAL',
        'color' => 'IMAGE_COLOR_COLOR'
    ]
]);
```

**Filter reference:**

| Filter        | Values                                                                                                                                                                                                                                   |
|---------------|------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `format`      | `IMAGE_FORMAT_JPEG`, `IMAGE_FORMAT_GIF`, `IMAGE_FORMAT_PNG`                                                                                                                                                                              |
| `size`        | `IMAGE_SIZE_ENORMOUS`, `IMAGE_SIZE_LARGE`, `IMAGE_SIZE_MEDIUM`, `IMAGE_SIZE_SMALL`, `IMAGE_SIZE_TINY`, `IMAGE_SIZE_WALLPAPER`                                                                                                            |
| `orientation` | `IMAGE_ORIENTATION_VERTICAL`, `IMAGE_ORIENTATION_HORIZONTAL`, `IMAGE_ORIENTATION_SQUARE`                                                                                                                                                 |
| `color`       | `IMAGE_COLOR_COLOR`, `IMAGE_COLOR_GRAYSCALE`, `IMAGE_COLOR_RED`, `IMAGE_COLOR_ORANGE`, `IMAGE_COLOR_YELLOW`, `IMAGE_COLOR_GREEN`, `IMAGE_COLOR_CYAN`, `IMAGE_COLOR_BLUE`, `IMAGE_COLOR_VIOLET`, `IMAGE_COLOR_WHITE`, `IMAGE_COLOR_BLACK` |

#### Reverse image search

Got an image and want to find similar ones? Three ways to pass it:

```php
// By URL
$results = $searchClient->images()->searchByImage([
    'url' => 'https://example.com/image.jpg'
]);

// By raw data (base64-encoded)
$imageData = base64_encode(file_get_contents('path/to/image.jpg'));
$results = $searchClient->images()->searchByImage([
    'data' => $imageData
]);

// By CBIR ID from a previous search
$results = $searchClient->images()->searchByImage([
    'id' => 'cbir_id_from_previous_search'
]);
```

---

### Wordstat

Keyword analytics, similar to [Yandex Wordstat](https://wordstat.yandex.ru/) — but through the API, so you can automate
it.

#### Top related phrases

What do people search for along with your keyword?

```php
$top = $searchClient->wordstat()->getTop('Laravel framework', [
    'numPhrases' => 50,
    'regions' => [213], // Moscow
    'devices' => 'DEVICE_ALL'
]);

echo "Phrase: {$top->phrase}\n";
foreach ($top->topPhrases as $phrase) {
    echo "- {$phrase['phrase']}: {$phrase['count']} searches\n";
}
```

#### Dynamics over time

See how a keyword's popularity changes:

```php
$dynamics = $searchClient->wordstat()->getDynamics('PHP programming', [
    'regions' => [213],
    'devices' => 'DEVICE_DESKTOP'
]);

foreach ($dynamics->dynamics as $period) {
    echo "{$period['date']}: {$period['count']} searches\n";
}
```

#### Regional breakdown

Where in the world (or country) is your keyword popular?

```php
$distribution = $searchClient->wordstat()->getRegionsDistribution('Laravel');

foreach ($distribution->regionsDistribution as $region) {
    echo "{$region['name']}: {$region['count']}\n";
}
```

#### Region tree

Get the full hierarchy of region codes (useful for building region filters):

```php
$tree = $searchClient->wordstat()->getRegionTree();

foreach ($tree->regions as $region) {
    echo "ID: {$region['id']} — {$region['name']}\n";
}
```

**Device filter options:** `DEVICE_ALL`, `DEVICE_DESKTOP`, `DEVICE_PHONE`, `DEVICE_TABLET`

---

## Configuration

| Option        | Env Variable         | Description                    |
|---------------|----------------------|--------------------------------|
| `folder_id`   | `YANDEX_FOLDER_ID`   | Your Yandex Cloud folder ID    |
| `oauth_token` | `YANDEX_OAUTH_TOKEN` | OAuth token for authentication |

**Where to get these:**

1. **OAuth token** — go to [oauth.yandex.ru](https://oauth.yandex.ru/) and create an app token with the right scopes
2. **Folder ID** — open the [Yandex Cloud Console](https://console.cloud.yandex.ru/), create or select a folder, and
   grab its ID

## How Authentication Works

You don't need to worry about IAM tokens, token refresh, or authorization headers. This library delegates all of that
to [`yandex-cloud-client-php`](https://github.com/tigusigalpa/yandex-cloud-client-php), which:

- converts your OAuth token into an IAM token
- automatically refreshes it before it expires
- injects the `Authorization: Bearer <IAM_TOKEN>` header into every request

Just pass your OAuth token and folder ID — the rest happens automatically.

## Testing

```bash
# Run the test suite
composer test

# Static analysis (PHPStan level 8)
composer phpstan

# Code style check (PSR-12)
composer cs-check

# Auto-fix code style
composer cs-fix
```

## Contributing

Found a bug? Have an idea? PRs are welcome.

1. Fork the repo
2. Create a branch (`git checkout -b feature/my-feature`)
3. Commit your changes
4. Push and open a Pull Request

## License

MIT — see [LICENSE](LICENSE) for the full text.

## Useful Links

- [Yandex Search API docs](https://aistudio.yandex.ru/docs/ru/search-api/concepts/index.html)
- [Yandex AI Studio](https://aistudio.yandex.ru/)
- [This package on GitHub](https://github.com/tigusigalpa/yandex-search-php)
- [This package on Packagist](https://packagist.org/packages/tigusigalpa/yandex-search-php)
- [yandex-cloud-client-php](https://github.com/tigusigalpa/yandex-cloud-client-php) — the auth layer this package uses
- [yandexgpt-php](https://github.com/tigusigalpa/yandexgpt-php) — PHP SDK for YandexGPT text generation and YandexART

## Author

**Igor Sazonov** — [@tigusigalpa](https://github.com/tigusigalpa) · [sovletig@gmail.com](mailto:sovletig@gmail.com)
