<?php

namespace Dunn\FitReader\Internals;

use RuntimeException;

class FitFileParser
{
    const COMPRESSED_HEADER_MASK = 0x80;
    const MESG_DEFINITION_MASK = 0x40;
    const DEV_DATA_MASK = 0x20;
    const MESG_HEADER_MASK = 0x00;
    const LOCAL_MESG_NUM_MASK = 0x0F;

    const HEADER_WITH_CRC_SIZE = 14;
    const HEADER_WITHOUT_CRC_SIZE = 12;
    const CRC_SIZE = 2;

    protected Stream $stream;
    protected array $localMessageDefinitions = [];
    protected array $developerDataDefinitions = [];
    
    /**
     * Holds the parsed messages.
     * Structure mimics the previously used library for compatibility:
     * [
     *    'session' => ['public' => [ ... ]],
     *    'record'  => ['public' => [ ... ]],
     *    'lap'     => ['public' => [ ... ]],
     * ]
     */
    public array $data_mesgs = [];

    public function __construct(protected string $filePath, protected array $options = [])
    {
        $this->stream = Stream::fromFile($filePath);
        $this->parse();
    }

    protected function parse(): void
    {
        if (!$this->isFIT()) {
            throw new RuntimeException("File is not a valid FIT file.");
        }

        while ($this->stream->getPosition() < $this->stream->getLength()) {
            $this->decodeNextFile();
        }
    }

    protected function isFIT(): bool
    {
        try {
            $fileHeaderSize = $this->stream->peekByte();
            if (!in_array($fileHeaderSize, [self::HEADER_WITH_CRC_SIZE, self::HEADER_WITHOUT_CRC_SIZE])) {
                return false;
            }

            if ($this->stream->getLength() < $fileHeaderSize + self::CRC_SIZE) {
                return false;
            }

            $fileHeader = $this->readFileHeader(['resetPosition' => true]);
            if ($fileHeader['dataType'] !== ".FIT") {
                return false;
            }
        } catch (\Throwable $e) {
            return false;
        }

        return true;
    }

    protected function readFileHeader(array $options = []): array
    {
        $resetPosition = $options['resetPosition'] ?? false;
        $position = $this->stream->getPosition();

        $headerSize = $this->stream->readByte();
        $protocolVersion = $this->stream->readByte();
        $profileVersion = $this->stream->readUInt16();
        $dataSize = $this->stream->readUInt32();
        $dataType = $this->stream->readString(4);
        $headerCRC = 0;

        if ($headerSize === self::HEADER_WITH_CRC_SIZE) {
            $headerCRC = $this->stream->readUInt16();
        }

        if ($resetPosition) {
            $this->stream->seek($position);
        }

        return [
            'headerSize' => $headerSize,
            'protocolVersion' => $protocolVersion,
            'profileVersion' => $profileVersion,
            'dataSize' => $dataSize,
            'dataType' => $dataType,
            'headerCRC' => $headerCRC,
        ];
    }

    protected function decodeNextFile(): void
    {
        $position = $this->stream->getPosition();
        
        $crcCalculator = new CrcCalculator();
        $this->stream->setCrcCalculator($crcCalculator);
        
        $header = $this->readFileHeader();
        
        // Read data messages and definitions
        while ($this->stream->getPosition() < ($position + $header['headerSize'] + $header['dataSize'])) {
            $this->decodeNextRecord();
        }

        // Check CRC
        $calculatedCrc = $crcCalculator->crc;
        $this->stream->setCrcCalculator(null);
        $crc = $this->stream->readUInt16();
        
        if ($crc !== $calculatedCrc) {
            throw new RuntimeException("CRC error");
        }
    }

    protected function decodeNextRecord(): void
    {
        $recordHeader = $this->stream->peekByte();

        if (($recordHeader & self::COMPRESSED_HEADER_MASK) === self::COMPRESSED_HEADER_MASK) {
            $this->decodeCompressedTimestampDataMessage();
            return;
        }

        if (($recordHeader & self::MESG_DEFINITION_MASK) === self::MESG_HEADER_MASK) {
            $this->decodeMessage();
            return;
        }

        if (($recordHeader & self::MESG_DEFINITION_MASK) === self::MESG_DEFINITION_MASK) {
            $this->decodeMessageDefinition();
            return;
        }
    }

    protected function decodeMessageDefinition(): void
    {
        $recordHeader = $this->stream->readByte();
        $localMesgNum = $recordHeader & self::LOCAL_MESG_NUM_MASK;
        
        $reserved = $this->stream->readByte();
        $architecture = $this->stream->readByte();
        $endianness = $architecture === 0 ? Stream::LITTLE_ENDIAN : Stream::BIG_ENDIAN;
        
        $globalMessageNumber = $this->stream->readUInt16($endianness);
        $numFields = $this->stream->readByte();
        
        $fieldDefinitions = [];
        
        for ($i = 0; $i < $numFields; $i++) {
            $fieldDefinitionNumber = $this->stream->readByte();
            $size = $this->stream->readByte();
            $baseType = $this->stream->readByte();
            
            $fieldDefinitions[] = [
                'fieldDefinitionNumber' => $fieldDefinitionNumber,
                'size' => $size,
                'baseType' => $baseType,
            ];
        }
        
        $developerFieldDefinitions = [];
        if (($recordHeader & self::DEV_DATA_MASK) === self::DEV_DATA_MASK) {
            $numDevFields = $this->stream->readByte();
            for ($i = 0; $i < $numDevFields; $i++) {
                $fieldDefinitionNumber = $this->stream->readByte();
                $size = $this->stream->readByte();
                $developerDataIndex = $this->stream->readByte();
                
                $developerFieldDefinitions[] = [
                    'fieldDefinitionNumber' => $fieldDefinitionNumber,
                    'size' => $size,
                    'developerDataIndex' => $developerDataIndex,
                ];
            }
        }
        
        $messageProfile = Profile::$messages[$globalMessageNumber] ?? null;
        
        if ($messageProfile === null) {
             $messageProfile = [
                'name' => (string)$globalMessageNumber,
                'num' => $globalMessageNumber,
                'fields' => []
            ];
        }
        
        $this->localMessageDefinitions[$localMesgNum] = [
            'globalMessageNumber' => $globalMessageNumber,
            'endianness' => $endianness,
            'fieldDefinitions' => $fieldDefinitions,
            'developerFieldDefinitions' => $developerFieldDefinitions,
            'messageProfile' => $messageProfile,
        ];
    }

    protected function decodeMessage(): void
    {
        $recordHeader = $this->stream->readByte();
        $localMesgNum = $recordHeader & self::LOCAL_MESG_NUM_MASK;
        
        if (!isset($this->localMessageDefinitions[$localMesgNum])) {
            throw new RuntimeException("Local message definition $localMesgNum not found.");
        }
        
        $definition = $this->localMessageDefinitions[$localMesgNum];
        $messageProfile = $definition['messageProfile'];
        $fields = $messageProfile['fields'] ?? [];
        
        $message = [];
        
        foreach ($definition['fieldDefinitions'] as $fieldDef) {
            $fieldNum = $fieldDef['fieldDefinitionNumber'];
            $fieldProfile = $fields[$fieldNum] ?? null;
            
            $rawFieldValue = $this->readRawFieldValue($fieldDef['baseType'], $fieldDef['size'], $definition['endianness']);
            
            if ($fieldProfile) {
                $fieldName = $fieldProfile['name'];
                $fieldValue = $this->transformValue($rawFieldValue, $fieldProfile, $fieldDef['baseType']);
                $message[$fieldName] = $fieldValue;
            } else {
                // Unknown field
                // $message["field_$fieldNum"] = $rawFieldValue;
            }
        }
        
        // Skip developer fields for now
        foreach ($definition['developerFieldDefinitions'] as $devFieldDef) {
            $this->stream->readBytes($devFieldDef['size']);
        }
        
        // Store message
        $mesgName = $messageProfile['name'];
        if (!isset($this->data_mesgs[$mesgName])) {
            $this->data_mesgs[$mesgName] = ['public' => []];
        }
        $this->data_mesgs[$mesgName]['public'][] = $message;
    }

    protected function decodeCompressedTimestampDataMessage(): void
    {
        // Not supported yet
        throw new RuntimeException("Compressed timestamp messages are not currently supported");
    }

    protected function readRawFieldValue(int $baseType, int $size, int $endianness)
    {
        $data = $this->stream->readBytes($size);
        
        if ($size === 1) {
            $val = ord($data);
            if ($baseType === FitConstants::SINT8) {
                return ($val > 127) ? $val - 256 : $val;
            }
            return $val;
        }
        
        if ($size === 2) {
            $format = $endianness === Stream::LITTLE_ENDIAN ? 'v' : 'n';
            $val = unpack($format, $data)[1];
            if ($baseType === FitConstants::SINT16) {
                return ($val > 32767) ? $val - 65536 : $val;
            }
            return $val;
        }
        
        if ($size === 4) {
            $format = $endianness === Stream::LITTLE_ENDIAN ? 'V' : 'N';
            $val = unpack($format, $data)[1];
            if ($baseType === FitConstants::SINT32) {
                if ($val >= 2147483648) {
                    $val -= 4294967296;
                }
            }
            return $val;
        }
        
        if ($baseType === FitConstants::STRING) {
            return rtrim($data, "\0");
        }
        
        return $data;
    }

    protected function transformValue($rawValue, array $fieldProfile, int $baseType)
    {
        $invalid = FitConstants::$BaseTypeDefinitions[$baseType]['invalid'] ?? null;
        if ($rawValue === $invalid) {
            return null;
        }
        
        $scale = $fieldProfile['scale'] ?? 1;
        $offset = $fieldProfile['offset'] ?? 0;
        
        if ($scale !== 1 || $offset !== 0) {
            return ($rawValue / $scale) - $offset;
        }
        
        if (($fieldProfile['type'] ?? '') === 'date_time') {
            return $rawValue + 631065600;
        }
        
        return $rawValue;
    }
}
