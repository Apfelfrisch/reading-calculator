<?php

namespace Proengeno\ReadingCalculator\Test;

use Mockery as m;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function tearDown(): void
    {
        m::close();
    }
}
