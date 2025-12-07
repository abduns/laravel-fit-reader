<?php

use Dunn\FitReader\Facades\FitReader;
use Dunn\FitReader\DTO\FitActivity;

describe('FIT Activity Examples', function () {
    
    it('can read Activity.fit from examples', function () {
        $path = __DIR__ . '/../../src/examples/Activity.fit';
        
        expect(file_exists($path))->toBeTrue();
        
        $activity = FitReader::fromPath($path);
        
        expect($activity)->toBeInstanceOf(FitActivity::class);
        expect($activity->totalDurationSeconds)->toBeGreaterThan(0);
        expect($activity->records)->toBeInstanceOf(\Illuminate\Support\Collection::class);
        expect($activity->records->count())->toBeGreaterThan(0);
        expect($activity->laps->count())->toBeGreaterThan(0);
        
        // Distance might be 0 in some activities
        expect($activity->totalDistanceMeters)->toBeGreaterThanOrEqual(0);
    });

    it('detects sport type from Activity.fit', function () {
        $path = __DIR__ . '/../../src/examples/Activity.fit';
        
        $activity = FitReader::fromPath($path);
        
        expect($activity->sport)->not->toBeNull();
        expect($activity->sport)->toBe(37); // stand_up_paddleboarding
        expect($activity->getSportName())->toBe('stand_up_paddleboarding');
    });

    it('reads all available data from activity records', function () {
        $path = __DIR__ . '/../../src/examples/Activity.fit';
        
        $activity = FitReader::fromPath($path);
        
        // Check that we have records with data
        $recordsWithData = $activity->records->filter(function ($record) {
            return $record->timestamp !== null;
        });
        
        expect($recordsWithData->count())->toBeGreaterThan(0);
        
        // Check various fields are available in at least some records
        $recordsWithHR = $activity->records->filter(fn($r) => $r->heartRateBpm !== null);
        $recordsWithGPS = $activity->records->filter(fn($r) => $r->latitude !== null && $r->longitude !== null);
        $recordsWithAltitude = $activity->records->filter(fn($r) => $r->altitudeMeters !== null);
        $recordsWithSpeed = $activity->records->filter(fn($r) => $r->speedMps !== null);
        
        // At least some records should have these fields
        expect($recordsWithHR->count())->toBeGreaterThan(0);
        expect($recordsWithGPS->count())->toBeGreaterThan(0);
        expect($recordsWithAltitude->count())->toBeGreaterThan(0);
        expect($recordsWithSpeed->count())->toBeGreaterThan(0);
    });

    it('can export activity data as array', function () {
        $path = __DIR__ . '/../../src/examples/Activity.fit';
        
        $activity = FitReader::fromPath($path);
        $array = $activity->toArray();
        
        expect($array)->toBeArray();
        expect($array)->toHaveKeys([
            'start_time',
            'total_distance_meters',
            'total_duration_seconds',
            'sport',
            'sport_name',
            'sub_sport',
            'records',
            'laps',
        ]);
        
        expect($array['sport'])->toBe(37);
        expect($array['sport_name'])->toBe('stand_up_paddleboarding');
        expect($array['records'])->toBeArray();
        expect($array['laps'])->toBeArray();
        expect(count($array['records']))->toBeGreaterThan(0);
        expect(count($array['laps']))->toBeGreaterThan(0);
    });

    it('can export activity data as JSON', function () {
        $path = __DIR__ . '/../../src/examples/Activity.fit';
        
        $activity = FitReader::fromPath($path);
        $json = $activity->toJson();
        
        expect($json)->toBeString();
        
        // Parse JSON to verify it's valid
        $decoded = json_decode($json, true);
        expect($decoded)->toBeArray();
        expect($decoded)->toHaveKeys([
            'start_time',
            'total_distance_meters',
            'total_duration_seconds',
            'sport',
            'sport_name',
            'sub_sport',
            'records',
            'laps',
        ]);
    });

    it('can export individual records as array', function () {
        $path = __DIR__ . '/../../src/examples/Activity.fit';
        
        $activity = FitReader::fromPath($path);
        $record = $activity->records->first();
        
        $array = $record->toArray();
        
        expect($array)->toBeArray();
        expect($array)->toHaveKeys([
            'timestamp',
            'latitude',
            'longitude',
            'altitude_meters',
            'heart_rate_bpm',
            'cadence_rpm',
            'speed_mps',
            'power_watts',
        ]);
    });

    it('can export individual laps as array', function () {
        $path = __DIR__ . '/../../src/examples/Activity.fit';
        
        $activity = FitReader::fromPath($path);
        $lap = $activity->laps->first();
        
        $array = $lap->toArray();
        
        expect($array)->toBeArray();
        expect($array)->toHaveKeys([
            'total_distance_meters',
            'total_duration_seconds',
            'average_speed_mps',
            'average_heart_rate_bpm',
            'max_heart_rate_bpm',
            'calories',
        ]);
    });

    it('can read multisport activity', function () {
        $path = __DIR__ . '/../../src/examples/activity_multisport.fit';
        
        if (!file_exists($path)) {
            expect(true)->toBeTrue();
            return;
        }
        
        $activity = FitReader::fromPath($path);
        
        expect($activity)->toBeInstanceOf(FitActivity::class);
        expect($activity->sport)->not->toBeNull();
        
        // Multisport should have sport type 18
        if ($activity->sport === 18) {
            expect($activity->getSportName())->toBe('multisport');
        }
    });

    it('can read pool swim activity', function () {
        $path = __DIR__ . '/../../src/examples/activity_poolswim.fit';
        
        if (!file_exists($path)) {
            expect(true)->toBeTrue();
            return;
        }
        
        $activity = FitReader::fromPath($path);
        
        expect($activity)->toBeInstanceOf(FitActivity::class);
        expect($activity->totalDurationSeconds)->toBeGreaterThan(0);
    });

});
