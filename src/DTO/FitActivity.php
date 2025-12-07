<?php

namespace Dunn\FitReader\DTO;

use DateTimeInterface;
use Illuminate\Support\Collection;

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
    ) {}
}
