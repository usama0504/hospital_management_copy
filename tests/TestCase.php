<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Tests ko built Vite assets ki zaroorat nahi (npm run build ke baghair bhi chalein).
        $this->withoutVite();
    }
}
