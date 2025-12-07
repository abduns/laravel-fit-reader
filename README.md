# Laravel Fit Reader

A focused Laravel 12 package to read Garmin .fit activity files. This package wraps `adriangibbons/php-fit-file-analysis` to provide a clean, DTO-based API for your application.

## Requirements

- PHP 8.2+
- Laravel 12.x

## Installation

You can install the package via composer:

```bash
composer require abduns/laravel-fit-reader
```

## Usage

### Using the Facade

```php
use Dunn\FitReader\Facades\FitReader;

// From a local file path
$activity = FitReader::fromPath('/path/to/activity.fit');

echo $activity->totalDistanceMeters; // 5000.0
echo $activity->startTime->format('Y-m-d H:i:s');

// From an uploaded file (in a Controller)
public function store(Request $request)
{
    $file = $request->file('activity');
    $activity = FitReader::fromUploadedFile($file);
    
    foreach ($activity->records as $record) {
        // $record is an instance of Dunn\FitReader\DTO\FitRecord
        echo $record->heartRateBpm;
    }
}
```

### Using Dependency Injection

You can type-hint the `Dunn\FitReader\Contracts\FitReader` interface in your controllers or jobs.

```php
use Dunn\FitReader\Contracts\FitReader;

class ProcessActivityJob
{
    public function __construct(
        protected FitReader $fitReader
    ) {}

    public function handle()
    {
        $activity = $this->fitReader->fromPath($this->filePath);
        // ...
    }
}
```

## Local Development

To work on this package locally within a Laravel app:

1.  Add the repository to your app's `composer.json`:

```json
"repositories": [
    {
        "type": "path",
        "url": "../path/to/laravel-fit-reader"
    }
]
```

2.  Require the package:

```bash
composer require your-vendor/laravel-fit-reader @dev
```

## Testing

```bash
composer test
```

## License

The MIT License (MIT).
