<?php

namespace Dunn\FitReader\Internals;

class CrcCalculator
{
    private static array $crcTable = [
        0x0000, 0xCC01, 0xD801, 0x1400, 0xF001, 0x3C00, 0x2800, 0xE401,
        0xA001, 0x6C00, 0x7800, 0xB401, 0x5000, 0x9C01, 0x8801, 0x4400
    ];

    public int $crc = 0;

    public function update(int $byte): void
    {
        $tmp = self::$crcTable[$this->crc & 0xF];
        $this->crc = ($this->crc >> 4) & 0x0FFF;
        $this->crc = $this->crc ^ $tmp ^ self::$crcTable[$byte & 0xF];

        $tmp = self::$crcTable[$this->crc & 0xF];
        $this->crc = ($this->crc >> 4) & 0x0FFF;
        $this->crc = $this->crc ^ $tmp ^ self::$crcTable[($byte >> 4) & 0xF];
    }

    public static function calculateCRC(string $data, int $start = 0, int $length = null): int
    {
        $crc = 0;
        $length = $length ?? strlen($data);
        
        for ($i = $start; $i < $start + $length; $i++) {
            $byte = ord($data[$i]);
            
            $tmp = self::$crcTable[$crc & 0xF];
            $crc = ($crc >> 4) & 0x0FFF;
            $crc = $crc ^ $tmp ^ self::$crcTable[$byte & 0xF];

            $tmp = self::$crcTable[$crc & 0xF];
            $crc = ($crc >> 4) & 0x0FFF;
            $crc = $crc ^ $tmp ^ self::$crcTable[($byte >> 4) & 0xF];
        }
        
        return $crc;
    }
}
