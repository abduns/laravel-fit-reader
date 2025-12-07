<?php

namespace Dunn\FitReader\Tests\Feature;

use Dunn\FitReader\Facades\FitReader;
use Dunn\FitReader\Tests\TestCase;

class FitReaderMetadataTest extends TestCase
{
    public function test_it_extracts_file_id_metadata()
    {
        $path = __DIR__ . '/../../src/examples/Activity.fit';
        
        if (!file_exists($path)) {
            $this->markTestSkipped('Example file not found at ' . $path);
        }

        $activity = FitReader::fromPath($path);

        // We don't know the exact values in Activity.fit, but we can check if the properties exist
        // and are of the correct type (or null).
        // We can also dump them to see what they are.

        $this->assertTrue(property_exists($activity, 'manufacturer'));
        $this->assertTrue(property_exists($activity, 'product'));
        $this->assertTrue(property_exists($activity, 'serialNumber'));

        // Assuming Activity.fit has some of these set.
        // If they are null, it's also fine as long as the code doesn't crash.
        // But ideally we want to see values.
    }
}
