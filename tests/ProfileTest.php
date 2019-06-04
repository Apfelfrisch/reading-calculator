<?php

namespace Proengeno\ReadingCalculator\Test;

use Proengeno\ReadingCalculator\Profiles\MonthlyProfile;

class ProfileTest extends TestCase
{
    /** @test */
    public function it_load_the_monthly_electric_templates()
    {
        $profiles = MonthlyProfile::fromElectricTemplates();

        $this->assertTrue(count($profiles) > 0);
    }
}
