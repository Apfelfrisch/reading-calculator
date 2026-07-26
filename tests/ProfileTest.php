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

    /** @test */
    public function it_prorates_the_periode_factor_within_a_single_month()
    {
        $profile = $this->buildFlatProfile();

        $factor = $profile->getPeriodeFactor(new DateTime('2019-06-01'), new DateTime('2019-06-14'));

        $this->assertEqualsWithDelta(1000 / 30 * 13, $factor, 0.001);
    }

    /** @test */
    public function it_never_exceeds_the_full_month_factor_within_a_single_month()
    {
        $profile = $this->buildFlatProfile();

        $factor = $profile->getPeriodeFactor(new DateTime('2019-06-01'), new DateTime('2019-06-30'));

        $this->assertLessThanOrEqual(1000.0, $factor);
    }

    /** @test */
    public function it_sums_partial_and_full_months_over_month_borders()
    {
        $profile = $this->buildFlatProfile();

        $factor = $profile->getPeriodeFactor(new DateTime('2019-05-15'), new DateTime('2019-08-10'));

        // 17 days May, full June and July, 9 days August
        $this->assertEqualsWithDelta(1000 / 31 * 17 + 1000 + 1000 + 1000 / 31 * 9, $factor, 0.001);
    }

    /** @test */
    public function it_needs_no_factor_for_the_until_month_when_the_periode_ends_on_the_first()
    {
        $profile = $this->buildFlatProfile();

        $factor = $profile->getPeriodeFactor(new DateTime('2019-12-01'), new DateTime('2020-01-01'));

        $this->assertEqualsWithDelta(1000.0, $factor, 0.001);
    }

    private function buildFlatProfile(): MonthlyProfile
    {
        $profile = new MonthlyProfile;

        foreach (range(1, 12) as $month) {
            $profile->addEntry(new DateTime(sprintf('2019-%02d-01', $month)), 1000);
        }

        return $profile;
    }
}
