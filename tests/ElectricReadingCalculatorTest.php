<?php

namespace Proengeno\ReadingCalculator\Test;

use Proengeno\ReadingCalculator\Profiles\MonthlyProfile;
use Proengeno\ReadingCalculator\ElectricReadingCalculator;

class ElectricReadingCalculatorTest extends TestCase
{
    /** @test */
    public function it_builds_with_templates_profiles()
    {
        $calculator = ElectricReadingCalculator::withProfileTemplates('monthly', 'H0');

        $this->assertTrue($calculator->hasProfile('H0'));
        $this->assertEquals('H0', $calculator->getFallbackName());
    }

    /** @test */
    public function it_calculates_the_yearly_usage_with_a_monthly_profile()
    {
        $usage = 500;
        $from = new \DateTime('2019-01-01');
        $until = new \DateTime('2019-03-31');

        $calculator = new ElectricReadingCalculator;
        $calculator->addProfile('H0', MonthlyProfile::fromArray($this->buildProfiles(), 'm'));
        $this->assertEquals(2000.0, $calculator->getYearlyUsage('H0', $from, $until, $usage));
    }

    /** @test */
    public function it_calculates_the_yearly_usage_with_a_fallback_profile()
    {
        $usage = 500;
        $from = new \DateTime('2019-01-01');
        $until = new \DateTime('2019-03-31');

        $calculator = new ElectricReadingCalculator;
        $calculator->addProfile('FALLBACK', MonthlyProfile::fromArray($this->buildProfiles(), 'm'), $isFallback = true);
        $this->assertEquals(2000.0, $calculator->getYearlyUsage('UNKNOW', $from, $until, $usage));
    }

    /** @test */
    public function it_calculates_the_period_usage_with_a_monthly_profile()
    {
        $yearlyUsage = 5000;

        $calculator = new ElectricReadingCalculator;
        $calculator->addProfile('H0', MonthlyProfile::fromArray($this->buildProfiles()));

        $sumUsage = 0;
        foreach (range(1, 12) as $month) {
            $usage = $calculator->getPeriodUsage(
                'H0',
                new \DateTime("2019-$month-01"),
                (new \DateTime("2019-$month-01"))->modify('last day of this month'),
                $yearlyUsage
            );
            $sumUsage += $usage;
        }

        $this->assertEquals((float)$yearlyUsage, $sumUsage);
    }

    private function buildProfiles()
    {
        return [
            [new \DateTime('2018-01-01'), 1000],
            [new \DateTime('2018-02-01'), 1000],
            [new \DateTime('2018-03-01'), 1000],
            [new \DateTime('2018-04-01'), 1000],
            [new \DateTime('2018-05-01'), 1000],
            [new \DateTime('2018-06-01'), 1000],
            [new \DateTime('2018-07-01'), 1000],
            [new \DateTime('2018-08-01'), 1000],
            [new \DateTime('2018-09-01'), 1000],
            [new \DateTime('2018-10-01'), 1000],
            [new \DateTime('2018-11-01'), 1000],
            [new \DateTime('2018-12-01'), 1000],
            [new \DateTime('2019-01-01'), 1000],
            [new \DateTime('2019-02-01'), 1000],
            [new \DateTime('2019-03-01'), 1000],
            [new \DateTime('2019-04-01'), 1000],
            [new \DateTime('2019-05-01'), 1000],
            [new \DateTime('2019-06-01'), 1000],
            [new \DateTime('2019-07-01'), 1000],
            [new \DateTime('2019-08-01'), 1000],
            [new \DateTime('2019-09-01'), 1000],
            [new \DateTime('2019-10-01'), 1000],
            [new \DateTime('2019-11-01'), 1000],
            [new \DateTime('2019-12-01'), 1000],
        ];
    }

}
