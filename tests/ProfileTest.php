<?php

namespace Proengeno\ReadingCalculator\Test;

use DateTime;
use Proengeno\ReadingCalculator\Profiles\MonthlyProfile;

class ProfileTest extends TestCase
{
    /** @test */
    public function it_load_the_monthly_electric_templates()
    {
        $profiles = MonthlyProfile::fromElectricTemplates();

        foreach($profiles as $profile) {
            $this->assertSame(1.0, round($profile->yearlyFactor(new DateTime)));
        }
    }

    /** @test */
    public function it_load_the_monthly_gas_templates()
    {
        $profiles = MonthlyProfile::fromGasTemplates();

        foreach($profiles as $profile) {
            $this->assertSame(1.0, round($profile->yearlyFactor(new DateTime)));
        }
    }
}
