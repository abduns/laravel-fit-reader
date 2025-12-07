<?php

namespace Dunn\FitReader\Internals;

class FitConstants
{
    // Base Types
    const ENUM = 0x00;
    const SINT8 = 0x01;
    const UINT8 = 0x02;
    const SINT16 = 0x83;
    const UINT16 = 0x84;
    const SINT32 = 0x85;
    const UINT32 = 0x86;
    const STRING = 0x07;
    const FLOAT32 = 0x88;
    const FLOAT64 = 0x89;
    const UINT8Z = 0x0A;
    const UINT16Z = 0x8B;
    const UINT32Z = 0x8C;
    const BYTE = 0x0D;
    const SINT64 = 0x8E;
    const UINT64 = 0x8F;
    const UINT64Z = 0x90;

    public static array $BaseTypeDefinitions = [
        self::ENUM => ['size' => 1, 'invalid' => 0xFF],
        self::SINT8 => ['size' => 1, 'invalid' => 0x7F],
        self::UINT8 => ['size' => 1, 'invalid' => 0xFF],
        self::SINT16 => ['size' => 2, 'invalid' => 0x7FFF],
        self::UINT16 => ['size' => 2, 'invalid' => 0xFFFF],
        self::SINT32 => ['size' => 4, 'invalid' => 0x7FFFFFFF],
        self::UINT32 => ['size' => 4, 'invalid' => 0xFFFFFFFF],
        self::STRING => ['size' => 1, 'invalid' => 0x00], // Size is variable, but base unit is 1 byte
        self::FLOAT32 => ['size' => 4, 'invalid' => 0xFFFFFFFF],
        self::FLOAT64 => ['size' => 8, 'invalid' => 0xFFFFFFFFFFFFFFFF],
        self::UINT8Z => ['size' => 1, 'invalid' => 0x00],
        self::UINT16Z => ['size' => 2, 'invalid' => 0x0000],
        self::UINT32Z => ['size' => 4, 'invalid' => 0x00000000],
        self::BYTE => ['size' => 1, 'invalid' => 0xFF],
        self::SINT64 => ['size' => 8, 'invalid' => 0x7FFFFFFFFFFFFFFF],
        self::UINT64 => ['size' => 8, 'invalid' => 0xFFFFFFFFFFFFFFFF],
        self::UINT64Z => ['size' => 8, 'invalid' => 0x0000000000000000],
    ];

    // Message Numbers (Global)
    const MESG_NUM_FILE_ID = 0;
    const MESG_NUM_SESSION = 18;
    const MESG_NUM_LAP = 19;
    const MESG_NUM_RECORD = 20;
    const MESG_NUM_EVENT = 21;
    const MESG_NUM_DEVICE_INFO = 23;
    const MESG_NUM_DEVELOPER_DATA_ID = 206;
    const MESG_NUM_FIELD_DESCRIPTION = 207;

    // Map Global Message Numbers to Names
    public static array $MesgNumToName = [
        self::MESG_NUM_FILE_ID => 'file_id',
        self::MESG_NUM_SESSION => 'session',
        self::MESG_NUM_LAP => 'lap',
        self::MESG_NUM_RECORD => 'record',
        self::MESG_NUM_EVENT => 'event',
        self::MESG_NUM_DEVICE_INFO => 'device_info',
        self::MESG_NUM_DEVELOPER_DATA_ID => 'developer_data_id',
        self::MESG_NUM_FIELD_DESCRIPTION => 'field_description',
    ];
}
