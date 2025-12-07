<?php

namespace Dunn\FitReader\Services;

use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Dunn\FitReader\Contracts\FitReader;
use Dunn\FitReader\DTO\FitActivity;
use Dunn\FitReader\DTO\FitLap;
use Dunn\FitReader\DTO\FitRecord;
use Dunn\FitReader\Exceptions\FitDecodingException;
use Dunn\FitReader\Exceptions\InvalidFitFileException;
use Dunn\FitReader\Internals\FitFileParser;

class FitReaderService implements FitReader
{
    public function __construct(
        protected array $config = []
    ) {}

    public function fromPath(string $path): FitActivity
    {
        if (!file_exists($path)) {
            throw new InvalidFitFileException("File not found at path: {$path}");
        }

        try {
            // Instantiate the internal parser
            $parser = new FitFileParser($path, $this->config);
            
            if (!isset($parser->data_mesgs['record']) && !isset($parser->data_mesgs['session'])) {
                 // Basic check if it parsed anything useful
            }

        } catch (\Throwable $e) {
            throw new FitDecodingException("Failed to decode FIT file: " . $e->getMessage(), 0, $e);
        }

        return $this->mapToDto($parser);
    }

    public function fromUploadedFile(UploadedFile $file): FitActivity
    {
        if (!$file->isValid()) {
            throw new InvalidFitFileException("Uploaded file is not valid.");
        }

        return $this->fromPath($file->getRealPath());
    }

    protected function mapToDto(FitFileParser $parser): FitActivity
    {
        // 1. Extract Session Data (Summary)
        $session = $parser->data_mesgs['session']['public'][0] ?? []; // Usually one session per file

        $startTimeTimestamp = $session['start_time'] ?? null;
        $startTime = $startTimeTimestamp ? Carbon::createFromTimestamp($startTimeTimestamp) : Carbon::now();

        $totalDistance = $session['total_distance'] ?? 0.0;
        $totalDuration = $session['total_timer_time'] ?? 0.0;

        // 2. Extract Records
        $recordsData = $parser->data_mesgs['record']['public'] ?? [];
        $records = collect($recordsData)->map(function ($r) {
            return new FitRecord(
                timestamp: isset($r['timestamp']) ? Carbon::createFromTimestamp($r['timestamp']) : Carbon::now(),
                latitude: isset($r['position_lat']) ? $this->semicirclesToDegrees($r['position_lat']) : null,
                longitude: isset($r['position_long']) ? $this->semicirclesToDegrees($r['position_long']) : null,
                altitudeMeters: $r['enhanced_altitude'] ?? $r['altitude'] ?? null,
                heartRateBpm: $r['heart_rate'] ?? null,
                cadenceRpm: $r['cadence'] ?? null,
                speedMps: $r['enhanced_speed'] ?? $r['speed'] ?? null,
                powerWatts: $r['power'] ?? null,
            );
        });

        // 3. Extract Laps
        $lapsData = $parser->data_mesgs['lap']['public'] ?? [];
        $laps = collect($lapsData)->map(function ($l) {
            return new FitLap(
                totalDistanceMeters: $l['total_distance'] ?? 0.0,
                totalDurationSeconds: $l['total_timer_time'] ?? 0.0,
                averageSpeedMps: $l['avg_speed'] ?? null,
                averageHeartRateBpm: $l['avg_heart_rate'] ?? null,
                maxHeartRateBpm: $l['max_heart_rate'] ?? null,
                calories: $l['total_calories'] ?? null,
            );
        });

        return new FitActivity(
            startTime: $startTime,
            totalDistanceMeters: $totalDistance,
            totalDurationSeconds: $totalDuration,
            records: $records,
            laps: $laps
        );
    }

    protected function semicirclesToDegrees($semicircles): float
    {
        // php-fit-file-analysis might return degrees if configured, 
        // but by default it often returns raw values or handles it.
        // If the value is large (like > 180 or < -180), it's likely semicircles.
        // Standard conversion: degrees = semicircles * (180 / 2^31)
        
        // However, checking the library source or behavior is ideal.
        // adriangibbons/php-fit-file-analysis often converts these automatically 
        // if you access the 'public' array which we are doing above ($parser->data_mesgs['record']['public']).
        // The 'public' key usually implies processed/calibrated values.
        // Let's assume they are already degrees if they are in the 'public' array.
        // But if they look like integers, they might be semicircles.
        
        if (is_numeric($semicircles) && abs($semicircles) > 180) {
             return $semicircles * (180.0 / 2147483648.0);
        }

        return (float) $semicircles;
    }
}
