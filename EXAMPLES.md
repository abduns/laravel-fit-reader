# Usage Examples for Laravel Fit Reader v1.2.0

## Activity Type Detection

```php
use Dunn\FitReader\Facades\FitReader;

$activity = FitReader::fromPath(storage_path('app/activities/my-run.fit'));

// Get sport information
echo "Activity Type: " . $activity->getSportName(); // e.g., "running"
echo "Sport ID: " . $activity->sport; // e.g., 1
echo "Sub-sport: " . $activity->subSport; // e.g., 0

// Check activity type and process accordingly
switch ($activity->getSportName()) {
    case 'running':
        echo "This is a running activity!";
        // Process running-specific data
        break;
    case 'cycling':
        echo "This is a cycling activity!";
        // Process cycling-specific data
        break;
    case 'swimming':
        echo "This is a swimming activity!";
        // Process swimming-specific data
        break;
}
```

## Reading All Available Data

```php
use Dunn\FitReader\Facades\FitReader;

$activity = FitReader::fromPath(storage_path('app/activities/workout.fit'));

// Activity-level data
echo "Duration: {$activity->totalDurationSeconds} seconds\n";
echo "Distance: {$activity->totalDistanceMeters} meters\n";
echo "Start Time: {$activity->startTime->format('Y-m-d H:i:s')}\n";

// Iterate through all records (time-series data)
foreach ($activity->records as $record) {
    echo sprintf(
        "%s | HR: %s | Speed: %s m/s | Lat: %s, Lon: %s | Alt: %s m | Cadence: %s | Power: %s W\n",
        $record->timestamp->format('H:i:s'),
        $record->heartRateBpm ?? 'N/A',
        $record->speedMps ?? 'N/A',
        $record->latitude ?? 'N/A',
        $record->longitude ?? 'N/A',
        $record->altitudeMeters ?? 'N/A',
        $record->cadenceRpm ?? 'N/A',
        $record->powerWatts ?? 'N/A'
    );
}

// Iterate through all laps
foreach ($activity->laps as $index => $lap) {
    echo sprintf(
        "Lap %d: %s m in %s s | Avg HR: %s | Max HR: %s | Avg Speed: %s m/s | Calories: %s\n",
        $index + 1,
        $lap->totalDistanceMeters,
        $lap->totalDurationSeconds,
        $lap->averageHeartRateBpm ?? 'N/A',
        $lap->maxHeartRateBpm ?? 'N/A',
        $lap->averageSpeedMps ?? 'N/A',
        $lap->calories ?? 'N/A'
    );
}
```

## Exporting Raw Data as Array

```php
use Dunn\FitReader\Facades\FitReader;

$activity = FitReader::fromPath(storage_path('app/activities/workout.fit'));

// Export entire activity as array
$activityArray = $activity->toArray();

// Structure of the array:
// [
//     'start_time' => '2021-07-20T21:11:20+00:00',
//     'total_distance_meters' => 5000.0,
//     'total_duration_seconds' => 1800.0,
//     'manufacturer' => 1,
//     'product' => 1234,
//     'serial_number' => 12345678,
//     'sport' => 1,
//     'sport_name' => 'running',
//     'sub_sport' => 0,
//     'records' => [ /* array of record arrays */ ],
//     'laps' => [ /* array of lap arrays */ ],
// ]

// Save to database or cache
cache()->put('activity-' . $id, $activityArray);

// Or store in database as JSON
DB::table('activities')->insert([
    'user_id' => $userId,
    'data' => json_encode($activityArray),
]);
```

## Exporting Raw Data as JSON

```php
use Dunn\FitReader\Facades\FitReader;

$activity = FitReader::fromPath(storage_path('app/activities/workout.fit'));

// Export as JSON string
$json = $activity->toJson();

// Export with pretty formatting
$prettyJson = $activity->toJson(JSON_PRETTY_PRINT);

// Return in API response
return response()->json([
    'status' => 'success',
    'activity' => $activity->toArray(), // or use $activity->toJson()
]);

// Save to file
file_put_contents(
    storage_path('app/exports/activity-' . $id . '.json'),
    $prettyJson
);

// Export individual components
$recordsJson = json_encode(
    $activity->records->map(fn($r) => $r->toArray())->toArray()
);

$lapsJson = json_encode(
    $activity->laps->map(fn($l) => $l->toArray())->toArray()
);
```

## Complete Example: Processing a Running Activity

```php
use Dunn\FitReader\Facades\FitReader;

$activity = FitReader::fromPath($uploadedFile->getRealPath());

// Verify it's a running activity
if ($activity->getSportName() !== 'running') {
    throw new \Exception('This is not a running activity!');
}

// Extract key metrics
$metrics = [
    'sport' => $activity->getSportName(),
    'duration' => $activity->totalDurationSeconds,
    'distance' => $activity->totalDistanceMeters,
    'start_time' => $activity->startTime->format('c'),
    
    // Calculate average heart rate from records
    'avg_heart_rate' => $activity->records
        ->filter(fn($r) => $r->heartRateBpm !== null)
        ->avg('heartRateBpm'),
    
    // Calculate max heart rate
    'max_heart_rate' => $activity->records
        ->filter(fn($r) => $r->heartRateBpm !== null)
        ->max('heartRateBpm'),
    
    // Calculate average speed
    'avg_speed' => $activity->records
        ->filter(fn($r) => $r->speedMps !== null)
        ->avg('speedMps'),
    
    // Count records with GPS data
    'gps_points' => $activity->records
        ->filter(fn($r) => $r->latitude !== null && $r->longitude !== null)
        ->count(),
    
    // Get lap count
    'lap_count' => $activity->laps->count(),
];

// Store in database
DB::table('activities')->insert([
    'user_id' => auth()->id(),
    'sport' => $activity->sport,
    'sport_name' => $activity->getSportName(),
    'metrics' => json_encode($metrics),
    'raw_data' => $activity->toJson(), // Store complete raw data
    'created_at' => now(),
]);

// Return response
return response()->json([
    'success' => true,
    'activity_id' => DB::getPdo()->lastInsertId(),
    'metrics' => $metrics,
]);
```

## Supported Sport Types

The package supports 49+ sport types including:

- running
- cycling
- swimming
- walking
- hiking
- fitness_equipment
- multisport
- rowing
- basketball
- soccer
- tennis
- golf
- skiing (alpine and cross-country)
- snowboarding
- And many more...

See `FitConstants::$Sports` for the complete list.
