<?php

namespace Dunn\FitReader\DTO;

class FitLap
{
    public function __construct(
        public float $totalDistanceMeters,
        public float $totalDurationSeconds,
        public ?float $averageSpeedMps = null,
        public ?float $averageHeartRateBpm = null,
        public ?float $maxHeartRateBpm = null,
        public ?float $calories = null,
    ) {}
}
