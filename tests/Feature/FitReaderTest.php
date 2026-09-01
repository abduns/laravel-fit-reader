<?php

namespace Dunn\FitReader\Tests\Feature;

use Dunn\FitReader\Contracts\FitReader;
use Dunn\FitReader\DTO\FitActivity;
use Dunn\FitReader\Exceptions\InvalidFitFileException;
use Dunn\FitReader\Facades\FitReader as FitReaderFacade;
use Dunn\FitReader\Tests\TestCase;

class FitReaderTest extends TestCase
{
    public function test_it_can_resolve_fit_reader_from_container()
    {
        $reader = $this->app->make(FitReader::class);
        $this->assertInstanceOf(\Dunn\FitReader\Services\FitReaderService::class, $reader);
    }

    public function test_it_throws_exception_for_non_existent_file()
    {
        $this->expectException(InvalidFitFileException::class);
        
        FitReaderFacade::fromPath('/path/to/non/existent/file.fit');
    }

    public function test_it_decodes_valid_fit_file()
    {
        $path = __DIR__.'/../../src/examples/Activity.fit';

        $activity = FitReaderFacade::fromPath($path);

        $this->assertInstanceOf(FitActivity::class, $activity);
        $this->assertGreaterThanOrEqual(0, $activity->totalDistanceMeters);
        $this->assertNotEmpty($activity->records);
        $this->assertNotEmpty($activity->laps);
    }
}
