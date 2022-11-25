<?php

namespace Proengeno\ReadingCalculator;

use DateTime;
use Proengeno\ReadingCalculator\Profiles\Profile;
use Proengeno\ReadingCalculator\Profiles\MonthlyProfile;

class GasReadingCalculator extends ReadingCalculator
{
    public static function withProfileTemplates(string $profileType, string $fallBackProfile = null): self
    {
        switch ($profileType) {
            case 'monthly':
                $instance = new self;
                foreach (MonthlyProfile::fromElectricTemplates() as $key => $profile) {
                    $instance->addProfile($key, $profile, $fallBackProfile == $key);
                }
                return $instance;
        }
        throw new \InvalidArgumentException("Unknow profile Type $profileType");
    }

    public function getYearlyUsageFromCustomerValue(string $profile, DateTime $targetDate, float $customerValue): float
    {
        return $customerValue * $this->getProfile($profile)->yearlyFactor($targetDate);
    }

    public function getPeriodUsageFromCustomerValue(string $profile, DateTime $from, DateTime $until, float $customerValue): float
    {
        return $customerValue * $this->getProfile($profile)->getPeriodeFactor($from, $until);
    }
}
