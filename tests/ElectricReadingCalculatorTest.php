<?php

namespace Proengeno\ReadingCalculator\Test;

use DateTime;
use Proengeno\ReadingCalculator\Profiles\MonthlyProfile;
use Proengeno\ReadingCalculator\ElectricReadingCalculator;

class ElectricReadingCalculatorTest extends TestCase
{
    /** @test */
    public function it_builds_with_templates_profiles()
    {
        $calculator = ElectricReadingCalculator::withProfileTemplates('monthly', 'H0');

        foreach (['G0', 'G1', 'G2', 'G3', 'G4', 'G5', 'G6', 'H0', 'L0', 'L1', 'L2'] as $profile) {
            $this->assertEquals(
                500,
                $calculator->getYearlyUsage($profile, new DateTime('2019-12-31'), new DateTime('2020-12-31'), 500)
            );
        }
        $this->assertEquals('H0', $calculator->getFallbackName());
    }

    /** @test */
    public function it_calculates_the_yearly_usage_with_a_monthly_profile()
    {
        $usage = 500;
        $calculator = new ElectricReadingCalculator;
        $calculator->addProfile('H0', MonthlyProfile::fromArray($this->buildProfiles(), 'm'));

        $from = new DateTime('2019-01-01');
        $until = new DateTime('2019-06-01');

        $this->assertEquals(1200.0, round($calculator->getYearlyUsage('H0', $from, $until, $usage), 0));

        $from = new DateTime('2018-01-01');
        $until = new DateTime('2019-01-01');

        $this->assertEquals(500.0, $calculator->getYearlyUsage('H0', $from, $until, $usage));

        $from = new DateTime('2018-12-31');
        $until = new DateTime('2019-12-31');

        $this->assertEquals(500.0, $calculator->getYearlyUsage('H0', $from, $until, $usage));
    }

    /** @test */
    public function it_calculates_the_yearly_usage_with_a_fallback_profile()
    {
        $usage = 500;
        $from = new DateTime('2018-12-31');
        $until = new DateTime('2019-12-31');

        $calculator = new ElectricReadingCalculator;
        $calculator->addProfile('FALLBACK', MonthlyProfile::fromArray($this->buildProfiles(), 'm'), $isFallback = true);
        $this->assertEquals(500.0, $calculator->getYearlyUsage('UNKNOW', $from, $until, $usage));
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
                new DateTime("2019-$month-01"),
                (new DateTime("2019-$month-01"))->modify('+1 month'),
                $yearlyUsage
            );
            $sumUsage += $usage;
        }

        $this->assertEquals((float)$yearlyUsage, round($sumUsage, 3));
    }

    private function buildProfiles()
    {
        return [
            [new DateTime('2018-01-01'), 1000],
            [new DateTime('2018-02-01'), 1000],
            [new DateTime('2018-03-01'), 1000],
            [new DateTime('2018-04-01'), 1000],
            [new DateTime('2018-05-01'), 1000],
            [new DateTime('2018-06-01'), 1000],
            [new DateTime('2018-07-01'), 1000],
            [new DateTime('2018-08-01'), 1000],
            [new DateTime('2018-09-01'), 1000],
            [new DateTime('2018-10-01'), 1000],
            [new DateTime('2018-11-01'), 1000],
            [new DateTime('2018-12-01'), 1000],
            [new DateTime('2019-01-01'), 1000],
            [new DateTime('2019-02-01'), 1000],
            [new DateTime('2019-03-01'), 1000],
            [new DateTime('2019-04-01'), 1000],
            [new DateTime('2019-05-01'), 1000],
            [new DateTime('2019-06-01'), 1000],
            [new DateTime('2019-07-01'), 1000],
            [new DateTime('2019-08-01'), 1000],
            [new DateTime('2019-09-01'), 1000],
            [new DateTime('2019-10-01'), 1000],
            [new DateTime('2019-11-01'), 1000],
            [new DateTime('2019-12-01'), 1000],
        ];
    }

}
