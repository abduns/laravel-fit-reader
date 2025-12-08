# Laravel Fit Reader

[![Latest Version on Packagist](https://img.shields.io/packagist/v/abduns/laravel-fit-reader.svg?style=flat-square)](https://packagist.org/packages/abduns/laravel-fit-reader)
[![License](https://img.shields.io/packagist/l/abduns/laravel-fit-reader.svg?style=flat-square)](https://packagist.org/packages/abduns/laravel-fit-reader)

A focused, developer-friendly Laravel package to read and parse Garmin `.fit` activity files.

This package provides a native PHP implementation for parsing FIT files, converting raw data into easy-to-use Data Transfer Objects (DTOs). It's perfect for building fitness apps, training logs, or analysis tools without external binary dependencies.

## ✨ Features

- 🚀 **Simple API**: Use the Facade or Dependency Injection to read files.
- 📦 **DTO Support**: Work with typed objects (`FitActivity`, `FitRecord`, `FitLap`) instead of raw arrays.
- 🛠 **Laravel Integration**: Built specifically for Laravel 12+.
- ⚙️ **Configurable**: Customize parsing options via a config file.

## ✅ Requirements

- PHP 8.2+
- Laravel 12.x

## 📦 Installation

You can install the package via composer:

```bash
composer require abduns/laravel-fit-reader
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="fit-reader-config"
```

## 📖 Usage

### Reading a File

You can easily read a `.fit` file using the `FitReader` facade.

```php
use Dunn\FitReader\Facades\FitReader;

// Read from a local path
$activity = FitReader::fromPath(storage_path('app/activities/run.fit'));
```

### Working with the Data

The `fromPath` method returns a `Dunn\FitReader\DTO\FitActivity` object. Here is how you can access the data:

#### Activity Summary

```php
echo "Start Time: " . $activity->startTime->format('Y-m-d H:i:s');
echo "Total Distance: " . $activity->totalDistanceMeters . " meters";
echo "Total Duration: " . $activity->totalDurationSeconds . " seconds";

// Sport Type (new in v1.2.0)
echo "Sport: " . $activity->getSportName(); // e.g., "running", "cycling", "swimming"
echo "Sport ID: " . $activity->sport; // Raw sport type ID

// Device Metadata
echo "Manufacturer ID: " . $activity->manufacturer;
echo "Product ID: " . $activity->product;
echo "Serial Number: " . $activity->serialNumber;
```

#### Records (Time Series Data)

Access the stream of data points (heart rate, speed, location, etc.) via the `records` collection.

```php
foreach ($activity->records as $record) {
    // $record is an instance of Dunn\FitReader\DTO\FitRecord
    
    echo $record->timestamp->format('H:i:s') . " - ";
    echo "HR: " . $record->heartRateBpm . " bpm, ";
    echo "Speed: " . $record->speedMps . " m/s";
    
    // Available properties:
    // $record->latitude
    // $record->longitude
    // $record->altitudeMeters
    // $record->heartRateBpm
    // $record->cadenceRpm
    // $record->speedMps
    // $record->powerWatts
}
```

#### Laps

Access lap data via the `laps` collection.

```php
foreach ($activity->laps as $index => $lap) {
    // $lap is an instance of Dunn\FitReader\DTO\FitLap
    
    echo "Lap " . ($index + 1) . ": ";
    echo $lap->totalDistanceMeters . "m in " . $lap->totalDurationSeconds . "s";
    echo "Avg HR: " . $lap->averageHeartRateBpm;
}
```

### Handling Uploads in a Controller

Here is a practical example of handling a file upload in a Laravel Controller.

```php
use Illuminate\Http\Request;
use Dunn\FitReader\Facades\FitReader;

class ActivityController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'activity_file' => 'required|file', 
        ]);

        $file = $request->file('activity_file');
        
        // Parse the uploaded file directly
        $activity = FitReader::fromUploadedFile($file);
        
        return response()->json([
            'distance' => $activity->totalDistanceMeters,
            'duration' => $activity->totalDurationSeconds,
            'records_count' => $activity->records->count(),
        ]);
    }
}
```

### Exporting Data

**New in v1.2.0**: You can export activity data as raw arrays or JSON for easy storage or API responses.

```php
// Export entire activity as array
$array = $activity->toArray();

// Export as JSON
$json = $activity->toJson();

// Export with pretty print
$prettyJson = $activity->toJson(JSON_PRETTY_PRINT);

// Export individual records
foreach ($activity->records as $record) {
    $recordArray = $record->toArray();
    $recordJson = $record->toJson();
}

// Export individual laps
foreach ($activity->laps as $lap) {
    $lapArray = $lap->toArray();
    $lapJson = $lap->toJson();
}
```

### Detecting Activity Type

**New in v1.2.0**: Automatically detect what type of activity was recorded.

```php
$activity = FitReader::fromPath('/path/to/run.fit');

if ($activity->getSportName() === 'running') {
    echo "This is a running activity!";
    // Access running-specific data
    echo "Average pace: " . /* calculate from records */;
} elseif ($activity->getSportName() === 'cycling') {
    echo "This is a cycling activity!";
}

// Supported sport types include:
// running, cycling, swimming, walking, hiking, 
// fitness_equipment, multisport, and 40+ more
```

### Dependency Injection

If you prefer dependency injection over Facades, you can type-hint the contract.

```php
use Dunn\FitReader\Contracts\FitReader;

class ImportActivityJob
{
    public function __construct(
        protected FitReader $fitReader
    ) {}

    public function handle()
    {
        $activity = $this->fitReader->fromPath('/path/to/file.fit');
        // ...
    }
}
```

## 🔧 Configuration

This is the contents of the published config file:

```php
return [
    'units' => [
        'raw_values' => false, // Set to true if you want raw values instead of converted units
    ],
];
```

## 🧪 Testing

```bash
composer test
```

## 📄 License

The MIT License (MIT).
