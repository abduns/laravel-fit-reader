<?php

namespace Dunn\FitReader\Internals;

class Profile
{
    public static array $messages = [
        FitConstants::MESG_NUM_FILE_ID => [
            'name' => 'file_id',
            'fields' => [
                0 => ['name' => 'type', 'type' => 'file'],
                1 => ['name' => 'manufacturer', 'type' => 'manufacturer'],
                2 => ['name' => 'product', 'type' => 'uint16'],
                3 => ['name' => 'serial_number', 'type' => 'uint32z'],
                4 => ['name' => 'time_created', 'type' => 'date_time'],
                5 => ['name' => 'number', 'type' => 'uint16'],
                8 => ['name' => 'product_name', 'type' => 'string'],
            ]
        ],
        FitConstants::MESG_NUM_SESSION => [
            'name' => 'session',
            'fields' => [
                253 => ['name' => 'timestamp', 'type' => 'date_time'],
                2 => ['name' => 'start_time', 'type' => 'date_time'],
                7 => ['name' => 'total_timer_time', 'type' => 'uint32', 'scale' => 1000, 'units' => 's'],
                8 => ['name' => 'total_elapsed_time', 'type' => 'uint32', 'scale' => 1000, 'units' => 's'],
                9 => ['name' => 'total_distance', 'type' => 'uint32', 'scale' => 100, 'units' => 'm'],
                11 => ['name' => 'total_calories', 'type' => 'uint16', 'units' => 'kcal'],
                14 => ['name' => 'avg_speed', 'type' => 'uint16', 'scale' => 1000, 'units' => 'm/s'],
                15 => ['name' => 'max_speed', 'type' => 'uint16', 'scale' => 1000, 'units' => 'm/s'],
                16 => ['name' => 'avg_heart_rate', 'type' => 'uint8', 'units' => 'bpm'],
                17 => ['name' => 'max_heart_rate', 'type' => 'uint8', 'units' => 'bpm'],
                18 => ['name' => 'avg_cadence', 'type' => 'uint8', 'units' => 'rpm'],
                19 => ['name' => 'max_cadence', 'type' => 'uint8', 'units' => 'rpm'],
                20 => ['name' => 'avg_power', 'type' => 'uint16', 'units' => 'watts'],
                21 => ['name' => 'max_power', 'type' => 'uint16', 'units' => 'watts'],
            ]
        ],
        FitConstants::MESG_NUM_LAP => [
            'name' => 'lap',
            'fields' => [
                253 => ['name' => 'timestamp', 'type' => 'date_time'],
                2 => ['name' => 'start_time', 'type' => 'date_time'],
                8 => ['name' => 'total_timer_time', 'type' => 'uint32', 'scale' => 1000, 'units' => 's'],
                9 => ['name' => 'total_distance', 'type' => 'uint32', 'scale' => 100, 'units' => 'm'],
                11 => ['name' => 'total_calories', 'type' => 'uint16', 'units' => 'kcal'],
                13 => ['name' => 'avg_speed', 'type' => 'uint16', 'scale' => 1000, 'units' => 'm/s'],
                14 => ['name' => 'max_speed', 'type' => 'uint16', 'scale' => 1000, 'units' => 'm/s'],
                15 => ['name' => 'avg_heart_rate', 'type' => 'uint8', 'units' => 'bpm'],
                16 => ['name' => 'max_heart_rate', 'type' => 'uint8', 'units' => 'bpm'],
                17 => ['name' => 'avg_cadence', 'type' => 'uint8', 'units' => 'rpm'],
                18 => ['name' => 'max_cadence', 'type' => 'uint8', 'units' => 'rpm'],
                19 => ['name' => 'avg_power', 'type' => 'uint16', 'units' => 'watts'],
                20 => ['name' => 'max_power', 'type' => 'uint16', 'units' => 'watts'],
            ]
        ],
        FitConstants::MESG_NUM_RECORD => [
            'name' => 'record',
            'fields' => [
                253 => ['name' => 'timestamp', 'type' => 'date_time'],
                0 => ['name' => 'position_lat', 'type' => 'sint32', 'units' => 'semicircles'],
                1 => ['name' => 'position_long', 'type' => 'sint32', 'units' => 'semicircles'],
                2 => ['name' => 'altitude', 'type' => 'uint16', 'scale' => 5, 'offset' => 500, 'units' => 'm'],
                3 => ['name' => 'heart_rate', 'type' => 'uint8', 'units' => 'bpm'],
                4 => ['name' => 'cadence', 'type' => 'uint8', 'units' => 'rpm'],
                5 => ['name' => 'distance', 'type' => 'uint32', 'scale' => 100, 'units' => 'm'],
                6 => ['name' => 'speed', 'type' => 'uint16', 'scale' => 1000, 'units' => 'm/s'],
                7 => ['name' => 'power', 'type' => 'uint16', 'units' => 'watts'],
                73 => ['name' => 'enhanced_speed', 'type' => 'uint32', 'scale' => 1000, 'units' => 'm/s'],
                78 => ['name' => 'enhanced_altitude', 'type' => 'uint32', 'scale' => 5, 'offset' => 500, 'units' => 'm'],
            ]
        ],
    ];
}
