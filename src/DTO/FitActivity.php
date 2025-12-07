<?php

namespace Dunn\FitReader\DTO;

use DateTimeInterface;
use Illuminate\Support\Collection;
use Dunn\FitReader\Internals\FitConstants;

class FitActivity
{
    /**
     * @param Collection<int, FitRecord> $records
     * @param Collection<int, FitLap> $laps
     */
    public function __construct(
        public DateTimeInterface $startTime,
        public float $totalDistanceMeters,
        public float $totalDurationSeconds,
        public Collection $records,
        public Collection $laps,
        public ?int $manufacturer = null,
        public ?int $product = null,
        public ?int $serialNumber = null,
        public ?int $sport = null,
        public ?int $subSport = null,
    ) {}

    /**
     * Get the human-readable sport type name
     */
    public function getSportName(): ?string
    {
        return $this->sport !== null ? (FitConstants::$Sports[$this->sport] ?? 'unknown') : null;
    }

    /**
     * Convert the activity to an array
     */
    public function toArray(): array
    {
        return [
            'start_time' => $this->startTime->format('c'),
            'total_distance_meters' => $this->totalDistanceMeters,
            'total_duration_seconds' => $this->totalDurationSeconds,
            'manufacturer' => $this->manufacturer,
            'product' => $this->product,
            'serial_number' => $this->serialNumber,
            'sport' => $this->sport,
            'sport_name' => $this->getSportName(),
            'sub_sport' => $this->subSport,
            'records' => $this->records->map(fn($record) => $record->toArray())->toArray(),
            'laps' => $this->laps->map(fn($lap) => $lap->toArray())->toArray(),
        ];
    }

    /**
     * Convert the activity to JSON
     */
    public function toJson(int $options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
