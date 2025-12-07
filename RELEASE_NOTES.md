# Release Notes for v1.1.0

## 📦 Laravel Fit Reader v1.1.0

This release adds support for extracting device metadata from FIT files.

### ✨ What's New

- **Device Metadata**: You can now access `manufacturer`, `product` (device ID), and `serialNumber` from the `FitActivity` object.

### 📖 Usage

```php
$activity = FitReader::fromPath('path/to/file.fit');

echo "Manufacturer: " . $activity->manufacturer;
echo "Product: " . $activity->product;
echo "Serial Number: " . $activity->serialNumber;
```

### 🔗 Links

- [Documentation](https://github.com/abduns/laravel-fit-reader/blob/main/README.md)
- [Changelog](https://github.com/abduns/laravel-fit-reader/blob/main/CHANGELOG.md)

