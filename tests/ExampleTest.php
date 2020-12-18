<?php

namespace Totaa\TotaaFileUpload\Tests;

use Orchestra\Testbench\TestCase;
use Totaa\TotaaFileUpload\TotaaFileUploadServiceProvider;

class ExampleTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [TotaaFileUploadServiceProvider::class];
    }
    
    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }
}
