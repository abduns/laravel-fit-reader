# Changelog

All notable changes to `laravel-fit-reader` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.2.0] - 2025-12-08

### Added
- Added `sport` and `subSport` properties to `FitActivity` DTO to detect activity types (running, cycling, swimming, etc.)
- Added `getSportName()` method to `FitActivity` for human-readable sport type names
- Added `toArray()` and `toJson()` methods to all DTOs (`FitActivity`, `FitRecord`, `FitLap`) for raw data export
- Added comprehensive PEST tests using example FIT files from `src/examples/`
- Added support for detecting 49 different sport types based on FIT SDK specification

### Improved
- Enhanced Profile definitions to include sport and sub_sport fields in session messages
- Test coverage now includes activity type detection, data completeness verification, and raw data export

## [1.1.0] - 2025-12-08

### Added
- Added support for reading device metadata from the file header.
- Added `manufacturer`, `product`, and `serialNumber` properties to `FitActivity` DTO.

## [1.0.0] - 2025-12-07

### Added
- Initial release of Laravel Fit Reader package
- Native PHP implementation for parsing Garmin .fit activity files
- FitReader facade for easy file reading
- Support for reading from local file paths via `fromPath()`
- Support for reading from uploaded files via `fromUploadedFile()`
- Data Transfer Objects (DTOs) for structured data:
  - `FitActivity`: Main activity data with summary information
  - `FitRecord`: Time series data points (heart rate, speed, GPS, etc.)
  - `FitLap`: Lap/interval data
- Comprehensive activity metrics:
  - Start time and timestamps
  - Total distance (meters)
  - Total duration (seconds)
  - GPS coordinates (latitude/longitude)
  - Altitude data
  - Heart rate (BPM)
  - Cadence (RPM)
  - Speed (m/s)
  - Power (watts)
- Laravel service provider with auto-discovery
- Dependency injection support via contract interface
- Configurable parsing options
- Publishable configuration file
- Full test suite with Pest PHP
- Comprehensive documentation in README

### Requirements
- PHP 8.2 or higher
- Laravel 12.x

[1.0.0]: https://github.com/abduns/laravel-fit-reader/releases/tag/v1.0.0
