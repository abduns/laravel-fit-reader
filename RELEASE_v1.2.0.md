# Release Notes for v1.2.0

## 📦 Laravel Fit Reader v1.2.0

This release adds powerful new features for activity type detection and data export capabilities.

### ✨ What's New

#### Activity Type Detection
- **Sport Detection**: Automatically detect what type of activity was recorded (running, cycling, swimming, etc.)
- **49 Sport Types Supported**: Based on the official FIT SDK specification
- **Easy Access**: Use `$activity->getSportName()` to get human-readable sport names
- **Raw Values Available**: Access raw `sport` and `subSport` IDs for advanced use cases

#### Data Export
- **Array Export**: Convert any DTO to an array with `->toArray()`
- **JSON Export**: Get JSON representation with `->toJson()`
- **Pretty Print Support**: Format JSON output with `->toJson(JSON_PRETTY_PRINT)`
- **Works on All DTOs**: Available on `FitActivity`, `FitRecord`, and `FitLap`

#### Enhanced Testing
- **Comprehensive Test Suite**: Added PEST tests using real example FIT files
- **Activity Type Tests**: Verify sport detection accuracy
- **Data Completeness Tests**: Ensure all fields are properly parsed
- **Export Tests**: Validate array and JSON export functionality

### 📖 Usage Examples

#### Detecting Activity Type

```php
use Dunn\FitReader\Facades\FitReader;

$activity = FitReader::fromPath('/path/to/run.fit');

// Get human-readable sport name
echo "Sport: " . $activity->getSportName(); // "running"

// Access raw sport IDs
echo "Sport ID: " . $activity->sport;
echo "Sub-sport ID: " . $activity->subSport;

// Build sport-specific logic
if ($activity->getSportName() === 'running') {
    echo "This is a running activity!";
    // Calculate running-specific metrics
} elseif ($activity->getSportName() === 'cycling') {
    echo "This is a cycling activity!";
    // Calculate cycling-specific metrics
}
```

#### Exporting Data

```php
// Export entire activity as array
$activityArray = $activity->toArray();

// Export as JSON
$activityJson = $activity->toJson();

// Pretty print JSON
$prettyJson = $activity->toJson(JSON_PRETTY_PRINT);

// Export individual records
foreach ($activity->records as $record) {
    $recordData = $record->toArray();
    // Store in database, send to API, etc.
}

// Export individual laps
foreach ($activity->laps as $lap) {
    $lapData = $lap->toArray();
}
```

### 🎯 Supported Sport Types

The package now recognizes 49 different sport types including:

- **Endurance**: running, cycling, swimming, walking, hiking, trail running
- **Gym**: fitness equipment, strength training, cardio training
- **Team Sports**: basketball, soccer, football, tennis, volleyball
- **Water Sports**: rowing, paddle sports, surfing, kiteboarding
- **Winter Sports**: skiing, snowboarding, skating
- **Others**: yoga, pilates, golf, martial arts, and many more

See the full list in the FIT SDK specification or check `getSportName()` method implementation.

### 🔄 Upgrade Guide

This is a **minor version update** with **no breaking changes**. Simply update your dependency:

```bash
composer update abduns/laravel-fit-reader
```

All existing code will continue to work. The new features are additive:
- New properties: `$activity->sport`, `$activity->subSport`
- New methods: `$activity->getSportName()`, `->toArray()`, `->toJson()`

### 📋 Requirements

- **PHP**: 8.2 or higher
- **Laravel**: 12.x

### 🔗 Links

- [Full Changelog](https://github.com/abduns/laravel-fit-reader/blob/main/CHANGELOG.md)
- [Documentation](https://github.com/abduns/laravel-fit-reader/blob/main/README.md)
- [Report Issues](https://github.com/abduns/laravel-fit-reader/issues)
- [Packagist](https://packagist.org/packages/abduns/laravel-fit-reader)

### 🙏 Credits

Created and maintained by **Dunn** ([@abduns](https://github.com/abduns))

### 📄 License

MIT License - see [LICENSE](https://github.com/abduns/laravel-fit-reader/blob/main/LICENSE) for details.

---

## What's Changed
- Added sport and subSport properties to FitActivity DTO
- Added getSportName() method for human-readable sport type names
- Added toArray() and toJson() methods to all DTOs
- Added comprehensive PEST tests with example FIT files
- Enhanced Profile definitions to include sport fields

**Full Changelog**: https://github.com/abduns/laravel-fit-reader/compare/v.1.1.0...v1.2.0
