<?php

namespace Dunn\FitReader\Contracts;

use Illuminate\Http\UploadedFile;
use Dunn\FitReader\DTO\FitActivity;

interface FitReader
{
    public function fromPath(string $path): FitActivity;

    public function fromUploadedFile(UploadedFile $file): FitActivity;
}
