<?php

namespace Proengeno\ReadingCalculator\Test;

use Proengeno\ReadingCalculator\GasReadingCalculator;
use Proengeno\ReadingCalculator\Profiles\GasCoefficientProfile;

class GasReadingCalculatorTest extends TestCase
{
    /** @test */
    public function it_calculates_the_yearly_usage_over_the_customer_value()
    {
        $customerValue = 81.29;
        $targetDate = new \DateTime('2019-03-31');

        $calculator = new GasReadingCalculator;
        $calculator->addProfile('H0', $this->buildProfile($targetDate));

        $this->assertEquals(21411.0, round($calculator->getYearlyUsageFromCustomerValue('H0', $targetDate, $customerValue)));
    }

    /** @test */
    public function it_calculates_the_period_usage_over_the_customer_value()
    {
        $customerValue = 81.29;
        $from = new \DateTime('2019-01-01');
        $until = new \DateTime('2019-03-31');

        $calculator = new GasReadingCalculator;
        $calculator->addProfile('H0', $this->buildProfile($until));

        $this->assertEquals(5279.0, round($calculator->getPeriodUsageFromCustomerValue('H0', $from, $until, $customerValue)));
    }

    /** @test */
    public function it_calculates_the_yearly_usage()
    {
        $usage = 6164;
        $from = new \DateTime('2019-01-01');
        $until = new \DateTime('2019-03-31');

        $calculator = new GasReadingCalculator;
        $calculator->addProfile('H0', $this->buildProfile($until));

        $this->assertEquals(24998.0, round($calculator->getYearlyUsage('H0', $from, $until, $usage)));
    }

    /** @test */
    public function it_calculates_the_period_usage()
    {
        $yearlyUsage = 25000;
        $from = new \DateTime('2019-01-01');
        $until = new \DateTime('2019-03-31');

        $calculator = new GasReadingCalculator;
        $calculator->addProfile('H0', $this->buildProfile($until));

        $this->assertEquals(6164.0, round($calculator->getPeriodUsage('H0', $from, $until, $yearlyUsage)));
    }

    private function buildProfile($until)
    {
        $profile = new GasCoefficientProfile(3.1935978110, -37.4142478269, 6.1824021474, 0.0721565909, 1.0000000000);

        $dayInYear = (clone $until)->modify('-1 year -1 day');
        while($dayInYear->modify('+1 day')->format('Ymd') <= $until->format('Ymd')) {
            $profile->addEntry($dayInYear, 10.00);
        }
        $profile->addEntry($dayInYear, 10.00);
        return $profile;
    }
}
