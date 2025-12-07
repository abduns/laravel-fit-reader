<?php

namespace Dunn\FitReader\DTO;

use DateTimeInterface;

class FitRecord
{
    public function __construct(
        public DateTimeInterface $timestamp,
        public ?float $latitude = null,
        public ?float $longitude = null,
        public ?float $altitudeMeters = null,
        public ?float $heartRateBpm = null,
        public ?float $cadenceRpm = null,
        public ?float $speedMps = null,
        public ?float $powerWatts = null,
    ) {}
}
