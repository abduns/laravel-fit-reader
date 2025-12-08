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

    /**
     * Convert the record to an array
     */
    public function toArray(): array
    {
        return [
            'timestamp' => $this->timestamp->format('c'),
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'altitude_meters' => $this->altitudeMeters,
            'heart_rate_bpm' => $this->heartRateBpm,
            'cadence_rpm' => $this->cadenceRpm,
            'speed_mps' => $this->speedMps,
            'power_watts' => $this->powerWatts,
        ];
    }

    /**
     * Convert the record to JSON
     */
    public function toJson(int $options = 0): string
    {
        $json = json_encode($this->toArray(), $options);
        
        if ($json === false) {
            throw new \RuntimeException('Failed to encode record to JSON: ' . json_last_error_msg());
        }
        
        return $json;
    }
}
