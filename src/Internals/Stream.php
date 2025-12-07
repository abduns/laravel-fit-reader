<?php

namespace Dunn\FitReader\Internals;

use RuntimeException;

class Stream
{
    const LITTLE_ENDIAN = 0;
    const BIG_ENDIAN = 1;

    protected $handle;
    protected int $length;
    protected int $position = 0;
    protected ?CrcCalculator $crcCalculator = null;

    public function __construct(string $data)
    {
        $this->handle = fopen('php://memory', 'r+');
        fwrite($this->handle, $data);
        rewind($this->handle);
        $this->length = strlen($data);
    }

    public static function fromFile(string $path): self
    {
        $data = file_get_contents($path);
        if ($data === false) {
            throw new RuntimeException("Could not read file: $path");
        }
        return new self($data);
    }

    public function setCrcCalculator(?CrcCalculator $crcCalculator): void
    {
        $this->crcCalculator = $crcCalculator;
    }

    public function getPosition(): int
    {
        return ftell($this->handle);
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function seek(int $offset): void
    {
        fseek($this->handle, $offset);
        $this->position = $offset;
    }

    public function readByte(): int
    {
        $char = fgetc($this->handle);
        if ($char === false) {
            throw new RuntimeException("Unexpected end of stream");
        }
        $byte = ord($char);
        if ($this->crcCalculator) {
            $this->crcCalculator->update($byte);
        }
        return $byte;
    }

    public function peekByte(): int
    {
        $pos = ftell($this->handle);
        // Don't update CRC on peek
        $tempCrc = $this->crcCalculator;
        $this->crcCalculator = null;
        $byte = $this->readByte();
        $this->crcCalculator = $tempCrc;
        fseek($this->handle, $pos);
        return $byte;
    }

    public function readBytes(int $length): string
    {
        $data = fread($this->handle, $length);
        if ($data === false || strlen($data) !== $length) {
            throw new RuntimeException("Unexpected end of stream");
        }
        if ($this->crcCalculator) {
            for ($i = 0; $i < $length; $i++) {
                $this->crcCalculator->update(ord($data[$i]));
            }
        }
        return $data;
    }

    public function readUInt16(int $endianness = self::LITTLE_ENDIAN): int
    {
        $data = $this->readBytes(2);
        $format = $endianness === self::LITTLE_ENDIAN ? 'v' : 'n';
        return unpack($format, $data)[1];
    }

    public function readUInt32(int $endianness = self::LITTLE_ENDIAN): int
    {
        $data = $this->readBytes(4);
        $format = $endianness === self::LITTLE_ENDIAN ? 'V' : 'N';
        return unpack($format, $data)[1];
    }

    public function readString(int $length): string
    {
        return rtrim($this->readBytes($length), "\0");
    }
    
    public function readValue(int $baseType, int $size, array $options = [])
    {
        // Implementation of reading based on baseType
        // This needs the FIT constants for base types.
        // For now, returning raw bytes or implementing basic types.
        
        // Basic implementation for now, to be expanded.
        $data = $this->readBytes($size);
        $endianness = $options['endianness'] ?? self::LITTLE_ENDIAN;
        
        // TODO: Implement full type conversion based on baseType
        return $data;
    }

    public function slice(int $start, int $length): string
    {
        $current = ftell($this->handle);
        fseek($this->handle, $start);
        $data = fread($this->handle, $length);
        fseek($this->handle, $current);
        return $data;
    }
}
