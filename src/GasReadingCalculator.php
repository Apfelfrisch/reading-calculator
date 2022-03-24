<?php

namespace Proengeno\ReadingCalculator;

use DateTime;

class GasReadingCalculator extends ReadingCalculator
{
    public function getYearlyUsageFromCustomerValue(string $profile, DateTime $targetDate, float $customerValue): float
    {
        return $customerValue * $this->getProfile($profile)->yearlyFactor($targetDate);
    }

    public function getPeriodUsageFromCustomerValue(string $profile, DateTime $from, DateTime $until, float $customerValue): float
    {
        return $customerValue * $this->getProfile($profile)->getPeriodeFactor($from, $until);
    }
}
