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
        // Note: In a real scenario, we would have a sample.fit file in tests/fixtures
        // For this example, we will assume the file exists and the parser works.
        // Since we cannot easily mock the `new phpFITFileAnalysis` call inside the service
        // without refactoring, we will demonstrate the assertion logic.

        $path = __DIR__ . '/../fixtures/sample.fit';
        
        // Create a dummy file just to pass the file_exists check if we were running this
        // But since we don't have a real FIT file, this test would fail on the parser step.
        // So we mark it as skipped or just show the logic.
        
        $this->markTestSkipped('Requires a valid sample.fit file to run.');

        $activity = FitReaderFacade::fromPath($path);

        $this->assertInstanceOf(FitActivity::class, $activity);
        $this->assertGreaterThan(0, $activity->totalDistanceMeters);
        $this->assertCount(1, $activity->laps);
    }
}
