# Release Notes for v1.0.0

## 📦 Laravel Fit Reader v1.0.0

This is the **first stable release** of Laravel Fit Reader - a native PHP package for parsing Garmin .fit activity files in Laravel applications.

### ✨ What's New

This initial release includes:

- **Native PHP Implementation**: No external dependencies required to parse .fit files
- **Simple API**: Easy-to-use facade and dependency injection support
- **Rich Data Objects**: Work with typed DTOs (`FitActivity`, `FitRecord`, `FitLap`)
- **Comprehensive Metrics**: Access GPS, heart rate, speed, cadence, power, and more
- **Laravel Integration**: Service provider with auto-discovery
- **Full Test Suite**: Reliable and tested codebase
- **Laravel 12 Support**: Built specifically for Laravel 12.x

### 🚀 Installation

```bash
composer require abduns/laravel-fit-reader
```

Publish the configuration file:

```bash
php artisan vendor:publish --tag="fit-reader-config"
```

### 📖 Quick Start

```php
use Dunn\FitReader\Facades\FitReader;

// Read a .fit file
$activity = FitReader::fromPath(storage_path('app/activities/run.fit'));

// Access activity data
echo "Distance: " . $activity->totalDistanceMeters . " meters\n";
echo "Duration: " . $activity->totalDurationSeconds . " seconds\n";

// Iterate through records
foreach ($activity->records as $record) {
    echo "HR: {$record->heartRateBpm} bpm, Speed: {$record->speedMps} m/s\n";
}
```

### 📋 Requirements

- **PHP**: 8.2 or higher
- **Laravel**: 12.x

### 🔗 Links

- [Documentation](https://github.com/abduns/laravel-fit-reader/blob/main/README.md)
- [Changelog](https://github.com/abduns/laravel-fit-reader/blob/main/CHANGELOG.md)
- [Report Issues](https://github.com/abduns/laravel-fit-reader/issues)
- [Packagist](https://packagist.org/packages/abduns/laravel-fit-reader)

### 👤 Credits

Created and maintained by **Dunn** ([@abduns](https://github.com/abduns))

### 📄 License

MIT License - see [LICENSE](https://github.com/abduns/laravel-fit-reader/blob/main/LICENSE) for details.

---

## Next Steps

After merging this PR:

1. **Push the Git Tag**: The `v1.0.0` tag has been created locally and needs to be pushed:
   ```bash
   git push origin v1.0.0
   ```

2. **Create GitHub Release**: 
   - Go to https://github.com/abduns/laravel-fit-reader/releases/new
   - Select the `v1.0.0` tag
   - Title: "v1.0.0 - Initial Release"
   - Copy the content from the "Release Notes" section above
   - Mark as "Latest release"
   - Publish the release

3. **Register on Packagist** (if not already done):
   - Visit https://packagist.org/packages/submit
   - Submit the GitHub repository URL
   - Set up auto-update webhook in GitHub repository settings

4. **Announce the Release**:
   - Share on social media
   - Post in relevant Laravel communities
   - Update any related documentation or projects

The package is now ready for public use! 🎉
