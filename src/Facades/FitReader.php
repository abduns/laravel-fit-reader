<?php

namespace Dunn\FitReader\Facades;

use Illuminate\Support\Facades\Facade;
use Dunn\FitReader\Contracts\FitReader as FitReaderContract;

/**
 * @method static \Dunn\FitReader\DTO\FitActivity fromPath(string $path)
 * @method static \Dunn\FitReader\DTO\FitActivity fromUploadedFile(\Illuminate\Http\UploadedFile $file)
 * 
 * @see \Dunn\FitReader\Services\FitReaderService
 */
class FitReader extends Facade
{
    protected static function getFacadeAccessor()
    {
        return FitReaderContract::class;
    }
}
