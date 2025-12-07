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

    /**
     * Convert the lap to an array
     */
    public function toArray(): array
    {
        return [
            'total_distance_meters' => $this->totalDistanceMeters,
            'total_duration_seconds' => $this->totalDurationSeconds,
            'average_speed_mps' => $this->averageSpeedMps,
            'average_heart_rate_bpm' => $this->averageHeartRateBpm,
            'max_heart_rate_bpm' => $this->maxHeartRateBpm,
            'calories' => $this->calories,
        ];
    }

    /**
     * Convert the lap to JSON
     */
    public function toJson(int $options = 0): string
    {
        $json = json_encode($this->toArray(), $options);
        
        if ($json === false) {
            throw new \RuntimeException('Failed to encode lap to JSON: ' . json_last_error_msg());
        }
        
        return $json;
    }
}
